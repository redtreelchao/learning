<?php
// hotel ticket product hook
class hotel_auto_prepay_hook
{
    static $initialise = false;

    protected $_profit = "IF(fd3.`profit` IS NULL,
                            IF( fd2.`profit` IS NULL,
                                IF( fd1.`type` = 'amount', fd1.`profit`, p.`price` * fd1.`profit` / 100),
                                IF( fd2.`type` = 'amount', fd2.`profit`, p.`price` * fd2.`profit` / 100)
                            ),
                            IF( fd3.`type` = 'amount', fd3.`profit`, p.`price` * fd3.`profit` / 100)
                         )";

    protected $_timeout = 1200;



    public function __construct()
    {
        if (self::$initialise) return true;
        self::$initialise = true;

        filter::add('api_hotel_search_condition',   array($this, 'api_search_condition'), 10, 6);
        filter::add('api_hotel_products',           array($this, 'api_search'), 10, 6);

        filter::add('product_items',                array($this, 'api_items'), 10, 6);

        filter::add('order_room',                   array($this, 'order_book_room'), 10, 6);
        filter::add('order_auto',                   array($this, 'order_book_auto'), 10, 5);
        filter::add('order_pay',                    array($this, 'order_pay'), 10, 4);
        filter::add('order_apply_refund',           array($this, 'order_apply_refund'), 10, 3);
        filter::add('order_complete',               array($this, 'order_complete'), 10, 3);
        //filter::add('order_confirmation',           array($this, 'order_confirmation'), 10, 2);

        action::add('order_manage_tpl_extend',      array($this, 'order_tpl_extend'), 10, 4);
        action::add('order_manage_tpl_operation',   array($this, 'order_tpl_operation'), 10, 3);
        action::add('order_manage_tpl_footer',      array($this, 'order_tpl_footer'), 10, 3);

        action::add('order_manage_save',            array($this, 'order_operate'), 10, 1);


        action::add('product_item_manage_tpl',              array($this, 'item_tpl'), 10, 5);
        filter::add('product_item_manage_save',             array($this, 'item_save'), 10, 3);
        filter::add('product_item_manage_save_callback',    array($this, 'item_save_callback'), 10, 3);

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

            if (!$api) $api = new hotel_auto_prepay_api();
            return call_user_func_array(array($api, $method), $args);
        }

        else if (false !== strpos($method, 'order'))
        {
            $method = substr($method, 6);
            include_once dirname(__FILE__).'/order.php';

            if (!$order) $order = new hotel_auto_prepay_order();
            return call_user_func_array(array($order, $method), $args);
        }
    }
    // __call






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
        if ($type != 2 || $payment != 'prepay') return;

        $db = db(config('db'));

        if ($itemid)
        {
            switch ($itemtype)
            {
                case 'room':
                    $sql = "SELECT p.*, h.`name` AS `hotel_name`, r.`name` AS `room`
                            FROM `ptc_product_item` AS p
                                LEFT JOIN `ptc_hotel` AS h ON p.`objpid` = h.`id`
                                LEFT JOIN `ptc_hotel_room_type` AS r ON p.`objid` = r.`id`
                            WHERE p.`id`=:id;";
                    $data = $db -> prepare($sql) -> execute(array(':id' => $itemid));
                    if (!$data) break;

                    $data = $data[0];
                    $extend = $data['data'] ? json_decode($data['data'], true) : null;

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
                break;

                case 'auto':
                    $sql = "SELECT p.*, h.`code` AS `auto_name`
                            FROM `ptc_product_item` AS p
                                LEFT JOIN `ptc_auto` AS h ON p.`objpid` = h.`id`
                            WHERE p.`id`=:id;";
                    $data = $db -> prepare($sql) -> execute(array(':id' => $itemid));
                    if (!$data) break;

                    $data = $data[0];
                    $extend = $data['data'] ? json_decode($data['data'], true) : null;
                break;
            }

            if (!$data)
                echo "<div class=\"alert alert-warning\" role=\"alert\">产品内容丢失，请联系开发人员！</div>";
        }
        else
        {
            $data = null;

            switch ($itemtype)
            {
                case 'room':
                    $extend = null;
                    $rooms = null;
                break;

                case 'auto':
                    $extend = null;
                break;
            }
        }

        switch ($itemtype)
        {
            case 'room':
            break;

            case 'auto':
                $sql = "SELECT a.`id`, a.`code`, a.`company` FROM `ptc_auto` AS a ORDER BY a.`id` DESC";
                $autos = $db -> prepare($sql) -> execute();
            break;
        }
        include dirname(__FILE__).'/product/item_'.$itemtype.'.tpl.php';
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
    public function item_save($data, $product, $type)
    {
        if ($product['type'] != 2 || $product['payment'] != 'prepay' || !in_array($type, array('hotel', 'auto'))) return $data;

        switch ($type)
        {
            // --------------- v --------------- hotel --------------- v ---------------
            case 'hotel':
                $data['objtype']    = 'room';
                $data['objid']      = (int)$_POST['room'];
                $data['objpid']     = (int)$_POST['hotel'];
                $data['ext']        = (int)$_POST['ext'];
                $data['ext2']       = (string)$_POST['bed'];
                $data['start']      = (int)strtotime($_POST['start']);
                $data['end']        = (int)strtotime($_POST['end']);
                $data['data']       = array(
                        'advance'       => (int)$_POST['advance'],
                        'min'           => (int)$_POST['min'],
                        'nation'        => (int)$_POST['nation'],
                        'package'       => (int)$_POST['package'],
                        'net'           => intval($_POST['wifi'].$_POST['net']),
                        'addbf'         => (int)$_POST['addbf'],
                        'addbe'         => (int)$_POST['addbe'],
                );

                if (!$data['objpid']) json_return(null, 1, '未选择关联酒店');
                if (!$data['objid'])  json_return(null, 1, '未选择关联房型');
                if (!$data['ext'])    json_return(null, 1, '价格包含晚数必填');              // remove future

                $db = db(config('db'));
                $hotel = $db -> prepare("SELECT `city`,`name` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$data['objpid']));
                if (!$hotel) json_return(null, 1, '酒店不存在');

                $room = $db -> prepare("SELECT `name` FROM `ptc_hotel_room_type` WHERE `id`=:id") -> execute(array(':id'=>$data['objid']));
                if (!$room) json_return(null, 1, '酒店房型不存在');

                $data['target'] = $hotel[0]['city'];

                $this -> save_data = array('hotel_name' => $hotel[0]['name'], 'room_name' => roomname($room[0]['name'], $data['ext2']));
            break;


            // --------------- v --------------- auto --------------- v ---------------
            case 'auto':
                $data['objtype']     = 'auto';
                $data['objpid']      = (int)$_POST['auto'];
                $data['objid']       = 0;
                $data['ext']         = 0;
                $data['ext2']        = '';
                $data['data']       = array();

                if (!$data['objpid']) json_return(null, 1, '未选择车辆');

                if (!$data['objid'])  $data['ext'] = 0;

                $sql = "SELECT a.`code` FROM `ptc_auto` AS a WHERE a.`id`=:id";

                $db = db(config('db'));
                $auto = $db -> prepare($sql) -> execute(array(':id' => $data['objpid']));
                if (!$auto) json_return(null, 1, '车辆不存在');

                $data['source'] = 0;
                $data['target'] = 0;

                $this -> save_data = array('auto' => $auto[0]['code']);
            break;
        }

        $data['data'] = json_encode($data['data']);
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
    }
    // item_save_callback




    /**
     * load & save price
     +-----------------------------------------
     * @access public
     * @param array $item
     * @return void
     */
    public function item_setprice($item)
    {
        if ($item['product_type'] != 2 || $item['product_payment'] != 'prepay') return;

        $db = db(config('db'));

        if ($_POST)
        {
            $db -> beginTrans();

            switch ($item['objtype'])
            {
                case 'room':
                    $data = $this -> _format_room_price($item);

                    // close old price
                    $where = "`supply`='EBK' AND `supplyid`=:sup AND `payment`=3 AND `hotel`=:hotel AND `room`=:room AND `date`>=:start AND `date`<=:end";
                    $condition = array(':sup'=>$item['pid'], ':hotel'=>$item['objpid'], ':room'=>$item['id'], ':start'=>$_POST['start'], ':end'=>$_POST['end']);
                    $rs = $db -> prepare("UPDATE `ptc_hotel_price_date` SET `close`=1, `price`=0 WHERE {$where}") -> execute($condition);
                    if ($rs === false)
                        json_return(null, 6, '保存失败，请重试');

                    if ($data)
                    {
                        $data = array_values($data);
                        list($column, $sql, $value) = array_values(insert_array($data));

                        $_columns = update_column(array_keys($data[0]));
                        $rs = $db -> prepare("INSERT INTO `ptc_hotel_price_date` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE {$_columns};") -> execute($value); //var_dump($rs); exit;
                        if (false == $rs)
                        {
                            $db -> rollback();
                            json_return(null, 7, '数据保存失败，请重试~');
                        }
                    }
                break;

                case 'auto':
                    $data = $this -> _format_auto_price($item);

                    // close old price
                    $where = "`auto`=:auto AND `date`>=:start AND `date`<=:end";
                    $condition = array(':auto'=>$item['objpid'], ':start'=>$_POST['start'], ':end'=>$_POST['end']);
                    $rs = $db -> prepare("UPDATE `ptc_auto_price_date` SET `close`=1, `price`=0 WHERE {$where}") -> execute($condition);
                    if ($rs === false)
                        json_return(null, 6, '保存失败，请重试');

                    if ($data)
                    {
                        $data = array_values($data);
                        list($column, $sql, $value) = array_values(insert_array($data));

                        $_columns = update_column(array_keys($data[0]));
                        $rs = $db -> prepare("INSERT INTO `ptc_auto_price_date` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE {$_columns};") -> execute($value); //var_dump($rs); exit;
                        if (false == $rs)
                        {
                            $db -> rollback();
                            json_return(null, 7, '数据保存失败，请重试~');
                        }
                    }
                break;
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

        switch ($item['objtype'])
        {
            case 'room':
                $_date = $db -> prepare("SELECT `key`,`date`,`price`,`breakfast`,`allot`,`sold`,`filled`,`standby`,`close` FROM `ptc_hotel_price_date` WHERE `supply`='EBK' AND `supplyid`=:sup AND `hotel`=:hotel AND `room`=:room AND `date`>=:start AND `date`<=:end AND `close`=0")
                            -> execute(array(':sup'=>$item['pid'], ':hotel'=>$item['objpid'], ':room'=>$item['id'], ':start'=>$start, ':end'=>$end));
                break;

            case 'auto':
                $_date = $db -> prepare("SELECT `key`,`date`,`price`,`child`,`baby`,`allot`,`sold`,`filled`,`close` FROM `ptc_auto_price_date` WHERE `auto`=:auto AND `date`>=:start AND `date`<=:end AND `close`=0")
                            -> execute(array(':auto'=>$item['objpid'], ':start'=>$start, ':end'=>$end));
                break;
        }

        $date = array();
        foreach ($_date as $v)
        {
            $date[$v['date']] = $v;
        }
        unset($_date);

        include dirname(__FILE__).'/product/price_'.$item['objtype'].'.tpl.php';
    }
    // price





    // _format_room_price
    private function _format_room_price($item)
    {
        $data = array();

        $extend  = $item['data'] ? json_decode($item['data'], true) : null;

        $_temp = array(
            'payment'   => 3,
            'hotel'     => $item['objpid'],
            'room'      => $item['id'],
            'bed'       => $item['ext2'],
            'roomtype'  => $item['objid'],
            'nation'    => (int)$extend['nation'],
            'package'   => (int)$extend['package'],
            'night'     => $item['ext'],
            'currency'  => 1,
            'rebate'    => 0,
            'supply'    => 'EBK',
            'supplyid'  => $item['pid'],
            'start'     => $item['start'],
            'end'       => $item['end'],
            'min'       => (int)$extend['min'],
            'advance'   => (int)$extend['advance'],
            'net'       => (int)$extend['net'],
            'addbf'     => (int)$extend['addbf'],
            'addbe'     => (int)$extend['addbe'],
        );

        foreach ($_POST['price'] as $date => $price)
        {
            if (!$price) continue;
            $allot  = (int)$_POST['allot'][$date];
            $breakfast = (int)$_POST['breakfast'][$date];
            $child  = (int)$_POST['child'][$date];
            $baby   = (int)$_POST['baby'][$date];

            // KEY : date(6), room(6), nation(3), pay(1), booktime(3), breakfast(1), package(3), supply(3), supply no.(-)
            $key =  date('ymd', $date)
                    .str_pad(strtoupper(dechex($_temp['room'])), 6, 0, STR_PAD_LEFT)
                    .str_pad(strtoupper(dechex($_temp['nation'])), 3, 0, STR_PAD_LEFT)
                    .'3'
                    .int2chr(abs($_temp['end'] - $_temp['start'])/86400)
                    .int2chr($_temp['advance'])
                    .int2chr($_temp['min'])
                    .int2chr($breakfast)
                    .str_pad(strtoupper(dechex($_temp['package'])), 3, 0, STR_PAD_LEFT)
                    .'EBK'
                    .$item['pid'];

            $data[$key] = array_merge($_temp, array(
                    'key'       => $key,
                    'uncombine' => substr($key, 6, 13).substr($key, 23),
                    'combine'   => substr($key, 19, 4),
                    'date'      => $date,
                    'price'     => $price,
                    'breakfast' => $breakfast,
                    'filled'    => $allot < 0 ? 1 : 0,
                    'allot'     => $allot < 0 ? 0 : $allot,
                    'cutoff'    => 0,
                    'standby'   => json_encode(array('child'=>$child, 'baby'=>$baby)),
                    'update'    => NOW,
                    'close'     => 0,
                ));
        }

        return $data;
    }
    // _format_room_price




    // _format_auto_price
    private function _format_auto_price($item)
    {
        $data = array();

        $extend  = $item['data'] ? json_decode($item['data'], true) : null;

        $auto = $item['objpid'];

        foreach ($_POST['price'] as $date => $price)
        {
            if (!$price) continue;
            $allot = (int)$_POST['allot'][$date];

            // KEY : date(6), auto(6), pay(1), booktime(2), supply(3), supply no.(-)
            $key   = date('ymd', $date)
                    .str_pad(strtoupper(dechex($auto)), 6, 0, STR_PAD_LEFT)
                    .'200'
                    .'EBK';

            $data[$key] = array(
                'key'       => $key,
                'uncombine' => substr($key, 6, 10).substr($key, 23),
                'combine'   => substr($key, 16, 4),
                'payment'   => 3,
                'auto'      => $auto,
                'package'   => 0,
                'date'      => $date,
                'price'     => $price,
                'currency'  => 1,
                'rebate'    => 0,
                'supply'    => 'EBK',
                'supplyid'  => '',
                'child'     => (int)$_POST['child'][$date],
                'baby'      => (int)$_POST['baby'][$date],
                'filled'    => $allot < 0 ? 1 : 0,
                'allot'     => $allot < 0 ? 0 : $allot,
                'cutoff'    => 0,
                'update'    => NOW,
                'close'     => 0,
            );
        }

        return $data;
    }
    // _format_auto_price



}

new hotel_auto_prepay_hook();

?>