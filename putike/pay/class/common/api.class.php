<?php
/**
 * api接口调用类
 * @author Jacky
 *
 */
class api
{
    //测试
    //const URL       = 'http://121.199.13.135/app.php';
    //正式
    const URL       = 'http://api.putike.cn/app.php';
    private $appid;
    private $secret;
    
    public function __construct( $appid, $secret )
    {
        $this-> appid  = $appid;
        $this-> secret = $secret;
    }
    private function show( $return )
    {
    	if( !isset( $return )) S::error( '501', 'api-show' );
    	if( (int)$return['code'] !== 0 ) S::error( '503', $return['code'].':'.$return['message'] );
    	return $return['data'];
    }
    /**
     * 获取套餐数据接口
     * @param number $page 页码
     * @param string $keyword 关键字
     * @param unknown $type 类型
     * @param number $source 出发城市代码
     * @param number $target 目的地城市代码
     * @param string $checkin 入住时间
     * @param string $checkout 离店时间
     * @param number $min_price 最小价格
     * @param number $max_price 最大价格
     * @param number $limit 行数
     */
    public function getpackages( $keyword, $type, $payment, $checkin=null, $checkout=null, $page=1, $limit=15, $source=null, $target=null, $min_price=null, $max_price=null){
        if(!empty($keyword))  $args['keyword']  =$keyword;
        if(!empty($payment))  $args['payment']  =$payment;
        if(!empty($type))     $args['type']     =$type;
        if(!empty($checkin))  $args['checkin']  =$checkin;
        if(!empty($checkout)) $args['checkout'] =$checkout;
        if(!empty($source))   $args['source']   =$source;
        if(!empty($target))   $args['target']   =$target;
        if(!empty($min_price))$args['min_price']=$min_price;
        if(!empty($max_price))$args['max_price']=$max_price;
        $args['limit'] = $limit;
    
        ksort($args, SORT_STRING);
        $request = '';
        foreach ($args as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
        $params = array(
            'appid'     => $this-> appid,
            'method'    => 'product_search',
            'format'    => 'json',//xml json
            'secret'    => md5($request.$this -> secret),
        );
        if( !empty( $page )) $params['page'] = $page;

        //var_dump(self::URL.'?'.http_build_query( array_merge( $params, $args )));
        $return = json_decode( file_get_contents( self::URL.'?'.http_build_query( array_merge( $params, $args ))), true );
        return $this-> show( $return );
    }
    /**
     * 获取产品状态
     * @param unknown $type
     * @param unknown $payment
     * @param number $status
     */
    public function getpackagestatus( $type, $payment, $status=1 )
    {
        if(!empty($payment))  $args['payment']  =$payment;
        if(!empty($type))     $args['type']     =$type;
        if(!empty($status))   $args['status']   =$status;
        ksort($args, SORT_STRING);
        $request = '';
        foreach ($args as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
        
        $params = array(
            'appid'     => $this-> appid,
            'method'    => 'product_status',
            'format'    => 'json',//xml json
            'secret'    => md5($request.$this -> secret),
        );
        $return = json_decode( file_get_contents( self::URL.'?'.http_build_query( array_merge( $params, $args ))), true );
        return $this-> show( $return );
    }
    /**
     * 新建订单
     * @param unknown $contact
     * @param unknown $tel
     * @param unknown $email
     * @param unknown $ip
     */
    public function createorder( $contact, $tel, $email, $ip )
    {
        $args = array(
            'currency' => 1,
            'paytype'  => 'internal',
        );
        if(!empty($contact))  $args['contact'] = $contact;
        if(!empty($tel))      $args['tel']     = $tel;
        if(!empty($email))    $args['email']   = $email;
        if(!empty($ip))       $args['ip']      = $ip;
        
        ksort( $args, SORT_STRING );
        $request = '';
        foreach ( $args as $k => $v ) $request .= "{$k}{$v}";
        
        $params = array(
                'appid'  => $this-> appid,
                'method' => 'order_create',
                'format' => 'json',//xml json
                'secret' => md5( $request.$this-> secret ),
        );
        $return = json_decode( S::curl( self::URL,http_build_query( array_merge( $params, $args ))), true );
        return $this-> show( $return );
    }
    /**
     * 添加订单详情
     * @param unknown $order
     * @param unknown $code
     * @param number $num
     * @param string $peoples
     * @param number $checkin
     * @param number $checkout
     * @param string $product
     * @param string $remark
     */
    public function uporder( $order, $code, $num, $peoples=null, $checkin=null, $checkout=null, $product=null,$remark=null){
        $postdata = array(
            'order'   =>$order,
            'code'    =>$code,
            'num'     =>$num,
        );
        if(!empty($peoples)) $postdata['peoples'] =$peoples;
        if(!empty($checkin)) $postdata['checkin'] =$checkin;
        if(!empty($checkout))$postdata['checkout']=$checkout;
        if(!empty($product)) $postdata['product'] =$product;
        if(!empty($remark))  $postdata['remark']  =$remark;
        ksort($postdata, SORT_STRING);
        $request = '';
        foreach ($postdata as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
    
        $params = array(
            'appid'     => $this-> appid,
            'method'    => 'order_room',
            'format'    => 'json',//xml json
            'secret'    => md5( $request.$this->secret ),
        );
        $return = json_decode( S::curl( self::URL, http_build_query( array_merge( $params, $postdata ))), true );
        return $this-> show( $return );
    }
    /**
     * 机+酒订单
     * @param unknown $order
     * @param unknown $code
     * @param unknown $num
     * @param unknown $date
     * @param unknown $peoples
     * @param string $product
     * @param string $remark
     */
    public function upfhorder( $order, $code, $num, $date, $peoples, $product=null, $remark=null){
        $postdata=array(
            'order'   =>$order,
            'code'    =>$code,
            'num'     =>$num,
            'date'    =>$date,
            'peoples' =>$peoples,
        );
        if(!empty($product)) $postdata['product'] =$product;
        if(!empty($remark))  $postdata['remark']  =$remark;
        ksort($postdata, SORT_STRING);
        $request = '';
        foreach ($postdata as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
    
        $params = array(
            'appid'     => $this->appid,
            'method'    => 'order_flight',
            'format'    => 'json',//xml json
            'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( S::curl( self::URL, http_build_query( array_merge( $params, $postdata ))), true );
        return $this-> show( $return );
    }
    /**
     * 订单详情
     * @param unknown $order
     */
    public function orderdetail( $order ){
        $postdata = array(
            'order'=> $order,
        );
        ksort($postdata, SORT_STRING);
        $request = '';
        foreach ($postdata as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
    
        $params = array(
            'appid'     => $this-> appid,
            'method'    => 'order_view',
            'format'    => 'json',//xml json
            'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( S::curl( self::URL, http_build_query( array_merge( $params, $postdata ))), true );
        return $this-> show( $return );
    }
    /*
     * 订单退款
     */
    public function orderrefund( $order ){
        $postdata = array(
            'order'=> $order,
        );
        ksort($postdata, SORT_STRING);
        $request = '';
        foreach ($postdata as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
        
        $params = array(
            'appid'     => $this-> appid,
            'method'    => 'order_refund',
            'format'    => 'json',//xml json
            'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( S::curl( self::URL, http_build_query( array_merge( $params, $postdata ))), true );
        return $this-> show( $return );
    }
    /**
     * 订单开票
     * @param unknown $order
     * @param unknown $payer //抬头
     * @param unknown $item  //类目
     * @param unknown $receiver //收件人
     * @param unknown $receivertel //收件电话
     * @param unknown $receiveraddr // 收件地址
     */
    public function orderinvoice( $order, $payer, $item, $receiver, $receivertel, $receiveraddr ){
        $postdata = array(
            'order'        => $order,
            'payer'        => $payer,
            'item'         => $item,
            'receiver'     => $receiver,
            'receivertel'  => $receivertel,
            'receiveraddr' => $receiveraddr,
        );
        ksort($postdata, SORT_STRING);
        $request = '';
        foreach ($postdata as $k => $v){
            if(is_array($v))$request .= $k.serialize($v);
            else $request .= "{$k}{$v}";
        }
    
        $params = array(
                'appid'     => $this-> appid,
                'method'    => 'order_invoice',
                'format'    => 'json',//xml json
                'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( S::curl( self::URL, http_build_query( array_merge( $params, $postdata ))), true );
        return $this-> show( $return );
    }
    /**
     * 获取所有酒店信息
     * @param string $country
     * @param string $city
     * @return unknown
     */
    public function getHotelAll( $country=null, $city=null ){
        $args = array();
        if(!empty($country))$args['country'] = $country;//国家id
        if(!empty($city))   $args['city']    = $city;//城市id

        ksort($args, SORT_STRING);
        $request = '';
        foreach ($args as $k => $v) $request .= "{$k}{$v}";
    
        $params = array(
                'appid'     => $this-> appid,
                'method'    => 'hotel_all',
                'format'    => 'json',//xml json
                'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( file_get_contents( self::URL.'?'.http_build_query( array_merge( $params, $args ))), true );
        return $this-> show( $return );
    }
    /**
     * 获取所有国家信息
     * @return unknown
     */
    public function getCountryAll( ){
        $args = array();
        ksort($args, SORT_STRING);
        $request = '';
        foreach ($args as $k => $v) $request .= "{$k}{$v}";
    
        $params = array(
                'appid'     => $this-> appid,
                'method'    => 'district_country',
                'format'    => 'json',//xml json
                'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( file_get_contents( self::URL.'?'.http_build_query( array_merge( $params, $args ))), true );
        return $this-> show( $return );
    }
    /**
     * 获取所有城市信息
     * @param string $country
     * @return unknown
     */
    public function getCityAll( $country ){
        $args = array();
        if(!empty($country))$args['country'] = $country;//国家id
        
        ksort($args, SORT_STRING);
        $request = '';
        foreach ($args as $k => $v) $request .= "{$k}{$v}";
    
        $params = array(
                'appid'     => $this-> appid,
                'method'    => 'district_city',
                'format'    => 'json',//xml json
                'secret'    => md5( $request.$this-> secret ),
        );
        $return = json_decode( file_get_contents( self::URL.'?'.http_build_query( array_merge( $params, $args ))), true );
        return $this-> show( $return );
    }
    /**
     * 添加订单信息
     * @param unknown $order_id
     * @param unknown $type
     * @param unknown $trade_no
     * @param array   $coupon //优惠券数组
     */
    public function orderpay( $order_id, $type, $trade_no, $coupon ){
        $postdata = array(
            'order'=> $order_id,
            'time' => date('Y-m-d H:i:s'),
            'type' => $type,
            'trade'=> $trade_no,
        );
        if( !empty( $coupon ))
        {
            $postdata['rebate'] = $coupon-> value;
            $postdata['rebatetype'] = $coupon-> eventname . $coupon-> code;
        }
         
        ksort($postdata, SORT_STRING);
        $request = '';
        foreach ( $postdata as $k => $v ){
            if( is_array( $v ))$request .= $k.serialize( $v );
            else $request .= "{$k}{$v}";
        }
    
        $params = array(
            'appid'     => $this-> appid,
            'method'    => 'order_pay',
            'format'    => 'json',//xml json
            'secret'    => md5( $request.$this-> secret ),
        );

        /**
         * 推送3次，如果成功跳出循环
         */
        for ( $i=1; $i<=3; $i++ )
        {
            //推送接口
            $return = json_decode( S::curl( self::URL, http_build_query( array_merge( $params, $postdata ))), true );
            
            /***********************************************记录推送日志*********************************************************/
            
            if( isset( $return ) && (int)$return['code'] !== 0 )//如果返回值不为空，并且CODE不为0
            {
                $db  = new PDO('mysql:host='.publicFunc::DBLOCATION.';dbname='.publicFunc::DBNAME, publicFunc::DBUSER, publicFunc::DBPASS);
                $db-> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $db-> exec('set names utf8');
                $sql = 'INSERT INTO `log_payapi` (`msg`,`order`,`time`,`code`) VALUES (:msg,:order,:time,:code)';
                $db-> prepare( $sql )-> execute( array( ':msg'=> $return['message'], ':order'=> $order_id, ':time'=> time(), ':code'=> $return['code'] ));
            }
            
            /*******************************************************************************************************************/
            if( (int)$return['code'] === 0 )//如果推送成功，则跳出循环
            {
                return $return['data'];
            }
            else //推送不成功
            {
            	if( $i == 3 ) //推送次数为3
            	{
            	    //并且接口推送无任何数据,记录错误日志
            		if( !isset( $return )) S::error( '501', 'api-show' );
            	}
            }
        }
    }
}
?>