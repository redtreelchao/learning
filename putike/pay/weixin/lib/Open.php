<?php

namespace Weixin;

class Open {
     
    private $_sign = 'putike&signeurlpaiement';
    
    private $_openid_key = 'wxid';

    private $_snsurl = 'pay.putike.cn';

    private $_config;

    public function __construct(array $config ) {

        if (  empty($config['Mchid'] ) ) {

            throw new \Exception('Mchid未设定');
        }

        if (  empty($config['Key'] ) ) {

            throw new \Exception('Key未设定');
        }

        if (  empty($config['appid'] ) ) {

            throw new \Exception('appid未设定');
        }

        if (  empty($config['appsercert'] ) ) {

            throw new \Exception('appsercert未设定');
        }

        if (  empty($config['asy_url'] ) ) {

            throw new \Exception('asy_url未设定');
        }

        $this->_config = $config;
    }

    public function getopenid() {

        $openid = $this->_getOpenid();

        if ( empty($openid) ) {

            $this->getWxCode();

            $rst = $this->getOpenidByWeixinCallback();

            if ( false === $rst ) {// 获取openid

                throw new \Exception('获取openid失败');

            }
        }

        return $openid;
    }

    public function pay( $openid, $data ) {

        $wxPay = new Pay();
        
        $wxPay->setAppId( $this->_config['appid'] );
        
        $wxPay->setAppSecret( $this->_config['appsercert'] );
        
        $wxPay->setMchid( $this->_config['Mchid'] );
        
        $wxPay->setKey( $this->_config['Key'] );
        
        $nonceStr = Helpers::createNonceStr();
        
        $payinfo = [];

        $payinfo['package'] = $wxPay->getPackage4JsPay( str_replace( ' ', '', $data->name ), '', $data->order, $data->total * 100, $this->_config['asy_url'], $_SERVER['REMOTE_ADDR'], '', '', 0, '', '', 1, 1, '', '', $nonceStr, $openid);
        
        $payinfo['appId'] = $wxPay->getAppId();
        
        $payinfo['timeStamp'] = strval( time() );
        
        $payinfo['nonceStr'] = $nonceStr;
        
        $payinfo['signType'] = 'MD5';
        
        $payinfo['paySign'] = $wxPay->getSign($payinfo);

        return $payinfo;
    }
    
    public function getWxCode() {

        if ( isset($_GET['wxcallback']) )  return false;

        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    
        $wxSns = new Sns( $this->_config['appid'] , $this->_config['appsercert'] );

        $redirect = 'http://'.$this->_snsurl.'/weixin/index.php?wxcallback=1&redirect='.$redirect;
        
        $redirect_uri = $wxSns->setRedirectUri( $redirect );
        
        $wxSns->getAuthorizeUrl();
    }

    public function getOpenidByWeixinCallback()
    {
        if ( !isset($_GET['redirect']) || !is_string($_GET['redirect']) || !filter_var($_GET['redirect'], FILTER_VALIDATE_URL) ) {

            Error::log("获取token失败,原因: redirect参数不存在".$_SERVER['REQUEST_URI']);

            return false;
        } 

        try {

            $smsServer = new Sns( $this->_config['appid'], $this->_config['appsercert'] );

            $accessToken = $smsServer->getAccessToken();
            
        } catch (\Exception $e) {

            Error::log("获取token失败,原因:".$e->getMessage());

            return false;
        }
        
        if (! isset($accessToken['errcode'])) {
            
            if ( isset($accessToken['openid']) ) {
                
                $accessToken = array(
                    $this->_openid_key => (string)$accessToken['openid']
                );
                
                $this->_setUserByCookie( $accessToken );

            } else {

                Error::log("获取token失败:" . json_encode($accessToken));

                return false;
            }
            
        } else {
            
            Error::log("获取token失败:" . json_encode($accessToken));

            return false;
        }
        
        header("location:{$_GET['redirect']}");
        
        exit(0);
    }

    private function _getopenid()
    {
        $user = $this->_getUserByCookie();

        if ( is_array($user) && isset($user[$this->_openid_key]) && true === $this->_checkOpenid($user[$this->_openid_key]) )
        {
            return $user[$this->_openid_key];
        }

        return null;
    }
        
    private function _checkOpenid( $openid )
    {

        return is_string($openid) && strlen($openid) > 15;
    }

    protected function _getUserByCookie( $cookie_name = 'p_i1' )
    {
        if( empty($_COOKIE[$cookie_name]) )
        {
            return false;
        }
        
        $wxUserInfo = json_decode( $_COOKIE[$cookie_name], true );
        
        if ( !$wxUserInfo || empty($wxUserInfo['md5Sign']) ) {
            
            return false;
            
        }
        
        $md5Sign = $wxUserInfo['md5Sign'];
        
        unset($wxUserInfo['md5Sign']);
        
        krsort($wxUserInfo);
        
        if (md5(json_encode($wxUserInfo) . $this->_sign) != $md5Sign) {
            
            return false;
            
        }
        
        return $wxUserInfo;
    }
    
    protected function _setUserByCookie( array $userInfo, $cookie_name = 'p_i1' )
    {
        krsort($userInfo); 
        
        $userInfo['md5Sign'] = md5(json_encode($userInfo) . $this->_sign ); 
        
        setcookie($cookie_name, json_encode($userInfo), time() + 86400 * 180, '/');
        
        return true;
    }
}