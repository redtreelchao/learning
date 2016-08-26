<?php
/**
 * 酒店信息检索及报价
 +-----------------------------------------
 * @category
 * @package hotel
 * @author nolan.zhou
 * @version $Id$f
 */
class hotel extends api
{

    // error message
    static public $error_msg = array(
        '701'   => '查询入住/离店日期不正确',
        '702'   => '关键词/城市必须包含其中一个参数',
        '704'   => '网页地址为空或不存在',
        '705'   => '地址不正确，无法解析',
        '706'   => '地址不符合解析规则',
        '707'   => '信息采集失败，请重试',
        '708'   => '提交内容不正确',
        '710'   => '酒店账户密码错误或不存在',
        '711'   => '电话号码格式不正确',
        '712'   => '坐标信息不正确',
        '713'   => '已存在相似酒店数据，请检查并确认',
        '714'   => '未提交酒店ID',
        '715'   => '酒店信息不存在',
        //'716'   => 'pms信息必填',
        '717'   => '关联周边不存在',
        '718'   => '已存在绑定的周边信息'
    );



    // 用户登录
    static function login($username, $password)
    {
        $db = db(config('db'));

        $hid = chr2dec(substr($username, 2));

        $sql = "SELECT `id`, `name`, `password` FROM `ptc_hotel` WHERE `id`=:id";
        $user = $db -> prepare($sql) -> execute(array(':id'=>$hid));

        if (!$user || $user[0]['password'] != md5($password))
        {
            return !self::$error = '710';
        }
        else
        {
            $user = $user[0];

            // token
            $user['token'] = 'H'.$user['id'].'M'.authcode($user['password'], 'PtkHotel', 'ENCODE');
            unset($user['password']);

            return $user;
        }
    }
    // login


    /**
     * verify account
     +-----------------------------------------
     * @access protected
     * @param string $token
     * @return void
     */
    static function verify($token)
    {
        $token = str_replace(' ', '+', $token);

        $pos = strpos($token, 'M');
        $uid = substr($token, 1, $pos-1);
        $token = substr($token, $pos+1);

        if (!$uid || !$token)
            self::format(NULL, '411', parent::$error_msg['411']);

        $db = db(config('db'));

        $user = $db -> prepare("SELECT `id`, `name`, `password` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$uid));
        if (!$user)
            self::format(NULL, '409', parent::$error_msg['409']);

        $mdstr = authcode($token, 'PtkHotel', 'DECODE');

        //if (substr($mdstr, -10) != $user[0]['lastlogin'])
          //  self::format(NULL, '413', parent::$error_msg['413']);

        if ($mdstr != $user[0]['password'])
            self::format(NULL, '411', parent::$error_msg['411']);

        return $user[0];
    }
    // verify



    /**
     * @param $hotel_id
     * @param int $limit
     * @return array|bool 返回与当前酒店绑定的周边列表
     *
     */
    static function around_list($hotel_id, $limit = 10)
    {
        if (!$hotel_id)
            return !self::$error = '714';
        $condition = [
            ':hotel_id' => $hotel_id
        ];

        $count_sql = "SELECT
                        COUNT(*)
                        FROM `ptc_hotel_around` AS b
                            LEFT JOIN `ptc_around` AS a ON a.`id` = b.`around_id`
                            LEFT JOIN `ptc_hotel` AS h ON h.`id` = b.`hotel_id`
                        WHERE
                            h.`id` IS NOT NULL
                        AND a.`id` IS NOT NULL
                        AND b.`hotel_id` = :hotel_id;";
        $sql = "SELECT
                    b.`hotel_id` AS hotel_id,
                    b.`around_id`,
                    a.`name`,
                    a.`en`,
                    a.`type`,
                    ROUND(6378.138 * 2 * asin(sqrt(pow(sin((a.`lat` * pi() / 180 - h.`lat` * pi() / 180) / 2),2) + cos(a.`lat` * pi() / 180) * cos(h.`lat` * pi() / 180) * pow(sin((a.`lng` * pi() / 180 - h.`lng` * pi() / 180) / 2),2))) * 1000) AS distance
                FROM `ptc_hotel_around` AS b
                    LEFT JOIN `ptc_around` AS a ON a.`id` = b.`around_id`
                    LEFT JOIN `ptc_hotel` AS h ON h.`id` = b.`hotel_id`
                WHERE
                    h.`id` IS NOT NULL
                AND a.`id` IS NOT NULL
                AND b.`hotel_id` = :hotel_id
                ORDER BY
                    distance ASC,
                    a.`id` ASC ";

        $db = db(config('db'));

        $count = $db->prepare($count_sql)->execute($condition);

        $_GET['page'] = self::$page;
        $page = new page($count[0]['c'], $limit);
        $limit = $page -> limit();
        $sql = $sql." LIMIT {$limit}";
        $list = $db -> prepare($sql) -> execute($condition);
        return array('page'=>$page->show(), 'list'=>$list);

    }


    //酒店绑定周边
    static function around_bind($hotel_id = 0, $around_ids = 0)
    {
        $hotel_id = intval($hotel_id);
        if(!$hotel_id)
            return !self::$error = '714';

        $bind_time = NOW;
        if(is_array($around_ids))
        {
            $around_ids = array_filter($around_ids);
            $history_data = implode(',', $around_ids);
            $arounds = "($hotel_id,".implode(" ,$bind_time), ($hotel_id,", $around_ids).",$bind_time)";
            $sql = 'INSERT INTO `ptc_hotel_around` (`hotel_id`, `around_id`, `bind_time`) VALUES '.$arounds;

        }else{
            $history_data = $around_ids = intval($around_ids);

            $sql = "INSERT INTO `ptc_hotel_around` (`hotel_id`, `around_id`, `bind_time`) VALUES ($hotel_id, $around_ids, $bind_time)";
        }
        $db = db(config('db'));
        $db -> beginTrans();
        $rs = $db->prepare($sql)->execute();
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }
        if(!history($hotel_id, 'hotel', '关联酒店周边', [$history_data, $bind_time]))
        {
            $db -> rollback();
            return false;
        }

        if ($db -> commit())
        {
            return true;
        }
        else
        {
            $db -> rollback();
            return false;
        }


    }

