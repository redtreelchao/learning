<?php
if (!defined("PT_PATH")) exit;

// 产品复制

if ($id = (int)$_POST['id'])
{
    $time = NOW;
    $type = $_POST['type'];

    if ($type == 'product')
    {
        $product = $db -> prepare("SELECT *, null AS `id`, 0 AS `status`, 0 AS `audit`, 0 AS `start`, 0 AS `end`, {$time} AS `updatetime`, 0 AS `onlinetime`, 0 AS `offlinetime` FROM `ptc_product` WHERE `id`=:id;")
                       -> execute(array(':id'=>$id));
        if (!$product)
            json_return(null, 1, '产品源数据不存在或已删除');

        $db -> beginTrans();

        // Product
        list($column, $sql, $value) = array_values(insert_array($product[0]));
        $new_id = $db -> prepare("INSERT INTO `ptc_product` {$column} VALUES {$sql}") -> execute($value);
        if (!$new_id)
        {
            $db -> rollback();
            json_return(null, 2, '产品复制失败，请重试');
        }

        $items = $db -> prepare("SELECT * FROM `ptc_product_item` WHERE `pid`=:pid;") -> execute(array(':pid'=>$id));
    }
    else
    {

        $items = $db -> prepare("SELECT * FROM `ptc_product_item` WHERE `id`=:id;") -> execute(array(':id'=>$id));
        if (!$items)
            json_return(null, 1, '产品源数据不存在或已删除');

        $new_id = $items[0]['pid'];

        $db -> beginTrans();
    }


    // Items
        //2015-12-2 应Jillian要求，不再复制子项信息
        //2016-6-17 应Jillian要求，恢复复制子项信息
    $ids = array();
    foreach($items as $k => $v)
    {
        $_oldid = $v['id'];
        $v['id'] = null;
        $v['pid'] = $new_id;
        $v['sold'] = 0;
        $v['updatetime'] = $time;

        list($column, $sql, $value) = array_values(insert_array($v));
        $newitemid = $db -> prepare("INSERT INTO `ptc_product_item` {$column} VALUES {$sql}") -> execute($value);
        if (!$newitemid)
        {
            $db -> rollback();
            json_return(null, 3, '产品复制失败，请重试');
        }

        $ids[$_oldid] = $newitemid;
    }

    // 利润
    $_ids = implode(',', array_keys($ids));

    if ($type == 'product')
    {
        $profits = $db -> prepare("SELECT * FROM `ptc_org_profit` WHERE (`objtype`='hotel' AND `objid`=:pid)" . ($_ids ? " OR (`objtype`='room' AND `objid` IN ({$_ids}))" : '')) -> execute(array(':pid'=>$id));
    }
    else
    {
        $profits = $db -> prepare("SELECT * FROM `ptc_org_profit` WHERE `objtype`='room' AND `objid`=:id") -> execute(array(':id'=>$id));
    }

    if ($profits)
    {
        foreach ($profits as $k => $v)
        {
            $profits[$k]['id'] = null;
            $profits[$k]['updatetime'] = $time;
            if ($v['objtype'] == 'hotel')
                $profits[$k]['objid'] = $new_id;
            else
                $profits[$k]['objid'] = $ids[$v['objid']];
        }

        list($column, $sql, $value) = array_values(insert_array($profits));
        $rs = $db -> prepare("INSERT INTO `ptc_org_profit` {$column} VALUES {$sql}") -> execute($value);
        if (!$rs)
        {
            $db -> rollback();
            json_return(null, 4, '产品复制失败，请重试');
        }
    }

    if ($type == 'product')
    {
        // QA
        $qa = $db -> prepare("SELECT *, null AS `id`, {$new_id} AS `product` FROM `ptc_qa` WHERE `product`=:product;") -> execute(array(':product'=>$id));
        if ($qa)
        {
            list($column, $sql, $value) = array_values(insert_array($qa));
            $rs = $db -> prepare("INSERT INTO `ptc_qa` {$column} VALUES {$sql}") -> execute($value);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 4, '产品复制失败，请重试');
            }
        }

        // History
        if (!history($new_id, 'product', "复制了产品从[{$id}]", array('product'=>$product, 'items'=>$items)))
        {
            $db -> rollback();
            json_return(null, 5, '保存失败，请重试');
        }
    }
    else
    {
        // History
        if (!history($new_id, 'product', "复制了内容项[{$id}]", array('item'=>$items[0])))
        {
            $db -> rollback();
            json_return(null, 5, '保存失败，请重试');
        }
    }

    if (!$db -> commit())
    {
        $db -> rollback();
        json_return(null, 9, '产品复制失败，请重试');
    }

    json_return($new_id);
}