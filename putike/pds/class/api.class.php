<?php
/**
 * API 基类
 +-----------------------------------------
 * @category
 * @package page
 * @author Page7
 * @version $Id$
 */
class api
{
    // error code
    static public $error = 0;

    // base code message
    static public $error_msg = array(
        '403'   => '账号无权限访问',
        '404'   => '方法未找到',
        '405'   => '无权操作该方法',
        '409'   => '账号验证失败',
        '410'   => '密钥错误',
        '411'   => 'Token错误',
        '413'   => '账号已在其他终端登录',
        '419'   => '订单操作必需使用POST方法',
        '420'   => 'XML文档不正确',
    );

    // account
    static public $account = 0;

    static public $org = 0;

    static public $orgname = '';

    // page
    static public $page = 1;

    // format
    static public $format = 'json';

    // all method and args
    static public $func = array(
        'district'  => array(
            'country'   => array(),
            'city'      => array('country'=>null),
            'area'      => array('city'=>null, 'type'=>'district'),
        ),
        'hotel'     => array(
            'login'         => array('username'=>null, 'password'=>null),
            'link'          => array('url'=>null),
            'types'         => array(),
            'load'          => array('id'=>null),
            'ext'           => array('id'=>null),
            'create'        => array('name'=>null, 'en'=>null, 'country'=>null, 'city'=>null, 'district'=>null, 'address'=>null, 'tel'=>null, 'lng'=>null, 'lat'=>null, 'pms'=>null, 'type'=>null, 'star'=>0, 'roomnum'=>null, 'opening'=>null, 'redecorate'=>null, 'bland'=>'',  'checkin'=>null, 'checkout'=>null, 'policies'=>array(), 'intro'=>null, 'edges'=>array(),  'tags'=>null, 'status'=>1),
            'update'        => array('id'=>null, 'name'=>null, 'en'=>null, 'country'=>null, 'city'=>null, 'district'=>null, 'address'=>null, 'tel'=>null, 'lng'=>null, 'lat'=>null, 'pms'=>null, 'type'=>null, 'star'=>0, 'roomnum'=>null, 'opening'=>null, 'redecorate'=>null, 'bland'=>'',  'checkin'=>null, 'checkout'=>null, 'policies'=>array(), 'intro'=>null, 'edges'=>array(), 'tags'=>null, 'status'=>1),
            'search'        => array('country'=>'', 'province'=>'', 'city'=>'', 'id'=>'', 'name'=>'', 'en'=>'', 'brand'=>'', 'type'=>'', 'star'=>'', 'limit'=>10, 'export'=>false),
            'all'           => array('country'=>0,  'city'=>0),
            'prepaymin'     => array('checkin'=>0,  'checkout'=>0, 'hotels'=>''),
            'history'       => array('id'=>null),
            'around_bind'   => array('hotel_id'=>0, 'around_ids'=>0),
            'around_unbind' => array('hotel_id'=>0, 'around_id'=>0),
            'around_list'   => array('hotel_id'=> 0, 'limit'=> 10)
        ),
        'room'      => array(
            'load'      => array('hotel'=>null),
            'save'      => array('hotel'=>null, 'roomsummary'=>null, 'tags'=>null, 'rooms'=>null),
            'candel'    => array('id' => 0),
        ),
        'amenserv'  => array(
            'amenity'           => array('hotel'=>null),
            'update_amenity'    => array('hotel'=>null, 'amenities'=>null),
            'activity'          => array('hotel'=>null),
            'update_activity'   => array('hotel'=>null, 'activities'=>null),
            'service'           => array('hotel'=>null),
            'update_service'    => array('hotel'=>null, 'services'=>null, 'tags'=>null),
        ),
        'tag'       => array(
            'types'     => array('types'=>null, 'tags'=>0),
            'create'    => array('name'=>null, 'type'=>null),
        ),
        'product'   => array(
            'status'    => array('type'=>1, 'payment'=>'ticket', 'status'=>null),
            'search'    => array('keyword'=>'', 'type'=>1, 'payment'=>'ticket', 'source'=>0, 'target'=>0, 'checkin'=>'', 'checkout'=>'', 'min_price'=>null, 'max_price'=>null, 'limit'=>15),
            'calendar'  => array('item'=>''),
        ),
        'order' => array(
            'booking'   => array('currency'=>1, 'paytype'=>'', 'contact'=>'', 'tel'=>'', 'email'=>'', 'ip'=>'', 'rooms'=>array(), 'flight'=>array()),
            'create'    => array('currency'=>1, 'paytype'=>'', 'contact'=>'', 'tel'=>'', 'email'=>'', 'ip'=>''),
            'room'      => array('order'=>'', 'code'=>'', 'num'=>1, 'product'=>'', 'peoples'=>array(), 'checkin'=>0, 'checkout'=>0, 'remark'=>''),
            'flight'    => array('order'=>'', 'code'=>'', 'num'=>1, 'product'=>'', 'date'=>'', 'peoples'=>array(), 'remark'=>''),
            'auto'      => array('order'=>'', 'code'=>'', 'num'=>1, 'product'=>'', 'date'=>'', 'peoples'=>array(), 'remark'=>''),
            'goods'     => array('order'=>'', 'code'=>'', 'num'=>1, 'product'=>'', 'contact'=>'', 'tel'=>'', 'address'=>'', 'remark'=>''),
            'apply'     => array('order'=>'', 'ticket'=>0, 'group'=>0, 'checkin'=>'', 'adult'=>2, 'child'=>0, 'people'=>'', 'birth'=>'', 'bed'=>'', 'tel'=>'', 'email'=>'', 'require'=>''),
            'invoice'   => array('order'=>'', 'payer'=>'', 'item'=>'', 'receiver'=>'', 'receivertel'=>'', 'receiveraddr'=>''),
            'pay'       => array('order'=>'', 'time'=>'', 'type'=>'', 'account'=>'', 'trade'=>'', 'rebate'=>0, 'rebatetype'=>''),
            'refund'    => array('order'=>'', 'remark'=>''),
            'view'      => array('order'=>''),
        ),
        'user'  => array(
            'login'     => array('username'=>null, 'password'=>null),
            'info'      => array('uid'=>null),
        ),
        'picture'  => array(
            'gallery_update'  => array('id'=>null, 'name'=>null, 'city'=>null, 'hotel'=>null),
            'gallery'         => array('id'=>null, 'type'=>'', 'order'=>'update', 'limit'=>10),
            'gallery_hotel'   => array('hotel'=>null,  'order'=>'update', 'limit'=>10),
            'recently'        => array('limit'=>3, 'picture'=>4),
            'search'          => array('keyword'=>'', 'type'=>'picture', 'limit'=>10),
            'queue'           => array('limit'=>10),
            'unbind'          => array(),
            'bind'            => array('id'=>null, 'hotel'=>null),
            'type_update'     => array('id'=>null, 'name'=>null, 'pid'=>0),
            'type_delete'     => array('id'=>null),
            'type'            => array('gallery'=>null),
            'tags'            => array('keyword'=>'', 'num'=>10),
            'upload'          => array('file'=>null, 'name'=>null, 'lng'=>null, 'lat'=>null,'gallery'=>0),
            'load'            => array('id'=>null),
            'edit'            => array('id'=>null, 'title'=>null, 'intro'=>null, 'gallery'=>null, 'type'=>null, 'subtype'=>null, 'copyright'=>'', 'tags'=>null),
            'edit_multi'      => array('ids'=>null, 'titles'=>null, 'gallery'=>null, 'type'=>null, 'subtype'=>null),
            'delete'          => array('ids'=>null),
        ),
        'around'    => array(
            'create'    => array('name'=>'', 'en'=>'', 'type'=>null, 'country'=>null, 'city'=>null, 'lng'=>'', 'lat'=>'', 'status'=>1),
            'update'    => array('id'=>0, 'name'=>'', 'en'=>'', 'type'=>null, 'country'=>null, 'city'=>null, 'lng'=>'', 'lat'=>'', 'status'=>1),
            'search'    => array('keywords'=>'', 'hotel_id' => null, 'distance'=>0, 'limit'=>0),
            'delete'    => array('id'=>0),
            'type'      => array(),
        ),
    );