    /**
     * @param int $hotel_id
     * @param int $around_id
     * @return bool
     * 解除绑定
     */
    static function around_unbind($hotel_id = 0, $around_id = 0)
    {
        $sql = "SELECT `id` FROM `ptc_hotel_around`  WHERE `hotel_id` = :hotel_id AND `around_id` = :around_id;";

        $condtions = [
            ':hotel_id' => $hotel_id,
            ':around_id' => $around_id
        ];

        $db = db(config('db'));
        $rs = $db -> prepare($sql) -> execute($condtions);

        if($rs === false || empty($rs)){
            return !self::$error = '717';
        }

        $val = delete('ptc_hotel_around', '`id` = '.$rs[0]['id'] );
        return $val;
    }


    /**
     * collection ELONG's hotel data
     +-----------------------------------------
     * @access public
     * @param  string $url
     * @return void
     */
    static function link($url)
    {
        if (!trim($url))
            return !self::$error = '704';

        set_time_limit(0);

        $parse = parse_url($url);
        if (empty($parse['host']))
            return !self::$error = '705';

        switch ($parse['host'])
        {
            case 'hotel.elong.com':
                $path = explode('/', $parse['path']);
                $code = $path[2];
                break;

            case 'globalhotel.elong.com':
                $code = substr($parse['path'], 8, -5);
                break;

            default:
                return !self::$error = '706';
        }

        if (!is_numeric($code))
            return !self::$error = '705';
/*
        $db = db(config('db'));

        // try to load new data
        $hotel = $db -> prepare("SELECT `name`, `address`, `tel` FROM `sup_elg_hotel` WHERE `id`=:code") -> execute(array(':code'=>$code));
        if ($hotel)
        {
            $hotel[0]['elg'] = $code;
            return $hotel[0];
        }

        // fail to collection data from url
        else
        {
*/
            $func = str_replace('.', '_', $parse['host']);
            $data = collection::$func($url);
            if ($data)
            {
                $data['elg'] = $code;
                return $data;
            }
            else
            {
                return !self::$error = '707';
            }
/*
        }
*/
    }
    // link




