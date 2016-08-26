<?php
/**
 * 机票
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

template::assign('nav', 'Flight');
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
                'leg'       => (int)$_POST['leg'],
                'depart'    => (string)$_POST['depart'],
                'arrive'    => (string)$_POST['arrive'],
                'takeoff'   => (string)trim($_POST['takeoff']),
                'landing'   => (string)trim($_POST['landing']),
                'company'   => (string)trim($_POST['company']),
                'updatetime'=> NOW,
            );

            if (!$data['code']) json_return(null, 1, '航班编号不能为空');
            if (!$data['depart'] || !$data['arrive']) json_return(null, 1, '请选择出发/到达城市');
            if (false === strtotime('today '.$data['takeoff']) || strlen($data['takeoff']) != 5) json_return(null, 1, '出发时间不正确');
            if (false === strtotime('today '.$data['landing']) || strlen($data['landing']) != 5) json_return(null, 1, '到达时间不正确');
            if (!empty($_POST['day'])) $data['landing'] .= ' +1d';

            $check = $db -> prepare("SELECT `id` FROM `ptc_flight` WHERE (`code`=:code AND `depart`=:depart AND `arrive`=:arrive)".($_POST['id'] ? " AND `id`!='{$_POST['id']}'" : ''))
                         -> execute(array(':code'=>$data['code'], ':depart'=>$data['depart'], ':arrive'=>$data['arrive']));
            if ($check)
                json_return(null, 1, '已存在相同航班数据，请检查并确认');

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_flight` SET {$sql} WHERE `id`=:id;") -> execute($value);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_flight` {$column} VALUES {$sql};") -> execute($value);
            }

            if ($rs)
                json_return($rs);
            else
                json_return(null, 1, '保存失败，请重试');
        }


        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_flight` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '酒店信息不存在或已删除');

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

        $airport = $db -> prepare("SELECT * FROM `ptc_flight_airport` WHERE 1=1;") -> execute();
        template::assign('airport', $airport);

        template::display('flight/edit');
        break;



    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            if (delete('ptc_flight', "`id`='{$id}'"))
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
        $join = 'LEFT JOIN `ptc_flight_airport` AS b ON b.`code` = a.`depart` LEFT JOIN `ptc_flight_airport` AS c ON c.`code` = a.`arrive`';
        template::assign('keyword','');

        if (!empty($_GET['keyword']))
        {
            if (is_numeric($_GET['keyword']))
            {
                $where .= " AND a.`id` = :id";
                $condition[':id'] = (int)$_GET['keyword'];
            }
            else
            {
                $keyword = '%'.$_GET['keyword'].'%';
                $where .= " AND ( a.`code` LIKE :keyword OR a.`depart` LIKE :keyword OR a.`arrive` LIKE :keyword )";
                $condition[':keyword'] = $keyword;
            }
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_flight` AS a {$join} WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.*, b.`name` AS `depart_airport`, c.`name` AS `arrive_airport`
                FROM `ptc_flight` AS a
                    {$join}
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

        template::display('flight/list');
        break;


}


?>