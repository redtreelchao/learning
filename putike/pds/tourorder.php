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

$method = !empty($_GET['method']) ? $_GET['method'] : 'list';
$method_map = [
        'create'  =>'create',
        'edit'    =>'edit',
        'preview' =>'preview',
        'save_preview' => 'save_preview',
        'list'    =>'get_list',
        'search'  =>'search',
        'save'    =>'save',
        'change_status' => 'change_status',
        'remove'  =>'remove',
        'pay_edit'=>'pay_edit',
        'pay_save'=>'pay_save',
        'pay_remove'=>'pay_remove',
        'pay_refund'=>'pay_refund',
        'save_refund'=>'save_refund',
        'price'        =>'price',
        'save_history' =>'save_history',
        'get_remark'   =>'get_remark',
        'modify_price' =>'modify_price',
        'make'         =>'make',
        'history'      =>'tourorder_history',
        'deldetail'     => 'deldetail',
        'apply_refund'  =>'apply_refund' //订单退款


        ];


if(array_key_exists($method, $method_map))
{
    //echo $method_map[$method];die;
    call_user_func($method_map[$method]);
}

/**
 * 订单退款
 */

function apply_refund(){
    //接收退款原始信息 入库
    $apply_total = trim($_POST['refundmoney']);//申请退款金额
    $refundname = trim($_POST['refundname']);//申请付款人
    $refundremark = trim($_POST['refundremark']);//申请退款备注
    $orderid = intval(trim($_POST['refundorderid']));

    if(empty($refundname)){
        json_return('',1,'付款人不能为空');
    }


    $db  = db(config('db'));
    $order = $db->prepare('SELECT * FROM ptc_tour_order   WHERE id = :id')->execute([':id'=> $orderid]);
    if(!$order  || count($order)<=0){
        json_return('',2,'未找到订单');
    }

    $total_check = $db->prepare("SELECT sum(price) as total FROM ptc_tour_order_pay WHERE orderid = :orderid AND status = 1")->execute([':orderid'=>$orderid]);
    if(!$total_check || ($total_check[0]['total']<$apply_total)){
        json_return('',3,'退款金额不能大于已支付金额');
    }

    $now = time();
    //申请信息入库
    $apply_data = [
        ':orderid'   => $orderid,
        ':apply_total'   => $apply_total,
        ':apply_refund_name'=>$refundname,
        ':apply_fefund_remark'=>$refundremark,
        ':apply_time'    => $now,
        ':order'    => $order[0]['order']
    ];
    //var_export($apply_data);die;


    $refundhave =$db->prepare("SELECT count(*) AS counts FROM ptc_tour_order_refund WHERE orderid = :orderid")->execute([':orderid'=> $orderid]);
    if($refundhave === false){
        json_return('',4,'查询出错');
    }

    if($refundhave[0]['counts']>0){
        json_return('',5,'已存在退款记录');
    }

    $db -> beginTrans();


    $sql = "INSERT INTO `ptc_tour_order_refund` SET orderid= :orderid,`order`=:order,apply_total=:apply_total,apply_refund_name=:apply_refund_name,apply_fefund_remark=:apply_fefund_remark,apply_time=:apply_time";
    $rs = $db -> prepare($sql) -> execute($apply_data);
    if($rs === false){
        $db -> rollback();
        json_return('',6,'申请出错，请重试');
    }

    //修改订单状态

    $order_status = $db->prepare('UPDATE ptc_tour_order SET status = 12 WHERE id = :orderid ')->execute([':orderid'=> $orderid]);
    if($order_status === false){
        $db -> rollback();
        json_return('',7,'修改订单状态出错');
    }

    $card_status = $db->prepare('UPDATE ptc_tour_card SET status = 12 WHERE id = (SELECT card FROM ptc_tour_order WHERE id = :id)')->execute([':id'=>$orderid]);
    if($card_status === false){
        $db -> rollback();
        json_return('',7,'修改定制卡状态出错');
    }

    //历史记录
    $data['updatetime'] = $now;
    $data['refundname'] = $refundname;
    $data['refundmoney'] = $apply_total;
    $data['refundremark'] = $refundremark;
    $data['orderid'] = $orderid;



    $history = history($orderid, 'tour', '申请了退款', $data);
    if($history === false){
        $db -> rollback();
        json_return('',8,'增加日志出错');
    }

    $db -> commit();
    json_return('',0,'');

}


