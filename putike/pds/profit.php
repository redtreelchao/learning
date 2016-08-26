<?php
/**
 * 利润设置
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

template::assign('nav', 'Product');
template::assign('subnav', 'profit');

$orgs = $db -> prepare("SELECT * FROM `ptc_org`") -> execute();
template::assign('orgs', $orgs);


// get products by type and payment
function get_products($type, $payment, $pid=0)
{
    global $db;

    $products = array();
    switch ($type)
    {
        case 'hotel':
            if ($payment == 'ticket')
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_product` WHERE `type`=1 AND `payment`='ticket' ORDER BY `id` DESC") -> execute();
            else
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel` ORDER BY `id` DESC") -> execute();
            break;

        case 'room':
            if ($payment == 'ticket')
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_product_item` WHERE `pid`=:pid ORDER BY `id` DESC") -> execute(array(':pid'=>$pid));
            else
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel_room_type` WHERE `hotel`=:pid ORDER BY `id` DESC") -> execute(array(':pid'=>$pid));
                if (!$products) $products = array();
                foreach ($products as $k => $v)
                    $products[$k]['name'] = roomname($v['name'], 2);
            break;

        case 'goods':
            if ($payment == 'ticket')
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_product` WHERE `type`=7 AND `payment`='ticket' ORDER BY `id` DESC") -> execute();
            break;

        case 'product2':
            if ($payment == 'prepay')
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_product` WHERE `type`=2 AND `payment`='prepay' ORDER BY `id` DESC") -> execute();
            break;

        case 'product4':
            if ($payment == 'prepay')
                $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_product` WHERE `type`=4 AND `payment`='prepay' ORDER BY `id` DESC") -> execute();
            break;

        case 'item':
            $products = $db -> prepare("SELECT `id`,`name` FROM `ptc_product_item` WHERE `pid`=:pid ORDER BY `id` DESC") -> execute(array(':pid'=>$pid));

    }

    return $products;
}


$method = empty($_GET['method']) ? '' : $_GET['method'];
switch ($method)
{
    case 'edit':
        // Save profit
        if ($_POST)
        {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id)
            {
                $_profit = $db -> prepare("SELECT * FROM `ptc_org_profit` WHERE `id`=:id") -> execute(array(':id'=>$id));
                $condition = $data = $_profit[0];
                $data['profit'] = (int)$_POST['profit'];
                $data['child']  = (int)$_POST['child'];
                $data['baby']   = (int)$_POST['baby'];
                $data['type']   = $_POST['type'];
                $data['updatetime'] = NOW;
            }
            else
            {
                $item = $_POST['item'] ? (int)$_POST['item'] : 0;
                $itemtype = $_POST['objtype'] == 'hotel' ? 'room' : 'item';

                $condition = array(
                    'org'       => $_POST['org'],
                    'payment'   => $_POST['payment'],
                    'objtype'   => $item ? $itemtype : $_POST['objtype'],
                    'objid'     => $item ? $item : (int)$_POST['objid']
                );

                $data = array(
                    'org'       => (int)$_POST['org'],
                    'payment'   => $_POST['payment'],
                    'objtype'   => $item ? $itemtype : $_POST['objtype'],
                    'objid'     => $item ? $item : (int)$_POST['objid'],
                    'profit'    => (int)$_POST['profit'],
                    'child'     => (int)$_POST['child'],
                    'baby'      => (int)$_POST['baby'],
                    'type'      => $_POST['type'],
                    'start'     => 0,
                    'end'       => 0,
                    'updatetime'=> NOW,
                );
            }

            $check = $db -> prepare("SELECT `id` FROM `ptc_org_profit` WHERE `org`=:org AND `payment`=:payment AND `objtype`=:objtype AND `objid`=:objid ".($id ? " AND `id`!='{$id}'" : ''))
                         -> execute(array(':org'=>$condition['org'], ':payment'=>$condition['payment'], ':objtype'=>$condition['objtype'], ':objid'=>$condition['objid']));
            if ($check)
                json_return(null, 1, '已存在同数据，点击<a href="./profit.php?method=edit&id='.$check[0]['id'].'">这里查看</a>');

            $db -> beginTrans();

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("REPLACE INTO `ptc_org_profit` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            if ($data['org'] == 0)
            {
                if (isset($data['id'])) unset($data['id']);

                switch($_POST['replace'])
                {
                    case 'all':
                        $others = array();
                        foreach ($orgs as $v)
                        {
                            $data['org'] = $v['id'];
                            $others[] = $data;
                        }

                        list($column, $sql, $value) = array_values(insert_array($others));
                        $rs = $db -> prepare("REPLACE INTO `ptc_org_profit` {$column} VALUES {$sql};") -> execute($value);
                        break;

                    case 'same':
                        if (isset($value[':id'])) unset($value[':id']);
                        $sql = "UPDATE `ptc_org_profit`
                                    SET `profit`=:profit, `child`=:child, `baby`=:baby, `type`=:type, `updatetime`=:updatetime
                                WHERE `org`!=:org AND `payment`=:payment AND `objtype`=:objtype AND `objid`=:objid AND `start`=:start AND `end`=:end
                                        AND `profit`=:profit AND `child`=:child AND `baby`=:baby AND `type`=:type;";
                        $rs = $db -> prepare($sql) -> execute($value);
                        break;
                }

                if (false === $rs)
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }
            }

            if ($db -> commit())
            {
                json_return(1);
            }
            else
            {
                $db -> rollback();
                json_return(null, 9, '保存失败，请重试');
            }
        }

        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_org_profit` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '利润信息不存在或已删除');

        $data = $data[0];
        if ($data['objtype'] == 'room')
        {
            $type = 'hotel';
            if ($data['payment'] == 'prepay')
                $parent = $db -> prepare("SELECT `hotel` AS `pid` FROM `ptc_hotel_room_type` WHERE `id`=:id") -> execute(array(':id'=>$data['objid']));
            else
                $parent = $db -> prepare("SELECT `pid` FROM `ptc_product_item` WHERE `id`=:id") -> execute(array(':id'=>$data['objid']));

            $item = $data['objid'];
            $data['objid'] = $parent[0]['pid'];
        }
        else if ($data['objtype'] == 'item')
        {
            $item = $data['objid'];

            $_item = $db -> prepare("SELECT p.`id`, p.`type` FROM `ptc_product_item` AS i LEFT JOIN `ptc_product` AS p ON i.`pid`=p.`id` WHERE i.`id`=:id") -> execute(array(':id'=>$data['objid']));
            switch ($_item[0]['type'])
            {
                case '7':
                    $type = 'goods';
                    $data['objtype'] = 'goods';
                    $data['objid'] = $_item[0]['id'];
                    break;

                default:
                    $type = 'product' . $_item[0]['type'];
                    $data['objtype'] = 'product' . $_item[0]['type'];
                    $data['objid'] = $_item[0]['id'];
            }
        }

        $data['products'] = get_products($type, $data['payment']);

    case 'new':

        if (!isset($data))
        {
            $data = null;
            $item = 0;
        }

        template::assign('item', $item);
        template::assign('data', $data);
        template::display('product/profit_edit');
        break;


    // 删除利润
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];

            $db = db(config('db'));

            $profit = $db -> prepare("SELECT * FROM `ptc_org_profit` WHERE `id`=:id") -> execute(array(':id' => $id));
            if ($profit[0]['objid'] == 0) json_return(null, 1, '基础利润不能删除');

            $db -> beginTrans();

            $rs = delete('ptc_org_profit', "`id`='{$id}'", false);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 1, '操作失败，请重试..');
            }

            if ($db -> commit())
                json_return(1);
            else
                json_return(null, 9, '操作失败，请重试..');
        }
        break;


    // 辅助读取产品类
    case 'product':

        if (!$_POST['payment'] || !$_POST['type']) json_return(null, 1, '支付方式及产品类型未选择');

        $pid = empty($_POST['pid']) ? 0 : $_POST['pid'];
        $products = get_products($_POST['type'], $_POST['payment'], $pid);
        json_return($products);


    // 列表
    case 'list':
    default:

        if (empty($_GET['org']))
        {
            $where = "1=1";
            $condition = array();
            template::assign('org', 0);
        }
        else
        {
            $org = (int)$_GET['org'];
            $where = "a.`org`=:org";
            $condition = array(':org' => $org);
            template::assign('org', $org);
        }

        $keyword = '';
        if (!empty($_GET['keyword']))
        {
            $keyword = trim($_GET['keyword']);
            $where .= " AND ((a.`payment`='prepay' AND a.`objtype`='hotel' AND h1.`name` LIKE :keyword)
                        OR (a.`payment`='prepay' AND a.`objtype`='room' AND h2.`name` LIKE :keyword)
                        OR (a.`payment`='prepay' AND (a.`objtype` IN ('product2', 'product4')) AND p1.`name` LIKE :keyword)
                        OR (a.`objtype`='item' AND p2.`name` LIKE :keyword)
                        OR (a.`payment`='ticket' AND a.`objtype`='goods' AND p1.`name` LIKE :keyword)
                        OR (a.`payment`='ticket' AND a.`objtype`='hotel' AND p1.`name` LIKE :keyword)
                        OR (a.`payment`='ticket' AND a.`objtype`='room' AND p2.`name` LIKE :keyword))";

            $condition[':keyword'] = '%'.$keyword.'%';
        }
        template::assign('keyword', $keyword);

        if (!empty($_GET['id']))
        {
            switch ($_GET['type'])
            {
                case 1:
                    $where .= " AND ((a.`payment`=:payment AND a.`objtype`='hotel' AND a.`objid`=:id)
                                OR (a.`payment`=:payment AND a.`objtype`='room' AND p2.`id`=:id))";
                    break;
                case 4:
                    $where .= " AND ((a.`payment`=:payment AND a.`objtype`='product4' AND a.`objid`=:id)
                                OR (a.`payment`=:payment AND a.`objtype`='item' AND p2.`id`=:id))";
                    break;
            }

            $condition[':payment'] = trim($_GET['payment']);
            $condition[':id'] = (int)$_GET['id'];
        }

        $field = "CASE
                    WHEN a.`payment`='prepay' AND a.`objtype`='hotel' THEN h1.`name`
                    WHEN a.`payment`='prepay' AND a.`objtype`='room' THEN CONCAT(h2.`name`, '<br /><span class=\"info\">', r.`name`, '</span>')
                    WHEN a.`payment`='prepay' AND (a.`objtype`='product4' OR a.`objtype`='product2') THEN p1.`name`
                    WHEN a.`payment`='prepay' AND a.`objtype`='item' THEN CONCAT(p2.`name`, '<br /><span class=\"info\">', i.`name`, '</span>')
                    WHEN a.`payment`='ticket' AND a.`objtype`='hotel' THEN p1.`name`
                    WHEN a.`payment`='ticket' AND a.`objtype`='room' THEN CONCAT(p2.`name`, '<br /><span class=\"info\">', i.`name`, '</span>')
                    WHEN a.`payment`='ticket' AND a.`objtype`='goods' THEN p1.`name`
                    WHEN a.`payment`='ticket' AND a.`objtype`='item' THEN CONCAT(p2.`name`, '<br /><span class=\"info\">', i.`name`, '</span>')
                  END AS `name`";

        $sql = "FROM `ptc_org_profit` AS a
                    LEFT JOIN `ptc_hotel` AS h1 ON a.`objid`=h1.id
                    LEFT JOIN `ptc_hotel_room_type` AS r ON a.`objid`=r.id
                    LEFT JOIN `ptc_hotel` AS h2 ON r.`hotel` = h2.`id`
                    LEFT JOIN `ptc_product` AS p1 ON a.`objid`=p1.id
                    LEFT JOIN `ptc_product_item` AS i ON a.`objid`=i.id
                    LEFT JOIN `ptc_product` AS p2 ON i.`pid` = p2.`id`
                WHERE {$where}
                GROUP BY a.`id`";

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM (SELECT a.`id` {$sql}) AS s;") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT a.*, {$field} {$sql} ORDER BY a.`id` ASC LIMIT {$limit};") -> execute($condition);
        //echo "SELECT a.*, {$field} {$sql} ORDER BY a.`objid` ASC, a.`objtype` DESC LIMIT {$limit};"; exit;

        template::assign('page', $page -> show());
        template::assign('list', $list);

        template::display('product/profit');
}

