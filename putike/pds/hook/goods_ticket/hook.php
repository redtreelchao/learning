<?php
// hotel ticket product hook
class goods_ticket_hook
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

        filter::add('product_items',                        array($this, 'api_items'), 10, 1);

        filter::add('order_goods',                          array($this, 'order_booking'), 10, 6);
        filter::add('order_pay',                            array($this, 'order_pay'), 10, 4);
        filter::add('order_apply_refund',                   array($this, 'order_apply_refund'), 10, 3);
        filter::add('order_complete',                       array($this, 'order_complete'), 10, 3);

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

            if (!$api) $api = new goods_ticket_api();
            return call_user_func_array(array($api, $method), $args);
        }

        else if (false !== strpos($method, 'order'))
        {
            $method = substr($method, 6);
            include_once dirname(__FILE__).'/order.php';

            if (!$order) $order = new goods_ticket_order();
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
        if ($type != 7 || $payment != 'ticket') return;

        if ($itemid)
        {
            $db = db(config('db'));

            $sql = "SELECT p.*, g.`name` AS `goods_name`
                    FROM `ptc_product_item` AS p
                        LEFT JOIN `ptc_goods` AS g ON p.`objpid` = g.`id`
                    WHERE p.`id`=:id;";
            $data = $db -> prepare($sql) -> execute(array(':id' => $itemid));
            if ($data)
            {
                $data = $data[0];
                $extend = $data['data'] ? json_decode($data['data'], true) : null;
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
    public function item_save($data, $product, $type='goods')
    {
        if ($product['type'] != 7 || $product['payment'] != 'ticket' || $type != 'goods') return $data;

        $data['objtype']    = 'goods';
        $data['objid']      = 0;
        $data['objpid']     = (int)$_POST['goods'];
        $data['data']       = json_encode(null);

        if (!$data['objpid']) json_return(null, 1, '未选择关联商品');

        $db = db(config('db'));

        // get hotel and room information
        $goods = $db -> prepare("SELECT `name` FROM `ptc_goods` WHERE `id`=:id") -> execute(array(':id'=>$data['objpid']));
        if (!$goods) json_return(null, 1, '商品不存在');

        $this -> save_data = array('goods_name' => $goods[0]['name']);

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
        if ($item['product_type'] != 7 || $item['product_payment'] != 'ticket') return;

        if ($_POST)
        {
            $db = db(config('db'));

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

            if (false === $rs)
                json_return(null, 9, '数据保存失败，请重试~');
            else
                json_return($rs);
        }

        include dirname(__FILE__).'/product/price.tpl.php';
    }
    // price



}

new goods_ticket_hook();

