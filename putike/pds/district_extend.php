<?php
/**
 * 区域规划（地区扩展）
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

template::assign('nav', 'District');
template::assign('subnav', 'list');


switch ($method)
{
    // 编辑
    case 'save':
        // 保存
        if ($_POST)
        {
            $data = array(
                'name'       => trim($_POST['name']),
                'pinyin'     => trim($_POST['pinyin']),
                'type'       => $_POST['type'],
                'pid'        => (int)$_POST['pid'],
                'updatetime' => NOW,
            );

            if (!$data['name']) json_return(null, 1, $type.'不能为空');
            if (!$data['pid']) json_return(null, 1, '提交信息不正确');

            if (!$data['pinyin'])
                $data['pinyin'] = pinyin::get($data['name']);

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $id = $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_district_ext` SET {$sql} WHERE `id`=:id;") -> execute($value);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_district_ext` {$column} VALUES {$sql};") -> execute($value);
                $id = $rs;
            }

            if ($rs !== false)
            {
                api::push('district', $id, '');
                json_return($rs);
            }
            else
            {
                json_return(null, 1, '保存失败，请重试');
            }
        }


    // 读取
    case 'load':

        if (!empty($_GET['id']))
        {
            $district = $db -> prepare("SELECT * FROM `ptc_district_ext` WHERE `id`=:id;") -> execute(array(':id' => (int)$_GET['id']));
            json_return($district[0]);
        }
        break;



    // 列表
    case 'list':
    default:

        $id = (int)$_GET['id'];
        $where = "`pid`=:pid";
        $condition = array(':pid'=>$id);
        template::assign('keyword', '');

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND ( `name` LIKE :keyword OR `pinyin` LIKE :keyword )";
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        $parent = $db -> prepare("SELECT * FROM `ptc_district` WHERE `id`=:id") -> execute(array(':id'=>$id));
        template::assign('city', $parent[0]);

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_district_ext` WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT * FROM `ptc_district_ext` WHERE {$where} ORDER BY `id` ASC LIMIT {$limit};";
        $list = $db -> prepare($sql) -> execute($condition);

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('district/extend/list');
        break;


}

?>