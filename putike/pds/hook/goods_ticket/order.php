<?php
// goods ticket product hook of order operation
class goods_ticket_order extends goods_ticket_hook
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
    public function booking($order, $code='', $num=1, $contact='', $tel='', $address='', $remark='')
    {
        $args = explode('_', $code);
        if ($args[count($args) - 1] !== 'ticket') return $order;

        $id = intval($args[0]);

        // Load Product's introduce, price, profit, sold number
        $sql = "SELECT  i.`id` AS `item`, i.`name` AS `item_name`, i.`price`, i.`allot`, i.`sold`,
                        b.`id` AS `ticket`, b.`name` AS `ticket_name`, b.`start`, b.`end`, b.`updatetime`,
                        h.`id` AS `goods`, h.`name` AS `goods_name`,
                        i.`min`, i.`max`,
                        {$this -> _profit} AS `profit`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_product` AS b ON i.`pid` = b.`id`
                    LEFT JOIN `ptc_goods` AS h ON i.`objpid` = h.`id`
                    LEFT JOIN `ptc_org_profit` AS fi1 ON fi1.`org` = :org AND fi1.`payment` = 'ticket' AND fi1.`objtype` = 'goods' AND fi1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fi2 ON fi2.`org` = :org AND fi2.`payment` = 'ticket' AND fi2.`objtype` = 'goods' AND fi2.`objid` = i.`pid`
                    LEFT JOIN `ptc_org_profit` AS fi3 ON fi2.`org` = :org AND fi3.`payment` = 'ticket' AND fi3.`objtype` = 'item' AND fi3.`objid` = i.`id`
                WHERE i.`id`=:id AND b.`status` = 1";

        $db = db(config('db'));
        $pro = $db -> prepare($sql) -> execute(array(':id'=>$id, ':org'=>api::$org));

        if (!$pro || !$pro[0]['ticket'] || !$pro[0]['goods'])
        {
            api::set_error(614);
            return false;
        }
        $pro = $pro[0];

        $min = max(1, $pro['min']);

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
        $sql = "SELECT SUM(a.`num`) AS `sum` FROM `ptc_order_goods` AS a
                    LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                WHERE a.`supply`='TICKET' AND a.`supplyid`=:product AND a.`goods`=:goods AND a.`status`=2 AND b.`create`>=:time";
        $locked = $db -> prepare($sql) -> execute(array(':product'=>$pro['ticket'], ':goods'=>$pro['goods'], ':time'=>NOW - $this -> _timeout));

        if ($pro['allot'] - $pro['sold'] - (int)$locked[0]['sum'] - $num < 0)
        {
            api::set_error(619);
            return false;
        }

        $_order = array(
            'productname'   => $pro['ticket_name'],
            'item'          => $pro['item'],
            'itemname'      => $pro['item_name'],
            'goodsname'     => $pro['goods_name'],
            'goods'         => $pro['goods'],
            'supply'        => 'TICKET',
            'supplyid'      => $pro['ticket'],
            'num'           => $num,
            'currency'      => 1,
            'floor'         => $num * $pro['price'],
            'total'         => $num * ($pro['price'] + round($pro['profit'])),
            'contact'       => trim($contact),
            'tel'           => trim($tel),
            'address'       => trim($address),
            'status'        => 2,                                                   // wait for pay
            'remark'        => (string)$remark,
        );

        $_order['return'] = array('goods'=>$_order['goodsname'], 'ticket'=>$_order['productname'], 'item'=>$_order['itemname'], 'num'=>$num, 'total'=>$_order['total'], 'status'=>2);
        $_order['data']   = json_encode(array(
            'type'      => 'goods_ticket',
            'price'     => $pro['price'],
            'profit'    => $pro['profit'],
            'ticket'    => $pro['ticket'],
            'item'      => $pro['item'],
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
    public function pay($status, $data, $type='goods', $expire=0)
    {
        if ($data['supply'] != 'TICKET' || $data['producttype'] > 0 || $type != 'goods') return $status;

        static $_product = array();

        // allot is locking
        if (NOW < $expire)
        {
            if ($data['status'] == 2)
            {
                $db = db(config('db'));
                $rs = $db -> prepare("UPDATE `ptc_product_item` SET `sold`=`sold`+:num WHERE `id`=:id") -> execute(array(':id'=>(int)$data['item'], ':num'=>(int)$data['rooms']));
                if ($rs === false) return -901;

                if (!isset($_product[$data['supplyid']]))
                {
                    sms::send($data['order'], 'goods_ticket_pay', $data);
                    $_product[$data['supplyid']] = 1;
                }
            }
            sms::send($data['order'], 'goods_ticket_pay_item', $data);

            return $data['status'] == 2 ? 3 : $data['status'];
        }
        else
        {
            $db = db(config('db'));

            // product
            $pro = $db -> prepare("SELECT `allot`,`sold` FROM `ptc_product_item` WHERE `id`=:id AND `pid`=:pid;") -> execute(array(':id'=>$data['item'], ':pid'=>$data['supplyid']));
            if (!$pro) return 0;
            $pro = $pro[0];

            // load locked allot
            $sql = "SELECT SUM(a.`num`) AS `sum`
                    FROM `ptc_order_goods` AS a
                    LEFT JOIN `ptc_order` AS b ON a.`orderid` = b.`id`
                    WHERE a.`supply`='TICKET' AND a.`supplyid`=:product AND a.`goods`=:goods AND a.`item`=:item AND a.`status`=2 AND b.`expire`>:time";
            $locked = $db -> prepare($sql) -> execute(array(':product'=>$data['supplyid'], ':goods'=>$data['goods'], ':item'=>$data['item'], ':time'=>NOW));

            // send sms
            if (!isset($_product[$data['supplyid']]))
            {
                sms::send($data['order'], 'goods_ticket_pay', $data);
                $_product[$data['supplyid']] = 1;
            }
            sms::send($data['order'], 'goods_ticket_pay_item', $data);

            if ($pro['allot'] - $pro['sold'] - $locked[0]['sum'] < $data['num'])
            {
                return 13;
            }
            else
            {
                $rs = $db -> prepare("UPDATE `ptc_product_item` SET `sold`=`sold`+:num WHERE `id`=:id") -> execute(array(':id'=>(int)$data['item'], ':num'=>(int)$data['num']));
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
    public function apply_refund($status, $data, $type='goods')
    {
        if ($data['supply'] != 'TICKET' || $data['producttype'] > 0 || $type != 'goods') return $status;

        $db = db(config('db'));

        if (!in_array($status, array(3,4,13))) return -610;

        $rs = $db -> prepare("UPDATE `ptc_order_goods` SET `refund`=`total` WHERE `id`=:id AND `status` IN (3,4,13)") -> execute(array(':id'=>$data['id']));
        if (false === $rs) return -502;

        return 10;
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
    public function tpl_extend($item, $order, $type='goods', $mode)
    {
        if ($item['product'] || $item['type'] != 'goods' || $item['payment'] != 'ticket') return;

        // product's information
        $_id = (int)$item['item'];
        $product = null;

        $db = db(config('db'));

        if ($_id)
        {
            $sql = "SELECT i.*, i.`intro` AS `item_intro`, p.* FROM `ptc_product_item` AS i LEFT JOIN `ptc_product` AS p ON i.`pid` = p.`id` WHERE i.`id`=:id";
            $product = $db -> prepare($sql) -> execute(array(':id'=>$_id));
            if ($product) $product = $product[0];

            echo '<div class="info"><b>数量：</b>' , "{$item['num']}" ,'</div>',
                '<div class="info"><b>快递地址：</b>' , "{$item['contact']} ({$item['tel']}) {$item['address']}" ,'</div>',
                '<hr style="margin:10px 0px 0px;">',
                '<div class="info"><b>商品说明：</b></div>',
                '<div class="info" style="line-height:20px; padding:0px 20px;">',
                    nl2br($product['intro']),
                '</div>';
        }

        $this -> _order = array('product'=>$product);

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
        if ($item['type'] != 'goods' || $item['payment'] != 'ticket') return;

        if ($order['status'] < 3) return;

        extract($this -> _order);

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
        if ($item['type'] != 'goods' || $item['payment'] != 'ticket') return;

        extract($this -> _order);

        $status = order::status();

        include dirname(__FILE__).'/order/order_footer.tpl.php';
    }
    // tpl_footer







    /* ================= OPERATE ================= */

    public function operate($order)
    {
        switch ($_POST['method'])
        {
            case 'goods-used':
                $this -> _used($order);
                break;

            case 'goods-refund':
                $this -> _refund_apply($order);
                break;

            case 'order-refund':
                if (empty($_POST['type']) || $_POST['type'] != 'goods-ticket') return;

                $this -> _refund_submit($order);
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

        $id = (int)$_POST['id'];

        $db = db(config('db'));
        $item = $db -> prepare("SELECT * FROM `ptc_order_goods` WHERE `id`=:id AND `orderid`=:orderid AND `supply`='TICKET'") -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '欲使用的券类不存在');

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_order_goods` SET `status`=IF(`status`=3,8,16), `express`=:express WHERE `id`=:orderid AND `orderid`=:orderid AND `status` IN (3,16);")
                  -> execute(array(':id'=>$id, ':orderid'=>$order['id'], ':express'=>$_POST['type'].':'.$_POST['number']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 3, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_order` SET `appointmentime`=:time WHERE `id`=:orderid") -> execute(array(':orderid'=>$order['id'], ':time'=>NOW));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 5, '操作失败，请重试');
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
        if (!order::_log($order, "确认已发货".($_POST['number'] ? '，单号：'.$_POST['number'] : ''), $return))
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

        $sql = "SELECT g.`total`, g.`refund` AS `apply_refund`,
                       o.`refund` AS `refund`
                FROM `ptc_order_goods` AS g
                    LEFT JOIN `ptc_order` AS o ON g.`orderid` = o.`id`
                WHERE g.`id`=:id AND g.`orderid`=:orderid AND g.`supply`='TICKET'";
        $item = $db -> prepare($sql) -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '退款项目不存在');

        $db -> beginTrans();

        $refund = $item[0]['refund'] + $item[0]['apply_refund'] + (int)$_POST['price'];
        if ($refund > $item[0]['total'])
            json_return(null, 2, '退款总额不得超过订单总额');

        $rs = $db -> prepare("UPDATE `ptc_order_goods` SET `refund`+=:refund, `status`=10 WHERE `id`=:id AND `orderid`=:orderid AND `status` IN (3,4,16);")
                  -> execute(array(':id'=>$id, ':orderid'=>$order['id'], ':refund'=>$refund));
        if (false === $rs)
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

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        if (!order::_log($order, "协助申请退款，<b>退款原因：</b>".$_POST['reason']))
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

        $status = (int)$_POST['status'] ? 11 : 3;

        $db = db(config('db'));

        $sql = "SELECT g.`id`, g.`total`, g.`refund` AS `apply_refund`, g.`status`,
                       o.`refund` AS `refund`
                FROM `ptc_order_goods` AS g
                    LEFT JOIN `ptc_order` AS o ON g.`orderid` = o.`id`
                WHERE g.`id`=:id AND g.`orderid`=:orderid AND g.`supply`='TICKET'";
        $item = $db -> prepare($sql) -> execute(array(':id'=>$id, ':orderid'=>$order['id']));
        if (!$item) json_return(null, 2, '退款项目不存在');

        $db -> beginTrans();

        $refund = $item[0]['refund'] + $item[0]['apply_refund'];
        if ($refund > $item[0]['total'])
            json_return(null, 2, '退款总额不得超过订单总额');

        if ($refund != $item[0]['total'] && $status == 11) $status = 16;

        $rs = $db -> prepare("UPDATE `ptc_order_goods` SET `refund`=0, `status`=:status WHERE `orderid`=:orderid AND `id`=:id;") -> execute(array(':id'=>$id, ':status'=>$status, ':orderid'=>$order['id']));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 4, '操作失败，请重试');
        }

        if ($status > 10)
        {
            $rs = $db -> prepare("UPDATE `ptc_order` SET `refund`=`refund`+:refund WHERE `id`=:orderid;") -> execute(array(':refund'=>$item[0]['apply_refund'], ':orderid'=>$order['id']));
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 5, '操作失败，请重试');
            }
        }

        if (false === order::_status($order))
        {
            $db -> rollback();
            json_return(null, 7, '操作失败，请重试');
        }

        // order's log
        $num = count($tickets);
        $message = $status ? "退款退订完成" : "拒绝退款";
        if (!order::_log($order, $message))
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
        if (!$order || $type != 'goods_ticket') return $result;

        $today = strtotime(date('Y-m-d 00:00:00'));

        $db = db(config('db'));

    }
    // complete




    // order confirmation
    static function confirmation($confirmation, $order)
    {
        $db = db(config('db'));

        $sql = "SELECT `id`, `product` FROM `ptc_order_goods` WHERE `orderid`=:oid AND `supply`='TICKET' AND `producttype`=0;";
        $goods = $db -> prepare($sql) -> execute(array(':oid'=>$order['id']));
        if (!$goods) return $confirmation;

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

