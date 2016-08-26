<?php

require_once("../class/payAsyn.class.php");

//获取数据
$payAsyn = new payAsyn();
$payAsyn-> setConfig( 'on' );
$config  = $payAsyn-> setWxpay();
$config = array_merge($config, ['Mchid'=>'1360813502','Key'=>'bhXxcPpfe3b5SYI05KcWZ0d0LGRHoprG','appid'=>'wx72ba41b63043c80a','appsercert'=>'d6e1cbaa4f4f8f64aa333823392f95c1']);

require_once 'lib/Pay.php';

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
            
    $postStr = file_get_contents('php://input');
    
    if ( empty($postStr) ) {
        
        throw new Exception('Params Not Allow Empty');
        
    }
    
    logs( $postStr );
    
    $wxPay = new Weixin\Pay();
    
    $wxPay->setAppId( $config['appid'] );
    
    $wxPay->setAppSecret( $config['appsercert'] );
    
    $wxPay->setMchid( $config['Mchid'] );
    
    $wxPay->setKey( $config['Key'] );
    
    $data = $wxPay->getNotifyData($postStr);
    
    $payAsyn->index( $data['out_trade_no'], 3, $data['transaction_id'] );
    
    echo "<xml><return_code>SUCCESS</return_code><return_msg></return_msg></xml>";
    
} catch ( Exception $e ) {
    
    echo "<xml><return_code>FAIL</return_code><return_msg>{$e->getMessage()}</return_msg></xml>";
    
    logs( $e->getMessage() );
}

/**
 * 写日志
 * @param unknown $txt
 */
function logs( $post_data )
{
    $log="./log/buy_".date('Ymd').".log";
    $txt = "date:".date("Y-m-d H:i:s").",\n post_data:".$post_data."\n\n";
    file_put_contents($log, $txt."\n", FILE_APPEND);
}