<?php
/**
 * Login / Logout
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */

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

$method = !empty($_GET['method']) ? $_GET['method'] : 'login';

switch ($method)
{
    // logout
    case 'logout':

        // destory session
        session_unset();
        session_destroy();

        setcookie('sess', '', NOW - 86400);

        // redirect login page
        redirect('./login.php');
        break;


    // login
    case 'login':
    default:

        if ($_POST)
        {
            if (empty($_POST['username']) || empty($_POST['password']))
                json_return(null, 1, '用户名或密码不能为空');

            import(CLASS_PATH.'extend/string');
            if (string::check($_POST['username'], 'email')) $key = 'email';
            else if (string::check($_POST['username'], 'mobile')) $key = 'tel';
            else $key = 'username';

            $db = db(config('db'));

            $user = $db -> prepare("SELECT `id`,`name`,`password`,`md`,`role` FROM `rbac_user` WHERE BINARY `{$key}`=:user") -> execute(array(':user'=>$_POST['username']));

            if (!$user || $user[0]['password'] != md5(md5($_POST['password']).$user[0]['md']))
            {
                json_return(null, 1, '帐号或用户名不正确');
            }
            else
            {
                $_SESSION['uid'] = $user[0]['id'];
                $_SESSION['name'] = $user[0]['name'];
                $_SESSION['role'] = $user[0]['role'];
				$_SESSION['token'] = 'U'.$user[0]['id'].'M'.authcode($user[0]['password'].NOW, $user[0]['md'], 'ENCODE');

                // remember user to login in one week
                if (!empty($_POST['remember']))
                {
                    $ck = $user[0]['id'] % 4;
                    $md = $user[0]['id'].'p'.substr($user[0]['password'], $ck).strtolower(substr($user[0]['md'], -$ck));
                    setcookie('sess', $md, NOW + 86400 * 7);
                }

                json_return(1);
            }
        }

        $referer = '/index.php';
        if (!empty($_GET['referer']) || !empty($_SERVER['HTTP_REFERER']))
        {
            $_referer = !empty($_GET['referer']) ? urldecode($_GET['referer']) : $_SERVER['HTTP_REFERER'];
            $_url = parse_url($_referer);
            if ((empty($_url['host']) || $_url['host'] == $_SERVER['HTTP_HOST']) && (empty($_url['path']) || !strpos($_url['path'], 'login.php')))
                $referer = $_referer;
        }

        if (!empty($_COOKIE['sess'])) redirect($referer);

        template::assign('redirect', $referer);
        template::display('login');
}

?>
