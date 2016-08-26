<?php
class hotel_auto_prepay_order extends hotel_auto_prepay_hook
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
    public function book_room($order, $code='', $num=1, $checkin=0, $checkout=0, $remark='')
    {
        $args = explode('_', $code);
        if ($args[count($args) - 1] !== 'product2') return $order;

        $key = $args[0];

        $sql = "SELECT  p.*, h.`name` AS `hotel_name`, r.`name` AS `room_name`, d.`name` AS `product_name`, di.`name` AS `item_name`, di.`target` AS `city`, c.`pid` AS `country`, di.`ext` AS `nights`,
                        d.`updatetime` AS `up1`, di.`updatetime` AS `up2`,
                        {$this -> _profit} AS `profit`
                FROM `ptc_hotel_price_date` AS p
                    LEFT JOIN `ptc_product` AS d ON p.`supplyid` = d.`id` AND d.`status` = 1
                    LEFT JOIN `ptc_product_item` AS di ON p.`room` = di.`id`
                    LEFT JOIN `ptc_district` AS c ON c.`id` = di.`target`
                    LEFT JOIN `ptc_hotel` AS h ON p.`hotel` = h.`id`
                    LEFT JOIN `ptc_hotel_room_type` AS r ON p.`roomtype` = r.`id`
                    LEFT JOIN `ptc_org_profit` AS fd1 ON fd1.`org` = :org AND fd1.`payment` = 'prepay' AND fd1.`objtype` = 'product2' AND fd1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fd2 ON fd2.`org` = :org AND fd2.`payment` = 'prepay' AND fd2.`objtype` = 'product2' AND fd2.`objid` = p.`supplyid`
                    LEFT JOIN `ptc_org_profit` AS fd3 ON fd3.`org` = :org AND fd3.`payment` = 'prepay' AND fd3.`objtype` = 'item' AND fd3.`objid` = p.`room`
                WHERE p.`key` = :code AND p.`close` = 0
                ORDER BY p.`date` ASC;";

        $db = db(config('db'));
        $pro = $db -> prepare($sql) -> execute(array(':code'=>$key, ':org'=>api::$org));

        if (!$pro || !$pro[0]['product_name'] || !$pro[0]['item_name'] || !$pro[0]['hotel'] || !$pro[0]['room'])
        {
            api::set_error(614);
            return false;
        }
        $pro = $pro[0];

        if ($pro['advance'] && $pro['date'] - NOW <= $pro['advance'] * 86400)
        {
            api::set_error(616);
            return false;
        }

        if ($pro['filled'] == 1)
        {
            api::set_error(623);
            return false;
        }

        // locked allot
        $sql = "SELECT SUM(a.`rooms`) AS `sum`
                FROM `ptc_order_hotel` AS a
                LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                WHERE a.`supply`='PRODUCT' AND a.`supplyid`=:product AND a.`hotel`=:hotel AND a.`room`=:room AND a.`checkin`=:date AND a.`status`=2 AND b.`expire`>:time";
        $locked = $db -> prepare($sql) -> execute(array(':product'=>$pro['supplyid'], ':hotel'=>$pro['hotel'], ':room'=>$pro['room'], ':date'=>$pro['date'], ':time'=>NOW));
        if ($pro['allot'] - $pro['sold'] - $locked[0]['sum'] < $num)
        {
            api::set_error(626);
            return false;
        }

        $status = $pro['allot'] - $pro['sold'] >= $num ? 2 : 1;

        $_order = array(
            'product'       => $pro['supplyid'],
            'producttype'   => 2,
            'productname'   => $pro['product_name'],
            'itemname'      => $pro['item_name'],
            'country'       => $pro['country'],
            'city'          => $pro['city'],
            'hotelname'     => $pro['hotel_name'],
            'roomname'      => roomname($pro['room_name'], $pro['bed']),
            'hotel'         => $pro['hotel'],
            'room'          => $pro['room'],
            'roomtype'      => $pro['roomtype'],
            'checkin'       => $pro['date'],
            'checkout'      => $pro['date'] + 86400 * $pro['nights'],
            'supply'        => 'PRODUCT',
            'supplyid'      => $pro['supplyid'],
            'rooms'         => $num,
            'nights'        => $pro['nights'],
            'floor'         => $num * $pro['price'],
            'total'         => $num * ($pro['price'] + round($pro['profit'])),
            'status'        => $status,
            'remark'        => (string)$remark,
        );

        $_order['return'] = array(
            'hotel'     => $_order['hotelname'],
            'room'      => $_order['roomname'],
            'product'   => $_order['productname'],
            'item'      => $_order['itemname'],
            'checkin'   => $_order['checkin'],
            'checkout'  => $_order['checkout'],
            'nights'    => $_order['nights'],
            'num'       => $num,
            'total'     => $_order['total'],
            'status'    => $status,
        );

        $_order['data']   = array(
            'type'      => 'hotel_auto_prepay',
            'price'     => $pro['price'],
            'profit'    => $pro['profit'],
            'product'   => $pro['supplyid'],
            'item'      => $pro['room'],
            'product_update' => $pro['up1'],
            'item_update'    => $pro['up2'],
            'service'        => array(),
        );

        $standby = json_decode($pro['standby'], true);
        if (!empty($standby['child'])) $_order['data']['service']['CHILD'] = $standby['child'];
        if (!empty($standby['baby']))  $_order['data']['service']['BABY']  = $standby['baby'];

        $_order['data'] = json_encode($_order['data']);

        return array_merge($order, $_order);
    }
    // booking for room







    /**
     * Booking for auto
     +-----------------------------------------
     * @access public
     * @param array $order
     * @param string $code
     * @param int $num
     * @param int $date
     * @param string $remark
     * @return void
     */
    public function book_auto($order, $code='', $num=1, $date=0, $remark='')
    {
        $args = explode('_', $code);
        if ($args[count($args) - 1] !== 'product2') return $order;

        $key = $args[0];

        $prokey = $args[count($args) - 2];
        if (substr($prokey, 0, 4) != 'auto') return $order;

        list($product, $item) = explode('.', substr($prokey, 4));

        $child_profit = str_replace('profit', 'child', $this -> _profit);

        $baby_profit = str_replace('profit', 'baby', $this -> _profit);

        $sql = "SELECT  p.*, f.`code` AS `auto_code`, f.`company` AS `auto_company`, d.`name` AS `product_name`, di.`name` AS `item_name`,
                        {$this -> _profit} AS `profit_adult`,
                        {$child_profit} AS `profit_child`,
                        {$baby_profit} AS `profit_baby`,
                        d.`updatetime` AS `up1`, di.`updatetime` AS `up2`
                FROM `ptc_auto_price_date` AS p
                    LEFT JOIN `ptc_product` AS d ON d.`id` = :product
                    LEFT JOIN `ptc_product_item` AS di ON di.`id` = :item
                    LEFT JOIN `ptc_auto` AS f ON p.`auto` = f.`id`
                    LEFT JOIN `ptc_org_profit` AS fd1 ON fd1.`org` = :org AND fd1.`payment` = 'prepay' AND fd1.`objtype` = 'product2' AND fd1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fd2 ON fd2.`org` = :org AND fd2.`payment` = 'prepay' AND fd2.`objtype` = 'product2' AND fd2.`objid` = :product
                    LEFT JOIN `ptc_org_profit` AS fd3 ON fd3.`org` = :org AND fd3.`payment` = 'prepay' AND fd3.`objtype` = 'item' AND fd3.`objid` = :item
                WHERE p.`key` = :code AND p.`close` = 0
                ORDER BY p.`date` ASC;";

        $db = db(config('db'));
        $pro = $db -> prepare($sql) -> execute(array(':code'=>$key, ':org'=>api::$org, ':product'=>$product, ':item'=>$item));

        if (!$pro || !$pro[0]['product_name'] || !$pro[0]['item_name'] || !$pro[0]['auto_code'])
        {
            api::set_error(614);
            return false;
        }
        $pro = $pro[0];

        if ($pro['advance'] && $pro['date'] - NOW <= $pro['advance'] * 86400)
        {
            api::set_error(616);
            return false;
        }

        // locked allot
        $sql = "SELECT SUM(a.`num`) AS `sum`
                FROM `ptc_order_auto` AS a
                LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                WHERE a.`supply`='PRODUCT' AND a.`auto`=:auto AND a.`status`=2 AND a.`date`=:date AND b.`expire`>:time";
        $locked = $db -> prepare($sql) -> execute(array(':auto'=>$pro['auto'], ':date'=>$date, ':time'=>NOW));
        if ($pro['allot'] - $pro['sold'] - $locked[0]['sum'] < $num)
        {
            api::set_error(626);
            return false;
        }

        if ($pro['filled'] == 1)
        {
            api::set_error(623);
            return false;
        }

        $_order = array(
            'product'       => $product,
            'producttype'   => 2,
            'productname'   => $pro['product_name'],
            'itemname'      => $pro['item_name'],
            'auto'          => $pro['auto'],
            'autoname'      => "{$pro['auto_code']} ({$pro['auto_company']})",
            'autocode'      => $pro['auto_code'],
            'package'       => $item,
            'date'          => $date,
            'floor'         => 0,
            'total'         => 0,
            'num'           => (int)$num,
            'driver'        => '',
            'supply'        => 'PRODUCT',
            'supplyid'      => $pro['supplyid'],
            'start'         => $pro['start'],
            'end'           => $pro['end'],
            'advance'       => $pro['advance'],
            'status'        => 0,
            'remark'        => (string)$remark,
        );

        $peoples = array();
        $driver = array();
        $_num = 0;
        foreach ($order['driver'] as $key => $people)
        {
            $_num++;
            $price = $pro['price'];

            $floor = $price;
            $total = $price + round($pro['profit_adult']);

            $_order['floor'] += $floor;
            $_order['total'] += $total;

            $peoples[] = array('people'=>$people['people'], 'credential'=>$people['credential']);
            $driver[]  = "{$people['people']}|{$people['credential']}";
        }

        $status = $pro['allot'] - $pro['sold'] >= $_num ? 2 : 1;
        if ($status == 1)
        {
            api::set_error(626);
            return false;
        }

        if ($num != $_num)
        {
            api::set_error(630);
            return false;
        }

        $_order['driver'] = implode('||', $driver);
        $_order['status'] = $status;

        $_order['return'] = array(
            'product'       => $_order['productname'],
            'item'          => $_order['itemname'],
            'autoname'      => $_order['autoname'],
            'autocode'      => $_order['autocode'],
            'num'           => $_order['num'],
            'total'         => $_order['total'],
            'status'        => $status,
            'peoples'       => $peoples,
        );

        $_order['data']   = json_encode(array(
            'type'      => 'hotel_auto',
            'price'     => $pro['price'],
            'child'     => $pro['child'],
            'baby'      => $pro['baby'],
            'profit_adult'  => round($pro['profit_adult']),
            'profit_child'  => round($pro['profit_child']),
            'profit_baby'   => round($pro['profit_baby']),
            'item'      => $pro['package'],
            'product_update'=> $pro['up1'],
            'item_update'   => $pro['up2'],
        ));

        return array_merge($order, $_order);
    }
    // book_auto









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
        if ($data['supply'] != 'PRODUCT' || $data['product'] == 0 || $data['producttype'] != 2) return $status;

        static $_sms_data = array();

        $db = db(config('db'));

        $product = $db -> prepare("SELECT * FROM `ptc_product` WHERE `id`=:pro AND `type`=2 AND `payment`='prepay';") -> execute(array(':pro'=>$data['product']));
        if (!$product) return -614;

        $_sms_data[$type] = $data;

        if ($type == 'auto')
        {
            // load auto peoples for sms
            $drivers = explode('||', $data['driver']);
            $peoples = array();
            foreach ($drivers as $v)
            {
                list($people, $credential) = explode('|', $v);
                $peoples[] = $people;
            }

            $_sms_data['auto']['driver'] = implode(',', $peoples);
        }

        // allot is locking
        if (NOW < $expire)
        {
            if ($data['status'] == 2)
            {
                $db = db(config('db'));
                if ($type == 'hotel')
                {
                    $rs = $db -> prepare("UPDATE `ptc_hotel_price_date` SET `sold`=`sold`+:num WHERE `supply`='EBK' AND `supplyid`=:sup AND `payment`=3 AND `hotel`=:hotel AND `room`=:room AND `date`=:date;")
                              -> execute(array(':sup'=>$data['supplyid'], ':hotel'=>$data['hotel'], ':room'=>$data['room'], ':date'=>$data['checkin'], ':num'=>(int)$data['rooms']));
                    if ($rs === false) return -901;
                }

                if ($type == 'auto')
                {
                    $rs = $db -> prepare("UPDATE `ptc_auto_price_date` SET `sold`=`sold`+:num WHERE `auto`=:auto AND `date`=:date;")
                              -> execute(array(':auto'=>$data['auto'], ':date'=>$data['date'], ':num'=>(int)$data['num']));
                    if ($rs === false) return -901;

                    // auto hook is later than hotel hook
                    sms::send($data['order'], 'hotel_auto_prepay_pay', $_sms_data);
                }
            }

            return $data['status'] == 2 ? 4 : $data['status'];
        }
        else
        {
            switch ($type)
            {
                case 'hotel':
                    // price
                    $pro = $db  -> prepare("SELECT `key`,`allot`,`sold` FROM `ptc_hotel_price_date` WHERE `supply`='EBK' AND `supplyid`=:sup AND `payment`=3 AND `hotel`=:hotel AND `room`=:room AND `date`=:date;")
                                -> execute(array(':sup'=>$data['supplyid'], ':hotel'=>$data['hotel'], ':room'=>$data['room'], ':date'=>$data['checkin']));
                    if (!$pro) return -614;
                    $pro = $pro[0];

                    // locked allot
                    $sql = "SELECT SUM(a.`rooms`) AS `sum`
                            FROM `ptc_order_hotel` AS a
                            LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                            WHERE a.`supply`='PRODUCT' AND a.`supplyid`=:product AND a.`hotel`=:hotel AND a.`room`=:room AND a.`checkin`=:date AND a.`status`=2 AND b.`expire`>:time";
                    $locked = $db -> prepare($sql) -> execute(array(':product'=>$data['supplyid'], ':hotel'=>$data['hotel'], ':room'=>$data['room'], ':date'=>$data['checkin'], ':time'=>NOW));

                    if ($pro['allot'] - $pro['sold'] - $locked[0]['sum'] < $data['rooms'])
                    {
                        return 13;
                    }
                    else
                    {
                        $rs = $db -> prepare("UPDATE `ptc_hotel_price_date` SET `sold`=`sold`+:num WHERE `key`=:key") -> execute(array(':key'=>$pro['key'], ':num'=>(int)$data['rooms']));
                        if (!$rs) return -501;

                        return 4;
                    }
                    break;

                case 'auto':
                    // price
                    $pro = $db  -> prepare("SELECT `key`,`allot`,`sold` FROM `ptc_auto_price_date` WHERE `auto`=:auto AND `date`=:date;")
                                -> execute(array(':auto'=>$data['auto'], ':date'=>$data['date']));
                    if (!$pro) return -614;
                    $pro = $pro[0];

                    // locked allot
                    $sql = "SELECT SUM(a.`num`) AS `sum`
                            FROM `ptc_order_auto` AS a
                            LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                            WHERE a.`supply`='PRODUCT' AND a.`auto`=:auto AND a.`date`=:date AND a.`status`=2 AND b.`expire`>:time";
                    $locked = $db -> prepare($sql) -> execute(array(':auto'=>$data['auto'], ':date'=>$data['date'], ':time'=>NOW));

                    // auto hook is later than hotel hook
                    sms::send($data['order'], 'hotel_auto_prepay_pay', $_sms_data);

                    if ($pro['allot'] - $pro['sold'] - $locked[0]['sum'] < $data['num'])
                    {
                        return 13;
                    }
                    else
                    {
                        $rs = $db -> prepare("UPDATE `ptc_auto_price_date` SET `sold`=`sold`+:num WHERE `key`=:key") -> execute(array(':key'=>$pro['key'], ':num'=>(int)$data['num']));
                        if (!$rs) return -501;

                        return 4;
                    }
                    break;
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
        if ($data['supply'] != 'PRODUCT' || $data['product'] == 0 || $data['producttype'] != 2) return $status;

        if (!in_array($status, array(3,4))) return -610;

        $db = db(config('db'));

        if ($type == 'hotel')
        {
            $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=`total` WHERE `id`=:id") -> execute(array(':id'=>$data['id']));
            if (false === $rs) return -502;
        }

        else if ($type == 'auto')
        {
            $rs = $db -> prepare("UPDATE `ptc_order_auto` SET `refund`=`total` WHERE `id`=:id") -> execute(array(':id'=>$data['id']));
            if (false === $rs) return -502;
        }

        return 10;
    }
    // apply_refund





    /* ================= MANAGE TEMPLATE ================= */

    protected $_order = array();


    /**
     * extend of manage order view
     +-----------------------------------------
     * @access public
     * @param array $subitem
     * @param array $order
     * @param string $type
     * @param string $mode
     * @return void
     */
    public function tpl_extend($subitem, $order, $type='hotel', $mode)
    {
        if ( !$subitem['product'] || $subitem['product_type'] != 2 || $subitem['product_payment'] != 'prepay') return;

        $_status = order::status();

        switch ($type)
        {
            case 'hotel':
                switch ($subitem['status'])
                {
                    case '3':
                    case '4':
                    case '10':
                    case '13':
                        $status = '<strong style="color:red;">'.$_status[$subitem['status']].'</strong>';
                        break;
                    default:
                        $status = $_status[$subitem['status']];
                }

                echo "<div class=\"info\"><b>酒店状态：</b>{$status}</div>";

                echo "<table class=\"list\">";
                echo "<tr><th>序号</th><th>入住/离店时间</th><th>入住人</th><th class=\"text-right\">床型及要求</th></tr>";

                $checkin = date('Y-m-d', $subitem['checkin']);
                $checkout = date('m-d', $subitem['checkout']);
                $night = ($subitem['checkout'] - $subitem['checkin'])/86400;

                $i = 1;
                foreach ($subitem['rooms'] as $v)
                {
                    switch ($v['bed'])
                    {
                        case 'S': $bed = '单人床'; break;
                        case 'D': $bed = '双人大床'; break;
                        case 'T': $bed = '双人双床'; break;
                        case 'K': $bed = '超大床'; break;
                        case 'C': $bed = '圆床'; break;
                        case 'D1': $bed = '尽量大床'; break;
                        case 'T1': $bed = '尽量双床'; break;
                        case 'D2': $bed = '务必大床'; break;
                        case 'T2': $bed = '务必双床'; break;
                        case '2': $bed = '大/双床'; break;
                        case 'O': $bed = '特殊床型'; break;
                    }
                    echo "<tr id=\"pro-{$subitem['product']}-h{$v['id']}\"><td>{$i}</td><td>{$checkin} / {$checkout} &nbsp; 共{$night}晚</td><td>{$v['people']}</td><td class=\"text-right\">{$bed}{$v['require']}</td></tr>";
                    $i++;
                }
                echo "</table>";
                break;

            case 'auto':
                switch ($subitem['status'])
                {
                    case '3':
                    case '4':
                    case '10':
                    case '13':
                        $status = '<strong style="color:red;">'.$_status[$subitem['status']].'</strong>';
                        break;
                    default:
                        $status = $_status[$subitem['status']];
                }

                echo "<div class=\"info\"><b>车辆状态：</b>{$status}</div>";

                echo "<table class=\"list\">";
                echo "<tr><th>序号</th><th>驾驶人</th><th class=\"text-right\">资料</th></tr>";

                $i = 1;
                foreach ($subitem['driver'] as $v)
                {
                    echo "<tr id=\"pro-{$subitem['product']}\"><td>{$i}</td><td>{$v['people']}</td><td class=\"text-right\">{$v['credential']}</td></tr>";
                    $i++;
                }
                echo "</table>";
                break;
        }

        return $subitem;
    }
    // tpl_extend




    /**
     * operation of manage order view
     +-----------------------------------------
     * @access public
     * @param mixed $product
     * @return void
     */
    public function tpl_operation($product, $order, $mode)
    {
        if ($product['type'] != 2 || $product['payment'] != 'prepay') return;

        if ($order['status'] < 3) return;

        foreach ($product['items'] as $item)
        {
            switch ($item['type'])
            {
                case 'hotel':
                    $hotel = $item;
                case 'auto':
                    $auto = $item;
            }
        }

        include dirname(__FILE__).'/order/order_operation.tpl.php';
    }
    // tpl_operation




    /**
     * footer of manage order view
     +-----------------------------------------
     * @access public
     * @param mixed $product
     * @return void
     */
    public function tpl_footer($product, $order, $mode)
    {
        if ($product['type'] != 2 || $product['payment'] != 'prepay') return;

        $status = order::status();

        foreach ($product['items'] as $item)
        {
            switch ($item['type'])
            {
                case 'hotel':
                    $hotel = $item;
                case 'auto':
                    $auto = $item;
            }
        }

        include dirname(__FILE__).'/order/order_footer.tpl.php';
    }
    // tpl_footer








    /* ================= OPERATE ================= */

    public function operate($order)
    {
        switch ($_POST['method'])
        {
            case 'pro2-booking':
                $this -> _submit($order);
                break;

            case 'pro2-refund':
                $this -> _refund_apply($order);
                break;

            case 'order-refund':
                if (empty($_POST['type']) || $_POST['type'] != 'hotel-auto-prepay') return;

                $this -> _refund_submit($order);
                break;


            default:
        }
    }




    /**
     * submit booking (hotel)
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
        $item = $db -> prepare("SELECT * FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid AND `supply`='PRODUCT'") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '产品不存在');

        if ($item[0]['status'] != 4) json_return(null, 2, '订单状态已变更，请刷新');

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `status`=8, `supplyorder`=:supplyorder, `confirmno`=:confirmno WHERE `id`=:id")
                  -> execute(array(':supplyorder'=>trim($_POST['supplyorder']), ':confirmno'=>trim($_POST['confirmno']), ':id'=>$id));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请刷新重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order_auto` SET `status`=8 WHERE `orderid`=:orderid") -> execute(array(':orderid'=>$order['id']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请刷新重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        $data = array('status'=>8, 'supplyorder'=>trim($_POST['supplyorder']), 'confirmno'=>trim($_POST['confirmno']));
        if (!order::_log($order, "酒店预约完成，确认号：<b>{$data['confirmno']}</b>", $data))
        {
            $db -> rollback();
            json_return(null, 8, '保存失败，请重试');
        }

        api::push('order', $order['order']);

        if ($db -> commit())
        {
            json_return(1);
        }
        else
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请刷新重试');
        }
    }
    // _hotel_submit




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

        $id = (int)$_POST['id'];

        $db = db(config('db'));
        $db -> beginTrans();

        $total = 0;
        $_refund = $refund = (int)$_POST['price'];

        if (!$refund) json_return(null, 2, '退款金额不能为0');

        // refund hotel
        $hotels = $db -> prepare("SELECT `id`,`status`,`total` FROM `ptc_order_hotel` WHERE `product`=:id AND `orderid`=:orderid") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$hotels) json_return(null, 2, '订单明细不存在');

        foreach ($hotels as $item)
        {
            if ($_refund <= 0) break;

            if (!in_array($item['status'], array(3,4,13)))
            {
                $db -> rollback();
                json_return(null, 2, '订单状态已变更，请刷新');
            }
            $total += $item['total'];

            if ($_refund >= $item['total'])
                $_price = $item['total'];
            else
                $_price = $_refund;

            $_refund -= $item['total'];

            $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `status`=10, `refund`=:price WHERE `id`=:id;") -> execute(array(':id'=>$item['id'], ':price'=>$_price));
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 4, '操作失败，请重试');
            }
        }

        // refund auto
        $autos = $db -> prepare("SELECT `id`,`status`,`total` FROM `ptc_order_auto` WHERE `product`=:id AND `orderid`=:orderid") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$autos) json_return(null, 3, '订单明细不存在');

        foreach ($autos as $item)
        {
            if ($_refund <= 0) break;

            if (!in_array($item['status'], array(3,4,13)))
            {
                $db -> rollback();
                json_return(null, 2, '订单状态已变更，请刷新');
            }

            $total += $item['total'];

            if ($_refund >= $item['total'])
                $_price = $item['total'];
            else
                $_price = $_refund;

            $_refund -= $item['total'];
            $rs = $db -> prepare("UPDATE `ptc_order_auto` SET `status`=10, `refund`=:price WHERE `id`=:id;") -> execute(array(':id'=>$item['id'], ':price'=>$_price));
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 5, '操作失败，请重试');
            }
        }

        $rs = $db -> prepare("UPDATE `ptc_order` SET `refundtime`=:time WHERE `id`=:orderid") -> execute(array(':orderid'=>$order['id'], ':time'=>NOW));

        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        // total < price
        if ($total < $refund)
        {
            $db -> rollback();
            json_return(null, 7, '退款金额不能超过本项小计金额');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        if (!order::_log($order, "申请订单退款，退款金额¥{$refund}，<b>退款原因：</b>".$_POST['reason'], null))
        {
            $db -> rollback();
            json_return(null, 8, '保存失败，请重试');
        }

        api::push('order', $order['order']);

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
        if (!$order) json_return(null, 1, '请求信息有误，请重试');

        $status = (int)$_POST['status'] ? 11 : 3;

        $db = db(config('db'));
        $h_item = $db -> prepare("SELECT `id`,`refund`,`status` FROM `ptc_order_hotel` WHERE `orderid`=:orderid AND `status`=10") -> execute(array(':orderid'=>$order['id']));
        if (!$h_item) json_return(null, 2, '退款产品不存在');

        $f_item = $db -> prepare("SELECT `id`,`refund`,`status` FROM `ptc_order_auto` WHERE `orderid`=:orderid AND `status`=10") -> execute(array(':orderid'=>$order['id']));
        if (!$f_item) json_return(null, 3, '退款产品不存在');

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=0, `status`=:status WHERE `orderid`=:orderid AND `status`=10;") -> execute(array(':status'=>$status, ':orderid'=>$order['id']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 4, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order_auto` SET `refund`=0, `status`=:status WHERE `orderid`=:orderid AND `status`=10;") -> execute(array(':status'=>$status, ':orderid'=>$order['id']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        $refund = 0;
        foreach($h_item as $v)
            $refund += $v['refund'];

        foreach($f_item as $v)
            $refund += $v['refund'];

        $rs = $db -> prepare("UPDATE `ptc_order` SET `refund`=`refund`+:refund, `status`=:status WHERE `id`=:orderid;") -> execute(array(':refund'=>$refund, ':status'=>$status, ':orderid'=>$order['id']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        // order's log
        $num = count($tickets);
        $message = $status ? "退款退订完成" : "拒绝退款";
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
        if (!$order || $type != 'hotel_auto_prepay') return $result;

        $today = strtotime(date('Y-m-d 00:00:00'));

        $db = db(config('db'));

        $items = $db -> prepare('SELECT * FROM `ptc_order_hotel` WHERE `order`=:order AND `status`=8 AND `checkout`<:now;')
                     -> execute(array(':order'=>$order['order'], ':now'=>$today));

        if (!$items) return $result;


        $db -> beginTrans();

        // Update hotel
        $rs = $db   -> prepare('UPDATE `ptc_order_hotel` SET `status`=9 WHERE `order`=:order AND `status`=8;')
                    -> execute(array(':order'=>$order['order']));
        if (!$rs)
        {
            $db -> rollback();
            return false;
        }

        // Update auto
        $rs = $db   -> prepare('UPDATE `ptc_order_auto` SET `status`=9 WHERE `order`=:order AND `status`=8;')
                    -> execute(array(':order'=>$order['order']));
        if (!$rs)
        {
            $db -> rollback();
            return false;
        }

        // order's log
        if (!order::_log($order, "已入住完成[h{$item['id']}]", array('id'=>$item['id']), '系统自动'))
        {
            $db -> rollback();
            return false;
        }

        // Update order
        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
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



    // order confirmation
    static function confirmation($confirmation, $order)
    {
        if ($confirmation) return $confirmation;

        if ($order['status'] != 8 && $order['status'] != 9) return $confirmation;

        return array('order'=>$order['order'], 'group'=>0, 'send'=>0);
    }
    // confirmation



}
