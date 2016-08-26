<?php
/**
 * 车辆
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

template::assign('nav', 'Auto');
template::assign('subnav', 'list');

switch ($method)
{
    // 编辑
    case 'edit':
        // 保存
        if ($_POST)
        {
            $data = array(
                'code'      => (string)trim($_POST['code']),
                'company'   => (string)trim($_POST['company']),
                'updatetime'=> NOW,
            );

            if (!$data['code']) json_return(null, 1, '车辆标识不能为空');

            $check = $db -> prepare("SELECT `id` FROM `ptc_auto` WHERE `code`=:code".($_POST['id'] ? " AND `id`!='{$_POST['id']}'" : ''))
                         -> execute(array(':code'=>$data['code']));
            if ($check)
                json_return(null, 1, '已存在相同车辆数据，请检查并确认');

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_auto` SET {$sql} WHERE `id`=:id;") -> execute($value);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_auto` {$column} VALUES {$sql};") -> execute($value);
            }

            if ($rs)
                json_return($rs);
            else
                json_return(null, 1, '保存失败，请重试');
        }


        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_auto` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '车辆信息不存在或已删除');

        $data = $data[0];



    // 新建
    case 'new':

        if (!isset($data))
        {
            $data = null;
            $city = array();
        }

        template::assign('data', $data);
        template::assign('id', $data ? $data['id'] : null);

        template::display('auto/edit');
        break;



    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            if (delete('ptc_auto', "`id`='{$id}'"))
                json_return(1);
            else
                json_return(null, 1, '操作失败，请重试..');
        }

        break;



    // 列表
    case 'list':
    default:

        $where = "1=1";
        $condition = array();
        template::assign('keyword','');

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND a.`code` LIKE :keyword OR a.`id` = :id";
            $condition[':id'] = (int)$_GET['keyword'];
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_auto` AS a WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.*
                FROM `ptc_auto` AS a
                WHERE {$where}
                ORDER BY a.`id` DESC
                LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);
        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()));
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('auto/list');
        break;


}

