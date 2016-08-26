<?php
/**
 * 艺龙供应商
 +-----------------------------------------
 * @category
 * @package elg
 * @author nolan.zhou
 * @version $Id$
 */
class elg extends supply implements supply_inf
{
    //全局网址
    private static $url = 'http://api.elong.com/rest';
    //private static $url = 'http://api.test.lohoo.com/rest'; // Test mode

    //验证信息
    public static $option = array('format'=>'json', 'user'=>'ae9684dc572bf4a1d24a14f48c933e59');

    public static $app_key = 'b84ad7cdc3d28c5ea5b69b98a04caa18';

    public static $secret_key = 'a9cb842dba42f50af9edc2e271fcf670';

    // 日志用名
    private static $log_name = '';



    // 发送请求
    public static function _request($method, $args=array(), $international=false)
    {
        ksort($args);

        if (!$international)
            $data = json_encode(array('Version'=>'1.13', 'Local'=>'zh_CN', 'Request'=>$args));
        else
            $data = json_encode($args);

        $sign = strtolower(md5(NOW . strtolower(md5($data . self::$app_key)) . self::$secret_key));

        $options = array_merge(array('method'=>$method, 'timestamp'=>NOW, 'data'=>$data, 'signature'=>$sign), self::$option);

        $jsonStr = curl_file_get_contents(self::$url . '?' . http_build_query($options));

        if (isset($_GET['debug'])) { header("Content-type:text/html; charset=utf-8"); echo self::$url . '?' . http_build_query($options); echo $jsonStr; exit; } //var_dump($jsonStr); exit;

        $json = json_decode($jsonStr, true);
        if (!$json)
        {
            self::error('获取数据异常：'.$jsonStr);
            return false;
        }

        unset($jsonStr);

        if ($json['Code'] > 0)
        {
            self::error('获取数据异常：'.$json['Code']);
            return false;
        }

        return $json['Result'];
    }
    // _request





    // _price
    static function _price($hotelcode, $roomcode='', $bedcode=null, $checkin=0, $checkout=0)
    {
        return null;
    }
    // price






