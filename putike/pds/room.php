<?php
/**
 * 房型
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
template::assign('subnav', 'room');


switch ($method)
{
    // 编辑
    case 'edit':
        // 保存
        if ($_POST)
        {
            $data = array(
                'hotel'     => (int)$_POST['hotel'],
                'name'      => trim($_POST['name']),
                'updatetime'=> NOW,
            );

            $db -> beginTrans();

            if ($_POST['id'])
            {
                $id = (int)$_POST['id'];

                $old = $db -> prepare("SELECT `name` FROM `ptc_hotel_room_type` WHERE `id`=:id;") -> execute(array(':id'=>$id));

                unset($data['hotel']);
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = $id;
                $rs = $db -> prepare("UPDATE `ptc_hotel_room_type` SET {$sql} WHERE `id`=:id;") -> execute($value);

                $history_message = "更新房型名 “{$old[0]['name']}” 为 “{$data['name']}”";
                $history_data    = array('oldname'=>$old[0]['name'], 'newname'=>$data['name']);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_hotel_room_type` {$column} VALUES {$sql};") -> execute($value);
                $data['id'] = $rs;

                $history_message = "创建房型 “{$data['name']}”";
                $history_data    = array('hotel'=>$data['hotel'], 'name'=>$data['name']);
            }

            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            // History
            if (!history((int)$_POST['hotel'], 'room', $history_message, $history_data))
            {
                $db -> rollback();
                json_return(null, 2, '保存失败，请重试');
            }

            if ($db -> commit())
                json_return($data);
            else
                json_return(null, 9, '保存失败，请重试');
        }


        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_hotel_room_type` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '酒店信息不存在或已删除');

        $data = $data[0];


    // 新建
    case 'new':

        if (!isset($data))
        {
            $data = null;
        }

        template::assign('data', $data);

        template::display('hotel/edit');
        break;



    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];

            $db -> beginTrans();

            $product = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_product_item` WHERE `objid`=:room AND `objtype`='room'") -> execute(array(':room' => $id));
            if ($product[0]['c']) json_return(null, 1, '有'.$product[0]['c'].'个产品关联，请撤销关联后删除');

            if (!delete('ptc_hotel_room_type', "`id`='{$id}'", false))
            {
                $db -> rollback();
                json_return(null, 1, '操作失败，请重试..');
            }

            $rs = $db -> prepare("UPDATE `ptc_hotel_room` SET `type`=0 WHERE `type`=:id;") -> execute(array(':id'=>$id));
            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 2, '操作失败，请重试..');
            }

            $rs = $db -> prepare("UPDATE `ptc_hotel_price_date` SET `roomtype`=0 WHERE `roomtype`=:id;") -> execute(array(':id'=>$id));

            if (false !== $rs && $db -> commit())
            {
                json_return(1);
            }
            else
            {
                $db -> rollback();
                json_return(null, 9, '操作失败，请重试..');
            }
        }

        break;




    // 同步更新
    case 'refresh':

        include COMMON_PATH.'room_refresh.inc.php';
        break;




    // 关联配对
    case 'bind':

        include COMMON_PATH.'room_bind.inc.php';
        break;



    // 读取历史
    case 'history':
        $hotelid = (int)$_GET['hotel'];
        $page = (int)$_GET['page'];
        if (!$page) $page = 1;

        $limit = 10;
        $start = $limit * $page;

        $history = $db -> prepare("SELECT `id`,FROM_UNIXTIME(`time`, '%m-%d %H:%i') AS `time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND `type`='room' ORDER BY `time` DESC LIMIT {$start},{$limit};") -> execute(array(':id'=>$hotelid));

        if ($history !== false)
            json_return($history);
        else
            json_return(null, 1, '读取失败，请重试');



    // 单个酒店房型
    case 'load':
        $id = (int)$_GET['id'];
        $rooms = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel_room_type` WHERE `hotel`=:hotel;") -> execute(array(':hotel'=>$id));
        foreach ($rooms as $k => $v)
        {
            $rooms[$k]['name'] = roomname($v['name'], 2);
        }
        json_return($rooms);



    // 列表
    case 'list':
    default:
        // 供应商
        $sups = supplies();
        if (!empty($_GET['sup']) && isset($sups[$_GET['sup']]))
            $sup = $_GET['sup'];
        else
            $sup = 'HMC';

        template::assign('supplies', $sups);
        template::assign('supply', $sup);
        template::assign('keyword', '');

        // 酒店列表
        $where = "1=1";
        $condition = array();

        if (isset($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND (a.`name` LIKE :keyword OR b.`name` LIKE :keyword OR c.`name` LIKE :keyword OR a.`pinyin` LIKE :keyword OR a.`address` LIKE :keyword OR a.`teL` LIKE :keyword)";
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        if (!empty($_GET['unbind']))
        {
            $where .= " AND d.`type` = 0";
            template::assign('unbind', 1);
        }

        $join = "LEFT JOIN `ptc_district` AS b ON a.`country` = b.`id`
                 LEFT JOIN `ptc_district` AS c ON a.`city` = c.`id`
                 LEFT JOIN `ptc_hotel_room` AS d ON a.`id` = d.`hotel`";

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM (SELECT a.`id` FROM `ptc_hotel` AS a {$join} WHERE {$where} GROUP BY a.`id`) AS s;") -> execute($condition);

        $page = new page($count[0]['c'], 5);
        $limit = $page -> limit();

        $sql = "SELECT a.*, b.name AS `country`, c.`name` AS `city`, c.`province`
                FROM `ptc_hotel` AS a
                    {$join}
                WHERE {$where}
                GROUP BY a.`id`
                ORDER BY a.`id` DESC
                LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);

        foreach ($list as $k => $v)
        {
            $rooms = $db -> prepare("SELECT * FROM `ptc_hotel_room_type` WHERE `hotel`=:hotel;") -> execute(array(':hotel'=>$v['id']));
            $list[$k]['rooms'] = $rooms;

            $unbind = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_hotel_room` WHERE `hotel`=:hotel AND `type`=0 AND `isdel`=0") -> execute(array(':hotel'=>$v['id']));
            $list[$k]['unbind'] = $unbind[0]['c'];
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('room/list');
        break;


}




?>
