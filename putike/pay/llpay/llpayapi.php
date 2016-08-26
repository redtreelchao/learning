<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>连连支付wap交易接口</title>
</head>
<?php


/* *
 * 功能：连连支付wap交易接口接入页
 * 版本：1.2
 * 修改日期：2014-06-13
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
require_once ("llpay.config.php");
require_once ("lib/llpay_submit.class.php");
require_once ("../class/payCreate.class.php");

$payCreate = new payCreate();
$payCreate-> setConfig();
$config = $payCreate-> setLlpay();

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201408071000001543
$llpay_config['oid_partner'] = $config['partner'];

//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = $config['key'];;

$data = $payCreate-> index();

/**************************请求参数**************************/

//商户用户唯一编号
$user_id = S::aes( $data-> user, publicFunc::AESKEY );

//支付类型
$busi_partner = '101001';

//商户订单号
$no_order = $data-> order;
//商户网站订单系统中唯一订单号，必填

//付款金额
$money_order = $data-> total;
//必填

//订单时间
$dt_order   = date( 'YmdHis',$data-> create );

//商品名称
$name_goods = $data-> name;

//订单描述
//$info_order = $_POST['info_order'];

//卡号
//$card_no = $_POST['card_no'];

//姓名
//$acct_name = $_POST['acct_name'];

//身份证号
//$id_no = $_POST['id_no'];

//协议号
//$no_agree = $_POST['no_agree'];

//风险控制参数
$json = json_decode( $data-> data, true );

if( !empty( $json['checkin'] ) && !empty( $json['checkout'] ) )
{
    $checkin  = date( 'YmdHis', strtotime( $json['checkin'] ));
    $checkout = date( 'YmdHis', strtotime( $json['checkout'] ));
}
else
{
    $checkin  = date( 'YmdHis', strtotime( 'today 00:00:00' ));
    $checkout = date( 'YmdHis', strtotime( 'today 00:00:00 +3 month' ));
}

$hotel_list = array(
    array(
    	'hotel_name'  => $data-> contact,//入住人姓名
        'hotel_phone' => $data-> tel, //入住人联系电话
        'dt_arrive'   => $checkin,//预付类 入住日期，其他产品开始时间（模拟）
        'dt_leave'    => $checkout,//预付类 离店日期，其他产品结束时间（模拟）
    )
);

$risk_item_arr = array(
	'frms_ware_category'      => '2001',//商品类目
    'user_info_mercht_userno' => S::aes( $data-> user, publicFunc::AESKEY ),//商户用户唯一标识
    'user_info_dt_register'   => date( 'YmdHis' ),//注册时间
    'hotel_list'              => $hotel_list,//酒店预订信息
);

$risk_item = addslashes( json_encode( $risk_item_arr ));
//订单有效期
//$valid_order = $_POST['valid_order'];

//服务器异步通知页面路径
$notify_url = $config['asy_url'];
//需http://格式的完整路径，不能加?id=123这类自定义参数

//页面跳转同步通知页面路径
$return_url = $config['syn_url'];
//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array (
	"oid_partner"  => trim($llpay_config['oid_partner']),
	"app_request"  => trim($llpay_config['app_request']),
	"sign_type"    => trim($llpay_config['sign_type']),
	"valid_order"  => trim($llpay_config['valid_order']),
	"user_id"      => $user_id,
	"busi_partner" => $busi_partner,
	"no_order"     => $no_order,
	"dt_order"     => $dt_order,
	"name_goods"   => $name_goods,
	//"info_order" => $info_order,
	"money_order"  => $money_order,
	"notify_url"   => $notify_url,
	"url_return"   => $return_url,
	//"card_no" => $card_no,
	//"acct_name" => $acct_name,
	//"id_no" => $id_no,
	//"no_agree" => $no_agree,
	"risk_item" => $risk_item,
	//"valid_order" => $valid_order
);

//建立请求
$llpaySubmit = new LLpaySubmit($llpay_config);
$html_text = $llpaySubmit->buildRequestForm($parameter, "post", "确认");
echo $html_text;
?>
</body>
</html>