    // function limit
    static public $func_limit = array(
        'district'  => array(
            'country'   => array('api'=>1, 'user'=>1, 'hotel'=>1),
            'city'      => array('api'=>1, 'user'=>1, 'hotel'=>1),
            'area'      => array('api'=>1, 'user'=>1, 'hotel'=>1),
        ),
        'hotel'     => array(
            'login'     => array('api'=>0, 'user'=>1, 'hotel'=>1, 'untoken'=>1),
            'link'      => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'types'     => array('api'=>0, 'user'=>1, 'hotel'=>1),
            'load'      => array('api'=>1, 'user'=>1, 'hotel'=>1),
            'ext'       => array('api'=>1, 'user'=>0, 'hotel'=>1),
            'create'    => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'update'    => array('api'=>0, 'user'=>1, 'hotel'=>'id'),
            'search'    => array('api'=>1, 'user'=>1, 'hotel'=>0),
            'all'       => array('api'=>1, 'user'=>1, 'hotel'=>0),
            'prepaymin' => array('api'=>1, 'user'=>1, 'hotel'=>0),
            'history'   => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'around_bind'  => array('api'=>0, 'user'=>1, 'hotel'=>'hotel_id'),
            'around_unbind'=> array('api'=>0, 'user'=>1, 'hotel'=>'hotel_id'),
            'around_list'  => array('api'=>0, 'user'=>1, 'hotel'=>'hotel_id'),

        ),
        'room'      => array(
            'load'      => array('api'=>1, 'user'=>1, 'hotel'=>'hotel'),
            'save'      => array('api'=>0, 'user'=>1, 'hotel'=>'hotel'),
            'candel'    => array('api'=>1, 'user'=>1, 'hotel'=>1),
        ),
        'amenserv'  => array(
            'amenity'           => array('api'=>1, 'user'=>1, 'hotel'=>'hotel'),
            'update_amenity'    => array('api'=>0, 'user'=>1, 'hotel'=>'hotel'),
            'activity'          => array('api'=>1, 'user'=>1, 'hotel'=>'hotel'),
            'update_activity'   => array('api'=>0, 'user'=>1, 'hotel'=>'hotel'),
            'service'           => array('api'=>1, 'user'=>1, 'hotel'=>'hotel'),
            'update_service'    => array('api'=>0, 'user'=>1, 'hotel'=>'hotel'),
        ),
        'tag'       => array(
            'types'     => array('api'=>1, 'user'=>1, 'hotel'=>1),
            'create'    => array('api'=>0, 'user'=>1, 'hotel'=>1),
        ),
        'product'   => array(
            'status'    => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'search'    => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'calendar'  => array('api'=>1, 'user'=>0, 'hotel'=>0),
        ),
        'order' => array(
            'booking'   => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'create'    => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'room'      => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'flight'    => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'auto'      => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'goods'     => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'apply'     => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'invoice'   => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'pay'       => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'refund'    => array('api'=>1, 'user'=>0, 'hotel'=>0),
            'view'      => array('api'=>1, 'user'=>0, 'hotel'=>0),
        ),
        'user'  => array(
            'login'     => array('api'=>0, 'user'=>1, 'hotel'=>0, 'untoken'=>1),
            'info'      => array('api'=>0, 'user'=>1, 'hotel'=>0),
        ),
        'picture'  => array(
            'gallery_update'  => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'gallery'         => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'gallery_hotel'   => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'recently'        => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'search'          => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'queue'           => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'unbind'          => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'bind'            => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'type_update'     => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'type'            => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'tags'            => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'upload'          => array('api'=>0, 'user'=>1, 'hotel'=>1),
            'load'            => array('api'=>0, 'user'=>1, 'hotel'=>1),
            'edit'            => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'edit_multi'      => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'delete'          => array('api'=>0, 'user'=>1, 'hotel'=>0),
        ),
        'around'    => array(
            'create'           => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'search'           => array('api'=>0, 'user'=>1, 'hotel'=>'hotel_id'),
            'update'           => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'delete'           => array('api'=>0, 'user'=>1, 'hotel'=>0),
            'type'             => array('api'=>0, 'user'=>1, 'hotel'=>0),
        ),
    );

