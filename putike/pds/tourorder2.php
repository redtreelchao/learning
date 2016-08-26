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

$db  = db(config('db'));

template::assign('nav', 'Tour');
template::assign('subnav', 'order');

$uid = (int)$_SESSION['uid'];

$status = array(
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
);
template::assign('status', $status);

$method = !empty($_GET['method']) ? $_GET['method'] : 'list';

switch ($method)
{
    // ------------------------- 创建定制卡订单 -------------------------
    case 'make':
        $cardid   = intval($_POST['id']);
        $is_exist  = $db -> prepare("SELECT `id` FROM `ptc_tour_order` WHERE `card`=:card LIMIT 0,1") -> execute(array(':card'=>$cardid));
        if ($is_exist)
            json_return($is_exist[0], 1, '已设计行程规划');

        $sql = "SELECT
                    CONCAT('O', c.`code`) AS `order`,
                    `contact`, `tel`, c.`id` AS `card`, c.`area_id`, c.`adults`, c.`kids`,
                    CONCAT( a.`name`, c.`days`, '日定制游' ) AS `title`,
                    4 AS `status`, {$uid} AS `designer_id`, ".NOW." AS `addtime`, ".NOW." AS `updatetime`
                FROM `ptc_tour_card` AS c
                    LEFT JOIN `ptc_tour_area` AS a ON c.`area_id`=a.`id`
                WHERE c.`id`=:card;";
        $card = $db -> prepare($sql) -> execute(array(':card'=>$cardid));
        if (!$card)
            json_return(null, 1, '定制需求卡不存在');

        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `ptc_tour_card` SET `status`=4 WHERE `id`=:card") -> execute(array(':card'=>$cardid));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 1, '创建失败，请重试');
        }

        list($column, $sql, $value) = array_values(insert_array($card[0]));
        $rs = $db -> prepare("INSERT INTO `ptc_tour_order` {$column} VALUES {$sql};") -> execute($value);
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 2, '创建失败，请重试');
        }

        if (!$db -> commit())
            json_return(null, 9, '创建失败，请重试');
        else
            json_return($rs);

    break;



    // ------------------------- 编辑订单 --------------------------------
    case 'edit':
        $id = intval($_GET['id']);

        $sql = "SELECT o.*, a.`name` AS `areaname`, d.`nickname`
                FROM `ptc_tour_order` AS o
                    LEFT JOIN `ptc_tour_area` AS a ON o.`area_id`=a.`id`
                    LEFT JOIN `ptc_tour_designer` AS d ON o.`designer_id`=d.`id`
                WHERE o.`id`=:id";
        $data = $db -> prepare($sql) -> execute(array(':id'=>$id));
        if (!$data)
            redirect('./tourorder.php');

        $data = $data[0];

        $sql = "SELECT COUNT(*) AS `all`, SUM(IF(`status`=1, 1, 0)) AS `paid` FROM `ptc_tour_order_pay` WHERE `orderid`=:order";
        $pay = $db -> prepare($sql) -> execute(array(':order'=>$id));
        if ($pay && $pay[0]['all'] > 0)
            $data['progress'] = number_format($pay[0]['paid'] / $pay['all'] * 100, 2, '.', '');
        else
            $data['progress'] = '0.00';

        $sql = 'SELECT * FROM `ptc_tour_order_detail` WHERE `orderid`=:order ORDER BY `day` ASC, `seq` ASC, `id` ASC;';
        $details = $db -> prepare($sql) -> execute(array(':order'=>$id));

        $type = [
            //'summary' => '',
            'flight'    => '航班',
            'hotel'     => '酒店',
            'traffic'   => '交通',
            'dining'    => '餐饮',
            'view'      => '景点',
            'play'      => '娱乐',
            'other'     => '其他体验'
        ];

        template::assign('data', $data);
        template::assign('type', $type);
        template::assign('details', $details);
        template::display('tour/order_edit');
    break;


