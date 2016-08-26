<?php
/**
 * 机场
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
template::assign('subnav', 'airport');

switch ($method)
{
    // 编辑
    case 'edit':
        // 保存
        if ($_POST)
        {
            $data = array(
                'code'      => (string)trim($_POST['code']),
                'code4'     => (string)trim($_POST['code4']),
                'name'      => (string)trim($_POST['name']),
                'city'      => (int)$_POST['city'],
                'updatetime'=> NOW,
            );

            if (!$data['code']) json_return(null, 1, '机场代码不能为空');
            if (!$data['city']) json_return(null, 1, '请选择机场城市');

            $db -> beginTrans();

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("REPLACE INTO `ptc_flight_airport` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            // Products about flight, linkage updating
            $sql = "UPDATE `ptc_product_item` AS a
                        LEFT JOIN `ptc_flight` AS b ON a.`objtype` = 'flight' AND a.`objpid` = b.id
                    SET a.source = IF(b.depart = :code, :city, a.source), a.target = IF(b.arrive = :code, :city, a.target)
                    WHERE a.`objtype`='flight' AND ( b.depart=:code OR b.arrive=:code )";
            $prs = $db -> prepare($sql) -> execute(array(':city'=>$data['city'], ':code'=>$data['code']));
            if (false === $prs)
            {
                $db -> rollback();
                json_return(null, 2, '保存失败，请重试');
            }

            if ($db -> commit())
                json_return($data['code']);
            else
                json_return(null, 1, '保存失败，请重试');
        }

        $code = trim($_GET['code']);
        $data = $db -> prepare("SELECT * FROM `ptc_flight_airport` WHERE `code`=:code") -> execute(array(':code'=>$code));
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

        $citys = $db -> prepare("SELECT * FROM `ptc_district` WHERE `pid`!=0;") -> execute();
        template::assign('citys', $citys);

        template::display('airport/edit');
        break;



    // 删除
    case 'del':
        if ($_POST)
        {
            $code = trim($_POST['code']);
            if (delete('ptc_flight_airport', "`code`='{$code}'"))
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
            if (is_numeric($_GET['keyword']))
            {
                $where .= " AND a.`id` = :id";
                $condition[':id'] = (int)$_GET['keyword'];
            }
            else
            {
                $keyword = '%'.$_GET['keyword'].'%';
                $where .= " AND ( a.`code` LIKE :keyword OR a.`name` LIKE :keyword )";
                $condition[':keyword'] = $keyword;
            }
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_flight_airport` AS a WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.*, b.`name` AS `city`
                FROM `ptc_flight_airport` AS a
                    LEFT JOIN `ptc_district` AS b ON b.`id` = a.`city`
                WHERE {$where}
                LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);
        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()));
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('airport/list');
        break;


}


?>