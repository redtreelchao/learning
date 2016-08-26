<?php
if (!defined("PT_PATH")) exit;

if ($_POST)
{
    $id = $_POST['id'];
    $type = $_POST['type'];

    $room = $db -> prepare("SELECT * FROM `ptc_hotel_room` WHERE `id`=:id") -> execute(array(':id'=>$id));
    if (!$room) json_return(null, 1, '房型不存在，请刷新后重试~');

    $db -> beginTrans();

    if ($room[0]['type'] == $type)
    {
        $type = 0;
        $rs = $db -> prepare("UPDATE `ptc_hotel_room` SET `type`=0 WHERE `id`=:id;") -> execute(array(':id'=>$id));

        $history_message = "房型 “{$room[0]['name']}” 取消匹配";
        $history_data = array('oldbind'=>$room[0]['type']);

        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 2, '操作失败，请重试..');
        }
    }
    else
    {
        $roomtype = $db -> prepare("SELECT `name` FROM `ptc_hotel_room_type` WHERE `id`=:type") -> execute(array(':type'=>$type));

        $rs = $db -> prepare("UPDATE `ptc_hotel_room` SET `type`=:type WHERE `id`=:id;") -> execute(array(':id'=>$id, ':type'=>$type));

        $history_message = "{$room[0]['supply']}房型 “{$room[0]['name']}” 匹配到 “{$roomtype[0]['name']}”";
        $history_data = array('oldbind'=>$room[0]['type'], 'newbind'=>$type);

        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 2, '操作失败，请重试..');
        }

        // 创建产品
        $sql = "SELECT i.*
                FROM `ptc_product` AS p
                    LEFT JOIN `ptc_product_item` AS i ON p.`id` = i.`pid`
                WHERE i.`objtype`='room' AND i.`objpid`=:hotel AND p.`type`=1 AND p.`payment`='prepay' AND p.`default`=1";
        $product = $db -> prepare($sql) -> execute(array(':hotel'=>$room[0]['hotel']));
        if (!$product)
        {
            $hotel = $db -> prepare("SELECT `name` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$room[0]['hotel']));
            $data = array(
                'name'      => $hotel[0]['name'],
                'type'      => 1,
                'payment'   => 'prepay',
                'start'     => (int)strtotime('today 00:00:00'),
                'end'       => (int)strtotime('2999-01-01 00:00:00'),
                'intro'     => '',
                'rule'      => '',
                'refund'    => '该订单确认后不可被取消修改，若未入住，我们将收取您全额的房费',
                'default'   => 1,
                'updatetime'=> NOW,
            );

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_product` {$column} VALUES {$sql};") -> execute($value);

            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 3, '操作失败，请重试');
            }

            // History
            if (!history($rs, 'product', '（自动）创建了产品', $data))
            {
                $db -> rollback();
                json_return(null, 4, '保存失败，请重试');
            }

            $additem = true;
            $productid = $rs;
        }
        else
        {
            foreach ($product as $v)
            {
                if ($v['objid'] == $type)
                {
                    $additem = false;
                    break;
                }
            }

            if (!isset($additem))
            {
                $additem = true;
                $productid = $v['pid'];
            }
        }

        if (isset($additem) && $additem)
        {
            $data = array(
                'name'      => roomname($roomtype[0]['name'], 2),
                'objtype'   => 'room',
                'objid'     => $type,
                'objpid'    => $room[0]['hotel'],
                'pid'       => $productid,
                'intro'     => '',
                'childstd'  => '',
                'babystd'   => '',
                'data'      => '{"advance":-1,"min":-1,"nation":-1,"package":-1,"supply":["HMC","JLT","CNB","ELG"]}',
            );

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_product_item` {$column} VALUES {$sql};") -> execute($value);
            $data['id'] = $rs;
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 5, '保存失败，请重试');
            }

            // Remove Api push.
            // Product can be edited before post, only.

            // History
            if (!history($productid, 'item', "（自动）新增了“{$data['name']}”", $data))
            {
                $db -> rollback();
                json_return(null, 6, '保存失败，请重试');
            }
        }

    }

    // History
    if (!history($room[0]['hotel'], 'room', $history_message, $history_data))
    {
        $db -> rollback();
        json_return(null, 2, '保存失败，请重试');
    }

    // 更新价格库配对
    $rs = $db -> prepare("UPDATE `ptc_hotel_price_date` SET `roomtype`=:type WHERE `room`=:id;") -> execute(array(':id'=>$id, ':type'=>$type));
    if (false === $rs)
    {
        $db -> rollback();
        json_return(null, 3, '操作失败，请重试..');
    }

    if ($db -> commit())
        json_return(1);
    else
        json_return(null, 9, '操作失败，请重试..');
}


// 供应商
$sups = supplies();
$supkey = implode('`,`', array_keys($sups));

// 酒店信息
$hotelid = (int)$_GET['hotel'];
$hotel = $db -> prepare("SELECT `id`,`name`,`{$supkey}` FROM `ptc_hotel` WHERE `id`=:hotel") -> execute(array(':hotel'=>$hotelid));

if (!$hotel) redirect('/room.php');
template::assign('hotel', $hotel[0]);

// 供应商
if (!empty($_GET['sup']) && isset($sups[$_GET['sup']]))
{
    $sup = $_GET['sup'];
}
else
{
    foreach ($sups as $k => $v)
    {
        if ($hotel[0][$k])
        {
            $sup = $k; break;
        }
    }
}

// 搜索所有可用的房型
foreach ($sups as $k => $v)
{
    $sups[$k] = array('name'=>$v, 'rooms'=>array());
    if ($hotel[0][$k])
    {
        $sups[$k]['rooms'] = $db -> prepare("SELECT * FROM `ptc_hotel_room` WHERE `hotel`=:hotel AND `supply`=:supply") -> execute(array(':hotel'=>$hotelid, ':supply'=>$k));

        $unbind = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_hotel_room` WHERE `hotel`=:hotel AND `supply`=:supply AND `type`=0") -> execute(array(':hotel'=>$hotelid, ':supply'=>$k));
        $sups[$k]['unbind'] = $unbind[0]['c'];
    }
}

template::assign('supplies', $sups);
template::assign('supply', $sup);

// 房型遍历
$roomtypes = $db -> prepare("SELECT * FROM `ptc_hotel_room_type` WHERE `hotel`=:hotel;") -> execute(array(':hotel'=>$hotelid));
template::assign('roomtypes', $roomtypes);

// 历史记录
$history = $db -> prepare("SELECT `id`,`time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND `type`='room' ORDER BY `time` DESC LIMIT 0,10;") -> execute(array(':id'=>$hotelid));
template::assign('history', $history);

template::display('room/bind');