/*
    // ------------------------- 订单预览 --------------------------------
    case 'preview':
        $id   = intval($_GET['id']);
        $data = tourorder_find($id);
        $data['card'] = tourcard_find($data['card']);
        $area = get_area($data['area_id']);
        $data['area_name'] = $area['name'];
        $sql = "select * from ptc_tour_order_detail where orderid=$id group by day"  ;
        $details = $db-> prepare($sql) -> execute();

        $where = "`id`=:id";
        $condition[':id'] = (int)$data['designer_id'];
        $sql = "SELECT `id`,`nickname`,`avatar` FROM `ptc_tour_designer` WHERE {$where} LIMIT 0,1";
        $rs  = $db-> prepare($sql) -> execute($condition);
        $designer =  $rs[0];


        $tour_type = [
            //'summary'=>'',
            'flight' =>'航班',
            'hotel'  =>'酒店',
            'traffic'=>'交通',
            'dining' =>'餐饮',
            'view'   =>'景点',
            'play'   =>'娱乐',
            'other'  =>'其他体验'
        ];


        $map['orderid'] = ['=',$id];
        $pay['total'] = tourorderpay_count($map);
        $map['status'] = ['=',1];
        $pay['paid'] = tourorderpay_count($map);
        $data['progress'] = $pay['paid']/$pay['total']*100;

        template::assign('id',$id);
        template::assign('data', $data);
        template::assign('details',$details);
        template::assign('designer',$designer);
        template::assign('tour_type',$tour_type);
        template::display('tour/tourorder_preview');
    break;

    // ------------------------- 发布预览 --------------------------------
    case 'save_preview':
        $cardid   = (int)$_POST['card'];
        $orderid  = (int)$_POST['id'];
        $status   = (int)$_POST['status'];
        $flag = tourorder_find($orderid);
        if($flag && $flag['total']>0)
        {
            $rs = change_status($cardid,$orderid,$status);
            json_return($rs);
        }else
        {
            json_return(0,0,'没有设置价格');
        }
    break;

    // ------------------------- 订单列表 --------------------------------
    case 'list':

        $param = [];
        $where = scope_array($param);

        $sql   = "SELECT COUNT(id) AS num FROM `ptc_tour_order` WHERE 1 = 1  $where ";
        $num = $db-> prepare($sql) -> execute();

        $count = $num?$num[0]['num']:0;
        $page  = new page($count, 10);
        $limit = $page -> limit();

        $sql   = 'SELECT a.`id`,a.`order`,a.`card`,a.`title`,a.`contact`,a.`addtime`,a.`status`,
                  b.`area_id`,b.`departure`,b.`days`,b.`budget` FROM `ptc_tour_order` AS a INNER JOIN `ptc_tour_card` AS b
                  ON a.`card` = b.`id`   WHERE 1 = 1  '.$where.' LIMIT '.$limit;
        $list = $db-> prepare($sql) -> execute( );

        foreach ($list as $k => $v)
        {
            $list[$k]['departure'] = date('Y-m-d',$v['departure']);
            $list[$k]['days']      = $v['days'];
            $list[$k]['area_name'] = get_area($v['area_id'])['name'];
            $list[$k]['budget']    = $v['budget'];
        }

        template::assign('list', $list);
        template::assign('page', $page -> show());
        template::display('tour/tourorder');

    break;

    // ------------------------- 保存订单 --------------------------------
    case 'save':
        $data    = $_POST;
        $data['designer_id'] =  $_SESSION['uid'];
        $details = $data['detail'];
        unset($data['detail']);
        if(isset($data['id']))
        {
            $id   = $data['id'];
            unset($data['id']);
            $data['updatetime'] = time();
            $flag = tourorder_modify($id,$data);
            if($details && $flag)
            {
                foreach ($details as  $k=>$v)
                {
                    for ($i=0; $i < count($v['title']) ; $i++)
                    {
                        $detail['title']    = $v['title'][$i];
                        $detail['type']     = $v['type'][$i];
                        $detail['describe'] = $v['describe'][$i];
                        $detail['seq']      = $i;
                        $detail['day']      = $k;
                        $detail['summary']  = $v['summary'];
                        $detail['orderid']  = $id;
                        $detail['order']    = $data['order'];
                        $detail['pic']      = isset($v['pic'][$i])?$v['pic'][$i]:'';
                        $detail['template'] = empty($v['template'][$i])?0:1;
                        //var_dump($detail);die;
                        if($v['id'][$i])
                        {
                            list($field, $value) = array_values(update_array($detail));
                            $value[':id'] = $id;
                            $sql = "UPDATE `ptc_tour_order_detail` SET {$field} WHERE `id`=:id;";
                            $flag  = $db -> prepare($sql) -> execute($value);

                        }else
                        {
                            list($column, $value, $param) = array_values(insert_array($detail));
                            $sql = "INSERT INTO `ptc_tour_order_detail` {$column} VALUES {$value};";
                            $flag  = $db -> prepare($sql) -> execute($param);

                        }

                    }

                }
            }
        }else
        {
            $flag = tourorder_add($data);
        }

        json_return($flag);
    break;

    // ------------------------- 修改订单和定制卡状态 --------------------
    case 'change_status':
        $data['updatetime'] = time();
        $data['status']     = $status;
        $flag = tourorder_modify($orderid,$data);
        if($flag)
        {
            $card['status'] = $status;
            $flag = tourcard_modify($cardid,$card);
        }

        json_return($flag);
    break;

    // -------------------------- 支付单编辑 ------------------------------
    case 'pay_edit':
        $id        = intval($_GET['id']);
        $data      = tourorderpay_find($id);
        json_return($data);
    break;

    // -------------------------- 支付单保存 -------------------------------
    case 'pay_save':
        $data = $_POST;
        if(!isset($data['deposit']) || strlen($data['deposit']) == 0){
            json_return('',1,'类型不能为空！');
        }

        if(intval($data['price']) <=0){
            json_return('',1,'金额有误');
        }

        if(isset($data['id'])) //修改
        {
            $id   = $data['id'];
            unset($data['id']);
            $tourorderpay = tourorderpay_find($id);
            if (!$tourorderpay) {
                json_return(null, 1);
            }

            if($data['deposit'] == 1){
                $sql = "SELECT COUNT(*) AS count FROM ptc_tour_order_pay WHERE deposit = 1 AND id <> {$id} AND orderid =".$tourorderpay['orderid'].";";
                $deposits = $db-> prepare($sql) -> execute();
                if($deposits[0]['count']>0){
                    json_return('',1,'订金项目只能存在一个！');
                }
            }

            $tour = tourorder_find($tourorderpay['orderid']);
            //var_export($tour);die;
            $total = $tour['total'];

            $pays = tourorderpay_page_list(['orderid' => ['=', $tourorderpay['orderid']]]);
            $pay_end_id = 0;
            $total_no_current = 0;
            $end = 1;
            foreach ($pays as $key => $value) {
                if ($value['id'] == $id && $value['deposit'] == -1) {
                    json_return(null, 2, '尾款不允许修改');
                }

                if ($value['deposit'] == -1) {
                    $pay_end_id = $value['id'];
                    $pay_end = $value['price'];
                    continue;
                }

                if ($value['id'] == $id) {
                    continue;
                }
                $total_no_current = $total_no_current + $value['price'];

            }

            $charge = $total - $total_no_current - $data['price'];
            if ($charge < 0) {
                json_return(null, 2, '价格计算有误');
            }
            $db->beginTrans();
            $current = tourorderpay_modify($id, $data);
            if ($charge == 0) {//无尾款
                if ($pay_end_id) {
                    //删除历史尾款
                    $end = tourorderpay_remove($pay_end_id);
                }
            } else {//有尾款

                $pay['price'] = $charge;
                $pay['orderid'] = $tourorderpay['orderid'];
                $pay['order'] = $tourorderpay['order'];
                $pay['name'] = '尾款';
                $pay['deposit'] = -1;
                $pay['operator'] = $_SESSION['uid'];
                if ($pay_end_id) {
                    //修改历史尾款
                    $end = tourorderpay_modify($pay_end_id,$pay);
                } else {
                    //增加尾款项目
                    $end = tourorderpay_add($pay);
                }
            }

            if($current !== false && $end !== false){
                $db->commit();
                json_return(null, 0, '操作成功');
            }else{
                $db->rollback();
                json_return(null, 3, '操作失败');
            }


        } else {

            if($data['deposit'] == 1){
                $deposits =  $db-> prepare("SELECT COUNT(*) AS count FROM ptc_tour_order_pay WHERE deposit = 1 AND orderid = {$data['orderid']};") -> execute();

                if($deposits[0]['count']>0){
                    json_return('',1,'订金项目只能存在一个！！');
                }
            }



            $rs = tourorderpay_modify_deposit($data['orderid'], $data['price']);

            if($rs['status'] !==0){
            json_return('',1,$rs['msg']);
            }

            $flag = tourorderpay::add($data);
            $status = $flag=== false? 1:0;
            $msg = $flag=== false? '操作失败':'';

            json_return('',$status,$msg);

        }

    break;

    // -------------------------- 支付单删除 -------------------------------
    case 'pay_remove':
        $id = (int)$_GET['id'];
        $tourorderpay = tourorderpay_find($id);
        if (!$tourorderpay) {
            json_return(null, 1);
        }
        $tour  = tourorder_find($tourorderpay['orderid']);
        $total = $tour['total'];
        $sum = 0;
        $pay_end_id = 0;
        $pays = tourorderpay_page_list(['orderid' => ['=', $tourorderpay['orderid']]]);
        foreach ($pays as $key => $pay){
            if($pay['deposit'] == -1){
                $pay_end_id = $pay['id'];
                continue;
            }

            if($pay['id'] == $id){
                continue;
            }
            $sum = $sum + $pay['price'];
        }
        $charge  = $total - $sum;
        $db->beginTrans();

        if($pay_end_id > 0){
            $end = tourorderpay_modify($pay_end_id, ['price'=> $charge]);
        } else{
            $data['price'] = $charge;
            $data['orderid'] = $tourorderpay['orderid'];
            $data['order'] = $tourorderpay['order'];
            $data['name'] = '尾款';
            $data['deposit'] = -1;
            $data['operator'] = $_SESSION['uid'];
            $end = tourorderpay_add($data);
        }

        $flag = tourorderpay_remove($id);


        if($flag !== false && $end !== false){
            $db->commit();
            json_return(null, 0, '操作成功');
        }else{
            $db->rollback();
            json_return(null, 3, '操作失败');
        }
    break;

    case 'pay_refund':
        $id = (int)$_GET['id'];
        $flag = tourorderpay_find($id);
        json_return($flag);
    break;

    case 'save_refund':
        $data =  $_POST;
        $refund['refundtime']  = time();
        $refund['refundtrade'] = serialize($_POST) ;
        $id   = $data['id'];
        $flag = tourorderpay_modify($id,$refund);
        json_return($flag);
    break;

    case 'price':
        $id   = intval($_GET['id']);
        $data = tourorder_find($id);
        $map['orderid'] = ['=',$id];
        $sql     = "SELECT * FROM ptc_tour_order_pay WHERE orderid = {$id} ORDER BY deposit DESC ,id ASC";
        $paylist = $db -> prepare($sql) -> execute();

        # history
        $where = "`type`=:type AND `pk`=:pk ";
        $condition[':type'] = 'tour';
        $condition[':pk']   = (int)$id;
        $sql = "SELECT * FROM `ptc_history` WHERE {$where} ORDER BY time DESC  LIMIT 0,1";
        $history = $db -> prepare($sql) -> execute($condition);


        $pay['total'] = tourorderpay_count($map);
        $map['status'] = ['=',1];
        $pay['paid'] = tourorderpay_count($map);
        $map['status'] = ['=',0];
        $pay['no'] = tourorderpay_count($map);

        $map['orderid'] = ['=',$id];
        $map['status']  = ['=',1];
        $sum['paid']    =  tourorderpay_sum($map);
        $map['status']  = ['=',0];
        $sum['no']      =  tourorderpay_sum($map);


        template::assign('sum',$sum);
        template::assign('pay',$pay);
        template::assign('id',$id);
        template::assign('data', $data);
        template::assign('paylist',$paylist);
        template::assign('history',$history?$history[0]:'');
        template::display('tour/tourorder_price');
    break;

    case 'save_history':
        $data  = $_POST;
        $flag  = history($data['pk'], 'tour', $data['intro'], '订单');
        json_return($data);
    break;

    case 'get_remark':
        $id   = intval($_GET['id']);
        $data = tourorderpay_find($id);
        json_return($data);
    break;

    case 'modify_price':
        $data    = $_POST;
        $data['designer_id'] =  $_SESSION['uid'];
        $id   = $data['id'];
        unset($data['id']);
        $data['updatetime'] = time();
        $db = db(config('db'));
        $db -> beginTrans();
        $flag_order = tourorder_modify($id,$data);

        $map['orderid'] = ['=',$id];
        $old_list = tourorderpay_page_list($map, 0);
        $deposit_end = 0;
        $deposit_other = 0;
        $pay_id = 0;
        foreach ($old_list as $key => $value){
            if($value['deposit'] == -1){
                $pay_id = $value['id'];
                continue;
            }else{
                $deposit_other = $value['price']+$deposit_other;
            }
        }

        $deposit_end = $data['total'] - $deposit_other;
        if($deposit_end < 0){
            $db -> rollback();
            json_return('',1,'总价不能低于已存在费用之和！');
        }

        if($pay_id == 0){ //没有尾款，则增加尾款项目

            if($deposit_end != 0) {//尾款不等于0 则增加
                $pay['price']    = $deposit_end;
                $pay['orderid']  = $id;
                $pay['order']    = $data['order'];
                $pay['name']     = '尾款';
                $pay['deposit']  = -1;
                $pay['operator'] = $_SESSION['uid'];
                $flag_order_pay  = tourorderpay_add($pay);
            }else{
                $flag_order_pay = true;
            }

        }else{//尾款不等于总数  存在其他支付项，刚修改尾款项目
            $pay['price']   = $deposit_end;
            $pay['operator'] = $_SESSION['uid'];

            if($deposit_end == 0) {//尾款等于0 则删除
                $flag_order_pay = tourorderpay_remove($pay_id);
            }else{
                $flag_order_pay = tourorderpay_modify($pay_id,$pay);
            }

        }

        $flag = ($flag_order_pay !== false) && ($flag_order !== false);
        if($flag){
            $db -> commit();
        }else{
            $db -> rollback();
        }

        json_return('',!$flag);
    break;

    */
}




