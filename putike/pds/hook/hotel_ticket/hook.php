<?php
// hotel ticket product hook
class hotel_ticket_hook
{
    static $initialise = false;

    protected $_profit = 'IF( fi3.`profit` IS NULL,
                            IF( fi2.`profit` IS NULL,
                                IF( fi1.`type` = "amount", fi1.`profit`, i.`price` * fi1.`profit` / 100),
                                IF( fi2.`type` = "amount", fi2.`profit`, i.`price` * fi2.`profit` / 100)
                            ),
                            IF( fi3.`type` = "amount", fi3.`profit`, i.`price` * fi3.`profit` / 100)
                        )';

    protected $_timeout = 1200;


    public function __construct()
    {
        if (self::$initialise) return true;
        self::$initialise = true;

        filter::add('api_hotel_search_condition',           array($this, 'api_search_condition'), 10, 6);
        filter::add('api_hotel_products',                   array($this, 'api_search'), 10, 6);

        filter::add('product_items',                        array($this, 'api_items'), 10, 1);

        filter::add('order_room',                           array($this, 'order_booking'), 10, 6);
        filter::add('order_pay',                            array($this, 'order_pay'), 10, 4);
        filter::add('order_apply_refund',                   array($this, 'order_apply_refund'), 10, 3);
        filter::add('order_complete',                       array($this, 'order_complete'), 10, 3);
        filter::add('order_confirmation',                   array($this, 'order_confirmation'), 10, 2);

        action::add('order_manage_tpl_extend',              array($this, 'order_tpl_extend'), 10, 4);
        action::add('order_manage_tpl_operation',           array($this, 'order_tpl_operation'), 10, 3);
        action::add('order_manage_tpl_footer',              array($this, 'order_tpl_footer'), 10, 3);
        filter::add('order_use_rule',                       array($this, 'order_use_rule'), 10, 1);
        filter::add('order_refund_rule',                    array($this, 'order_refund_rule'), 10, 1);

        action::add('order_manage_save',                    array($this, 'order_operate'), 10, 1);

        action::add('product_preview',                      array($this, 'preview'), 10, 1);

        action::add('product_item_manage_tpl',              array($this, 'item_tpl'), 10, 5);
        filter::add('product_item_manage_save',             array($this, 'item_save'), 10, 3);
        filter::add('product_item_manage_save_callback',    array($this, 'item_save_callback'), 10, 3);

        action::add('product_list_item_manage_tpl',         array($this, 'list_control'), 10, 2);
        action::add('product_item_manage_modal',            array($this, 'modal'), 10);
        action::add('product_item_manage_extend',           array($this, 'extend'), 10, 1);

        action::add('product_item_manage_price_tpl',        array($this, 'item_setprice'), 10, 1);
        action::add('product_item_manage_price_save',       array($this, 'item_setprice'), 10, 1);
    }



    /**
     * Magic Call Method
     */
    public function __call($method, $args=array())
    {
        static $api, $order;

        if (false !== strpos($method, 'api'))
        {
            $method = substr($method, 4);
            include_once dirname(__FILE__).'/api.php';

            if (!$api) $api = new hotel_ticket_api();
            return call_user_func_array(array($api, $method), $args);
        }

        else if (false !== strpos($method, 'order'))
        {
            $method = substr($method, 6);
            include_once dirname(__FILE__).'/order.php';

            if (!$order) $order = new hotel_ticket_order();
            return call_user_func_array(array($order, $method), $args);
        }
    }
    // __call



    /**
     * product preview
     +-----------------------------------------
     * @access public
     * @param mixed $product
     * @return void
     */
    public function preview($product)
    {
        if ($product['type'] != 1 || $product['payment'] != 'ticket') return;

        $sql = "SELECT i.*, h.`name` AS `hotelname`, r.`name` AS `roomname`,
                (i.`price` + ROUND({$this -> _profit})) AS `price`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_hotel` AS h ON i.`objtype`='room' AND i.`objpid`=h.`id`
                    LEFT JOIN `ptc_hotel_room_type` AS r ON i.`objtype`='room' AND i.`objid`=r.`id`
                    LEFT JOIN `ptc_org_profit` AS fi1 ON fi1.`org` = 1 AND fi1.`payment` = 'ticket' AND fi1.`objtype` = 'hotel' AND fi1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fi2 ON fi2.`org` = 1 AND fi2.`payment` = 'ticket' AND fi2.`objtype` = 'hotel' AND fi2.`objid` = i.`pid`
                    LEFT JOIN `ptc_org_profit` AS fi3 ON fi3.`org` = 1 AND fi3.`payment` = 'ticket' AND fi3.`objtype` = 'room'  AND fi3.`objid` = i.`id`
                WHERE i.`pid` = :pid
                ORDER BY i.`id` ASC";

        $db = db(config('db'));
        $items = $db -> prepare($sql) -> execute(array(':pid'=>$product['id']));

        $minprice = 0;
        foreach ($items as $i)
            if (!$minprice || $i['price'] < $minprice) $minprice = $i['price'];

        include dirname(__FILE__).'/preview/index.tpl.php';
    }
    // preview




    /**
     * product item template
     +-----------------------------------------
     * @access public
     * @param int $pid
     * @param int $type
     * @param string $payment
     * @param string $itemtype
     * @param int $itemid
     * @return void
     */
    public function item_tpl($pid, $type, $payment, $itemtype, $itemid)
    {
        if ($type != 1 || $payment != 'ticket') return;

        if ($itemid)
        {
            $db = db(config('db'));

            $sql = "SELECT p.*, h.`name` AS `hotel_name`, r.`name` AS `room`
                    FROM `ptc_product_item` AS p
                        LEFT JOIN `ptc_hotel` AS h ON p.`objpid` = h.`id`
                        LEFT JOIN `ptc_hotel_room_type` AS r ON p.`objid` = r.`id`
                    WHERE p.`id`=:id;";
            $data = $db -> prepare($sql) -> execute(array(':id' => $itemid));
            if ($data)
            {
                $data = $data[0];
                $extend = $data['data'] ? json_decode($data['data'], true) : null;

                // package
                $supply = $db -> prepare("SELECT * FROM `ptc_supply` WHERE `id`=:id;") -> execute(array(':id'=>(int)$data['supply']));
                $extend['supply_name'] = $supply ? $supply[0]['name'] : '';

                // hotel room
                if ($data['objpid'])
                {
                    $rooms = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel_room_type` WHERE `hotel`=:id") -> execute(array(':id'=>$data['objpid']));
                    foreach ($rooms as $k => $v)
                        $rooms[$k]['name'] = roomname($v['name'], 2);
                }

                // nation
                $nation = $db -> prepare("SELECT * FROM `ptc_nation` WHERE `id`=:id;") -> execute(array(':id'=>(int)$extend['nation']));
                $extend['nation_name'] = $nation ? $nation[0]['name'] : '';

                // package
                $package = $db -> prepare("SELECT * FROM `ptc_hotel_package` WHERE `id`=:id;") -> execute(array(':id'=>(int)$extend['package']));
                $extend['package_name'] = $package ? $package[0]['name'] : '';

                // profit
                $profit = $db -> prepare("SELECT `profit` FROM `ptc_org_profit` WHERE `org`=0 AND `payment`='ticket' AND `objtype`='room' AND `objid`=:id") -> execute(array(':id'=>$data['id']));
                $profit = $profit ? $profit[0]['profit'] : null;
            }
            else
            {
                echo "<div class=\"alert alert-warning\" role=\"alert\">产品内容丢失，请联系开发人员！</div>";
            }
        }
        else
        {
            $data = null;
            $extend = null;
            $rooms = null;
            $profit = null;
        }

        include dirname(__FILE__).'/product/item.tpl.php';
    }
    // item_tpl




    private $save_data = array();


    /**
     * save item's data
     +-----------------------------------------
     * @access public
     * @param array $data
     * @param string $type
     * @return void
     */
    public function item_save($data, $product, $type='hotel')
    {
        if ($product['type'] != 1 || $product['payment'] != 'ticket' || $type != 'hotel') return $data;

        $data['objtype']    = 'room';
        $data['objid']      = (int)$_POST['room'];
        $data['objpid']     = (int)$_POST['hotel'];
        $data['ext']        = (int)$_POST['ext'];
        $data['ext2']       = (string)$_POST['bed'];
        $data['online']     = (int)strtotime($_POST['online']);
        $data['offline']    = (int)strtotime($_POST['offline']);
        $data['userdata']   = trim($_POST['userdata']);
        $data['start']      = (int)strtotime($_POST['start']);
        $data['end']        = (int)strtotime($_POST['end']);
        $data['remark']     = trim($_POST['remark']);
        $data['supply']     = (int)$_POST['supply'];
        $data['bookingcode']= trim($_POST['bookingcode']);
        $data['price']      = (int)$_POST['price'];
        $data['currency']   = trim($_POST['currency']);
        $data['rate']       = (int)$_POST['rate'];
        $data['data']       = array(
                'advance'       => (int)$_POST['advance'],
                'min'           => (int)$_POST['min'],
                'nation'        => (int)$_POST['nation'],
                'package'       => (int)$_POST['package'],
                'net'           => intval($_POST['wifi'].$_POST['net']),
                'addbf'         => (int)$_POST['addbf'],
                'addbe'         => (int)$_POST['addbe'],
        );

        $data['data'] = json_encode($data['data']);

        if (!$data['objpid']) json_return(null, 1, '未选择关联酒店');
        if (!$data['objid'])  json_return(null, 1, '未选择关联房型');
        if (!$data['ext'])    json_return(null, 1, '价格包含晚数必填');              // remove future
        if (!$data['online'] || !$data['offline']) json_return(null, 1, '售卖时间不能为空');
        if (!$data['start'] || !$data['end'])      json_return(null, 1, '预订有效期不能为空');

        $db = db(config('db'));

        // get hotel and room information
        $hotel = $db -> prepare("SELECT `city`,`name` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$data['objpid']));
        if (!$hotel) json_return(null, 1, '酒店不存在');

        $room = $db -> prepare("SELECT `name` FROM `ptc_hotel_room_type` WHERE `id`=:id") -> execute(array(':id'=>$data['objid']));
        if (!$room) json_return(null, 1, '酒店房型不存在');

        $data['target'] = $hotel[0]['city'];

        $this -> save_data = array('hotel_name' => $hotel[0]['name'], 'room_name' => roomname($room[0]['name'], $data['ext2']));

        return $data;
    }
    // item_save






    /**
     * save callback
     +-----------------------------------------
     * @access public
     * @param array $data
     * @return void
     */
    public function item_save_callback($data)
    {
        return array_merge($data, $this -> save_data);

        if ((int)$_POST['total'])
        {
            $db = db(config('db'));

            // 计算利润
            $profit = intval($_POST['total']) - intval($_POST['price']);

            $data = array('org'=>0, 'payment'=>'ticket', 'objtype'=>'room', 'objid'=>$item['id'], 'profit'=>$profit, 'type'=>'amount', 'updatetime'=>NOW);
            $datas = array($data);

            $orgs = $db -> prepare("SELECT * FROM `ptc_org`") -> execute();
            foreach ($orgs as $v)
            {
                $data['org'] = $v['id'];
                $datas[] = $data;
            }

            list($column, $sql, $value) = array_values(insert_array($datas));
            $rs = $db -> prepare("REPLACE INTO `ptc_org_profit` {$column} VALUES {$sql};") -> execute($value);
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 2, '数据保存失败，请重试~');
            }
        }
    }
    // item_save_callback




    /**
     * products item control template
     +-----------------------------------------
     * @access public
     * @param mixed $product
     * @param mixed $item
     * @return void
     */
    public function list_control($product, $item)
    {
        if ($product['type'] != 1 || $product['payment'] != 'ticket') return;

        echo '<a href="javascript:;" onclick="booking(this);" data-code="'.$item['id'].'" class="btn btn-sm btn-default"><span class="fa fa-calendar hidden-md"></span><span class="hidden-xs hidden-sm"> 预订</span></a>';
    }
    // list_control


    public function modal()
    {
        include dirname(__FILE__).'/product/modal.tpl.php';
    }


    public function extend($item)
    {
        if ($item['product_type'] != 1 || $item['product_payment'] != 'ticket') return;

        $db = db(config('db'));

        if ($_POST)
        {
            $db -> beginTrans();

            $data = array();
            foreach ($_POST['allot'] as $date => $allot)
            {
                if ($allot)
                    $data[] = array('item'=>$item['id'], 'date'=>$date, 'allot'=>$allot);
            }

            // close old price
            $rs = $db -> prepare("UPDATE `ptc_product_item_booking` SET `allot`=0 WHERE `item`=:item") -> execute(array(':item'=>$item['id']));
            if ($rs === false)
                json_return(null, 1, '保存失败，请重试');

            if ($data)
            {
                $data = array_values($data);
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_product_item_booking` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE `allot`=VALUES(`allot`);") -> execute($value); //var_dump($rs); exit;
                if (false == $rs)
                {
                    $db -> rollback();
                    json_return(null, 2, '数据保存失败，请重试~');
                }
            }

            if (false === $db -> commit())
                json_return(null, 9, '数据保存失败，请重试~');
            else
                json_return($rs);
        }

        $month = !empty($_GET['month']) ? $_GET['month'] : date('Y-m');

        $first = strtotime($month.'-1');
        $first_day  = date('N', $first);

        $start  = $first_day == 7 ? $first : $first - $first_day * 86400;
        $end    = $start + 41 * 86400;

        $_date = $db -> prepare("SELECT * FROM `ptc_product_item_booking` WHERE `item`=:item AND `allot`>0") -> execute(array(':item'=>$item['id']));

        $date = array();
        foreach ($_date as $v)
        {
            $date[$v['date']] = $v;
        }
        unset($_date);

        include dirname(__FILE__).'/product/booking.tpl.php';
    }


    /**
     * load & save price
     +-----------------------------------------
     * @access public
     * @param array $item
     * @return void
     */
    public function item_setprice($item)
    {
        if ($item['product_type'] != 1 || $item['product_payment'] != 'ticket') return;

        $db = db(config('db'));

        if ($_POST)
        {
            if (is_numeric($_POST['allot']))
            {
                $sign = '';
                $allot = (int)$_POST['allot'];
            }
            else
            {
                $sign = $_POST['allot'][0] == '+' ? '`allot`+' : '`allot`-';
                $allot = (int)substr($_POST['allot'], 1);
            }

            $data = array(
                ':id'         => $item['id'],
                ':price'      => (int)$_POST['price'],
                ':currency'   => $_POST['currency'],
                ':child'      => (int)$_POST['child'],
                ':baby'       => (int)$_POST['baby'],
                ':allot'      => $allot,
                ':min'        => (int)$_POST['min'],
                ':max'        => (int)$_POST['max'],
            );

            $db -> beginTrans();

            $sql = "UPDATE `ptc_product_item` AS i
                        LEFT JOIN `ptc_product` AS p ON p.`id` = i.`pid`
                    SET i.`price` = :price,
                        i.`currency` = :currency,
                        i.`child` = :child,
                        i.`baby`  = :baby,
                        i.`allot` = {$sign}:allot,
                        i.`min`   = :min,
                        i.`max`   = :max,
                        p.`minprice` = IF(p.`minprice` = 0 OR p.`minprice` > :price, :price, `minprice`),
                        p.`maxprice` = IF(p.`maxprice` < :price, :price, `maxprice`)
                    WHERE i.`id`=:id";
            $rs = $db -> prepare($sql) -> execute($data);
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 1, '数据保存失败，请重试~');
            }

            if ((int)$_POST['total'])
            {
                // 计算利润
                $profit = intval($_POST['total']) - intval($_POST['price']);

                $data = array('org'=>0, 'payment'=>'ticket', 'objtype'=>'room', 'objid'=>$item['id'], 'profit'=>$profit, 'type'=>'amount', 'updatetime'=>NOW);
                $datas = array($data);

                $orgs = $db -> prepare("SELECT * FROM `ptc_org`") -> execute();
                foreach ($orgs as $v)
                {
                    $data['org'] = $v['id'];
                    $datas[] = $data;
                }

                list($column, $sql, $value) = array_values(insert_array($datas));
                $rs = $db -> prepare("REPLACE INTO `ptc_org_profit` {$column} VALUES {$sql};") -> execute($value);
                if ($rs === false)
                {
                    $db -> rollback();
                    json_return(null, 2, '数据保存失败，请重试~');
                }
            }

            if (!$db -> commit())
                json_return(null, 9, '数据保存失败，请重试~');
            else
                json_return($rs);
        }

        $product = $db -> prepare("SELECT `status` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$item['pid']));

        $profit = $db -> prepare("SELECT `profit` FROM `ptc_org_profit` WHERE `org`=0 AND `payment`='ticket' AND `objtype`='room' AND `objid`=:id") -> execute(array(':id'=>$item['id']));
        $profit = $profit ? $profit[0]['profit'] : null;

        include dirname(__FILE__).'/product/price.tpl.php';
    }
    // price



}

new hotel_ticket_hook();
