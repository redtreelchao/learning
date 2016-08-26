<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<?php
error_reporting(E_ALL & ~ E_NOTICE);
	//引入API文件
require_once("../class/payAsyn.class.php");

$payAsyn = new payAsyn();
$payAsyn-> setConfig( 'on' );
$config  = $payAsyn-> setUmppay();

/******************************设置支付参数*************************************/
//商户的私钥路径
define("privatekey",$config['privatekey']);
//UMPAY的平台证书路径
define("platcert",$config['platcert']);
//日志生成目录
define("logpath","./umpLog/");
//记录日志文件的同时是否在页面输出:要输出为true,否则为false
define("log_echo",false);
//UMPAY平台地址,根据实际情况修改
define("plat_url","http://pay.soopay.net");
//支付产品名称:标准支付spay
define("plat_pay_product_name","spay");

require_once ('api/plat2Mer.php');
require_once ('api/mer2Plat.php');

$data = new HashMap ();
$data->put ( "service", $_REQUEST ['service'] );
$data->put ( "charset", $_REQUEST ['charset'] );
$data->put ( "mer_id", $_REQUEST ['mer_id'] );
$data->put ( "sign_type", $_REQUEST ['sign_type'] );
$data->put ( "order_id", $_REQUEST ['order_id'] );
$data->put ( "mer_date", $_REQUEST ['mer_date'] );
$data->put ( "trade_no", $_REQUEST ['trade_no'] );
if($_REQUEST ['goods_id']!=null)
$data->put ( "goods_id", $_REQUEST ['goods_id'] );
$data->put ( "pay_date", $_REQUEST ['pay_date'] );
$data->put ( "amount", $_REQUEST ['amount'] );
$data->put ( "amt_type", $_REQUEST ['amt_type'] );
$data->put ( "pay_type", $_REQUEST ['pay_type'] );
if($_REQUEST ['media_id']!=null)
$data->put ( "media_id", $_REQUEST ['media_id'] );
if($_REQUEST ['media_type']!=null)
$data->put ( "media_type", $_REQUEST ['media_type'] );
$data->put ( "settle_date", $_REQUEST ['settle_date'] );
if($_REQUEST ['mer_priv']!=null)
$data->put ( "mer_priv", $_REQUEST ['mer_priv'] );
$data->put ( "trade_state", $_REQUEST ['trade_state'] );
$data->put ( "pay_seq", $_REQUEST ['pay_seq'] );
if($_REQUEST ['error_code']!=null)
$data->put ( "error_code", $_REQUEST ['error_code'] );
$data->put ( "version", $_REQUEST ['version'] );
$data->put ( "sign", $_REQUEST ['sign'] );

//获取UMPAY平台请求商户的支付结果通知数据,并对请求数据进行验签
$reqData = PlatToMer::getNotifyRequestData ( $data );
//获取请求数据
$service = $reqData->get ( "service" );
$charset = $reqData->get ( "charset" );
$mer_id = $reqData->get ( "mer_id" );
$sign_type = $reqData->get ( "sign_type" );
$order_id = $reqData->get ( "order_id" );
$mer_date = $reqData->get ( "mer_date" );
$trade_no = $reqData->get ( "trade_no" );
$goods_id = $reqData->get ( "goods_id" );
$pay_date = $reqData->get ( "pay_date" );
$amount = $reqData->get ( "amount" );
$amt_type = $reqData->get ( "amt_type" );
$pay_type = $reqData->get ( "pay_type" );
$media_id = $reqData->get ( "media_id" );
$media_type = $reqData->get ( "media_type" );
$settle_date = $reqData->get ( "settle_date" );
$mer_priv = $reqData->get ( "mer_priv" );
$trade_state = $reqData->get ( "trade_state" );
$pay_seq = $reqData->get ( "pay_seq" );
$error_code = $reqData->get ( "error_code" );
$version = $reqData->get ( "version" );
$sign = $reqData->get ( "sign" );

//生成平台响应UMPAY平台数据,将该串放入META标签
$resData = new HashMap ();
$resData->put ( "mer_id", $reqData->get ( "mer_id" ) );
$resData->put ( "sign_type", $reqData->get ( "sign_type" ) );
$resData->put ( "mer_date", $reqData->get ( "mer_date" ) );
$resData->put ( "order_id", $reqData->get ( "order_id" ) );
$resData->put ( "mer_trace", $reqData->get ( "order_id" ) );
$resData->put ( "mer_check_date", $reqData->get ( "settle_date" ) );
if ("TRADE_SUCCESS" == $reqData->get ( "trade_state" )){
	$resData->put ( "ret_code", "0000" );
} else {
	$resData->put ( "ret_code", "1111" );
}
$resData->put ( "ret_msg", "测试商户结果通知响应数据" );
$resData->put ( "version", "4.0" );
$data = MerToPlat::notifyResponseData ( $resData );
?>
<!-- 下面是商户响应平台数据 -->
<META NAME="MobilePayPlatform" CONTENT="<?php echo $data;?>" />
<title>商户接收结果通知</title>
</head>
<body>
</body>
</html>
<?php
if ("TRADE_SUCCESS" == $reqData->get ( "trade_state" ))
{
	if( $payAsyn-> index( $order_id, 2, $trade_no )) echo "success";
}
else
{
    S::error( '202', 'UMP-异步', false );
}
?>