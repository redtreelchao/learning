<?php
/**
 * 组合产品
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

template::assign('nav', 'Product');
template::assign('subnav', 'list');

switch ($method)
{
    case 'default':
        $item = $db->prepare('SELECT `id`, `objtype`, `default`, `pid` FROM `ptc_product_item` WHERE `id`=:id;')->execute(array(':id'=>$_POST['item']));
        if (empty($item))
            json_return(null, 1, '未找到对应记录，请重试');

        if ($item[0]['default'] == 1)
            json_return(null, 1, '此记录已设置为前端显示');

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_product_item` SET `default`=0 WHERE `pid`=:pid AND `objtype`=:type;")->execute(array(':pid'=>$item[0]['pid'], ':type'=>$item[0]['objtype']));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 1, '操作失败，请重试');
        }

        $rs = $db -> prepare("UPDATE `ptc_product_item` SET `default`=1 WHERE `id`= :id;")->execute(array(':id'=>$item[0]['id']));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 2, '操作失败，请重试');
        }

        if(!$db -> commit())
        {
            $db -> rollback();
            json_return(null, 9, '操作失败，请重试');
        }
        else
        {
            json_return(1);
        }
        break;



    // Edit product information
    case 'edit':
    case 'preview':


        if ($_POST)
        {
            if($method == 'preview')
                die;

            $name = str_replace(array('（', '）'), array('(', ')'), $_POST['name']);

            $data = array(
                'org'       => implode(',', $_POST['org']),
                'bd'        => implode(',', $_POST['bd']),
                'ba'        => implode(',', $_POST['ba']),
                'name'      => trim($name),
                //'tags'      => empty($_POST['tag']) ? '' : implode(',', $_POST['tag']),
                'start'     => (int)strtotime(date('Y-m-d 00:00:00', strtotime($_POST['start']))),
                'end'       => (int)strtotime(date('Y-m-d 00:00:00', strtotime($_POST['end']))),
                'intro'     => trim($_POST['intro']),
                'rule'      => trim($_POST['rule']),
                'refund'    => trim($_POST['refund']),
/*                 'supplyname'=> trim($_POST['supplyname']),        // 取消供应商功能
                'contact1'  => $_POST['contact1'],
                'contact2'  => $_POST['contact2'],
                'bookingcode'   => trim($_POST['bookingcode']),
                'supplyrule'=> trim($_POST['supplyrule']), */
                //'new'       => !empty($_POST['new']) ? 1 : 0,
                //'exclusive' => !empty($_POST['exclusive']) ? 1 : 0,
                //'excstart'  => !empty($_POST['exclusive']) ? (int)strtotime(date('Y-m-d 00:00:00', strtotime($_POST['excstart']))) : 0,
                //'excend'    => !empty($_POST['exclusive']) ? (int)strtotime(date('Y-m-d 00:00:00', strtotime($_POST['excend']))) : 0,
                'updatetime'=> NOW,
            );

            if (!$data['name'])  json_return(null, 1, '产品名称不能为空');
            if (!$data['intro']) json_return(null, 1, '产品描述不能为空');
            if (!$data['rule'])  json_return(null, 1, '使用要求不能为空');
            if (!$data['org'])   json_return(null, 1, '售卖渠道未选择');
            if (!$data['start']) json_return(null, 1, '未设置上架时间');
            if (!$data['end'])   json_return(null, 1, '未设置下架时间');
            if ($data['start'] < strtotime('today 00:00:00')) json_return(null, 1, '上架时间不得小于当日');
            if ($data['end'] < strtotime('today 00:00:00'))   json_return(null, 1, '下架时间不得小于当日');

            /* if (!$data['supplyname']) json_return(null, 1, '请填写供应商名称');   // 取消供应商功能 */

            if ($data['exclusive'])
            {
                if (!$data['excstart'] || !$data['excend']) json_return(null, 1, '未设置独家有效时间');
            }

