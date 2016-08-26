<?php
require_once("../class/common/publicFunc.class.php");
/**
 * 支付生成
 * @author Jacky
 *
 */
class payCreate extends publicFunc
{
    private $order;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 入口类
     */
    public function index()
    {
    	$order  = S::get('id');   //订单号
    	$coupon = S::get('coupon');  //优惠券码
    	if( empty( $order )) S::error( '401', 'payCreate-index' );
    	$this-> getOrder( $order ); //获取订单信息
    	if( !empty( $coupon )) $this-> coupon( $coupon );  //如果优惠券存在更新订单信息
    	return $this-> order;
    }
    /**
     * 获取订单信息
     * @param unknown $order
     */
    private function getOrder( $order )
    {
        if(strtolower(substr($order,0,3)) == 'oct'){
            $orders = explode('_', $order);
            $sql ="SELECT p.id as `product`, p.`name`, o.`updatetime` AS `create`, o.`tel`, p.`price` AS `total`, CONCAT_WS('_',p.`order`,p.id) AS `order` FROM ptc_tour_order_pay AS p LEFT JOIN ptc_tour_order as o ON p.orderid = o.id WHERE p.id =:id ";

            $sh  = $this->oct_db->prepare($sql);
            $sh-> execute( array( ':id'=> $orders[1] ));
            $row = $sh->fetch();
            $this-> order = (object)$row;

        }else{
            $sql = "SELECT * FROM `fnp_order` WHERE `order`=:orderid";
            $sh  = $this-> db-> prepare( $sql );
            $sh-> execute( array( ':orderid'=> $order ));
            $row = $sh->fetch();
            $this-> order = (object)$row;
	    //$ingore_channel = S::get('ingore_channel');  // 三方
            //if( (int)$this-> channel != (int)$this-> order-> channel && empty($ingore_channel)) S::error( '408', 'payCreate-getOrder');
        }



    }
    /**
     * 优惠券主类
     * @param unknown $coupon
     */
    private function coupon( $coupon )
    {
    	if( empty( $coupon )) S::error( '401', 'payCreate-coupon' );
    	$data = $this-> getCoupon( $coupon );   //获取优惠券信息
    	$this-> upOrderByCoupon( $data['id'] ); //更新订单
    	$this-> order-> total = (int)$this-> order-> total - (int)$data['value'];
    }
    /**
     * 更新订单信息，加入优惠券码
     * @param unknown $couponid
     */
    private function upOrderByCoupon( $couponid )
    {
        try {
            $sql    = "UPDATE `fnp_order` SET `coupon`=:coupon WHERE `order`=:order";
            $param  = array( ':coupon'=> $couponid, ':order'=> $this-> order-> order );
            $sh     = $this-> db-> prepare( $sql );
            $return = $sh-> execute( $param );
            if( false === $return ){
                S::error( '504', 'payCreate-upOrderByCoupon' );
            }
        } catch (Exception $e) {
            S::error( '505', 'payCreate-upOrderByCoupon' );
        }
        
    }
    /**
     * 获取优惠券信息并容错
     * @param unknown $coupon
     * @return unknown
     */
    private function getCoupon( $coupon )
    {
    	$sql = "SELECT * FROM `cou_coupon` WHERE `code`=:code";
        $sh  = $this-> db-> prepare($sql);
        $sh-> execute( array( ':code'=> $coupon ));
        $row = $sh-> fetch();
        if( empty( $row )) S::error( '402', 'payCreate-getCoupon' );             //优惠券不存在
        if( $row['start'] > time()) S::error( '403', 'payCreate-getCoupon' );    //优惠券未到使用时间
        if( $row['end'] < time()) S::error( '404', 'payCreate-getCoupon' );      //优惠券过期
        if( (int)$row['status'] != 0 ) S::error( '405', 'payCreate-getCoupon' ); //优惠券已使用
        if( (int)$row['channel'] != (int)$this-> order-> channel ) S::error( '406', 'payCreate-getCoupon' ); //优惠券渠道与订单渠道不同
        if( (int)$row['limit'] != 0 && (int)$this-> order-> total < (int)$row['limit'] ) S::error( '407', 'payCreate-getCoupon' ); //优惠券最低金额未满足
        return $row;
    }
}

?>
