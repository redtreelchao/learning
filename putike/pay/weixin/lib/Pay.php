<?php

namespace Weixin;
/**
 * 微信支付接口
 * 
 * 第三方接口 version 3.37
 */
class Pay
{
    /**
     * 接口地址
     */
    private $_url = 'https://api.mch.weixin.qq.com/';

    /**
     * 获取微信支付版本
     *
     * @return string
     */
    public function getVersion()
    {
        return '3.3.7';
    }
    
    /**
     * access_token微信公众平台凭证。
     */
    private $_accessToken = null;
    
    /**
     * Key 商户支付密钥。登录微信商户后台，进入栏目【账设置】【密码安全】【 API密钥】，进入设置 API密钥。
     */
    private $_key = "";
    
    /**
     * appId
     * 
     * 微信公众号身份的唯一标识。
     */
    private $_appId = null;
    
    /**
     * appSecret
     * 
     * 微信公众号应用密钥
     */
    private $_appSecret = null;
    
    /**
     * Mchid 商户 ID ，身份标识
     */
    private $_mchid = "";
    
    /**
     * 子商户号 sub_mch_id
     */
    private $_sub_mch_id = "";
    
    public function setAppId($appId)
    {
        $this->_appId = $appId;
    }

    public function getAppId()
    {
        if ( empty($this->_appId) ) {
            
            throw new \Exception('AppId未设定');
            
        }
        
        return $this->_appId;
    }

    public function setAppSecret($appSecret)
    {
        $this->_appSecret = $appSecret;
    }

    public function getAppSecret()
    {
        if ( empty($this->_appSecret) ) {
            
            throw new \Exception('AppSecret未设定');
            
        }
        
        return $this->_appSecret;
    }
    
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        if ( empty($this->_accessToken) ) {
            
            throw new \Exception('access token未设定');
            
        }
        