    /**
     * get call functions' args
     +-----------------------------------------
     * @access public
     * @param mixed $class
     * @param mixed $function
     * @return void
     */
    static function __args($class, $function)
    {
        if (!isset(self::$func[$class]) || !isset(self::$func[$class][$function]))
            self::format(NULL, '404', self::$error_msg['404']);

        return self::$func[$class][$function];
    }



    /**
     * verify account
     +-----------------------------------------
     * @access protected
     * @param string $appid
     * @return void
     */
    protected static function __account($appid)
    {
        $db = db(config('db'));
        $sql = "SELECT a.*, b.`name` AS `orgname` FROM `ptc_api_account` AS a LEFT JOIN `ptc_org` AS b ON a.`org` = b.`id` WHERE `appid`=:appid;";
        $account = $db -> prepare($sql) -> execute(array(':appid'=>trim($appid)));

        if (!$account)
            self::format(NULL, '409', self::$error_msg['409']);

        // recode account
        self::$account = $account[0]['id'];
        self::$org = $account[0]['org'];
        self::$orgname = $account[0]['orgname'];

        return $account[0];
    }




    /**
     * get normal request from POST or GET
     +-----------------------------------------
     * @access public
     * @return void
     */
    static function request()
    {
        // parse request method
        $method = trim($_REQUEST['method']);
        self::$format = empty($_REQUEST['format']) ? 'json' : trim($_REQUEST['format']);
        list($class, $func) = explode('_', $method, 2);

        // order method must use POST
        if ($class == 'order')
        {
            $args = $_request = $_POST;
            if (!$args)
                self::format(NULL, '419', self::$error_msg['419']);
        }
        else
        {
            $args = $_request = $_REQUEST;
        }

        if (!empty($_request['page']))
            $_GET['page'] = self::$page = (int)$_request['page'];


        // unset base data
        unset($args['method'], $args['format'], $args['appid'], $args['secret'], $args['page'], $args['callback'], $args['token']);

        // parse method args
        $params = array();
        $func_args = self::__args($class, $func);
        if (is_null($func_args))
            $_GET['page'] = self::format(NULL, '404', self::$error_msg['404']);

        $limit = self::$func_limit[$class][$func];

        if ($limit['api'] && isset($_request['appid']))
        {
            // verify api account
            $account = self::__account($_request['appid']);

            // verify secret code
            ksort($args, SORT_STRING);
            $request = '';
            foreach ($args as $k => $v)
            {
                if (is_string($v)) $request .= "{$k}{$v}";
                else $request .= $k.serialize($v);
            }

            if (md5($request.$account['appsecret']) != $_request['secret'])
                self::format(NULL, '410', self::$error_msg['410']);
        }
        else
        {
            // verify user
            if (empty($limit['untoken']))
            {
                if ($_request['token'][0] == 'U')
                {
                    $user = user::verify($_request['token']);
                }
                else if ($_request['token'][0] == 'H')
                {
                    $user = hotel::verify($_request['token']);

                    if (!$limit['hotel'])
                        self::format(NULL, '404', self::$error_msg['404']);
                    else if ($limit['hotel'] !== 1)
                        $args[$limit['hotel']] = $user['id'];
                }

                if (!$user)
                    self::format(NULL, '411', self::$error_msg['411']);

                $_SESSION['uid']  = $user['id'];
                $_SESSION['name'] = $user['name'];
            }
        }


        foreach ($func_args as $k => $v)
        {
            $params[] = !isset($args[$k]) ? $v : $args[$k];
        }

        // call method
        $result = call_user_func_array(array($class, $func), $params);

        if ($result)
        {
            self::format($result, 0, '');
        }
        else
        {
            $error = $class::get_error();
            self::log($method, $args, $error);
            self::format(NULL, $error['code'], $error['msg']);
        }
    }



