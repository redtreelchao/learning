<?php
// goods ticket product hook of api
class goods_ticket_api extends goods_ticket_hook
{

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
        if ($product['type'] != 7 || $product['payment'] != 'ticket') return $product;

        $sql = "SELECT i.`id` AS `code`, i.`name`, i.`objpid` AS `goods`, (i.`price` + ROUND({$this -> _profit})) AS `price`, i.`default`,
                        IF(i.`allot`<=i.`sold`, 0,  i.`allot`-i.`sold`) AS `allot`, i.`intro`, i.`start`, i.`end`,i.`min`,i.`max`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_org_profit` AS fi1 ON fi1.`org` = :org AND fi1.`payment` = 'ticket' AND fi1.`objtype` = 'goods' AND fi1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fi2 ON fi2.`org` = :org AND fi2.`payment` = 'ticket' AND fi2.`objtype` = 'goods' AND fi2.`objid` = i.`pid`
                    LEFT JOIN `ptc_org_profit` AS fi3 ON fi2.`org` = :org AND fi3.`payment` = 'ticket' AND fi3.`objtype` = 'item' AND fi3.`objid` = i.`id`
                WHERE i.`pid` = :pid AND i.`link` = 0 AND i.`price` > 0
                GROUP BY i.`id`
                ORDER BY i.`seq` ASC, i.`id` ASC";

        $db = db(config('db'));
        $product['items'] = $db -> prepare($sql) -> execute(array(':pid'=>$product['id'], ':org'=>api::$org));

        if (!$product['items']) $product['items'] = array();
        $_min = $min = 0;
        foreach ($product['items'] as $k => $item)
        {
            $item['code'] = key_encryption(str_pad($item['code'], 10, "0", STR_PAD_LEFT).'_ticket');
            $item['price'] = (int)$item['price'];
            $item['start'] = '';
            $item['end']   = '';
            $product['items'][$k] = $item;

            if ((!$min || $min > $item['price']) && $item['allot'] > 0) $min = $item['price'];

            if (!$_min || $_min > $item['price']) $_min = $item['price'];
        }

        $product['min']   = $min ? $min : $_min;
        return $product;
    }
    // items


}
