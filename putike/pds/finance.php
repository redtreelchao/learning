<?php
/**
 * Created by PhpStorm.
 * User: 玉鑫
 * Date: 2016/3/16
 * Time: 14:35
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

$method = empty($_GET['method']) ? 'check' : $_GET['method'];

template::assign('nav', 'Finance');
template::assign('method', $method);


$paytype = [
    'offline' => '银行打款',
    'alipay' => '支付宝',
    'weixin' => '微信',
    'online' => '在线支付'
];
template::assign('paytype', $paytype);

switch ($method){
    //线下支付退款
    case 'refund':

        if($_POST){
            $id = intval($_POST['id']);
            $refundtype = trim($_POST['refundtype']);
            if(empty($refundtype)){
                json_return('',1,'退款渠道不能为空');
            }

            $refundname = trim($_POST['refundname']);
            if(empty($refundname)){
                json_return('',1,'收款人不能为空');
            }

            $refundaccount = trim($_POST['refundaccount']);
            if(empty($refundaccount)){
                json_return('',1,'退款帐号不能为空');
            }

            $refundtrade = trim($_POST['refundtrade']);
            if(empty($refundtrade)){
                json_return('',1,'流水号不能为空');
            }

            $refundbankaccount = trim($_POST['refundbankaccount']);//开户行

            $refundtime = time();
            $query_sql = "SELECT * FROM ptc_tour_order_refund WHERE id = :id AND (status = :status);";
            $check = $db->prepare($query_sql)->execute([':id' => $id, ":status" => 0]);


            //未找到记录
            if(count($check) < 1){
                json_return('',2,'未找到对应状态的记录');
            }

            $refunddata = [
                ':refundaccount' => $refundaccount,//退款帐号
                ':refundbankaccount'    => $refundbankaccount, //开户行
                ':refundtype'    =>  $refundtype,//退款渠道
                ':refundtrade'   => $refundtrade,
                ':refundtime'    => $refundtime,
                ':refundname'   => $refundname,
                ':id'=> $id
            ];

            $db -> beginTrans();

            $update_pay = $db->prepare("UPDATE ptc_tour_order_refund SET status = 1,refundname=:refundname,refundaccount=:refundaccount,refundbankaccount=:refundbankaccount,refundtype = :refundtype, refundtime = :refundtime, refundtrade = :refundtrade WHERE id = :id")
                ->execute($refunddata);

            $order = $db->prepare("SELECT * FROM ptc_tour_order WHERE id= :id")->execute([':id'=>$check[0]['orderid']]);
            if(!$order || count($order)<=0){
                $db -> rollback();
                json_return('',2,'未找到对应订单或已退款');
            }

                $update_order = $db->prepare('UPDATE ptc_tour_order SET status = 11 WHERE id = :id')->execute([':id'=> $check[0]['orderid']]);
                $card_status = $db->prepare('UPDATE ptc_tour_card SET status = 11 WHERE id = (SELECT card FROM ptc_tour_order WHERE id = :id)')->execute([':id'=>$check[0]['orderid']]);


            //历史记录
            $historydatas = [
                'refundaccount' => $refundaccount,//退款帐号
                'refundbankaccount'    => $refundbankaccount, //开户行
                'refundtype'    =>  $refundtype,//退款渠道
                'refundtrade'   => $refundtrade,
                'refundtime'    => $refundtime,
                'refundname'   => $refundname,
                'id'=> $id,
                'remark'    =>'申请收款人：'.$check[0]['apply_refund_name'].',实际收款人：'.$refundname,
            ];



            $history = history($check[0]['orderid'], 'tour', '审核了退款请求', $historydatas);


            if($update_pay == false || $update_order == false || $card_status==false || $history === false){
                $db -> rollback();
                json_return('',2,'更新失败');
            }else{
                $db -> commit();
                json_return($return_data,0,'');
            }



        }else{
            $count_sql = "SELECT count(*) c FROM ptc_tour_order_return AS r   ORDER BY r.status ASC ,r.apply_time ASC";

            $count = $db->prepare($count_sql)->execute();
            $page = new page($count[0]['c'], 15);
            $limit = $page->limit();
            $sql = "SELECT r.*,o.title,o.contact FROM ptc_tour_order_refund AS r LEFT JOIN ptc_tour_order AS o ON r.orderid = o.id ORDER BY r.status ASC ,r.apply_time ASC";

            $list = $db->prepare($sql)->execute();
            template::assign('list', $list);
            template::assign('page', $page->show());

            template::assign('subnav', 'refund');
            template::display('finance/refund');

        }




        break;

    //发票管理
    case 'invoice':

        if($_POST){
            $orderid = intval($_POST['orderid']);
            $expresstype = trim($_POST['expresstype']);
            $expressno = trim($_POST['expressno']);

            if($orderid <= 0){
                json_return('',1,'参数有误');
            }

            if(empty($expressno) || empty($expresstype)){
                json_return('',2,'请输入正确的快递公司信息');
            }

            $db -> beginTrans();
            $sql = 'UPDATE ptc_tour_order_ext SET expresstype = :expresstype, expressno = :expressno WHERE ISNULL(expressno) AND orderid = :orderid;';
            $up = $db->prepare($sql)->execute([':expresstype' => $expresstype, ':expressno' => $expressno, ':orderid' => $orderid]);

            $history_data = ['expresstype' => $expresstype, 'expressno' => $expressno];
            $history = history($orderid, 'tour', '发票管理-确认发件', $history_data);

            if($up === 1 || $history !== false){
                $db -> commit();
                json_return('',0,'');
            }else{
                $db -> rollback();
                json_return('',3,'操作失败');
            }


        }else{
            $count_sql = "SELECT count(*) c FROM ptc_tour_order_ext AS e ORDER BY e.orderid DESC";

            $count = $db->prepare($count_sql)->execute();
            $page = new page($count[0]['c'], 15);
            $limit = $page->limit();
            $sql = "SELECT * FROM ptc_tour_order_ext e ORDER BY e.orderid DESC LIMIT {$limit};";

            $expresstype = [
                'youshuwuliu'   => '优速物流',
                'huitongkuaidi'=>'汇通快递',
                'shunfeng'=>'顺丰',
                'zhaijisong'=>'宅急送',
                'youzhengguonei'=>'邮政快递',
                'ems'=>'EMS',
                'tiantian'=>'天天快递',
                'yuantong'=>'圆通快递',
                'yunda'=>'韵达快递',
                'zhongtong'=>'中通快递',
                'shentong'=>'申通快递',
                'other' =>'其他快递'
            ];

            $list = $db->prepare($sql)->execute();
            template::assign('list', $list);
            template::assign('expresstype',$expresstype);
            template::assign('page', $page->show());

            template::assign('subnav', 'invoice');
            template::display('finance/invoice');

        }


        break;

    //线下支付核对
    case 'check':
    default:

        if($_POST){
            $id = intval($_POST['id']);
            $paytrade = trim($_POST['paytrade']);
            if(empty($paytrade)){
                json_return('',1,'支付流水号不能为空');
            }


            $paytime = time();
            $query_sql = "SELECT orderid,deposit FROM ptc_tour_order_pay WHERE id = :id AND (status = :status  OR ISNUll(status));";
            $check = $db->prepare($query_sql)->execute([':id' => $id, ":status" => 0]);
            //未找到记录
            if(count($check) < 1){
                json_return('',2,'未找到对应状态的记录');
            }

            $orderid = $check[0]['orderid'];

            /*$order = $db->prepare("SELECT COUNT(*) c FROM ptc_tour_order  WHERE id = :id AND status = 6")->execute([':id'=> $orderid]);
            if($order[0]['c']<>1){
                json_return('',2,'行程单状态为非【等待支付】');
            }*/

            $db -> beginTrans();
            $operator = $_SESSION['uid'];
            $update_pay = $db->prepare("UPDATE ptc_tour_order_pay SET status = 1,paytrade = :paytrade, paytime = :paytime, operator = :operator WHERE id = :id")
                ->execute([':paytrade'=>$paytrade,':id'=> $id, ':paytime'=>$paytime, ':operator'=> $operator]);
            if($update_pay == false){
                $db -> rollback();
                json_return('',2,'更新失败');
            }

            $order_check = true;
            $orderpays = $db->prepare('SELECT status FROM  `ptc_tour_order_pay` WHERE orderid = :orderid and deposit<>-1')->execute([':orderid'=> $orderid]);
            foreach ($orderpays as $key => $value) {
                if($value['status'] <> 1){
                    $order_check = false;
                    break;
                }
            }




            if($order_check){
                $update_order = $db->prepare("UPDATE ptc_tour_order SET status = 7 WHERE id = :id ")->execute([":id" => $orderid]);
                if($update_order === false){
                    $db -> rollback();
                    json_return('',2,'更新失败');
                }


                    $card_status = $db->prepare('UPDATE ptc_tour_card SET status = 7 WHERE id = (SELECT card FROM ptc_tour_order WHERE id = :id)')->execute([':id'=>$orderid]);
                    if($card_status === false){
                        $db -> rollback();
                        json_return('',3,'更新失败');
                    }




            }


            $db->commit();

            $show_time = date("Y-m-d H:i:s", $paytime);

            json_return($show_time,0,'');



        }else {


            $status = trim($_GET['status']);

            $status = $status == '' ? -99 : $status;

            $conditions = '';
            if($status == 0){
                $conditions = "AND( ISNULL(p.status) OR p.status =" . $status .")";
            }
            if($status ==1){
                $conditions = "AND  p.status =" . $status ;
            }


            $count_sql = "SELECT count(*) c FROM ptc_tour_order_pay AS p  WHERE 1  " . $conditions . "  ORDER BY p.id DESC";

            $count = $db->prepare($count_sql)->execute();
            $page = new page($count[0]['c'], 15);
            $limit = $page->limit();
            $sql = "SELECT p.id,p.orderid, p.`order`, p.name,p.deposit, p.price, p.paytype, p.status, p.paytype, p.paytime, p.price, o.contact, o.title, o.tel FROM ptc_tour_order_pay AS p "
                . " LEFT JOIN ptc_tour_order as o on o.id = p.orderid WHERE 1  " . $conditions . "  ORDER BY p.id DESC LIMIT {$limit};";

            $list = $db->prepare($sql)->execute();



            template::assign('status', $status);
            template::assign('list', $list);
            template::assign('page', $page->show());
            template::assign('subnav', 'check');
            template::display('finance/check');
        }
        break;
}



