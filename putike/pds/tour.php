<?php
/**
 * 订单
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

$method = empty($_GET['method']) ? 'card' : $_GET['method'];

$db = db(config('db'));

template::assign('nav', 'Tour');
template::assign('subnav', $method);

switch ($method)
{
    // ------------------------- 产品线 -------------------------
    case 'area':

        // 保存
        if ($_POST)
        {
            // 上下线操作
            if (isset($_POST['status']))
            {
                $id = $_POST['id'];
                $status = $_POST['status'] ? 1 : 0;
                $rs = $db -> prepare("UPDATE `ptc_tour_area` SET `status`=:status WHERE `id`=:id") -> execute(array(':id'=>$id, ':status'=>$status));

                if ($rs === false)
                    json_return(null, 1, '操作失败请重试');
                else
                    json_return($rs);
            }

            // 保存操作
            else
            {
                $data = array(
                    'name'          => trim($_POST['name']),
                    'pics'          => implode(',', array_filter($_POST['pics'])),
                    'cities'        => implode('|', array_filter($_POST['cities'])),
                    'updatetime'    => NOW,
                );

                if (!$data['name']) json_return(null, 1, '产品线名称不能为空');
                if (!$data['pics']) json_return(null, 1, '产品线图片不能为空');
                if (!$data['cities']) json_return(null, 1, '推荐国家/城市不能为空');

                if (!empty($_POST['id']))
                {
                    list($sql, $value) = array_values(update_array($data));
                    $value[':id'] = (int)$_POST['id'];
                    $rs = $db -> prepare("UPDATE `ptc_tour_area` SET {$sql} WHERE `id`=:id;") -> execute($value);
                }
                else
                {
                    list($column, $sql, $value) = array_values(insert_array($data));
                    $rs = $db -> prepare("INSERT INTO `ptc_tour_area` {$column} VALUES {$sql};") -> execute($value);
                }

                if ($rs === false)
                    json_return(null, 1, '保存失败，请重试');

                json_return($rs);
            }
        }

        // 添加/编辑
        if (isset($_GET['id']))
        {
            $id = (int)$_GET['id'];
            if ($id)
                $data = $db -> prepare("SELECT * FROM `ptc_tour_area` WHERE `id`=:id") -> execute(array(':id'=>$id));
            else
                $data = null;

            if ($data)
                $data[0]['designers'] = $db -> prepare("SELECT `nickname` FROM `ptc_tour_designer` WHERE `isdel`=0 AND FIND_IN_SET(:id, `areas`)") -> execute(array(':id'=>$id));

            template::assign('data', $data[0]);
            template::display('tour/area_edit'); exit;
        }

        $count = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_tour_area` WHERE 1=1") -> execute();
        $page  = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT `id`,`name`,`cities`,`status` FROM `ptc_tour_area` WHERE 1=1 ORDER BY `status` DESC LIMIT {$limit}") -> execute();

        template::assign('list', $list);
        template::assign('page', $page -> show());
        template::display('tour/area');
        break;


    // ------------------------- 行程设计师 -------------------------
    case 'designer':

        if ($_POST)
        {
            // 删除
            if(!empty($_POST['del']))
            {
                $id = (int)$_POST['del'];
                $rs = $db -> prepare('UPDATE `ptc_tour_designer` SET `isdel`=1 WHERE id=:id') -> execute(array(':id'=>$id));
                if ($rs === false)
                    json_return(null, 1, '操作失败，请重试');
                else
                    json_return($rs);
            }

            // 保存
            else
            {
                $data = array(
                    'uid'       => (int)$_POST['uid'],
                    'nickname'  => trim($_POST['nickname']),
                    'avatar'    => trim($_POST['avatar']),
                    'description'   => trim($_POST['description']),
                    'wechat'    => trim($_POST['wechat']),
                    'email'     => trim($_POST['email']),
                    'mobile'    => trim($_POST['mobile']),
                    'areas'     => implode(',', $_POST['areas']),
                    'updatetime'    => NOW,
                );

                if (!$data['uid'])      json_return(null, 1, '请选择用户');
                if (!$data['nickname']) json_return(null, 1, '设计师昵称不能为空');
                if (!$data['avatar'])   json_return(null, 1, '设计师头像不能为空');
                if (!$data['areas'])    json_return(null, 1, '负责线路不能为空');

                $check = $db -> prepare("SELECT `id`,`isdel` FROM `ptc_tour_designer` WHERE `uid`=:uid") -> execute(array(':uid'=>$data['uid']));
                if ($check)
                {
                    $_POST['id'] = $check[0]['id'];
                    $data['isdel'] = 0;
                }

                $id = (int)$_POST['id'];
                if ($id)
                {
                    list($sql, $value) = array_values(update_array($data));
                    $value[':id'] = $id;
                    $rs = $db -> prepare("UPDATE `ptc_tour_designer` SET {$sql} WHERE `id`=:id;") -> execute($value);
                }
                else
                {
                    list($column, $sql, $value) = array_values(insert_array($data));
                    $rs = $db -> prepare("INSERT INTO `ptc_tour_designer` {$column} VALUES {$sql};") -> execute($value);
                }

                if ($rs === false)
                    json_return(null, 1, '保存失败，请重试');

                json_return( $id ? $id : $rs );
            }
        }


        // 添加/编辑
        if (isset($_GET['id']))
        {
            $id = (int)$_GET['id'];

            if ($id)
                $designer = $db -> prepare("SELECT * FROM `ptc_tour_designer` WHERE `id`=:id") -> execute(array(':id'=>$id));
            else
                $designer = null;

            $sql = "SELECT u.`id`, u.`name`, d.`id` AS `disabled`
                    FROM `rbac_user` AS u
                        LEFT JOIN `ptc_tour_designer` AS d ON u.`id`=d.`uid` AND d.`id`!=:id AND d.`isdel`=0
                    WHERE u.`isdel`=0
                    ORDER BY u.`id` DESC;";
            $users = $db -> prepare($sql) -> execute(array(':id'=>$id));
            template::assign('users', $users);

            $areas = $db -> prepare("SELECT `id`,`name` FROM `ptc_tour_area` WHERE `status`=1") -> execute();
            template::assign('areas', $areas);

            template::assign('data', $designer ? $designer[0] : null);
            template::display('tour/designer_edit');
            exit;
        }

        $count = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_tour_designer` WHERE 1=1") -> execute();
        $page  = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $sql = "SELECT a.`id`, a.`nickname`, a.`areas`, b.`name`, GROUP_CONCAT(c.`name`) AS `areas`
                FROM `ptc_tour_designer` AS a
                    LEFT JOIN `rbac_user` AS b ON a.uid = b.id
                    LEFT JOIN `ptc_tour_area` AS c ON FIND_IN_SET(c.`id`, a.`areas`) AND c.`status`=1
                WHERE a.`isdel`=0
                GROUP BY a.`id`
                ORDER BY a.`id` DESC
                LIMIT {$limit}
                ";
        $list = $db -> prepare($sql) -> execute();

        template::assign('list', $list);
        template::assign('page', $page -> show());
        template::display('tour/designer');
        break;


    // ------------------------- 定制卡需求 -------------------------

    default:
        break;

}