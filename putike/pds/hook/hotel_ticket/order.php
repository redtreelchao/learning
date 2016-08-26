<?php
// hotel ticket product hook of order operation
class hotel_ticket_order extends hotel_ticket_hook
{

    /* ================= BOOKING ================= */

    /**
     * Booking for Room
     +-----------------------------------------
     * @access public
     * @param array $order
     * @param string $code
     * @param int $num
     * @param int $checkin
     * @param int $checkout
     * @param string $remark
     * @return void
     */
    public function booking($order, $code='', $num=1, $checkin=0, $checkout=0, $remark='')
    {
        $args = explode('_', $code);
        if ($args[count($args) - 1] !== 'ticket') return $order;

        $id = intval($args[0]);

        // Load Product's introduce, price, profit, sold number
        $sql = "SELECT  i.`id` AS `item`, i.`name` AS `item_name`, i.`target` AS `city`, d.`pid` AS `country`, i.`ext2` AS `bed`, i.`ext` AS `night`, i.`price`, i.`allot`, i.`sold`, i.`data`, i.`start` AS `start_book`, i.`end` AS `end_book`, i.`supply`,
                        b.`id` AS `ticket`, b.`name` AS `ticket_name`, b.`start`, b.`end`, b.`updatetime`,
                        h.`id` AS `hotel`, h.`name` AS `hotel_name`,
                        r.`id` AS `room`, r.`name` AS `room_name`, i.`min`, i.`max`,
                        y.`payby` AS `settleby`, y.`period`,
                        {$this -> _profit} AS `profit`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_product` AS b ON i.`pid` = b.`id`
                    LEFT JOIN `ptc_district` AS d ON d.`id` = i.`target`
                    LEFT JOIN `ptc_hotel` AS h ON i.`objpid` = h.`id`
                    LEFT JOIN `ptc_hotel_room_type` AS r ON i.`objid` = r.`id`
                    LEFT JOIN `ptc_supply` AS y ON i.`supply` = y.`id`
                    LEFT JOIN `ptc_org_profit` AS fi1 ON fi1.`org` = :org AND fi1.`payment` = 'ticket' AND fi1.`objtype` = 'hotel' AND fi1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fi2 ON fi2.`org` = :org AND fi2.`payment` = 'ticket' AND fi2.`objtype` = 'hotel' AND fi2.`objid` = i.`pid`
                    LEFT JOIN `ptc_org_profit` AS fi3 ON fi3.`org` = :org AND fi3.`payment` = 'ticket' AND fi3.`objtype` = 'room'  AND fi3.`objid` = i.`id`
                WHERE i.`id`=:id AND b.`status` = 1";

        $db = db(config('db'));
        $pro = $db -> prepare($sql) -> execute(array(':id'=>$id, ':org'=>api::$org));

        if (!$pro || !$pro[0]['ticket'] || !$pro[0]['hotel'] || !$pro[0]['room'])
        {
            api::set_error(614);
            return false;
        }
        $pro = $pro[0];

        $min = max(1,$pro['min']);

        $max = $pro['max'] < 1 ? 99 : $pro['max'];

        if ( $max < $num || $min > $num )
        {
            api::set_error(626);

            return false;
        }

        if ($pro['start'] || $pro['end'])
        {
            if (NOW < $pro['start'])
            {
                api::set_error(627);
                return false;
            }

            if ($pro['end'] && NOW >= $pro['end'] + 86400)
            {
                api::set_error(628);
                return false;
            }
        }

        // Don't check booking time. booking's expiration date is use for booking, not for pay.

        // Load locked allot
        $sql = "SELECT SUM(a.`rooms`) AS `sum` FROM `ptc_order_hotel` AS a
                    LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                WHERE a.`supply`='TICKET' AND a.`supplyid`=:product AND a.`hotel`=:hotel AND a.`room`=:room AND a.`status`=2 AND b.`create`>=:time";
        $locked = $db -> prepare($sql) -> execute(array(':product'=>$pro['ticket'], ':hotel'=>$pro['hotel'], ':room'=>$pro['item'], ':time'=>NOW - $this -> _timeout));

        if ($pro['allot'] - $pro['sold'] - (int)$locked[0]['sum'] - $num < 0)
        {
            api::set_error(619);
            return false;
        }

        // allot more than 5, exprie time is 48hour
        if ($pro['allot'] - $pro['sold'] - (int)$locked[0]['sum'] > 5)
        {
            order::$expire = 48 * 3600;
        }

        $_order = array(
            'productname'   => $pro['ticket_name'],
            'itemname'      => $pro['item_name'],
            'country'       => $pro['country'],
            'city'          => $pro['city'],
            'hotelname'     => $pro['hotel_name'],
            'roomname'      => roomname($pro['room_name'], $pro['bed']),
            'hotel'         => $pro['hotel'],
            'room'          => $pro['item'],
            'roomtype'      => $pro['room'],
            'checkin'       => 0,
            'checkout'      => 0,
            'nights'        => $pro['night'],
            'supply'        => 'TICKET',
            'supplyid'      => $pro['ticket'],
            'rooms'         => $num,
            'start'         => $pro['start_book'],
            'end'           => $pro['end_book'],
            'currency'      => 1,
            'floor'         => $num * $pro['price'],
            'total'         => $num * ($pro['price'] + round($pro['profit'])),
            'status'        => 2,                                                   // wait for pay
            'remark'        => (string)$remark,
            'settleby'      => $pro['settleby'] > 1 ? $pro['settleby'] : -(int)$pro['period'],   // settle
            'supply'        => (string)$pro['supply'],
        );

        $_order['return'] = array('hotel'=>$_order['hotelname'], 'room'=>$_order['roomname'], 'ticket'=>$_order['productname'], 'item'=>$_order['itemname'], 'num'=>$num, 'total'=>$_order['total'], 'status'=>2, 'expire'=>NOW+order::$expire);
        $_order['data']   = json_encode(array(
            'type'      => 'hotel_ticket',
            'price'     => $pro['price'],
            'profit'    => $pro['profit'],
            'ticket'    => $pro['ticket'],
            'item'      => $pro['item'],
            'service'   => array(),
        ));

        return array_merge($order, $_order);
    }
    // booking





    /**
     * check for paying order
     +-----------------------------------------
     * @access public
     * @param int $status
     * @param array $data
     * @param string $type
     * @param int $expire
     * @return int
     */
    public function pay($status, $data, $type='hotel', $expire=0)
    {
        if ($data['supply'] != 'TICKET' || $data['producttype'] > 0 || $type != 'hotel') return $status;

        static $_product = array();

        $data['start'] = date('m月d日', $data['start']);
        $data['end'] = date('m月d日', $data['end']);

        // allot is locking
        if (NOW < $expire)
        {
            if ($data['status'] == 2)
            {
                $db = db(config('db'));
                $rs = $db -> prepare("UPDATE `ptc_product_item` SET `sold`=`sold`+:num WHERE `id`=:id") -> execute(array(':id'=>(int)$data['room'], ':num'=>(int)$data['rooms']));
                if ($rs === false) return -901;

                if (!isset($_product[$data['supplyid']]))
                {
                    sms::send($data['order'], 'hotel_ticket_pay', $data);
                    $_product[$data['supplyid']] = 1;
                }
            }
            sms::send($data['order'], 'hotel_ticket_pay_item', $data);

            return $data['status'] == 2 ? 3 : $data['status'];
        }
        else
        {
            $db = db(config('db'));

            // product
            $pro = $db -> prepare("SELECT `allot`,`sold` FROM `ptc_product_item` WHERE `id`=:id AND `pid`=:pid;") -> execute(array(':id'=>$data['room'], ':pid'=>$data['supplyid']));
            if (!$pro) return 0;
            $pro = $pro[0];

            // load locked allot
            $sql = "SELECT SUM(a.`rooms`) AS `sum`
                    FROM `ptc_order_hotel` AS a
                    LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                    WHERE a.`supply`='TICKET' AND a.`supplyid`=:product AND a.`hotel`=:hotel AND a.`room`=:room AND a.`status`=2 AND b.`expire`>:time";
            $locked = $db -> prepare($sql) -> execute(array(':product'=>$data['supplyid'], ':hotel'=>$data['hotel'], ':room'=>$data['room'], ':time'=>NOW));

            // send sms
            if (!isset($_product[$data['supplyid']]))
            {
                sms::send($data['order'], 'hotel_ticket_pay', $data);
                $_product[$data['supplyid']] = 1;
            }
            sms::send($data['order'], 'hotel_ticket_pay_item', $data);

            if ($pro['allot'] - $pro['sold'] - $locked[0]['sum'] < $data['rooms'])
            {
                return 13;
            }
            else
            {
                $rs = $db -> prepare("UPDATE `ptc_product_item` SET `sold`=`sold`+:num WHERE `id`=:id") -> execute(array(':id'=>(int)$data['room'], ':num'=>(int)$data['rooms']));
                if (!$rs) return -901;

                return 3;
            }
        }
    }
    // pay







    /**
     * check for refund
     +-----------------------------------------
     * @access public
     * @param int $status
     * @param array $data
     * @param string $type
     * @return void
     */
    public function apply_refund($status, $data, $type='hotel')
    {
        if ($data['supply'] != 'TICKET' || $data['producttype'] > 0 || $type != 'hotel') return $status;

        $db = db(config('db'));

        if (!in_array($status, array(3,4,13))) return -610;

        $tickets = $db -> prepare("SELECT `id`,`ticket`,`data` FROM `ptc_order_room` WHERE `pid`=:pid") -> execute(array(':pid'=>$data['id']));
        $i = 0;
        $price = $data['total'] / $data['rooms'];
        $_data = json_encode(array('time'=>NOW, 'price'=>$price, 'from'=>'user'));
        foreach ($tickets as $ticket)
        {
            if ($ticket['ticket'] == 0)
            {
                $i ++;
                $rs = $db -> prepare("UPDATE `ptc_order_room` SET `ticket`=10, `data`=:data WHERE `id`=:id") -> execute(array(':id'=>$ticket['id'], ':data'=>$_data));
                if (false === $rs) return -501;
            }
        }

        if ($i)
        {
            $refund = (int)($price * $i);
            $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=`refund`+:refund WHERE `id`=:id") -> execute(array(':id'=>$data['id'], ':refund'=>$refund));
            if (false === $rs) return -502;

            return 10;
        }
        else
        {
            return 0;
        }
    }
    // apply_refund





    /* ================= MANAGE TEMPLATE ================= */

    private $_order = array();


    /**
     * extend of manage order view
     +-----------------------------------------
     * @access public
     * @param array $item
     * @param array $order
     * @param string $type
     * @param string $mode
     * @return void
     */
    public function tpl_extend($item, $order, $type='hotel', $mode)
    {
        if ($item['product'] || $item['type'] != 'hotel' || $item['payment'] != 'ticket') return;

        if ($item['start'] || $item['end'])
        {
            if ($item['start'] && !$item['end'])
                $expire = date('Y-m-d', $item['start']).'起';
            else if (!$item['start'] && $item['end'])
                $expire = '至'.date('Y-m-d', $item['end']).'止';
            else
                $expire = date('Y-m-d', $item['start']).' 至 '.date('Y-m-d', $item['end']);

            echo "<div class=\"info\"><b>有效期：</b>{$expire}</div>";
        }

        if ($item['advance'] || $item['min'])
        {
            echo "<div class=\"info\"><b>预订要求：</b>".($item['advance'] ? "需提前{$item['advance']}天预订;" : "").($item['min'] ? "需连住{$item['min']}晚;" : "").'</div>';
        }

        $used = 0;          // apply booking / booked / finish
        $enable = 0;        // paid / book failure
        $refund = 0;        // apply refund / refund successful / refund failure
        $need_refund = 0;   // apply refund
        $list = array();
        $rflist = array();
        foreach ($item['rooms'] as $ticket)
        {
            if ($ticket['ticket'] == 0)
            {
                if ($order['status'] > 2) $enable ++;
            }
            else
            {
                $status = $ticket['ticket'];

                // recode ticket status list
                if (in_array($status, array(4,5,8,9)))
                {
                    if ($ticket['ticket'] == 5)
                        $enable ++;

                    $group = $ticket['group'];
                    $room = $ticket['room'];

                    if (!isset($list[$group])) $list[$group] = array();

                    $list[$group][$room] = $ticket;
                    $used ++;
                }

                if (in_array($status, array(10,11,16)))
                {
                    $group = $ticket['group'];

                    if (!isset($rflist[$group])) $rflist[$group] = array();

                    $rflist[$group][] = $ticket;
                    $refund ++;
                    if ($status == 10) $need_refund ++;
                }
            }
        }

        $_data = json_decode($item['data'], true);

        // product's information
        $_id = (int)$_data['item'];
        $product = null;

        $db = db(config('db'));

        if ($_id)
        {
            $sql = "SELECT i.*, i.`intro` AS `item_intro`, p.* FROM `ptc_product_item` AS i LEFT JOIN `ptc_product` AS p ON i.`pid` = p.`id` WHERE i.`id`=:id";
            $product = $db -> prepare($sql) -> execute(array(':id'=>$_id));
            if ($product) $product = $product[0];
        }

        $this -> _order = array(
            'used'      => $used,
            'enable'    => $enable,
            'refund'    => $refund,
            'need_refund' => $need_refund,
            'hotel'     => $item['hotel'],
            'room'      => $item['room'],
            'bed'       => $item['bed'],
            'night'     => $item['nights'],
            'start'     => $item['start'],
            'end'       => $item['end'],
            'advance'   => $item['advance'],
            'min'       => $item['min'],
            'floor'     => $_data['price'],
            'profit'    => $_data['profit'],
            'ticket'    => $product,
        );

        if ($order['status'] > 2)
        {
            echo "<div class=\"info\"><b>购买券数：</b>".count($item['rooms'])."张</div>";
            echo "<div class=\"info\"><b>可用券数：</b>{$enable}张</div>";
        }
        else
        {
            echo "<div class=\"info\"><b>购买券数：</b>".count($item['rooms'])."张</div>";
        }

        $_status = order::status();

        if ($used)
        {
            echo "<div class=\"info\"><b>已用券数：</b>{$used}张</div>";

            echo "<div class=\"list\"><div class=\"table-responsive\"><table class=\"table\">";
            echo "<tr><th>房间</th><th>入住/离店时间</th><th>入住人</th>";
            if (!in_array($item['settleby'], array(2,3)))
                echo '<th>结算日</th>';
            if ($mode == 'operate')
                echo "<th class=\"text-right\">状态</th>";
            echo "</tr>";

            $i = 1;
            foreach ($list as $group)
            {
                $room = array();
                foreach (array_values($group) as $k => $v)
                {
                    $t = json_decode($v['data'], true);
                    $checkin = date('Y-m-d', $v['checkin']);
                    $checkout = date('m-d', $v['checkout']);
                    $night = ($v['checkout'] - $v['checkin'])/86400;

                    if ($mode == 'operate')
                    {
                        switch ($v['ticket'])
                        {
                            case '4':
                            case '15':
                                $status = '<a href="javascript:;" onclick="ticket_booking(this, '.$v['id'].');">'.$_status[$v['ticket']].'</a>';
                                break;
                            case '8':
                                $status = '<a href="javascript:;" onclick="ticket_booked(this, '.$v['id'].');">'.$_status[$v['ticket']].'</a>';
                                break;
                            default:
                                $status = $_status[$v['ticket']];
                                break;
                        }
                    }
                    else
                    {
                        $status = $_status[$v['ticket']];
                    }

                    if ($k == 0) $status = "<td rowspan=\"".count($group)."\" class=\"st text-right\">{$status}</td>"; else $status = '';

                    $settle = '';
                    if (!in_array($item['settleby'], array(2,3)))
                    {
                        $this -> _order['settles'][] = array('date'=>$v['settletime'] + 7 * 86400, 'status'=>$v['settle']);
                        $settle = '<td>'.date('Y-m-d', $v['settletime'] + 7 * 86400).'</td>';
                    }

                    echo "<tr id=\"ticket-{$v['id']}\"><td>{$i}</td><td>{$checkin} / {$checkout} &nbsp; 共{$night}晚</td><td>".trim($v['people'], ', ')."</td>{$settle}{$status}</tr>";
                    $i++;
                }
            }
            echo "</table></div></div>";
        }

        if ($item['settleby'] == 2 || $item['settleby'] == 3)
        {
            $this -> _order['settles'] = array('all'=>array('date'=>$item['rooms'][0]['settletime'], 'status'=>$item['rooms'][0]['settle']));
            echo "<div class=\"info\"><b>结算截至日：</b>".date('Y-m-d', $item['rooms'][0]['settletime'])."</div>";
        }

        if ($refund)
        {
            echo "<div class=\"info\"><b>退款券数：</b>{$refund}张</div>";

            echo "<table class=\"list\">";
            echo "<tr><th>序号</th><th>退款申请时间</th><th class=\"text-right\">状态</th></tr>";
            $i = 1;
            foreach ($rflist as $group)
            {
                foreach ($group as $k => $v)
                {
                    $t = json_decode($v['data'], true);
                    $time = !empty($t['time']) ? date('Y-m-d H:i', $t['time']) : '未知';
                    $status = $_status[$v['ticket']];

                    echo "<tr id=\"ticket-{$v['id']}\"><td>{$i}</td><td>{$time}</td><td class=\"st text-right\">{$status}</td></tr>";
                    $i++;
                }
            }
            echo "</table>";
        }

        echo '<hr style="margin:10px 0px 0px;">',
            '<div class="info"><b>产品包含：</b></div>',
            '<div class="info" style="line-height:20px; padding:0px 20px;">',
                nl2br($product['intro']),
            '</div>',
            '<div class="info"><b>用券说明：</b></div>',
            '<div class="info" style="line-height:20px; padding:0px 20px;">',
                nl2br($product['item_intro']),
            '</div>';

        return $item;
    }
    // tpl_extend




    /**
     * operation of manage order view
     +-----------------------------------------
     * @access public
     * @param mixed $item
     * @return void
     */
    public function tpl_operation($item, $order, $mode)
    {
        if ($item['type'] != 'hotel' || $item['payment'] != 'ticket') return;

        if ($order['status'] < 3) return;

        extract($this -> _order);

        if (!$start && !$end)
            $expire = '长期有效';
        else if ($start && !$end)
            $expire = date('Y-m-d', $start).'起';
        else if (!$start && $end)
            $expire = '至'.date('Y-m-d', $end).'止';
        else
            $expire = date('Y-m-d', $start).' 至 '.date('Y-m-d', $end);

        include dirname(__FILE__).'/order/order_operation.tpl.php';
    }
    // tpl_operation




    /**
     * footer of manage order view
     +-----------------------------------------
     * @access public
     * @param mixed $item
     * @return void
     */
    public function tpl_footer($item, $order, $mode)
    {
        if ($item['type'] != 'hotel' || $item['payment'] != 'ticket') return;

        extract($this -> _order);

        $status = order::status();

        include dirname(__FILE__).'/order/order_footer.tpl.php';
    }
    // tpl_footer





    /**
     * use rule for view
     +-----------------------------------------
     * @access public
     * @param mixed $item
     * @return void
     */
    public function use_rule($rule)
    {
        if (empty($this -> _order['ticket'])) return $rule;
        return nl2br($this -> _order['ticket']['rule']);
    }
    // use_rule




    /**
     * refund rule for view
     +-----------------------------------------
     * @access public
     * @param mixed $item
     * @return void
     */
    public function refund_rule($rule)
    {
        if (empty($this -> _order['ticket'])) return $rule;
        return nl2br($this -> _order['ticket']['refund']);
    }
    // refund_rule



    /* ================= OPERATE ================= */

    public function operate($order)
    {
        switch ($_POST['method'])
        {
            case 'ticket-used':
                $this -> _used($order);
                break;

            case 'ticket-book-submit':
                $this -> _submit($order);
                break;

            case 'ticket-book-cancel':
                $this -> _cancel($order);
                break;

            case 'ticket-refund':
                $this -> _refund_apply($order);
                break;

            case 'order-refund':
                if (empty($_POST['type']) || $_POST['type'] != 'hotel-ticket') return;

                $this -> _refund_submit($order);
                break;

            case 'order-settle':
                if (empty($_POST['type']) || $_POST['type'] != 'hotel-ticket') return;

                $this -> _settle($order);
                break;

            default:
        }
    }



    /**
     * ticket be used for booking
     +-----------------------------------------
     * @access protected
     * @param array $order
     * @return void
     */
    protected function _used($order)
    {
        if (!$_POST['id'] || !$order) json_return(null, 1, '请求信息有误，请重试');

        if (!(int)$_POST['num']) json_return(null, 1, '使用券数量不正确');

        if (!(int)$_POST['room']) json_return(null, 1, '预订房间数量不正确');

        $id = (int)$_POST['id'];

        $db = db(config('db'));
        $item = $db -> prepare("SELECT * FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid AND `supply`='TICKET'") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '欲使用的券类不存在');

        $night = (int)$item[0]['nights'];

        // get all enable ticket
        $num = $_POST['num'];
        $rooms = (int)$_POST['room'];

        $tickets = $db -> prepare("SELECT * FROM `ptc_order_room` WHERE `pid`=:pid AND `orderid`=:orderid AND (`ticket`=0 OR `ticket`=5) ORDER BY `ticket` DESC;") -> execute(array(':pid'=>$id, ':orderid'=>$order['id']));
        if ($tickets && count($tickets) < $num) json_return(null, 2, '可用券数量不足');

        if ($num % $rooms > 0) json_return(null, 2, '使用券数量不正确');
        $perroom_ticket = intval($num / $rooms);

        // checkin
        $checkin = strtotime($_POST['checkin']);
        if (!$checkin) json_return(null, 2, '预约日期不正确');
        $checkout = $checkin + $perroom_ticket * $night * 86400;

        //if (($item[0]['start'] && $checkin < $item[0]['start']) || ($item[0]['end'] && $checkin > $item[0]['end']))
            //json_return(null, 2, '预约日期不在有效期内');

        // supply
        $supply = trim($_POST['supply']);
        if ($supply == 'HAND')
        {
            // supply is own hotels by sign
            $floor = array();
            $price = round($item[0]['floor'] / $item[0]['rooms'] / $night);
            for ($date = $checkin; $date < $checkout; $date = $date+86400)
            {
                $floor[] = array('d'=>$date, 'f'=>$price);
            }
        }
        else
        {
            // supply is ota or ebooking
            $hotel = $db -> prepare("SELECT `{$supply}` FROM `ptc_hotel` WHERE `id`=:hotel") -> execute(array(':hotel'=>$item[0]['hotel']));
            if (!$hotel || !$hotel[0][$supply])
                json_return(null, 3, '供应所酒店信息未关联');

            $rooms = $db -> prepare("SELECT `key` FROM `ptc_hotel_room` WHERE `type`=:room AND `supply`=:supply") -> execute(array(':room'=>$item[0]['room'], ':supply'=>$supply));
            if (!$rooms)
                json_return(null, 3, '供应商房型信息未关联');

            $class = strtolower($supply);
            $key = call_user_func_array(array($class, 'parsekey'), array($rooms[0]['key']));
            $rs = call_user_func_array(array($class, 'refresh'), array($hotel[0][$supply], $key['room'], null, $checkin, $checkin+$night*86400));
            if (!$rs)
                json_return(null, 3, '获取供应商价格失败');

            $sql = 'SELECT p.`uncombine`, p.`roomtype` AS `room`, p.`bed`, p.`payment`, p.`nation`, p.`start`, p.`end`, p.`min`, p.`advance`, p.`supply`
                    FROM `ptc_hotel_price_date` AS p
                    WHERE p.`hotel`=:hotel AND p.`payment`=1 AND p.`date` >= :checkin AND p.`date` < :checkout AND p.`close` = 0
                    GROUP BY p.`uncombine`
                    ORDER BY p.`roomtype` ASC, p.`supply` ASC, p.`bed` DESC';
            ///$prices = $db -> prepare($sql) -> execute();
        }

        $db -> beginTrans();

        $max = $db -> prepare("SELECT MAX(`group`) AS `max` FROM `ptc_order_room` WHERE `pid`=:pid") -> execute(array(':pid'=>$id));
        $group = $max[0]['max'] + 1;

        $return = array('rooms'=>array(), 'floor'=>$floor, 'checkin'=>$checkin, 'checkout'=>$checkout, 'num'=>$num);

        foreach ($tickets as $k => $ticket)
        {
            if ($k >= $num) break;
            $i = floor($k / $perroom_ticket);

            $data = array(
                'adult'     => trim($_POST['adult'][$i]),
                'child'     => trim($_POST['child'][$i]),
                'people'    => trim($_POST['people'][$i]),
                'bed'       => trim($_POST['bed'][$i]),
                'birth'     => trim($_POST['birth'][$i]),
                'tel'       => trim($_POST['tel'][$i]),
                'email'     => trim($_POST['email'][$i]),
                'require'   => trim($_POST['require'][$i]),
                'ticket'    => 4,
                'group'     => $group,
                'room'      => $i,
                'checkin'   => $checkin,
                'checkout'  => $checkout,
                'supply'    => $supply,
                'data'      => json_encode(array('rooms'=>$rooms, 'floor'=>$floor, 'time'=>NOW)),
            );

            if ($supply == 'HAND') unset($data['supply']);
            if (!$data['adult']) json_return(null, 3, '登记成人数量不能为0');
            if (!$data['people']) json_return(null, 3, '必须登记一位入住人');

            $return['rooms'][] = array('people'=>$_POST['people'][$i]);

            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = $ticket['id'];
            $rs = $db -> prepare("UPDATE `ptc_order_room` SET {$sql} WHERE `id`=:id AND `ticket` IN (0,5)") -> execute($value);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 4, '保存错误或券已被使用，请刷新重试');
            }
        }

        $rs = $db -> prepare("UPDATE `ptc_order` SET `appointmentime`=:time WHERE `id`=:orderid") -> execute(array(':orderid'=>$order['id'], ':time'=>NOW));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        if (false === $this -> _status($id))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        $night = ($checkout - $checkin)/86400;
        $checkin = date('Y-m-d', $checkin);
        $checkout = date('m-d', $checkout);
        // order's log
        if (!order::_log($order, "第{$group}次预约，{$rooms}间{$night}晚（{$checkin}/{$checkout}），用券{$num}张", $return))
        {
            $db -> rollback();
            json_return(null, 8, '保存失败，请重试');
        }


        if ($db -> commit())
        {
            api::push('order', $order['order']);

            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
    }
    // _used




    /**
     * submit booking
     +-----------------------------------------
     * @access protected
     * @param array $order
     * @return void
     */
    protected function _submit($order)
    {
        if (!$_POST['id'] || !$order) json_return(null, 1, '请求信息有误，请重试');

        $id = (int)$_POST['id'];

        $db = db(config('db'));

        $sql = "SELECT b.`productname`, b.`itemname`, b.`hotel`, b.`hotelname`, b.`roomname`, b.`settleby`, a.`pid`, a.`ticket`, a.`group`, a.`room`, a.`data`, a.`pid`, a.`checkin`, a.`checkout`, h.`address`, h.`tel`
                FROM `ptc_order_room` AS a
                    LEFT JOIN `ptc_order_hotel` AS b ON a.`pid` = b.`id`
                    LEFT JOIN `ptc_hotel` AS h ON b.`hotel` = h.`id`
                WHERE a.`id`=:id AND a.`orderid`=:orderid";
        $item = $db -> prepare($sql) -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '产品不存在');

        if ($item[0]['ticket'] != 4) json_return(null, 2, '订单状态已变更，请刷新');

        $db -> beginTrans();

        $_temp = $item[0]['data'] ? json_decode($item[0]['data'], true) : array();
        $_temp['supplyorder'] = trim($_POST['supplyorder']);
        $_temp['confirmno'] = trim($_POST['confirmno']);

        $settletime = '';
        if ($item[0]['settleby'] < 0)
        {
            $settletime = ', `settletime`='.($item[0]['checkin'] + ($item[0]['settleby'] - 7) * 86400);
        }
        else if ($item['settleby'] == 4)
        {
            $settletime = ', `settletime`='.(strtotime(date('Y-m-t')) - 6 * 86400);
        }

        $rs = $db -> prepare("UPDATE `ptc_order_room` SET `ticket`=8, `data`=:data, `confirmno`=:confirmno {$settletime} WHERE `pid`=:pid AND `group`=:group AND `ticket`=4;")
                  -> execute(array(':pid'=>$item[0]['pid'], ':group'=>$item[0]['group'], ':data'=>json_encode($_temp), ':confirmno'=>$_temp['confirmno']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 3, '操作失败，请重试');
        }

        if (false === $this -> _status($item[0]['pid']))
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        // order's log
        if (!order::_log($order, "第{$item[0]['group']}次预约，预约成功", $_temp))
        {
            $db -> rollback();
            json_return(null, 7, '保存失败，请重试');
        }

        if ($db -> commit())
        {
            api::push('order', $order['order']);

            $item = array_merge($item[0], $_temp);
            sms::send($order['order'], 'hotel_ticket_booking_success', $item);

            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
    }
    // _submit





    /**
     * cancel booking
     +-----------------------------------------
     * @access protected
     * @param array $order
     * @return void
     */
    protected function _cancel($order)
    {
        if (!$_POST['id'] || !$order) json_return(null, 1, '请求信息有误，请重试');

        $id = (int)$_POST['id'];

        $db = db(config('db'));

        $sql = "SELECT b.`productname`, b.`itemname`, b.`hotelname`, b.`roomname`, a.`pid`, a.`ticket`, a.`group`, a.`room`, a.`data`, a.`pid`, a.`checkin`, a.`checkout`
                FROM `ptc_order_room` AS a
                    LEFT JOIN `ptc_order_hotel` AS b ON a.`pid` = b.`id`
                WHERE a.`id`=:id AND a.`orderid`=:orderid";
        $item = $db -> prepare($sql) -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '产品不存在');

        if ($_POST['status'] == 0)
        {
            if (!in_array($item[0]['ticket'], array(4,8))) json_return(null, 2, '订单状态已变更，请刷新');
            $status = 0;
        }
        else
        {
            if ($item[0]['ticket'] != 4) json_return(null, 3, '订单状态已变更，请刷新');
            $status = 5;
        }

        $db -> beginTrans();

        $_temp = $item[0]['data'] ? json_decode($item[0]['data'], true) : array();

        $rs = $db -> prepare("UPDATE `ptc_order_room` SET `ticket`=:status, `group`=0, `room`=0, `checkin`=0, `checkout`=0, `data`='' WHERE `pid`=:pid AND `group`=:group AND `ticket`=:ticket;")
                  -> execute(array(':pid'=>$item[0]['pid'], ':group'=>$item[0]['group'], ':ticket'=>$item[0]['ticket'], ':status'=>$status));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        if (false === $this -> _status($item[0]['pid']))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        $msg  = $status ? "第{$item[0]['group']}次预约，预约失败" : "取消本次预约";
        $data =  array('log'=>array('checkin'=>$item[0]['checkin'], 'checkout'=>$item[0]['checkout'], 'data'=>$_temp));
        if (!order::_log($order, $msg, $data))
        {
            $db -> rollback();
            json_return(null, 8, '保存失败，请重试');
        }

        if ($db -> commit())
        {
            if ($status)
            {
                $item = array_merge($item[0], $_temp);
                sms::send($order['order'], 'hotel_ticket_booking_fail', $item);
            }

            api::push('order', $order['order']);

            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
    }
    // _cancel





    /**
     * apply refund by admin
     +-----------------------------------------
     * @access protected
     * @param array $order
     * @return void
     */
    protected function _refund_apply($order)
    {
        if (!$_POST['id'] || !$order) json_return(null, 1, '请求信息有误，请重试');

        if (!(int)$_POST['num']) json_return(null, 1, '券数量不正确');

        $id = (int)$_POST['id'];

        $db = db(config('db'));
        $item = $db -> prepare("SELECT * FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid AND `supply`='TICKET'") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '退款券类不存在');

        $num = (int)$_POST['num'];

        $tickets = $db  -> prepare("SELECT * FROM `ptc_order_room` WHERE `pid`=:pid AND `orderid`=:orderid AND (`ticket`=0 OR `ticket`=5) ORDER BY `ticket` DESC;")
                        -> execute(array(':pid'=>$id, ':orderid'=>$order['id']));
        if ($tickets && count($tickets) < $num) json_return(null, 2, '可退券数量不足');


        $db -> beginTrans();
        // update ticket
        $price = $item[0]['total'] / $item[0]['rooms'];
        $data = json_encode(array('time'=>NOW, 'refund'=>$price));
        $rs = $db -> prepare("UPDATE `ptc_order_room` SET `ticket`=10, `data`=:data WHERE `orderid`=:orderid AND `pid`=:pid AND (`ticket`=0 OR `ticket`=5) ORDER BY `ticket` DESC LIMIT {$num};")
                  -> execute(array(':pid'=>$id, ':orderid'=>$order['id'], ':data'=>$data));
        if (!$rs)
        {
            $db -> rollback();
            json_return(null, 3, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=`refund`+:refund WHERE `id`=:id AND `orderid`=:orderid;")
                  -> execute(array(':id'=>$id, ':orderid'=>$order['id'], ':refund'=>(int)($price * $num) ));
        if (!$rs)
        {
            $db -> rollback();
            json_return(null, 4, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order` SET `refundtime`=:time WHERE `id`=:orderid") -> execute(array(':orderid'=>$order['id'], ':time'=>NOW));

        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        if (false === $this -> _status($id))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        if (!order::_log($order, "协助申请退款x{$num}张，<b>退款原因：</b>".$_POST['reason'], array('refund'=>(int)($price * $num), 'price'=>$price, 'num'=>$num)))
        {
            $db -> rollback();
            json_return(null, 8, '保存失败，请重试');
        }

        if ($db -> commit())
        {
            api::push('order', $order['order']);
            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
    }
    // _refund_apply





    /**
     * submit refund
     +-----------------------------------------
     * @access protected
     * @param array $order
     * @return void
     */
    protected function _refund_submit($order)
    {
        if (!$_POST['id'] || !$order) json_return(null, 1, '请求信息有误，请重试');

        $id = (int)$_POST['id'];

        $status = (int)$_POST['status'] ? 11 : 0;

        $db = db(config('db'));
        $item = $db -> prepare("SELECT `id`,`refund`,`status` FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid AND `supply`='TICKET'") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '退款产品不存在');

        $tickets = $db  -> prepare("SELECT `ticket`, `data` FROM `ptc_order_room` WHERE `pid`=:pid AND `orderid`=:orderid AND `ticket`=10;")
                        -> execute(array(':pid'=>$id, ':orderid'=>$order['id']));
        if (!$tickets) json_return(null, 2, '未找到退款项');

        $db -> beginTrans();
        // update ticket
        $rs = $db -> prepare("UPDATE `ptc_order_room` SET `ticket`=:status WHERE `orderid`=:orderid AND `pid`=:pid AND `ticket`=10;")
                  -> execute(array(':pid'=>$id, ':orderid'=>$order['id'], ':status'=>$status));
        if (!$rs)
        {
            $db -> rollback();
            json_return(null, 3, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=0 WHERE `orderid`=:orderid AND `id`=:id;") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 4, '操作失败，请重试');
        }

        if ($status)
        {
            $rs = $db -> prepare("UPDATE `ptc_order` SET `refund`=`refund`+:refund, `refundedtime`=:time WHERE `id`=:orderid;") -> execute(array(':refund'=>$item[0]['refund'], ':time'=>NOW, ':orderid'=>$order['id']));
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 5, '操作失败，请重试');
            }
        }

