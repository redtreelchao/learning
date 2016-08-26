<?php

error_reporting(0);

$config = ['Mchid'=>'1360813502','Key'=>'bhXxcPpfe3b5SYI05KcWZ0d0LGRHoprG','appid'=>'wx72ba41b63043c80a','appsercert'=>'d6e1cbaa4f4f8f64aa333823392f95c1','asy_url'=>'http://'.$_SERVER['HTTP_HOST'].'/weixin/notify_url.php'];

require_once 'lib/Open.php';

function loadClass($className) {

   if ( !class_exists($className,false) )
   {
   		if ( false !== ($lastNsPos = strripos($className, 'Weixin\\') ) ) {

   			$filename = __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.substr($className, 7).'.php';

			if ( file_exists($filename) ) {
				
				require $filename;

			} else {

				throw new Exception($className."类文件不存在");
			}
   		}

   	    
   }
}

spl_autoload_register('loadClass');

try {
    
    $openSer = new Weixin\Open($config);

    $openid = $openSer->getopenid();

    require_once("../class/payCreate.class.php");

	//获取数据
	$payCreate = new payCreate();

	$payCreate-> setConfig();

	$data = $payCreate->index();

    $payinfo = $openSer->pay($openid, $data );
    
} catch ( Exception $e ) {
	// @TODO 异常处理
	echo 'exception：'.$e->getMessage();
    exit(0);
}

?>
<html>
<head>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script language="javascript">
function jsApiCall()
{
	WeixinJSBridge.invoke(
		'getBrandWCPayRequest',
		<?php echo json_encode($payinfo); ?>,
		function(res){
			WeixinJSBridge.log(res.err_msg);
			if (res.err_msg=='get_brand_wcpay_request:ok'){location.href ='<?php echo $payCreate-> geturl( $data-> order, $data->channel ); ?>'}else{history.go(-2);}
		}
	);
}
window.onload = function(){
	if (typeof WeixinJSBridge == "undefined"){
	    if( document.addEventListener ){
	        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
	    }else if (document.attachEvent){
	        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
	        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
	    }
	}else{
	    jsApiCall();
	}
}
</script>
</head>
<body>
</body>
</html>

