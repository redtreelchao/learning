<?php
// hotel ticket product hook of api
class hotel_auto_prepay_api extends hotel_auto_prepay_hook
{

    /**
     * add condition for hotel search
     +-----------------------------------------
     * @access public
     * @return void
     */
    public function search_condition($sql, $types, $checkin, $checkout, $min, $max)
    {
        if (in_array('auto', $types))
        {
            $sql[0] .= "LEFT JOIN `ptc_product_item` AS di ON di.`objtype`='room' AND di.`objpid` = h.`id` "
                    .($checkin && $checkout ? 'AND di.`start` <= :checkin AND di.`end` > :checkout ' : '')
                    ."LEFT JOIN `ptc_product` AS d ON di.`pid` = d.`id` AND d.`type`=2 AND d.`payment`='prepay' AND d.`status` = 1";

            $condition = array();
            $_where = 'd.`id` IS NOT NULL';

            if ($checkin && $checkout)
            {
                $condition[':checkin'] = $checkin;
                $condition[':checkout'] = $checkout;
            }

            // if search price . load profit
            if ($min !== null)
            {
                $_where .= " AND d.`maxprice` >= :min";
                $condition[':min'] = $min;
            }

            if ($max !== null)
            {
                $_where .= " AND d.`minprice` <= :max";
                $condition[':max'] = $max;
            }

            $sql[1]['auto'] = $_where;
            $sql[2] = array_merge($sql[2], $condition);
        }

        return $sql;
    }
    // search_condition




    /**
     * search products for hotel
     +-----------------------------------------
     * @access public
     * @return void
     */
    public function search($hotel, $types, $checkin, $checkout, $min, $max)
    {
        if (!in_array('auto', $types)) return $hotel;

        $condition = array(':org'=>api::$org, ':type'=>'room', ':id'=>$hotel['id']);

        // date
        if ($checkin && $checkout)
        {
            $condition[':checkin'] = $checkin;
            $condition[':checkout'] = $checkout;
        }

        // price
        if ($min !== null)
        {
            $condition[':min'] = $min;
        }

        if ($max !== null)
        {
            $condition[':max'] = $max;
        }

        $sql = "SELECT d.`type`, d.`payment`, d.`id`, d.`name`, d.`intro`, d.`rule`, d.`minprice`, d.`maxprice`, d.`updatetime`,
                        fd1.`profit` AS `profit1`, fd1.`child` AS `profit_child1`, fd1.`baby` AS `profit_baby1`, fd1.`type` AS `type1`,
                        fd2.`profit` AS `profit2`, fd2.`child` AS `profit_child2`, fd2.`baby` AS `profit_baby2`, fd2.`type` AS `type2`
                FROM `ptc_product_item` AS di
                    LEFT JOIN `ptc_product` AS d ON di.`pid` = d.`id` AND d.`status` = 1
                        LEFT JOIN `ptc_org_profit` AS fd1 ON fd1.`org` = :org AND fd1.`payment` = 'prepay' AND fd1.`objtype` = 'product2' AND fd1.`objid` = 0
                        LEFT JOIN `ptc_org_profit` AS fd2 ON fd2.`org` = :org AND fd2.`payment` = 'prepay' AND fd2.`objtype` = 'product2' AND fd2.`objid` = d.`id`
                WHERE d.`type`=2 AND di.`objtype`=:type AND di.`objpid` = :id"
                .($checkin && $checkout ? ' AND di.`start` <= :checkin AND di.`end` > :checkout' : '')
                .($min !== null ? ' AND d.`maxprice` >= :min' :'')
                .($max !== null ? ' AND d.`minprice` <= :max' :'')
                ." GROUP BY d.`id`";

        $db = db(config('db'));
        $_products = $db -> prepare($sql) -> execute($condition);

        foreach ($_products as $key => $product)
        {
            if ($product['profit2'])
            {
                $type         = $product['type2'];
                $profit       = $product['profit2'];
                $profit_child = $product['profit_child2'];
                $profit_baby  = $product['profit_baby2'];
            }
            else
            {
                $type         = $product['type1'];
                $profit       = $product['profit1'];
                $profit_child = $product['profit_child1'];
                $profit_baby  = $product['profit_baby1'];
            }

            unset($product['type1'], $product['type2'], $product['profit1'], $product['profit2'], $product['profit_child1'], $product['profit_child2'], $product['profit_baby1'], $product['profit_baby2']);

            $product = $this -> items($product, $type, $profit, $profit_child, $profit_baby);
            unset($product['payment']);
            $product['type'] = 'auto';
            $hotel['products'][] = $product;
        }

        return $hotel;
    }
    // search