        if (false === $this -> _status($id))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        $num = count($tickets);
        $message = $status ? "退款退订完成{$num}张(¥{$item[0]['refund']})" : "拒绝退款{$num}张";
        if (!order::_log($order, $message, array('refund'=>$item[0]['refund'], 'num'=>$num)))
        {
            $db -> rollback();
            json_return(null, 7, '保存失败，请重试');
        }

        if ($db -> commit())
        {
            api::push('order', $order['order']);
            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
    }
    // _refund_submit



    // 结算确认
    public function _settle($order)
    {
        if (!$_POST['id'] || !$order) json_return(null, 1, '请求信息有误，请重试');

        $id = (int)$_POST['id'];

        $db = db(config('db'));

        $item = $db -> prepare("SELECT `id`,`refund`,`status` FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid AND `supply`='TICKET'") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '结算产品不存在');

        $tickets = $db  -> prepare("SELECT COUNT(*) AS c, GROUP_CONCAT(`id`) AS `ids` FROM `ptc_order_room` WHERE `pid`=:pid AND `orderid`=:orderid AND `settletime`>0 AND `settle`=0;") -> execute(array(':pid'=>$id, ':orderid'=>$order['id']));
        if (!$tickets[0]['c']) json_return(null, 2, '未找到待结算券');