function get_status($status)
{
    $status_txt = [
                    '1'=>'待处理',
                    '2'=>'标记为优先',
                    '3'=>'标记为有效',
                    '4'=>'行程设计中',
                    '5'=>'需要更改',
                    '6'=>'等待支付',
                    '7'=>'支付成功',
                    '8'=>'旅途中',
                    '9'=>'旅行结束',
                    '10'=>'已过期',
                  ]  ;

    if(array_key_exists($status,$status_txt))
    {
        return $status_txt[$status];
    }else{
        return '状态错误';
    }

}

function get_paytype($type)
{
    $deposit = '';
    switch ($type) {
        case '1':
            $deposit = '定金';
            break;
        case '0':
            $deposit = '普通';
            break;
        case '-1':
            $deposit = '尾款';
            break;

        default:
            $deposit = '普通';
            break;
    }

    return $deposit;
}

function get_paystatus($stauts)
{
    $txt = '';
    switch ($stauts) {
        case 0:
            $txt = '待支付';
            break;
        case 1:
            $txt = '已支付';
            break;
        case -1:
            $txt = '已退款';
            break;

        default:
            $txt = '待支付';
            break;
    }

    return $txt;
}

function  tourorder_add($data)
{
    $db  = db(config('db'));
    list($column, $value, $param) = array_values(insert_array($data));
    $sql = "INSERT INTO `ptc_tour_order` {$column} VALUES {$value};";
    $rs  = $db-> prepare($sql) -> execute($param);
    if (false === $rs)
    {
        return false;
    }
        return $rs;

}

