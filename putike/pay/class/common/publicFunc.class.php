<?php
require_once("../class/common/S.class.php");
require_once("../class/common/errorMsg.class.php");
require_once("../class/common/api.class.php");
/**
 * 公共类
 * @author Jacky
 *
 */
class publicFunc
{
    public $db, $oct_db;
    public $channel;
    public $appid;
    public $secret;

    const AESKEY      = "OjEA+wrv3cDZ8s1[OodLqLss+Zsb2@~1";

    const DBLOCATION  = 'putike2015.mysql.rds.aliyuncs.com';
    const DBNAME      = 'fnp';
    const DBUSER      = 'oa_system';
    const DBPASS      = '5u5Oz4pBot';
    const PUTIKEURL   = 'http://www.putike.cn/pay/success';
    const FEEKRURL    = 'http://m.feekr.com/pay/success';


	const DBOCTLOCATION  = 'rdsjxuu7wsflhstjhie5opublic.mysql.rds.aliyuncs.com';
	const DBOCTNAME      = 'putike';
	const DBOCTUSER      = 'putike';
	const DBOCTPASS      = 'uGR5oSy2';

    public function __construct()
    {
        date_default_timezone_set('Asia/Shanghai');
        $this->linkdb();
		/**
		 *  以下代码上线时开启 并修改对应的数据库连接
 		 *
		 */
		$this->octdb();
    }
    private function linkdb()//连接数据库
	{
	    try{
	        $this-> db = new PDO('mysql:host='.publicFunc::DBLOCATION.';dbname='.publicFunc::DBNAME, publicFunc::DBUSER, publicFunc::DBPASS);
	        //开启异常处理
	        $this-> db-> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		    $this-> db-> exec('set names utf8');
	    }catch(PDOException $e){
	        echo "数据库连接失败：".$e->getMessage();
	        exit;
	    }
	}

	private function octdb()//连接数据库
	{
		try{
			$this-> oct_db = new PDO('mysql:host='.publicFunc::DBOCTLOCATION.';dbname='.publicFunc::DBOCTNAME, publicFunc::DBOCTUSER, publicFunc::DBOCTPASS);
			//开启异常处理
			$this-> oct_db-> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this-> oct_db-> exec('set names utf8');
		}catch(PDOException $e){
			echo "数据库连接失败：".$e->getMessage();
			exit;
		}
	}