    // price
    static function price($hotelcode, $roomcode='', $bedcode='', $checkin=0, $checkout=0)
    {
        if (!$checkin) $checkin = strtotime("today");
        if (!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." +27 day");
        return array();

        if ($checkin >= $checkout)
            return false;

        // Time change
        $checkin = date('Y-m-d', $checkin);
        $checkout = date('Y-m-d', $checkout);

        $condition = array('ArrivalDate'=>$checkin, 'DepartureDate'=>$checkout, 'HotelIds'=>$hotelcode, 'RoomTypeId'=>$roomcode, 'RatePlanId'=>0, 'PaymentType'=>'Prepay', 'Options'=>'2,5');

        $data = self::_request('hotel.detail', $condition);

        if (!$data['Count'])
            return array();

        $hotel = &$data['Hotels'][0];
        $hotelid = $hotel['HotelId'];

        $_addbf_code = array();
        $_bf_code = array();
        $_net_code = array();
        $_package_code = array();
        $_min_code = array();
        $_advance_code = array();

        // 附加服务
        $value_adds = &$hotel['ValueAdds'];
        foreach ($value_adds as $v)
        {
            $code = 'VA_'.$v['ValueAddId'];
            switch ($v['TypeCode'])
            {
                case '01':
                    if ($v['IsInclude'])
                    {
                        $_bf_code[$code] = array('num'=>$v['Amount']);
                        if ($v['IsExtAdd'] && $v['ExtOption'] == 'Money') $_addbf_code[$code] = array('price'=>$v['ExtPrice']);
                    }
                    else
                    {
                        if ($v['ExtOption'] == 'Money') $_addbf_code[$code] = array('price'=>$v['ExtPrice']);
                    }
                    break;

                case '02':
                    if ($v['IsInclude'])
                        $_package_code[$code] = array('name'=>'送午餐');
                    break;

                case '03':
                    if ($v['IsInclude'])
                        $_package_code[$code] = array('name'=>'送晚餐');
                    break;
            }
        }


        // 预订规则
        $drr_rules = &$hotel['DrrRules'];
        foreach ($drr_rules as $v)
        {
            $code = 'DR_'.$v['DrrRuleId'];
            switch ($v['TypeCode'])
            {
                case 'DRRBookAhead':              // 提前几天
                    $_advance_code[$code] = array('num'=>$v['DayNum'], 'type'=>$v['CashScale'], 'price'=>$v['DeductNum']);
                    break;

                case 'DRRStayPerRoomPerNight':    // 连住几天
                    $_min_code[$code] = array('num'=>$v['CheckInNum'], 'type'=>$v['CashScale'], 'price'=>$v['DeductNum']);
                    break;
            }
        }


        // 礼品包
        $gifts = &$hotel['Gifts'];
        foreach ($gifts as $v)
        {
            $code = 'GF_'.$v['GiftId'];
            switch ($v['GiftTypes'])
            {
                case '3':   // 送礼品
                    $_package_code[$code] = array('name'=>'送礼品');
                    break;

                case '5':   // 免费接机
                    $_package_code[$code] = array('name'=>'免费接机');
                    break;

                case '7':   // 送旅游/门票
                    $_package_code[$code] = array('name'=>'套票');
                    break;

                case '8':   // 其他
                    $_package_code[$code] = array('name'=>$v['WayOfGivingOther']);
                    break;
            }
        }


        // 床型
        $rooms = &$hotel['Rooms'];
        foreach ($rooms as $room)
        {
            $roomid = $room['RoomId'];

            // 搜索自己的房型数据，未配对，未添加的跳过
            $ourroom = self::_room($hotelcode, $hotelcode.'_'.$roomid, $room['Name']);
            if (!$ourroom || !$ourroom['type']) continue;

            foreach ($room['RatePlans'] as $rate)
            {
                $currency = self::_data($rate['CurrencyCode'], 'currency');

                $allot = $rate['CurrentAlloment'] == 0 ? 9 : (int)$rate['CurrentAlloment'];

                $typeid = $rate['RoomTypeId'];
                $rateid = $rate['RatePlanId'];

                $dr_ids = !empty($rate['DrrRuleIds']) ? array_filter(explode(',', $rate['DrrRuleIds'])) : array();
                $va_ids = !empty($rate['ValueAddIds']) ? array_filter(explode(',', $rate['ValueAddIds'])) : array();
                $gf_ids = !empty($rate['GiftIds']) ? array_filter(explode(',', $rate['GiftIds'])) : array();

                $check = true;
                $breakfast = $addbf = $advance = $min = $nation = 0;
                $package = array();

                foreach ($dr_ids as $id)
                {
                    $id = 'DR_'.$id;
                    if (!isset($_advance_code[$id]) && !isset($_min_code[$id]))
                    {
                        $check = false;
                    }

                    if (isset($_advance_code[$id]))
                        $advance = $_advance_code[$id]['num'];

                    if (isset($_advance_code[$id]))
                        $min = $_min_code[$id]['num'];
                }
                if (!$check) continue;

                foreach ($gf_ids as $id)
                {
                    $id = 'GF_'.$id;

                    if (isset($_package_code[$id]))
                        $package[] = $_package_code[$id]['name'];
                }

                foreach ($va_ids as $id)
                {
                    $id = 'VA_'.$id;
                    if (isset($_bf_code[$id]))
                        $breakfast = $_bf_code[$id]['num'];

                    if (isset($_addbf_code[$id]))
                        $addbf = $_addbf_code[$id]['price'];

                    if (isset($_package_code[$id]))
                        $package[] = $_package_code[$id]['name'];
                }

                $package = self::_data('', 'package', implode(',', $package));
                if ($package === false) $package = 0;

                // 单日价格
                foreach ($rate['NightlyRates'] as $night)
                {
                    $time = strtotime(substr($night['Date'], 0, 10));

                    $price = $night['Member'] <= 0 ? 0 : (int)$night['Member'];

                    $filled = $night['Status'] ? 0 : 1;

                    $addbe = $night['AddBed'] <= 0 ? 0 : (int)$night['AddBed'];

                    $price = array(
                        'payment'   => 1,
                        'hotel'     => $ourroom['hotel'],
                        'room'      => $ourroom['id'],
                        'bed'       => $ourroom['bed'],
                        'roomtype'  => $ourroom['type'],
                        'nation'    => 0,
                        'package'   => 0,
                        'date'      => $time,
                        'price'     => $price,
                        'currency'  => $currency,
                        'rebate'    => 0,
                        'breakfast' => $breakfast,
                        'supply'    => 'ELG',
                        'supplyid'  => $typeid,
                        'start'     => 0,
                        'end'       => 0,
                        'min'       => $min,
                        'advance'   => $advance,
                        'addbf'     => $addbf,
                        'addbe'     => $addbe,
                        'net'       => $ourroom['net'],
                        'filled'    => $filled,
                        'allot'     => $filled ? 0 : $allot,
                        'standby'   => json_encode(array('rate'=>$rateid), JSON_UNESCAPED_UNICODE),
                        'cutoff'    => strtotime('18:00:00') - strtotime('00:00:00'),
                        'update'    => NOW,
                        'close'     => 0,
                    );

                    // KEY :       日期6,房型6,国籍3,预付/现付1,范围/提前/连住3,早餐数1,价格包3,供应商3,子供应商(合同号)-
                    // uncombine : 房型3,国籍3,预付/现付1,范围/提前/连住3,子供应商6
                    // combine :   早餐数1,价格包3
                    $price['key'] = date('ymd', $time)
                        .str_pad(strtoupper(dechex($ourroom['id'])), 6, 0, STR_PAD_LEFT)
                        .'00010'
                        .int2chr($price['advance'])
                        .int2chr($price['min'])
                        .int2chr($price['breakfast'])
                        .str_pad(strtoupper(dechex($package)), 3, 0, STR_PAD_LEFT)
                        .'ELG'
                        .$typeid;

                    $price['uncombine'] = substr($price['key'], 6, 10).substr($price['key'], 23);

                    $price['combine'] = substr($price['key'], 16, 4);

                    if (isset($result[$price['key']]) && self::_compare($result[$price['key']], $price))
                        continue;

                    $result[$price['key']] = $price;
                }
            }
        }

        return $result;
    }
    // price