function tourcard_find($id)
{
    $db  = db(config('db'));
    $where = "`id`=:id";
    $condition[':id'] = (int)$id;
    $sql = "SELECT `code`,`contact`,`tel`,`area_id`,`adults`,`kids`,`days`,`departure`,`budget` FROM `ptc_tour_card` WHERE {$where} LIMIT 0,1";
    $rs = $db -> prepare($sql) -> execute($condition);
    if(isset($rs)) return $rs[0];
    return $rs;

}

function tourcard_modify($id,$data)
{
    $db  = db(config('db'));
    list($field, $value) = array_values(update_array($data));
    $value[':id'] = $id;
    $sql = "UPDATE `ptc_tour_card` SET {$field} WHERE `id`=:id;";
    $rs = $db-> prepare($sql) -> execute($value);
    if (false === $rs)
    {
        return !self::$error = '501';
    }
    return $rs;
}

function tourorderpay_find($id)
{
    $db  = db(config('db'));
    $where = "`id`=:id";
    $condition[':id'] = (int)$id;
    $sql = "SELECT * FROM `ptc_tour_order_pay` WHERE {$where} LIMIT 0,1";
    $rs = $db -> prepare($sql) -> execute($condition);
    if(isset($rs)) return $rs[0];
    return $rs;
}

