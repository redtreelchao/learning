<?php
/**
 * 酒店信息检索及报价
 +-----------------------------------------
 * @category
 * @package hotel
 * @author nolan.zhou
 * @version $Id$
 */
class product extends api
{

    // error message
    static public $error_msg = array(
        '701'   => '查询日期不正确',
    );



    /**
     * products' status list
     +-----------------------------------------
     * @access public
     * @param int $type
     * @param string $payment
     * @return void
     */
    public static function status($type=0, $payment='prepay', $status=null)
    {
        // type
        $where = "`type`=:type";
        $condition[':type'] = (int)$type;

        // payment
        $where .= " AND `payment`=:payment";
        $condition[':payment'] = (string)$payment;

        // status
        if ($status !== null)
        {
            $where .= " AND `status`=:status";
            $condition[':status'] = (int)$status;
        }

        $db = db(config('db'));
        $result = $db -> prepare("SELECT `id`,`name`,`status`,`start`,`end` FROM `ptc_product` WHERE {$where};") -> execute($condition);
        return $result;
    }



    /**
     * Search Product
     +-----------------------------------------
     * @access public
     * @param string $keyword
     * @param int $source
     * @param int $target
     * @param string $checkin
     * @param string $checkout
     * @param mixed $min_price
     * @param mixed $max_price
     * @param int $limit
     * @return void
     */
    public static function search($keyword='', $type=0, $payment='ticket', $source=0, $target=0, $checkin='', $checkout='', $min_price=null, $max_price=null, $limit=15)
    {
        //$where = 'd.`org`=:org';
        $where = '1=1';
        $condition = array();

        // keyword
        if ($keyword)
        {
            if (is_numeric($keyword))
            {
                $where .= " AND d.`id` = :id";
                $condition[':id'] = (int)$keyword;
            }
            else
            {
                $where .= " AND (d.`name` LIKE :keyword OR di.`name` LIKE :keyword OR t.name = :district OR s.name = :district)";
                $condition[':keyword'] = "%{$keyword}%";
                $condition[':district'] = "$keyword";
            }
        }

        // type
        $where .= " AND d.`type`=:type";
        $condition[':type'] = (int)$type;

        // payment
        $where .= " AND d.`payment`=:payment";
        $condition[':payment'] = (string)$payment;

        // source
        if ($source)
        {
            $where .= " AND (di.`source`=:source OR d.`type`!=1)";
            $condition[':source'] = $source;
        }

        // target
        if ($target)
        {
            $where .= " AND di.`target`=:target";
            $condition[':target'] = $target;
        }

        // checkdate
        if ($checkin && $checkout)
        {
            $checkin = strtotime($checkin);
            $checkout = strtotime($checkout);
            if (!$checkin || !$checkout)
            {
                self::$error = '701';
                return false;
            }

            if ($payment != 'prepay')
            {
                $where .= ' AND di.`start` <= :checkin AND di.`end` > :checkout';
                $condition[':checkin'] = $checkin;
                $condition[':checkout'] = $checkout;
            }
        }

        // price
        if ($min_price)
        {
            $where .= ' AND d.`maxprice` + IF(fd2.`profit` IS NULL, fd1.`profit`, fd2.`profit`) >= :min';
            $condition[':min'] = $min_price;
        }

        if ($max_price)
        {
            $where .= ' AND d.`minprice` + IF(fd2.`profit` IS NULL, fd1.`profit`, fd2.`profit`) <= :max';
            $condition[':max'] = $max_price;
        }

        $db = db(config('db'));

        $sql = "SELECT COUNT(*) AS `c` FROM (
                    SELECT d.`id` FROM `ptc_product_item` AS di
                        LEFT JOIN `ptc_product` AS d ON di.`pid` = d.`id` AND d.`status`!=0
                        LEFT JOIN `ptc_district` AS t ON di.`target` = t.`id`
                        LEFT JOIN `ptc_district` AS s ON di.`source` = s.`id`
                    WHERE {$where} GROUP BY d.`id`
                ) AS `s`";
        $count = $db -> prepare($sql) -> execute($condition);
        $count = $count[0]['c'];

        $protypes = array(
            1 => 'hotel',
            2 => 'product2',
            3 => 'flight',
            4 => 'product4',
            5 => 'view',
            7 => 'goods',
        );
        $condition[':protype'] = $protypes[$type];

        // limit builder
        $start = (self::$page - 1) * $limit;
        $limit = "{$start},{$limit}";

        $condition[':org'] = api::$org;

        $sql = "SELECT d.`type`, d.`payment`, d.`id`, d.`name`, d.`intro`, d.`rule`, d.`refund`, d.`org`, d.`minprice`, d.`maxprice`, d.`status`, d.`updatetime`,
                        fd1.`profit` AS `profit1`, fd1.`child` AS `profit_child1`, fd1.`baby` AS `profit_baby1`, fd1.`type` AS `type1`,
                        fd2.`profit` AS `profit2`, fd2.`child` AS `profit_child2`, fd2.`baby` AS `profit_baby2`, fd2.`type` AS `type2`
                FROM `ptc_product_item` AS di
                    LEFT JOIN `ptc_product` AS d ON di.`pid` = d.`id` AND d.`status`!=0
                    LEFT JOIN `ptc_district` AS t ON di.`target` = t.`id`
                    LEFT JOIN `ptc_district` AS s ON di.`source` = s.`id`
                        LEFT JOIN `ptc_org_profit` AS fd1 ON fd1.`org` = :org AND fd1.`payment` = :payment AND fd1.`objtype` = :protype AND fd1.`objid` = 0
                        LEFT JOIN `ptc_org_profit` AS fd2 ON fd2.`org` = :org AND fd2.`payment` = :payment AND fd2.`objtype` = :protype AND fd2.`objid` = d.`id`
                WHERE {$where}
                GROUP BY d.`id`
                LIMIT {$limit}";
        $products = $db -> prepare($sql) -> execute($condition);

        foreach ($products as $key => $product)
        {
            if ($product['profit2'])
            {
                $protype      = $product['type2'];
                $profit       = $product['profit2'];
                $profit_child = $product['profit_child2'];
                $profit_baby  = $product['profit_baby2'];
            }
            else
            {
                $protype      = $product['type1'];
                $profit       = $product['profit1'];
                $profit_child = $product['profit_child1'];
                $profit_baby  = $product['profit_baby1'];
            }

            unset($product['type1'], $product['type2'], $product['profit1'], $product['profit2'], $product['profit_child1'], $product['profit_child2'], $product['profit_baby1'], $product['profit_baby2']);

            include_once PT_PATH.'hook/hook.php';
            $product = filter::apply('product_items', $product, $protype, $profit, $profit_child, $profit_baby, $checkin, $checkout);
            $products[$key] = $product;
        }

        return array('count' => $count, 'products' => $products);
    }




    static public function calendar($item)
    {
        $db = db(config('db'));

        $_date = $db -> prepare("SELECT `item`,`date`,`allot`-`used` AS `allot` FROM `ptc_product_item_booking` WHERE `item`=:item AND `allot`>0") -> execute(array(':item'=>$item));

        $date = array();
        foreach ($_date as $v)
        {
            if ($v['allot'] > 0)
                $date[$v['date']] = $v['allot'];
        }
        unset($_date);
        return $date;
    }


}