	public function setConfig( $api='off' )
	{
		$this-> getChannel( $api );
	}
	/**
	 * 配置支付宝参数
	 * @return multitype:string
	 */
	public function setAlipay()
	{
	    if( empty( $this-> channel )) S::error( '401', 'publicFunc-setAlipay' );

		switch ( $this-> channel )
		{
			case 1:
                return array(
	               'asy_url'            => 'http://pay.feekr.com/alipay/notify_url.php',    //异步通知地址
	               'syn_url'            => 'http://pay.feekr.com/alipay/call_back_url.php', //同步通知地址
	               'seller_email'       => 'service@feekr.com',                             //账号名
	               'partner'            => '2088411825083435',
	               'key'                => 'tvvojhgzdk4amywx378tczzxx55wvbby',
	               'private_key_path'   => 'key/feekr/rsa_private_key.pem',
	               'ali_public_key_path'=> 'key/feekr/alipay_public_key.pem',
                );
			    break;
			case 2:
			    return array(
			       'asy_url'            => 'http://pay.putike.cn/alipay/notify_url.php',
			       'syn_url'            => 'http://pay.putike.cn/alipay/call_back_url.php',
			       'seller_email'       => '1751175610@qq.com',
			       'partner'            => '2088801745078615',
			       'key'                => 'ojlej34koeojn2qiuqfqhbk4xqrv6y0j',
			       'private_key_path'   => 'key/putike/rsa_private_key.pem',
			       'ali_public_key_path'=> 'key/putike/alipay_public_key.pem',
                );
			    break;
		}
	}
	/**
	 * 配置连连支付
	 * @return multitype:string
	 */
	public function setLlpay()
	{
	    if( empty( $this-> channel )) S::error( '401', 'publicFunc-setLlpay' );

	    switch ( $this-> channel )
	    {
	    	case 1:
	    	    return array(
    	    	    'asy_url'=> 'http://pay.feekr.com/llpay/notify_url.php',    //异步通知地址
    	    	    'syn_url'=> 'http://pay.feekr.com/llpay/return_url.php',    //同步通知地址
    	    	    'partner'=> '201504171000289503',
    	    	    'key'    => 'sKteoOGFR4RzmKEAql8TsjfvLfnmV4mT',
	    	    );
	    	    break;
	    	case 2:
	    	    return array(
    	    	    'asy_url'=> 'http://pay.putike.cn/llpay/notify_url.php',
    	    	    'syn_url'=> 'http://pay.putike.cn/llpay/return_url.php',
    	    	    'partner'=> '201504171000289503',
    	    	    'key'    => 'sKteoOGFR4RzmKEAql8TsjfvLfnmV4mT',
	    	    );
	    	    break;
	    }
	}
	/**
	 * 配置UMP参数
	 * @return multitype:string
	 */
	public function setUmppay()
	{
	    if( empty( $this-> channel )) S::error( '401', 'publicFunc-setUmppay' );

	    switch ( $this-> channel )
	    {
	    	case 1:
	    	    return array(
    	    	    'asy_url'   => 'http://pay.feekr.com/ump/notify_url.php',    //异步通知地址
    	    	    'syn_url'   => 'http://pay.feekr.com/ump/call_back_url.php', //同步通知地址
    	    	    'mer_id'    => '6476',
    	    	    'privatekey'=> './cert/feekr/6476_ShangHaiPuTiKe.key.pem',
    	    	    'platcert'  => './cert/feekr/cert_2d59.cert.pem',
	    	    );
	    	    break;
	    	case 2:
	    	    return array(
    	    	    'asy_url'   => 'http://pay.putike.cn/ump/notify_url.php',
    	    	    'syn_url'   => 'http://pay.putike.cn/ump/call_back_url.php',
    	    	    'mer_id'    => '6476',
                    'privatekey'=> './cert/putike/6476_ShangHaiPuTiKe.key.pem',
    	    	    'platcert'  => './cert/putike/cert_2d59.cert.pem',
	    	    );
	    	    break;
	    }
	}
	/**
	 * 配置招行参数
	 * @return multitype:string
	 */
	public function setCmbpay()
	{
	    if( empty( $this-> channel )) S::error( '401', 'publicFunc-setCmbpay' );

	    switch ( $this-> channel )
	    {
	    	case 1:
	    	    return array(
    	    	    'syn_url' => 'http://pay.feekr.com/cmb/call_back_url.php', //同步通知地址
    	    	    'branchid'=> '0021',
    	    	    'cono'    => '004756',
	    	    );
	    	    break;
	    	case 2:
	    	    return array(
    	    	    'syn_url' => 'http://pay.putike.cn/cmb/call_back_url.php',
    	    	    'branchid'=> '0021',
    	    	    'cono'    => '004756',
	    	    );
	    	    break;
	    }
	}
	/**
	 * 配置微信支付参数
	 * @return multitype:string
	 */
	public function setWxpay()
	{
	    if( empty( $this-> channel )) S::error( '401', 'publicFunc-setWxpay' );

	    switch ( $this-> channel )
	    {
	    	case 1:
	    	    return array(
    	    	    'asy_url'   => 'http://pay.feekr.com/wechat_pay/notify_url.php', //异步通知地址
    	    	    'appid'     => 'wx5e301c49a2a27c7f',
    	    	    'appkey'    => 'LRjt2vUWoW7FLRuEHfJ1UeHmlEaWdPfhJOeAC6FhnnZF3Uql7BR2jMIq2wbWezDzUo0bU7HHRZisdL11Z5fEHWCzYUYA1BbcsKOG6nViyuQqCS4rGZu5rM2eGJac89Nc',
    	    	    'signtype'  => 'sha1',
    	    	    'partnerkey'=> 'af30735e449bcfa4aa1d06d26cc01e96',
    	    	    'appsercert'=> '6a3da6a151738ea480ff6d7bc05e0edc',
    	    	    'partner'   => '1220479701',
	    	    );
	    	    break;
	    	case 2:
	    	    return array(
    	    	    'asy_url'   => 'http://pay.putike.cn/wechat_pay/notify_url.php', //异步通知地址
    	    	    'appid'     => 'wx5e301c49a2a27c7f',
    	    	    'appkey'    => 'LRjt2vUWoW7FLRuEHfJ1UeHmlEaWdPfhJOeAC6FhnnZF3Uql7BR2jMIq2wbWezDzUo0bU7HHRZisdL11Z5fEHWCzYUYA1BbcsKOG6nViyuQqCS4rGZu5rM2eGJac89Nc',
    	    	    'signtype'  => 'sha1',
    	    	    'partnerkey'=> 'af30735e449bcfa4aa1d06d26cc01e96',
    	    	    'appsercert'=> '6a3da6a151738ea480ff6d7bc05e0edc',
    	    	    'partner'   => '1220479701',
	    	    );
	    	    break;
	    }
	}
	/**
	 * 根据渠道设置渠道值
	 * @param unknown $api
	 */
	private function getChannel( $api )
	{
	    $host = $_SERVER['HTTP_HOST'];
	    switch ( $host )
	    {
	    	case 'pay.feekr.com':
	    	    $this-> channel = 1;
	    	    break;
	    	case 'pay.putike.cn':

	    	    $this-> channel = 2;
	    	    break;
			/**测试部分**/
			case 'pay.fedora.local':
				$this-> channel = 2;
				break;
			/**测试部分**/
	    	default:
	    	    exit('你想去哪个域名？');
	    	    break;
	    }
	    if( $api == 'on' )
	    {
	    	$sql = 'SELECT * FROM `fnp_channel` WHERE `id`=:id ';
	    	$sh  = $this-> db-> prepare( $sql );
	    	$sh-> execute( array( ':id'=> $this-> channel ));
	    	$row = $sh->fetch();
	    	$this-> appid = $row['appid'];
	    	$this-> secret = $row['secret'];
	    }
	}
    /**
     * 设置支付渠道名称
     * @param unknown $payway
     * @return string
     */
	public function setPayName( $payway )
	{
	    switch ( $payway ){
	    	case '1':
	    	    return '支付宝';
	    	    break;
	    	case '2':
	    	    return '联动优势';
	    	    break;
	    	case '3':
	    	    return '微信支付';
	    	    break;
	    	case '4':
	    	    return '招行支付';
	    	    break;
	    	case '5';
    	    	return '连连支付';
    	    	break;
	    }
	}
	/**
	 * 生成跳转链接
	 * @param unknown $order_id
	 * @return string
	 */
	public function geturl( $order_id, $channel = -1 ){
	    
	    if ( in_array($channel,array('1', '2', '3', '4')) ) $this->channel = $channel;
	    
	    switch ( $this-> channel )
	    {
	    	case '1':
	    	    return self::FEEKRURL.'?id='.$order_id;
	    	    break;
	    	case '2':
	    	    return self::PUTIKEURL.'?id='.$order_id;
	    	    break;
	    	case '3':
	    	    return 'http://mall.tourzj.com/pay/success?id='.$order_id;
	    	case '4':
	    	    return 'http://meisu.putike.cn/pay/success?id='.$order_id;
	    }
	}
}
?>