        return $this->_accessToken;
    }
    
    public function setMchid($mchid)
    {
        $this->_mchid = $mchid;
    }

    public function getMchid()
    {
        if (empty($this->_mchid)) {
            throw new \Exception('Mchid未设定');
        }
        return $this->_mchid;
    }

    public function setSubMchId($sub_mch_id)
    {
        $this->_sub_mch_id = $sub_mch_id;
    }

    public function getSubMchId()
    {
        return $this->_sub_mch_id;
    }
    
    public function setKey($key)
    {
        $this->_key = $key;
    }

    public function getKey()
    {
        if ( empty($this->_key) ) {
            
            throw new \Exception('Key未设定');
            
        }
        
        return $this->_key;
    }

    /**
     * cert 商户证书。
     *
     * @var string
     */
    private $_cert = "";

    public function setCert($cert)
    {
        $this->_cert = $cert;
    }

    public function getCert()
    {
        if ( empty($this->_cert) ) {
            throw new f('商户证书未设定');
        }
        return $this->_cert;
    }

    /**
     * certKey 商户证书秘钥。
     *
     * @var string
     */
    private $_certKey = "";

    public function setCertKey($certKey)
    {
        $this->_certKey = $certKey;
    }

    public function getCertKey()
    {
        if ( empty($this->_certKey) ) {
            
            throw new \Exception('商户证书秘钥未设定');
            
        }
        
        return $this->_certKey;
    }

    /**
     * 统一支付接口
     * URL地址:https://api.mch.weixin.qq.com/pay/unifiedorder
     *
     */
    public function unifiedorder($device_info, $nonce_str, $body, $attach, $out_trade_no, $total_fee, $spbill_create_ip, $time_start, $time_expire, $goods_tag, $notify_url, $trade_type, $openid, $product_id)
    {
        $postData = array();
        $postData["appid"] = $this->getAppId();
        $postData["mch_id"] = $this->getMchid();
        $postData["device_info"] = $device_info;
        $postData["nonce_str"] = $nonce_str;
        $postData["body"] = $body;
        $postData["attach"] = $attach;
        $postData["out_trade_no"] = $out_trade_no;
        $postData["total_fee"] = $total_fee;
        $postData["spbill_create_ip"] = $spbill_create_ip;
        $postData["time_start"] = $time_start;
        $postData["time_expire"] = $time_expire;
        $postData["goods_tag"] = $goods_tag;
        $postData["notify_url"] = $notify_url;
        $postData["trade_type"] = $trade_type;
        $postData["openid"] = $openid;
        $postData["product_id"] = $product_id;
        
        $sign = $this->getSign($postData);
        $postData["sign"] = $sign;
        $xml = Helpers::arrayToXml($postData);
        $rst = $this->post($this->_url . 'pay/unifiedorder', $xml);
        return $this->returnResult($rst);
    }

    /**
     * 订单查询接口
     * 接口链：https://api.mch.weixin.qq.com/pay/orderquery
     */
    public function orderquery($transaction_id, $out_trade_no, $nonce_str)
    {
        $postData = array();
        $postData["appid"] = $this->getAppId();
        $postData["mch_id"] = $this->getMchid();
        $postData["transaction_id"] = $transaction_id;
        $postData["out_trade_no"] = $out_trade_no;
        $postData["nonce_str"] = $nonce_str;
        $postData["sign"] = $this->getSign($postData);
        $xml = Helpers::arrayToXml($postData);
        $rst = $this->post($this->_url . 'pay/orderquery', $xml);
        return $this->returnResult($rst);
    }
    
    /**
     * 关闭订单接口
     * 接口url：https://api.mch.weixin.qq.com/pay/closeorder
     */
    public function closeorder($out_trade_no, $nonce_str)
    {
        $postData = array();
        $postData["appid"] = $this->getAppId();
        $postData["mch_id"] = $this->getMchid();
        $postData["out_trade_no"] = $out_trade_no;
        $postData["nonce_str"] = $nonce_str;
        $sign = $this->getSign($postData);
        $postData["sign"] = $sign;
        $xml = Helpers::arrayToXml($postData);
        $rst = $this->post($this->_url . 'pay/closeorder', $xml);
        return $this->returnResult($rst);
    }

    /**
     * Sign签名生成方法
     *
     * @param array $para   
     * @return string
     */
    public function getSign(array $para)
    {
        // 过滤不参与签名的参数
        $paraFilter = Helpers::paraFilter($para);
        // 对数组进行（字典序）排序
        $paraFilter = Helpers::argSort($paraFilter);
        // 进行URL键值对的格式拼接成字符串string1
        $string1 = Helpers::createLinkstring($paraFilter);
        
        $sign = $string1 . '&key=' . $this->getKey();
        
        $sign = strtoupper(md5($sign));
        
        return $sign;
    }
    
    public function getPackage4JsPay($body, $attach, $out_trade_no, $total_fee, $notify_url, $spbill_create_ip, $time_start, $time_expire, $transport_fee, $product_fee, $goods_tag, $bank_type = "WX", $fee_type = 1, $input_charset = "GBK", $device_info = "", $nonce_str = "", $openid = "")
    {
        $ret = $this->unifiedorder($device_info, $nonce_str, $body, $attach, $out_trade_no, $total_fee, $spbill_create_ip, $time_start, $time_expire, $goods_tag, $notify_url, "JSAPI", $openid, "");
    
        return "prepay_id={$ret['prepay_id']}";
    }
    
    /**
     * 申请退款接口
     * 接口url：https://api.mch.weixin.qq.com/secapi/pay/refund
     */
    public function refund($transaction_id, $out_trade_no, $out_refund_no, $total_fee, $refund_fee, $op_user_id, $nonce_str)
    {
        $postData = array();
        $postData["appid"] = $this->getAppId();
        $postData["mch_id"] = $this->getMchid();
        $postData["transaction_id"] = $transaction_id;
        $postData["out_trade_no"] = $out_trade_no;
        $postData["nonce_str"] = $nonce_str;
        $postData['out_refund_no'] = $out_refund_no;
        $postData['refund_fee'] = $refund_fee;
        $postData['op_user_id'] = $op_user_id;
        $postData['total_fee'] = $total_fee;
        $postData["sign"] = $this->getSign($postData);
        $xml = Helpers::arrayToXml($postData);
        $rst = $this->post($this->_url . 'secapi/pay/refund', $xml);
        return $this->returnResult($rst);
    }
    
    /**
     * 查询退款接口
     * 接口url：https://api.mch.weixin.qq.com/pay/refundquery
     */
    public function refundQuery($out_trade_no, $out_refund_no, $transaction_id, $refund_id)
    {
        $postData = array();
        $postData["appid"] = $this->getAppId();
        $postData["mch_id"] = $this->getMchid();
        $postData["transaction_id"] = $transaction_id;
        $postData["out_trade_no"] = $out_trade_no;
        $postData["nonce_str"] = $nonce_str;
        $postData['out_refund_no'] = $out_refund_no;
        $postData['refund_id'] = $refund_id;
        $postData["sign"] = $this->getSign($postData);
        $xml = Helpers::arrayToXml($postData);
        $rst = $this->post($this->_url . 'pay/refundquery', $xml);
        return $this->returnResult($rst);
    }
    
    /**
     * 通用通知接口
     * @return array
     */
    public function getNotifyData($xml)
    {
        return $this->initXml($xml);
    }
    
    protected function MakeSign( $values )
    {
        //签名步骤一：按字典序排序参数
        ksort( $values );
        
        $string = $this->ToUrlParams( $values );
        
        //签名步骤二：在string后加入KEY
        
        $string = $string . "&key=".$this->getKey();
        
        //签名步骤三：MD5加密
        
        $string = md5($string);
        
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        
        return $result;
    }
    
    protected function ToUrlParams( $values )
    {
        $buff = "";
        
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
    
        $buff = trim($buff, "&");
        
        return $buff;
    }
    
    /**
     * 校正微信通知结果是否正确
     * @param xml $xml
     */
    public function initXml( $xml )
    {
        $postArray = Helpers::xmlToArray($xml);
        
        if( empty($postArray) ){
            
            throw new Exception("xml数据异常！");
            
        }
        
        if( isset($postArray['return_code']) && $postArray['return_code'] !== 'SUCCESS' )
        {
            throw new Exception( (string)$postArray['return_msg'] );
        }
        
        if( isset($postArray['result_code']) && $postArray['result_code'] !== 'SUCCESS' )
        {
            throw new Exception( (string)$postArray['err_code'] . "." . (string)$postArray['err_code_des'] );
        }
        
        $this->checkSign( $postArray );
        
        return $postArray;
        
    }
    
    private function checkSign( $postArray )
    {
        if( !array_key_exists('sign', $postArray) )
        {
            throw new Exception("签名错误！");
        }
        
        $sign = $this->MakeSign( $postArray );
        
        if( $postArray['sign'] !== $sign ){
        
            throw new Exception('签名错误！'.$postArray['sign'].'---'.$sign);
        }
        
    }
    
    private function returnResult($rst)
    {
        $rst = Helpers::xmlToArray($rst);
        if (! empty($rst['return_code'])) {
            if ($rst['return_code'] == 'FAIL') {
                throw new \Exception($rst['return_msg']);
            } else {
                if ($rst['result_code'] == 'FAIL') {
                    throw new \Exception($rst['err_code'] . ":" . $rst['err_code_des']);
                } else {
                    return $rst;
                }
            }
        } else {
            throw new \Exception("网络请求失败");
        }
    }

    /**
     * 推送消息给到微信服务器
     *
     * @param string $url            
     * @param string $xml            
     * @param array $options            
     * @return mixed
     */
    public function post($url, $xml, $timeout = 20 )
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        curl_setopt($ch, CURLOPT_URL, $url);
       
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		curl_setopt($ch, CURLOPT_POST, TRUE);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		
		$rst = curl_exec($ch);
		
		$error = curl_errno($ch);
		
		curl_close($ch); 
		
		if ( empty($rst) ) throw new \Exception("微信服务器未有效的响应请求".$url);
		
		return $rst;
    }

}

