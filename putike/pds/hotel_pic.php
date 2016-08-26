<?php
/**
 * Created by PhpStorm.
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

$method = empty($_GET['method']) ? 'list' : $_GET['method'];
$id=$_GET['id'];

template::assign('nav', 'hotel_pic');
template::assign('method', $method);
template::assign('token', $_SESSION['token']);
template::assign('id', $id);


switch ($method){
    //
    case 'list':
            template::display('hotel_pic/list');
        break;
    //
    case 'rescue':
            template::display('hotel_pic/rescue');
        break;
    //
    case 'notice':
        template::display('hotel_pic/notice');
        break;
    //
    case 'class':
            template::display('hotel_pic/class');
        break;
    case 'detail':
            template::display('hotel_pic/detail');
        break;
    case 'gallery':
            template::display('hotel_pic/gallery');
        break;
}



