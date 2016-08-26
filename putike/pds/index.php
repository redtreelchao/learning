<?php
/**
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */
/**   */
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

// check permission
rbac_user();

// public actions
$method = empty($_GET['method']) ? '' : $_GET['method'];

switch ($method)
{
    // 获取城市
    case 'city':
        $id = $_POST['pid'];

        $db = db(config('db'));
        $city = $db -> prepare("SELECT * FROM `ptc_district` WHERE `pid`=:pid ORDER BY `id` ASC;") -> execute(array(':pid'=>$id));
        json_return($city);

    // 文件上传
    case 'media':
        if ($_POST)
        {
            $rs = media::upload();
            json_return($rs['rs'], $rs['code'], $rs['err']);
        }

        if (isset($_GET['p']))
        {
            $data = media::get_all('image', $_GET['p'], 20);

            if($data['rs'] !== false)
                json_return(array('list'=>$data['rs'], 'page'=>$data['page']));
            else
                json_return(null, 1, '读取失败，请重试！');
        }

        template::assign('select', (isset($_GET['select']) ? (int)$_GET['select']: 1));
        template::assign('link', (isset($_GET['link']) ? 1 : 0));
        template::display('common/media', false); exit;
        break;
}

redirect('./demo.php');
