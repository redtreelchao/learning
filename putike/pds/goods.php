<?php
/**
 * 大宗商品
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

template::assign('nav', 'Goods');
template::assign('subnav', 'list');

switch ($method)
{
    // 编辑
    case 'edit':
        // 保存
        if ($_POST)
        {
            $data = array(
                'name'  => (string)trim($_POST['name']),
                'type'  => (string)trim($_POST['type']),
                'made'  => (string)trim($_POST['made']),
                'supply'=> (string)trim($_POST['supply']),
                'updatetime'=> NOW,
            );

            if (!$data['name']) json_return(null, 1, '商品名称不能为空');
            if (!$data['type']) json_return(null, 1, '请选择商品类型');

            $check = $db -> prepare("SELECT `id` FROM `ptc_goods` WHERE `name`=:name".($_POST['id'] ? " AND `id`!='{$_POST['id']}'" : ''))
                         -> execute(array(':name'=>$data['name']));
            if ($check)
                json_return(null, 1, '已存在相同商品数据，请检查并确认');

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_goods` SET {$sql} WHERE `id`=:id;") -> execute($value);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_goods` {$column} VALUES {$sql};") -> execute($value);
            }

            if ($rs)
                json_return($rs);
            else
                json_return(null, 1, '保存失败，请重试');
        }


        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_goods` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '商品信息不存在或已删除');

        $data = $data[0];



    // 新建
    case 'new':

        if (!isset($data))
        {
            $data = null;
        }

        template::assign('data', $data);
        template::assign('id', $data ? $data['id'] : null);

        template::display('goods/edit');
        break;



    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            if (delete('ptc_goods', "`id`='{$id}'"))
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
            $where .= " AND a.`name` LIKE :keyword OR a.`id` = :id";
            $condition[':id'] = (int)$_GET['keyword'];
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_goods` AS a WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.*
                FROM `ptc_goods` AS a
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

        template::display('goods/list');
        break;


}

