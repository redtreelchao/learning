<?php
/**
 * 订单
 +-----------------------------------------
 * @category
 * @package hotel
 * @author nolan.zhou
 * @version $Id$
 */
class order extends api
{
    // error message
    static public $error_msg = array(
            601     => '未选择结算货币',
            602     => '未选择结算方式',
            603     => '必须填写联系人信息',
            604     => '未传递订单号，请先创建订单',
            605     => '产品代码不正确',
            606     => '入住人信息不正确',
            607     => '未知的服务需求码',
            608     => '订单已支付',
            609     => '订单未支付',
            610     => '订单已执行退款或状态不可退款',
            611     => '该订单尚有未使用的券',
            612     => '该订单已超过申请发票时限',
            614     => '产品不存在',
            615     => '产品不在预订期内',
            616     => '产品预订需提前指定天数',
            617     => '产品预订需连住指定天数',
            618     => '部分日期不可预订',
            619     => '产品已售罄',
            621     => '预付产品需要入住/离店日期',
            622     => '入住/离店日期不正确',
            623     => '房态/航班已变化，部分日期不订',
            624     => '航班日期不正确',
            625     => '订单等待确认中',
            626     => '库存低于预订数量，请咨询客服',
            627     => '产品未开始销售',
            628     => '商品已下架',
            629     => '订单状态已变化，请更新订单',
            630     => '所填信息不完善',
            631     => '订单已确认，请使用修改订单接口',
            632     => '券信息不存在或不正确',
            633     => '券已被使用，无法预订',
            634     => '预订时间不正确',
            635     => '住客成人数量不正确',
            636     => '入住人姓名不正确',
            637     => '房型选择不正确',
            638     => '联系电话不正确',
            639     => '联系邮箱不正确',
            640     => '该日期不可预订',
            641     => '该日期库存不足，无法预订',
            901     => '确认订单支付信息失败，请联系客服人员，核实订单',
        );


    // inset order data
    static protected $inset = false;


    // order expire time: default 30m
    static public $expire = 1800;



    /**
     * booking by one request
     +-----------------------------------------
     * @access public
     * @param int       $currency
     * @param string    $paytype
     * @param string    $contact
     * @param string    $tel
     * @param string    $email
     * @param string    $ip
     * @param array     $rooms       hotel product
     * @param array     $flights     flight product
     * @param array     $payment
     * @return void
     */
    static function booking($currency, $paytype, $contact, $tel, $email='', $ip='', $rooms=array(), $flights=array(), $payment=array())
    {
        $db = db(config('db'));
        $db -> beginTrans();

        self::$inset = true;
        //$rs = self::create($currency, $paytype, $contact, $tel, $email, $ip);

        $rooms = empty($rooms['room'][0]) ? $rooms : $rooms['room'];
        foreach ($rooms as $room)
        {
            // parse people data
            $peoples = array();
            $_peoples = empty($room['peoples']['people'][0]) ? $room['peoples'] : $room['peoples']['people'];
            foreach ($_peoples as $v)
            {
                $peoples[] = array(
                    'name'  => (string)$v['name'],
                    'bed'   => (string)$v['bed'],
                    'require'   => (string)$v['require'],
                );
            }

            $rs = self::room('', $room['code'], $room['num'], $room['product'], $peoples, $room['checkin'], $room['checkout'], $room['remark']);
        }

        exit;

    }
    // booking




    /**
     * Create new empty order
     +-----------------------------------------
     * @access public
     * @param int    $currency
     * @param string $paytype
     * @param string $contact
     * @param string $tel
     * @param string $email
     * @param string $ip
     * @return void
     */
    static function create($currency, $paytype, $contact, $tel, $email='', $ip='')
    {
        if (!$currency)
        {
            self::$error = 601;
            return false;
        }

        if (!$paytype)
        {
            self::$error = 602;
            return false;
        }

        if (!$contact || !$tel)
        {
            self::$error = 603;
            return false;
        }

        $order = array(
            'order'     => date('ymd').str_pad(self::$org, 3, 0, STR_PAD_LEFT).rand(10, 99).substr(str_pad(self::$account, 3, 0, STR_PAD_LEFT), -3, 3),
            'currency'  => $currency,
            'paytype'   => $paytype,
            'contact'   => trim($contact),
            'tel'       => trim($tel),
            'status'    => 0,
            'from'      => self::$org,
            'update'    => NOW,
            'create'    => NOW,
            'date'      => strtotime('today'),
            'email'     => (string)$email,
            'status'    => -1,
            'ip'        => (string)$ip,
            'expire'    => NOW + self::$expire,
            'hide'      => 1,
        );

        $db = db(config('db'));
        $db -> beginTrans();

        list($column, $sql, $value) = array_values(insert_array($order));
        $rs = $db -> prepare("INSERT INTO `ptc_order` {$column} VALUES {$sql};") -> execute($value);
        if (!$rs)
        {
            self::$error = 501;
            $db -> rollback();
            return false;
        }

        $order = $db -> prepare("SELECT * FROM `ptc_order` WHERE `id`=:id") -> execute(array(':id'=>$rs));
        if (!$order)
        {
            self::$error = 502;
            $db -> rollback();
            return false;
        }

        if (self::$inset) self::$inset = $order[0];

        // order ext data
        $rs = $db -> prepare("INSERT INTO `ptc_order_ext` (`orderid`, `order`) VALUES (:id, :order);") -> execute(array(':id'=>$rs, ':order'=>$order[0]['order']));
        if (false === $rs)
        {
            self::$error = 503;
            $db -> rollback();
            return false;
        }

        if ($db -> commit())
        {
            return $order[0]['order'];
        }
        else
        {
            self::$error = 504;
            $db -> rollback();
            return false;
        }
    }
    // create





