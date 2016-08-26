<?php
/**
 * 报错信息
 * @author Jacky
 *
 */
class errorMsg
{
    public function __construct()
    {
        
    }
    /**
     * 出错信息
     * @param unknown $code
     */
    public function error($code)
    {
        if( preg_match( "/^2\d{2,3}$/",$code ))
        {
        	return $this -> err200( $code );
        }
        elseif( preg_match( "/^3\d{2,3}$/",$code ))
        {
            return $this -> err300( $code );
        }
        elseif( preg_match( "/^4\d{2,3}$/",$code ))
        {
            return $this -> err400( $code );
        }
        elseif( preg_match( "/^5\d{2,3}$/",$code ))
        {
            return $this -> err500( $code );
            echo 5;
        }
        elseif( preg_match( "/^6\d{2,3}$/",$code ))
        {
            echo 6;
        }
        else
        {
        	
        }
    }
    /**
     * 200错误
     * @param unknown $code
     */
    private function err200($code)
    {
        $msgArr = array(
            '201'=> '订单信息不存在',
            '202'=> '支付失败',
            '203'=> '支付验证失败',
            '204'=> '支付测试',
        );
        return array( null, $code, $msgArr[$code] );
    }
    /**
     * 300错误
     * @param unknown $code
     */
    private function err300($code)
    {
        $msgArr = array(

        );
        return array( null, $code, $msgArr[$code] );
    }
    /**
     * 400错误
     * @param unknown $code
     */
    private function err400($code)
    {
    	$msgArr = array(
            '401'=> '参数为空或不正确',
    	    '402'=> '优惠券不存在',
    	    '403'=> '优惠券没有到可用日期',
    	    '404'=> '优惠券已过期',
    	    '405'=> '优惠券已使用过',
    	    '406'=> '优惠券渠道与订单渠道不同',
    	    '407'=> '订单金额小于优惠券最低金额',
    	    '408'=> '订单渠道不正确',
    	);
    	return array( null, $code, $msgArr[$code] );
    }
    /**
     * 500错误
     * @param unknown $code
     */
    private function err500($code)
    {
        $msgArr = array(
            '501' => '接口数据错误', //调用数据中心出现错误
            '502' => '系统错误',     //自身系统中出现错误
            '503' => '接口异常报错', //调用接口时，接口抛出异常错误
            '504' => '数据库写入失败',
            '505' => '数据库异常',
        );
        return array( null, $code, $msgArr[$code] );
    }
}
?>