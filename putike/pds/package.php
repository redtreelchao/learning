<?php
/**
 * PACKAGE
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

$method = empty($_GET['method']) ? 'list' : $_GET['method'];

template::assign('nav', 'Hotel');
template::assign('subnav', 'package');

switch ($method)
{
    case 'edit':
        if($_POST)
        {
            if (!trim($_POST['name']))
                json_return(null, 1, '请输入增值信息');

            if (!$_POST['id'])
            {
                $rs = $db -> prepare("INSERT INTO `ptc_hotel_package` (`name`, `updatetime`) VALUES (:name, :time);")
                          -> execute(array(':name'=>trim($_POST['name']), ':time'=>NOW));
            }
            else
            {
                $rs = $db -> prepare("UPDATE `ptc_hotel_package` SET `name`=:name, `updatetime`=:time WHERE `id`=:id") -> execute(array(':id'=>$_POST['id'], ':name'=>$_POST['name'], ':time'=>NOW));
            }

            if (false === $rs) json_return(null, 9, '保存失败，请重试');

            if ($rs > 4000)
            {
                import(CLASS_PATH.'extend/email');
                $email = new email('PUTIKE API SYSTEM<system@putike.cn>', 'smtp.exmail.qq.com', 25, 'system@putike.cn', 'ptk123456');
                $email -> send('nolan.zhou@putike.cn', "§§ Package ID is close to  the threshold", charset_convert("国籍要求数据ID已接近阈值，请及时调整。", 'utf-8', 'gb2312'), '', 'jacky.yan@putike.cn');
            }

            json_return($rs);
        }

        redirect('/package.php');
        break;





    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            if (delete('ptc_hotel_package', "`id`='{$id}'"))
                json_return(1);
            else
                json_return(null, 1, '操作失败，请重试..');
        }

        break;





    case 'list':
    default:

        $where = "`id` > 0";
        $condition = array();
        template::assign('keyword', '');

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND `name` LIKE :keyword";
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_hotel_package` WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT * FROM `ptc_hotel_package` WHERE {$where} ORDER BY `id` DESC LIMIT {$limit};") -> execute($condition);

        // 关联信息
        $supplies = supplies();
        foreach ($list as $k => $v)
        {
            $list[$k]['binds'] = array();
            foreach ($supplies as $supkey => $sup)
            {
                $supply = strtolower($supkey);
                $bind = $db -> prepare("SELECT `code`,`name` FROM `sup_{$supply}_package` WHERE `bind`=:bind") -> execute(array(':bind'=>$v['id']));
                if ($bind)
                {
                    foreach ($bind as $b)
                    {
                        $list[$k]['binds'][] = array('code'=>$b['code'], 'name'=>"{$sup}: {$b['name']}");
                    }
                }
            }
        }

        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()), 0, $keyword);
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('package/list');
    break;




}


?>
