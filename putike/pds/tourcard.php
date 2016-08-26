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

$db = db(config('db'));

template::assign('nav', 'Tour');
template::assign('subnav', 'card');

$method = !empty($_GET['method']) ? $_GET['method'] : 'list';

$status = array(
    '0'=>'已核实',
    '1' =>'待处理',
    '2' =>'优先',
    '3' =>'无效',
    '4' =>'行程设计中',
    '5' =>'需要更改',
    '6' =>'等待支付',
    '7' =>'支付成功',
    '8' =>'旅途中',
    '9' =>'旅行结束',
    '10'=>'已过期',
    '11'=>'已退款',
    '12'=>'退款中'
);
template::assign('status', $status);

switch ($method)
{

    // ------------------------- 消息 -------------------------
    case 'message':
        if ($_POST)
        {
            $designer = $db -> prepare("SELECT * FROM `ptc_tour_designer` WHERE `uid`=:uid") -> execute(array(':uid'=>$_SESSION['uid']));
            if (!$designer)
                json_return(null, 1, '您不是行程设计师，无法使用该功能');

            $data = array(
                'card'  => (int)$_POST['card'],
                'msg'   => trim($_POST['message']),
                'tel'   => trim($_POST['tel']),
                'designer'  => $designer[0]['id'],
                'time'      => NOW,
            );

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_tour_message` {$column} VALUES {$sql};") -> execute($value);

            sms::tour(1, $data['tel'], $designer[0]['nickname'], $designer[0]['mobile']);

            if ($rs === false)
                json_return(null, 1, '保存失败，请重试');

            json_return($rs);
        }

        $where = '';
        $condition = [];
        if(intval($_GET['card']))
        {
           $where .= " AND `card` = :card";
           $condition[':card'] = intval($_GET['card']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_tour_message` WHERE 1=1 {$where}") -> execute($condition);
        $page  = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $sql = "SELECT * FROM `ptc_tour_message` WHERE 1=1 {$where} ORDER BY `id` DESC LIMIT {$limit}";
        $list = $db -> prepare($sql) -> execute($condition);

        template::assign('list', $list);
        template::assign('page', $page -> show());
        template::display('tour/message');

        break;




    // ------------------------- 标记 -------------------------
    case 'tag':

        $id = $_POST['id'];
        $status = $_POST['status'] > 3 ? 0 : (int)$_POST['status'];
        $rs = $db -> prepare("UPDATE `ptc_tour_card` SET `status`=:status WHERE `id`=:id AND `status` <= 3") -> execute(array(':id'=>$id, ':status'=>$status));

        if ($rs === false)
            json_return(null, 1, '操作失败请重试');
        else
            json_return($rs);

        break;



    // ------------------------- 查看 -------------------------
    case 'view':

        $options = [
             '4star'    => '四星级酒店',
             '5star'    => '五星级酒店',
             'B&B'      => '当地民宿',
             'first'    => '头等舱',
             'business' => '商务舱',
             'economy'  => '经济舱',
             'any'      => '不限',
        ];
        template::assign('options', $options);

        $id= (int)$_GET['id'];
        $sql = "SELECT c.*, a.`name` AS `areaname`
                FROM `ptc_tour_card` AS c
                    LEFT JOIN `ptc_tour_area` AS a ON c.`area_id` = a.`id`
                WHERE c.`id`=:id";
        $data = $db -> prepare($sql) -> execute(array(':id'=>$id));
        if (!$data) redirect('./tourcard.php');
        template::assign('data', $data[0]);
        $designer = $db -> prepare("SELECT  `id` FROM `ptc_tour_designer` WHERE isdel=0 and `uid`=:uid") -> execute(array(':uid'=>$_SESSION['uid']));

        template::assign('is_designer',$designer?1:0);
        $order = $db -> prepare("SELECT `id`, `title` FROM `ptc_tour_order` WHERE `card`=:card") -> execute(array(':card'=>$id));
        template::assign('order', $order ? $order[0] : null);

        template::display('tour/card_view');
        break;



    // ------------------------- 列表 -------------------------
    case 'list':
    default:

        $join = array();
        $where = "1=1";
        $condition = array();

        // quick search
        $keyword = '';
        if (!empty($_GET['keyword']))
        {
            $keyword = trim($_GET['keyword']);
            if (preg_match('/^[0-9- ]+$/', $keyword))
            {
                $where .= " AND (c.`code` LIKE :keyword OR c.`tel` LIKE :keyword)";
                $condition[':keyword'] = '%'.$keyword;
            }
            else
            {
                $where .= " AND c.`contact` LIKE :keyword";
                $condition[':keyword'] = '%'.$keyword.'%';
            }
        }
        template::assign('keyword', $keyword);


        // advanced search
        $keywords = array('time'=>'','start'=>'','end'=>'','order'=>'','area'=>'','tel'=>'','people'=>'','status'=>'');
        if (!empty($_GET['time']))
        {
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end']);
            if ($start || $end)
            {
                switch ($_GET['time'])
                {
                    case 'booking':
                        if ($start)
                        {
                            $where .= " AND c.`createtime` >= :start";
                            $condition[':start'] = $start;
                            $keywords['start'] = $start;
                        }

                        if ($end)
                        {
                            $where .= " AND c.`createtime` < :end";
                            $condition[':end'] = $end;
                            $keywords['end'] = $end;
                        }

                        $keywords['time'] = 'booking';
                        break;

                    case 'start':
                        if ($start)
                        {
                            $where .= " AND c.`departure` >= :start";
                            $condition[':start'] = $start;
                            $keywords['start'] = $start;
                        }

                        if ($end)
                        {
                            $end += 86400;
                            $where .= " AND c.`departure` < :end";
                            $condition[':end'] = $end;
                            $keywords['end'] = $end;
                        }

                        $keywords['time'] = 'start';
                        break;
                }
            }
        }

        if (!empty($_GET['order']))
        {
            $where .= " AND c.`code` LIKE :order";
            $condition[':order'] = '%'.trim($_GET['order']);
            $keywords['order'] = trim($_GET['order']);
        }

        if (!empty($_GET['name']))
        {
            $where .= " AND a.`name` = :name";
            $condition[':name'] = trim($_GET['name']);
            $keywords['name'] = trim($_GET['name']);
        }

        if (!empty($_GET['tel']))
        {
            $where .= " AND c.`tel` LIKE :tel";
            $condition[':tel'] = '%'.trim($_GET['tel']);
            $keywords['tel'] = trim($_GET['tel']);
        }

        if (!empty($_GET['people']))
        {
            $where .= " AND c.`contact` LIKE :people";
            $condition[':people'] = '%'.trim($_GET['people']).'%';
            $keywords['people'] = trim($_GET['people']);
        }

        if (!empty($_GET['status']))
        {
            $where .= " AND c.`status` = :status";
            $condition[':status'] = (int)$_GET['status'];
            $keywords['status'] = (int)$_GET['status'];
        }

        template::assign('keywords', $keywords);

        $count = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_tour_card` AS c LEFT JOIN `ptc_tour_area` AS a ON c.`area_id` = a.`id` WHERE {$where}") -> execute($condition);
        $page  = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $sql = "SELECT c.`id`, c.`code`, a.`name` AS `area`, c.`contact`, c.`tel`, c.`departure`, c.`return`, c.`days`, c.`budget`, c.`status`, c.`createtime`
                FROM `ptc_tour_card` AS c
                    LEFT JOIN `ptc_tour_area` AS a ON c.`area_id` = a.`id`
                WHERE {$where}
                ORDER BY `id` DESC, `status` DESC
                LIMIT {$limit}";
        $list = $db -> prepare($sql) -> execute($condition);

        template::assign('list', $list);
        template::assign('page', $page -> show());
        template::display('tour/card');
}

