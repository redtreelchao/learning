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

template::assign('nav', 'Order');

// include hook
include_once PT_PATH.'hook/hook.php';

// ---------------- v ---------------- 保存订单操作 ---------------- v ----------------
if ($_POST)
{
    // Export
    if ($_POST['method'] == 'export')
    {
        include_once PT_PATH.'common/order_export.inc.php';
    }

    // Order Method
    $order_code = trim($_POST['order']);

    $where = 'o.`order`=:code';
    $condition = array(':code'=>$order_code);

    if (in_array($_SESSION['role'], array(7)))
    {
        $where .= ' AND o.`from`=3';
    }

    $sql = "SELECT o.*, b.`name` AS `org_name`
            FROM `ptc_order` AS o
                LEFT JOIN `ptc_org` AS b ON o.`from` = b.`id`
            WHERE {$where};";
    $order = $db -> prepare($sql) -> execute($condition);
    if (!$order)
        json_return(null, 1, '订单未找到');

    switch ($_POST['method'])
    {
        // Pay from offline
        case 'order-pay':
            $account = $_POST['type'].': '.trim($_POST['account']);
            $rs = order::pay($order[0]['order'], date('Y-m-d H:i:s'), '线下付款', $account, $_POST['trade'], 0, '');

            if (!$rs)
            {
                $error = order::get_error();
                json_return(null, $error['code'], $error['msg']);
            }

            api::push('order', $order[0]['order'], '');

            json_return(1, 0, '操作成功！');

        break;

        // Send invoice by express
        case 'order-express':
            $db -> beginTrans();

            $data = array(
                'expresstype'   => trim($_POST['type']),
                'expressno'     => trim($_POST['number']),
                'expressfloor'  => (int)$_POST['floor'],
                'expressfee'    => (int)$_POST['fee'],
            );
            list($sql, $value) = array_values(update_array($data));
            $value[':orderid'] = $order[0]['id'];
            $rs = $db -> prepare("UPDATE `ptc_order_ext` SET {$sql} WHERE `orderid`=:orderid") -> execute($value);
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            $rs = $db -> prepare("UPDATE `ptc_order` SET `invoice`=2 WHERE `id`=:orderid") -> execute(array(':orderid'=>$order[0]['id']));
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 2, '保存失败，请重试');
            }

            if (!order::_log($order[0], '发票已寄送', $data))
            {
                $db -> rollback();
                json_return(null, 3, '保存失败，请重试');
            }

            api::push('order', $order[0]['order']);

            if ($db -> commit())
                json_return(1);
            else
                json_return(null, 9, '保存失败，请重试');

            break;


        // Question
        case 'order-qa':
            $message = strip_tags(trim($_POST['message']));

            $return = array('time'=>date('m-d H:i', NOW), 'message'=>bbcode($message));

            $rs = $db -> prepare("INSERT INTO `ptc_qa` (`product`,`remark`,`uid`,`time`) VALUES (:product,:remark,:uid,:time)")
                      -> execute(array(':product'=>(int)$_POST['product'], ':remark'=>$message, ':uid'=>$_SESSION['uid'], ':time'=>NOW));

            if ($rs)
                json_return($return);
            else
                json_return(null, 9, '保存失败，请重试');
            break;


        // Log message
        case 'order-log':

            $message = $_POST['message'];
            $message = preg_replace('/<img src=\"[\S]+\/([a-z.]+)\.gif\"[^>]+>/i', '[smiley]$1[/smiley]', $message);
            $message = preg_replace('/<b>([\s\S]*?)<\/b>/i', '[b]$1[/b]', $message);
            $message = preg_replace('/<font color=\"([#a-z0-9-]+)\">([\s\S]*?)<\/font>/i', '[color=$1]$2[/color]', $message);
            $message = strip_tags($message);
            $message = str_replace(array('<p>', '</p>', '<br />', '<br>'), array("", "\n", "\n", "\n"), $message);

            $return = array('time'=>date('m-d H:i', NOW), 'message'=>bbcode($message), 'username'=>$_SESSION['name']);

            $rs = order::_log($order[0], 'r:'.$message, null);
            if ($rs)
                json_return($return);
            else
                json_return(null, 9, '保存失败，请重试');
            break;

        // Confirmation
        case 'confirmation':
            include_once PT_PATH.'common/order_confirmation.inc.php';
            break;

        // Hook
        default:

            if ($order[0]['status'] < 3) json_return(null, 1, '订单未支付');

            action::exec('order_manage_save', $order[0]);
    }
}



