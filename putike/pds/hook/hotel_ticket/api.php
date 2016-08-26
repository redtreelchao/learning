<?php
// hotel ticket product hook of api
class hotel_ticket_api extends hotel_ticket_hook
{

    /**
     * add condition for hotel search
     +-----------------------------------------
     * @access public
     * @return void
     */
    public function search_condition($sql, $types, $checkin, $checkout, $min, $max)
    {
        if (in_array('ticket', $types))
        {
            $sql[0] .= "LEFT JOIN `ptc_product_item` AS i ON i.`objtype` = 'room' AND i.`objpid` = h.`id`".($checkin && $checkout ? ' AND i.`start` <= :checkin AND i.`end` > :checkout' : '')." AND i.`link`=0
                            LEFT JOIN `ptc_product` AS t ON i.`pid` = t.`id` AND t.`type`=1 AND t.`payment`='ticket' AND t.`status` = 1 AND (t.`start` = 0 OR t.`start` < :now) AND (t.`end` = 0 OR t.`end` > :now)
                                LEFT JOIN `ptc_org_profit` AS fi1 ON fi1.`org` = :org AND fi1.`payment` = 'ticket' AND fi1.`objtype` = 'hotel' AND fi1.`objid` = 0
                                LEFT JOIN `ptc_org_profit` AS fi2 ON fi2.`org` = :org AND fi2.`payment` = 'ticket' AND fi2.`objtype` = 'hotel' AND fi2.`objid` = i.`pid`
                                LEFT JOIN `ptc_org_profit` AS fi3 ON fi3.`org` = :org AND fi3.`payment` = 'ticket' AND fi3.`objtype` = 'room'  AND fi3.`objid` = i.`id`";

            $condition = array();
            $condition[':now'] = NOW;
            $condition[':org'] = api::$org;
            $_where = 't.`id` IS NOT NULL';

            if ($checkin && $checkout)
            {
                $condition[':checkin'] = $checkin;
                $condition[':checkout'] = $checkout;
            }

            // if search price . load profit
            if ($min !== null)
            {
                $_where .= " AND i.`price` + {$this -> _profit} >= :min";
                $condition[':min'] = $min;
            }

            if ($max !== null)
            {
                $_where .= " AND i.`price` + {$this -> _profit} <= :max";
                $condition[':max'] = $max;
            }

            $sql[1]['ticket'] = $_where;
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
        if (!in_array('ticket', $types)) return $hotel;

        $sql = "SELECT t.`type`, t.`payment`, t.`id`, t.`name`, t.`start`, t.`end`, t.`intro`, t.`rule`
                FROM `ptc_product` AS t
                    LEFT JOIN `ptc_product_item` AS i ON i.`pid`= t.`id` AND i.`link`=0
                WHERE i.`objpid` = :hotel AND t.`type`=1 AND t.`payment`='ticket' AND t.`start` <= :now AND t.`end` >= :now AND t.`status` = 1
                GROUP BY t.`id`
                ORDER BY t.`id` DESC";

        $db = db(config('db'));
        $_products = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel['id'], ':now'=>NOW));
        //debug var_dump($_products); exit;

        foreach ($_products as $k => $product)
        {
            $product = $this -> items($product);
            unset($product['payment']);
            $product['type'] = 'ticket';
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
        if ($product['type'] != 1 || $product['payment'] != 'ticket') return $product;

        $sql = "SELECT i.`id` AS `code`, i.`name`, i.`objpid` AS `hotel`, i.`objid` AS `room`, i.`ext2` AS `bed`, i.`ext` AS `night`, i.`haslink` AS `hastrip`,
                        (i.`price` + ROUND({$this -> _profit})) AS `price`, i.`default`,
                        IF(i.`allot`<=i.`sold`, 0,  i.`allot`-i.`sold`) AS `allot`, i.`intro`, i.`start`, i.`end`,i.`min`,i.`max`,i.`rate`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_org_profit` AS fi1 ON fi1.`org` = :org AND fi1.`payment` = 'ticket' AND fi1.`objtype` = 'hotel' AND fi1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fi2 ON fi2.`org` = :org AND fi2.`payment` = 'ticket' AND fi2.`objtype` = 'hotel' AND fi2.`objid` = i.`pid`
                    LEFT JOIN `ptc_org_profit` AS fi3 ON fi3.`org` = :org AND fi3.`payment` = 'ticket' AND fi3.`objtype` = 'room'  AND fi3.`objid` = i.`id`
                WHERE i.`pid` = :pid AND i.`link` = 0 AND i.`price` > 0
                ORDER BY i.`seq` ASC, i.`id` ASC";

        $db = db(config('db'));
        $product['items'] = $db -> prepare($sql) -> execute(array(':pid'=>$product['id'], ':org'=>api::$org));

        if (!$product['items']) $product['items'] = array();
        $_min = $min = 0;
        foreach ($product['items'] as $k => $item)
        {
            if ($item['hastrip'])
            {
                $sql = "SELECT `name`, `objpid` AS `hotel`, `objid` AS `room`, `ext2` AS `bed`, `ext` AS `night`
                        FROM `ptc_product_item` WHERE `pid`=:pid AND `link`=:link ORDER BY `seq` ASC, `id` ASC";
                $trip = $db -> prepare($sql) -> execute(array(':pid'=>$product['id'], ':link'=>$item['code']));
                if ($trip)
                {
                    array_unshift($trip, array('name'=>$item['name'], 'hotel'=>$item['hotel'], 'room'=>$item['room'], 'bed'=>$item['bed'], 'night'=>$item['night']));
                    $item['trips'] = $trip;
                }
            }

            $item['code'] = key_encryption(str_pad($item['code'], 10, "0", STR_PAD_LEFT).'_ticket');
            $item['price'] = (int)$item['price'];
            $item['start'] = $item['start'] ? date('Y-m-d', $item['start']) : '';
            $item['end']   = $item['end'] ? date('Y-m-d', $item['end']) : '';
            $product['items'][$k] = $item;

            if ((!$min || $min > $item['price']) && $item['allot'] > 0) $min = $item['price'];

            if (!$_min || $_min > $item['price']) $_min = $item['price'];
        }

        $product['min']   = $min ? $min : $_min;
        return $product;
    }
    // items


}
