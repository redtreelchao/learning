<?php
/**
 * APP½Ó¿Ú
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */

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


//$_starttime = microtime(true);

// post all order data
$data = empty($GLOBALS['HTTP_RAW_POST_DATA']) ? false : $GLOBALS['HTTP_RAW_POST_DATA'];
if ($data)
{
    api::parsexml($data);
}
else
{
    api::request();
}

//$_endtime = microtime(true);
//log_message('[request] Time:'.date('H:i:s').' Use:'.($_endtime - $_starttime).' Url:'.$_SERVER['REQUEST_URI']."\r\n", 0, 'Debug');
