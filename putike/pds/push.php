<?php
// สýพÝอฦหอ
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

ignore_user_abort(true);
set_time_limit(10);
ini_set('memory_limit', '30M');

$db = db(config('db'));

$where = '`status` = 0 AND `times` < 4 AND `time` > :time';
$condition = array(':time'=>NOW-3600);

if (!empty($_GET['id']))
{
    $where = '`id`=:id';
    $condition = array(':id'=>$_GET['id']);
}

$list = $db -> prepare("SELECT * FROM `ptc_api_push` WHERE {$where}") -> execute($condition);

foreach($list as $k => $v)
{
    $result = trim(curl_file_get_contents($v['url'], null, null, 10));
    $times = (int)$v['times'] + 1;
    $status = $result == 'ok' ? 1 : 0;

    $rs = $db -> prepare("UPDATE `ptc_api_push` SET `status`=:status, `times`=:times, `result{$times}`=:result, `time`=:time WHERE `id`=:id")
              -> execute(array(':id'=>$v['id'], ':status'=>$status, ':times'=>$times, ':result'=>(string)$result, ':time'=>NOW));

    if ($result != 'ok' && $times == 4 && strpos($v['url'], 'type=order&id='))
    {
        $url = parse_url($v['url']);
        parse_str($url['query'], $arg);
        sms::send($arg['id'], 'order_push_fail', array(), '18930620341');
    }

    echo "{$v['org']} [$status]: {$result}\n";
}


?>