    /**
     * load product's items
     +-----------------------------------------
     * @access public
     * @param array $product
     * @param string $protype
     * @param int $profit
     * @return void
     */
    public function items($product, $protype='amount', $profit=0, $profit_child=0, $profit_baby=0)
    {
        if ($product['type'] != 2 || $product['payment'] != 'prepay') return $product;

        // type = 1 only hotel + auto product
        $sql = "SELECT a.`id`, a.`name`, a.`objtype` AS `type`, a.`source`, a.`target`, a.`objpid`, a.`objid`, a.`ext`, a.`ext2`, a.`intro`, a.`childstd`, a.`babystd`, a.`default`,
                       b.`profit` AS `profit`, b.`child` AS `profit_child`, b.`baby` AS `profit_baby`, b.`type` AS `protype`
                FROM `ptc_product_item` AS a
                    LEFT JOIN `ptc_org_profit` AS b ON b.`org` = :org AND b.`payment` = 'prepay' AND b.`objtype` = 'item' AND b.`objid` = a.`id`
                WHERE a.`pid`=:pid
                ORDER BY a.`objtype` DESC, a.`seq` ASC;";
        $db = db(config('db'));
        $items = $db -> prepare($sql) -> execute(array(':pid'=>$product['id'], ':org'=>api::$org));

        $hmax = $fmax = 0;
        $hmin = $fmin = 9999999;
        foreach ($items as $k => $v)
        {
            $sql = "SELECT a.`id` AS `city`, a.`name` AS `cityname`, a.`pid` AS `country`, b.`name` AS `countryname`, a.`lng`, a.`lat`
                        FROM `ptc_district` AS a
                            LEFT JOIN `ptc_district` AS b ON a.pid = b.id
                        WHERE a.`id`=:id";

            if ($v['source'])
            {
                $source = $db -> prepare($sql) -> execute(array(':id' => $v['source']));
                $items[$k]['source'] = $source[0];
            }
            else
            {
                $items[$k]['source'] = null;
            }

            $target = $db -> prepare($sql) -> execute(array(':id' => $v['target']));
            $items[$k]['target'] = $target[0];

            if ($v['type'] == 'room')
            {
                $items[$k]['hotel']  = $v['objpid'];
                $items[$k]['room']   = $v['objid'];
                $items[$k]['night']  = $v['ext'];

                $sql = "SELECT p.`key` AS `code`, p.`date`, p.`price`, p.`allot`-`sold` AS `allot`, p.`filled`, p.`standby`
                        FROM `ptc_hotel_price_date` AS p
                        WHERE p.`supply`='EBK' AND p.`supplyid`=:sup AND p.`hotel`=:hotel AND p.`room`=:room AND p.`close`=0";
                $condition = array(':sup'=>$product['id'], ':hotel'=>$v['objpid'], ':room'=>$v['id']);

                $date = $db -> prepare($sql) -> execute($condition);
            }
            else
            {
                $items[$k]['auto']  = $v['objpid'];

                $sql = "SELECT p.`key` AS `code`, p.`date`, p.`price`, p.`child`, p.`baby`, p.`allot`-p.`sold` AS `allot`, p.`filled`
                        FROM `ptc_auto_price_date` AS p
                        WHERE `auto`=:auto AND `close`=0";
                $condition = array(':auto'=>$v['objpid']);

                $date = $db -> prepare($sql) -> execute($condition);
            }

            if ($v['protype'])
            {
                $_protype = $v['protype'];
                $_profit = $v['profit'];
                $_profit_child = $v['profit_child'];
                $_profit_baby = $v['profit_baby'];
            }
            else
            {
                $_protype = $protype;
                $_profit = $profit;
                $_profit_child = $profit_child;
                $_profit_baby = $profit_baby;
            }

            unset($items[$k]['protype'], $items[$k]['profit'], $items[$k]['profit_child'], $items[$k]['profit_baby']);

            $items[$k]['dates'] = array();
            foreach ($date as $d)
            {
                $d['code'] = key_encryption($d['code'].'_auto'.$product['id'].'.'.$v['id'].'_product2');
                $d['date'] = date('Y-m-d', $d['date']);
                $d['price'] = $d['price'] + round($_protype == 'amount' ? $_profit : ($d['price'] * $_profit / 100));
                $d['allot'] = $d['filled'] || $d['allot'] < 0 ? 0 : $d['allot'];
                if (isset($d['standby']))
                {
                    $standby = json_decode($d['standby'], true);
                    $d['child'] = $standby['child'] ? $standby['child'] + round($_protype == 'amount' ? $_profit_child : ($standby['child'] * $_profit_child / 100)) : 0;
                    $d['baby']  = $standby['baby'] ? $standby['baby'] + round($_protype == 'amount' ? $_profit_baby : ($standby['baby'] * $_profit_baby / 100)) : 0;
                }
                else
                {
                    $d['child'] = $d['child'] ? $d['child'] + round($_protype == 'amount' ? $_profit_child : ($d['child'] * $_profit_child / 100)) : 0;
                    $d['baby']  = $d['baby'] ? $d['baby'] + round($_protype == 'amount' ? $_profit_baby : ($d['baby'] * $_profit_baby / 100)) : 0;
                }
                unset($d['uncombine'], $d['combine'], $d['standby']);
                $items[$k]['dates'][] = $d;

                if (!$d['allot']) continue;

                if ($v['type'] == 'room')
                {
                    $hmin = $hmin > $d['price'] ? $d['price'] : $hmin;
                    $hmax = $hmax < $d['price'] ? $d['price'] : $hmax;
                }
                else
                {
                    $fmin = $fmin > $d['price'] ? $d['price'] : $fmin;
                    $fmax = $fmax < $d['price'] ? $d['price'] : $fmax;
                }
            }
        }

        $product['maxprice'] = $hmax + $fmax;
        $product['minprice'] = $hmin + $fmin;
        $product['items'] = $items;
        return $product;
    }
    // items



}