    /**
     * format data for return
     +-----------------------------------------
     * @access public
     * @param mixed $data
     * @param int $status
     * @param string $error
     * @return void
     */
    static function format($data, $status=0, $error='')
    {
        if (self::$format == 'xml')
        {
            header("Content-type:text/xml; charset=utf-8");
            $xml = false;
            echo build_xml(array('data' => $data, 'code' => $status, 'message' => $error), 1, $xml, 'response');
        }
        else if (self::$format == 'jsonp')
        {
            $callback = $_REQUEST['callback'];
            echo $callback,'(',json_encode(array('data' => $data, 'code' => $status, 'message' => $error), JSON_UNESCAPED_UNICODE),');';
        }
        else
        {
            echo json_encode(array('data' => $data, 'code' => $status, 'message' => $error), JSON_UNESCAPED_UNICODE);
        }
        exit;
    }



    /**
     * get error message
     +-----------------------------------------
     * @access public
     * @param int $code
     * @return void
     */
    static public function get_error($code=null)
    {
        if ($code === null) $code = self::$error;

        if (!empty(static::$error_msg[$code]))
            return array('code'=>$code, 'msg'=>static::$error_msg[$code]);

        if (!empty(self::$error_msg[$code]))
            return array('code'=>$code, 'msg'=>self::$error_msg[$code]);

        if ($code >= 500 && $code < 600)
            return array('code'=>$code, 'msg'=>'数据操作异常导致失败');

        return array('code'=>$code, 'msg'=>'undefined');
    }



