<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>U付手机支付</title>
</head>
<body>
<?php
//error_reporting(E_ALL & ~ E_NOTICE);
require_once("../class/payCreate.class.php");

$payCreate = new payCreate();
$payCreate-> setConfig();
$config = $payCreate-> setUmppay();

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

/*******************************************************************************/

require_once ('api/mer2Plat.php');
require_once ('api/plat2Mer.php');

$data = $payCreate-> index();


$goods_id   = $data-> product;                   //商品号【goods_id】：
$ret_url    = $config['syn_url'];                //页面返回地址【ret_url】：
$notify_url = $config['asy_url'];                //页面通知地址【notify_url】：
$goods_inf  = $data-> name;                      //商品描述信息【goods_inf】：
$media_id   = $data-> tel;                       //媒介标识【media_id】手机号：
$order_id   = $data-> order;                     //商品订单号【order_id】：
$mer_date   = date ( "Ymd", $data-> create );    //商品订单日期【mer_date】：
$amount     = $data-> total * 100;               //付款金额【amount】分：

$service     = 'pay_req';                        //接口名称【service】：
$charset     = 'UTF-8';                          //字符编码【charset】：
$mer_id      = $config['mer_id'];                //商户编号【mer_id】：
$sign_type   = 'RSA';                            //签名方式【sign_type】：
$res_format  = 'HTML';                           //响应数据格式【res_format】：
$version     = '4.0';                            //版本号【version】：
$media_type  = 'MOBILE';                         //媒介类型【media_type】：
$amt_type    = 'RMB';                            //付款币种【amt_type】：
$pay_type    = 'CREDITCARD';                     //默认支付方式【pay_type】：
$expire_time = date("Ymd",time()+1200);          //订单过期时常


//默认银行【gate_id】：
//$gate_id = $_REQUEST ['gate_id'];
//商户私有域【mer_priv】：
//$mer_priv = $_REQUEST ['mer_priv'];


//业务扩展信息【expand】：
//$expand = $_REQUEST ['expand'];
//用户IP地址【user_ip】：
//$user_ip = $_REQUEST ['user_ip'];

$map = new HashMap ();
$map->put ( "service", $service );
$map->put ( "charset", $charset );
$map->put ( "mer_id", $mer_id );
$map->put ( "ret_url", $ret_url );
$map->put ( "sign_type", $sign_type );
$map->put ( "res_format", $res_format );
$map->put ( "notify_url", $notify_url );
$map->put ( "goods_id", $goods_id );
$map->put ( "goods_inf", $goods_inf );
$map->put ( "media_id", $media_id );
$map->put ( "media_type", $media_type );
$map->put ( "order_id", $order_id );
$map->put ( "mer_date", $mer_date );
$map->put ( "amount", $amount );
$map->put ( "amt_type", $amt_type );
$map->put ( "pay_type", $pay_type );
//$map->put ( "gate_id", $gate_id );
//$map->put ( "mer_priv", $mer_priv );
//$map->put ( "user_ip", $user_ip );
$map->put ( "expire_time", $expire_time );
//$map->put ( "expand", $expand );
$map->put ( "version", $version );

$reqData = MerToPlat::requestTransactionsByGet ( $map ); //这个是重要的
$sign = $reqData->getSign (); //这个是为了在本DEMO中显示签名结果。
$plain = $reqData->getPlain (); //这个是为了在本DEMO中显示签名原串
$url = $reqData->getUrl();

//请求平台取得平台响应结果
$html = file_get_contents($url);
//解析平台响应数据
$resData = PlatToMer:: getResDataByHtml ($html);
$retCode = $resData->H_table['ret_code'];
//var_dump($resData->H_table);
//判断退费结果,retCode=0000为成功,其他为失败,
if($retCode=="0000"){
		echo '<script type="text/javascript">location.href="https://m.soopay.net/m/html5/index.do?tradeNo='.$resData->H_table['trade_no'].'"</script>';
}else{
		echo "\n交易失败,响应码:" . $retCode;
}
?>
</body>
</html>