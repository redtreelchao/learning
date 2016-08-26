<?php


/* *
 * 功能：连连支付服务器异步通知页面
 * 版本：2.0
 * 日期：2014-10-16
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 */
require_once ("llpay.config.php");
require_once ("lib/llpay_notify.class.php");
require_once("../class/payAsyn.class.php");

$payAsyn = new payAsyn();
$payAsyn-> setConfig( 'on' );
$config  = $payAsyn-> setLlpay(); //设置连连支付参数

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201408071000001543
$llpay_config['oid_partner'] = $config['partner'];

//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = $config['key'];;

//计算得出通知验证结果
$llpayNotify = new LLpayNotify($llpay_config);
$llpayNotify-> verifyNotify();
if ($llpayNotify->result) { //验证成功
	//获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
	$no_order    = $llpayNotify-> notifyResp['no_order'];//商户订单号
	$oid_paybill = $llpayNotify-> notifyResp['oid_paybill'];//连连支付单号
	$result_pay  = $llpayNotify-> notifyResp['result_pay'];//支付结果，SUCCESS：为支付成功
	$money_order = $llpayNotify-> notifyResp['money_order'];// 支付金额
	if( $result_pay == "SUCCESS" ){
		//请在这里加上商户的业务逻辑程序代(更新订单状态、入账业务)
		//——请根据您的业务逻辑来编写程序——
		//payAfter($llpayNotify->notifyResp);
	    
	    $payAsyn-> index( $no_order, 5, $oid_paybill );
	    die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
	}
	else
	{
		S::error( '202', '连连支付-异步', false );
		die("{'ret_code':'9999','ret_msg':'验签失败'}");
	}
	//file_put_contents("log.txt", "异步通知 验证成功\n", FILE_APPEND);
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
	//file_put_contents("log.txt", "异步通知 验证失败\n", FILE_APPEND);
    S::error( '203', '连连支付-异步', false );
	//验证失败
	die("{'ret_code':'9999','ret_msg':'验签失败'}");
	
	//调试用，写文本函数记录程序运行情况是否正常
	//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>