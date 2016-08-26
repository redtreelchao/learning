<?php
include_once("WxPayHelper.php");
require_once("../common/common.php");

//获取数据
exit;
$common = new common();
if(!empty($_GET['coupon']))
    $data = $common->orderdata($_GET['id'],$_GET['coupon']);
else
    $data = $common->orderdata($_GET['id']);

$wxPayHelper = new WxPayHelper();

$wxPayHelper->setParameter("bank_type", "WX");
$wxPayHelper->setParameter("body", str_replace(' ','',$data['name']));
$wxPayHelper->setParameter("partner", "1220479701");
$wxPayHelper->setParameter("out_trade_no", $data['order']);
//$wxPayHelper->setParameter("total_fee", $data['total']*100);
$wxPayHelper->setParameter("total_fee", 1);
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", 'http://www.putike.cn/wechat_pay/test_url.php');
$wxPayHelper->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
$wxPayHelper->setParameter("input_charset", "UTF-8");

//var_dump($wxPayHelper->create_biz_package());
exit;
?>
<html>
<script language="javascript">
document.addEventListener(
	'WeixinJSBridgeReady', function onBridgeReady() {
		WeixinJSBridge.invoke('getBrandWCPayRequest',<?php echo $wxPayHelper->create_biz_package(); ?>,function(res){
		WeixinJSBridge.log(res.err_msg);
		//==alert(res.err_code+res.err_desc+res.err_msg);
		if (res.err_msg=='get_brand_wcpay_request:ok'){location.href ='../index.php/order/orderr/orderList'}else{history.go(-2);}
		});
	}
);
</script>
<body>

</body>
</html>