function deldetail($id){
    $db  = db(config('db'));
    $id  = intval($_POST['id']);

    $rs = $db -> prepare("DELETE FROM `ptc_tour_order_detail` WHERE `id`=:id") -> execute(array(':id'=>$id));
    if($rs){
        json_return('', 0, '');
    }else{
        json_return('', 1, '删除失败');
    }


}

function tourorder_history()
{
    $db  = db(config('db'));
    $id  = intval($_GET['id']);
    $sql = "SELECT COUNT(id) AS num FROM `ptc_history` WHERE 1 = 1 AND  type=:type AND pk=:pk ";
    $num = $db-> prepare($sql) -> execute([':type'=>'tour',':pk'=>$id]);
    $count = $num?$num[0]['num']:0;
    $page  = new page($count, 10);
    $limit = $page -> limit();
    $sql   = 'SELECT * FROM `ptc_history` WHERE 1 = 1  AND type=:type AND pk=:pk LIMIT '.$limit;
    $list = $db-> prepare($sql) -> execute([':type'=>'tour',':pk'=>$id]);
    
    template::assign('list', $list);
    template::assign('page', $page -> show());    
    template::display('tour/tourorder_history');
}

function make()
{
    $db       = db(config('db'));
    $cardid   = intval($_POST['id']);
    $uid      = $_SESSION['uid'];
    $is_exist = $db -> prepare("SELECT `id` FROM `ptc_tour_order` WHERE `card`=:card LIMIT 0,1") -> execute(array(':card'=>$cardid));
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
    //echo $sql;die;        
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
}

function create()
{
    $db        = db(config('db'));
    $card_id   = intval($_GET['id']);
    $is_exist  = tourorder::findbycard($card_id);
    if($is_exist)
    {
        json_return(0,0,'定制卡已存在');
    }

    $c['status'] = 4;
    tourcard::modify($card_id,$c);


    $card    = tourcard::find($card_id);
    $area    = $db -> prepare("SELECT * FROM `ptc_tour_area` WHERE `id`=:id") -> execute(array(':id'=>$card['area_id']));
    $data['order']   = 'O'.$card['code'];
    $data['contact'] = $card['contact'];
    $data['tel']     = $card['tel'];
    $data['card']    = $card_id;
    $data['area_id'] = $card['area_id'];
    $data['adults']  = $card['adults'];
    $data['kids']    = $card['kids'];
    $title           = $area[0]['name'].$card['days'].'日定制游';
    $data['title']   = $title;
    $data['status']  = 4; //行程设计中
    $data['designer_id'] = $_SESSION['uid'];
    $data['addtime'] = time();
    $flag = tourorder::add($data);

    redirect('/tourorder.php?method=edit&id='.$flag);




}

function get_list()
{
    $db        = db(config('db'));
    $designers = designer::get();


    $where = '';
    if($_GET['keyword'])
    {
        $where  = '  AND a.`order` like \'%'.$_GET['keyword'].'%\'';
        $where .=' OR a.contact  like \'%'.$_GET['keyword'].'%\'';
        $where .=' OR a.tel like  \'%'.$_GET['keyword'].'%\'';
    }
    

    $sql   = "SELECT COUNT(id) AS num FROM `ptc_tour_order` WHERE 1 = 1  $where ";
    //echo $sql;die;
    $num = $db-> prepare($sql) -> execute();
    $count = $num?$num[0]['num']:0;
    $page  = new page($count, 10);
    $limit = $page -> limit();

    $sql   = 'SELECT a.`id`,a.`order`,a.`card`,a.`title`,a.`contact`,a.`addtime`,a.`status`,
                  b.`area_id`,b.`departure`,b.`days`,b.`budget` FROM `ptc_tour_order` AS a INNER JOIN `ptc_tour_card` AS b
                  ON a.`card` = b.`id`   WHERE 1 = 1  '.$where.' LIMIT '.$limit;
    $list = $db-> prepare($sql) -> execute( );
    //var_dump($list);die;
    foreach ($list as $k => $v)
    {
        $card = tourcard::find($v['card']);
        //var_dump($card);die;
        $list[$k]['departure'] = date('Y-m-d',$card['departure']);
        $list[$k]['days']      = $card['days'];

        $list[$k]['area_name'] = get_area($card['area_id'])['name'];
        $list[$k]['budget']    = $card['budget'];
    }

   
    template::assign('list', $list);
    template::assign('page', $page -> show());
    template::assign('keyword',isset($_GET['keyword'])?$_GET['keyword']:'');
    template::display('tour/tourorder');

}

