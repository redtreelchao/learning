<?php
class hotel_prepay_api extends hotel_prepay_hook
{

    /**
     * add condition for hotel search
     +-----------------------------------------
     * @access public
     * @return void
     */
    public function search_condition($sql, $types, $checkin, $checkout, $min, $max)
    {
        if (in_array('prepay', $types))
        {
            // Group only by hotel, Can't load room's profit.
            $sql[0] .= "LEFT JOIN `ptc_hotel_price_date` AS p ON p.`hotel`=h.`id` AND p.`roomtype`!=0 AND p.`payment`=1 AND p.`date` >= :checkin AND p.`date` < :checkout AND p.`close` = 0
                        LEFT JOIN `ptc_org_profit` AS fp1 ON fp1.`org` = :org AND fp1.`payment` = 'prepay' AND fp1.`objtype` = 'hotel' AND fp1.`objid` = 0
                        LEFT JOIN `ptc_org_profit` AS fp2 ON fp2.`org` = :org AND fp2.`payment` = 'prepay' AND fp2.`objtype` = 'hotel' AND fp2.`objid` = p.`hotel`
                        LEFT JOIN `ptc_org_profit` AS fp3 ON fp3.`org` = :org AND fp3.`payment` = 'prepay' AND fp3.`objtype` = 'room'  AND fp3.`objid` = p.`roomtype`";

            $condition = array();
            $condition[':org'] = api::$org;
            $condition[':checkin'] = $checkin;
            $condition[':checkout'] = $checkout;

            $_where = 'p.`id` IS NOT NULL';

            // if search price . load profit
            if ($min !== null)
            {
                $condition[':min'] = (int)$min;
                $_where .= " AND p.`price` + {$this -> _profit} >= :min";
            }

            if ($max !== null)
            {
                $condition[':max'] = (int)$max;
                $_where .= " AND p.`price` + {$this -> _profit} <= :max";
            }

            //$_where .= ' AND p.`id` = 0'; // close all prepay

            $sql[1]['prepay'] = $_where;
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
    static public function search($hotel, $types, $checkin, $checkout, $min, $max, $filter=array())
    {
        // return $hotel; // close all
        if (!in_array('prepay', $types)) return $hotel;

        $night = ($checkout - $checkin) / 86400;

        $db = db(config('db'));

        $where = 'p.`hotel`=:hotel AND p.`roomtype`!=0';
        $condition = array(':hotel'=>$hotel['id'], ':checkin'=>$checkin, ':checkout'=>$checkout, ':org'=>api::$org);

        if (!empty($hotel['room']))
        {
            $where .= ' AND p.`roomtype`=:room';
            $condition[':room'] = (int)$hotel['room'];
        }

        if (!empty($hotel['bed']))
        {
            $where .= ' AND p.`bed`=:bed';
            $condition[':bed'] = $hotel['bed'];
        }

        if (isset($filter['advance']) && $filter['advance'] >= 0)
        {
            if ($filter['advance'])
                $where .= ' AND p.`advance` > 0';
            else
                $where .= ' AND p.`advance` = 0';
        }

        if (isset($filter['min']) && $filter['min'] >= 0)
        {
            if ($filter['min'])
                $where .= ' AND p.`min` > 0';
            else
                $where .= ' AND p.`min` = 0';
        }

        if (isset($filter['nation']) && $filter['nation'] >= 0)
        {
            //$where = ' AND p.`min` = 0';
        }

        if (isset($filter['package']) && $filter['package'] >= 0)
        {
            if ($filter['package'])
                $where .= ' AND p.`package` > 0';
            else
                $where .= ' AND p.`package` = 0';
        }

        if (!empty($filter['supply']) && count($filter['supply']) < count(supplies()))
        {
            $where .= ' AND p.`supply` IN ("'.explode('","', $filter['supply']).'")';
        }

        $sql = "SELECT 'prepay' AS `type`, p.`uncombine`, p.`roomtype` AS `room`, r.`name` AS `roomname`, p.`bed`, p.`payment`, p.`nation`, n.`name` AS `nationname`, p.`start`, p.`end`, p.`min`, p.`advance`, p.`supply`,
                    fp1.`type` AS `type1`, fp2.`type` AS `type2`, fp3.`type` AS `type3`,
                    fp1.`profit` AS `profit1`, fp2.`profit` AS `profit2`, fp3.`profit` AS `profit3`,
                    SUM(p.`price`)/COUNT(p.`date`) AS `avg`
                FROM `ptc_hotel_price_date` AS p
                    LEFT JOIN `ptc_nation` AS n ON p.`nation` = n.`id`
                    LEFT JOIN `ptc_hotel_room_type` AS r ON p.`roomtype` = r.`id`
                    LEFT JOIN `ptc_org_profit` AS fp1 ON fp1.`org` = :org AND fp1.`payment` = 'prepay' AND fp1.`objtype` = 'hotel' AND fp1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fp2 ON fp2.`org` = :org AND fp2.`payment` = 'prepay' AND fp2.`objtype` = 'hotel' AND fp2.`objid` = p.`hotel`
                    LEFT JOIN `ptc_org_profit` AS fp3 ON fp3.`org` = :org AND fp3.`payment` = 'prepay' AND fp3.`objtype` = 'room'  AND fp3.`objid` = p.`roomtype`
                WHERE {$where} AND p.`payment`=1 AND p.`date` >= :checkin AND p.`date` < :checkout AND p.`close` = 0
                GROUP BY p.`uncombine`
                ORDER BY p.`roomtype` ASC, p.`supply` ASC, p.`bed` DESC";

        $_products = $db -> prepare($sql) -> execute($condition); //var_dump($sql); var_dump($condition); var_dump($_products);

        $_bed_2 = array();
        $_bed_t = array();
        $_min = 0;

        foreach ($_products as $k => $product)
        {
            // load profit, because price is different everyday.
            $type = 'amount'; $profit = 0;
            for($i = 3; $i >= 1; $i--)
            {
                if ($product['type'.$i])
                {
                    $type = $product['type'.$i];
                    $profit = $product['profit'.$i];
                    unset($product['type1'], $product['type2'], $product['type3'], $product['profit1'], $product['profit2'], $product['profit3']);
                    break;
                }
            }

            $_key = array($product['room'], $product['nation'], $product['start'], $product['end'], $product['min'], $product['advance']);
            $_key = implode('_', $_key);

            // format price items
            $items = self::_prices($product['uncombine'], $checkin, $checkout, $type, $profit, $product['bed'].'_'.$_key, $product['supply'], count($hotel['products']));
            if (!$items)
            {
                $hotel['products'][] = null;
                continue;
            }

            if (isset($items['merger']))
            {
                $index = $items['merger'];
                $merger_key = $items['key']; // T_*

                $hotel['products'][$index]['bed'] = 2;
                $hotel['products'][$index]['roomname'] = roomname($product['roomname'], '2');
                foreach ($hotel['products'][$index]['items'] as $key => $item)
                {
                    $code = key_encryption($item['code'], true);
                    $hotel['products'][$index]['items'][$key]['code'] = key_encryption('_'.$code);
                    $code = explode('_', $code, 2);
                    $merger_combine = array_pop($code);
                    self::_weight($merger_key.'_'.substr($merger_combine, 0, -7), '2', 'merger');
                }

                $hotel['products'][] = null;
                continue;
            }

            // merge one supply's rooms
            // delete single room with same price and same status
            $product['items'] = array();

            $_supkey = $product['supply'].'_'.$_key;
            $bed = $product['bed'];
            unset($product['supply']);

            $_bed_d = array();
            foreach ($items as $combine => $item)
            {
                if (empty($item['prices'])) continue;

                $product['items'][] = array('code'=>key_encryption($item['code'].'_prepay'), 'total'=>$item['total'], 'night'=>$item['night'], 'average'=>$item['average'], 'prices'=>$item['prices']);

                // get min price
                if (!$_min || $_min > $item['min'])
                    $_min = $item['min'];
            }

            unset($items);

            $product['roomname'] = roomname($product['roomname'], $product['bed']);
            $product['avg'] = round($product['avg']) + round($type == 'amount' ? $profit : ($product['avg'] * $profit / 100));

            unset($product['uncombine'], $product['avg']);
            $hotel['products'][] = $product;
        }

        $hotel['prepay_min'] = $_min;
        $hotel['products'] = array_filter($hotel['products']);
        return $hotel;
    }
    // search





    // items
    public static function items($product, $protype='amount', $profit=0, $profit_child=0, $profit_baby=0, $checkin=0, $checkout=0)
    {
        if ($product['type'] != 1 || $product['payment'] != 'prepay') return $product;

        $sql = "SELECT i.`name`, h.`name` AS `hotelname`, i.`objpid` AS `id`, i.`objid` AS `room`, i.`ext2` AS `bed`, i.`ext` AS `night`, i.`data`, i.`intro`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_hotel` AS h ON i.`objpid`=h.`id`
                WHERE i.`pid` = :pid AND i.`link` = 0
                ORDER BY i.`seq` ASC, i.`id` ASC";

        $db = db(config('db'));
        $items = $db -> prepare($sql) -> execute(array(':pid'=>$product['id'])); //var_dump($item);

        if (empty($product['items'])) $product['items'] = array();

        foreach ($items as $k => $item)
        {
            $item['products'] = array();
            $condition = json_decode($item['data'], true);
            $item = self::search($item, array('prepay'), $checkin, $checkout, null, null, $condition); //var_dump($item);
            $data = array(
                'name'      => $item['name'],
                'hotel'     => $item['id'],
                'hotelname' => $item['hotelname'],
                'intro'     => $item['intro'],
            );

            foreach ($item['products'] as $v)
            {
                $product['items'][] = array_merge($data, $v);
            }
        }

        return $product;
    }
    // items




    // get product's everyday prices
    private static function _prices($uncombine, $checkin, $checkout, $protype='amount', $profit=0, $_key, $supply, $product_index)
    {
        $sql = "SELECT p.`date`, p.`combine`, p.`price`, p.`breakfast`, p.`package`, k.`name` AS `packagename`, p.`allot`, p.`filled`
                FROM `ptc_hotel_price_date` AS p
                    LEFT JOIN `ptc_hotel_package` AS k ON k.`id` = p.`package`
                WHERE p.`uncombine`=:uncombine AND p.`date` >= :checkin AND p.`date` < :checkout AND p.`close` = 0
                ORDER BY p.`date` ASC;";

        $db = db(config('db'));
        $prices =  $db  -> prepare($sql) -> execute(array(':uncombine'=>$uncombine, ':checkin'=>$checkin, ':checkout'=>$checkout));
        /* debug:/ var_dump($prices); exit; //*/

        $_prices = array();
        $_combines = array();
        foreach ($prices as $p)
        {
            if (empty($_prices[$p['date']]))
                $_prices[$p['date']] = array();

            $_prices[$p['date']][$p['combine']] = array(
                'date'        => date('Y-m-d', $p['date']),
                'day'         => date('N', $p['date']),
                'key'         => $p['combine'],
                'price'       => $p['price'] + round($protype == 'amount' ? $profit : ($p['price'] * $profit / 100)),
                'breakfast'   => $p['breakfast'],
                'package'     => $p['package'],
                'packagename' => (string)$p['packagename'],
                'allot'       => $p['allot'],
                'filled'      => !$p['allot'] ? 1 : $p['filled'],   // BD requirement
            );

            $_combines[$p['combine']] = array('package'=>$p['package'], 'breakfast'=>$p['breakfast']);
        }

        $items = self::_combine($_prices, $checkin, $checkout, $_combines, $uncombine, $_key, $supply, $product_index);
        return $items;
    }
    // prices






    // filter and combination
    private static function _combine(&$prices, $checkin, $checkout, $combines, $uncombine, $_key, $supply, $product_index)
    {
        static $_bedT = array();

        $night = ($checkout - $checkin) / 86400;

        $merger_num = 0;

        if ($_key[0] == 'D')
        {
            $_tempkey = $_key;
            $_tempkey[0] = 'T';
        }

        $data = array();

        foreach ($combines as $key => $combine)
        {
            $_prices = array();
            $_total  = 0;
            $_days   = 0;
            $_min    = 0;
            $_filled = 0;
            $_filled_str = '';
            $_allot  = 0;
            $_keys = array();
            $_empty = array(
                'date'      => '',
                'day'       => '',
                'key'       => '-',
                'price'     => 0,
                'breakfast' => 0,
                'package'   => 0,
                'packagename' => '',
                'allot'     => 0,
                'filled'    => 1,
            );

            for ($day = $checkin; $day < $checkout; $day = $day + 86400)
            {
                $_empty['date'] = date('Y-m-d', $day);
                $_empty['day']  = date('N', $day);

                if (empty($prices[$day]))
                {
                    $_prices[$day] = $_empty;
                }
                else if (isset($prices[$day][$key]))
                {
                    $_prices[$day] = $prices[$day][$key];
                }
                else
                {
                    foreach ($prices[$day] as $p)
                    {
                        if ($p['breakfast'] >= $combine['breakfast'] && $p['filled'] == 0 && ($combine['package'] == 0 || $p['package'] == $combine['package']))
                        {
                            $_prices[$day] = $p; break;
                        }
                    }

                    if (empty($_prices[$day])) $_prices[$day] = $_empty;
                }

                $_keys[] = $_prices[$day]['key'];

                if ($_prices[$day]['price'])
                {
                    $_days ++;
                    $_total += $_prices[$day]['price'];
                    $_filled_str .= $_prices[$day]['filled'];
                    if ($_prices[$day]['filled']) $_filled ++;
                    if ($_prices[$day]['allot'])  $_allot ++;

                    if (!$_min || $_min > $_prices[$day]['price']) $_min = $_prices[$day]['price'];
                }
                else
                {
                    $_filled ++;
                }
            }

            $_keys = array_flip($_keys);
            if (count($_keys) == 1 || (count($_keys) == 2 && isset($_keys['-'])))
                $_combine = $key;
            else
                $_combine = implode('_', $_keys);

            // compare item's weight
            $weight  = $_allot * 10 + ($night - $_allot - $_filled) * 1;
            if ($_filled)
                $weight = - $_filled;

            $weight -= round($_total/$_days) / 1000000;

            if (!self::_weight("{$_key}_{$_combine}", $weight)) continue;

            // Double Bed
            $merger = 0;
            if ($_key[0] == 'D')
            {
                if (isset($_bedT[$supply.$_tempkey]) && $_tmp = &$_bedT[$supply.$_tempkey]['items'] && !empty($_tmp[$key]) && $_tmp[$key]['combine'] == $_combine)
                {
                    $merger = array('product'=>$_bedT[$supply.$_tempkey]['product'], 'item'=>array_search($key, array_keys($_tmp)));
                    $merger_num ++;
                }
            }

            $data[$key] = array(
                    'code'      => $uncombine.'_'.$_combine,
                    'combine'   => $_combine,
                    'breakfast' => $combine['breakfast'],
                    'package'   => $combine['package'],
                    'prices'    => array_values($_prices),
                    'total'     => $_total,
                    'average'   => round($_total/$_days),
                    'min'       => $_min,
                    'filled'    => $_filled,
                    'filledstr' => $_filled_str,
                    'night'     => $_days,
                    //'merger'    => $merger, // develop 单项合并
                );
        }

        // Recode T bed
        if ($_key[0] == 'T')
        {
            if (empty($_bedT[$supply.$_key]))
                $_bedT[$supply.$_key] = array('product'=>$product_index, 'items'=>$data);
        }

        // Check if all D same as T
        if ($_key[0] == 'D' && isset($_bedT[$supply.$_key]) && count($_bedT[$supply.$_key]['items']) == $merger_num)
        {
            return array('merger'=>$_bedT[$supply.$_tempkey]['product'], 'key'=>$_tempkey);
        }

        return $data;
    }
    // _combine




    // comparison of weight
    static private function _weight($key, $weight, $operate='recode')
    {
        static $_weights = array();

        if ($operate == 'recode')
        {
            if (!empty($_weights[$key]) && $_weights[$key] > $weight) return false;

            if (isset($_weights[$key]) && $_weights[$key] = $weight) return 'replace';

            $_weights[$key] = $weight;
            return true;
        }
        else if ($operate == 'merger')
        {
            if (isset($_weights[$key]))
            {
                $newkey = $key;
                $newkey[0] = '2';
                $_weights[$newkey] = $_weights[$key];
            }
        }

    }
    // _weight



}
?>
