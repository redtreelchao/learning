<?php
if (!defined("PT_PATH")) exit;


/**
 * 自动配对
 +-----------------------------------------
 * @access public
 * @param int $hotel
 * @param string $name
 * @return void
 */
function autobind($hotel, $name)
{
    //type
    $name = str_replace(array('大床', '双床'), '', $name);

    $name = str_replace(array('间', '客房', '房'), '%', trim($name));

    $db = db(config('db'));
    $types = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel_room_type` WHERE `hotel`=:hotel AND `name` LIKE :name") -> execute(array(':hotel'=>$hotel, ':name'=>$name));

    if (!$types) return array();

    if (count($types) == 1) return $types[0];

    $check = array();
    foreach($types as $t)
    {
        similar_text($t['name'], $name, $per);
        if ($per > 80)
        {
            $check[] = $t;
        }
    }

    if (count($check) == 1)
        return $check[0];
    else
        return array();
}




ignore_user_abort(true);
set_time_limit(0);
header("Content-type:text/html; charset=utf-8");

// 缓冲输出
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
ob_implicit_flush(0);
ob_start();

$sups = supplies();
$sup = $_GET['sup'];
$supclass = strtolower($sup);
if(!$sup || !isset($sups[$sup])) redirect('./room.php');

?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>PUTIKE &rsaquo; 酒店配对列表</title>

<link href="'.RESOURCES_URL.'css/bootstrap.min.css" rel="stylesheet" />
<link href="'.RESOURCES_URL.'css/font-awesome.min.css" rel="stylesheet" />
<link href="'.RESOURCES_URL.'css/admin.css" rel="stylesheet" />

<!--[if lt IE 9]><script src="'.RESOURCES_URL.'js/ie8-responsive-file-warning.js"></script><![endif]-->
<script src="'.RESOURCES_URL.'js/ie-emulation-modes-warning.js"></script>

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="'.RESOURCES_URL.'js/ie10-viewport-bug-workaround.js"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="'.RESOURCES_URL.'js/html5shiv.min.js"></script>
<script src="'.RESOURCES_URL.'js/respond.min.js"></script>
<![endif]-->
</head>
<body style="padding-top:0px;">
<div class="container-fluid">
<div class="row">
<?php

$sql = "SELECT a.*, b.`id` AS `hotelid`, b.`name` AS `hotelname`
        FROM `sup_".$supclass."_room` AS a
            INNER JOIN `ptc_hotel` AS b ON a.`hotel` = b.`{$sup}`
        WHERE 1=1
        ORDER BY b.`id` ASC;";
$rooms = $db -> prepare($sql) -> execute();

$hotelid = 0;
foreach ($rooms as $room)
{
    $old = $db -> prepare("SELECT * FROM `ptc_hotel_room` WHERE `supply`=:supply AND `key`=:key") -> execute(array(':supply'=>$sup, ':key'=>$supclass::roomkey($room)));

    $new = array(
        'hotel' => $room['hotelid'],
        'type'  => 0,
        'name'  => $room['roomname'],
        'bed'   => $supclass::bed($room['bed']),
        'net'   => $supclass::net($room),
        'supply'=> $sup,
        'key'   => $supclass::roomkey($room),
        'isdel' => $room['isdel'],
        'updatetime'    => NOW,
    );

    // 输出酒店名
    if ($room['hotelid'] != $hotelid)
    {
        echo "<h4>{$room['hotelid']} : {$room['hotelname']}</h4>";
        $hotelid = $room['hotelid'];

        // History
        history($room['hotelid'], 'room', "更新了{$sup}房型", array());
    }

    // 输出房型名
    if (!$old)
    {
        // 新房型
        echo "<p>新房型 : {$new['name']} ";

        // 自动配对
        $type = autobind($new['hotel'], $new['name']);
        if ($type)
        {
            $new['type'] = $type['id'];

            // History
            history($new['hotel'], 'room', "新房型 “{$new['name']}” 自动匹配到 “{$type['name']}”", array());

            echo '<span class="label label-primary">配对到:'.$type['name'].'</span> ';
        }
        else
        {
            echo '<span class="label label-default">未配对</span> ';
        }

        list($column, $sql, $value) = array_values(insert_array($new));
        $rs = $db -> prepare("INSERT INTO `ptc_hotel_room` {$column} VALUES {$sql};") -> execute($value);
        echo ($rs === false ? '<span class="label label-danger">保存失败</span>' : '<span class="label label-success">保存成功</span>')."</p>";
    }
    else
    {
        $assoc = array_diff_assoc($new, $old[0]);   // 差集
        if (isset($assoc['name']) || isset($assoc['bed']) || isset($assoc['net']) || isset($assoc['isdel']))
        {
            // 更新数据
            $new['type'] = isset($assoc['name']) ? 0 : $old[0]['type'];
            list($sql, $value) = array_values(update_array($new));
            $value[':id'] = (int)$old[0]['id'];

            $rs = $db -> prepare("UPDATE `ptc_hotel_room` SET {$sql} WHERE `id`=:id;") -> execute($value);

            // 显示更新信息
            echo "<p>更新房型 : {$old[0]['name']} ";
            if (isset($assoc['bed'])) echo '<span class="label label-info">床型 '.$old[0]['bed'].' => '.$new['bed'].'</span> ';
            if (isset($assoc['net'])) echo '<span class="label label-info">宽带 '.$old[0]['net'].' => '.$new['net'].'</span> ';
            if (isset($assoc['name']))
            {
                echo '<span class="label label-warning">新房型名 '.$new['name'].'</span> ';
                if(false === $db -> prepare("UPDATE `ptc_hotel_price_date` SET `roomtype`=0 WHERE `room`=:id") -> execute(array(':id'=>$old[0]['id'])))
                {
                    echo '<span class="label label-danger">更新关联价格失败</span> ';
                }

                // 自动配对
                $type = autobind($new['hotel'], $new['name']);
                if ($type)
                {
                    $new['type'] = $type['id'];
                    echo '<span class="label label-primary">应配对到:'.$type['name'].'，未操作</span> ';
                }
                else
                {
                    echo '<span class="label label-default">未配对</span> ';
                }
            }

            if (isset($assoc['isdel'])) echo $new['isdel'] ? '<span class="label label-default">禁用</span>' : '<span class="label label-info">开启</span> ';
            echo ($rs === false ? '<span class="label label-danger">保存失败</span>' : '<span class="label label-success">保存成功</span>')."</p>";
        }
    }

    ob_flush();
    flush();
}

//echo delete('ptc_hotel_room', "`isdel`=1") ? '<span class="label label-success">清理删除数据成功</span>' : '<span class="label label-danger">清理删除数据失败</span>';

echo "<h4>更新完毕!</h4>";


?>

</div></div></body></html>