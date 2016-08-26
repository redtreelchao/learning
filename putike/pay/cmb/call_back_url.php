<?php
##
## 在jre目录下运行此命令
## nohup java -jar JavaBridge.jar SERVLET_LOCAL:8080 >/dev/null 2>&1 &
##
require_once("../class/paySyn.class.php");

$signature = $_SERVER['QUERY_STRING'];

$paySyn = new paySyn();
$paySyn-> setConfig( 'on' );

//$log='/tmp/cmb-'.date('Ymd').'.log';
//file_put_contents($log, 'time:'.date('Y-m-d H:i:s')."\ndata:".json_encode($_GET)."\nQUERY_STRING:".$_SERVER['QUERY_STRING']."\n", FILE_APPEND);
//$signature="Succeed=Y&CoNo=004756&BillNo=0929354582&Amount=0.01&Date=20160729&MerchantPara=160729004170045&Msg=00210047562016072916272957300000001260&Signature=78|71|146|243|90|182|182|24|200|131|60|204|252|45|12|175|143|212|61|171|188|41|238|151|123|52|217|29|228|41|182|249|196|46|11|20|199|216|194|121|147|11|14|45|163|80|191|101|154|145|15|28|195|206|119|109|189|45|87|138|81|16|32|182|";

require_once("lib/Java.inc");

$here=realpath(dirname($_SERVER["SCRIPT_FILENAME"]));

java_set_file_encoding("GB2312");
$Security=new java('cmb.netpayment.Security',$here.'/public.key');
$res = $Security->checkInfoFromBank($signature);//检验签名

//file_put_contents($log, 'check_signature='.var_export($res,true)."\n", FILE_APPEND);

if (java_values($res)) 
{
    $paystat = $_GET['Succeed'];
    if( $paystat == 'Y' )
    {
        $order    = (string)$_GET['MerchantPara'];
        $trade_no = (string)$_GET['Msg'];
        
        if( $paySyn-> index( $order, 4, $trade_no ))
            echo "success";
    }
    else
    {
        S::error( '202', '招行-同步', false );
        exit('交易失败');
    }
}
else
{
    S::error( '203', '招行-同步', false );
	exit('签名验证失败');
}
?>