function edit()
{
    $db    = db(config('db'));
    $id    = intval($_GET['id']);
    $where = "a.`id`=:id";
    $condition[':id'] = $id;   
    $sql = " SELECT a.*,b.`code` FROM `ptc_tour_order` as a LEFT JOIN `ptc_tour_card` as b ON a.`card` = b.`id` WHERE {$where} LIMIT 0,1";        
    $rs = $db-> prepare($sql) -> execute($condition);
    $data      = $rs[0];

    $area      = get_area($data['area_id']);

    $data['area_name'] = $area['name'];
    $designer  = user::info($data['designer_id']);

    
    $data['progress'] = pay_progress($id);
    


    if($data){
        $detail = tourorderdetail::page_list(['orderid' => ['=',$id]],100);
    }

    $lists = [];
    foreach ($detail as $key => $value){
        $lists[$value['day']][] = $value;
    }

    $tour_type = [
        //'summary'=>'',
        'flight'=>'航班',
        'hotel'=>'酒店',
        'traffic'=>'交通',
        'dining'=>'餐饮',
        'view'=>'景点',
        'play'=>'娱乐',
        'other'=>'其他体验'
    ];



    $day_sql = "SELECT count(DISTINCT `day`) as days FROM ptc_tour_order_detail WHERE orderid = ".$data['id'];
    $days = $db -> prepare($day_sql) -> execute();

    if(empty($days[0]['days'])){$days[0]['days']=1;}
    template::assign('data', $data);
    template::assign('tourtype', $tour_type);
    template::assign('days', $days[0]['days']);
    template::assign('lists', $lists);
    template::assign('designer',$designer);
    template::display('tour/tourorder_edit');

}

function save_preview()
{
    $cardid   = (int)$_POST['card'];
    $orderid  = (int)$_POST['id'];
    $status   = (int)$_POST['status'];


    $flag = tourorder::find($orderid);
    if($flag && $flag['total']>0)
    {
        $rs = change_status($cardid,$orderid,$status);
        json_return($rs);
    }else
    {
        json_return(0,0,'没有设置价格');
    }

}

function change_status($cardid,$orderid,$status)
{

    $data['updatetime'] = time();
    $data['status']     = $status;
    $flag = tourorder::modify($orderid,$data);

    if($flag !== false)
    {
        $card['status'] = $status;
        $flag = tourcard::modify($cardid,$card);
    }

    $flag = $flag === false ? 0 :1;
    json_return($flag);
}



function save()
{
    $data    = $_POST;
    $data['designer_id'] =  $_SESSION['uid'];
    $details = $data['detail'];
    unset($data['detail']);



    if(isset($data['id']))
    {
        $id   = $data['id'];
        unset($data['id']);
        $data['updatetime'] = time();
        $flag = tourorder::modify($id,$data);
        history($id, 'tour', '修改了行程概要', $data);  

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
                    $detail['pic']      = $v['pic'][$i];
                    $detail['template'] = empty($v['template'][$i])?0:1;
                    //var_dump($detail);die;
                    if($v['id'][$i])
                    {
                        $flag = tourorderdetail::modify($v['id'][$i],$detail);                      
                       
                        history($id, 'tour', '修改了行程详情', $detail);
                    }else
                    {
                       $flag = tourorderdetail::add($detail);                      
                      
                       history($id, 'tour', '添加了行程详情', $detail);
                    }

                }

            }
        }
    }else
    {
        $flag = tourorder::add($data);
       
        history($flag, 'tour', '添加了行程概要', $data);        
    }

    json_return($flag);
}

