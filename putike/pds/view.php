<?php
/**
 * 酒店
 +-----------------------------------------
 * @author han
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

template::assign('nav', 'View');
template::assign('subnav', 'list');

switch ($method)
{
    // Edit view
    case 'edit':
        if($_POST)
        {
            if ($_POST['position'])
                list($lng, $lat) = explode(',', $_POST['position']);
            else
                $lng = $lat = 0;

            $id = (int)$_POST['id'];
            $name = str_replace(array('（', '）'), array('(', ')'), $_POST['name']);

            $data = array(
                'name'      => trim($name),
                'pinyin'    => trim($_POST['pinyin']),
                'country'   => (int)$_POST['country'],
                'city'      => (int)$_POST['city'],
                'tel'       => (string)$_POST['tel'],
                'address'   => (string)$_POST['address'],
                'type'      => (string)$_POST['type'],
                'lng'       => (string)$lng,
                'lat'       => (string)$lat,
                'updatetime'=> NOW,
            );

            if (!$data['pinyin'])
            {
                $data['pinyin'] = pinyin::get($name);
            }

            if (!$data['name']) json_return(null, 1, '景区/体验名称不能为空');
            if (!$data['country'] || !$data['city']) json_return(null, 1, '请选择国家或城市');
            if (!$data['lat'] || $data['lng'] === '') json_return(null, 1, '坐标不正确');

            import(CLASS_PATH.'extend/string');
            if ($data['tel'] && !string::check($data['tel'], 'phone')) json_return(null, 2, '电话号码格式不正确');
            if (!string::check($data['lat'], 'double') || !string::check($data['lng'], 'double')) json_return(null, 2, '坐标不正确');

            // Check have the same data
            $check = $db -> prepare("SELECT `id` FROM `ptc_view` WHERE (`name`=:name OR `address`=:address)".($id ? " AND `id`!='{$id}'" : '')) -> execute(array(':name'=>$data['name'], ':address'=>$data['address']));
            if ($check)
                json_return(null, 1, '已存在相似景区/体验数据，请检查并确认');

            $db -> beginTrans();

            if ($id)
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = $id;
                $rs = $db -> prepare("UPDATE `ptc_view` SET {$sql} WHERE `id`=:id;") -> execute($value);
                if (false === $rs) json_return(null, 1, '保存失败，请重试');

                // Products about hotel, linkage updating
                $prs = $db -> prepare("UPDATE `ptc_product_item` SET `target`=:city WHERE `objtype`='view' AND `objid`=:id") -> execute(array(':city'=>$data['city'], ':id'=>$id));
                if (false === $prs)
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }

                // Tag update
                $trs = $db -> prepare("UPDATE `ptc_tag` SET `name`=:name WHERE `code`=:code") -> execute(array(':name'=>$data['name'], ':code'=>'V'.$id));
                if (false === $trs)
                {
                    $db -> rollback();
                    json_return(null, 3, '保存失败，请重试');
                }
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_view` {$column} VALUES {$sql};") -> execute($value);
                if (!$rs) json_return(null, 1, '保存失败，请重试');

                // Tag insert
                $tag = array(
                    'name'      => $name,
                    'code'      => 'V'.$rs,
                    'type'      => 'view',
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

            // push api log
            api::push('view', $id, '');

            if (!$db -> commit())
            {
                $db -> rollback();
                json_return(null, 9, '保存失败，请重试');
            }

            json_return($rs);
        }


        $id = (int)$_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_view` WHERE id = :id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '景区/体验信息不存在或已删除');

        $data = $data[0];


    // Add a new view
    case 'add':

        if (!isset($data))
        {
            $data = null;
            $city = array();
        }

        if ($data && $data['country'])
            $city = $db -> prepare("SELECT `id`,`name` FROM `ptc_district` WHERE `pid`=:id") -> execute(array(':id'=>$data['country']));
        else
            $city = array();

        template::assign('city', $city);

        $country = $db -> prepare("SELECT * FROM `ptc_district` WHERE `pid`=0 ORDER BY `id` ASC;") -> execute();
        template::assign('country', $country);

        template::assign('data', $data);
        template::display('view/edit');
    break;



    // Delete data
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];

            $db = db(config('db'));

            $product = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_product_item` WHERE `objid`=:id AND `objtype`='view'") -> execute(array(':id' => $id));
            if ($product[0]['c']) json_return(null, 1, '有'.$product[0]['c'].'个产品关联，请撤销关联后删除');

            $rs = delete('ptc_view', "`id`='{$id}'", false);

            $rs = delete('ptc_tag', "`code`='V{$id}'", false);

            if ($rs !== false)
                json_return(1);
            else
                json_return(null, 9, '操作失败，请重试..');
        }

        break;



    // Search list
    case 'list':
    default:

        $where = "1=1";
        $condition = array();
        template::assign('keyword','');

        if (!empty($_GET['keyword']))
        {
            if (is_numeric($_GET['keyword']))
            {
                $where .= " AND a.id = :id";
                $condition[':id'] = (int)$_GET['keyword'];
            }
            else
            {
                $keyword = '%'.$_GET['keyword'].'%';
                $where .= " AND ( a.`name` LIKE :keyword OR b.`name` LIKE :keyword OR c.`name` LIKE :keyword OR a.`pinyin` LIKE :keyword OR a.`address` LIKE :keyword OR a.`teL` LIKE :keyword )";
                $condition[':keyword'] = $keyword;
            }
            template::assign('keyword', $_GET['keyword']);
        }

        if (!empty($_GET['type']))
        {
            $where .= " AND a.`type`=:type";
            $condition[':type'] = trim($_GET['type']);
        }

        $join = "LEFT JOIN `ptc_district` AS b ON a.`country` = b.`id`
                 LEFT JOIN `ptc_district` AS c ON a.`city` = c.`id`
                 LEFT JOIN `ptc_tag` AS t ON t.`code` = CONCAT('V', a.`id`)";

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_view` AS a {$join} WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.*, b.name AS `country`, c.`name` AS `city`, c.`province`, t.`id` AS `tag` FROM `ptc_view` AS a
                    {$join}
                WHERE {$where}
                ORDER BY a.`id` DESC
                LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);

        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()), 0, $keyword);
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('view/list');
    break;
}