function tourorderpay_count($map=[])
{
    $db  = db(config('db'));
    $where = scope_array($map);
    $sql   = "SELECT COUNT(id) AS num FROM `ptc_tour_order_pay` WHERE 1 = 1  $where ";
    $count = $db-> prepare($sql) -> execute();
    if($count)
    {
           return $count[0]['num'];
    }else
        {
        return 0;
    }

}

function tourorderpay_page_list($map=[],$limit=10)
{
    $db    = db(config('db'));
    $limit = $limit == 0 ? '':'LIMIT '.$limit;//为0 则无分页
    $where = scope_array($map);
    $sql   = 'SELECT * FROM `ptc_tour_order_pay` WHERE 1 = 1  '.$where.' ORDER BY deposit DESC  '.$limit;

    $list = $db -> prepare($sql) -> execute( );
    return $list;
}

function tourorderpay_sum($map)
{
    $db  = db(config('db'));
    $where = scope_array($map);
    $sql   = "SELECT sum(price)  AS num FROM `ptc_tour_order_pay` WHERE 1 = 1  $where ";
    $count = $db-> prepare($sql) -> execute();
    if($count)
    {
        return $count[0]['num'];
    }else
    {
        return 0;
    }
}

function  tourorderpay_modify($id,$data)
{
    $db  = db(config('db'));
    list($field, $value) = array_values(update_array($data));
    $value[':id'] = $id;
    $sql = "UPDATE `ptc_tour_order_pay` SET {$field} WHERE `id`=:id;";
    $rs = $db-> prepare($sql) -> execute($value);
    if (false === $rs)
    {
        return !self::$error = '更新失败';
    }
    return $rs;
}