function search()
{
    $txt = $_GET['txt'];
    $sql = "SELECT * FROM ptc_tour_order_detail WHERE `title` LIKE '%$txt%' AND `template`=1 ";
    $rs  = tourorderdetail::findbysql($sql);
    json_return($rs);

}


function modify_price()
{
    $data    = $_POST;
    $data['designer_id'] =  $_SESSION['uid'];
    $id   = $data['id'];
    unset($data['id']);
    $data['updatetime'] = time();
    $db = db(config('db'));
    $db -> beginTrans();
    $flag_order = tourorder::modify($id,$data);

    $map['orderid'] = ['=',$id];
    $old_list = tourorderpay::page_list($map, 0);
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
            $pay['price']   = $deposit_end;
            $pay['orderid'] = $id;
            $pay['order']   = $data['order'];
            $pay['name']    = '尾款';
            $pay['deposit'] = -1;
            $pay['operator'] = $_SESSION['uid'];
            $flag_order_pay = tourorderpay::add($pay);
        }else{
            $flag_order_pay = true;
        }

    }else{//尾款不等于总数  存在其他支付项，刚修改尾款项目
        $pay['price']   = $deposit_end;
        $pay['operator'] = $_SESSION['uid'];

        /*if($deposit_end == 0) {//尾款等于0 则删除
            $flag_order_pay = tourorderpay::remove($pay_id);
        }else{
            $flag_order_pay = tourorderpay::modify($pay_id,$pay);
        }*/

        $flag_order_pay = tourorderpay::modify($pay_id,$pay);

    }

    $flag = ($flag_order_pay !== false) && ($flag_order !== false);
    if($flag){
        $db -> commit();
    }else{
        $db -> rollback();
    }

    json_return('',!$flag);
}

function remove()
{
    $id = (int)$_GET['id'];
    $flag = tourorder::remove($id);
    json_return($flag);
}


function pay_edit()
{
    $id        = intval($_GET['id']);
    $data      = tourorderpay::find($id);
    json_return($data);
}

function pay_remove()
{
    $id = (int)$_GET['id'];



    $tourorderpay = tourorderpay::find($id);
    if (!$tourorderpay) {
        json_return(null, 1);
    }
    $tour = tourorder::find($tourorderpay['orderid']);

    //var_export($tour);die;

    $total = $tour['total'];
    $sum = 0;
    $pay_end_id = 0;
    $pays = tourorderpay::page_list(['orderid' => ['=', $tourorderpay['orderid']]]);
    //var_export($pays);die;

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

    //var_dump($sum);die;
    $charge  = $total - $sum;



    $db = db(config('db'));
    $db->beginTrans();

    if($pay_end_id > 0){
        $end = tourorderpay::modify($pay_end_id, ['price'=> $charge]);
    } else{
        $data['price'] = $charge;
        $data['orderid'] = $tourorderpay['orderid'];
        $data['order'] = $tourorderpay['order'];
        $data['name'] = '尾款';
        $data['deposit'] = -1;
        $data['operator'] = $_SESSION['uid'];
        $end = tourorderpay::add($data);
    }

    $flag = tourorderpay::remove($id);


    if($flag !== false && $end !== false){
        $db->commit();
        json_return(null, 0, '操作成功');
    }else{
        $db->rollback();
        json_return(null, 3, '操作失败');
    }



}

function pay_refund()
{
    $id = (int)$_GET['id'];
    $flag = tourorderpay::find($id);
    json_return($flag);
}

