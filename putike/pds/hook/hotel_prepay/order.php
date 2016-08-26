<?php
// hotel prepay product hook of order operation
class hotel_prepay_order extends hotel_prepay_hook
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
        $sign = 0;
        if ($code[0] == '_')
        {
            $code = substr($code, 1);
            $sign = 1;
        }

        $args = explode('_', $code);
        if ($args[count($args) - 1] !== 'prepay') return $order;

        array_pop($args);
        $uncombine = array_shift($args);

        if (!$checkin || !$checkout)
        {
            api::set_error(621);
            return false;
        }

        $night = ($checkout - $checkin) / 86400;
        if (!$args || (count($args) > 1 && count($args) < $night) || in_array('-', $args))
        {
            order::set_error(618);
            return false;
        }

        $db = db(config('db'));

        // confirm prices & allot
        $_tmp = $db -> prepare("SELECT `hotel`,`supply` FROM `ptc_hotel_price_date` WHERE `uncombine`=:uncombine LIMIT 0,1") -> execute(array(':uncombine'=>$uncombine));

        if ($_tmp[0]['supply'] != 'EBK')
        {
            set_time_limit(60);
            $rs = curl_file_get_contents('http://pds.putike.cn/'.strtolower($_tmp[0]['supply']).'.php?method=refresh&id='.$_tmp[0]['hotel'].'&auto='.md5('auto'.date('Y-m-d')), null, null, 30);
            $callback = strpos($rs, 'success') !== false;
            /*
            $_supply = strtolower($_tmp[0]['supply']);
            $_code = $db -> prepare("SELECT `{$_tmp[0]['supply']}` AS `code`, FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$_tmp[0]['hotel']));
            $callback = call_user_func(array($_supply, 'refresh'), $_code[0]['code'], null, $checkin, $checkout); // localhost
            //*/
            if (!$callback)
            {
                order::set_error(901);
                return false;
            }
        }

        // get new prices
        $sql = "SELECT  p.*, h.`name` AS `hotel_name`, h.`country`, h.`city`, r.`name` AS `room_name`, n.`name` AS `nation_name`,
                        {$this -> _profit} AS `profit`
                FROM `ptc_hotel_price_date` AS p
                    LEFT JOIN `ptc_hotel` AS h ON p.`hotel` = h.`id`
                    LEFT JOIN `ptc_hotel_room_type` AS r ON p.`roomtype` = r.`id`
                    LEFT JOIN `ptc_nation` AS n ON n.`id` = p.`nation`
                    LEFT JOIN `ptc_org_profit` AS fp1 ON fp1.`org` = :org AND fp1.`payment` = 'prepay' AND fp1.`objtype` = 'hotel' AND fp1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fp2 ON fp2.`org` = :org AND fp2.`payment` = 'prepay' AND fp2.`objtype` = 'hotel' AND fp2.`objid` = p.`hotel`
                    LEFT JOIN `ptc_org_profit` AS fp3 ON fp3.`org` = :org AND fp3.`payment` = 'prepay' AND fp3.`objtype` = 'room'  AND fp3.`objid` = p.`roomtype`
                WHERE p.`uncombine` = :uncombine AND p.`date` >= :checkin AND p.`date` < :checkout AND p.`close` = 0
                ORDER BY p.`date` ASC;";

        $pro_date = $db -> prepare($sql) -> execute(array(':uncombine'=>$uncombine, ':checkin'=>$checkin, ':checkout'=>$checkout, ':org'=>api::$org));

        if (!$pro_date || !$pro_date[0]['hotel_name'] || !$pro_date[0]['room_name'])
        {
            api::set_error(614);
            return false;
        }

        $pro = $pro_date[0];

        if (($pro['start'] || $pro['end']) && (NOW < $pro['start'] || NOW + order::$expire >= $pro['end']))
        {
            api::set_error(615);
            return false;
        }

        if ($pro['advance'] && $checkin - NOW <= $pro['advance'] * 86400)
        {
            api::set_error(616);
            return false;
        }

        if ($pro['min'] && $pro['min'] > $night)
        {
            api::set_error(617);
            return false;
        }

        // room's name
        $package = count($args) > 1 ? '' : $args[0];
        if ($package)
        {
            $package = $db -> prepare("SELECT `name` FROM `ptc_hotel_package` WHERE `id`=:id;") -> execute(array(':id'=>$package));
            $package = $package ? $package[0]['name'] : '';
        }
        $roomname = roomname($pro['room_name'], ($sign ? 2 : $pro['bed']), $pro['nation_name'], $package);

        $prices = array();
        $return = array('hotel'=>$pro['hotel_name'], 'room'=>$roomname, 'num'=>$num, 'total'=>0, 'night'=>$night, 'dates'=>array());
        $total  = 0;
        $floor  = 0;
        $status = 2;    // waiting for pay
        for ($i = 0; $i < $night; $i++)
        {
            $_date = $i * 86400 + $checkin;
            $arg = count($args) == 1 ? $args[0] : $args[$i];
            $package_name = '';

            foreach ($pro_date as $k => $date)
            {
                if (count($args) > 1 && $data['package'] != 0)
                {
                    $package = $db -> prepare("SELECT `name` FROM `ptc_hotel_package` WHERE `id`=:id;") -> execute(array(':id'=>$package));
                    $package_name = $package ? $package[0]['name'] : '';
                }

                if ($date['date'] == $_date && $date['combine'] == $arg && $date['filled'] == 0)
                {
                    $prices[] = array('k'=>$date['key'], 'd'=>$_date, 'p'=>$date['price'], 'pf'=>$date['profit'], 'bf'=>$date['breakfast'], 'pk'=>$date['package'], 'pkn'=>$package_name);
                    $floor += $date['price'];
                    $total += $date['price'] + $date['profit'];
                    // filled
                    if ($date['filled'] == 1)
                    {
                        api::set_error(623);
                        return false;
                    }
                    // allot
                    if ($date['allot'] <= 0)
                    {
                        $status = 1;
                    }

                    $return['dates'][] = array('date'=>date('Y-m-d', $_date), 'price'=>$date['price'] + $date['profit'], 'breakfast'=>$date['breakfast'], 'package'=>$date['package']);
                    break;
                }
                else if ($date['date'] > $_date)
                {
                    break;
                }
                unset($pro_date[$k]);
            }
        }

        if (count($prices) < $night)
        {
            api::set_error(623);
            return false;
        }

        $_order = array(
            'productname'   => '',
            'itemname'      => '',
            'country'       => $pro['country'],
            'city'          => $pro['city'],
            'itemname'      => '',
            'hotelname'     => $pro['hotel_name'],
            'roomname'      => $roomname,
            'hotel'         => $pro['hotel'],
            'room'          => $pro['room'],
            'roomtype'      => $pro['roomtype'],
            'bed'           => $sign ? 2 : $pro['bed'],
            'nation'        => $pro['nation'],
            'package'       => $pro['package'],
            'checkin'       => $checkin,
            'checkout'      => $checkout,
            'start'         => $pro['start'],
            'end'           => $pro['end'],
            'min'           => $pro['min'],
            'advance'       => $pro['advance'],
            'supply'        => $pro['supply'],
            'supplyid'      => $pro['supplyid'],
            'rooms'         => (int)$num,
            'nights'        => $night,
            'floor'         => $num * $floor,
            'total'         => $num * $total,
            'status'        => $status,
            'remark'        => (string)$remark,
        );

        $return['total'] = $_order['total'];
        $return['status'] = $status;
        $_order['return'] = $return;
        $_order['data']   = array(
            'type'      => 'hotel_prepay',
            'price'     => $prices,
            'service'   => array(),
        );

        if ($pro['addbf']) $_order['data']['service']['ADDBF'] = $pro['addbf'] > 0 ? (int)$pro['addbf'] : 0;
        if ($pro['addbe']) $_order['data']['service']['ADDBE'] = $pro['addbe'] > 0 ? (int)$pro['addbe'] : 0;

        $_order['data'] = json_encode($_order['data']);

        return array_merge($order, $_order);
    }
    // order





    /**
     * check for paying order
     +-----------------------------------------
     * @access public
     * @param int $status
     * @param array $data
     * @param string $type
     * @param int $expire
     * @return void
     */
    public function pay($status, $data, $type='hotel', $expire=0)
    {
        if ($data['supply'] == 'TICKET' || $data['supply'] == 'PRODUCT' || $data['producttype'] > 0 || $type != 'hotel') return $status;

        $db = db(config('db'));

        // get new prices
        $status = 4;

        $_data = json_decode($data['data'], true);
        foreach ($_data['price'] as $k => $v)
        {
            $_price = $db -> prepare("SELECT `price`,`filled`,`allot`,`sold` FROM `ptc_hotel_price_date` WHERE `key`=:key;") -> execute(array(':key'=>$v['k']));
            if ($_price[0]['price'] != $v['p'] || $_price[0]['filled'] || !$_price[0]['allot'])
                $status = 4;

            if ($_price[0]['allot'] && $_price[0]['allot'] - $_price[0]['sold'] > $data['rooms'])
                $status = 4;

            if ($_price)
            {
                $db -> prepare("UPDATE `ptc_hotel_price_date` SET `sold`=`sold`+:num WHERE `key`=:key") -> execute(array(':key'=>$v['k'], ':num'=>(int)$data['rooms']));
            }
        }

        return $status;
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
        if ($data['supply'] == 'TICKET' || $data['supply'] == 'PRODUCT' || $data['producttype'] > 0 || $type != 'hotel') return $status;

        $db = db(config('db'));

        //if (!in_array($status, array(3,4,13)))
        return -610;

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=`total`, `status`=10 WHERE `id`=:id") -> execute(array(':id'=>$data['id']));
        if (false === $rs) return -502;

        return 10;
    }
    // apply_refund




    /* ================= MANAGE TEMPLATE ================= */


    protected $_order = array();


    /**
     * order extend infomation
     +-----------------------------------------
     * @access public
     * @param mixed $item
     * @return void
     */
    public function tpl_extend($item, $order, $type='hotel', $mode)
    {
        if ($item['product'] || $item['type'] != 'hotel' || $item['payment'] != 'prepay') return;

        $this -> _order = $order;

        $_status = order::status();

        switch ($item['status'])
        {
            case '3':
                $status = '<strong>'.$_status[$item['status']].'</strong>';
                break;
            case '4':
                $status = '<strong style="color:red">'.$_status[$item['status']].'</strong>';
                break;
            default:
                $status = $_status[$item['status']];
        }

        echo "<div class=\"info\"><b>状态：</b>{$status}</div>";

        echo "<table class=\"list\">";
        echo "<tr><th>序号</th><th>入住/离店时间</th><th>入住人</th><th class=\"text-right\">床型及要求</th></tr>";

        $checkin = date('Y-m-d', $item['checkin']);
        $checkout = date('m-d', $item['checkout']);
        $night = ($item['checkout'] - $item['checkin'])/86400;

        $i = 1;
        foreach ($item['rooms'] as $v)
        {
            $bed = bedname($v['bed']);
            $_require = json_decode($v['require'], true);
            $require = '';
            foreach ($_require as $k => $v)
            {
                $require .= requirename($k). ($v ? " x {$v}" : '');
            }

            $v['people'] = trim($v['people'], ', ');
            echo "<tr id=\"prepay-{$v['id']}\"><td>{$i}</td><td>{$checkin} / {$checkout} &nbsp; 共{$night}晚</td><td>{$v['people']}</td><td class=\"text-right\">{$bed}{$require}</td></tr>";
            $i++;
        }
        echo "</table>";


        echo '<div class="til">供应商信息</div>';

        $supplies = supplies();
        $supply = $supplies[$item['supply']];
        echo "<div class=\"info\"><b>供应：</b><a target=\"_blank\" href=\"".BASE_URL."order.php?method=redirect&sup={$item['supply']}&hotel={$item['hotel']}&checkin={$item['checkin']}&checkout={$item['checkout']}\">{$supply}</a></div>";

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
        if ($item['type'] != 'hotel' || $item['payment'] != 'prepay') return;

        if ($order['status'] < 3) return;

        $_temp = json_decode($item['data'], true);

        $prices = array();
        foreach ($_temp['price'] as $v)
        {
            $prices[$v['d']] = $v;
        }

        $start = date('N', $item['checkin']) == 7 ? $item['checkin'] : ($item['checkin'] - date('N', $item['checkin']) * 86400);
        $end   = date('N', $item['checkout']) == 7 ? ($item['checkout'] + 6 * 86400) : ($item['checkout'] + (6 - date('N', $item['checkout'])) * 86400);

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
        if ($item['type'] != 'hotel' || $item['payment'] != 'prepay') return;

        $status = order::status();

        include dirname(__FILE__).'/order/order_footer.tpl.php';
    }
    // footer






    /* ================= OPERATE ================= */

    public function operate($order)
    {
        switch ($_POST['method'])
        {
            case 'prepay-book-submit':
                $this -> _submit($order);
                break;

            case 'prepay-refund':
                $this -> _refund_apply($order);
                break;

            case 'order-refund':
                if (empty($_POST['type']) || $_POST['type'] != 'hotel-prepay') return;

                $this -> _refund_submit($order);
                break;

            default:
        }
    }




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
        $item = $db -> prepare("SELECT * FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '订单明细不存在');

        if (!in_array($item[0]['status'], array(3,4,12,13))) json_return(null, 2, '订单状态已变更，请刷新');

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `status`=8, `supplyorder`=:supplyorder, `confirmno`=:confirmno WHERE `id`=:id")
                  -> execute(array(':supplyorder'=>trim($_POST['supplyorder']), ':confirmno'=>trim($_POST['confirmno']), ':id'=>$id));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
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
    // _submit




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
        $price = (int)$_POST['price'];

        if (!$price) json_return(null, 1, '退款金额不能为零');

        $db = db(config('db'));
        $item = $db -> prepare("SELECT * FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '订单明细不存在');

        if (!in_array($item[0]['status'], array(3,4,13,12))) json_return(null, 2, '订单状态已变更，请刷新');

        if ($item[0]['total'] < $price) json_return(null, 2, '退款金额不能超过本项小计金额');

        $db -> beginTrans();

        $_data = json_decode($item[0]['data'], true);
        $_data['refund'] = $price;

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=:refund, `status`=10, `data`=:data WHERE `id`=:id")
                  -> execute(array(':refund'=>$price, ':data'=>json_encode($_data), ':id'=>$id));

        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order` SET `refundtime`=:time WHERE `id`=:orderid") -> execute(array(':orderid'=>$order['id'], ':time'=>NOW));

        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 6, '操作失败，请重试');
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        api::push('order', $order['order']);

        // order's log
        if (!order::_log($order, "协助申请退款¥{$price}，<b>退款原因：</b>".$_POST['reason'], array('refund'=>$price, 'num'=>$num)))
        {
            $db -> rollback();
            json_return(null, 8, '保存失败，请重试');
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
    // refund_apply






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

        $status = (int)$_POST['status'] ? 11 : 12;

        $db = db(config('db'));
        $item = $db -> prepare("SELECT `id`,`total`,`refund`,`status` FROM `ptc_order_hotel` WHERE `id`=:id AND `orderid`=:orderid")
                    -> execute(array(':id'=>$id, ':orderid'=>$order['id']));

        if (!$item) json_return(null, 2, '退款产品不存在');

        if ($item[0]['status'] != 10) json_return(null, 2, '订单未申请退款');

        // refund price
        $refund = (int)$item[0]['refund'];
        if ($refund > $item[0]['total']) json_return(null, 2, '退款金额不能超过产品总额');

        if ($refund < $item[0]['total']) $status = 16;

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `refund`=0, `status`=:status WHERE `orderid`=:orderid AND `id`=:id;")
                  -> execute(array(':id'=>$id, ':orderid'=>$order['id'], ':status'=>$status));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 4, '操作失败，请重试');
        }

        if ($status == 11)
        {
            $rs = $db -> prepare("UPDATE `ptc_order` SET `refund`=`refund`+:refund WHERE `id`=:orderid;") -> execute(array(':refund'=>$refund, ':orderid'=>$order['id']));
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
        $message = $status != 12 ? ( $status == 11 ? "退款退订完成¥{$refund}" : "部分退款完成¥{$refund}") : "拒绝退款";
        if (!order::_log($order, $message, array('refund'=>$item[0]['refund'])))
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
        if (!$order || $type != 'hotel_prepay') return $result;

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

        // order's log
        if (!order::_log($order, "已入住完成", array('id'=>$item['id']), '系统自动'))
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
        return $confirmation;
    }
    // confirmation


}

