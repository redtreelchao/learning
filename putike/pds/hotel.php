<?php
/**
 * 酒店
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
template::assign('subnav', 'list');


switch ($method)
{
    case 'collection':

        include COMMON_PATH.'hotel_collection.inc.php';
        break;



    // Edit
    case 'edit':
        // Save hotel information
        if ($_POST)
        {
            if ($_POST['position'])
                list($lng, $lat) = explode(',', $_POST['position']);
            else
                $lng = $lat = '';

            $id = (int)$_POST['id'];
            $name = str_replace(array('（', '）'), array('(', ')'), $_POST['name']);

            $data = array(
                'name'      => trim($name),
                'pinyin'    => trim($_POST['pinyin']),
                'en'        => trim($_POST['en']),
                'country'   => (int)$_POST['country'],
                'city'      => (int)$_POST['city'],
                'tel'       => (string)$_POST['tel'],
                'address'   => (string)$_POST['address'],
                'lng'       => (string)$lng,
                'lat'       => (string)$lat,
                'updatetime'=> NOW,
            );

            if (!$data['pinyin'])
            {
                $data['pinyin'] = pinyin::get($name);
            }

            if (!empty($_POST['ELG']))
            {
                $data['ELG'] = trim($_POST['ELG']);
            }

            if (!$data['name']) json_return(null, 1, '酒店名称不能为空');
            if (!$data['country'] || !$data['city']) json_return(null, 1, '请选择国家或城市');
            //if (!$data['tel'] && $data['country'] == 1) json_return(null, 1, '电话号码不能为空');
            if (!$data['address']) json_return(null, 1, '地址不能为空');
            //if (!$data['lat'] || !$data['lng']) json_return(null, 1, '坐标不正确');

            import(CLASS_PATH.'extend/string');
            // if ($data['tel'] && !string::check($data['tel'], 'phone')) json_return(null, 2, '电话号码格式不正确');
            // if (!string::check($data['lat'], 'double') || !string::check($data['lng'], 'double')) json_return(null, 2, '坐标不正确');

            // Check have the same data
            $check = $db -> prepare("SELECT `id` FROM `ptc_hotel_original` WHERE (`name`=:name AND `address`=:address)".($id ? " AND `id`!='{$id}'" : ''))
                         -> execute(array(':name'=>$data['name'], ':address'=>$data['address']));
            if ($check)
                json_return(null, 1, '已存在相似酒店数据，请检查并确认');

            $check = $db -> prepare("SELECT `id` FROM `ptc_hotel` WHERE (`name`=:name AND `address`=:address)".($id ? " AND `id`!='{$id}'" : ''))
                         -> execute(array(':name'=>$data['name'], ':address'=>$data['address']));
            if ($check)
                json_return(null, 1, '已存在相似酒店数据，请检查并确认');


            $db -> beginTrans();

            if ($id)
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = $id;
                $rs = $db -> prepare("UPDATE `ptc_hotel` SET {$sql} WHERE `id`=:id;") -> execute($value);
                if (false === $rs)
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }

                // History
                if (!history($id, 'hotel', '修改了酒店信息', $data))
                {
                    $db -> rollback();
                    json_return(null, 3, '保存失败，请重试');
                }

                // Products about hotel, linkage updating
                $prs = $db -> prepare("UPDATE `ptc_product_item` SET `target`=:city WHERE `objtype`='room' AND `objpid`=:id") -> execute(array(':city'=>$data['city'], ':id'=>$id));
                if (false === $prs)
                {
                    $db -> rollback();
                    json_return(null, 4, '保存失败，请重试');
                }

                // push api log
                api::push('hotel', $id, '');
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_hotel` {$column} VALUES {$sql};") -> execute($value);
                if (!$rs) json_return(null, 2, '保存失败，请重试');

                // History
                if (!history($rs, 'hotel', '创建了酒店', $data))
                {
                    $db -> rollback();
                    json_return(null, 3, '保存失败，请重试');
                }

                // add original data
                $data = array(':id'=>$rs, ':name'=>$data['name'], ':address'=>$data['address'], ':tel'=>$data['tel']);
                $ors = $db -> prepare("INSERT INTO `ptc_hotel_original` (`id`,`name`,`address`,`tel`) VALUES (:id, :name, :address, :tel);") -> execute($data);
                if ($ors === false)
                {
                    $db -> rollback();
                    json_return(null, 4, '保存失败，请重试');
                }

                // push api log
                api::push('hotel', $rs, '');
            }

            if (!$db -> commit())
            {
                $db -> rollback();
                json_return(null, 9, '保存失败，请重试');
            }

            json_return($rs);
        }

        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '酒店信息不存在或已删除');

        $data = $data[0];

        $history = $db -> prepare("SELECT `id`,`time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND `type`='hotel' ORDER BY `time` DESC LIMIT 0,10;") -> execute(array(':id'=>$id));
        template::assign('history', $history);



    // Create hotel information
    case 'new':

        if (!isset($data))
        {
            $data = null;
            $city = array();
        }

        if (!empty($_GET['sup']))
        {
            $supply = strtolower($_GET['sup']);

            $sql = "SELECT a.*,  b.name AS `countryname`, c.name AS `cityname`
                    FROM `sup_{$supply}_hotel` AS a
                        LEFT JOIN `sup_{$supply}_country` AS b ON a.`country` = b.`code`
                        LEFT JOIN `sup_{$supply}_city` AS c ON a.`city` = c.`code`
                    WHERE `id`=:code";
            $data = $db -> prepare($sql) -> execute(array(':code'=>$_GET['code']));

            if ($data)
            {
                $data = $data[0];
                $_city = $db -> prepare("SELECT * FROM `ptc_district` WHERE `name` = :keyword") -> execute(array(':keyword'=>trim($data['cityname'])));
                if ($_city)
                {
                    $data['country'] = $_city[0]['pid'];
                    $data['city']    = $_city[0]['id'];
                }
                $data['id'] = null;
            }
            else
            {
                $data = null;
            }
        }

        if ($data && $data['country'])
            $city = $db -> prepare("SELECT `id`,`name` FROM `ptc_district` WHERE `pid`=:id") -> execute(array(':id'=>$data['country']));
        else
            $city = array();

        template::assign('data', $data);
        template::assign('id', $data ? $data['id'] : null);
        template::assign('city', $city);

        $country = $db -> prepare("SELECT * FROM `ptc_district` WHERE `pid`=0 ORDER BY `id` ASC;") -> execute();
        template::assign('country', $country);

        if (in_array($_SESSION['uid'], array(2,4,33,39,27,97)))
            template::display('hotel/editbyadmin');
        else
            redirect('http://info.putike.cn/'); //template::display('hotel/edit');
        break;




    // 读取历史
    case 'history':
        $hotelid = (int)$_GET['hotel'];
        $page = (int)$_GET['page'];
        if (!$page) $page = 1;

        $limit = 10;
        $start = $limit * $page;

        $history = $db -> prepare("SELECT `id`,FROM_UNIXTIME(`time`, '%m-%d %H:%i') AS `time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND `type`='hotel' ORDER BY `time` DESC LIMIT {$start},{$limit};") -> execute(array(':id'=>$hotelid));

        if ($history !== false)
            json_return($history);
        else
            json_return(null, 1, '读取失败，请重试');

        break;



    // Delete data
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];

            $db = db(config('db'));

            $product = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_product_item` WHERE `objpid`=:hotel AND `objtype`='room'") -> execute(array(':hotel' => $id));
            if ($product[0]['c']) json_return(null, 1, '有'.$product[0]['c'].'个产品关联，请撤销关联后删除');

            $db -> beginTrans();

            $rs = delete('ptc_hotel', "`id`='{$id}'", false);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 1, '操作失败，请重试..');
            }

            $rs = delete('ptc_hotel_room_type', "`hotel`='{$id}'", false);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 2, '操作失败，请重试..');
            }

            $rs = delete('ptc_hotel_room', "`hotel`='{$id}'", false);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 3, '操作失败，请重试..');
            }

            if ($db -> commit())
                json_return(1);
            else
                json_return(null, 9, '操作失败，请重试..');
        }

        break;



    // Bind hotels' supply code
    case 'bind':

        include COMMON_PATH.'hotel_bind.inc.php';
        break;



    // Manger links
    case 'link':

        include COMMON_PATH.'hotel_link.inc.php';
        break;



    // Tags
    case 'tag':

        include COMMON_PATH.'hotel_tag.inc.php';
        break;



    // Hotel list
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

        $join = "LEFT JOIN `ptc_district` AS b ON a.`country` = b.`id`
                 LEFT JOIN `ptc_district` AS c ON a.`city` = c.`id`";

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_hotel` AS a {$join} WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.*, b.name AS `country`, c.`name` AS `city`, c.`province` FROM `ptc_hotel` AS a
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

        $supplies = supplies();
        template::assign('supplies', $supplies);

        template::display('hotel/list');
        break;


}
?>
