<?php
/**
 * JLT PUSH
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

// defined resources url
define('RESOURCES_URL', config('web.resources_url'));

ignore_user_abort(true);
set_time_limit(0);
$data = json_decode(str_replace("'", '"', $GLOBALS["HTTP_RAW_POST_DATA"]), true);

if ($data['usercd'] != jlt::$option['usercd'])
{
    echo json_encode(array('usercd'=>jlt::$option['usercd'], 'authno'=>jlt::$option['authno'], 'success'=>8, 'msg'=>'身份验证失败!')); exit;
}

$roomtypeids = array_filter(explode('/', $data['roomtypeids']));

$rs = jlt::refresh('', $roomtypeids);
if ($rs)
{
    echo json_encode(array('usercd'=>jlt::$option['usercd'], 'authno'=>jlt::$option['authno'], 'success'=>1, 'msg'=>'更新成功!'));
}
else
{
    echo json_encode(array('usercd'=>jlt::$option['usercd'], 'authno'=>jlt::$option['authno'], 'success'=>8, 'msg'=>'更新数据失败!'));
}

?>