function  tourorderpay_remove($id)
{
    $db  = db(config('db'));
    $param[':id'] = $id;
    $sql = "DELETE FROM `ptc_tour_order_pay` WHERE id=:id";
    $rs = $db->prepare($sql)->execute($param);
    if (false === $rs)
    {
        return !self::$error = '501';
    }
    return $rs;
}

function tourorderpay_modify_deposit($orderid,$price)
{
    $db  = db(config('db'));
    $map['orderid'] = ['=',$orderid];
    $map['deposit'] = ['=',-1];
    $where = scope_array($map);
    $sql   = "SELECT `price`   FROM `ptc_tour_order_pay` WHERE 1 = 1  $where ";
    $rs    = $db-> prepare($sql) -> execute();
    $final = $rs[0];
   // var_dump($final);die;
    if($final['price'] && (($final['price']-$price)>0))
    {
        $sql = "UPDATE `ptc_tour_order_pay` SET price=price-$price WHERE `orderid`=$orderid AND deposit=-1 ;";
        $rs =  $db -> prepare($sql) -> execute();
        if (false === $rs)
        {
            //return self::$error_msg = '501';
            return ['status' =>1, 'msg'=> '操作失败','data'=>''];

        }
        return ['status' =>0, 'msg'=> '','data'=>''];
        //return $rs;
    }else
    {
        return ['status' =>1, 'msg'=> '支付金额超出总金额','data'=>''];
            //return self::$error_msg = '601';
    }

}