    /**
     * set error code
     +-----------------------------------------
     * @access public
     * @param int $code
     * @return void
     */
    static public function set_error($code)
    {
        self::$error = $code;
    }




    /**
     * log
     +-----------------------------------------
     * @access protected
     * @param string $method
     * @param array $args
     * @param array $error
     * @return void
     */
    static protected function log($method, $args, $error)
    {
        $log = array(
            ':org'      => (int)self::$org,
            ':method'   => (string)$method,
            ':args'     => (string)json_encode($args, JSON_UNESCAPED_UNICODE),
            ':error'    => (string)json_encode($error, JSON_UNESCAPED_UNICODE),
        );

        $db = db(config('db'));
        $rs = $db -> prepare("INSERT INTO `ptc_api_log` (`org`, `method`, `args`, `error`) VALUES (:org, :method, :args, :error);") -> execute($log);

        return $rs ? true : false;
    }



    /**
     * push list
     +-----------------------------------------
     * @access public
     * @param mixed $type
     * @param mixed $id
     * @param string $payment
     * @param boolen $autopush
     * @return void
     */
    static public function push($type, $id, $payment='', $orgs=null, $autopush=true)
    {
        $db = db(config('db'));

        if ($orgs)
            $where = " WHERE `org` IN ({$orgs})";

        $accounts = $db -> prepare("SELECT `org`, `push` FROM `ptc_api_account` {$where};") -> execute();
        $list = array();
        foreach ($accounts as $v)
        {
            if (!$v['push']) continue;

            $list[] = array(
                'org'   => $v['org'],
                'url'   => str_replace(array('{$type}', '{$id}', '{$payment}'), array($type, $id, $payment), $v['push']),
                'time'  => NOW,
                'times' => 0,
                'status'=> 0,
            );
        }

        list($column, $sql, $value) = array_values(insert_array($list));
        $rs = $db -> prepare("INSERT INTO `ptc_api_push` {$column} VALUES {$sql};") -> execute($value);

        if ($rs && $autopush)
        {
            curl_file_get_contents('http://121.199.13.135/push.php?id='.$rs, null, null, 1);
        }

        return $rs;
    }

}
