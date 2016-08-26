<?php
/**
 +-----------------------------------------
 * @author jacky.yan
 * @category
 * @version $Id$
 */
/**   */
// session start
define("SESSION_ON", true);

// define config file
define("CONFIG", '/conf/web.php');

// debug switch
define("DEBUG", true);

// include common
include('./common.php');

// include project common functions
include(COMMON_PATH.'web_func.php');

// defined resources url
define('RESOURCES_URL', config('web.resources_url'));

// check permission
rbac_user();


$db = db(config('db'));


// 酒店列表
$where = "1=1";
$condition = array();

if (isset($_GET['keyword']))
{
    $keyword = '%'.$_GET['keyword'].'%';
    $where .= " AND (a.`name` LIKE :keyword OR b.`name` LIKE :keyword OR c.`name` LIKE :keyword OR a.`pinyin` LIKE :keyword OR a.`address` LIKE :keyword OR a.`teL` LIKE :keyword)";
    $condition[':keyword'] = $keyword;
    template::assign('keyword', $_GET['keyword']);
}

$join = "LEFT JOIN `ptc_district` AS b ON a.`country` = b.`id`
         LEFT JOIN `ptc_district` AS c ON a.`city` = c.`id`
         LEFT JOIN `ptc_hotel_room_type` AS d ON a.`id` = d.`hotel`";

$count = $db -> prepare("SELECT COUNT(*) AS `c` FROM (SELECT a.`id` FROM `ptc_hotel` AS a {$join} WHERE {$where} GROUP BY a.`id`) AS s;") -> execute($condition);

$page = new page($count[0]['c'], 20);
$limit = $page -> limit();

$sql = "SELECT a.*, b.name AS `country`, c.`name` AS `city`, c.`province`
        FROM `ptc_hotel` AS a
        {$join}
        WHERE {$where}
        GROUP BY a.`id`
        LIMIT {$limit};";

$list = $db -> prepare($sql) -> execute($condition);

foreach ($list as $k => $v)
{
    $sql = "SELECT a.`id` AS `typeid`, a.`name` AS `typename`, b.`id` AS `roomid`, b.`name` AS `roomname`, b.`supply`
            FROM `ptc_hotel_room_type` AS a
            LEFT JOIN `ptc_hotel_room` AS b ON a.`id`=b.`type`
            WHERE a.`hotel`=:hotel AND b.`type`!=0
            ORDER BY a.`id`,b.`supply`;";

    $data = $db -> prepare($sql) -> execute(array(':hotel'=>$v['id']));

    $rooms = array();
    foreach ($data as $l =>$w)
    {
        $roomname   = format_text(trim($w['roomname']));    // room_name
        $typename   = format_text(trim($w['typename']));       // room_type_name
        similar_text($typename, $roomname, $percent);

        $typeid = $w['typeid'];
        $roomid = $w['roomid'];

        if (!isset($rooms[$typeid]))
            $rooms[$typeid] = array(
                'id'        => $typeid,
                'name'      => $w['typename'],
                'match'     => array(),
            );

        if (!isset($rooms[$typeid]['match'][$roomid]))
            $rooms[$typeid]['match'][$roomid] = array(
                'id'        => $roomid,
                'name'      => $w['roomname'],
                'supply'    => $w['supply'],
                'percent'   => number_format($percent, 1),
            );
    }

    $list[$k]['rooms'] = $rooms;
}

//var_dump($list);
template::assign('page', $page->show());
template::assign('list', $list);
template::assign('nav', 'room');
template::display('room/check');


//格式化文字
function format_text($text)
{
    $text = str_replace(array('（','（'),'(',$text);
    $text = str_replace(array('大床','双床'),'',$text);
    $text = explode('(',$text)[0];
    $text = preg_replace("/房\$|间\$|别墅\$|公寓\$|室\$/", "", $text);
    return $text;
}
?>