    /**
     * Hotel types
     +-----------------------------------------
     * @access public
     * @return void
     */
    static function types()
    {
        return array(
            array('id'=>1, 'name'=>'高端精品'),
            array('id'=>2, 'name'=>'高端民宿'),
            array('id'=>3, 'name'=>'普通民宿'),
            array('id'=>4, 'name'=>'房车'),
            array('id'=>5, 'name'=>'树屋'),
            array('id'=>99, 'name'=>'其他'),
        );
    }
    // types


    static function type_name($id){
        foreach (self::types() as $k => $v){
            if($id == $v['id'])
            {
                return $v['name'];
            }
        }
        return false;
    }




        /**
     * Load Hotel
     +-----------------------------------------
     * @access public
     * @param mixed $id
     * @return void
     */
    static public function load($id)
    {
        if (!$id)
            return !self::$error = '714';

        $db = db(config('db'));

        $sql = "SELECT h.*, e.*, d1.`name` AS  'country_name', d2.`name` AS  'city_name', d3.`name` AS  'district_name'
                FROM `ptc_hotel` AS h
                    LEFT JOIN `ptc_hotel_ext` AS e ON h.`id`=e.`id`
                    LEFT JOIN ptc_district AS d1 ON h.`country`=d1.`id`
                    LEFT JOIN ptc_district AS d2 ON h.`city`=d2.`id`
                    LEFT JOIN ptc_district_ext AS d3 ON h.`district`=d3.`id`
                WHERE h.`id`=:hotel";
        $hotel = $db -> prepare($sql) -> execute(array(':hotel'=>$id));
        if (!$hotel)
            return !self::$error = '715';

        $hotel = $hotel[0];

        $policies = explode('¶', $hotel['policies']);
        $hotel['policies'] = array();
        foreach ($policies as $k => $v)
        {
            list($name, $content) = explode('¦', $v, 2);
            $hotel['policies'][] = array('name'=>$name, 'content'=>$content);
        }

        $edges = explode('¶', $hotel['edges']);
        $hotel['edges'] = array();
        foreach ($edges as $k => $v)
        {
            list($name, $content) = explode('¦', $v, 2);
            $hotel['edges'][] = array('name'=>$name, 'content'=>$content);
        }


        unset($hotel['roomsummary']);

        $sql = "SELECT t.`id`, t.`name`, t.`type`
                FROM `ptc_tag_rel` AS r
                    LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                WHERE r.`objtype`='hotel' AND r.`objid`=:id AND t.`type` IN ('design','crowd','atmosphere','characteristic')
                GROUP BY r.`tag`";
        $hotel['tags'] = $db -> prepare($sql) -> execute(array(':id'=>$id));

        return $hotel;
    }
    // load


    static public function ext( $hotel_id ) {

        $detail = ['amenity'=>[],'around'=>[],'ext'=>[]];

        if ( is_numeric($hotel_id) ) {

            $db = db(config('db'));

            $detail['amenity'] = $db -> prepare("SELECT `name`,`type` FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel and `type` = 'amenity' and `checked` = 1 ORDER BY `id` ASC;") -> execute(array(':hotel'=>$hotel_id));

            if (!is_array($detail['amenity'])) $detail['amenity'] = [];

            $sql = "SELECT
                        a.`name`,
                        ROUND(6378.138 * 2 * asin(sqrt(pow(sin((a.`lat` * pi() / 180 - h.`lat` * pi() / 180) / 2),2) + cos(a.`lat` * pi() / 180) * cos(h.`lat` * pi() / 180) * pow(sin((a.`lng` * pi() / 180 - h.`lng` * pi() / 180) / 2),2))) * 1000) AS distance
                    FROM `ptc_hotel_around` AS b
                        LEFT JOIN `ptc_around` AS a ON a.`id` = b.`around_id`
                        LEFT JOIN `ptc_hotel` AS h ON h.`id` = b.`hotel_id`
                    WHERE
                        h.`id` IS NOT NULL
                    AND a.`id` IS NOT NULL
                    AND b.`hotel_id` = :hotel_id
                    ORDER BY
                        distance ASC,
                        a.`id` ASC ";

            $detail['around'] = $db -> prepare($sql) -> execute([':hotel_id' => $hotel_id]);

            if (!is_array($detail['amenity'])) $detail['around'] = [];

            $detail['ext'] = $db -> prepare('select `opening`, `redecorate`, `roomnum` from `ptc_hotel_ext` where `id` = :hotel') -> execute(array(':hotel'=>$hotel_id));

            if (!is_array($detail['ext'])) $detail['ext'] = [];

        }

        return $detail;
    }

