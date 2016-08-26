<?php
// 接口文档
// session start
define("SESSION_ON", false);

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

$type = empty($_GET['type']) || $_GET['type'] == 'bus' ? 'business' : 'personal';
template::assign('type', $type);

// check permission
if ($type === 'business')
    rbac_user();



if (!empty($_GET['view']) && $_GET['view'] == 'log')
{
    $db = db::init();
    $list = $db -> prepare("SELECT * FROM `p_api_log` WHERE `time` >= :today ORDER BY `id` DESC;") -> execute(array(':today'=>strtotime('today 00:00:00')));
    template::assign('list', $list);
    template::display('apilog');
    exit;
}


$error = api::$error_msg;

if (isset($_GET['method']))
{
    $method = trim($_GET['method']);
    list($class, $func) = explode('_', $method, 2);

    $args = api::$func[$class][$func];
    template::assign('args', $args);

    $help = document::$func[$class][$func];
    template::assign('help', $help['args']);
    template::assign('array', $help['array']);

    $error = $error + $class::$error_msg;
}

template::assign('error', $error);

$methods = api::$func;
template::assign('methods', $methods);

$limit = api::$func_limit;
template::assign('limit', $limit);

$doc = document::$func;
template::assign('doc', $doc);

template::display('document');
