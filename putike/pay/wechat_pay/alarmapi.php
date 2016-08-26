<?php
	require_once("../common/common.php");
	include_once("WxPayHelper.php");
	include_once("CommonUtil.php");

	$post_data = $GLOBALS["HTTP_RAW_POST_DATA"];
	$post_data = simplexml_load_string($post_data, 'SimpleXMLElement', LIBXML_NOCDATA);
	$post_data = (array)$post_data;
	
	$nativeObj["alarmcontent"] = $post_data['AlarmContent'];
	$nativeObj["appid"] = APPID;
	$nativeObj["appkey"] = APPKEY;
	$nativeObj["description"] = $post_data['Description'];
	$nativeObj["errortype"] = $post_data['ErrorType'];
	$nativeObj["timestamp"] = $post_data['TimeStamp'];

	//验证AppSignature
	if($post_data['AppSignature'] == $wxPayHelper->get_biz_sign($nativeObj) && $post_data['AppSignature'] != null && $post_data['AppSignature'] != ''){
		$msg = "ErrorType:".$post_data['ErrorType'].", Description:".$post_data['Description'].", AlarmContent:".$post_data['AlarmContent'];
		WriteLog($msg,$post_data);
		echo "success";
	}else{
		WriteLog("签名失败",$post_data);
		echo "签名验证失败" . "<br>";
		exit;
	}
	function WriteLog($msg,$post_data){
		$path = "./log/".date("Ymd").".txt";
		$handle = fopen($path, "a");
		$data = "date:".date("Y-m-d H:i:s").", msg:".$msg.", post_data:".$post_data."\n";
		fwrite($handle, $data);
		fclose($handle);
	}
?>