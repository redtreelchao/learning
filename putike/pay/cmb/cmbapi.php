<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>招行付手机支付</title>
</head>
<body>
<?php 
require_once("../class/payCreate.class.php");

$payCreate = new payCreate();
$payCreate-> setConfig();
$config = $payCreate-> setCmbpay();
$data = $payCreate-> index();

$host = 'https://netpay.cmbchina.com/netpayment/BaseHttp.dll?';

$param=array();

$param['MfcISAPICommand']= 'PrePayWAP';
$param['BranchID']       = $config['branchid'];
$param['CoNo']           = $config['cono'];
$param['BillNo']         = date("His").rand(1000,9999);
$param['Amount']         = (int)$data-> total;
$param['Date']           = date("Ymd");
$param['ExpireTimeSpan'] = 20;
$param['MerchantUrl']    = $config['syn_url'];
$param['MerchantPara']   = $data-> order;

$param = http_build_query( $param );

$command = '/usr/java/bin/java sign '.urlencode($param);

exec("cd /html/pay/cmb/");

$MerchantCode = shell_exec($command);

/*$sLen = strlen($s);

if( $sLen < 2 || $sLen > 100 )
{
	exit($s);
}*/

$url = $host.$param.'&MerchantCode='.trim($MerchantCode);

header("location:{$url}");
?>
</body>
</html>