    /**
     * Create hotel
     +-----------------------------------------
     * @access public
     * @param  string $name
     * @param  string $en
     * @param  string $pms
     * @param  string $country
     * @param  string $city
     * @param  string $address
     * @param  string $tel
     * @param  string $lng
     * @param  string $lat
     * @param  int    $type
     * @param  int    $star
     * @param  int    $roomnum
     * @param  string $opening
     * @param  string $redecorate
     * @param  string $bland
     * @param  string $checkin
     * @param  string $checkout
     * @param  array  $policy
     * @param  string $intro
     * @param  array  $edges
     * @param  array  $tags
     * @param  int    $status
     * @return void
     */
    static function create( $name, $en=null, $country, $city, $district, $address, $tel, $lng, $lat, $pms=null,
                            $type=null, $star=0, $roomnum, $opening, $redecorate, $brand='',
                            $checkin, $checkout, $policies=array(), $intro, $edges=array(),
                            $tags=null, $status=1, $id=null )
    {
        $name = str_replace(array('（', '）'), array('(', ')'), $name);

        $args = document::$func['hotel']['create']['args'];
        foreach ($args as $k => $v)
        {
            if (!$$k && !in_array($k, array('district','type','en', 'pms', 'star', 'bland', 'policies', 'edges', 'tags', 'status', 'id')))
            {
                $name = substr($v, 0, strpos($v, '，'));
                self::$error_msg['708_'.$k] = $k.'_'.$v.self::$error_msg['708'];
                return !self::$error = '708_'.$k;
            }
        }

        $data = array(
            'name'      => trim($name),
            'pinyin'    => pinyin::get($name),
            'pms'       => trim($pms),
            'en'        => trim($en),
            'type'      => (int)$type,
            'star'      => (int)$star,
            'country'   => (int)$country,
            'city'      => (int)$city,
            'district'  => (int)$district,
            'tel'       => (string)$tel,
            'address'   => (string)$address,
            'lng'       => (string)$lng,
            'lat'       => (string)$lat,
            'status'    => (int)$status,
            'updatetime'=> NOW,
        );

        import(CLASS_PATH.'extend/string');

        // 其他政策
        $_policy = array();
        foreach ($policies as $v)
        {
            if (!$v['name'] || !$v['content']) continue;
            $_policy[] = string::text($v['name'], 6).'¦'.string::text($v['content'], 200);
            //if (count($_policy) == 4) break;
        }

        // 亮点
        $_edge = array();
        foreach ($edges as $v)
        {
            if (!$v['name'] || !$v['content']) continue;
            $_edge[] = string::text($v['name'], 12).'¦'.string::text($v['content'], 200);
            //if (count($_edge) == 6) break;
        }



        $ext = array(
            'brand'     => (string)$brand,
            'roomnum'   => (int)$roomnum,
            'opening'   => (int)$opening,
            'redecorate'=> (int)$redecorate,
            'checkin'   => date('H:i', strtotime('today '.$checkin)),
            'checkout'  => date('H:i', strtotime('today '.$checkout)),
            'policies'  => implode('¶', $_policy),
            'intro'     => trim($intro),
            'edges'     => implode('¶', $_edge),
        );

        if (!$ext['checkin'] || !$ext['checkout'])
        {
            self::$error_msg['708_checkdata'] = '入住离店时间' . self::$error_msg['708'];
            return !self::$error = '708_checkdata';
        }

        $db = db(config('db'));

        if ($data['tel'] && !string::check($data['tel'], 'phone'))
            return !self::$error = '711';

        if (!string::check($data['lat'], 'double') || !string::check($data['lng'], 'double'))
            return !self::$error = '712';

        // Check have the same data
        $check = $db -> prepare("SELECT `id` FROM `ptc_hotel_original` WHERE (`name`=:name AND `address`=:address)".($id ? " AND `id`!='{$id}'" : ''))
                     -> execute(array(':name'=>$data['name'], ':address'=>$data['address']));
        if ($check)
            return !self::$error = '713';

        $check = $db -> prepare("SELECT `id` FROM `ptc_hotel` WHERE (`name`=:name AND `address`=:address)".($id ? " AND `id`!='{$id}'" : ''))
                     -> execute(array(':name'=>$data['name'], ':address'=>$data['address']));
        if ($check)
            return !self::$error = '713';

        $db -> beginTrans();

        if ($id)
        {
            $id = (int)$id;

            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = $id;
            $rs = $db -> prepare("UPDATE `ptc_hotel` SET {$sql} WHERE `id`=:id;") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = '501';
            }

            // History
            if (!history($id, 'hotel', '修改了酒店信息', array_merge($data, $ext)))
            {
                $db -> rollback();
                return !self::$error = '502';
            }

            // Save extend data
            $update_column = update_column(array_keys($ext));
            $ext['id'] = $id;
            list($column, $sql, $value) = array_values(insert_array($ext));
            $ers = $db -> prepare("INSERT INTO `ptc_hotel_ext` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE {$update_column};") -> execute($value);
            if (false === $ers)
            {
                $db -> rollback();
                return !self::$error = '503';
            }

            // Products about hotel, linkage updating
            $prs = $db -> prepare("UPDATE `ptc_product_item` SET `target`=:city WHERE `objtype`='room' AND `objpid`=:id;") -> execute(array(':city'=>$data['city'], ':id'=>$id));
            if (false === $prs)
            {
                $db -> rollback();
                return !self::$error = '504';
            }

            // Push api log
            api::push('hotel', $id, '');
        }
        else
        {
            $data['creator'] = (int)$_SESSION['uid'];
            $data['createtime'] = NOW;
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel` {$column} VALUES {$sql};") -> execute($value);
            if (!$rs)
            {
                $db -> rollback();
                return !self::$error = '511';
            }

            $id = $rs;

            // History
            if (!history($id, 'hotel', '创建了酒店', array_merge($data, $ext)))
            {
                $db -> rollback();
                return !self::$error = '512';
            }

            // Add original data
            $data = array(':id'=>$id, ':name'=>$data['name'], ':address'=>$data['address'], ':tel'=>$data['tel']);
            $ors = $db -> prepare("INSERT INTO `ptc_hotel_original` (`id`,`name`,`address`,`tel`) VALUES (:id, :name, :address, :tel);") -> execute($data);
            if ($ors === false)
            {
                $db -> rollback();
                return !self::$error = '513';
            }

            // Save extend data
            $update_column = update_column(array_keys($ext));
            $ext['id'] = $id;
            list($column, $sql, $value) = array_values(insert_array($ext));
            $ers = $db -> prepare("INSERT INTO `ptc_hotel_ext` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE {$update_column};") -> execute($value);
            if (false === $ers)
            {
                $db -> rollback();
                return !self::$error = '514';
            }

            // Push api log
            api::push('hotel', $id, '');
        }

        // Bind tags
        if (!is_null($tags))
        {
            $sql = "DELETE r.* FROM `ptc_tag_rel` AS r
                        LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                    WHERE t.`type` IN ('design','crowd','atmosphere','characteristic') AND r.`objtype`='hotel' AND r.`objid`=:id;";
            $rs = $db -> prepare($sql) -> execute(array(':id'=>$id));
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = '515';
            }

            $_tags = array();
            foreach ($tags as $v)
            {
                if (!(int)$v) continue;
                $_tags[] = array('tag'=>(int)$v, 'objtype'=>'hotel', 'objid'=>$id, 'value'=>'');
            }

            if ($_tags)
            {
                list($column, $sql, $value) = array_values(insert_array($_tags));
                $rs = $db -> prepare("INSERT INTO `ptc_tag_rel` {$column} VALUES {$sql};") -> execute($value);
                if (false === $rs)
                {
                    $db -> rollback();
                    return !self::$error = '516';
                }
            }
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = '599';
        }

        return $id;
    }
    // create



    /*
     * Update hotel
     +-----------------------------------------
     * @access public
     */
    static function update( $id, $name, $pms, $en='', $country, $city, $district, $address, $tel, $lng, $lat,
                            $type='', $star=0, $roomnum, $opening, $redecorate, $bland='',
                            $checkin, $checkout, $policies=array(), $intro, $edges=array(),
                            $tags=null,$status=1 )
    {
        if (!$id)
            return !self::$error = '714';

        return self::create( $name,$pms, $en, $country, $city, $district, $address, $tel, $lng, $lat,
                             $type, $star, $roomnum, $opening, $redecorate, $bland,
                             $checkin, $checkout, $policies, $intro, $edges,
                             $tags,$status, $id);
    }
    // update









    /**
     * search hotel and price
     +-----------------------------------------
     * @access public
     * @param mixed $checkin
     * @param mixed $checkout
     * @param mixed $city
     * @param string $keyword
     * @param int $star
     * @param int $min_price
     * @param int $max_price
     * @param int $limit
     * @return void
     */
    static public function search($country='', $province='', $city='', $id='', $name='', $en='', $brand='', $type='', $star='', $limit=10, $export = false)
    {
        $db = db(config('db'));

        $where = '1=1';
        $condition = array();

        if ($country)
        {
            $where .= ' AND d.`name` LIKE :country';
            $condition[':country'] = $country.'%';
        }

        if ($province)
        {
            $where .= ' AND c.`province` LIKE :province';
            $condition[':province'] = $province.'%';
        }

        if ($city)
        {
            $where .= ' AND c.`name` LIKE :city';
            $condition[':city'] = $city.'%';
        }

        if ($id)
        {
            $where .= ' AND a.`id`=:id';
            $condition[':id'] = (int)$id;
        }

        if ($name)
        {
            $where .= ' AND a.`name` LIKE :name';
            $condition[':name'] = '%'.trim($name).'%';
        }

        if ($en)
        {
            $where .= ' AND a.`en` LIKE :en';
            $condition[':en'] = '%'.trim($en).'%';
        }

        if ($brand)
        {
            $where .= ' AND b.`brand`=:brand';
            $condition[':brand'] = trim($brand);
        }

        if ($type)
        {
            $where .= ' AND a.`type`=:type';
            $condition[':type'] = (int)$type;
        }

        if ($star)
        {
            $where .= ' AND a.`star`=:star';
            $condition[':star'] = (int)$star;
        }

        $db = db(config('db'));

        $sql = "SELECT COUNT(*) AS c
                FROM `ptc_hotel` AS a
                    LEFT JOIN `ptc_district` AS c ON c.`id` = a.`city`
                    LEFT JOIN `ptc_district` AS d ON d.`id` = a.`country`
                    LEFT JOIN `ptc_hotel_ext` AS b ON a.`id` = b.`id`
                WHERE {$where};";
        $count = $db -> prepare($sql) -> execute($condition);

        $sql = "SELECT a.`id`, a.`name`, a.`en`, a.`address`, a.`country` AS `countryid`, d.`name` AS `country`, c.`province`, a.`city` AS `cityid`, c.`name` AS `city`, a.`type`, a.`star`, a.`status`, b.`brand`
                FROM `ptc_hotel` AS a
                    LEFT JOIN `ptc_district` AS c ON c.`id` = a.`city`
                    LEFT JOIN `ptc_district` AS d ON d.`id` = a.`country`
                    LEFT JOIN `ptc_hotel_ext` AS b ON a.`id` = b.`id`
                WHERE {$where}
                ORDER BY a.`id` DESC
                ";
        //导出操作
        if($export === true){
            $list = $db -> prepare($sql) -> execute($condition);

            //var_export($list);die;

            include_once CLASS_PATH.'PHPExcel.php';
            $objExcel = new PHPExcel();

            $objProps = $objExcel -> getProperties();
            $objProps -> setCreator("PUTIKE.CN");
            $objProps -> setTitle("璞缇客酒店数据导出，仅供内部使用");
            $objExcel -> setActiveSheetIndex(0);
            $objActSheet = $objExcel -> getActiveSheet();
            $objActSheet -> setTitle('璞缇客酒店数据导出('.date('YmdHis',NOW).')');

            $defaultCss = $objActSheet -> getDefaultStyle();
            $defaultCss -> getFont() -> setSize(10);

            // Default Style
            $objActSheet -> getDefaultRowDimension() -> setRowHeight(18);

            $column_names = array(
                array('column'=>'country', 'name'  => '国家', 'width' => 15, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'province', 'name'  => '所在省', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'city', 'name'  => '所在市', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'id', 'name'  => '酒店ID', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'name', 'name'  => '酒店中文名', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'en', 'name'  => '酒店英文名', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'brand', 'name'  => '所属品牌', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'type', 'name'  => '酒店类型', 'width' => 5, 'bg'   =>   null, 'type'  => ''),
                array('column'=>'star', 'name'  => '酒店星级', 'width' => 5, 'bg'   =>   null, 'type'  => '')
            );

// Columns
            $i = 0;
            $field = array();

            foreach ($column_names as $k => $v)
            {
                $code = (floor($i / 26) ? chr(64 + floor($i / 26)) : '') . chr(65 + $i % 26);
                //$objActSheet -> getColumnDimension($code) -> setWidth($v['width']);
                $objActSheet -> getColumnDimension($code) -> setAutoSize(true);
                $objActSheet -> getRowDimension(1) -> setRowHeight(18);
                //$objActSheet -> getStyle("{$code}1") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) -> setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setWrapText(true);
                $objActSheet -> getStyle("{$code}1") -> getFont() -> setBold(true);

                // Style
                $style = $objActSheet -> getStyle("{$code}1");
                $style -> getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('CCCCCCC');

                $objActSheet -> setCellValue("{$code}1", $v['name']);
                $field[$k] = array(
                    'code'  => $code,
                    'column'    => $v['column']
                );
                $i ++;
            }
            unset($k);
            unset($v);

            foreach ($list as $key => $value)
            {
                foreach ($field as $k => $v )
                {
                    if($v['column'] == 'type')
                    {
                        $export_value = self::type_name($value[$v['column']]);
                        $export_value = $export_value ? $export_value : '';
                    }else{
                        $export_value = $value[$v['column']];
                    }

                    $objActSheet -> setCellValue($v['code'].($key+2), $export_value);

                }

            }

            $path_name = 'putike_hotel_export';
            if(!is_dir($path_name))
            {
                mkdir($path_name);
            }

            $file_name = "putike_hotel_".date('YmdHis').'_'.uniqid().".xlsx";


            /*header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition:inline;filename=\"putike_hotel_".date('YmdHis').".xlsx\"");
            header("Content-Transfer-Encoding: binary");
            header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");*/
            $objWriter = new PHPExcel_Writer_Excel2007($objExcel);
            //$objWriter -> save('php://output');
            $objWriter -> save($path_name.DIRECTORY_SEPARATOR.$file_name);
            return $path_name.'/'.$file_name;
            exit;



        }
        else //常规查询业务
        {
            $_GET['page'] = self::$page;
            $page = new page($count[0]['c'], $limit);
            $limit = $page -> limit();
            $sql = $sql." LIMIT {$limit}";
            $list = $db -> prepare($sql) -> execute($condition);
            return array('page'=>$page->show(), 'list'=>$list);

        }

        /*$_GET['page'] = self::$page;
        $page = new page($count[0]['c'], $limit);
        $limit = $page -> limit();

        $sql = "SELECT a.`id`, a.`name`, a.`en`, a.`address`, a.`country` AS `countryid`, d.`name` AS `country`, c.`province`, a.`city` AS `cityid`, c.`name` AS `city`, a.`type`, a.`star`, a.`status`, b.`brand`
                FROM `ptc_hotel` AS a
                    LEFT JOIN `ptc_district` AS c ON c.`id` = a.`city`
                    LEFT JOIN `ptc_district` AS d ON d.`id` = a.`country`
                    LEFT JOIN `ptc_hotel_ext` AS b ON a.`id` = b.`id`
                WHERE {$where}
                ORDER BY a.`id` DESC
                LIMIT {$limit}";

        $list = $db -> prepare($sql) -> execute($condition);

        return array('page'=>$page->show(), 'list'=>$list);*/
    }
    // search




    /**
     * all hotels simple information
     +-----------------------------------------
     * @access public
     * @param int $country
     * @param int $city
     * @return void
     */
    static public function all($country=0, $city=0)
    {
        $db = db(config('db'));

        $where = '1=1';
        $condition = array();

        if ($country)
        {
            $where .= ' AND h.`country` = :country';
            $condition[':country'] = $country;
        }

        if ($city)
        {
            $sql .= " AND h.`city`=:city";
            $condition[':city'] = $city;
        }

        $sql = "SELECT h.`id`, h.`name`, h.`pinyin`,h.`pms`, h.`en` AS `english`, h.`star`, h.`country`, h.`city`, h.`district`, h.`address`, h.`tel`, h.`lng`, h.`lat`, h.`updatetime`,
                        r.`id` AS `room`, r.`name` AS `roomname`
                FROM `ptc_hotel` AS h
                    LEFT JOIN `ptc_hotel_room_type` AS r ON r.`hotel` = h.`id`
                WHERE {$where}
                ORDER BY h.`id` ASC";
        $list = $db -> prepare($sql) -> execute($condition);

        $hotels = array();
        $id = 0;
        foreach ($list as $k => $v)
        {
            if ($id != $v['id'])
            {
                $hotels[$v['id']] = array(
                    'id'        => $v['id'],
                    'name'      => $v['name'],
                    'pinyin'    => $v['pinyin'],
                    'pms'       => $v['pms'],
                    'star'      => $v['star'],
                    'country'   => $v['country'],
                    'city'      => $v['city'],
                    'district'  => $v['district'],
                    'address'   => $v['address'],
                    'tel'       => $v['tel'],
                    'lng'       => $v['lng'],
                    'lat'       => $v['lat'],
                    'rooms'     => array(),
                );
                $id = $v['id'];
            }

            $hotels[$v['id']]['rooms'][] = array(
                'id'    => $v['room'],
                'name'  => roomname($v['roomname'], 2),
            );
        }

        return array('count'=>count($hotels), 'hotels'=>$hotels);
    }
    // all




    /**
     * prepay product min price
     +-----------------------------------------
     * @access public
     * @param mixed $checkin
     * @param mixed $checkout
     * @param string $hotels
     * @return void
     */
    public static function prepaymin($checkin, $checkout, $hotels='')
    {
        $checkin = strtotime($checkin);
        $checkout = strtotime($checkout);
        if (!$checkin || !$checkout)
        {
            self::$error = 701;
            return false;
        }

        $where = '';
        if ($hotels)
        {
            $hotels = array_filter(array_map('intval', explode(',', $hotels)));
            $where .= ' AND p.`hotel` IN ('.implode(',', $hotels).')';
        }

        $sql = "SELECT p.`hotel`, ROUND(MIN(p.`price` + IF( fp3.`profit` IS NULL,
                            IF( fp2.`profit` IS NULL,
                                IF( fp1.`type` = 'amount', fp1.`profit`, p.`price` * fp1.`profit` / 100),
                                IF( fp2.`type` = 'amount', fp2.`profit`, p.`price` * fp2.`profit` / 100)
                            ),
                            IF( fp3.`type` = 'amount', fp3.`profit`, p.`price` * fp3.`profit` / 100)
                        ))) AS `min`
                FROM `ptc_hotel_price_date` AS p
                    LEFT JOIN `ptc_org_profit` AS fp1 ON fp1.`org` = 1 AND fp1.`payment` = 'prepay' AND fp1.`objtype` = 'hotel' AND fp1.`objid` = 0
                    LEFT JOIN `ptc_org_profit` AS fp2 ON fp2.`org` = 1 AND fp2.`payment` = 'prepay' AND fp2.`objtype` = 'hotel' AND fp2.`objid` = p.`hotel`
                    LEFT JOIN `ptc_org_profit` AS fp3 ON fp3.`org` = 1 AND fp3.`payment` = 'prepay' AND fp3.`objtype` = 'room'  AND fp3.`objid` = p.`roomtype`
                WHERE p.`date` >= :checkin AND p.`date` <= :checkout AND p.`close`=0 {$where}
                GROUP BY p.`hotel`";

        $db = db(config('db'));
        $list = $db -> prepare($sql) -> execute(array(':checkin'=>$checkin, ':checkout'=>$checkout));

        return array('hotels' => $list);
    }
    // prepaymin

    // 酒店历史操作记录
    public static function history($id)
    {
        $sql = "SELECT `intro`, `username`, FROM_UNIXTIME(`time`) AS `time` FROM `ptc_history` WHERE `type` = 'hotel' AND `pk` = :id;";

        $db = db(config('db'));
        $history = $db -> prepare($sql) -> execute(array(':id'=>$id));

        return $history;
    }


}

?>
