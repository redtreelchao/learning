<?php
if (!defined("PT_PATH")) exit;

// sort similarity
function sortby($a, $b)
{
    if ($a['seq'] == $b['seq'])
        return 0;

    return ($a['seq'] > $b['seq']) ? -1 : 1;
}

if ($_POST)
{
    $id     = (int)$_POST['id'];
    $code   = (string)$_POST['code'];
    $supply = (string)$_POST['supply'];
    $hotel  = $db -> prepare("SELECT `{$supply}` FROM `ptc_hotel` WHERE `id`=:id;") -> execute(array(':id'=>$id));

    $db -> beginTrans();

    // Clear all price about the supply of hotel
    $prs = $db -> prepare("DELETE FROM `ptc_hotel_price_date` WHERE `hotel`=:hotel AND `supply`=:supply") -> execute(array(':hotel'=>$id, ':supply'=>$supply));
    if (false === $prs)
    {
        $db -> rollback();
        json_return(null, 1, '操作失败，请重试');
    }

    // Clear all rooms' data about the supply
    $rrs = delete('ptc_hotel_room', "`hotel`={$id} AND `supply`='{$supply}'", false);
    if (false === $rrs)
    {
        $db -> rollback();
        json_return(null, 2, '操作失败，请重试');
    }

    if ($hotel[0][$supply] == $code)
    {
        $rs = $db -> prepare("UPDATE `ptc_hotel` SET `{$supply}`=NULL WHERE `id`=:id;") -> execute(array(':id'=>$id));
        $history_message = "取消 [{$supply}] 配对";
        $history_data = array('old'=>$hotel[0][$supply], 'supply'=>$supply);
    }
    else
    {
        $rs = $db -> prepare("UPDATE `ptc_hotel` SET `{$supply}`=:code WHERE `id`=:id;") -> execute(array(':code'=>$code, ':id'=>$id));
        $history_message = "更改 [{$supply}] 配对为 [{$code}]";
        $history_data = array('old'=>$hotel[0][$supply], 'new'=>$code, 'supply'=>$supply);
    }

    if (false === $rs)
    {
        $db -> rollback();
        json_return(null, 2, '操作失败，请重试');
    }

    // History
    if (!history($id, 'hotel', $history_message, $history_data))
    {
        $db -> rollback();
        json_return(null, 3, '保存失败，请重试');
    }

    if (false === $db -> commit())
        json_return(null, 9, '操作失败，请重试');
    else
        json_return(null, 0, '操作成功');
}


$sups = supplies();
if (!empty($_GET['sup']) && isset($sups[$_GET['sup']]))
    $sup = $_GET['sup'];
else
    $sup = 'HMC';

template::assign('supplies', $sups);
template::assign('supply', $sup);

// Hotel list load
$where = "1=1";
$condition = array();
template::assign('keyword','');

if (isset($_GET['keyword']))
{
    $keyword = '%'.$_GET['keyword'].'%';
    $where .= " AND (a.`name` LIKE :keyword OR b.`name` LIKE :keyword OR c.`name` LIKE :keyword OR a.`pinyin` LIKE :keyword OR a.`address` LIKE :keyword OR a.`teL` LIKE :keyword)";
    $condition[':keyword'] = $keyword;
    template::assign('keyword', $_GET['keyword']);
}

$join = "LEFT JOIN `ptc_district` AS b ON a.`country` = b.`id`
        LEFT JOIN `ptc_district` AS c ON a.`city` = c.`id`";

$count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_hotel` AS `a` {$join} WHERE {$where};") -> execute($condition);

$page = new page($count[0]['c'], 10);
$limit = $page -> limit();

$sql = "SELECT a.*, b.name AS `country`, c.`name` AS `city`, c.`province` FROM `ptc_hotel` AS a
            {$join}
        WHERE {$where}
        ORDER BY a.`id` DESC
        LIMIT {$limit};";

$list = $db -> prepare($sql) -> execute($condition);


// Search all about supply's hotel like to.
foreach ($list as $k => $hotel)
{
    $list[$k]['binds'] = array();

    $b0 = array();
    if ($hotel[strtoupper($sup)])
    {
        $b0 = $db -> prepare("SELECT *, '0' AS `t` FROM `sup_{$sup}_hotel` WHERE `id`=:id") -> execute(array(':id'=>$hotel[strtoupper($sup)]));
    }

    $name = str_replace(array('大酒店','大饭店','酒店','饭店','宾馆','公寓','旅馆','客栈','精品',$hotel['city']), '%', $hotel['name']);
    $address = $hotel['address'];
    $tel = explode('-', $hotel['tel']);
    rsort($tel, SORT_NUMERIC);
    $tel = $tel[0];

    if (!$name || strlen($name) < 6)
    {
        $name = str_replace(array('酒店','饭店','宾馆','公寓','旅馆','客栈'), '%', $hotel['name']);
        if (!$name) continue;
    }

    $where = '`name` LIKE :name';
    $condition = array(':name' => '%'.$name);

    $b1 = $b2 = $b3 = array();
    $sup = strtolower($sup);
    $b1 = $db -> prepare("SELECT *, '1' AS `t` FROM `sup_{$sup}_hotel` WHERE {$where};") -> execute($condition);
    if (!$b1) $b1 = array();

    // Search Address
    if ($address)
    {
        $where = '`address` LIKE :address';
        $condition = array(':address' => "%{$address}%");
        $b2 = $db -> prepare("SELECT *, '2' AS `t` FROM `sup_{$sup}_hotel` WHERE {$where};") -> execute($condition);
        if (!$b2 || count($b2) > 5) $b2 = array();
    }

    // Search Tel
    if ($tel)
    {
        $where = '`tel` LIKE :tel';
        $condition = array(':tel' => "%{$tel}%");
        $b3 = $db -> prepare("SELECT *, '3' AS `t` FROM `sup_{$sup}_hotel` WHERE {$where};") -> execute($condition);
        if (!$b3) $b3 = array();
    }

    // Bind all information
    $_binds = array_merge($b0, $b1, $b2, $b3);
    $binds = array();
    foreach ($_binds as $v)
    {
        if (!isset($binds[$v['id']]))
        {
            $v['seq'] = 0;
            $v['weight'] = 0;
            $binds[$v['id']] = $v;
        }

        $weight  = 0;
        $percent = 0;
        $b = 0;
        switch ($v['t'])
        {
            case 1: similar_text($hotel['name'], $v['name'], $percent); $percent = $percent * 1.4; $weight++; break;
            case 2: similar_text($hotel['address'], $v['address'], $percent); $percent = $percent * 1; $weight++; break;
            case 3: similar_text($hotel['tel'], $v['tel'], $percent); $percent = $percent * 0.6; $weight++; break;
        }
        $binds[$v['id']]['seq'] += $percent;
        $binds[$v['id']]['weight'] += $weight;
    }

    foreach ($binds as $i => $v)
    {
        $binds[$i]['seq'] = $v['seq'] / 3 * (0.9 + 0.1 * $v['weight']);
    }

    // sort by weight
    usort($binds, "sortby");

    $list[$k]['binds'] = $binds;
}


template::assign('page', $page->show());
template::assign('list', $list);

template::display('hotel/bind');