    /**
     * Add hotel product for order
     +-----------------------------------------
     * @access public
     * @param string $order
     * @param string $code
     * @param int $num
     * @param array $peoples
     * @param string $product
     * @param int $checkin
     * @param int $checkout
     * @param array $keys
     * @param string $remark
     * @return void
     */
    static function room($order, $code, $num=1, $product='', $peoples=array(), $checkin=0, $checkout=0, $remark='')
    {
        if (!$order && !self::$inset)
        {
            self::$error = 604;
            return false;
        }

        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        $code = key_encryption($code, true);
        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        // not call from inset of class, verify order data
        if (!self::$inset)
        {
            $_order = self::_order($order);
            if (!$_order)
            {
                self::$error = 605;
                return false;
            }

            if ($_order['status'] > 2)
            {
                self::$error = 631;
                return false;
            }
        }

        // check check date
        if ($checkin && $checkout)
        {
            $checkin = strtotime($checkin);
            $checkout = strtotime($checkout);
            if (!$checkin || !$checkout || $checkin >= $checkout)
            {
                self::$error = 622;
                return false;
            }
        }
        else
        {
            $checkin = $checkout = 0;
        }

        $order_hotel = array(
            'orderid'       => self::$inset ? self::$inset['id'] : $_order['id'],
            'order'         => self::$inset ? self::$inset['order'] : $_order['order'],
            'return'        => array(),
            'data'          => array(),
        );

        $order_room = array();
        $requires_price = 0;
        $request = 0;
        for ($i = 0; $i < $num; $i++)
        {
            if (empty($peoples) || empty($peoples[$i])) $peoples[$i] = '||';
            $_data = is_string($peoples[$i]) ? explode('|', !empty($peoples[$i]) ? $peoples[$i] : '||') : array_values($peoples[$i]);

            $people     = trim($_data[0]);
            $bed        = empty($_data[1]) ? '' : trim($_data[1]);
            //$bed        = filter::apply('order_bed', $bed, $pricedata['type']);

            // parse require code
            $require_code   = self::require_code();
            $requires       = array();
            $_requires      = array_filter(explode(',', empty($_data[2]) ? '' : trim($_data[2])));
            $addbe = $addbf = $addbeprice = $addbfprice = 0;
            foreach ($_requires as $v)
            {
                if (strpos($v, ':'))
                {
                    list($code, $rnum) = explode(':', $v);
                }
                else
                {
                    $code = $v;
                    $rnum = 0;
                }

                if (empty($require_code[$code]))
                {
                    self::$error = 607;
                    return false;
                }

                if (!empty($pricedata['service'][$code]))
                {
                    $requires[$code] = (int)$rnum;
                    $requires_price += $rnum * $pricedata['service'][$code];

                    if ($code == 'ADDBE')
                    {
                        $addbe = (int)$rnum;
                        $addbeprice = (int)$pricedata['service'][$code];
                    }

                    if ($code == 'ADDBF')
                    {
                        $addbf = (int)$rnum;
                        $addbfprice = (int)$pricedata['service'][$code];
                    }
                }
            }

            $order_room[] = array(
                'orderid'       => self::$inset ? self::$inset['id'] : $_order['id'],
                'order'         => self::$inset ? self::$inset['order'] : $_order['order'],
                'pid'           => 0,
                'people'        => $people,
                'bed'           => $bed,
                'require'       => json_encode($requires),
                'addbe'         => $addbe,
                'addbf'         => $addbf,
                'addbefloor'    => $addbeprice,
                'addbffloor'    => $addbfprice,
                'addbeprice'    => $addbeprice,
                'addbfprice'    => $addbfprice,
                'addbestatus'   => $addbe ? -1 : 0,
                'addbfstatus'   => $addbf ? -1 : 0,
                'supply'        => '',
            );

            if (!empty($_data[2])) $request = 1;
        }

        // load hotel order by hook
        include_once PT_PATH.'hook/hook.php';
        $order_hotel = filter::apply('order_room', $order_hotel, $code, $num, $checkin, $checkout, $remark);
        if (!$order_hotel) return false;

        // get return data
        $return    = $order_hotel['return'];
        unset($order_hotel['return']);


        $db = db(config('db'));

        if (!self::$inset)
            $db -> beginTrans();

        // refresh order price
        $ors = $db  -> prepare("UPDATE `ptc_order` SET `total`=`total`+:total, `floor`=`floor`+:floor, `request`=`request`+:request, `update`=:now, `expire`=:expire, `status`=2, `hide`=0 WHERE `order`=:order;")
                    -> execute(array(':total'=>$order_hotel['total'] + $requires_price, ':floor'=>$order_hotel['floor'] + $requires_price, ':order'=>$_order['order'], ':request'=>$request, ':now'=>NOW, ':expire'=>NOW + self::$expire));

        if (!$ors)
        {
            $db -> rollback();
            self::$error = 501;
            return false;
        }

        // exist
        $sql = "SELECT `id`,`order`,`product`,`hotel`,`room`,`roomtype`,`bed`,`nation`,`package`,`checkin`,`checkout`,
                        `start`,`end`,`min`,`advance`,`nights`,`currency`,`supply`,`supplyid`,`cancel`
                FROM `ptc_order_hotel` WHERE `orderid`=:orderid";
        $exists = $db -> prepare($sql) -> execute(array(':orderid' => $order_hotel['orderid']));

        $_exist = false;
        if ($exists)
        {
            foreach ($exists as $v)
            {
                $_temp = array_intersect_key($order_hotel, $v);
                $_check = array_intersect_assoc($_temp, $v);
                if (count($_check) == count($_temp))
                {
                    $_exist = true;
                    $pid = $v['id'];
                    break;
                }
            }
        }

        if (!$_exist)
        {
            // insert order hotel data
            list($column, $sql, $value) = array_values(insert_array($order_hotel));
            $pid = $db -> prepare("INSERT INTO `ptc_order_hotel` {$column} VALUES {$sql};") -> execute($value);

            if (!$pid)
            {
                $db -> rollback();
                self::$error = 502;
                return false;
            }
        }

        // bind hotel data pid
        foreach ($order_room as $k => $v)
            $order_room[$k]['pid'] = $pid;


        list($column, $sql, $value) = array_values(insert_array($order_room));
        $rs = $db -> prepare("INSERT INTO `ptc_order_room` {$column} VALUES {$sql};") -> execute($value);

        if (!$rs)
        {
            $db -> rollback();
            self::$error = 503;
            return false;
        }

        if (!self::$inset)
        {
            if ($db -> commit())
            {
                return $return;
            }
            else
            {
                self::$error = 504;
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    // addroom





    /**
     * Add flight product for order
     +-----------------------------------------
     * @access public
     * @param string $order
     * @param string $code
     * @param int $num
     * @param array $peoples
     * @param string $product
     * @param int $checkin
     * @param int $checkout
     * @param array $keys
     * @param string $remark
     * @return void
     */
    static function flight($order, $code, $num=1, $product='', $date='', $peoples=array(), $remark='')
    {
        if (!$order && !self::$inset)
        {
            self::$error = 604;
            return false;
        }

        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        $code = key_encryption($code, true);
        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        // not call from inset of class, verify order data
        if (!self::$inset)
        {
            $_order = self::_order($order);
            if (!$_order)
            {
                self::$error = 605;
                return false;
            }

            if ($_order['status'] > 2)
            {
                self::$error = 631;
                return false;
            }
        }

        // check check date
        if ($date)
        {
            $date = strtotime($date);
            if (!$date)
            {
                self::$error = 624;
                return false;
            }
        }
        else
        {
            $date = 0;
        }

        $order_flight = array(
            'orderid'       => self::$inset ? self::$inset['id'] : $_order['id'],
            'order'         => self::$inset ? self::$inset['order'] : $_order['order'],
            'passenger'     => array(),
            'return'        => array(),
            'data'          => array(),
        );

        $requires_price = 0;
        for ($i = 0; $i < $num; $i++)
        {
            $_data = is_string($peoples[$i]) ? explode('|', !empty($peoples[$i]) ? $peoples[$i] : '||', 3) : array_values($peoples[$i]);

            $people     = trim($_data[0]);
            $type       = trim($_data[1]);
            $credential = trim($_data[2]);

            // parse require code
            $order_flight['passenger'][] = array(
                'orderid'       => self::$inset ? self::$inset['id'] : $_order['id'],
                'order'         => self::$inset ? self::$inset['order'] : $_order['order'],
                'pid'           => 0,
                'people'        => $people,
                'type'          => $type,
                'credential'    => $credential,
            );
        }

        // load hotel order by hook
        include_once PT_PATH.'hook/hook.php';
        $order_flight = filter::apply('order_flight', $order_flight, $code, $num, $date, $remark);
        if (!$order_flight)
            return false;

        // get return data
        $return    = $order_flight['return'];
        $pricedata = $order_flight['data'];
        $order_passenger = $order_flight['passenger'];
        unset($order_flight['return'], $order_flight['passenger']);


        $db = db(config('db'));

        if (!self::$inset)
            $db -> beginTrans();

        // refresh order price
        $ors = $db  -> prepare("UPDATE `ptc_order` SET `total`=`total`+:total, `floor`=`floor`+:floor, `request`=`request`+:request, `update`=:now, `expire`=:expire, `status`=2, `hide`=0 WHERE `order`=:order;")
                    -> execute(array(':total'=>$order_flight['total'], ':floor'=>$order_flight['floor'], ':order'=>$_order['order'], ':request'=>0, ':now'=>NOW, ':expire'=>NOW+self::$expire));

        if (!$ors)
        {
            $db -> rollback();
            self::$error = 501;
            return false;
        }

        // exist
        $sql = "SELECT `id`,`order`,`product`,`flight`,`package`,`class`,`date`,`start`,`end`,
                        `advance`,`currency`,`supply`,`supplyid`,`meal`,`back`,`backday`,`cancel`
                FROM `ptc_order_flight` WHERE `orderid`=:orderid";
        $exists = $db -> prepare($sql) -> execute(array(':orderid' => $order_flight['orderid']));

        $_exist = false;
        if ($exists)
        {
            foreach ($exists as $v)
            {
                $_temp = array_intersect_key($order_flight, $v);
                $_check = array_intersect_assoc($_temp, $v);
                if (count($_check) == count($_temp))
                {
                    $_exist = true;
                    $pid = $v['id'];
                    break;
                }
            }
        }

        if (!$_exist)
        {
            // insert order hotel data
            list($column, $sql, $value) = array_values(insert_array($order_flight));
            $pid = $db -> prepare("INSERT INTO `ptc_order_flight` {$column} VALUES {$sql};") -> execute($value);

            if (!$pid)
            {
                $db -> rollback();
                self::$error = 502;
                return false;
            }
        }

        // bind hotel data pid
        foreach ($order_passenger as $k => $v)
            $order_passenger[$k]['pid'] = $pid;


        list($column, $sql, $value) = array_values(insert_array($order_passenger));
        $rs = $db -> prepare("INSERT INTO `ptc_order_passenger` {$column} VALUES {$sql};") -> execute($value);

        if (!$rs)
        {
            $db -> rollback();
            self::$error = 503;
            return false;
        }

        if (!self::$inset)
        {
            if ($db -> commit())
            {
                return $return;
            }
            else
            {
                self::$error = 504;
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    // addflight



    /**
     * Add auto product for order
     +-----------------------------------------
     * @access public
     * @param string $order
     * @param string $code
     * @param int $num
     * @param array $peoples
     * @param string $product
     * @param int $checkin
     * @param int $checkout
     * @param array $keys
     * @param string $remark
     * @return void
     */
    static function auto($order, $code, $num=1, $product='', $date='', $peoples=array(), $remark='')
    {
        if (!$order && !self::$inset)
        {
            self::$error = 604;
            return false;
        }

        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        $code = key_encryption($code, true);
        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        // not call from inset of class, verify order data
        if (!self::$inset)
        {
            $_order = self::_order($order);
            if (!$_order)
            {
                self::$error = 605;
                return false;
            }

            if ($_order['status'] > 2)
            {
                self::$error = 631;
                return false;
            }
        }

        // check check date
        if ($date)
        {
            $date = strtotime($date);
            if (!$date)
            {
                self::$error = 624;
                return false;
            }
        }
        else
        {
            $date = 0;
        }

        $order_auto = array(
            'orderid'       => self::$inset ? self::$inset['id'] : $_order['id'],
            'order'         => self::$inset ? self::$inset['order'] : $_order['order'],
            'driver'        => array(),
            'return'        => array(),
            'data'          => array(),
        );

        $requires_price = 0;
        for ($i = 0; $i < $num; $i++)
        {
            $_data = is_string($peoples[$i]) ? explode('|', !empty($peoples[$i]) ? $peoples[$i] : '|', 2) : array_values($peoples[$i]);

            $people     = trim($_data[0]);
            $credential = trim($_data[1]);

            // parse require code
            $order_auto['driver'][] = array(
                'people'        => $people,
                'credential'    => $credential,
            );
        }

        // load hotel order by hook
        include_once PT_PATH.'hook/hook.php';
        $order_auto = filter::apply('order_auto', $order_auto, $code, $num, $date, $remark);
        if (!$order_auto)
            return false;

        // get return data
        $return    = $order_auto['return'];
        $pricedata = $order_auto['data'];
        unset($order_auto['return']);


        $db = db(config('db'));

        if (!self::$inset)
            $db -> beginTrans();

        // refresh order price
        $ors = $db  -> prepare("UPDATE `ptc_order` SET `total`=`total`+:total, `floor`=`floor`+:floor, `request`=`request`+:request, `update`=:now, `expire`=:expire, `status`=2, `hide`=0 WHERE `order`=:order;")
                    -> execute(array(':total'=>$order_auto['total'], ':floor'=>$order_auto['floor'], ':order'=>$_order['order'], ':request'=>0, ':now'=>NOW, ':expire'=>NOW+self::$expire));

        if (!$ors)
        {
            $db -> rollback();
            self::$error = 501;
            return false;
        }

        // exist
        $sql = "SELECT `id`,`order`,`product`,`auto`,`package`,`date`,`start`,`end`, `advance`,`currency`,`supply`,`supplyid`,`cancel`
                FROM `ptc_order_auto` WHERE `orderid`=:orderid";
        $exists = $db -> prepare($sql) -> execute(array(':orderid' => $order_auto['orderid']));

        $_exist = false;
        if ($exists)
        {
            foreach ($exists as $v)
            {
                $_temp = array_intersect_key($order_auto, $v);
                $_check = array_intersect_assoc($_temp, $v);
                if (count($_check) == count($_temp))
                {
                    $_exist = true;
                    $pid = $v['id'];
                    break;
                }
            }
        }

        if (!$_exist)
        {
            // insert order hotel data
            list($column, $sql, $value) = array_values(insert_array($order_auto));
            $pid = $db -> prepare("INSERT INTO `ptc_order_auto` {$column} VALUES {$sql};") -> execute($value);

            if (!$pid)
            {
                $db -> rollback();
                self::$error = 502;
                return false;
            }
        }

        if (!self::$inset)
        {
            if ($db -> commit())
            {
                return $return;
            }
            else
            {
                self::$error = 504;
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    // addauto






    /**
     * Add goods product for order
     +-----------------------------------------
     * @access public
     * @param string $order
     * @param string $code
     * @param int $num
     * @param array $peoples
     * @param string $product
     * @param array $keys
     * @param string $remark
     * @return void
     */
    static function goods($order, $code, $num=1, $product='', $contact='', $tel='', $address='', $remark='')
    {
        if (!$order && !self::$inset)
        {
            self::$error = 604;
            return false;
        }

        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        $code = key_encryption($code, true);
        if (!$code)
        {
            self::$error = 605;
            return false;
        }

        if (!$contact || !$tel || !$address)
        {
            self::$error = 630;
            return false;
        }

        // not call from inset of class, verify order data
        if (!self::$inset)
        {
            $_order = self::_order($order);
            if (!$_order)
            {
                self::$error = 605;
                return false;
            }

            if ($_order['status'] > 2)
            {
                self::$error = 631;
                return false;
            }
        }

        $order_goods = array(
            'orderid'       => self::$inset ? self::$inset['id'] : $_order['id'],
            'order'         => self::$inset ? self::$inset['order'] : $_order['order'],
            'return'        => array(),
            'data'          => array(),
        );

        // load hotel order by hook
        include_once PT_PATH.'hook/hook.php';
        $order_goods = filter::apply('order_goods', $order_goods, $code, $num, $contact, $tel, $address, $remark);
        if (!$order_goods) return false;

        // get return data
        $return    = $order_goods['return'];
        unset($order_goods['return']);


        $db = db(config('db'));

        if (!self::$inset)
            $db -> beginTrans();

        // refresh order price
        $ors = $db  -> prepare("UPDATE `ptc_order` SET `total`=`total`+:total, `floor`=`floor`+:floor, `request`=`request`+:request, `update`=:now, `expire`=:expire, `status`=2, `hide`=0 WHERE `order`=:order;")
                    -> execute(array(':total'=>$order_goods['total'], ':floor'=>$order_goods['floor'], ':order'=>$_order['order'], ':request'=>$request, ':now'=>NOW, ':expire'=>NOW+self::$expire));

        if (!$ors)
        {
            $db -> rollback();
            self::$error = 501;
            return false;
        }

        // exist
        $sql = "SELECT `id`,`order`,`product`,`item`,`goods`,`num`,`floor`,`total`,`currency`,`supply`,`supplyid`,`cancel`,`contact`,`tel`,`address`
                FROM `ptc_order_goods` WHERE `orderid`=:orderid";
        $exists = $db -> prepare($sql) -> execute(array(':orderid' => $order_goods['orderid']));

        $_exist = false;
        if ($exists)
        {
            foreach ($exists as $v)
            {
                $_temp = array_intersect_key($order_goods, $v);
                $_check = array_intersect_assoc($_temp, $v);
                if (count($_check) == count($_temp))
                {
                    $_exist = true;
                    $pid = $v['id'];
                    break;
                }
            }
        }

        if (!$_exist)
        {
            // insert order hotel data
            list($column, $sql, $value) = array_values(insert_array($order_goods));
            $pid = $db -> prepare("INSERT INTO `ptc_order_goods` {$column} VALUES {$sql};") -> execute($value);

            if (!$pid)
            {
                $db -> rollback();
                self::$error = 502;
                return false;
            }
        }

        if (!self::$inset)
        {
            if ($db -> commit())
            {
                return $return;
            }
            else
            {
                self::$error = 504;
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    // addroom






    /**
     * load hotel requires' code
     +-----------------------------------------
     * @access public
     * @return void
     */
    static function require_code()
    {
        static $cache = array();

        if (!$cache)
        {
            $db = db(config('db'));
            $_cache = $db -> prepare("SELECT * FROM `ptc_hotel_require` WHERE 1=1") -> execute();
            foreach ($_cache as $v)
                $cache[$v['code']] = $v['name'];
        }

        return $cache;
    }
    //require_code




    /**
     * apply booking use ticket
     +-----------------------------------------
     * @access public
     * @return void
     */
    static function apply($order, $ticket, $group=0, $checkin='', $adult=2, $child=0, $people='', $birth='', $bed='', $tel='', $email='', $require='')
    {
        $db = db(config('db'));

        $sql = "SELECT r.`id`, r.`ticket`, h.`nights`, h.`room`
                FROM `ptc_order_room` AS r
                    LEFT JOIN `ptc_order_hotel` AS h ON r.`pid` = h.`id`
                WHERE r.`id`=:id AND r.`order`=:order AND h.`supply`='TICKET'";
        $item = $db -> prepare($sql) -> execute(array(':id'=>$ticket, ':order'=>$order));
        if (!$item)
        {
            self::$error = 632;
            return false;
        }

        if ($item[0]['ticket'])
        {
            self::$error = 633;
            return false;
        }

        $checkin = strtotime($checkin);
        if (!$checkin || $checkin % 86400 != 57600 ) // 中国时区
        {
            self::$error = 634;
            return false;
        }
        $checkout = $checkin + 86400 * $item[0]['nights'];

        // debug：库存占用冲突，5分钟后发起，可能存在异步导致库存超
        $allot = $db -> prepare("SELECT * FROM `ptc_product_item_booking` WHERE `item`=:id AND `date`>=:date;") -> execute(array(':id'=>$item[0]['room'], ':date'=>$checkin));
        if (!$allot)
        {
            self::$error = 640;
            return false;
        }

        if ($allot[0]['allot'] - $allot[0]['used'] <= 0)
        {
            self::$error = 641;
            return false;
        }

        if (!$group)
        {
            $max = $db -> prepare("SELECT MAX(`group`) AS `max` FROM `ptc_order_room` WHERE `pid`=:pid") -> execute(array(':pid'=>$item[0]['pid']));
            $group = (int)$max[0]['max'] + 1;
            $room = 1;
        }
        else
        {
            $max = $db -> prepare("SELECT MAX(`room`) AS `max` FROM `ptc_order_room` WHERE `pid`=:pid AND `group`=:group") -> execute(array(':pid'=>$item[0]['pid'], ':group'=>$group));
            $room = (int)$max[0]['max'] + 1;
        }

        $data = array(
            'adult'     => (int)$adult,
            'child'     => (int)$child,
            'people'    => trim($people),
            'birth'     => $birth ? (int)strtotime($birth) : '',
            'bed'       => (string)$bed,
            'require'   => trim($require),
            'tel'       => trim($tel),
            'email'     => trim($email),
            'ticket'    => 15,
            'group'     => $group,
            'room'      => $room,
            'checkin'   => $checkin,
            'checkout'  => $checkout,
        );

        foreach (array('adult', 'people', 'bed', 'tel', 'email') as $i => $a)
        {
            if (!$data[$a])
            {
                self::$error = 635 + $i;
                return false;
            }
        }

        $db -> beginTrans();

        list($sql, $value) = array_values(update_array($data));
        $value[':id'] = $ticket;
        $rs = $db -> prepare("UPDATE `ptc_order_room` SET {$sql} WHERE `id`=:id") -> execute($value);
        if ($rs === false)
        {
            $db -> rollback();
            self::$error = 501;
            return false;
        }

        $rs = $db -> prepare("UPDATE `ptc_product_item_booking` SET `used`=`used`+1 WHERE `id`=:id AND `used`+1 <= `allot`;") -> execute(array(':id'=>$allot[0]['id']));
        if ($rs === false)
        {
            $db -> rollback();
            self::$error = 641;
            return false;
        }

        $status = self::_status($order);
        if (!$status)
        {
            self::$error = 515;
            $db -> rollback();
            return false;
        }

        if (!self::_log($_order[0], '使用券并在线预订', null, '客户'))
        {
            self::$error = 516;
            $db -> rollback();
            return false;
        }

        return $db -> commit() ? array('group'=>$group) : false;
    }
    // apply





    /**
     * invoice information
     +-----------------------------------------
     * @access public
     * @return void
     */
    static function invoice($order, $payer, $item='代订房费', $receiver='', $receivertel='', $receiveraddr='')
    {
        $db = db(config('db'));

        $_order = $db -> prepare("SELECT `id`,`order`,`status`,`create` FROM `ptc_order` WHERE `order`=:order") -> execute(array(':order'=>$order));
        if (!$_order)
        {
            self::$error = 604;
            return false;
        }

        // 半年以上不可开票
        $sql = "SELECT MAX(r.`checkout`) AS `lastcheck`, MIN(r.`ticket`) AS `status`
                FROM `ptc_order_hotel` AS h
                    LEFT JOIN `ptc_order_room` AS r ON h.`order` = r.`order`
                WHERE h.`product`=0 AND h.`producttype`=0 AND h.`order`=:order
                GROUP BY h.`order`";
        $info = $db -> prepare($sql) -> execute(array(':order'=>$order));

        if ($info)
        {
            if ($info[0]['status'] == 0)
            {
                self::$error = 611;
                return false;
            }

            $maxtime = strtotime('+6 month', strtotime(date('Y-m-d 00:00:00', $info[0]['lastcheck'])));
            if ($maxtime <= NOW)
            {
                self::$error = 612;
                return false;
            }
        }


        $db -> beginTrans();
        $data = array(
            'payer'     => $payer,
            'item'      => $item ? $item : '代订房费',
            'receiver'      => $receiver,
            'receivertel'   => $receivertel,
            'receiveraddr'  => $receiveraddr,
        );

        list($sql, $value) = array_values(update_array($data));

        $value[':order'] = $order;
        $rs = $db -> prepare("UPDATE `ptc_order_ext` SET {$sql} WHERE `order`=:order;") -> execute($value);
        if ($rs === false)
        {
            self::$error = 501;
            return false;
        }

        $rs = $db -> prepare("UPDATE `ptc_order` SET `invoice`=1, `update`=:time WHERE `order`=:order;") -> execute(array(':order'=>$order, ':time'=>NOW));
        if ($rs === false)
        {
            self::$error = 502;
            return false;
        }

        if (!self::_log($_order[0], '修改了发票信息', $data, '客户'))
        {
            self::$error = 506;
            $db -> rollback();
            return false;
        }

        if ($db -> commit())
        {
            return true;
        }
        else
        {
            self::$error = 509;
            return false;
        }
    }
    // invoice





    /**
     * submit order (pay)
     +-----------------------------------------
     * @access public
     * @param mixed $order
     * @return void
     */
    static function pay($order, $time='', $type='', $account='', $trade='', $rebate=0, $rebatetype='')
    {
        $db = db(config('db'));

        if (substr($order, 0, 3) == 'OCT')
        {
            list($order, $payid) = explode('_', $order);
            if (!$payid)
                return !self::$error = 604;

            // save pay information
            switch (trim($type))
            {
                case '支付宝': $type = 'alipay'; break;
                case '联动优势': $type = 'ump'; break;
                case '微信支付': $type = 'weixin'; break;
                case '线下付款': $type = 'offline'; break;
                case '招行支付': $type = 'cmb'; break;
                case '连连支付': $type = 'lian'; break;
                default: $type = 'unknown';
            }

            $db -> beginTrans();

            $rs = $db -> prepare("UPDATE `ptc_tour_order_pay` SET `status`=1, `paytime`=:time, `paytrade`=:trade WHERE `order`=:order AND `id`=:id AND `status`=0;")
                      -> execute(array(':order'=>$order, ':id'=>$payid, ':time'=>NOW, ':trade'=>$type.':'.$trade));
            if ($rs === false)
            {
                $db -> rollback();
                return !self::$error = 520;
            }

            $unpay = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_tour_order_pay` WHERE `order`=:order AND `status`=0 AND `deposit`>=0;") -> execute(array(':order'=>$order));
            if (!$unpay[0]['c'])
            {
                $rs = $db -> prepare("UPDATE `ptc_tour_order` SET `status`=7 WHERE `order`=:order AND `status`=6;") -> execute(array(':order'=>$order));
                if (!$rs)
                {
                    $db -> rollback();
                    return !self::$error = 521;
                }

                $rs = $db -> prepare("UPDATE `ptc_tour_card` SET `status`=7 WHERE `code`=:code;") -> execute(array(':code'=>substr($order, 1)));
                if ($rs === false)
                {
                    $db -> rollback();
                    return !self::$error = 522;
                }
            }

            return $db -> commit() ? true : false;
        }
        else
        {
            $db -> beginTrans();
            // load order
            $_order = $db -> prepare("SELECT `id`,`order`,`status`,`expire`,`paytime` FROM `ptc_order` WHERE `order`=:order") -> execute(array(':order'=>$order));
            if (!$_order)
            {
                self::$error = 604;
                return false;
            }

            if ($_order[0]['status'] != 2)
            {
                self::$error = 608;
                return false;
            }

            include_once PT_PATH.'hook/hook.php';

            // recheck before pay
            $types = array('hotel', 'flight', 'auto', 'goods');
            foreach ($types as $i => $t)
            {
                $datas = $db -> prepare("SELECT * FROM `ptc_order_{$t}` WHERE `order`=:order") -> execute(array(':order'=>$order));
                foreach ($datas as $value)
                {
                    $status = $value['status'];
                    $status = filter::apply('order_pay', $status, $value, $t, $_order[0]['expire']);
                    if ($status <= 0)
                    {
                        self::$error = $status ? abs($status) : 619;
                        $db -> rollback();
                        return false;
                    }

                    if ($status != $value['status'])
                    {
                        $rs = $db -> prepare("UPDATE `ptc_order_{$t}` SET `status`=:status WHERE `id`=:id") -> execute(array(':status'=>$status, ':id'=>$value['id']));
                        if (false === $rs)
                        {
                            self::$error = 501 . $i;
                            $db -> rollback();
                            return false;
                        }

                        if ($t == 'hotel' && in_array($value['settleby'], array(2,3)))
                        {
                            $rs = $db -> prepare("UPDATE `ptc_order_room` SET `settletime`=:now WHERE `pid`=:id;") -> execute(array(':id'=>$value['id'], ':now'=>NOW));
                            if (false === $rs)
                            {
                                self::$error = 502;
                                $db -> rollback();
                                return false;
                            }
                        }
                    }
                }
            }

            $time = (int)strtotime($time);

            // change status
            $rs = $db -> prepare("UPDATE `ptc_order` SET `status`=3, `update`=:time, `paytime`=:time, `rebate`=:rebate WHERE `order`=:order AND `status`=2")
                      -> execute(array(':status'=>$status, ':time'=>$time, ':order'=>$order, ':rebate'=>(int)$rebate));

            if (false === $rs)
            {
                self::$error = 504;
                $db -> rollback();
                return false;
            }

            $_order[0]['paytime'] = $time;

            if (!self::_status($_order[0]))
            {
                self::$error = 505;
                $db -> rollback();
                return false;
            }

            // save pay information
            switch (trim($type))
            {
                case '支付宝': $type = 'alipay'; break;
                case '联动优势': $type = 'ump'; break;
                case '微信支付': $type = 'weixin'; break;
                case '线下付款': $type = 'offline'; break;
                case '招行支付': $type = 'cmb'; break;
                case '连连支付': $type = 'lian'; break;
                default: $type = 'unknown';
            }

            $data = array('paytype'=>$type, 'payaccount'=>(string)$account, 'paytime'=>$time, 'paytrade'=>(string)$trade, 'rebatetype'=>(string)$rebatetype);

            list($sql, $value) = array_values(update_array($data));
            $value[':order'] = $order;
            $rs = $db -> prepare("UPDATE `ptc_order_ext` SET {$sql} WHERE `order`=:order;") -> execute($value);
            if (false === $rs)
            {
                self::$error = 506;
                $db -> rollback();
                return false;
            }

            if (!self::_log($_order[0], '支付了订单'.($rebate > 0 ? '(含优惠)' : ''), $data, empty($_SESSION['uid']) ? '客户' : ''))
            {
                self::$error = 507;
                $db -> rollback();
                return false;
            }

            /* test all
            $db -> rollback();
            return false;
            //*/

            return $db -> commit() ? true : false;
        }
    }
    // pay




    /**
     * refund order from user
     +-----------------------------------------
     * @access public
     * @param mixed $order
     * @return void
     */
    static function refund($order, $remark='')
    {
        $db = db(config('db'));
        $db -> beginTrans();

        // load order
        $_order = $db -> prepare("SELECT * FROM `ptc_order` WHERE `order`=:order") -> execute(array(':order'=>$order));
        if (!$_order)
        {
            self::$error = 604;
            return false;
        }

        if ($_order[0]['status'] <= 2)
        {
            self::$error = 609;
            return false;
        }

        if (in_array($_order[0]['status'], array(8,9,10,11,12,14,16)))
        {
            self::$error = 610;
            return false;
        }


        include_once PT_PATH.'hook/hook.php';

        // recheck before refund
        $types = array('hotel', 'flight', 'auto', 'goods');
        foreach ($types as $i => $type)
        {
            $datas = $db -> prepare("SELECT * FROM `ptc_order_{$type}` WHERE `order`=:order") -> execute(array(':order'=>$order));
            foreach ($datas as $value)
            {
                $status = $value['status'];
                $status = filter::apply('order_apply_refund', $status, $value, $type);
                if ($status < 0)
                {
                    self::$error = -$status;
                    $db -> rollback();
                    return false;
                }

                if ($status && $status != $value['status'])
                {
                    $rs = $db -> prepare("UPDATE `ptc_order_{$type}` SET `status`=:status WHERE `id`=:id") -> execute(array(':status'=>$status, ':id'=>$value['id']));
                    if (false === $rs)
                    {
                        self::$error = 511 + $i;
                        $db -> rollback();
                        return false;
                    }
                }
            }
        }

        if (false === $db -> prepare("UPDATE `ptc_order` SET `refundtime`=:time WHERE `order`=:order") -> execute(array(':order'=>$order, ':time'=>NOW)))
        {
            self::$error = 514;
            $db -> rollback();
            return false;
        }

        $status = self::_status($_order[0]);
        if (!$status)
        {
            self::$error = 515;
            $db -> rollback();
            return false;
        }

        if ($status[0] != 10 && $status[1] != 10)
        {
            self::$error = 610;
            $db -> rollback();
            return false;
        }

        if (!self::_log($_order[0], '申请了订单退款'.($remark ? '客户备注：'.$remark : ''), null, '客户'))
        {
            self::$error = 516;
            $db -> rollback();
            return false;
        }

        sms::send($_order[0]['order'], 'refund', $_order[0]);

        return $db -> commit() ? true : false;
    }
    // refund







    // ---------------------------- v ---------------------------- Load & Status ---------------------------- v ----------------------------



    // get order main data
    static function _order($code)
    {
        $db = db(config('db'));
        $order = $db -> prepare("SELECT `id`,`order`,`contact`,`tel`,`total`,`from`,`status` FROM `ptc_order` WHERE `order`=:code;") -> execute(array(':code'=>$code));
        return $order ? $order[0] : false;
    }
    // _order



    /**
     * view order
     +-----------------------------------------
     * @access public
     * @param mixed $order
     * @param bool $admin
     * @param bool $detail
     * @return void
     */
    static public function view($order, $admin=false, $detail=true)
    {
        $db = db(config('db'));

        if (is_string($order))
        {
            $order = $db -> prepare("SELECT `id`,`order`,`currency`,`total`,`contact`,`tel`,`email`,`invoice`,`status`,`from`,`expire`".($admin ? ',`paytime`' : '')." FROM `ptc_order` WHERE `order`=:code;") -> execute(array(':code'=>$order));
            if (!$order) return false;
            $order = $order[0];

            $ext = $db -> prepare("SELECT `payer`,`item`,`receiver`,`receivertel`,`receiveraddr`,`expresstype`,`expressno`,`paytype`,`payaccount`,`paytrade`,`paytime` FROM `ptc_order_ext` WHERE `orderid`=:id;")
                       -> execute(array(':id'=>$order['id']));
            $ext = $ext[0];

            if ($order['invoice'] > 0)
                $order['invoice'] = array('payer'=>$ext['payer'], 'item'=>$ext['item'], 'receiver'=>$ext['receiver'], 'receivertel'=>$ext['receivertel'], 'receiveraddr'=>$ext['receiveraddr'], 'expresstype'=>$ext['expressno'] ? expressname($ext['expresstype']) : '', 'expressno'=>$ext['expressno']);
            else
                $order['invoice'] = 0;

            if ($order['status'] > 3)
                $order['pay'] = array('paytype'=>$ext['paytype'], 'payaccount'=>$ext['payaccount'], 'paytrade'=>$ext['paytrade'], 'paytime'=>date('Y-m-d H:i:s', $ext['paytime']));
        }

        // ------v------ products
        $products = array();

        // ------v------ hotel ------v------
        $_hotels = $db -> prepare("SELECT * FROM `ptc_order_hotel` WHERE `orderid`=:oid") -> execute(array(':oid'=>$order['id']));
        if ($_hotels)
        {
            $hotels = array();
            foreach ($_hotels as $k => $_hotel)
            {
                if ($detail)
                {
                    $fields = '`id`,`people`,`bed`,`require`,`addbe`,`addbf`,`addbeprice`,`addbfprice`';
                    if ($_hotel['supply'] == 'TICKET') $fields .= ',`ticket`,`checkin`,`checkout`,`group`,`room`';
                    if ($admin) $fields .= ',`data`,`settle`,`settletime`';
                    $rooms = $db -> prepare("SELECT {$fields} FROM `ptc_order_room` WHERE `orderid`=:oid AND `pid`=:pid") -> execute(array(':oid'=>$order['id'], ':pid'=>$_hotel['id']));

                    if (!$admin && $_hotel['supply'] == 'TICKET')
                    {
                        $group = array();
                        foreach($rooms as $kv => $v)
                        {
                            if ($v['ticket'])
                            {
                                if (!isset($group[$v['group']]))
                                    $group[$v['group']] = array();

                                if (!isset($group[$v['group']][$v['room']]))
                                    $group[$v['group']][$v['room']] = 0;

                                $rooms[$kv]['checkin'] = date('Y-m-d', $v['checkin'] + $group[$v['group']][$v['room']] * $_hotel['nights'] * 86400 );
                                $rooms[$kv]['checkout'] = date('Y-m-d', $v['checkin'] + ($group[$v['group']][$v['room']] + 1) * $_hotel['nights'] * 86400);

                                $group[$v['group']][$v['room']]++;
                            }
                        }
                    }
                }

                $hotel = array(
                    'type'      => 'hotel',
                    'payment'   => $_hotel['supply'] == 'TICKET' ? 'ticket' : 'prepay',
                    'hotel'     => $_hotel['hotel'],
                    'room'      => $_hotel['roomtype'],
                    'bed'       => $_hotel['bed'],
                    'hotelname' => $_hotel['hotelname'],
                    '_room'     => $_hotel['room'],
                    'roomname'  => $_hotel['roomname'],
                    'nation'    => $_hotel['nation'],
                    'package'   => $_hotel['package'],
                    'checkin'   => $_hotel['checkin'] ? date('Y-m-d', $_hotel['checkin']) : '',
                    'checkout'  => $_hotel['checkout'] ? date('Y-m-d', $_hotel['checkout']) : '',
                    'nights'    => $_hotel['nights'],
                    'start'     => $_hotel['start'] ? date('Y-m-d', $_hotel['start']) : '',
                    'end'       => $_hotel['end']   ? date('Y-m-d', $_hotel['end']) : '',
                    'min'       => $_hotel['min'],
                    'advance'   => $_hotel['advance'],
                    'rooms'     => isset($rooms) ? $rooms : $_hotel['rooms'],
                    'total'     => $_hotel['total'],
                    'remark'    => $_hotel['remark'],
                    'status'    => $_hotel['status'],
                    'status2'   => $_hotel['status2'],
                    'status3'   => $_hotel['status3'],
                );

                if ($admin)
                {
                    // operator mode, append more information
                    $hotel['id']            = $_hotel['id'];
                    $hotel['product']       = $_hotel['product'];
                    $hotel['checkin']       = $_hotel['checkin'];
                    $hotel['checkout']      = $_hotel['checkout'];
                    $hotel['start']         = $_hotel['start'];
                    $hotel['end']           = $_hotel['end'];
                    $hotel['supply']        = $_hotel['supply'];
                    $hotel['confirmno']     = $_hotel['confirmno'];
                    $hotel['supplyorder']   = $_hotel['supplyorder'];
                    $hotel['refund']        = $_hotel['refund'];
                    $hotel['settleby']      = $_hotel['settleby'];
                    $hotel['data']          = $_hotel['data'];
                    $hotel['operator']      = $_hotel['operator'];

                    if ($hotel['product'])
                    {
                        $hotel['product_type']      = $_hotel['producttype'];
                        $hotel['product_payment']   = $hotel['payment'];
                    }
                }

                if ($_hotel['product'])
                {
                    // create new product element
                    $proid = $_hotel['product'];
                    if (empty($products[$proid]))
                    {
                        $products[$proid] = array(
                            'id'        => $proid,
                            'name'      => $_hotel['productname'],
                            'type'      => $_hotel['producttype'],
                            'payment'   => $hotel['payment'],
                            'total'     => 0,
                            'items'     => array(),
                        );

                        if ($admin) $products[$proid]['operator'] = $hotel['operator'];
                    }

                    $hotel['item'] = $_hotel['itemname'];
                    unset($hotel['payment']);

                    $products[$proid]['total']   += $hotel['total'];
                    $products[$proid]['items'][] = $hotel;
                }
                else
                {
                    if ($_hotel['productname'])
                    {
                        $hotel['productname'] = $_hotel['productname'];
                        $hotel['itemname']    = $_hotel['itemname'];
                        $hotel['ticket']      = $_hotel['supplyid'];
                    }
                    $hotels[] = $hotel;
                }
            }
        }
        // ------^------ hotel ------^------


        // ------v------ flight ------v------
        $_flights = $db -> prepare("SELECT * FROM `ptc_order_flight` WHERE `orderid`=:oid") -> execute(array(':oid'=>$order['id']));
        if ($_flights)
        {
            $flights = array();
            foreach ($_flights as $k => $_flight)
            {
                if ($detail)
                {
                    $fields = '`people`,`type`,`credential`,`fuel`,`tax`,(`total`-`fuel`-`tax`) AS `price`';
                    if ($admin) $fields .= ',`id`';
                    $passengers = $db -> prepare("SELECT {$fields} FROM `ptc_order_passenger` WHERE `orderid`=:oid AND `pid`=:pid") -> execute(array(':oid'=>$order['id'], ':pid'=>$_flight['id']));
                }

                $flight = array(
                    'type'      => 'flight',
                    'payment'   => $_flight['supply'] == 'TICKET' ? 'ticket' : 'prepay',
                    'flight'    => $_flight['flight'],
                    'flightname'=> $_flight['flightname'],
                    'flightcode'=> $_flight['flightcode'],
                    'leg'       => $_flight['leg'],
                    'class'     => $_flight['class'],
                    'date'      => $_flight['date']  ?  date('Y-m-d', $_flight['date']) : '',
                    'start'     => $_flight['start'] ?  date('Y-m-d', $_flight['start']) : '',
                    'end'       => $_flight['end']   ?  date('Y-m-d', $_flight['end']) : '',
                    'advance'   => $_flight['advance'],
                    'meal'      => $_flight['meal'],
                    'back'      => $_flight['back'],
                    'backday'   => $_flight['backday'],
                    'passengers'=> isset($passengers) ? $passengers : $_flight['num'],
                    'total'     => $_flight['total'],
                    'remark'    => $_flight['remark'],
                    'status'    => $_flight['status'],
                );

                if ($admin)
                {
                    // operator mode, append more information
                    $flight['id']       = $_flight['id'];
                    $flight['product']  = $_flight['product'];
                    $flight['supply']   = $_flight['supply'];
                    $flight['data']     = $_flight['data'];
                    $flight['date']     = $_flight['date'];
                    $flight['start']    = $_flight['start'];
                    $flight['end']      = $_flight['end'];
                    $flight['refund']   = $_flight['refund'];
                    $flight['operator'] = $_flight['operator'];

                    if ($flight['product'])
                    {
                        $flight['product_type']      = $_flight['producttype'];
                        $flight['product_payment']   = $flight['payment'];
                    }
                }

                if ($_flight['product'])
                {
                    // create new product element
                    $proid = $_flight['product'];
                    if (empty($products[$proid]))
                    {
                        $products[$proid] = array(
                            'id'        => $proid,
                            'name'      => $_flight['productname'],
                            'type'      => $_flight['producttype'],
                            'payment'   => $flight['payment'],
                            'total'     => 0,
                            'items'     => array(),
                        );

                        if ($admin) $products[$proid]['operator'] = $flight['operator'];
                    }

                    $flight['item'] = $_flight['itemname'];
                    unset($flight['payment']);

                    $products[$proid]['total']   += $flight['total'];
                    $products[$proid]['items'][] = $flight;
                }
                else
                {
                    if ($_flight['productname'])
                    {
                        $flight['productname'] = $_flight['productname'];
                        $flight['itemname']    = $_flight['itemname'];
                    }
                    $flights[] = $flight;
                }
            }
        }
        // ------^------ flight ------^------

        // ------v------ auto ------v------
        $_autos = $db -> prepare("SELECT * FROM `ptc_order_auto` WHERE `orderid`=:oid") -> execute(array(':oid'=>$order['id']));
        if ($_autos)
        {
            $autos = array();
            foreach ($_autos as $k => $_auto)
            {
                if ($detail)
                {
                    $drivers = explode('||', $_auto['driver']);
                    $peoples = array();
                    foreach ($drivers as $v)
                    {
                        list($people, $credential) = explode('|', $v);
                        $peoples[] = array('people'=>$people, 'credential'=>$credential);
                    }
                }

                $auto = array(
                    'type'      => 'auto',
                    'payment'   => $_auto['supply'] == 'TICKET' ? 'ticket' : 'prepay',
                    'auto'      => $_auto['auto'],
                    'autoname'  => $_auto['autoname'],
                    'autocode'  => $_auto['autocode'],
                    'date'      => $_auto['date']  ?  date('Y-m-d', $_auto['date']) : '',
                    'start'     => $_auto['start'] ?  date('Y-m-d', $_auto['start']) : '',
                    'end'       => $_auto['end']   ?  date('Y-m-d', $_auto['end']) : '',
                    'advance'   => $_auto['advance'],
                    'driver'    => isset($peoples) ? $peoples : $_auto['num'],
                    'total'     => $_auto['total'],
                    'remark'    => $_auto['remark'],
                    'status'    => $_auto['status'],
                );

                if ($admin)
                {
                    // operator mode, append more information
                    $auto['id']       = $_auto['id'];
                    $auto['product']  = $_auto['product'];
                    $auto['supply']   = $_auto['supply'];
                    $auto['data']     = $_auto['data'];
                    $auto['date']     = $_auto['date'];
                    $auto['start']    = $_auto['start'];
                    $auto['end']      = $_auto['end'];
                    $auto['refund']   = $_auto['refund'];
                    $auto['operator'] = $_auto['operator'];

                    if ($auto['product'])
                    {
                        $auto['product_type']      = $_auto['producttype'];
                        $auto['product_payment']   = $auto['payment'];
                    }
                }

                if ($_auto['product'])
                {
                    // create new product element
                    $proid = $_auto['product'];
                    if (empty($products[$proid]))
                    {
                        $products[$proid] = array(
                            'id'        => $proid,
                            'name'      => $_auto['productname'],
                            'type'      => $_auto['producttype'],
                            'payment'   => $auto['payment'],
                            'total'     => 0,
                            'items'     => array(),
                        );

                        if ($admin) $products[$proid]['operator'] = $auto['operator'];
                    }

                    $auto['item'] = $_auto['itemname'];
                    unset($auto['payment']);

                    $products[$proid]['total']   += $auto['total'];
                    $products[$proid]['items'][] = $auto;
                }
                else
                {
                    if ($_auto['productname'])
                    {
                        $auto['productname'] = $_auto['productname'];
                        $auto['itemname']    = $_auto['itemname'];
                    }
                    $autos[] = $auto;
                }
            }
        }
        // ------^------ auto ------^------

        // ------v------ goods ------v------
        $_goods = $db -> prepare("SELECT * FROM `ptc_order_goods` WHERE `orderid`=:oid") -> execute(array(':oid'=>$order['id']));
        if ($_goods)
        {
            $goods = array();
            foreach ($_goods as $k => $_good)
            {
                $good = array(
                    'type'      => 'goods',
                    'payment'   => $_good['supply'] == 'TICKET' ? 'ticket' : 'prepay',
                    'goods'     => $_good['goods'],
                    'goodsname' => $_good['goodsname'],
                    'num'       => $_good['num'],
                    'total'     => $_good['total'],
                    'remark'    => $_good['remark'],
                    'status'    => $_good['status'],
                );

                if ($detail)
                {
                    $good['contact'] = $_good['contact'];
                    $good['tel']     = $_good['tel'];
                    $good['address'] = $_good['address'];
                }

                if ($admin)
                {
                    // operator mode, append more information
                    $good['id']       = $_good['id'];
                    $good['item']     = $_good['item'];
                    $good['product']  = $_good['product'];
                    $good['supply']   = $_good['supply'];
                    $good['refund']   = $_good['refund'];
                    $good['operator'] = $_good['operator'];

                    if ($good['product'])
                    {
                        $good['product_type']      = $_good['producttype'];
                        $good['product_payment']   = $good['payment'];
                    }
                }

                if ($_good['product'])
                {
                    // create new product element
                    $proid = $_good['product'];
                    if (empty($products[$proid]))
                    {
                        $products[$proid] = array(
                            'id'        => $proid,
                            'name'      => $_good['productname'],
                            'type'      => $_good['producttype'],
                            'payment'   => $good['payment'],
                            'total'     => 0,
                            'items'     => array(),
                        );

                        if ($admin) $products[$proid]['operator'] = $good['operator'];
                    }

                    $good['item'] = $_good['itemname'];
                    unset($good['payment']);

                    $products[$proid]['total']   += $good['total'];
                    $products[$proid]['items'][] = $good;
                }
                else
                {
                    if ($_good['productname'])
                    {
                        $good['productname'] = $_good['productname'];
                        $good['itemname']    = $_good['itemname'];
                        $good['ticket']      = $_good['supplyid'];
                    }
                    $goods[] = $good;
                }
            }
        }
        // ------^------ goods ------^------
        foreach(array('hotels', 'flights', 'autos', 'goods', 'products') as $t)
        {
            if (!empty($$t))
            {
                $order[$t] = array_values($$t);
            }
        }

        unset($order['id']);
        return $order;
    }
    // view




    // get all status
    static function status()
    {
        $db = db(config('db'));
        $_status = $db -> prepare("SELECT `id`, `name` FROM `ptc_order_status`") -> execute();
        $status = array();
        foreach ($_status as $v)
        {
            $status[$v['id']] = $v['name'];
        }

        return $status;
    }
    // status




    // order log
    static function _log($order, $message='', $data, $user='')
    {
        $log = array(
            'orderid'   => $order['id'],
            'order'     => $order['order'],
            'data'      => json_encode($data),
            'time'      => NOW,
            'uid'       => $user ? 0 : $_SESSION['uid'],
            'username'  => $user ? $user : $_SESSION['name'],
            'remark'    => $message,
        );

        $db = db(config('db'));
        list($column, $sql, $value) = array_values(insert_array($log));
        $rs = $db -> prepare("INSERT INTO `ptc_order_log` {$column} VALUES {$sql};") -> execute($value);
        return $rs;
    }
    // _log


	/**
	 * order's STATUS
	 * 1:支付后等待确认
	 * 2:  待支付
	 * 3;//订单支付成功，未预约
	 * 4;支付成功,发出预约等酒店确认
	 * 5:预订失败
	 * 8;// 酒店回传确认，预约成功
	 * 9;//订单完结
	 * 10;// 退款申请
	 * 11:退订退款完成
	 * 12;// 拒绝退订
	 * 16;// 部分退款
	 */

    // format order status
    static function _status($order)
    {
        $order = self::view($order, false, false);
        $_status = array();
        if (!empty($order['hotels']))
        {
            foreach ($order['hotels'] as $v)
            {
                $_status[] = $v['status'];
                if ($v['status2'])
                    $_status[] = $v['status2'];
                if ($v['status3'])
                    $_status[] = $v['status3'];
            }
        }

        if (!empty($order['flights']))
        {
            foreach ($order['flights'] as $v)
            {
                $_status[] = $v['status'];
            }
        }

        if (!empty($order['goods']))
        {
            foreach ($order['goods'] as $v)
            {
                $_status[] = $v['status'];
            }
        }

        if (!empty($order['products']))
        {
            foreach ($order['products'] as $v)
            {
                foreach ($v['items'] as $s)
                    $_status[] = $s['status'];
            }
        }


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
            if (in_array(15, $s)) $status[] = 15;
        }

        $status = array_reverse($status);

        $db = db(config('db'));
        $rs = $db -> prepare("UPDATE `ptc_order` SET `status`=:status, `status2`=:status2, `status3`=:status3, `update`=:time WHERE `order`=:order;")
                  -> execute(array(':order'=>$order['order'], ':status'=>$status[0], ':status2'=>$status[1], ':status3'=>$status[2], ':time'=>NOW));
        if (false === $rs)
            return false;
        else
            return $status;
    }
    // _status
}


?>