/*             if (!$data['contact1']['fax'] && !$data['contact1']['email'])  // 取消供应商功能
                json_return(null, 1, '主要联系人传真/邮箱必须填写之一');

            import(CLASS_PATH.'extend/string');
            foreach (array('contact1'=>'主要联系人', 'contact2'=>'第二联系人') as $key => $name)
            {
                foreach ($data[$key] as $k => $v)
                {
                    switch ($k)
                    {
                        case 'email':
                        case 'cc':
                            if ($v && !string::check($v, 'email'))
                                json_return(null, 1, $name.($k == 'cc' ? '抄送邮箱' : '邮箱').'格式错误');
                            break;

                        case 'fax':
                        case 'tel':
                            if ($v && !string::check($v, 'phone'))
                                json_return(null, 1, $name.($k == 'fax' ? '传真' : '联系电话').'号码格式错误');
                            break;
                    }
                }
            }

            $data['contact1'] = json_encode($data['contact1'], JSON_UNESCAPED_UNICODE);
            $data['contact2'] = json_encode($data['contact2'], JSON_UNESCAPED_UNICODE); */

            $db -> beginTrans();

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_product` SET {$sql} WHERE `id`=:id ;") -> execute($value);
                $id = (int)$_POST['id'];

                $history_msg = '修改了产品';
            }
            else
            {
                $data['type']       = (int)$_POST['type'];
                $data['payment']    = (string)$_POST['payment'];

                if (!$data['type']) json_return(null, 1, '请选择产品类型');
                if (!$data['payment']) json_return(null, 1, '请选择产品支付类型');

                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_product` {$column} VALUES {$sql};") -> execute($value);
                $id = $rs;

                $history_msg = '创建了产品';
            }

            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            // Api push
            // Remove by beta2.0
            // Product can be edited before post, only.
            $product = $db -> prepare("SELECT `id`,`type`,`payment`,`org` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$id));
            if (!api::push($product[0]['type'], $id, $product[0]['payment'], $product[0]['org']))
            {
                $db -> rollback();
                json_return(null, 2, '保存失败，请重试');
            }

            // History
            if (!history($id, 'product', $history_msg, $data))
            {
                $db -> rollback();
                json_return(null, 3, '保存失败，请重试');
            }

            if ($db -> commit())
                json_return($rs);
            else
                json_return(null, 9, '保存失败，请重试');
        }

        $id = (int)$_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$id));

        if($method != 'preview')
        {
            if (!$data)
                template::assign('error', '产品信息不存在或已删除');

            if ($data[0]['status'] != 0)
                template::assign('error', '产品已发布，不允许修改');
        }


        $data = $data[0];

