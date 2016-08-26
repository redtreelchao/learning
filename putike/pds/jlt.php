<?php
/**
 * JLT
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */

// session start
define("SESSION_ON", true);

// define config file
define("CONFIG", './conf/web.php');

// debug switch
define("DEBUG", true);

// include common
include('./common.php');

// include project common functions
include(COMMON_PATH.'web_func.php');

// defined resources url
define('RESOURCES_URL', config('web.resources_url'));

// check permission
if (empty($_GET['auto']) || $_GET['auto'] != md5('auto'.date('Y-m-d')))
rbac_user();


$db = db(config('db'));

$method = empty($_GET['method']) ? 'home' : $_GET['method'];

template::assign('nav', 'Api');
template::assign('subnav', 'jlt');

switch ($method)
{

    // 酒店列表
    case 'hotel':
        if ($_POST)
        {
            ignore_user_abort(true);
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            if (jlt::hotel())
                json_return(1);
            else
                json_return(null, 1, '更新失败，请重试');
        }

        $where = "a.`isdel`=0";
        $condition = array();

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $keyword2 = $_GET['keyword'];
            $where .= " AND (a.`name` LIKE :keyword OR b.`name` LIKE :keyword OR c.`name` LIKE :keyword OR a.`address` LIKE :keyword OR a.`id` = :keyword2)";
            $condition[':keyword'] = $keyword;
            $condition[':keyword2'] = $keyword2;
        }

        if (isset($_GET['status']))
        {
            $where .= $_GET['status'] ? " AND d.`id` IS NOT NULL" : " AND d.`id` IS NULL";
        }

        $sql = "FROM `sup_jlt_hotel` AS a
                    LEFT JOIN `sup_jlt_city` AS b ON a.`city` = b.`code`
                    LEFT JOIN `sup_jlt_country` AS c ON a.`country` = c.`code`
                    LEFT JOIN `ptc_hotel` AS d ON d.`JLT` = a.`id`
                WHERE {$where}";

        $count = $db -> prepare("SELECT COUNT(*) AS `c` {$sql}") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT a.*, b.`name` AS `cityname`, c.`name` AS `countryname`, d.`id` AS `status` {$sql} LIMIT {$limit}") -> execute($condition);

        if(IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()));
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('jlt/hotel');
    break;





    // 房型信息
    case 'room':

        $hotelid = trim($_GET['id']);

        $hotel = $db -> prepare("SELECT * FROM `sup_jlt_hotel` WHERE `id`=:id") -> execute(array(':id'=>$hotelid));
        template::assign('hotel', $hotel[0]);

        $list = $db -> prepare("SELECT * FROM `sup_jlt_room` WHERE `isdel`=0 AND `hotel`=:hotel") -> execute(array(':hotel'=>$hotelid));
        template::assign('list', $list);

        if(IS_AJAX)
            json_return($list);

        template::display('jlt/room');
    break;




    // 国籍要求
    case 'nation':
        if ($_POST)
        {
            $rs = $db -> prepare("UPDATE `sup_jlt_nation` SET `bind`=:bind, `new`=0 WHERE `code`=:code") -> execute(array(':code'=>$_POST['code'], ':bind'=>$_POST['bind']));
            if ($rs === false)
                json_return(null, 1, '保存失败，请重试');
            else
                json_return(1);
        }

        $where = "1=1";
        $condition = array();

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND a.`name` LIKE :keyword";
            $condition[':keyword'] = $keyword;
        }

        if (isset($_GET['status']))
        {
            $where .= $_GET['status'] ? " AND a.`bind` > 0" : " AND a.`bind` = 0";
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `sup_jlt_nation` AS a WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT a.*, b.`name` AS `bindname` FROM `sup_jlt_nation` AS a LEFT JOIN `ptc_nation` AS b ON a.`bind`=b.`id` WHERE {$where} LIMIT {$limit};") -> execute($condition);

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('jlt/nation');
    break;



    // 服务包要求
    case 'package':

        if ($_POST)
        {
            $rs = $db -> prepare("UPDATE `sup_jlt_package` SET `bind`=:bind, `new`=0 WHERE `code`=:code") -> execute(array(':code'=>$_POST['code'], ':bind'=>$_POST['bind']));
            if ($rs === false)
                json_return(null, 1, '保存失败，请重试');
            else
                json_return(1);
        }

        $where = "1=1";
        $condition = array();

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND a.`name` LIKE :keyword";
            $condition[':keyword'] = $keyword;
        }

        if (isset($_GET['status']))
        {
            $where .= $_GET['status'] ? " AND a.`bind` > 0" : " AND a.`bind` = 0";
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `sup_jlt_package` AS a WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT a.*, b.`name` AS `bindname` FROM `sup_jlt_package` AS a LEFT JOIN `ptc_hotel_package` AS b ON a.`bind`=b.`id` WHERE {$where} LIMIT {$limit};") -> execute($condition);

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('jlt/package');
    break;




    // 报价房态
    case 'price':

        $hotels = $db -> prepare("SELECT `id`,`name` FROM `sup_jlt_hotel` WHERE 1=1") -> execute();
        template::assign('hotels', $hotels);


        if (empty($_GET['hotel']))
        {
            $data = array();
            $checkin = strtotime('today');
            $checkout = strtotime('tomorrow');

            template::assign('hotel', '');
        }
        else
        {
            $checkin = strtotime($_GET['checkin']);
            $checkout = strtotime($_GET['checkout']);
            $hotel = (int)$_GET['hotel'];
            $room = (int)$_GET['room'];

            template::assign('hotel', $hotel);

            $rooms = $db -> prepare("SELECT * FROM `sup_jlt_room` WHERE `hotel`=:hotel") -> execute(array(':hotel'=>$hotel));
            template::assign('rooms', $rooms);
            template::assign('room', $room);

            $data = jlt::_price($hotel, $room, '', $checkin, $checkout);
        }

        template::assign('checkin', $checkin);
        template::assign('checkout', $checkout);
        template::assign('data', $data);

        template::display('jlt/price');
    break;


    // 刷新
    case 'refresh':

        ignore_user_abort(true);
        set_time_limit(30);

        $hotel = $db -> prepare("SELECT `id`,`name`,`JLT` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>(int)$_GET['id']));
        $rs = jlt::refresh($hotel[0]['JLT']);

        echo "{$hotel[0]['id']}:{$hotel[0]['name']} -- ".($rs ? 'success' : 'fail');
        exit;


    // 全量刷新
    case 'refresh_all':

        ignore_user_abort(true);
        set_time_limit(0);

        @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
        ob_implicit_flush(1);
        ob_start();
        echo(str_repeat(' ', 2048));

        $hotels = $db -> prepare("SELECT `id` FROM `ptc_hotel` WHERE `JLT` IS NOT NULL ORDER BY `id` ASC") -> execute();
        foreach ($hotels as $k => $h)
        {
            echo (round(($k + 1) / count($hotels) * 100)) , '% ';
            echo curl_file_get_contents('http://121.199.13.135/jlt.php?method=refresh&id='.$h['id'].'&auto='.md5('auto'.date('Y-m-d')), null, 0, 30), '<br />';

            ob_flush();
            flush();
            usleep( 100000 );
        }

        echo 'over';
        exit;


    // 首页
    case 'home':
    default:

        template::display('jlt/home');
    break;
}


?>