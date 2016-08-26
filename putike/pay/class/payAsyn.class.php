<?php
require_once("../class/common/publicFunc.class.php");
/**
 * 支付返回异步
 * @author Jacky
 *
 */
class payAsyn extends publicFunc
{
    private $id;       //订单号
    private $order;    //订单信息
    private $payway;   //支付类型值
    private $payname;  //支付类型名称
    private $trade_no; //流水号
    private $api;      //api对象
    private $coupon;   //优惠券信息

    public function __construct()
    {
        S::error( '204', '异步数据记录', false );
        parent::__construct();
    }
    
    /**
     * 入口类
     */
    public function index( $id, $payway, $trade_no )
    {
        if( empty( $id ) || empty( $payway ) || empty( $trade_no )) S::error( '401', 'paySyn-index' );
        $this-> api = new api( $this-> appid, $this-> secret );
        $this-> id       = $id;
        $this-> payway   = $payway;
        $this-> trade_no = $trade_no;
        $this-> payname  = $this-> setPayName( $this-> payway );
    	$this-> getOrder();  //获取订单信息

    	if( $this-> validationStat()) //订单状态已支付
    	{
    	    return true;
    	}
    	else //未支付
    	{
    	    if( $this-> saveOrder() ) return true;
    	}
    }
    /**
     * 获取订单信息
     */
    private function getOrder()
    {
        $sql = "SELECT * FROM `fnp_order` WHERE `order`=:orderid";
        $sh  = $this-> db-> prepare( $sql );
        $sh-> execute( array( ':orderid'=> $this-> id ));
        $row = $sh->fetch();
        $this-> order = (object)$row;
    }
    private function getApiOrder()
    {
    	return $this-> api-> orderdetail( $this-> id );
    }
    /**
     * 验证订单状态
     * @return boolean
     */
    private function validationStat()
    {
    	if( empty( $this-> order )) s::error( '201', 'paySyn-validationStat' );
    	$apiorder = $this-> getApiOrder();//获取接口数据

    	if( (int)$this-> order-> stat != (int)$apiorder['status'] )//如果接口订单状态与数据库不同，更新数据库
    	{
    	    try {
    	        $sql = 'UPDATE `fnp_order` SET `stat`=:stat WHERE `order`=:order';
    	        $sh  = $this-> db-> prepare( $sql );
    	        $re  = $sh-> execute( array( ':stat'=> $apiorder['status'], ':order'=> $this-> id ));
    	        if( false === $re ){
    	            S::error( '504', 'paySyn-validationStat' );
    	        }
    	        $this-> order-> stat = $apiorder['status'];
    	    } catch ( Exception $e ) {
    	        S::error( '505', 'paySyn-validationStat' );
    	    }
    	}

    	if( (int)$this-> order-> stat != 1 && (int)$this-> order-> stat != 2 )
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    /**
     * 更新订单信息，推送API，更新优惠券，更新产品购买数
     * @return boolean
     */
    private function saveOrder()
    {
        if( !empty( $this-> order-> coupon )) $this-> getCoupon();
        $this-> api-> orderpay( $this-> id, $this-> payname, $this-> trade_no, $this-> coupon ); //推送支付信息
        
        try
        {
            $this-> db-> beginTransaction();//开启事务处理
            
            if( !empty( $this-> coupon ))
            {
                //优惠券状态更新
                $sql   = 'UPDATE `cou_coupon` SET `state`=:state,`usetime`=:usetime WHERE `id`=:id';
                $param = array(
                    ':state'  => 1,
                    ':usetime'=> time(), 
                    ':id'     => $this-> coupon-> id 
                );
                $sh    = $this-> db-> prepare( $sql );
                if( false === $sh-> execute( $param )) S::error( '504', 'paySyn-saveOrder-coupon' );

                //原价减去优惠价格
                $total = (int)$this-> order-> total - (int)$this-> coupon-> value;
            }
            else
            {
                $total = (int)$this-> order-> total;
            }
             
            $sql   = 'UPDATE `fnp_order` SET `stat`=:stat,`paydate`=:paydate,`payway`=:payway,`trade_no`=:trade_no,`total`=:total WHERE `order`=:order';
            $param = array( 
                ':stat'    => 3, 
                ':paydate' => time(), 
                ':payway'  => $this-> payway, 
                ':trade_no'=> $this-> trade_no, 
                ':total'   => $total, 
                ':order'   => $this-> id
            );
            $sh    = $this-> db-> prepare( $sql );
            if( false === $sh-> execute( $param )) S::error( '504', 'paySyn-saveOrder-order' );
             
            $sql   = 'UPDATE `fnp_product` SET `buys`=`buys`+1 WHERE `id`=:id';
            $sh    = $this-> db-> prepare( $sql );
            
            $param = array( 
                ':id'=> $this-> order-> product 
            );
            
            if( false === $sh-> execute( $param ))
            {
                S::error( '504', 'paySyn-saveOrder-product' );
            }
            else
            {
                $this-> db-> commit();//所有操作成功
                return true;
            }
        }
        catch (Exception $e)
        {
            S::error( '505', 'paySyn-saveOrder' );
        }
    }
    /**
     * 获取优惠券信息
     */
    private function getCoupon()
    {
    	$sql = 'SELECT c.`event`,a.`code`,a.`value`,a.`id`,a.`limit`
                FROM `cou_coupon` AS a 
                  INNER JOIN `cou_coupon_type` AS b ON a.`type`=b.`id`
                  INNER JOIN `cou_activity` AS c ON b.activity=c.id
                WHERE a.`id`=:id';
    	$sh  = $this-> db-> prepare( $sql );
    	$sh-> execute( array( ':id'=> $this-> order-> coupon ));
    	$row = $sh->fetch();
    	$this-> coupon = (object)$row;
    }
}

?>