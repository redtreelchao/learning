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
rbac_user();


$db = db(config('db'));

$method = empty($_GET['method']) ? 'home' : $_GET['method'];

template::assign('nav', 'Api');

switch ($method)
{
    case 'account':

        template::assign('keyword', '');

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_api_account` WHERE 1=1") -> execute();

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT a.*, b.`name` AS `orgname` FROM `ptc_api_account` AS a LEFT JOIN `ptc_org` AS b ON a.`org` = b.`id` WHERE 1=1 LIMIT {$limit};") -> execute();
        template::assign('list', $list);
        template::assign('page', $page -> show());

        template::assign('subnav', 'account');
        template::display('api/account');
        break;


    case 'price':

        set_time_limit(20);

        $supply = trim($_POST['supply']);
        $hotel  = (int)$_POST['hotel'];
        $room   = (int)$_POST['room'];
        $checkin = strtotime($_POST['checkin']);
        $night  = (int)$_POST['night'];

        if (!$supply || !$hotel || !$room || !$checkin || !$night)
            json_return(null, 1, '请求参数有误，请重试');

        if ($supply != 'EBK')
        {
            $hotel = $db -> prepare("SELECT `id`,`{$supply}` FROM `ptc_hotel` WHERE `id`=:hotel") -> execute(array(':hotel'=>$hotel));
            if (!$hotel || !$hotel[0][$supply])
                json_return(null, -1, '酒店未关联');

            $rooms = $db -> prepare("SELECT `key` FROM `ptc_hotel_room` WHERE `type`=:room AND `supply`=:supply") -> execute(array(':room'=>$room, ':supply'=>$supply));
            if (!$rooms)
                json_return(null, -1, '没有关联房型');

            $checkout = $checkin+$night*86400;

            $class = strtolower($supply);
            $key = call_user_func_array(array($class, 'parsekey'), array($rooms[0]['key']));
            $rs = call_user_func_array(array($class, 'refresh'), array($hotel[0][$supply], $key['room'], $checkin, $checkout));
            if (!$rs)
                json_return(null, -1, '更新价格信息失败');

            // include hotel prepay hook
            include_once PT_PATH.'hook/hotel_prepay/hook.php';

            $sql = "SELECT p.`uncombine`, p.`roomtype` AS `room`, r.`name` AS `roomname`, p.`bed`, p.`payment`, p.`nation`, n.`name` AS `nationname`, p.`start`, p.`end`, p.`min`, p.`advance`, p.`supply`
                    FROM `ptc_hotel_price_date` AS p
                        LEFT JOIN `ptc_nation` AS n ON p.`nation` = n.`id`
                        LEFT JOIN `ptc_hotel_room_type` AS r ON p.`roomtype` = r.`id`
                    WHERE p.`hotel`=:hotel AND p.`payment`=1 AND p.`roomtype`=:roomtype AND p.`supply`=:supply AND p.`date` >= :checkin AND p.`date` < :checkout AND p.`close` = 0
                    GROUP BY p.`uncombine`
                    ORDER BY p.`bed` DESC";
            $products = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel[0]['id'], ':roomtype'=>$room, ':supply'=>$supply, ':checkin'=>$checkin, ':checkout'=>$checkout));

            $data = array();
            foreach ($products as $k => $product)
            {
                $items = hotel_prepay_hook::prices($product['uncombine'], $checkin, $checkout);
                $product['roomname'] = roomname($product['roomname'], $product['bed']);
                foreach ($items as $v)
                {
                    $data[] = array_merge($product, $v);
                }
            }
        }
        else
        {
            $data = array();
        }

        json_return($data);
        break;

    case 'document':

        template::assign('subnav', 'document');
        template::display('api/document');
        break;



    case 'home':
    default:

        template::assign('subnav', 'api');
        template::display('api/index');
}