/*         $data['contact1'] = json_decode($data['contact1'], true);   // 取消供应商功能
        $data['contact2'] = json_decode($data['contact2'], true); */

        $data['items'] = $db -> prepare("SELECT * FROM `ptc_product_item` WHERE `pid`=:pid ORDER BY `id` ASC;") -> execute(array(':pid' => $data['id']));

        // 售卖渠道
        $data['org'] = explode(',', $data['org']);

        $history = $db -> prepare("SELECT `id`,`time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND (`type`='product' OR `type`='item') ORDER BY `time` DESC LIMIT 0,10;") -> execute(array(':id' => $data['id']));
        template::assign('history', $history);

        template::assign('method', $method);
        if($method == 'preview')
        {

            $data['baname']= $data['bdname'] = array();
            $user_condition = array();
            if($data['ba'])//产品助理
            {
                $sql = "SELECT `id`, `name`, `username` FROM `rbac_user` WHERE `role` = 2  AND `id` IN (".$data['ba'].');';
                $rs = $db -> prepare($sql)->execute();
                if($rs){
                    $data['baname'] = $rs;
                }
            }

            if($data['bd'])//产品经理
            {

                $sql = "SELECT `id`, `name`, `username` FROM `rbac_user` WHERE `role` = 2  AND `id` IN (".$data['bd'].');';
                $rs = $db -> prepare($sql)->execute();
                if($rs){
                    $data['bdname'] = $rs;
                }
            }

            template::assign('data', $data);
            template::display('product/preview');
            break;
        }


    // Create product
    case 'new':

        if (!isset($data))
            $data = null;

        template::assign('data', $data);
        template::assign('method', $method);
        template::display('product/edit');
        break;


    case 'user':

        $sql = "SELECT `id`, `name`, `username` FROM `rbac_user` WHERE `role` = 2 ORDER BY `id` ASC;";
        $list = $db -> prepare($sql) -> execute();
        json_return($list, 0);

        break;

    // Delete data
    case 'del':

        if ($_POST)
        {
            $id = (int)$_POST['id'];
            $type = trim($_POST['type']);

            if ($type == 'product')
            {
                $db = db(config('db'));

                $product = $db -> prepare("SELECT `id`,`type`,`payment`,`status` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$id));
                if (!$product)
                    json_return(null, 1, '数据不存在，或已被删除..');

                if ($product[0]['status'] != 0)
                    json_return(null, 1, '产品已发布，不允许删除');


                $db -> beginTrans();

                $rs = delete('ptc_product', "`id`='{$id}'", false);
                if (!$rs)
                {
                    $db -> rollback();
                    json_return(null, 1, '操作失败，请重试..');
                }

                $rs = delete('ptc_product_item', "`pid`='{$id}'", false);
                if (!$rs)
                {
                    $db -> rollback();
                    json_return(null, 2, '操作失败，请重试..');
                }

                // Remove Api push.
                // Product can be deleted before post, only.

                $rs = $db -> commit();
            }
            else
            {
                $product = $db -> prepare("SELECT a.`id`,a.`type`,a.`payment`,a.`status` FROM `ptc_product` AS a LEFT JOIN `ptc_product_item` AS b ON a.`id`=b.`pid` WHERE b.`id`=:id") -> execute(array(':id'=>$id));
                if (!$product)
                    json_return(null, 1, '数据不存在，或已被删除..');

                if ($product[0]['status'] != 0)
                    json_return(null, 1, '产品已发布，不允许删除');

                $rs = delete('ptc_product_item', "`id`='{$id}'");

                // Remove Api push.
                // Product can be deleted before post, only.
            }

            if ($rs)
                json_return(1);
            else
                json_return(null, 9, '操作失败，请重试..');
        }

        break;




    // Copy Product data to create a new
    case 'clone':

        include PT_PATH.'common/product_clone.inc.php';

        break;





    // Update product's status
    case 'status':

        if ($_POST)
        {
            $id     = (int)$_POST['id'];
            $status = (int)$_POST['status'];

            if ($status >= 0) json_return(null, 1, '上架操作已变更流程，请重新操作');

            $product = $db -> prepare("SELECT `id`,`type`,`payment`,`org`,`status` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$id));

            $msg = '产品已下架';
            $reason = trim($_POST['reason']);
            $remark = trim($_POST['remark']);
            $log = '下架了产品,下架理由：'.$reason.'下架原因：'.$remark;
            $key = 'offlinetime';

            if ($product[0]['status'] == $status)
                json_return(null, 2, $msg.'，请刷新');

            $db -> beginTrans();

            $rs = $db -> prepare("UPDATE `ptc_product` SET `status`=:status, `audit`=0, `updatetime`=:time, `{$key}`=:time WHERE `id`=:id") -> execute(array(':id'=>$id, ':status'=>$status, ':time'=>NOW));
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            // Recode online offline time
            if ($key == 'onlinetime' || $key == 'offlinetime')
            {
                // Api Push
                if (!api::push($product[0]['type'], $id, $product[0]['payment'], $product[0]['org']))
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }
            }

            // History
            if (!history($id, 'product', $log, null))
            {
                $db -> rollback();
                json_return(null, 3, '保存失败，请重试');
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





    // Update product's audit
    case 'audit':

        if ($_POST)
        {
            $id = (int)$_POST['id'];
            $audit = (int)$_POST['audit'];

            $product = $db -> prepare("SELECT `id`,`type`,`payment`,`org`,`status`,`audit` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$id));
            if (!$product) json_return(null, 1, '产品不存在');
            $product = $product[0];

            switch ($audit)
            {
                // 待审核
                case 1:
                    if ($product['status'] > 0) json_return(null, 1, '产品已发布，申请无效');
                    if ($product['audit'] > 0 && $product['audit'] != 2) json_return(null, 1, '审核状态错误，无此操作');
                    if (!in_array($_SESSION['role'], [0,2,3,4])) json_return(null, 1, '无权限执行该操作');
                    $message = '申请产品审核';
                    break;

                // 审核通过
                case 2:
                    if ($product['status'] > 0) json_return(null, 1, '产品已发布，审核无效');
                    if ($product['audit'] != 1) json_return(null, 1, '审核状态错误，无此操作');
                    if (!in_array($_SESSION['role'], [0,3,4])) json_return(null, 1, '无权限执行该操作');
                    $message = '审核通过';
                    $status = 1;
                    break;

                // 审核拒绝
                case -2:
                    if ($product['status'] > 0) json_return(null, 1, '产品已发布，审核无效');
                    if ($product['audit'] != 1) json_return(null, 1, '审核状态错误，无此操作');
                    if (!in_array($_SESSION['role'], [0,3,4])) json_return(null, 1, '无权限执行该操作');
                    $message = '审核拒绝';
                    break;

                // 申请修改
                case 3:
                    if ($product['status'] == 0) json_return(null, 1, '产品未发布，操作无效');
                    if ($product['audit'] != 2 && $product['audit'] != 4) json_return(null, 1, '审核状态错误，无此操作');
                    if (!in_array($_SESSION['role'], [0,2,3,4])) json_return(null, 1, '无权限执行该操作');
                    $message = '申请修改产品信息' . ($_POST['remark'] ? "，修改原因：{$_POST['remark']}" : '');
                    break;

                // 可修改
                case -1:
                    if ($product['status'] == 0) json_return(null, 1, '产品未发布，操作无效');
                    if ($product['audit'] != 3) json_return(null, 1, '审核状态错误，无此操作');
                    if (!in_array($_SESSION['role'], [0,1,3,4])) json_return(null, 1, '无权限执行该操作');
                    $message = '同意修改申请';
                    $status = -1;
                    break;

                // 拒绝修改
                case 4:
                    if ($product['status'] == 0) json_return(null, 1, '产品未发布，操作无效');
                    if ($product['audit'] != 3) json_return(null, 1, '审核状态错误，无此操作');
                    if (!in_array($_SESSION['role'], [0,1,3,4])) json_return(null, 1, '无权限执行该操作');
                    $message = '拒绝修改申请';
                    break;
            }

            $db -> beginTrans();

            $set = '';
            $values = array(':id'=>$id, ':audit'=>$audit, ':time'=>NOW);
            if (isset($status))
            {
                if ($status > 0)
                {
                    $set = ', `status`=:status, `onlinetime`=:time';
                    $values[':status'] = $status;
                }
                else
                {
                    $set = ', `status`=:status, `offlinetime`=:time';
                    $values[':status'] = $status;
                }
            }

            $rs = $db -> prepare("UPDATE `ptc_product` SET `audit`=:audit, `updatetime`=:time {$set} WHERE `id`=:id") -> execute($values);
            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            // Api Push
            if (isset($status))
            {
                if (!api::push($product['type'], $id, $product['payment'], $product['org']))
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }
            }

            // History
            if (!history($id, 'product', $message, null))
            {
                $db -> rollback();
                json_return(null, 3, '保存失败，请重试');
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



        // =========== v =========== Items =========== v ===========

    // Edit item infromation
    case 'item':

        // include product hook
        include_once PT_PATH.'hook/hook.php';

        if ($_POST)
        {
            $pid = (int)$_POST['pid'];

            $product = $db -> prepare("SELECT `id`,`type`,`payment`,`status` FROM `ptc_product` WHERE `id`=:id") -> execute(array(':id'=>$pid));
            if (!$product) json_return(null, 1, '产品已删除或不存在');

            //if ($product[0]['status'] != 0) json_return(null, 1, '产品已发布，不允许修改');

            $product = $product[0];

            $name = str_replace(array('（', '）'), array('(', ')'), trim($_POST['name']));
            if (!$name) json_return(null, 1, '名称不能为空');

            $data = array(
                'name'      => $name,
                'source'    => 0,
                'target'    => 0,
                'pid'       => $product['id'],
                'intro'     => trim($_POST['intro']),
                'childstd'  => trim($_POST['childstd']),
                'babystd'   => trim($_POST['babystd']),
                'data'      => '',
            );

            $data = filter::apply('product_item_manage_save', $data, $product, $_POST['type']);

            $db -> beginTrans();

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_product_item` SET {$sql} WHERE `id`=:id;") -> execute($value);
                $data['id'] = $_POST['id'];

                $history_msg = "修改了“{$name}”[{$data['id']}]";
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_product_item` {$column} VALUES {$sql};") -> execute($value);
                $data['id'] = $rs;

                $history_msg = "新增了“{$name}”";
            }

            if ($rs === false)
            {
                $db -> rollback();
                json_return(null, 1, '保存失败，请重试');
            }

            // Remove Api push.
            // Product can be edited before post, only.

            // History
            if (!history($product['id'], 'item', $history_msg, $data))
            {
                $db -> rollback();
                json_return(null, 3, '保存失败，请重试');
            }

            if ($db -> commit())
            {
                $return = $data;
                $return = filter::apply('product_item_manage_save_callback', $return);
                json_return($return);
            }
            else
            {
                $db -> rollback();
                json_return(null, 9, '保存失败，请重试');
            }
        }

        // Load item's data or new
        $id = empty($_GET['id']) ? 0 : (int)$_GET['id'];
        if ($id)
        {
            $sql = "SELECT p.`id`, p.`type`, p.`payment`,p.`status`,i.`objtype` AS `itemtype`, i.`id` AS `itemid` FROM `ptc_product_item` AS i
                        LEFT JOIN `ptc_product` AS p ON i.`pid` = p.`id`
                    WHERE i.`id`=:id";
            $product = $db -> prepare($sql) -> execute(array(':id'=>$id));
        }
        else
        {
            $type = trim($_GET['type']);
            $product = $db -> prepare("SELECT `id`, `type`, `payment`, `status`, '{$type}' AS `itemtype`, 0 AS `itemid` FROM `ptc_product` WHERE `id`=:id")
                           -> execute(array(':id'=>(int)$_GET['pid']));
        }

        if (!$product)
            exit("<div class=\"alert alert-danger\" role=\"alert\">产品资料不存在！</div>");
/*
        if ($product[0]['status'] != 0)
            exit("<div class=\"alert alert-warning\" role=\"alert\">产品已发布，不允许修改！</div>");
*/
        $product = $product[0];

        action::exec('product_item_manage_tpl', $product['id'], $product['type'], $product['payment'], $product['itemtype'], $product['itemid']);

        break;


    // Edit items' price & stock
    case 'price':

        // include product hook
        include_once PT_PATH.'hook/hook.php';

        $sql = "SELECT i.*, p.`type` AS `product_type`, p.`payment` AS `product_payment`, p.`org` AS `product_org`, p.`start`, p.`end`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_product` AS p ON p.`id` = i.`pid`
                WHERE i.`id`=:id;";

        if ($_POST)
        {
            $id = $_POST['id'];

            $item = $db -> prepare($sql) -> execute(array(':id'=>$id));
            if (!$item) json_return(null, 1, '产品数据不能存在');
            $item = $item[0];

            // Api push
            // Api is before the action,
            // because we need to ensure the push is running.
            if (!api::push($item['product_type'], $item['pid'], $item['product_payment'], $item['product_org']))
                json_return(null, 1, '更新失败，请重试');

            // Do save action
            action::exec('product_item_manage_price_save', $item);

            // History
            history($item['pid'], 'item', "更新“{$item['name']}”[{$id}]价格/库存", $_POST);
        }

        // load item
        $id = (int)$_GET['id'];
        $item = $db -> prepare($sql) -> execute(array(':id'=>$id));
        $item = $item[0];



        $currencys = $db -> prepare('SELECT * FROM ptc_currency') -> execute();
        $item['currencys'] = $currencys;
        template::assign('item', $item);

        action::exec('product_item_manage_price_tpl', $item);
        break;


    // Edit items' by hook extend
    case 'extend':
        // include product hook
        include_once PT_PATH.'hook/hook.php';

        $sql = "SELECT i.*, p.`type` AS `product_type`, p.`payment` AS `product_payment`, p.`start`, p.`end`
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_product` AS p ON p.`id` = i.`pid`
                WHERE i.`id`=:id;";

        // load item
        $id = (int)$_GET['id'];
        $item = $db -> prepare($sql) -> execute(array(':id'=>$id));
        $item = $item[0];

        action::exec('product_item_manage_extend', $item);
        break;



    // Sort items
    case 'sort':

        if (empty($_POST['sort']))
            json_return(null, 1, "无操作数据记录，请重试");

        $db -> beginTrans();

        $seq = array();
        foreach ($_POST['sort'] as $k => $id)
        {
            $r = $db -> prepare("UPDATE `ptc_product_item` SET `seq`=:seq WHERE `id`=:id AND `pid`=:pid") -> execute(array(':seq'=>$k, ':id'=>(int)$id, ':pid'=>(int)$_POST['product']));
            if ($r === false)
            {
                $db -> rollback();
                json_return(null, $k+1, "保存错误，请重试~");
            }
        }

        if ($db -> commit())
            json_return(1);

        $db -> rollback();
        json_return(null, $k+1, "保存错误，请重试~");

        break;

        // =========== ^ =========== Items =========== ^ ===========



    // View product
    case 'preview':

        $id = (int)$_GET['id'];

        $product = $db -> prepare("SELECT * FROM `ptc_product` WHERE `id`=:id") -> execute(array('id'=>$id));
        if (!$product) exit('产品不存在');

        include_once PT_PATH.'hook/hook.php';

        import(CLASS_PATH.'extend/string');
        action::exec('product_preview', $product[0]);

        break;



    // Load history
    case 'history':
        $id = (int)$_GET['product'];
        $page = (int)$_GET['page'];
        if (!$page) $page = 1;

        $limit = 10;
        $start = $limit * $page;

        $history = $db -> prepare("SELECT `id`,FROM_UNIXTIME(`time`, '%m-%d %H:%i') AS `time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND (`type`='product' OR `type`='item') ORDER BY `id` DESC LIMIT {$start},{$limit};") -> execute(array(':id'=>$id));

        if ($history !== false)
            json_return($history);
        else
            json_return(null, 1, '读取失败，请重试');

        break;

    case 'channel':
        $id   = (int)$_POST['product_id'];
        $orgs = implode(',', $_POST['org']);
        $r    = $db -> prepare("UPDATE `ptc_product` SET `org`=:org WHERE `id`=:id ") 
                    -> execute(array(':org'=>$orgs, ':id'=>(int)$id));
        if ($r === false)
        {                
             json_return(null, 1, "保存错误，请重试~");
        }
        json_return(1);
        break;

    // Load product list
    case 'list':
    default:

        include_once PT_PATH.'hook/hook.php';

        $search = array(
            'status'    => '',
            'from'      =>'',
            'type'      =>'',
            'payment'      =>'',
            'style'      =>'',
        );

        $where = "1=1";
        $condition = array();
        template::assign('keyword','');
        template::assign('status', '');

        // orgs
        $orgs = $db -> prepare("SELECT `id`,`name` FROM `ptc_org`") -> execute();
        template::assign('orgs', $orgs);

        $remind = (int)config('web.product_remind');
        $order = '`id` DESC';

        if (!empty($_GET['keyword']))
        {
            if (is_numeric($_GET['keyword']))
            {
                $where .= " AND `id` = :id";
                $condition[':id'] = (int)$_GET['keyword'];
            }
            else
            {
                $keyword = '%'.$_GET['keyword'].'%';
                $where .= " AND (`name` LIKE :keyword OR `intro` LIKE :keyword)";
                $condition[':keyword'] = $keyword;
            }
            template::assign('keyword', $_GET['keyword']);
        }

        //筛选渠道

        if(!empty($_GET['from'])){
            $where .= " AND FIND_IN_SET(:from, `org`) ";
            $condition[':from'] = intval($_GET['from']);
            $search['from'] = intval($_GET['from']);
        }

        //上架时间
        if(!empty($_GET['start'])){
            $where .= " AND `start` >= :start ";
            $condition[':start'] = strtotime($_GET['start']);
            $search['start'] = strtotime($_GET['start']);
        }

        //下架时间
        if(!empty($_GET['end'])){
            $where .= " AND `end` <= :end ";
            $condition[':end'] = strtotime($_GET['end']);
            $search['end'] = strtotime($_GET['end']);
        }

        //筛选支付类型
        if(!empty($_GET['payment'])){
            $where .= " AND `payment` = :payment ";
            $condition[':payment'] = trim($_GET['payment']);
            $search['payment'] = trim($_GET['payment']);
        }



        if (!empty($_GET['status']))
        {
            if ($_GET['status'] == 'stopselling')
            {
                $where .= " AND (`end` <= :time AND `end` != 0 AND `status`=1)";
                $condition[':time'] = NOW + $remind;

                $order = '`end` ASC';
                template::assign('status', 'stopselling');
                $search['status'] = 'stopselling';
            }
            else
            {
                $search['status'] = $_GET['status'];
                switch ($_GET['status']){
                    case 'uncommitted':
                        $where .= " AND ( ( `audit` = 0 AND `status` >= 0 ) OR ( `audit` = 4 AND `status` = 0 ) ) ";
                        break;

                    case 'offline':
                        $where .= " AND ((`audit` = 0  AND `status` < 0 ) OR (`audit` = 2  AND `status` <= 0) OR ( `audit` = 4 AND `status` < 0 ) )";
                        break;

                    case 'inexamine':
                        $where .= " AND `audit` = 1 ";
                        break;

                    case 'on':
                        $where .= " AND ( (`audit` = 2 AND `status` > 0 ) OR (`audit` = 4 AND `status` > 0 )) ";
                        break;

                    case 'am':
                        $where .= " AND `audit` = 3 ";
                        break;

                    case 'revising':
                        $where .= " AND `audit` = -1 ";
                        break;

                    case 'af':
                        $where .= " AND `audit` = -2 ";
                        break;

                    default:
                        break;

                }
            }
        }

        //产品类型筛选
        if(!empty($_GET['type'])){
            $where .= " AND `type` = :type ";
            $condition[':type'] = trim($_GET['type']);
            $search['type'] = trim($_GET['type']);
        }

        /*if (!empty($_GET['type']))
        {
            if ($_GET['type'] == 'online')
                $where .= ' AND `onlinetime`>=:start AND `onlinetime`<:end';
            else
                $where .= ' AND `offlinetime`>=:start AND `offlinetime`<:end';

            $start = empty($_GET['start']) ? 0 : (int)strtotime($_GET['start']);
            $end   = empty($_GET['end']) ? 0 : (int)strtotime($_GET['end']);

            if (!$start) $start = strtotime(date('Y-m-1'));
            if (!$end) $end = strtotime(date('Y-m-1').' +1 month -1 day');

            $condition[':start'] = $start;
            $condition[':end'] = $end;
        }*/

        if (!empty($_GET['state']))
        {
            if ($_GET['state'] == 'review')
            {
                $where .= ' AND `status` <= 0 AND `audit` = 1';
            }

            if ($_GET['state'] == 'reedit')
            {
                $where .= ' AND `status` <= 0 AND `audit` IN (-1,-2)';
            }

            if ($_GET['state'] == 'online')
            {
                $where .= ' AND `status` = 1';
            }

            if ($_GET['state'] == 'apply')
            {
                $where .= ' AND `audit` = 3';
            }
        }

        // Remind Count
        $remind = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `end`<=:time AND `end`!=0 AND `status`=1") -> execute(array(':time' => NOW + $remind));
        template::assign('remind', $remind[0]['c']);


        // Search List
        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 5);
        $limit = $page -> limit();

        if(isset($_GET['action']) && $_GET['action'] == 'export'){
            $list = $db -> prepare("SELECT `id`,`name`,`type`,`payment`,`start`,`end`,`status`,`audit`,`org` FROM `ptc_product` WHERE {$where} ORDER BY {$order} LIMIT 50;") -> execute($condition);

        }else{

            $list = $db -> prepare("SELECT `id`,`name`,`type`,`payment`,`start`,`end`,`status`,`audit`,`org` FROM `ptc_product` WHERE {$where} ORDER BY {$order} LIMIT {$limit};") -> execute($condition);
        }




        foreach ($list as $k => $v)
        {
            $sql = "SELECT i.`id`, i.`name`, i.`objtype`, i.`objid`, i.`objpid`, i.`ext`, i.`ext2`, i.`start`, i.`end`, i.`online`, i.`offline`, i.`allot`, i.`sold`, i.`status`,
                            h.`name` AS `hotelname`, r.`name` AS `roomname`, f.`code` AS `flight`
                    FROM `ptc_product_item` AS i
                        LEFT JOIN `ptc_hotel` AS h ON i.`objtype`='room' AND i.`objpid`=h.`id`
                        LEFT JOIN `ptc_hotel_room_type` AS r ON i.`objtype`='room' AND i.`objid`=r.`id`
                        LEFT JOIN `ptc_flight` AS f ON i.`objtype`='flight' AND i.`objpid`=f.`id`
                    WHERE i.`pid`=:pid ORDER BY i.`status` DESC, i.`seq` ASC, i.`id` ASC";
            $items = $db -> prepare($sql) -> execute(array(':pid'=>$v['id']));

            $list[$k]['items'] = $items;

            if ($v['audit'] == 3)
            {
                $sql = "SELECT `intro` AS `reason` FROM `ptc_history` WHERE `type` = 'product' AND `pk` = :id ORDER BY `id` DESC LIMIT 0,1;";
                $rs = $db -> prepare($sql) -> execute(array(':id'=>$v['id']));
                $list[$k]['reason'] = $rs[0]['reason'];
            }

        }



        template::assign('page', $page -> show());
        template::assign('list', $list);

        // 状态栏
        $important = array();

        if ($_SESSION['role'] == 2)  // 产品
        {
            // 待审核
            $sql = "SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `status` <= 0 AND `audit` = 1;";
            $rs = $db -> prepare($sql) -> execute();
            $important[] = array('type'=>'待审核', 'count'=>$rs[0]['c'], 'state'=>'review');

            // 重新编辑
            $sql = "SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `status` <= 0 AND `audit` IN (-1,-2);";
            $rs = $db -> prepare($sql) -> execute();
            $important[] = array('type'=>'重新编辑', 'count'=>$rs[0]['c'], 'state'=>'reedit');

            // 申请修改
            $sql = "SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `audit` = 3;";
            $rs = $db -> prepare($sql) -> execute();
            $important[] = array('type'=>'申请修改', 'count'=>$rs[0]['c'], 'state'=>'apply');

            // 已上架
            $sql = "SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `status` = 1;";
            $rs = $db -> prepare($sql) -> execute();
            $important[] = array('type'=>'已上架', 'count'=>$rs[0]['c'], 'state'=>'online');
        }
        elseif ($_SESSION['role'] == 3)  // 运营
        {
            // 待审核
            $sql = "SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `status` <= 0 AND `audit` = 1;";
            $rs = $db -> prepare($sql) -> execute();
            $important[] = array('type'=>'待审核', 'count'=>$rs[0]['c'], 'state'=>'review');

        }
        elseif ($_SESSION['role'] == 1)  // 客服
        {
            // 申请修改 (客服)
            $sql = "SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `audit` = 3;";
            $rs = $db -> prepare($sql) -> execute();
            $important[] = array('type'=>'申请修改', 'count'=>$rs[0]['c'], 'state'=>'apply');
        }

        template::assign('important', $important);


        template::assign('search', $search);

        if(isset($_GET['action']) && $_GET['action'] == 'export'){
            search_export($list, $orgs);
        }

        template::display('product/list');
        break;


}


function search_export($list, $orgs){
    var_export($list);

    $orgs = array_column($orgs, 'name', 'id');

    include_once CLASS_PATH.'PHPExcel.php';
    $objExcel = new PHPExcel();

    $objProps = $objExcel -> getProperties();
    $objProps -> setCreator("PUTIKE.CN");
    $objProps -> setTitle("璞缇客产品组合导出，仅供内部使用");
    $objExcel -> setActiveSheetIndex(0);
    $objActSheet = $objExcel -> getActiveSheet();
    $objActSheet -> setTitle('璞缇客产品组合导出('.date('YmdHis',NOW).')');

    $defaultCss = $objActSheet -> getDefaultStyle();
    $defaultCss -> getFont() -> setSize(11);

    // Default Style
    $objActSheet -> getDefaultRowDimension() -> setRowHeight(18);

    $column_names = array(
        array('column'=>'id', 'name'  => 'ID', 'width' => 15, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'name', 'name'  => '产品名称', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'name', 'name'  => '售卖渠道', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'type', 'name'  => '产品类型', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'name', 'name'  => '状态', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'name', 'name'  => '券名', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '产品介绍', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '关联酒店', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '售卖日期开始', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '售卖日期结束', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '国家', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '省份', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '城市', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '有效订单数', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '有效券数', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '总夜数', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '有效订单金额', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
        array('column'=>'id', 'name'  => '有效券金额', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
    );

// Columns
    $i = 0;
    $field = array();

    foreach ($column_names as $k => $v)
    {
        $code = (floor($i / 26) ? chr(64 + floor($i / 26)) : '') . chr(65 + $i % 26);
        //$objActSheet -> getColumnDimension($code) -> setWidth($v['width']);
        $objActSheet -> getColumnDimension($code) -> setAutoSize(true);
        $objActSheet -> getRowDimension(1) -> setRowHeight(18);
        //$objActSheet -> getStyle("{$code}1") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) -> setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setWrapText(true);
        $objActSheet -> getStyle("{$code}1") -> getFont() -> setBold(true);

        // Style
        $style = $objActSheet -> getStyle("{$code}1");
        $style -> getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('CCCCCCC');

        $objActSheet -> setCellValue("{$code}1", $v['name']);
        $field[$k] = array(
            'code'  => $code,
            'column'    => $v['column']
        );
        $i ++;
    }
    unset($k);
    unset($v);

    $producttypes = producttypes();
    $types = [];
    foreach ($producttypes as $pt_k => $pt_v){
        $types[$pt_v['code']] =  $pt_v['name'];
    }
    //var_export($types);die;
    //var_export($field);die;

    foreach ($list as $key => $value)
    {

        foreach ($field as $k => $v )
        {
            $export_value = $value[$v['column']];

            //售卖渠道



            //产品类型
            if($v['column'] == 'type')
            {
                $export_value = $types[$value[$v['column']]];
            }



            $objActSheet -> setCellValue($v['code'].($key+2), $export_value);

        }

    }

        /*$path_name = 'putike_hotel_export';
        if(!is_dir($path_name))
        {
            mkdir($path_name);
        }

        $file_name = "putike_hotel_".date('YmdHis').'_'.uniqid().".xlsx";*/


    ob_end_clean();
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition:inline;filename=\"璞缇客产品组合导出.xlsx\"");
    header("Content-Transfer-Encoding: binary");
    header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    $objWriter = new PHPExcel_Writer_Excel2007($objExcel);
    $objWriter -> save('php://output');
    /*$objWriter -> save($path_name.DIRECTORY_SEPARATOR.$file_name);
    return $path_name.'/'.$file_name;*/
    exit;


    var_export($list);

    exit;
}