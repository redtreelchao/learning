<?php
/**
 * 国家城市
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
            if (!empty($_POST['pos']))
                list($lng, $lat) = explode(',', $_POST['pos']);
            else
                $lng = $lat = 0;

            $data = array(
                'name'      => trim($_POST['name']),
                'pinyin'    => trim($_POST['pinyin']),
                'en'        => trim($_POST['en']),
                'pid'       => (int)$_POST['pid'],
                'lng'       => (string)$lng,
                'lat'       => (string)$lat,
                'province'       => trim($_POST['province']),
            );

            $type = $data['pid'] ? '城市' : '国家';

            if (!$data['name']) json_return(null, 1, $type.'不能为空');
            if (!$data['pinyin']) json_return(null, 1, '拼音不能为空');
            if ($data['pid'] && (!$data['lng'] || !$data['lat'])) json_return(null, 1, '城市坐标不能为空');

            $db -> beginTrans();

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $id = $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_district` SET {$sql} WHERE `id`=:id;") -> execute($value);
                if (false === $rs) json_return(null, 1, '保存失败，请重试');

                // Tag update
                $trs = $db -> prepare("UPDATE `ptc_tag` SET `name`=:name WHERE `code`=:code") -> execute(array(':name'=>$data['name'], ':code'=>'D'.$id));
                if (false === $trs)
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_district` {$column} VALUES {$sql};") -> execute($value);
                if (!$rs) json_return(null, 1, '保存失败，请重试');

                // Tag insert
                $tag = array(
                    'name'      => $data['name'],
                    'code'      => 'D'.$rs,
                    'type'      => 'district',
                    'editor'    => 'tag',
                    'default'   => 1,
                    'pid'       => NULL,
                );
                list($column, $sql, $value) = array_values(insert_array($tag));
                $trs = $db -> prepare("INSERT INTO `ptc_tag` {$column} VALUES {$sql}") -> execute($value);
                if (false === $trs)
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }
            }

            if (!$db -> commit())
            {
                $db -> rollback();
                json_return(null, 9, '保存失败，请重试');
            }
            else
            {
                json_return($rs);
            }
        }


    // 读取
    case 'load':

        if (!empty($_GET['id']))
        {
            $district = $db -> prepare("SELECT * FROM `ptc_district` WHERE `id`=:id;") -> execute(array(':id' => (int)$_GET['id']));
            json_return($district[0]);
        }
        break;



    // 列表
    case 'list':
    default:

        $where = "`pid`=0";
        $condition = array();
        template::assign('keyword','');

        if (!empty($_GET['id']))
        {
            $where = "`pid`=:pid";
            $condition[':pid'] = (int)$_GET['id'];

            $country = $db -> prepare("SELECT `id`,`name` FROM `ptc_district` WHERE `id`=:id AND `pid`=0;") -> execute(array(':id' => (int)$_GET['id']));
            template::assign('country', $country[0]);
        }

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND ( `name` LIKE :keyword OR `pinyin` LIKE :keyword )";
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_district` WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT * FROM `ptc_district` WHERE {$where} ORDER BY `id` ASC LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('district/list');
        break;


}


?>