$method =  !empty($_GET['method']) ? $_GET['method'] : 'list';

switch ($method)
{
    // ---------------- v ---------------- 订单提醒 ---------------- v ----------------
    case 'remind':
        $time = $_GET['time'];

        $_where = '';
        $where = 'o.`create` > :time AND o.`status` > 0';
        $condition = array(':time'=>$time);

        if (in_array($_SESSION['role'], array(7)))
        {
            $_where = ' AND o.`from`=3';
        }

        // new order
        $sql = "SELECT o.*, s.`name` AS `status_str`, org.`name` AS `org`, org.`color`
                FROM `ptc_order` AS o
                    LEFT JOIN `ptc_order_status` AS s ON o.`status` = s.`id`
                    LEFT JOIN `ptc_org` AS org ON org.`id` = o.`from`
                WHERE {$where} {$_where}
                ORDER BY o.`id` DESC;";
        $list = $db -> prepare($sql) -> execute($condition);

        foreach ($list as $k => $v)
        {
            $order = order::view($v, true, false);

            switch ($order['status'])
            {
                case 4:
                    $order['important'] = 1;
                    break;

                case 10:
                    $order['important'] = 1;
                    break;

                case 13:
                    $order['important'] = 1;
                    break;

                case 15:
                    $order['important'] = 1;
                    break;
            }

            $list[$k] = $order;
        }

        // update order
        $updates = $db -> prepare("SELECT `order`,`status`,`update` FROM `ptc_order` AS o WHERE `update` > :time AND `status` IN (4,10,13,15) {$_where} ORDER BY `update` DESC")
                       -> execute(array(':time'=>$time));

        $need_invoice = $db -> prepare("SELECT `order`, 'invoice' AS `status`, `update` FROM `ptc_order` AS o WHERE `update` > :time AND `invoice`=1 AND (`status`=9 OR `status2`=9 OR `status3`=9) {$_where} ORDER BY `update` DESC")
                            -> execute(array(':time'=>$time));

        $clear = $db -> prepare("SELECT r.`order`, 'clear' AS `status`, o.`update` FROM `ptc_order_room` AS r LEFT JOIN `ptc_order` AS o ON r.`orderid`=o.`id` WHERE o.`update` > :time AND r.`settletime` > 0 AND r.`settletime` < " . NOW . " AND o.`clear`=0 {$_where} GROUP BY r.`orderid`")
                     -> execute(array(':time'=>$time));

        if ($updates && $need_invoice)
            $updates = $updates[0]['update'] > $need_invoice[0]['update'] ? array_merge($updates, $need_invoice) : array_merge($need_invoice, $updates);
        else if ($need_invoice)
            $updates = $need_invoice;

        if ($updates && $clear)
            $updates = $updates[0]['update'] > $clear[0]['update'] ? array_merge($updates, $clear) : array_merge($clear, $updates);
        else if ($clear)
            $updates = $clear;

        // operators
        $_operators = $db -> prepare("SELECT `id`,`name` FROM `rbac_user`") -> execute();
        $operators = array();
        foreach ($_operators as $k => $v)
        {
            $operators[$v['id']] = $v['name'];
        }
        template::assign('operators', $operators);

        // orgs
        $orgs = $db -> prepare("SELECT `id`,`name` FROM `ptc_org`") -> execute();
        template::assign('orgs', $orgs);

        // supplies
        $supplies = supplies();
        template::assign('supplies', $supplies);

        template::assign('list', $list);
        template::assign('status', order::status());

        $html = template::fetch('order/_tr');

        $time = $list ? $list[0]['create'] : $time;
        $time = $updates && $updates[0]['update'] > $time ? $updates[0]['update'] : $time;

        $data = array('html'=>$html, 'update'=>$updates, 'time'=>$time);
        json_return($data);

        break;


    // ---------------- v ---------------- 产品搜索列表 ---------------- v ----------------
    case 'product':
        template::assign('subnav', 'product');
        template::display('order/product');
        break;


    // ---------------- v ---------------- 查看订单 ---------------- v ----------------
    case 'view':
    case 'operate':

        $order_code = trim($_GET['order']);

        $where = 'o.`order`=:code';
        $condition = array(':code'=>$order_code);

        if (in_array($_SESSION['role'], array(7)))
        {
            $where .= ' AND o.`from`=3';
        }

        $sql = "SELECT o.*, b.`name` AS `org_name` FROM `ptc_order` AS o
                    LEFT JOIN `ptc_org` AS b ON o.`from` = b.`id`
                WHERE {$where};";
        $order = $db -> prepare($sql) -> execute($condition);

        if (!$order) redirect('/order.php');


        // product key
        $_key = array();

        if (!empty($_GET['pr']))
        {
            $item_id = (int)substr($_GET['pr'], 1);
            $item_type = substr($_GET['pr'], 0, 1);
            $rs = true;
            $uid = (int)$_SESSION['uid'];

            if ($item_type == 'p')
            {
                // product
                $item = $db -> prepare("SELECT `producttype`, `operator`, `hotel` FROM `ptc_order_hotel` WHERE `orderid`=:orderid AND `product`=:product")
                            -> execute(array(':orderid'=>$order[0]['id'], ':product'=>$item_id));

                $_key = array('product'=>$item_id, 'hotel'=>$item[0]['hotel']);

                if ($method == 'operate')
                {
                    if ($item[0]['operator'] != $uid)
                    {
                        $db -> beginTrans();
                        $rs1 = $db -> prepare("UPDATE `ptc_order_hotel` SET `operator`=:operator WHERE `orderid`=:orderid AND `product`=:product AND `operator`=0")
                                   -> execute(array(':orderid'=>$order[0]['id'], ':product'=>$item_id, ':operator'=>$uid));

                        switch ($item[0]['producttype'])
                        {
                            case 2:
                                $rs2 = $db -> prepare("UPDATE `ptc_order_auto` SET `operator`=:operator WHERE `orderid`=:orderid AND `product`=:product AND `operator`=0")
                                           -> execute(array(':orderid'=>$order[0]['id'], ':product'=>$item_id, ':operator'=>$uid));
                                break;
                            case 4:
                                $rs2 = $db -> prepare("UPDATE `ptc_order_flight` SET `operator`=:operator WHERE `orderid`=:orderid AND `product`=:product AND `operator`=0")
                                           -> execute(array(':orderid'=>$order[0]['id'], ':product'=>$item_id, ':operator'=>$uid));
                                break;
                        }

                        if (!$rs1 || !$rs2 || !$db -> commit())
                        {
                            $db -> rollback();
                            $rs = $rs1 === 0 && $rs2 === 0 ? 0 : false;
                        }
                    }
                    else
                    {
                        $rs = true;
                    }
                }
            }
            else if ($item_type == 'h')
            {
                // hotel
                $item = $db -> prepare("SELECT `operator`, `hotel`, `supply`, `supplyid` FROM `ptc_order_hotel` WHERE `orderid`=:orderid AND `id`=:id")
                            -> execute(array(':orderid'=>$order[0]['id'], ':id'=>$item_id));

                $_key = array('product'=>($item[0]['supply'] == 'TICKET' ? $item[0]['supplyid'] : ''), 'hotel'=>$item[0]['hotel']);

                if ($method == 'operate')
                {
                    if ($item[0]['operator'] != $uid)
                    {
                        $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `operator`=:operator WHERE `orderid`=:orderid AND `id`=:id AND `operator`=0")
                                  -> execute(array(':orderid'=>$order[0]['id'], ':id'=>$item_id, ':operator'=>$uid));
                    }
                    else
                    {
                        $rs = true;
                    }
                }
            }
            else if ($item_type == 'f')
            {
                // flight
                $item = $db -> prepare("SELECT `operator`, `flight` FROM `ptc_order_flight` WHERE `orderid`=:orderid AND `id`=:id")
                            -> execute(array(':orderid'=>$order[0]['id'], ':id'=>$item_id));

                $_key = array('product'=>($item[0]['supply'] == 'TICKET' ? $item[0]['supplyid'] : ''), 'flight'=>$item[0]['flight']);

                if ($method == 'operate')
                {
                    if ($item[0]['operator'] != $uid)
                    {
                        $rs = $db -> prepare("UPDATE `ptc_order_flight` SET `operator`=:operator WHERE `orderid`=:orderid AND `id`=:id AND `operator`=0")
                                  -> execute(array(':orderid'=>$order[0]['id'], ':id'=>$item_id, ':operator'=>$uid));
                    }
                    else
                    {
                        $rs = true;
                    }
                }
            }

            if ($method == 'operate')
            {
                if (!$rs)
                {
                    template::assign('error', $rs === 0 ? '订单已被他人锁定，请刷新。' : '订单锁定失败，请刷新重试。');
                    $method = 'view';
                }
                else
                {
                    if($rs !== true) order::_log($order[0], "锁定了订单 [{$item_type}{$item_id}]", null);

                    // All confirmations
                    $confirmations = $db -> prepare("SELECT `order`,`group`,`send` FROM `ptc_order_confirmation` WHERE `orderid`=:orderid;") -> execute(array(':orderid'=>$order[0]['id']));
                    $confirmations = filter::apply('order_confirmation', $confirmations, $order[0]);
                    template::assign('confirmations', $confirmations);
                }
            }
        }

        $orderid = $order[0]['id'];
        $order = order::view($order[0], true);


        // Order extend infromation
        $extend = $db -> prepare("SELECT * FROM `ptc_order_ext` WHERE `orderid`=:orderid;") -> execute(array(':orderid'=>$orderid));

        // Operate logs
        $logs = $db -> prepare("SELECT `id`,`time`,`uid`,`username`,`remark` FROM `ptc_order_log` WHERE `orderid`=:orderid ORDER BY `id` DESC;")
                    -> execute(array(':orderid'=>$orderid));

        // Hotel link
        if (!empty($_key['hotel']))
        {
            $links = $db -> prepare("SELECT * FROM `ptc_hotel_link` WHERE `hotel`=:hotel") -> execute(array(':hotel'=>$item[0]['hotel']));
            template::assign('links', $links);
        }

        // Question and Answer
        if (!empty($_key['product']))
        {
            $qa = $db -> prepare("SELECT * FROM `ptc_qa` WHERE `product`=:id ORDER BY `id` DESC") -> execute(array(':id'=>$_key['product']));
            template::assign('qa', $qa);

            $supply = $db -> prepare("SELECT `supplyname`, `contact1`, `contact2`, `bookingcode`, `supplyrule` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$_key['product']));
            $supply[0]['contact1'] = json_decode($supply[0]['contact1'], true);
            $supply[0]['contact2'] = json_decode($supply[0]['contact2'], true);
            template::assign('supply', $supply[0]);

            template::assign('qa_product', $_key['product']);
        }

        template::assign('order', $order);
        template::assign('extend', $extend[0]);
        template::assign('log', $logs);
        template::assign('status', order::status());
        template::assign('mode', $method);

        template::assign('subnav', 'list');

        template::display('order/view');
        break;


    // ---------------- v ---------------- 确认单操作 ---------------- v ----------------
    case 'confirmation':

        $order = trim($_GET['order']);
        $group = intval($_GET['group']);

        $confirmation = $db -> prepare("SELECT * FROM `ptc_order_confirmation` WHERE `order`=:order AND `group`=:group")
                             -> execute(array(':order'=>$order, ':group'=>$group));
        if (!$confirmation)
            redirect('./order.php');

        $data = json_decode($confirmation[0]['data'], true);

        $history = $db -> prepare("SELECT `id`,`time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND `type`='confirmation' ORDER BY `time` DESC;") -> execute(array(':id'=>$confirmation[0]['id']));
        template::assign('history', $history);

        template::assign('order', $order);
        template::assign('group', $group);
        template::assign('data',  $data);
        template::assign('mode',  'edit');
        template::display('order/confirmation');
        break;


    // ---------------- v ---------------- 操作解锁 ---------------- v ----------------
    case 'unlock':
        $order_code = trim($_GET['order']);

        $where = '`order`=:code';
        $condition = array(':code'=>$order_code);

        if (in_array($_SESSION['role'], array(7)))
        {
            $where .= ' AND `from`=3';
        }

        $sql = "SELECT `id`, `order` FROM `ptc_order` WHERE {$where};";
        $order = $db -> prepare($sql) -> execute($condition);
        if (!$order)
            json_return(null, 1, '订单未找到');

        $item_id = (int)substr($_GET['item'], 1);
        $item_type = substr($_GET['item'], 0, 1);
        $rs = true;

        if ($item_type == 'p')
        {
            $db -> beginTrans();
            $rs1 = $db -> prepare("UPDATE `ptc_order_hotel` SET `operator`=:operator WHERE `order`=:order AND `product`=:product")
                      -> execute(array(':order'=>$order_code, ':product'=>$item_id, ':operator'=>0));

            $item = $db -> prepare("SELECT `operator`, `hotel`, `supply`, `supplyid`, `producttype` FROM `ptc_order_hotel` WHERE `order`=:order AND `product`=:product")
                        -> execute(array(':order'=>$order_code, ':product'=>$item_id));

            switch ($item[0]['producttype'])
            {
                case 2:
                    $rs2 = $db -> prepare("UPDATE `ptc_order_auto` SET `operator`=:operator WHERE `order`=:order AND `product`=:product")
                               -> execute(array(':order'=>$order_code, ':product'=>$item_id, ':operator'=>0));
                    break;
                case 4:
                    $rs2 = $db -> prepare("UPDATE `ptc_order_flight` SET `operator`=:operator WHERE `order`=:order AND `product`=:product")
                               -> execute(array(':order'=>$order_code, ':product'=>$item_id, ':operator'=>0));
                    break;
            }

            if ($rs1 === false || $rs2 === false || !$db -> commit())
            {
                $db -> rollback();
                $rs = false;
            }
        }
        else if ($item_type == 'h')
        {
            $rs = $db -> prepare("UPDATE `ptc_order_hotel` SET `operator`=:operator WHERE `order`=:order AND `id`=:id")
                      -> execute(array(':order'=>$order_code, ':id'=>$item_id, ':operator'=>0));
        }
        else if ($item_type == 'f')
        {
            $rs = $db -> prepare("UPDATE `ptc_order_flight` SET `operator`=:operator WHERE `order`=:order AND `id`=:id")
                      -> execute(array(':order'=>$order_code, ':id'=>$item_id, ':operator'=>0));
        }


        if ($rs === false)
        {
            json_return(null, 1, '解锁失败，请重试');
        }
        else
        {
            order::_log($order[0], "解锁了订单 [{$item_type}{$item_id}]", null);
            json_return(1);
        }

        break;


    // ---------------- v ---------------- 订单导出 ---------------- v ----------------
    case 'export':

        // orgs
        $orgs = $db -> prepare("SELECT `id`,`name` FROM `ptc_org`") -> execute();
        template::assign('orgs', $orgs);

        // supplies
        $supplies = supplies();
        template::assign('supplies', $supplies);

        template::assign('status', order::status());

        template::assign('subnav', 'export');

        template::display('order/export');
        break;


    // ---------------- v ---------------- 订单列表 ---------------- v ----------------
    case 'list':
    default:

        $join = array();
        $where = "1=1";
        $order = '';
        $condition = array();

        if (in_array($_SESSION['role'], array(7)))
        {
            $where .= ' AND o.`from`=3';
        }

        // quick search
        if (!empty($_GET['keyword']))
        {
            if (preg_match('/^[0-9- ]+$/', $_GET['keyword']))
            {
                $where .= " AND (o.`order` LIKE :keyword OR o.`tel` LIKE :keyword)";
                $condition[':keyword'] = '%'.trim($_GET['keyword']);
            }
            else
            {
                $where .= " AND o.`contact` LIKE :keyword";
                $condition[':keyword'] = '%'.trim($_GET['keyword']).'%';
            }

            template::assign('keyword', $_GET['keyword']);
        }
        // quick search


        // advanced search
        $keywords = array('time'=>'','start'=>'','end'=>'','order'=>'','name'=>'','tel'=>'','people'=>'','from'=>'','supply'=>'','status'=>'','operator'=>'','invoice'=>'','clear'=>'');
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
                            $where .= " AND o.`create` >= :start";
                            $condition[':start'] = $start;
                            $keywords['start'] = $start;
                        }

                        if ($end)
                        {
                            $where .= " AND o.`create` < :end";
                            $condition[':end'] = $end;
                            $keywords['end'] = $end;
                        }

                        $keywords['time'] = 'booking';
                        break;

                    case 'checkin':
                        $join["order_hotel"] = "LEFT JOIN `ptc_order_hotel` AS h ON o.`id`=h.`orderid`";
                        $join["order_room"] = "LEFT JOIN `ptc_order_room` AS r ON o.`id`=r.`orderid`";
                        if ($start)
                        {
                            $where .= " AND (h.`checkin` >= :start OR r.`checkin` >= :start)";
                            $condition[':start'] = $start;
                            $keywords['start'] = $start;
                        }

                        if ($end)
                        {
                            $end += 86400;
                            $where .= " AND ((h.`checkin` > 0 AND h.`checkin` < :end) OR (r.`checkin` > 0 AND r.`checkin` < :end))";
                            $condition[':end'] = $end;
                            $keywords['end'] = $end;
                        }

                        $keywords['time'] = 'checkin';
                        break;
                }
            }
        }

        if (!empty($_GET['order']))
        {
            $where .= " AND o.`order` LIKE :order";
            $condition[':order'] = '%'.trim($_GET['order']);
            $keywords['order'] = trim($_GET['order']);
        }

        if (!empty($_GET['name']))
        {
            $join["order_hotel"] = "LEFT JOIN `ptc_order_hotel` AS h ON o.`id`=h.`orderid`";
            $join["order_goods"] = "LEFT JOIN `ptc_order_goods` AS g ON o.`id`=g.`orderid`";
            $where .= " AND (h.`productname` LIKE :name OR h.`hotelname` LIKE :name OR g.`productname` LIKE :name)";
            $condition[':name'] = '%'.trim($_GET['name']).'%';
            $keywords['name'] = trim($_GET['name']);
        }

        if (!empty($_GET['tel']))
        {
            $where .= " AND o.`tel` LIKE :tel";
            $condition[':tel'] = '%'.trim($_GET['tel']);
            $keywords['tel'] = trim($_GET['tel']);
        }

        if (!empty($_GET['people']))
        {
            $join["order_room"] = "LEFT JOIN `ptc_order_room` AS r ON o.`id`=r.`orderid`";
            $where .= " AND (o.`contact` LIKE :people OR r.`people` LIKE :people)";
            $condition[':people'] = '%'.trim($_GET['people']).'%';
            $keywords['people'] = trim($_GET['people']);
        }

        if (!empty($_GET['from']))
        {
            $where .= " AND o.`from` = :from";
            $condition[':from'] = (int)$_GET['from'];
            $keywords['from'] = (int)$_GET['from'];
        }

        if (!empty($_GET['supply']))
        {
            $join["order_hotel"] = "LEFT JOIN `ptc_order_hotel` AS h ON o.`id`=h.`orderid`";
            $join["order_room"] = "LEFT JOIN `ptc_order_room` AS r ON o.`id`=r.`orderid`";
            $where .= " AND (h.`supply` = :supply AND r.`supply` = :supply)";
            $condition[':supply'] = $_GET['supply'];
            $keywords['supply'] = $_GET['supply'];
        }

        if (!empty($_GET['status']))
        {
            $where .= " AND o.`status` = :status";
            $condition[':status'] = (int)$_GET['status'];
            $keywords['status'] = (int)$_GET['status'];

            if ($_GET['status'] == 10)
            {
                $order = 'o.`refundtime` DESC,';
            }

            if ($_GET['status'] == 4)
            {
                $order = 'o.`update` DESC,';
            }
        }
        else
        {
            if (isset($_GET['view']))
            {
                if ($_GET['view'] == 'pay')
                    $_SESSION['_order_view'] = 'pay';

                else if ($_GET['view'] == 'all')
                    $_SESSION['_order_view'] = 'all';
            }

            if (isset($_SESSION['_order_view']) && $_SESSION['_order_view'] == 'pay')
                $where .= " AND o.`status` > 2";
            else
                $where .= " AND o.`status` > 0";
        }

        if (!empty($_GET['operator']))
        {
            $join["order_hotel"] = "LEFT JOIN `ptc_order_hotel` AS h ON o.`id`=h.`orderid`";
            $where .= " AND h.`operator` = :operator";
            $condition[':operator'] = (int)$_GET['operator'];
            $keywords['operator'] = (int)$_GET['operator'];
        }

        if (!empty($_GET['clear']))
        {
            if ($_GET['clear'] == 1)
            {
                $where .= " AND o.`clear` = 1";
                $keywords['clear'] = 1;
            }
            else if ($_GET['clear'] == -1)
            {
                $join['order_room'] = "LEFT JOIN `ptc_order_room` AS r ON o.`id`=r.`orderid`";
                $where .= " AND r.`settletime` > 0 AND r.`settletime` < ".NOW;
                $keywords['clear'] = -1;
            }
        }

        if (!empty($_GET['invoice']))
        {
            if ($_GET['invoice'] == 1)
            {
                $where .= " AND o.`invoice` = 2";
                $keywords['invoice'] = 1;
            }
            else
            {
                $where .= " AND o.`invoice` = 1";
                $keywords['invoice'] = -1;

                if (count(array_filter($keywords)) == 1)
                    $where .= ' AND (`status`=9 OR `status2`=9 OR `status3`=9)';
            }
        }

        template::assign('keywords', $keywords);
        // advanced search

        // status important
        $_where = '';
        if (in_array($_SESSION['role'], array(7)))
            $_where .= ' AND o.`from`=3';

        $sql = "SELECT COUNT(o.id) AS `count`, s.`id` AS `status`
                FROM `ptc_order_status` AS s
                    LEFT JOIN `ptc_order` AS o ON s.`id` = o.`status` {$_where}
                WHERE s.`id` IN (4,10,13,15)
                GROUP BY s.`id` ORDER BY s.`id` ASC;";
        $important = $db -> prepare($sql) -> execute();

        $invoice = $db -> prepare("SELECT COUNT(*) AS `count` FROM `ptc_order` AS o WHERE o.`invoice`=1 {$_where}") -> execute();
        $important[] = array('count'=>$invoice[0]['count'], 'status'=>'invoice');

        $clear = $db -> prepare("SELECT COUNT(DISTINCT `order`) AS `count` FROM `ptc_order_room` WHERE `settletime` > 0 AND `settletime` < " . NOW . " {$_where}") -> execute();
        $important[] = array('count'=>$clear[0]['count'], 'status'=>'clear');

        template::assign('important', $important);

        // list
        $join = implode(" ", $join);

        $sql = "FROM `ptc_order` AS o
                    LEFT JOIN `ptc_order_status` AS s ON o.`status` = s.`id`
                    LEFT JOIN `ptc_org` AS org ON org.`id` = o.`from`
                    {$join}
                WHERE {$where}"
                .($join ? " GROUP BY o.`id`" : '')."
                ORDER BY {$order}o.`id` DESC";

        $count = $db -> prepare($join ? "SELECT COUNT(*) AS `c` FROM (SELECT o.`id` {$sql}) AS `s`" : "SELECT COUNT(*) AS `c` {$sql};") -> execute($condition);
        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT o.*, s.`name` AS `status_str`, org.`name` AS `org`, org.`color` {$sql} LIMIT {$limit};") -> execute($condition);
        foreach ($list as $k => $v)
        {
            $list[$k] = order::view($v, true, false);
        }

        // operators
        $_operators = $db -> prepare("SELECT `id`,`name` FROM `rbac_user`") -> execute();
        $operators = array();
        foreach ($_operators as $k => $v)
        {
            $operators[$v['id']] = $v['name'];
        }
        template::assign('operators', $operators);

        // orgs
        $orgs = $db -> prepare("SELECT `id`,`name` FROM `ptc_org`") -> execute();
        template::assign('orgs', $orgs);

        // supplies
        $supplies = supplies();
        template::assign('supplies', $supplies);

        template::assign('page', $page->show());
        template::assign('list', $list);
        template::assign('status', order::status());

        template::assign('subnav', 'list');

        template::display('order/list');
}

