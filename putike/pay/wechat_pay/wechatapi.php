<?php
require_once("../class/payCreate.class.php");

//获取数据
$payCreate = new payCreate();
$payCreate-> setConfig();
$config = $payCreate-> setWxpay();

define( APPID, $config['appid'] );  //appid
define( APPKEY, $config['appkey'] ); //paysign key
define( SIGNTYPE, $config['signtype'] ); //method
define( PARTNERKEY, $config['partnerkey'] );//通加密串
define( APPSERCERT, $config['appsercert'] );

$data   = $payCreate-> index();

include_once("WxPayHelper.php");
$wxPayHelper = new WxPayHelper();

$wxPayHelper->setParameter("bank_type", "WX");
$wxPayHelper->setParameter("body", str_replace( ' ', '', $data-> name ));
$wxPayHelper->setParameter("partner", $config['partner'] );
$wxPayHelper->setParameter("out_trade_no", $data-> order );
$wxPayHelper->setParameter("total_fee", $data-> total * 100 );
//$wxPayHelper->setParameter("total_fee", 1);
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", $config['asy_url'] );
$wxPayHelper->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
$wxPayHelper->setParameter("input_charset", "UTF-8");

//var_dump($payCreate-> geturl( $data-> order ));
//var_dump($config['asy_url']);
//exit;
?>
<html>
<script language="javascript">
document.addEventListener(
	'WeixinJSBridgeReady', function onBridgeReady() {
		WeixinJSBridge.invoke('getBrandWCPayRequest',<?php echo $wxPayHelper->create_biz_package(); ?>,function(res){
		WeixinJSBridge.log(res.err_msg);
		//==alert(res.err_code+res.err_desc+res.err_msg);
		if (res.err_msg=='get_brand_wcpay_request:ok'){location.href ='<?php echo $payCreate-> geturl( $data-> order, $data->channel ); ?>'}else{history.go(-2);}
		});
	}
);
</script>
<body>

</body>
</html>

