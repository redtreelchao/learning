<?php

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

if ($_POST)
{
    $db -> beginTrans();

    if ($_POST['password'])
    {
        if ($_POST['password'] != $_POST['_password'])
            json_return(null, 1, '两次密码输入不一致');

        $rs = $db -> prepare("UPDATE `rbac_user` SET `password`=MD5(CONCAT(MD5(:password), `md`)) WHERE `id`=:id") -> execute(array(':id'=>$_SESSION['uid'], ':password'=>$_POST['password']));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 1, '保存失败，请重试');
        }
    }

    if ($db -> commit())
        json_return(1);
    else
        json_return(null, 9, '保存失败，请重试');
}

template::display('setting');