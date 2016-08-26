<?php
require_once("../class/payAsyn.class.php");
//S::error( '201', '测试', false );

//获取数据
$payAsyn = new payAsyn();
$payAsyn-> setConfig( 'on' );
$config  = $payAsyn-> setWxpay();

define( APPID, $config['appid'] );  //appid
define( APPKEY, $config['appkey'] ); //paysign key
define( SIGNTYPE, $config['signtype'] ); //method
define( PARTNERKEY, $config['partnerkey'] );//通加密串
define( APPSERCERT, $config['appsercert'] );

include_once("WxPayHelper.php");
include_once("CommonUtil.php");

$get_data  = $_GET;
$post_data = $GLOBALS["HTTP_RAW_POST_DATA"];

$post_data = simplexml_load_string($post_data, 'SimpleXMLElement', LIBXML_NOCDATA);
$post_data = (array)$post_data;

logs( $post_data, $get_data );

$sign_type		= $get_data['sign_type'];		//签名方式
$input_charset	= $get_data['input_charset'];	//字符集
$sign			= $get_data['sign'];			//签名
$trade_mode		= $get_data['trade_mode'];		//交易模式
$trade_state	= $get_data['trade_state'];		//交易状态
$partner		= $get_data['partner'];			//商户号
$bank_type		= $get_data['bank_type'];		//付款银行
$bank_billno	= $get_data['bank_billno'];		//银行订单号
$total_fee		= $get_data['total_fee'];		//总金额
$fee_type		= $get_data['fee_type'];		//币种 1人民币
$notify_id		= $get_data['notify_id'];		//通知ID
$transaction_id = $get_data['transaction_id'];	//订单号(微信)
$out_trade_no	= $get_data['out_trade_no'];	//商户订单号
$attach			= $get_data['attach'];			//商户数据包
$time_end		= $get_data['time_end'];		//支付完成时间
$transport_fee	= $get_data['transport_fee'];	//物流费用
$product_fee	= $get_data['product_fee'];		//物品费用
$discount		= $get_data['discount'];		//折扣价格

$wxPayHelper = new WxPayHelper();

$wxPayHelper->setParameter("sign_type", $sign_type);
$wxPayHelper->setParameter("input_charset", $input_charset);
$wxPayHelper->setParameter("sign", $sign);
$wxPayHelper->setParameter("trade_mode", $trade_mode);
$wxPayHelper->setParameter("trade_state", $trade_state);
$wxPayHelper->setParameter("partner", $partner);
$wxPayHelper->setParameter("bank_type", $bank_type);
$wxPayHelper->setParameter("bank_billno", $bank_billno);
$wxPayHelper->setParameter("total_fee", $total_fee);
$wxPayHelper->setParameter("fee_type", $fee_type);
$wxPayHelper->setParameter("notify_id", $notify_id);
$wxPayHelper->setParameter("transaction_id", $transaction_id);
$wxPayHelper->setParameter("out_trade_no", $out_trade_no);
$wxPayHelper->setParameter("attach", $attach);
$wxPayHelper->setParameter("time_end", $time_end);
$wxPayHelper->setParameter("transport_fee", $transport_fee);
$wxPayHelper->setParameter("product_fee", $product_fee);
$wxPayHelper->setParameter("discount", $discount);

$nativeObj["appid"] = APPID;
$nativeObj["appkey"] = APPKEY;
$nativeObj["timestamp"] = $post_data['TimeStamp'];
$nativeObj["noncestr"] = $post_data['NonceStr'];
$nativeObj["openid"] = $post_data['OpenId'];
$nativeObj["issubscribe"] = $post_data['IsSubscribe'];