function preview()
{
    $db  = db(config('db'));
    $id   = intval($_GET['id']);

    $sql = "SELECT a.*,b.days,b.code,c.nickname,c.avatar,c.mobile FROM `ptc_tour_order` as a  
            INNER JOIN `ptc_tour_card` AS b  ON a.card  = b.id
            LEFT JOIN `ptc_tour_designer` AS c ON a.designer_id = c.uid
            WHERE a.id=$id LIMIT 0,1";
    //echo $sql;die;
    $rs =  $db -> prepare($sql) -> execute();
    //var_dump($rs);die;
    $data = $rs[0];
    $area      = get_area($data['area_id']);
    $data['area_name'] = $area['name'];
    $sql = "select * from ptc_tour_order_detail where orderid=$id  "  ;
    $details = $db -> prepare($sql) -> execute();
    

    
   
    $data['progress'] = pay_progress($id);

    $tour_type = [
        //'summary'=>'',
        'flight'=>'航班',
        'hotel'=>'酒店',
        'traffic'=>'交通',
        'dining'=>'餐饮',
        'view'=>'景点',
        'play'=>'娱乐',
        'other'=>'其他体验'
    ];


    template::assign('id',$id);
    template::assign('data', $data);
    template::assign('details',$details);
    
    template::assign('tour_type',$tour_type);
    template::display('tour/tourorder_preview');
}

function price()
{
    $paytype = [
        'offline'=>'银行汇款',
        'alipay'=>'支付宝',
        'weixin'=>'微信',
        'online'=>'在线支付'
    ];

    $db   = db(config('db'));
    $id   = intval($_GET['id']);
    $data = tourorder::find($id);
    $map['orderid'] = ['=',$id];
    $sql = "SELECT * FROM ptc_tour_order_pay WHERE orderid = {$id} ORDER BY deposit DESC ,id ASC";
    $paylist = tourorderpay::findbysql($sql);

    $deposit = $total = 0;
    $deposit_check = $db->prepare("SELECT price FROM ptc_tour_order_pay WHERE orderid = {$id} AND deposit = -1 ")->execute();
    if($deposit_check){
        $deposit = $deposit_check[0]['price'];
    }
    $total_check = $db->prepare("SELECT total FROM ptc_tour_order WHERE id = {$id} ")->execute();
    if($total_check){
        $total = $total_check[0]['total'];
    }
//var_export($total);die;
    if($deposit && $total){
        $steppay = true;
    }else{
        $steppay = false;
    }


    
    # history
    $where = "`type`=:type AND `pk`=:pk ";
    $condition[':type'] = 'tour';
    $condition[':pk']   = (int)$id;
    $sql = "SELECT * FROM `ptc_history` WHERE {$where} ORDER BY time DESC";
    $history = $db -> prepare($sql) -> execute($condition);
    //var_export($history);die;

    $map['deposit'] = ['<>',-1];

    $pay['total'] = tourorderpay::count($map);
    $map['status'] = ['=',1];
    $pay['paid'] = tourorderpay::count($map);
    $map['status'] = ['=',0];
    $pay['no'] = tourorderpay::count($map);

    $map['orderid'] = ['=',$id];
    $map['status'] = ['=',1];
    $sum['paid'] =  tourorderpay::sum($map);
    $map['status'] = ['=',0];
    $sum['no'] =  tourorderpay::sum($map);


    template::assign('steppay',$steppay);
    template::assign('paytype',$paytype);
    template::assign('sum',$sum);
    template::assign('pay',$pay);
    template::assign('id',$id);
    template::assign('data', $data);
    template::assign('paylist',$paylist);
    template::assign('history',$history);
    template::display('tour/tourorder_price');
}

