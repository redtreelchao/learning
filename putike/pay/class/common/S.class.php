<?php
/**
 * 静态类
 * @author Jacky
 *
 */
class S
{
    /**
     * 获取GET
     * @param unknown $label
     * @return Ambigous <NULL, unknown>
     */
    static function get( $label )
    {
        return !empty( $_GET[$label] ) ? $_GET[$label] : null;
    }
    /**
     * 获取POST
     * @param unknown $label
     * @return Ambigous <NULL, unknown>
     */
    static function post( $label )
    {
        return !empty( $_POST[$label] ) ? $_POST[$label] : null;
    }
    /**
     * 获取POST
     * @param unknown $label
     * @return Ambigous <NULL, unknown>
     */
    static function postget( $label )
    {
        return !empty( $_REQUEST[$label] ) ? $_REQUEST[$label] : null;
    }
    /**
     * jsonp输出
     * @param unknown $data
     * @param number $errcode
     * @param string $err
     */
    static function json_return( $data, $errcode=0, $err='' )
    {
        $tag = S::get('callback');
        
        ob_clean();
        
        if( !empty( $tag ))
        {
            header('Content-Type: application/json; charset=utf-8');
            exit( $tag . '(' . json_encode( array( 'code'=>(int)$errcode, 'data'=>$data, 'msg'=>$err ), JSON_UNESCAPED_UNICODE ) . ')' );
        }
        else
        {
            header('Content-Type: application/json; charset=utf-8');
            exit( json_encode( array( 'code'=>(int)$errcode, 'data'=>$data, 'msg'=>$err ), JSON_UNESCAPED_UNICODE ));
        }
    }
    /**
     * 报错
     * @param unknown $code
     */
    static function error( $code, $func, $show=true )
    {
        $errorMsg = new errorMsg();
        $return   = $errorMsg -> error( $code );
        
        $db  = new PDO('mysql:host='.publicFunc::DBLOCATION.';dbname='.publicFunc::DBNAME, publicFunc::DBUSER, publicFunc::DBPASS);
        $db-> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db-> exec('set names utf8');
        
        $get   = $_SERVER['QUERY_STRING'];
        $post  = file_get_contents("php://input");
        
        $sql   = 'INSERT INTO `log_pay` (`msg`,`code`,`func`,`post`,`get`,`time`,`date`) VALUES (:msg,:code,:func,:post,:get,:time,:date)';
        $param = array
        (
            ':msg' => $return[2],
            ':code'=> $return[1],
            ':func'=> $func,
            ':post'=> $post,
            ':get' => $get,
            ':time'=> time(),
            ':date'=> date('Y-m-d H:i:s'),
        );
        
        $db-> prepare( $sql )-> execute( $param );
        if( $show ) S::json_return( $return[0], $return[1], $return[2] );
    }

    /**
     * 写错误日志
     * @param unknown $txt
     */
    static function logs( $type=1, $txt ){
        switch ($type){
        	case '1':
        	    $log="./log/error_".date('Ymd').".log";
        	    file_put_contents($log, $txt."\n", FILE_APPEND);
        	    break;
        	case '2':
        	    $log="./log/record_".date('Ymd').".log";
        	    file_put_contents($log, $txt."\n", FILE_APPEND);
        	    break;
        }
    
    }
    /**
     * curl获取
     * @param unknown $url
     * @param unknown $postStr
     * @return unknown
     */
    static function curl( $url, $postStr )
    {
        $curlPost = $postStr;
        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }

    /* aes 256 加密与解密
     * @param String $ostr
    * @param String $securekey
    * @param String $type encrypt, decrypt
    */
    static function aes( $ostr, $securekey, $type = 'encrypt' )
    {
        if( $ostr == '' )
        {
            return '';
        }
    
        $key = $securekey;
        $iv  = strrev($securekey);
        $td  = mcrypt_module_open( 'rijndael-256', '', 'ofb', '' );
        mcrypt_generic_init( $td, $key, $iv );
    
        $str = '';
    
        switch($type)
        {
        	case 'encrypt':
        	    $str = base64_encode( mcrypt_generic( $td, $ostr ));
        	    break;
    
        	case 'decrypt':
        	    $str = mdecrypt_generic( $td, base64_decode( $ostr ));
        	    break;
        }
    
        mcrypt_generic_deinit( $td );
    
        return $str;
    }
    static function dump( $data )
    {
    	var_dump( $data );
    	exit;
    }
    /**
     * uid加密
     * @param unknown $uid
     * @return unknown
     */
    static function uidEncrypt( $uid )
    {
        $id = S::aes( $uid, publicFunc::AESKEY );
        $id = S::createKey( $id );
        return $id;
    }
    static function createKey( $str, $type=true )
    {
    	$a = array('+','=');
    	$b = array('{5c}','{5d}');
    	if( $type )
    	{
    	   return str_replace($a, $b, $str);
    	}
    	else
    	{
    	   return str_replace($b, $a, $str);
    	}
    }
    /**
     * uid解密
     * @param unknown $uid
     * @return string
     */
    static function uidDeal( $uid ){
        $id = S::createKey( $uid, false );
        $id = S::aes( $id, publicFunc::AESKEY, 'decrypt' );
        if( !preg_match( "/\d{1,10}/", $id )) S::error( '402' );
        return $id;
    }
}
?>