//验证AppSignature
if($post_data['AppSignature'] == $wxPayHelper->get_biz_sign($nativeObj) && $post_data['AppSignature'] != null && $post_data['AppSignature'] != ''){
	if (null == PARTNERKEY || "" == PARTNERKEY ) {
		echo "密钥不能为空！" . "<br>";
		exit;
	}
	//验证sign
	$commonUtil = new CommonUtil();
	ksort($wxPayHelper->parameters);
	$unSignParaString = $commonUtil->formatQueryParaMap($wxPayHelper->parameters, false);
	$md5SignUtil = new MD5SignUtil();
	if($sign == $md5SignUtil->sign($unSignParaString,$commonUtil->trimString(PARTNERKEY)) && $sign != null && $sign != '' ){
	    
		//$common->orderdeal_byasyn($out_trade_no,$transaction_id,3);
		$payAsyn-> index( $out_trade_no, 3, $transaction_id );
		
		$access_token = get_access_token();
		$deliver_timestamp = time();
		$SignData['appid'] = APPID;
		$SignData['appkey'] = APPKEY;
		$SignData['openid'] = $post_data['OpenId'];
		$SignData['transid'] = $transaction_id;
		$SignData['out_trade_no'] = $out_trade_no;
		$SignData['deliver_timestamp'] = $deliver_timestamp;
		$SignData['deliver_status'] = "1";
		$SignData['deliver_msg'] = "ok";
		$app_signature = $wxPayHelper->get_biz_sign($SignData);

		$PostData['appid'] = APPID;
		$PostData['openid'] = $post_data['OpenId'];
		$PostData['transid'] = $transaction_id;
		$PostData['out_trade_no'] = $out_trade_no;
		$PostData['deliver_timestamp'] = $deliver_timestamp;
		$PostData['deliver_status'] = "1";
		$PostData['deliver_msg'] = "ok";
		$PostData['app_signature'] = $app_signature;
		$PostData['sign_method'] = "sha1";
		$PostData = json_encode($PostData);
		$return = curl($PostData,$access_token);
		echo "success";
	}else{
		echo "签名验证失败" . "<br>";
		S::error( '202', '微信支付-异步', false );
		WriteLog("sign签名验证失败",$post_data,$get_data);
		exit;
	}
}else{
	echo "签名验证失败" . "<br>";
	S::error( '203', '微信支付-异步', false );
	WriteLog("AppSignature签名验证失败",$post_data,$get_data);
	exit;
}

/*
	获取access_token
*/
function get_access_token(){
	$path = "../common/access_token.txt";
	$handle = fopen($path,'r');
	$filesize = filesize($path);
	$json = fread($handle,$filesize);
	fclose($handle);
	$access = json_decode($json,true);
	$access_token = $access['access_token'];
	$access_time = $access['access_time'];

	
	if($access_token == '' || $access_token == null || $access_time == '' || $access_time == null || $access_time < time()){
		$now = time();
		$url = sprintf("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",APPID, APPSERCERT);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//输出内容为字符串
		$json_str = curl_exec($ch);
		$data = json_decode($json_str);
		
		$txt['access_token'] = $data->access_token;
		$txt['access_time'] = $now + $data->expires_in*1-200;
		$txt = json_encode($txt);
		$handle = fopen($path, "w");
		fwrite($handle, $txt);     //把刚才替换的内容写入生成的html文件
		fclose($handle);
		return $access_token;
	}else{
		return $access_token;
	}
}
//传送信息
function curl($postStr,$access_token){
	$url = sprintf("https://api.weixin.qq.com/pay/delivernotify?access_token=%s",$access_token);
	$curlPost = $postStr;
	$ch = curl_init();//初始化curl
	curl_setopt($ch,CURLOPT_URL,$url);//抓取指定网页
	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	$data = curl_exec($ch);//运行curl
	curl_close($ch);
	return $data;
}
/**
 * 写日志
 * @param unknown $txt
 */
function logs( $post_data, $get_data )
{
    $log="./log/buy_".date('Ymd').".log";
    $txt = "date:".date("Y-m-d H:i:s").",\n post_data:".json_encode($post_data).",\n get_data:".json_encode($get_data)."\n\n";
    file_put_contents($log, $txt."\n", FILE_APPEND);
}

?>