function pay_save()
{
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
        $tourorderpay = tourorderpay::find($id);
        if (!$tourorderpay) {
            json_return(null, 1);
        }

        if($data['deposit'] == 1){
            $sql = "SELECT COUNT(*) AS count FROM ptc_tour_order_pay WHERE deposit = 1 AND id <> {$id} AND orderid =".$tourorderpay['orderid'].";";
            $deposits = tourorderpay::findbysql($sql);
            if($deposits[0]['count']>0){
                json_return('',1,'订金项目只能存在一个！');
            }
        }

        $tour = tourorder::find($tourorderpay['orderid']);
        //var_export($tour);die;
        $total = $tour['total'];

        $pays = tourorderpay::page_list(['orderid' => ['=', $tourorderpay['orderid']]]);
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

        $db = db(config('db'));
        $db->beginTrans();






        $current = tourorderpay::modify($id, $data);
        //if ($charge == 0) {//无尾款

            /*if ($pay_end_id) {

                $end = tourorderpay::modify($pay_end_id, ['price'=>0]);
            }*/
        //} else { //有尾款

            $pay['price'] = $charge;
            $pay['orderid'] = $tourorderpay['orderid'];
            $pay['order'] = $tourorderpay['order'];
            $pay['name'] = '尾款';
            $pay['deposit'] = -1;
            $pay['operator'] = $_SESSION['uid'];


            if ($pay_end_id) {
                //修改历史尾款
                $end = tourorderpay::modify($pay_end_id,$pay);
            } else {
                //增加尾款项目
                $end = tourorderpay::add($pay);
            }
        //}

        if($current !== false && $end !== false){
            $db->commit();
            json_return(null, 0, '操作成功');
        }else{
            $db->rollback();
            json_return(null, 3, '操作失败');
        }


    } else {

        if($data['deposit'] == 1){
            $deposits = tourorderpay::findbysql("SELECT COUNT(*) AS count FROM ptc_tour_order_pay WHERE deposit = 1 AND orderid = {$data['orderid']};");
            if($deposits[0]['count']>0){
                json_return('',1,'订金项目只能存在一个！！');
            }
        }


        $db   = db(config('db'));
        $db -> beginTrans();
        $rs = tourorderpay::modify_deposit($data['orderid'], $data['price']);

        if($rs['status'] !==0){
            $db -> rollback();
            json_return('',1,$rs['msg']);
        }


        /*$sql = "DELETE  FROM ptc_tour_order_pay WHERE `orderid`=:orderid AND deposit=-1 AND price = 0;";
        $leastPrice = $db->prepare($sql)->execute([':orderid'=>$data['orderid']]);

        if($leastPrice=== false){
            $db -> rollback();
            json_return('',1,'操作失败');
        }*/



        $flag = tourorderpay::add($data);

        $status = $flag=== false? 1:0;
        $msg = $flag=== false? '操作失败':'';

        if($status == 1){
            $db -> rollback();
        }else{
            $db -> commit();
        }

        json_return('',$status,$msg);

    }

}

function save_history()
{
    $data             = $_POST;
    $data['username'] = $_SESSION['name'];
    $data['time']     = time();
    $data['uid']      = $_SESSION['uid'];
    $flag             = history($data['pk'], 'tour', $data['intro'], $data['data']);
    $data['time'] = date('Y-m-d H:i:s',$data['time']);
    //$data['data'] =json_decode($data['data'],true);
    json_return($data);
}

function get_remark()
{
    $id   = intval($_GET['id']);
    $data = tourorderpay::find($id);
    json_return($data);
}

function save_refund()
{
    $data =  $_POST;
    $refund['refundtime']  = time();
    $refund['refundtrade'] = serialize($_POST) ;
    $id   = $data['id'];
    $flag = tourorderpay::modify($id,$refund);
    json_return($flag);

}

function get_status($status)
{
    $status_txt = [
                    '0'=>'已核实',
                    '1'=>'待处理',
                    '2'=>'优先',
                    '3'=>'无效',
                    '4'=>'行程设计中',
                    '5'=>'需要更改',
                    '6'=>'等待支付',
                    '7'=>'支付成功',
                    '8'=>'旅途中',
                    '9'=>'旅行结束',
                    '10'=>'已过期',
                    '11'=>'已退款',
                    '12'=>'退款中'        
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

function get_area($id)
{
    $db = db(config('db'));
    $area = $db -> prepare("SELECT * FROM `ptc_tour_area` WHERE `id`=:id") -> execute(array(':id'=>$id));
    if($area)
        return $area[0];
    else
        return null;

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

function pay_progress($order_id)
{
    $db = db(config('db'));
   
   $sql = "SELECT COUNT(*) AS `all`, SUM(IF(`status`=1, 1, 0)) AS `paid` FROM `ptc_tour_order_pay` WHERE `orderid`=:order AND `deposit`<>-1";
        $pay = $db -> prepare($sql) -> execute(array(':order'=>$order_id));

        
        if ($pay && $pay[0]['all'] > 0)
            $progress = number_format($pay[0]['paid'] / $pay[0]['all'] * 100, 2, '.', '');
        else
            $progress = '0.00';
    
    return $progress;
}