function  tourorderpay_add($data)
{
    $db  = db(config('db'));
    list($column, $value, $param) = array_values(insert_array($data));
    $sql = "INSERT INTO `ptc_tour_order_pay` {$column} VALUES {$value};";
    $rs  = $db -> prepare($sql) -> execute($param);
    if (false === $rs)
    {
        return false;
    }
    return $rs;

}





function tourorder_find($id)
{
    $db  = db(config('db'));
    $where = "`id`=:id";
    $condition[':id'] = (int)$id;
    $sql = 'SELECT * FROM `ptc_tour_order` '." WHERE {$where} LIMIT 0,1";
    $rs  = $db-> prepare($sql) -> execute($condition);
    if(isset($rs)) return $rs[0];
    return $rs;
}

function  tourorder_modify($id,$data)
{
    $db  = db(config('db'));
    list($field, $value) = array_values(update_array($data));
    $value[':id'] = $id;
    $sql = "UPDATE `ptc_tour_order` SET {$field} WHERE `id`=:id;";
    $rs = $db-> prepare($sql) -> execute($value);
    if (false === $rs)
    {
        return '修改失败';
    }
    return $rs;
}

function tourorder_findbycard($card)
{
    $db  = db(config('db'));
    $rs = $db->prepare("SELECT * FROM `ptc_tour_order` WHERE `card`=:card LIMIT 0,1") -> execute(array(':card'=>$card));
    if($rs)
     return $rs[0];

}


function get_area($id)
{
    $db  = db(config('db'));
    $area = $db -> prepare("SELECT `id`,`name` FROM `ptc_tour_area` WHERE `id`=:id") -> execute(array(':id'=>$id));
    if($area)
        return $area[0];
    else
        return null;

}


function tourorderdetail_page_list($map=[],$limit=10)
{
    $db  = db(config('db'));
    $where = scope_array($map);
    $sql   = 'SELECT * FROM `ptc_tour_order_detail`  WHERE 1 = 1  '.$where.' ORDER BY day ASC, seq ASC LIMIT '.$limit;
    $list = $db -> prepare($sql) -> execute( );
    return $list;
}

// 将数组拆分成查询条件
function scope_array($map,$operator='AND')
{
    $where = ' ';
    if($map)
    {
        foreach ($map as $k => $v)
        {
            if($v[0]=='in')
            {
                $val = "(".$v[1].")";
            }elseif($v[0]=='between')
            {
                $val = $v[1];
            }elseif($v[0]=='FIND_IN_SET')
            {
                $val = "FIND_IN_SET($v[1],$k)";
                $k   ='';
                $v[0] = '';
            }
            else
            {
                $val = "'".$v[1]."'";
            }
            $where .= $operator.' '.$k.' '.$v[0].' '.$val.' ';
        }
    }

    return $where;
}













