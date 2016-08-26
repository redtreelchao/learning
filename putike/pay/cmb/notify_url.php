<?php
require_once("../common/common.php");
$signature=$_SERVER['QUERY_STRING'];
$common = new common();
$common -> paylog ( 4,'syn' );
/*//以下测试部分
file_put_contents('logs/cmb_notify_log'.time().'.log',json_encode(['get'=> $_GET,'post'=>$_POST]));
$log='logs/'.date('Ymd').'.log';
file_put_contents($log, 'time:'.date('Y-m-d H:i:s')."\ndata:".json_encode($_GET)."\nQUERY_STRING:".$_SERVER['QUERY_STRING']."\n", FILE_APPEND);
//以上测试部分*/
//$signature="Succeed=Y&CoNo=000004&BillNo=8104700022&Amount=60&Date=20071213&MerchantPara=8120080420080414701013700022&Msg=00270000042007121307321387100000002470&Signature=177|48|67|121|22|40|125|29|39|162|103|204|103|156|74|196|63|148|45|142|206|139|243|120|224|193|84|46|216|23|42|29|25|64|232|213|114|3|22|51|131|76|169|143|183|229|87|164|138|77|185|198|116|254|224|68|26|169|194|160|94|35|111|150|";
$command="/usr/java/bin/java test \"".$signature."\"";
exec("cd /html/web/pay/cmb/");
$s=shell_exec($command);
if($s)
{
    $paystat =$_GET['Succeed'];
    if($paystat=='Y')
    {
        $order   = (string)$_GET['MerchantPara'];
        $trade_no= (string)$_GET['Msg'];
        
        if($common->orderdeal($order,$trade_no,4))
            echo "success";
    }
    else
    {
        exit('交易失败');
    }
}
else
{
	exit('签名验证失败');
}
?>