    // 更新价格
    // 仅能更新一个酒店
    static function refresh($hotelcode, $roomcode='', $checkin=0, $checkout=0)
    {
        $roomcode = '';
        return self::_refresh($hotelcode, $roomcode, $checkin, $checkout);
    }
    // refresh






    // 更新酒店列表
    static function hotel($id=null, $international=false, $page=1, $callback=null)
    {
        if ($international)
        {
            self::international_hotel($id);
        }
        else
        {
            self::domestic_hotel($id, $page, $callback);
        }
    }
    // hotel




    // 特殊的根据条件检索酒店
    static function search_hotel($city, $price=1000, $page=2)
    {
        $opts = array(
            'checkInDate'   => date('Y-m-d', strtotime('+6 day')),
            'checkOutDate'  => date('Y-m-d', strtotime('+7 day')),
            'destinationId' => $city,
            'RoomGroup'     => array(
                0 => array(
                    'childAges'         => null,
                    'numberOfAdults'    => 2,
                    'numberOfChildren'  => 0,
                ),
            ),
            'minRate'       => $price,
            'pageIndex'     => $page,
            'pageSize'      => 100,
        );

        $data = self::_request('ghotel.search.list', $opts, true);
        return $data;
    }




    // 更新国内酒店列表
    static function domestic_hotel($id=null, $page=1, $callback=null)
    {
        self::$log_name = 'elg_hotel_refresh'.($id ? "_{$id}" : '');

        // Disable any repeat request. If a process is runing.
        if (self::log(self::$log_name)) return false;

        if (!$id)
        {
            $path = PT_PATH.'log/elong_'.date('Ymd').'_domestic_hotel.xml';
            if (!file_exists($path))
            {
                $rs = copy(substr(self::$url, 0, -5).'/xml/v2.0/hotel/hotellist.xml', $path);
                if (!$rs) return false;
            }
            $xml = simplexml_load_string(file_get_contents($path));

            $root = $xml -> children();
            $hotels = $root[0] -> children();
        }
        else
        {
            $hotels = array(array('HotelId'=>$id));
        }

        $db = db(config('db'));
        $db -> beginTrans();

        $i = 0;
        foreach ($hotels as $v)
        {
            if ($i < ($page - 1) * 10000) { $i++; continue; }
            if ($i >= $page * 10000) break;
            $i ++;

            $hotelid = (string)$v['HotelId'];
            $xml_str = curl_file_get_contents(substr(self::$url, 0, -5).'/xml/v2.0/hotel/cn/'.substr($hotelid, -2, 2).'/'.$hotelid.'.xml');
            if (!empty($_GET['debug'])){ echo $xml_str; exit; }
            if (!$xml_str) continue;

            $pe = ($i + 1). '/'. count($hotels). ': ';

            $hotel_xml = simplexml_load_string($xml_str);

            if ($hotel_xml)
            {
                $_hotel = $hotel_xml -> children();

                $detail = &$_hotel -> Detail;
                $hotel = array(
                    'id'        => $hotelid,
                    'name'      => (string)trim($detail -> Name),
                    'country'   => 1,
                    'city'      => (string)$detail -> CityId,
                    'address'   => (string)$detail -> Address,
                    'tel'       => (string)$detail -> Phone,
                    'isdel'     => 0,
                );

                if (isset($v['Status'])) $hotel['isdel'] = (int)$v['Status'] ? 1 : 0;


                $old = $db -> prepare("SELECT `name`,`address`,`tel`".(isset($v['Status']) ? ',`isdel`' : '')." FROM `sup_elg_hotel` WHERE `id`=:id;") -> execute(array(':id'=>$v['HotelId']));
                if (!$old)
                    self::log(self::$log_name, $pe."NEW HOTEL: {$hotel['id']} - {$hotel['name']}");
                else if ($up = array_diff_assoc($old[0], $hotel))
                    self::log(self::$log_name, $pe."UPDATE HOTEL: {$hotel['id']} - {$hotel['name']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));

                list($column, $sql, $value) = array_values(insert_array($hotel));
                $rs = $db -> prepare("REPLACE INTO `sup_elg_hotel` {$column} VALUES {$sql};") -> execute($value);
                if (false === $rs)
                {
                    $db -> rollback();
                    return false;
                }

                $rooms = $_hotel -> Rooms -> children();
                $rs = self::room($rooms, $hotelid, $db);
                if (!$rs)
                {
                    $db -> rollback();
                    return false;
                }
            }
            else
            {
                self::error('更新酒店错误：'.$hotelid.':'.$xml_str, true);
                self::log(self::$log_name, $pe."UPDATE FAIL: {$hotelid}");
            }
            //break;
        }

        if ($db -> commit())
        {
            self::log(self::$log_name, true);
            if (!is_null($callback)) $callback($page);
            return true;
        }
        else
        {
            $db -> rollback();
            return false;
        }
    }
    // domestic_hotel




    // 更新国际酒店
    static function international_hotel($id=null)
    {
        self::$log_name = 'elg_hotel_inter_refresh'.($id ? "_{$id}" : '');

        // Disable any repeat request. If a process is runing.
        if (self::log(self::$log_name)) return false;

        // Download zip file
        $zip_path = PT_PATH.'log/elong_'.date('Ymd').'_international_hotels.zip';
        if (!file_exists($zip_path))
        {
            $rs = copy(substr(self::$url, 0, -5).'/gfiles/Hotel_Description_zh_CN.zip', $zip_path);
            if (!$rs) return false;
        }

        // Unzip the zip file
        $ext_path = PT_PATH.'log/tmp/elg_'.date('Ymd').'/Hotel_Description_zh_CN.txt';
        if (!file_exists($ext_path))
        {
            $zip = new ZipArchive;
            if ($zip -> open($zip_path) !== true)
                return false;

            mkdirs(dirname($ext_path));
            $zip->extractTo(dirname($ext_path));
            $zip->close();

            file_put_contents($ext_path, mb_convert_encoding(file_get_contents($ext_path), 'UTF-8', 'UCS-2LE'));
        }

        // static $countries and $cities
        static $countries, $cities;

        // Begin translation
        $db = db(config('db'));
        $db -> beginTrans();
/*
        $rs = $db -> prepare("UPDATE `sup_elg_hotel` SET `isdel`=1 WHERE ".()) -> execute();
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }
*/
        // Load txt content
        $handle = fopen($ext_path, "r");
        while (!feof($handle))
        {
            $line = fgets($handle);
            $_data = explode('|', $line);

            $_id = $_data[0];
            if (!$_id || !is_numeric($_id)) continue;

            if ($id && $id != $_id) continue;

            $_city = trim($_data[3]);
            $_country = trim($_data[5]);

            // search country by code
            if (!isset($countries[$_country]))
            {
                $country = $db -> prepare("SELECT * FROM `sup_elg_country` WHERE `code`=:code") -> execute(array(':code'=>$_country));
                if (!$country)
                {
                    self::log(self::$log_name, "HOTEL'S (ID:{$_id}) COUNTRY NOT FOUND: {$_country}");
                    $countries[$_country] = false;
                    continue;
                }

                $country = $countries[$_country] = $country[0]['id'];
            }
            else
            {
                $country = $countries[$_country];
                if (!$country) continue;
            }

            // search city by name
            if (!isset($cities[$country.'-'.$_city]))
            {
                $city = $db -> prepare("SELECT * FROM `sup_elg_city` WHERE `name`=:name AND `international`=1 AND `type`='city' AND `country`=:country") -> execute(array(':name'=>$_city, ':country'=>$country));
                if (!$city)
                {
                    self::log(self::$log_name, "HOTEL'S (ID:{$_id}) CITY NOT FOUND: {$_country} - {$_city}");
                    $cities[$country.'-'.$_city] = false;
                    continue;
                }

                $city = $cities[$country.'-'.$_city] = $city[0]['code'];
            }
            else
            {
                $city = $cities[$country.'-'.$_city];
                if (!$city) continue;
            }

            $hotel = array('id'=>$_id, 'name'=>$_data[1], 'country'=>$country, 'city'=>$city, 'address'=>$_data[2], 'tel'=>'', 'isdel'=>0);

            // log old data
            $old = $db -> prepare("SELECT `name`,`address` FROM `sup_elg_hotel` WHERE `id`=:id;") -> execute(array(':id'=>$_id));
            if (!$old)
                self::log(self::$log_name, "NEW HOTEL: {$hotel['id']} - {$hotel['name']}");
            else if ($up = array_diff_assoc($old[0], $hotel))
                self::log(self::$log_name, "UPDATE HOTEL: {$hotel['id']} - {$hotel['name']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));

            if (isset($_GET['debug'])) echo $_id."\n";

            // save data
            list($column, $sql, $value) = array_values(insert_array($hotel));
            $db -> prepare("REPLACE INTO `sup_elg_hotel` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                fclose($handle);
                $db -> rollback();
                return false;
            }

            //break;
        }
        fclose($handle);

        return true;

        if ($db -> commit())
        {
            self::log(self::$log_name, true);
            return true;
        }
        else
        {
            $db -> rollback();
            return false;
        }
    }
    // international_hotel






    // 更新房型
    private static function room(&$rooms, $hotelid, $db)
    {
        $rs = $db -> prepare("UPDATE `sup_elg_room` SET `isdel`=1 WHERE `hotel`=:hotel") -> execute(array(':hotel'=>$hotelid));
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        // 保存新数据
        $data = array();

        foreach ($rooms as $room)
        {
            $attr = $room -> attributes();
            $bedname = trim((string)$attr -> BedType);

            $roomid = (string)$attr -> Id;
            $key = "{$hotelid}_{$roomid}";
            $data[$key] = array(
                'hotel'     => $hotelid,
                'room'      => $roomid,
                'roomname'  => (string)$attr -> Name,
                'bed'       => self::bedtype($bedname),
                'bedname'   => $bedname,
                'net'       => (int)$attr -> BroadnetAccess,
                'netfee'    => (int)$attr -> BroadnetFee,
                'isdel'     => 0,
            );

            $old = $db  -> prepare("SELECT `roomname`, `bedname`, `net`, `netfee` FROM `sup_elg_room` WHERE `hotel`=:hotel AND `room`=:room;") -> execute(array(':hotel'=>$hotelid, ':room'=>$roomid));
            if (!$old)
                self::log(self::$log_name, "\tNEW ROOM: {$data[$key]['room']} - {$data[$key]['roomname']}");
            else if ($up = array_diff_assoc($old[0], $data[$key]))
                self::log(self::$log_name, "\tUPDATE ROOM: {$data[$key]['room']} - {$data[$key]['roomname']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));
        }

        $data = array_values($data);
        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("REPLACE INTO `sup_elg_room` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
        }

        return true;
    }
    // room






    // 床型
    static function bedtype($name)
    {
        static $beds = array();

        if (empty($beds[$name]))
        {
            $db = db(config('db'));
            $_bed = $db -> prepare("SELECT * FROM `sup_elg_bed` WHERE `name`=:name") -> execute(array(':name' => $name));
            if (!$_bed)
            {
                $type = '';
                $type .= (strpos($name, '双') !== false ? 'T' : '');
                $type .= (strpos($name, '大') !== false ? 'D' : '');
                $type .= (strpos($name, '单') !== false ? 'S' : '');
                $type .= (strpos($name, '圆') !== false ? 'C' : '');

                if ($type == 'TD') $type = 2;
                else if ($type == 'TS') $type = 'T';
                else if ($type == 'DS') $type = 'D';
                else if ($type == 'TDS') $type = 2;
                else if (strlen($type) > 1) $type = 'O';
                else if (!$type) $type = 'O';

                $db -> prepare("INSERT INTO `sup_elg_bed` (`name`,`type`) VALUES (:name, :type)") -> execute(array(':name' => $name, ':type'=>$type));
            }
            else
            {
                $type = $_bed[0]['type'];
            }

            $beds[$name] = $type; // cache
        }
        else
        {
            $type = $beds[$name];
        }

        return $type;
    }
    // bedtype






    // 更新城市
    // Download Link：   http://api.elong.com/gfiles/ParentRegionList.zip
    static function city($international=false)
    {
        if (!$international)
        {
            $xml_str = curl_file_get_contents(substr(self::$url, 0, -5).'/xml/v2.0/hotel/geo_cn.xml');
            $xml = simplexml_load_string($xml_str);

            file_put_contents(PT_PATH.'log/geo_cn.xml', $xml_str);
            unset($xml_str);

            $citys = array();
            if (!$xml) return true;

            $root = $xml -> children();
            $_citys = $root[0] -> children();
            foreach ($_citys as $v)
            {
                $attr = $v -> attributes();
                $citys[] = array(
                    'code'          => (string)$attr['CityCode'],
                    'name'          => (string)$attr['CityName'],
                    'international' => 0,
                    'type'          => 'City',
                    'pid'           => 37,
                    'ptype'         => 'Country',
                    'country'       => 37,
                    'isdel'         => 0,
                );
            }

            $db = db(config('db'));
            $db -> beginTrans();

            $rs = $db -> prepare("UPDATE `sup_elg_city` SET `isdel`=1 WHERE `country`= '37';") -> execute();
            if ($rs === false)
            {
                $db -> rollback();
                return false;
            }

            list($column, $sql, $value) = array_values(insert_array($citys));
            $rs = $db -> prepare("REPLACE INTO `sup_elg_city` {$column} VALUES {$sql};") -> execute($value);
            if ($rs === false)
            {
                $db -> rollback();
                return false;
            }
        }
        else
        {
            // rar file unzip to log/tmp/
            $file_path = PT_PATH.'log/tmp/RegionListWithDestinationID.txt';

            // Begin translation
            $db = db(config('db'));
            $db -> beginTrans();

            $rs = $db -> prepare("UPDATE `sup_elg_city` SET `isdel`=1 WHERE `international`=0;") -> execute();
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }

            // Load txt content
            $handle = fopen($file_path, "r");
            while (!feof($handle))
            {
                $line = fgets($handle);
                $_data = explode('|', $line);

                $id = $_data[0];
                if (!$id || !is_numeric($id)) continue;

                if ($_data[1] == 'Country') continue;

                $city = array('code'=>$id, 'name'=>$_data[4], 'destination'=>$_data[10], 'international'=>1, 'type'=>trim($_data[1]), 'pid'=>$_data[6], 'ptype'=>trim($_data[7]), 'isdel'=>0);

                if(isset($_GET['debug'])) echo $id."\n";

                list($column, $sql, $value) = array_values(insert_array($city));
                $db -> prepare("REPLACE INTO `sup_elg_city` {$column} VALUES {$sql};") -> execute($value);
                if (false === $rs)
                {
                    fclose($handle);
                    $db -> rollback();
                    return false;
                }
            }
            fclose($handle);

            // Update
            $citys = $db -> prepare("SELECT * FROM `sup_elg_city` WHERE `international` = 1 AND `type`='City';") -> execute();
            foreach ($citys as $city)
            {
                $country = self::_city_parent($city);
                $rs = $db -> prepare("UPDATE `sup_elg_city` SET `country`=:country WHERE `code`=:code AND `international` = 1;") -> execute(array(':code'=>$city['code'], ':country'=>$country));
                $rs = $rs === false ? 'fail' : 'success';

                if(isset($_GET['debug'])) echo "{$city['code']}:{$country} {$rs}\n";
            }
        }

        // Commit
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
    // city





    // 更新城市的所属国家
    static private function _city_parent($city)
    {
        if ($city['ptype'] == 'Country')
        {
            return $city['pid'];
        }
        else
        {
            $db = db(config('db'));
            $parent = $db -> prepare("SELECT * FROM `sup_elg_city` WHERE `code`=:code AND `international` = 1;") -> execute(array(':code'=>$city['pid']));
            if (!$parent) return 0;

            return self::_city_parent($parent[0]);
        }
    }
    // _city_parent





    // 更新国家
    // Download Link：  http://api.elong.com/gfiles/CountryList.zip => CountryList.txt
    //                  http://api.elong.com/gfiles/CountryList_zh_CN.zip => CountryList_zh_CN.txt
    static function country()
    {
        // Download zip file
        $zip_path = PT_PATH.'log/elong_'.date('Ymd').'_country.zip';
        $zip_path_zh = PT_PATH.'log/elong_'.date('Ymd').'_country_zh_CN.zip';
        if (!file_exists($zip_path) && !file_exists($zip_path_zh))
        {
            $rs = copy(substr(self::$url, 0, -5).'/gfiles/CountryList.zip', $zip_path);
            if (!$rs) return false;

            $rs = copy(substr(self::$url, 0, -5).'/gfiles/CountryList_zh_CN.zip', $zip_path_zh);
            if (!$rs) return false;
        }

        // Unzip the zip file
        $ext_path = PT_PATH.'log/tmp/elg_'.date('Ymd').'/CountryList.txt';
        $ext_path_zh = PT_PATH.'log/tmp/elg_'.date('Ymd').'/CountryList_zh_CN.txt';
        if (!file_exists($ext_path))
        {
            $zip = new ZipArchive;
            if ($zip -> open($zip_path) !== true)
                return false;

            mkdirs(dirname($ext_path));
            $zip -> extractTo(dirname($ext_path));
            $zip -> close();

            if ($zip -> open($zip_path_zh) !== true)
                return false;

            $zip -> extractTo(dirname($ext_path_zh));
            $zip -> close();
        }

        // Load txt content
        $country = array();
        $handle = fopen($ext_path, "r");    // <--- use fopen reduced memory usage
        while (!feof($handle))
        {
            $line = fgets($handle);
            list($id, $lang, $name, $code) = explode('|', $line);

            if (!$id || !is_numeric($id)) continue;

            $country[$id] = array('id'=>$id, 'name'=>$name, 'code'=>trim($code), 'isdel'=>0);
        }
        fclose($handle);

        $handle = fopen($ext_path_zh, "r");
        while (!feof($handle))
        {
            $line = fgets($handle);
            list($id, $lang, $name) = explode('|', $line);
            if(isset($country[$id])) $country[$id]['name'] = trim($name);
        }
        fclose($handle);

        /* debug:
        var_dump($country); exit;
        //*/

        if (!$country) return false;

        $db = db(config('db'));
        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `sup_elg_country` SET `isdel`=1;") -> execute();
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        list($column, $sql, $value) = array_values(insert_array(array_values($country)));
        $rs = $db -> prepare("REPLACE INTO `sup_elg_country` {$column} VALUES {$sql};") -> execute($value);
        if (false === $rs)
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
    // country







    // 转义房型键值
    static function roomkey($room)
    {
        return $room['hotel'].'_'.$room['room'];
    }
    // roomkey




    // 解析房型键值
    static function parsekey($key)
    {
        list($hotel, $room) = explode('_', $key);
        return array('hotel'=>$hotel, 'room'=>$room);
    }
    // parsekey




    // 转义床型
    static function bed($bed)
    {
        return $bed;
    }
    // bed




    // 转义宽带
    static function net($net)
    {
        if ($net['net'] == 0) return 0;
        return $net['netfee'] ? 3 : 4;
    }
    // net



}
?>