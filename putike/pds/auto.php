<?php
/**
 * Auto EveryDay
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

ignore_user_abort(true);
set_time_limit(120);

header("Content-type:text/html; charset=utf-8");

$db = db(config('db'));

$day = date('Y-m-d');
$today = strtotime("{$day} 00:00:00");

// 日志
function task_log($log)
{
    global $day;

    $logfile = "task-everyday-{$day}.log";
    file_put_contents(PT_PATH.'log/'.$logfile, $log."\r\n", FILE_APPEND);
}


// 下架产品
function product()
{
    global $db, $today;

    $products = $db -> prepare('SELECT `id`,`type`,`payment`,`org` FROM `ptc_product` WHERE `status`=1 AND `end`<:time') -> execute(array(':time'=>$today));
    if (!$products) return true;

    $db -> beginTrans();
    $r = $db -> prepare('UPDATE `ptc_product` SET `status`=-1, `updatetime`=:now, `offlinetime`=:now WHERE `status`=1 AND `end`<:time') -> execute(array(':time'=>$today, ':now'=>NOW));

    if ($r === false)
    {
        $db -> rollback();
        return false;
    }

    foreach ($products as $v)
    {
        if( !api::push($v['type'], $v['id'], $v['payment'], $v['org'], false) )
        {
            $db -> rollback();
            return false;
        }
    }

    if ($db -> commit())
        return true;
    else
        return false;
}


// 完成订单
function order()
{
    global $db, $today;

    include_once PT_PATH.'hook/hook.php';

    // 酒店券类
    $sql = 'SELECT o.* FROM `ptc_order_hotel` h
                LEFT JOIN `ptc_order_room` r ON h.`id` = r.`pid`
                LEFT JOIN `ptc_order` o ON h.`orderid` = o.`id`
            WHERE h.`supply` = "TICKET" AND r.`ticket` = 8 AND r.`checkout` < :now
            GROUP BY h.`orderid`';

    $orders = $db -> prepare($sql) -> execute(array(':now'=>$today));
    foreach ($orders as $order)
    {
        $rs = null;
        $rs = filter::apply('order_complete', $rs, $order, 'hotel_ticket');
        if (!$rs)
            task_log("{$order['order']} -- fail.");
        else
            task_log("{$order['order']} -- success.");
    }

    // 酒店预付
    $sql = 'SELECT o.* FROM `ptc_order_hotel` h
                LEFT JOIN `ptc_order` o ON h.`orderid` = o.`id`
            WHERE h.`product` = 0 AND h.`status`=8 AND h.`checkout` < :now AND h.`supply` != "TICKET" AND h.`supply` != "PRODUCT"
            GROUP BY h.`orderid`';

    $orders = $db -> prepare($sql) -> execute(array(':now'=>$today));
    foreach ($orders as $order)
    {
        $rs = null;
        $rs = filter::apply('order_complete', $rs, $order, 'hotel_prepay');
        if (!$rs)
            task_log("{$order['order']} -- fail.");
        else
            task_log("{$order['order']} -- success.");
    }

    // 机加酒
    $sql = 'SELECT o.* FROM `ptc_order_hotel` h
                LEFT JOIN `ptc_order` o ON h.`orderid` = o.`id`
            WHERE h.`producttype` = 4 AND h.`status`=8 AND h.`checkout` < :now AND h.`supply` = "PRODUCT"
            GROUP BY h.`orderid`';

    $orders = $db -> prepare($sql) -> execute(array(':now'=>$today));
    foreach ($orders as $order)
    {
        $rs = null;
        $rs = filter::apply('order_complete', $rs, $order, 'hotel_flight_prepay');
        if (!$rs)
            task_log("{$order['order']} -- fail.");
        else
            task_log("{$order['order']} -- success.");
    }
}



// 删除旧价格
function delete_price()
{
    global $db, $today;

    $r = $db -> prepare('DELETE FROM `ptc_hotel_price_date` WHERE `date` < :time AND `supply` != "EBK"') -> execute(array(':time'=>$today));

    if ($r === false)
    {
        return false;
    }

    return true;
}


// 更新新价格
function refresh_price()
{
    $md = md5('auto'.date('Y-m-d'));

    curl_file_get_contents('http://121.199.13.135/hmc.php?method=refresh_all&auto='.$md, null, null, 1);

    sleep(3600);

    curl_file_get_contents('http://121.199.13.135/jlt.php?method=refresh_all&auto='.$md, null, null, 1);

    sleep(3600);

    curl_file_get_contents('http://121.199.13.135/cnb.php?method=refresh_all&auto='.$md, null, null, 1);

    sleep(3600);

    curl_file_get_contents('http://121.199.13.135/elg.php?method=refresh_all&auto='.$md, null, null, 1);
}


task_log('Discontinue carrying products -- '.(product() ? 'success' : 'fail'));

task_log('Order auto set complete status -- start:');

order();

task_log('Order auto set complete status -- end.');

task_log('Delete old prices -- '.(delete_price() ? 'success' : 'fail'));

task_log('Refresh supplies prices -- start. Please view erery supply log.');

refresh_price();



?>