        $db -> beginTrans();
        // update ticket
        $rs = $db -> prepare("UPDATE `ptc_order_room` SET `settle`=1 WHERE `orderid`=:orderid AND `pid`=:pid AND `settletime`>0  AND `settle`=0;")
                  -> execute(array(':pid'=>$id, ':orderid'=>$order['id']));
        if (!$rs)
        {
            $db -> rollback();
            json_return(null, 3, '操作失败，请重试');
        }

        $check = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_order_room` WHERE `orderid`=:orderid AND `settle`=0;") -> execute(array(':orderid'=>$order['id']));
        if (!$check[0]['c'])
        {
            $rs = $db -> prepare("UPDATE `ptc_order` SET `clear`=1 WHERE `id`=:orderid;") -> execute(array(':orderid'=>$order['id']));
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 5, '操作失败，请重试');
            }
        }

        // order's log
        $message = '完成结算'.$tickets[0]['c'].'张券';
        if (!order::_log($order, $message, array('ids'=>$tickets[0]['ids'])))
        {
            $db -> rollback();
            json_return(null, 7, '保存失败，请重试');
        }

        if ($db -> commit())
        {
            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
    }
    // _settle



    /**
     * Complete the booking
     *
     * @access public
     * @param mixed $result
     * @param array $order
     * @param string $type
     * @return void
     */
    public function complete($result, $order, $type)
    {
        if (!$order || $type != 'hotel_ticket') return $result;

        $today = strtotime(date('Y-m-d 00:00:00'));

        $db = db(config('db'));

        $items = $db -> prepare('SELECT `pid`, `group` FROM `ptc_order_room` WHERE `order`=:order AND `ticket`=8 AND `checkout`<:now GROUP BY `pid`, `group`;')
                     -> execute(array(':order'=>$order['order'], ':now'=>$today));

        if (!$items) return $result;


        $db -> beginTrans();

        // Update room
        $rs = $db   -> prepare('UPDATE `ptc_order_room` SET `ticket`=9 WHERE `order`=:order AND `ticket`=8 AND `checkout`<:now;')
                    -> execute(array(':order'=>$order['order'], ':now'=>$today));
        if (!$rs)
        {
            $db -> rollback();
            return false;
        }


        $itemids = array();

        foreach ($items as $item)
        {
            // order's log
            if (!order::_log($order, "第{$item['group']}次预约已入住完成", array('group'=>$item['group']), '系统自动'))
            {
                $db -> rollback();
                return false;
            }

            if (isset($itemids[$item['pid']])) continue;

            // Update hotel
            if (false === $this -> _status($item['pid']))
            {
                $db -> rollback();
                return false;
            }

            // Update order
            if (false === order::_status($order))
            {
                $db -> rollback();
                return false;
            }

            $itemids[$item['pid']] = 1;
        }


        if ($db -> commit())
        {
            api::push('order', $order['order']);
            return true;
        }
        else
        {
            $db -> rollback();
            return false;
        }
    }
    // complete





    // get order and order-hotel status
    protected function _status($id)
    {
        $db = db(config('db'));

        $items = $db -> prepare("SELECT `ticket` FROM `ptc_order_room` WHERE `pid`=:pid") -> execute(array(':pid'=>$id));
        $_status = array();
        foreach ($items as $v)
            $_status[] = $v['ticket'] ? $v['ticket'] : 3;

        $s = array_unique($_status);
        sort($s, SORT_NUMERIC);
        $str = implode('.', $s);
        $status = array(0,0);
        if (count($s) == 1)
        {
            $status[] = $s[0];
        }
        else
        {
            $status[] = 0;
            if (in_array(9, $s)) $status[] = 9;
            if (in_array(8, $s)) $status[] = 8;
            if (in_array(3, $s)) $status[] = 3;
            if (in_array(12, $s)) $status[] = 12;
            if (in_array(11, $s))
            {
                if (array_filter($status))
                    $status[] = 16;
                else
                    $status[] = 11;
            }
            if (in_array(16, $s)) $status[] = 16;
            if (in_array(4, $s)) $status[] = 4;
            if (in_array(13, $s)) $status[] = 13;
            if (in_array(10, $s)) $status[] = 10;
        }

        $status = array_reverse($status);

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `status`=:status, `status2`=:status2, `status3`=:status3 WHERE `id`=:id;")
                  -> execute(array(':id'=>$id, ':status'=>$status[0], ':status2'=>$status[1], ':status3'=>$status[2]));
        if (false === $rs)
        {
            return false;
        }

        return true;
    }
    // _status



    // order confirmation
    static function confirmation($confirmation, $order)
    {
        $db = db(config('db'));

        $sql = "SELECT `id`, `product` FROM `ptc_order_hotel` WHERE `orderid`=:oid AND `supply`='TICKET' AND `producttype`=0;";
        $hotel = $db -> prepare($sql) -> execute(array(':oid'=>$order['id']));
        if (!$hotel) return $confirmation;

        $groups = array();
        foreach ($confirmation as $v)
            $groups[] = $v['group'];

        $sql = "SELECT `order`,`group`,0 AS `send` FROM `ptc_order_room` WHERE `orderid`=:oid AND `ticket` IN (8, 9) GROUP BY `group`;";
        $news = $db -> prepare($sql) -> execute(array(':oid'=>$order['id']));

        foreach ($news as $v)
            if (!in_array($v['group'], $groups))
                $confirmation[] = $v;

        return $confirmation;
    }
    // confirmation



}

