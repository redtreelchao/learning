<?php
// hotel prepay product hook
class hotel_prepay_hook
{
    static $initialise = false;

    protected $_profit = "IF( fp3.`profit` IS NULL,
                            IF( fp2.`profit` IS NULL,
                                IF( fp1.`type` = 'amount', fp1.`profit`, p.`price` * fp1.`profit` / 100),
                                IF( fp2.`type` = 'amount', fp2.`profit`, p.`price` * fp2.`profit` / 100)
                            ),
                            IF( fp3.`type` = 'amount', fp3.`profit`, p.`price` * fp3.`profit` / 100)
                        )";

    protected $_timeout = 0;


    public function __construct()
    {
        if (self::$initialise) return true;
        self::$initialise = true;

        filter::add('api_hotel_search_condition',           array($this, 'api_search_condition'), 10, 6);
        filter::add('api_hotel_products',                   array($this, 'api_search'), 10, 6);

        filter::add('product_items',                        array($this, 'api_items'), 10, 7);

        filter::add('order_room',                           array($this, 'order_booking'), 10, 6);
        filter::add('order_pay',                            array($this, 'order_pay'), 10, 4);
        filter::add('order_apply_refund',                   array($this, 'order_apply_refund'), 10, 3);
        filter::add('order_complete',                       array($this, 'order_complete'), 10, 3);
        //filter::add('order_confirmation',                   array($this, 'order_confirmation'), 10, 2);

        action::add('order_manage_tpl_extend',              array($this, 'order_tpl_extend'), 10, 4);
        action::add('order_manage_tpl_operation',           array($this, 'order_tpl_operation'), 10, 3);
        action::add('order_manage_tpl_footer',              array($this, 'order_tpl_footer'), 10, 3);

        action::add('order_manage_save',                    array($this, 'order_operate'), 10, 1);


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

            if (!$api) $api = new hotel_prepay_api();
            return call_user_func_array(array($api, $method), $args);
        }

        else if (false !== strpos($method, 'order'))
        {
            $method = substr($method, 6);
            include_once dirname(__FILE__).'/order.php';

            if (!$order) $order = new hotel_prepay_order();
            return call_user_func_array(array($order, $method), $args);
        }
    }
    // __call









    // ---------------------- v -------------------- ITEM EDIT ------------------------ v -----------------

    /**
     * item
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
        if ($type != 1 || $payment != 'prepay') return;

        $db = db(config('db'));

        if ($itemid)
        {
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

            // 检查其他存在的子产品
            $sql = "SELECT p.*, h.`name` AS `hotel_name`, r.`name` AS `room`
                    FROM `ptc_product_item` AS p
                        LEFT JOIN `ptc_hotel` AS h ON p.`objpid` = h.`id`
                        LEFT JOIN `ptc_hotel_room_type` AS r ON p.`objid` = r.`id`
                    WHERE p.`pid`=:pid;";
            $_item = $db -> prepare($sql) -> execute(array(':pid' => $pid));
            if ($_item)
            {
                $data = array(
                    'objpid'     => $_item[0]['objpid'],
                    'hotel_name' => $_item[0]['hotel_name'],
                );

                $rooms = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel_room_type` WHERE `hotel`=:id") -> execute(array(':id'=>$data['objpid']));
                foreach ($rooms as $k => $v)
                    $rooms[$k]['name'] = roomname($v['name'], 2);
            }

        }

        include dirname(__FILE__).'/product/item.tpl.php';
    }
    // item




    private $save_data = array();



    /**
     * save item's data
     +-----------------------------------------
     * @access public
     * @param array $data
     * @param array $product
     * @param string $type
     * @return void
     */
    public function item_save($data, $product, $type)
    {
        if ($product['type'] != 1 || $product['payment'] != 'prepay' || $type != 'hotel') return $data;

        $data['objtype']    = 'room';
        $data['objid']      = (int)$_POST['room'];
        $data['objpid']     = (int)$_POST['hotel'];
        $data['ext']        = 0;
        $data['ext2']       = (string)$_POST['bed'];
        $data['start']      = 0;
        $data['end']        = 0;
        $data['data']       = array(
                'advance'       => (int)$_POST['advance'],
                'min'           => (int)$_POST['min'],
                'nation'        => (int)$_POST['nation'],
                'package'       => (int)$_POST['package'],
                'supply'        => empty($_POST['supply']) ? array() : array_filter($_POST['supply']),
        );

        $data['data'] = json_encode($data['data']);

        if (!$data['objpid']) json_return(null, 1, '未选择关联酒店');
        if (!$data['objid'])  json_return(null, 1, '未选择关联房型');

        $db = db(config('db'));
        $hotel = $db -> prepare("SELECT `city`,`name` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$data['objpid']));
        if (!$hotel) json_return(null, 1, '酒店不存在');

        $room = $db -> prepare("SELECT `name` FROM `ptc_hotel_room_type` WHERE `id`=:id") -> execute(array(':id'=>$data['objid']));
        if (!$room) json_return(null, 1, '酒店房型不存在');

        $data['target'] = $hotel[0]['city'];

        $this -> save_data = array('hotel_name' => $hotel[0]['name'], 'room_name' => roomname($room[0]['name'], 2));
        return $data;
    }
    // save




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
    // save back





}

new hotel_prepay_hook();

?>
