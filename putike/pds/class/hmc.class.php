<?php
/**
 * 华闽供应商
 +-----------------------------------------
 * @category
 * @package hmc
 * @author nolan.zhou
 * @version $Id$
 */
class hmc extends supply implements supply_inf
{
    //全局网址
    private static $url = 'http://api.huamin.com.hk/api/';

    //验证信息
    private static $option = array('p_company'=>'CN00891', 'p_id'=>'APIADMIN', 'p_pass'=>'PTK021', 'p_lang'=>'SIM', 'p_filter'=>'N', 'p_gzip'=>'N');

    // 日志用名
    private static $log_name = '';


    // 发送请求
    private static function _request($method, $args=array())
    {
        $args = array_merge(self::$option, $args);
        $url = self::$url . 'xml_' . $method .'.php?' . http_build_query($args);

        //获取
        $xmlStr = curl_file_get_contents($url, null, null, 100000);

        if (isset($_GET['debug'])) { header("Content-type:text/xml"); echo str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8"?><debug><url><![CDATA['.$url.']]></url>', $xmlStr), "</debug>"; exit; } //var_dump($xmlStr); exit;

        if ($xmlStr == '<?xml version="1.0"?>')
            $xml = '';
        else
            $xml = simplexml_load_string($xmlStr);

        if (!$xml)
        {
            self::error('获取数据异常：'.$xmlStr);
            return false;
        }

        $xmlArr = parse_xml($xml);

        if (!empty($xmlArr['error_code']))
        {
            self::error('获取数据异常：'.$xmlArr['error_msg']);
            return false;
        }

        if ($xmlArr['XML_RESULT']['RETURN_CODE'] > 0)
        {
            self::error('获取数据异常：'.$xmlStr);
            return false;
        }

        return $xmlArr;
    }
    // _request






    // _price
    static function _price($hotelcode, $roomcode='', $bedcode='', $checkin=0, $checkout=0)
    {
        $db = db(config('db'));

        if(!$checkin) $checkin = strtotime("today");
        if(!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." +30 month");

        if($checkin >= $checkout)
            return false;

        // Time change
        $checkin = strtoupper(date('j-M-y', $checkin));
        $checkout = strtoupper(date('j-M-y', $checkout));

        $condition = array('p_hotel'=>$hotelcode, 'p_checkin'=>$checkin, 'p_checkout'=>$checkout);
        if ($roomcode)
            $condition['p_rcat'] = $roomcode;
        if ($bedcode)
            $condition['p_rtype'] = $bedcode;

        $data = self::_request('qrate', $condition);

        // Clear Old Data
        if(!isset($data['XML_RESULT']['CONTRACTS']))
            return array();

        if(!isset($data['XML_RESULT']['CONTRACTS'][0]))
            $data['XML_RESULT']['CONTRACTS'] = array($data['XML_RESULT']['CONTRACTS']);

        $result = array();

        foreach($data['XML_RESULT']['CONTRACTS'] as $contract)
        {
            $contract_id = $contract['CONTRACT'];

            // Products
            if(!isset($contract['PRODUCT'][0]))
                $contract['PRODUCT'] = array($contract['PRODUCT']);

            // Products 跨月份时经常出现分拆
            foreach($contract['PRODUCT'] as $product)
            {
                // Product id（即若产品id相同的相同房型，服务早餐必然相同）
                $product_id = $product['PROD'];

                // Nation
                $nation = $db -> prepare("SELECT * FROM `sup_hmc_nation` WHERE `code`=:code") -> execute(array(':code'=>$product['NATION']));
                $nation = $nation[0]['name'];

                // Package
                $package = $product['RORATE'];

                // Min
                $min = ((int)$product['MIN'] <= 1) ? 0 : (int)$product['MIN'];

                // Advance
                $advance = (int)$product['ADVANCE'];

                // debug : var_dump($product['ROOM']); exit;
                if(!isset($product['ROOM'][0]))
                    $product['ROOM'] = array($product['ROOM']);

                // Rooms
                foreach($product['ROOM'] as $room)
                {
                    $_room = array(
                        'room' => $room['CAT'],
                        'bed'  => self::bed($room['TYPE']),
                    );

                    // 不更新非要求的房型
                    if($roomcode && $roomcode != $_room['room']) continue;

                    if($bedcode && $bedcode != $_room['bed']) continue;

                    if(!isset($room['STAY'][0]))
                        $room['STAY'] = array($room['STAY']);

                    // 无需跟随更新的部分
                    $extend = array(
                        'nation'    => $nation,
                        'min'       => $min,            // 连住
                        'start'     => 0,
                        'end'       => 0,
                        'advance'   => $advance,        // 提前，连住和提前，存在于产品字段，应该属于长期不更新状态。
                        'breakfast' => (int)$room['BF'],
                        'package'   => $package,
                    );

                    foreach($room['STAY'] as $price)
                    {
                        // 不允许0价格入库
                        if((int)$price['PRICE'] <= 0) continue;

                        $time = explode('-', $price['STAYDATE']);
                        $time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);

                        // 需要更新的部分
                        $price = array(
                            'date'      => $time,
                            'price'     => (int)$price['PRICE'],
                            //'currency'  => $currency,
                            'rebate'    => 0,
                            'allot'     => $price['IS_ALLOT'] == 'C' ? 0 : ($price['IS_ALLOT'] == 99 ? 9 : $price['IS_ALLOT']),
                            'filled'    => ((($package == 42 || $package == 148) && $price['IS_ALLOT'] == 0) || $price['IS_ALLOT'] == 'C') ? 1 : 0,
                        );

                        $price['key'] = date('ymd', $time)
                            .$product_id
                            .$_room['room']
                            .$_room['bed']
                            .int2chr($extend['breakfast'])
                            .$contract_id;

                        if (!isset($result[$time])) $result[$time] = array();

                        if (isset($result[$time][$price['key']]) && self::_compare($result[$time][$price['key']], $price))
                            continue;

                        $result[$time][$price['key']] = array_merge($extend, $price);
                    }
                }
            }
        }

        return $result;
    }
    // price






    // price
    static function price($hotelcode, $roomcode='', $bedcode='', $checkin=0, $checkout=0)
    {
        if (!$checkin) $checkin = strtotime("today");
        if (!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." +30 month");

        if ($checkin >= $checkout)
            return false;

        // Time change
        $checkin = strtoupper(date('j-M-y', $checkin));
        $checkout = strtoupper(date('j-M-y', $checkout));

        $condition = array('p_hotel'=>$hotelcode, 'p_checkin'=>$checkin, 'p_checkout'=>$checkout);
        if ($roomcode)
            $condition['p_rcat'] = $roomcode;
        if ($bedcode)
            $condition['p_rtype'] = $bedcode;

        $data = self::_request('qrate', $condition);

        // Clear Old Data
        if (!isset($data['XML_RESULT']['CONTRACTS']))
            return array();

        if (!isset($data['XML_RESULT']['CONTRACTS'][0]))
            $data['XML_RESULT']['CONTRACTS'] = array($data['XML_RESULT']['CONTRACTS']);

        $result = array();

        foreach ($data['XML_RESULT']['CONTRACTS'] as $contract)
        {
            $contract_id = $contract['CONTRACT'];

            // currency
            $currency = self::_data($contract['CUR'], 'currency');
            if ($currency === false) continue;

            // serv
            $service = self::serv($contract_id);
            if ($service === false)
            {
                self::error("Hotel:{$hotelcode} Contract:{$contract_id} serv load fail！");
                //continue;
            }

            // Products
            if (!isset($contract['PRODUCT'][0]))
                $contract['PRODUCT'] = array($contract['PRODUCT']);

            // Products 跨月份时经常出现分拆
            foreach ($contract['PRODUCT'] as $product)
            {
                // Product id（即若产品id相同的相同房型，服务早餐必然相同）
                $product_id = $product['PROD'];

                // Nation
                $nation = self::_data($product['NATION'], 'nation');
                if($nation === false) continue;

                // Package
                $package = self::_data('', 'package', $product['RORATE']);
                if($package === false) $package = 0;

                // Min
                $min = ((int)$product['MIN'] <= 1) ? 0 : (int)$product['MIN'];

                // Advance
                $advance = (int)$product['ADVANCE'];

                // debug : var_dump($product['ROOM']); exit;
                if (!isset($product['ROOM'][0]))
                    $product['ROOM'] = array($product['ROOM']);

                // Rooms
                foreach ($product['ROOM'] as $room)
                {
                    $_room = array(
                        'room' => $room['CAT'],
                        'bed'  => self::bed($room['TYPE']),
                    );

                    // 不更新非要求的房型
                    if ($roomcode && $roomcode != $_room['room']) continue;

                    if ($bedcode && $bedcode != $_room['bed']) continue;

                    // 保存服务及相关订单所需资料
                    if (isset($service[$room['SERV']]))
                    {
                        $net = (int)$service[$room['SERV']]['net'];
                        $serv = $service[$room['SERV']];
                    }
                    else
                    {
                        $net = 0;
                        $serv = array('BE'=>array(), 'BF'=>array(), 'net'=>0);
                    }
                    $addBF = $serv['BF'] ? ($serv['BF']['PRICE'] ? $serv['BF']['PRICE'] : -1) : 0;
                    $addBE = $serv['BE'] ? ($serv['BE']['PRICE'] ? $serv['BE']['PRICE'] : -1) : 0;
                    if (isset($serv['BF']['PRICE'])) unset($serv['BF']['PRICE']);
                    if (isset($serv['BE']['PRICE'])) unset($serv['BE']['PRICE']);

                    $standby = array(
                        'PROD'  => $product_id,
                        'SERV'  => $room['SERV'],
                        'BF'    => $serv['BF'],
                        'BE'    => $serv['BE']
                    );

                    if (!isset($room['STAY'][0]))
                        $room['STAY'] = array($room['STAY']);

                    // 搜索自己的房型数据，未配对，未添加的跳过
                    $ourroom = self::_room($hotelcode, "{$hotelcode}_{$room['CAT']}_{$room['TYPE']}", $room['STAY'][0]['CATNAME']);
                    if (!$ourroom || !$ourroom['type']) continue;

                    // 无需跟随更新的部分
                    $extend = array(
                        'payment'   => 1,
                        'hotel'     => $ourroom['hotel'],
                        'room'      => $ourroom['id'],
                        'bed'       => $ourroom['bed'],
                        'roomtype'  => $ourroom['type'],
                        'nation'    => $nation,
                        'package'   => $package,
                        'currency'  => $currency,
                        'breakfast' => (int)$room['BF'],
                        'supply'    => 'HMC',
                        'supplyid'  => $contract_id,    // 合同号不可合并
                        'start'     => 0,
                        'end'       => 0,
                        'min'       => $min,            // 连住
                        'advance'   => $advance,        // 提前，连住和提前，存在于产品字段，应该属于长期不更新状态。
                        'net'       => $ourroom['net'],
                        'addbf'     => $addBF,
                        'addbe'     => $addBE,
                        'cutoff'    => 0,
                        'update'    => NOW,
                        'close'     => 0,
                    );

                    foreach ($room['STAY'] as $price)
                    {
                        // 不允许0价格入库
                        if ((int)$price['PRICE'] <= 0) continue;

                        $time = explode('-', $price['STAYDATE']);
                        $time = mktime(0, 0, 0, $time[1], $time[0], $time[2]);

                        $standby['ALLOT'] = $price['ALLOT'];

                        // 需要更新的部分
                        $price = array(
                            'date'      => $time,
                            'price'     => (int)$price['PRICE'],
                            'rebate'    => 0,
                            'filled'    => ((($package == 42 || $package == 148) && $price['IS_ALLOT'] == 0) || $price['IS_ALLOT'] == 'C') ? 1 : 0,
                            'allot'     => $price['IS_ALLOT'] == 'C' ? 0 : ($price['IS_ALLOT'] == 99 ? 9 : $price['IS_ALLOT']),
                            'seq'       => 0,
                            'standby'   => json_encode($standby, JSON_UNESCAPED_UNICODE),
                        );

                        // KEY :       日期6,房型6,国籍3,预付/现付1,范围/提前/连住3,早餐数1,价格包3,供应商3,子供应商(合同号)-
                        // uncombine : 房型3,国籍3,预付/现付1,范围/提前/连住3,子供应商6
                        // combine :   早餐数1,价格包3
                        $price['key'] = date('ymd', $time)
                            .str_pad(strtoupper(dechex($ourroom['id'])), 6, 0, STR_PAD_LEFT)
                            .str_pad(strtoupper(dechex($nation)), 3, 0, STR_PAD_LEFT)
                            .'10'
                            .int2chr($advance)
                            .int2chr($min)
                            .int2chr($extend['breakfast'])
                            .str_pad(strtoupper(dechex($package)), 3, 0, STR_PAD_LEFT)
                            .'HMC'
                            .$contract_id;

                        $price['uncombine'] = substr($price['key'], 6, 13).substr($price['key'], 23);

                        $price['combine'] = substr($price['key'], 19, 4);

                        if (isset($result[$price['key']]) && self::_compare($result[$price['key']], $price))
                            continue;

                        $result[$price['key']] = array_merge($extend, $price);
                    }
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





    // 获取服务包
    private static function serv($contract, $serv='')
    {
        $condition = array('p_contract'=>$contract);
        if ($serv) $condition['p_serv'] = $serv;

        $data = self::_request('qserv', $condition);

        if(!isset($data['XML_RESULT']['CONTRACTS']))
        {
            self::error('Serv Contract:'.$contract.' 没有数据！');
            return false;
        }

        if(!isset($data['XML_RESULT']['CONTRACTS']['SERVICE'][0]))
            $data['XML_RESULT']['CONTRACTS']['SERVICE'] = array($data['XML_RESULT']['CONTRACTS']['SERVICE']);

        $addBFCode = array('BF','ABF','ABF3','BBF','CNBF','CPB','FBF','FCBF','FOBF','OBF','XBBF'); //CPB 必须惠顾是个什么玩意擦

        $addBECode = array('EB','BE','BE2','BE1','BEB','EBB','EBK2','EBK3','EKB4','FBED','FEBB');

        $netCode = array('BD'=>1, 'BDF'=>2, 'FBD'=>2, 'FIA'=>1, 'FWFB'=>0, 'IT'=>1, 'ITD'=>1, 'ITH'=>1, 'WF'=>1, 'WFF'=>2);

        $service = array();
        foreach($data['XML_RESULT']['CONTRACTS']['SERVICE'] as $serv)
        {
            $_serv = array('BF'=>array(), 'BE'=>array(), 'net'=>0);

            if(!isset($serv['ITEM'][0]))
                $serv['ITEM'] = array($serv['ITEM']);

            foreach($serv['ITEM'] as $item)
            {
                if(in_array($item['SERVCODE'], $addBFCode))
                {
                    if(!$_serv['BF'] || $_serv['BF']['PRICE'] > $item['PRICE'])
                        $_serv['BF'] = $item;
                }

                if(in_array($item['SERVCODE'], $addBECode))
                {
                    if(!$_serv['BE'] || $_serv['BE']['PRICE'] > $item['PRICE'])
                        $_serv['BE'] = $item;
                }

                if(isset($netCode[$item['SERVCODE']]))
                {
                    if($netCode[$item['SERVCODE']] > $_serv['net'])
                        $_serv['net'] = (int)$netCode[$item['SERVCODE']];
                }
            }

            unset($_serv['BE']['SERVNAME'], $_serv['BE']['MIN'], $_serv['BF']['SERVNAME'], $_serv['BF']['MIN']);
            $service[$serv['SERV']] = $_serv;
        }

        return $service;
    }
    // serv






    // 更新酒店列表
    static function hotel($country, $city=null)
    {
        // 正在更新，禁止重复更新
        self::$log_name = 'hmc_hotel_refresh_'.$country.($city ? "_{$city}" : '');
        if (self::log(self::$log_name)) return false;

        $condition = array('p_country'=>$country);
        if ($city)
            $condition['p_city'] = $city;

        $data = self::_request('qhotelcattype', $condition);

        if(!isset($data["XML_RESULT"]["HOTELS"]["HOTEL"])) return false;

        $hotels = empty($data['XML_RESULT']['HOTELS']['HOTEL'][0]) ? array($data['XML_RESULT']['HOTELS']['HOTEL']) : $data['XML_RESULT']['HOTELS']['HOTEL'];

        // 关闭所有原有酒店
        $where = "`country`=:country";
        $condition = array(':country' => $country);
        if ($city)
        {
            $where .= " AND `city`=:city";
            $condition[':city'] = $city;
        }

        $db = db(config('db'));
        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `sup_hmc_hotel` SET `isdel`=1 WHERE {$where}") -> execute($condition);
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        foreach ($hotels as $k => $v)
        {
            $hotel = array(
                'id'        => (string)$v['HOTEL'],
                'name'      => (string)$v['HOTELNAME'],
                'country'   => (string)$v['COUNTRY'],
                'city'      => (string)$v['CITY'],
                'address'   => (string)$v['ADDRESS'],
                'tel'       => (string)$v['TEL'],
                'lng'       => (string)$v['LONGITUDE'],
                'lat'       => (string)$v['LATITUDE'],
                'isdel'     => 0,
            );

            $old = $db -> prepare("SELECT `name`,`address`,`tel` FROM `sup_hmc_hotel` WHERE `id`=:id;") -> execute(array(':id'=>$v['HOTEL']));
            if (!$old)
                self::log(self::$log_name, "NEW HOTEL: {$hotel['id']} - {$hotel['name']}");
            else if ($up = array_diff_assoc($old[0], $hotel))
                self::log(self::$log_name, "UPDATE HOTEL: {$hotel['id']} - {$hotel['name']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));


            list($column, $sql, $value) = array_values(insert_array($hotel));
            $rs = $db -> prepare("REPLACE INTO `sup_hmc_hotel` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }

            $rs = self::room($v['ROOMS']['ROOM'], $v['HOTEL'], $db);
            if (!$rs)
            {
                $db -> rollback();
                return false;
            }
        }

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
    // hotel





    // 更新房型
    private static function room($rooms, $hotelid, $db)
    {
        $rooms = isset($rooms[0]) ? $rooms : array($rooms);

        $rs = $db -> prepare("UPDATE `sup_hmc_room` SET `isdel`=1 WHERE `hotel`=:hotel") -> execute(array(':hotel'=>$hotelid));
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        $data = array();
        foreach($rooms as $room)
        {
            $key = $hotelid.'_'.$room['CAT'].'_'.$room['TYPE'];
            $data[$key] = array(
                'hotel'     => $hotelid,
                'room'      => $room['CAT'],
                'roomname'  => $room['CATNAME'],
                'bed'       => $room['TYPE'],
                'bedname'   => $room['TYPENAME'],
                'wifi'      => (string)$room['WIFI'],
                'net'       => (string)$room['BOARDBAND'],
            );

            $old = $db  -> prepare("SELECT `roomname` FROM `sup_hmc_room` WHERE `hotel`=:hotel AND `room`=:room AND `bed`=:bed;")
                        -> execute(array(':hotel'=>$hotelid, ':room'=>$room['CAT'], ':bed'=>$room['TYPE']));
            if (!$old)
                self::log(self::$log_name, "\tNEW ROOM: {$data[$key]['room']} - {$data[$key]['roomname']}");
            else if ($up = array_diff_assoc($old[0], $data[$key]))
                self::log(self::$log_name, "\tUPDATE ROOM: {$data[$key]['room']} - {$data[$key]['roomname']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));
        }

        $data = array_values($data);
        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("REPLACE INTO `sup_hmc_room` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
        }

        return true;
    }
    // room





    // 国籍
    static function nation()
    {
        $data = self::_request('qnation');

        $db = db(config('db'));

        foreach ($data['XML_RESULT']['NATIONS'] as $v)
        {
            $nation = array(
                'code'  => (string)$v['NATION'],
                'name'  => (string)$v['NATIONNAME'],
            );

            $rs = true;
            $_nation = $db -> prepare("SELECT `bind`,`name` FROM `sup_hmc_nation` WHERE `code`=:code;") -> execute(array(':code'=>$nation['code']));
            if ($_nation)
            {
                if ($_nation[0]['name'] != $nation['name'])
                    $rs = $db -> prepare("UPDATE `sup_hmc_nation` SET `name`=:name WHERE `code`=:code;") -> execute(array(':code'=>$nation['code'], ':name'=>$nation['name']));
            }
            else
            {
                $rs = $db -> prepare("INSERT INTO `sup_hmc_nation` (`code`,`bind`,`name`) VALUES (:code, 0, :name);") -> execute(array(':code'=>$nation['code'], ':name'=>$nation['name']));
            }

            if ($rs === false) return false;
        }

        return true;
    }
    // nation





    // 更新国家
    static function country()
    {
        $data = self::_request('qcountry');

        $countrys = array();
        foreach ($data['XML_RESULT']['COUNTRIES'] as $country)
        {
            $countrys[] = array(
                'code'  => $country['COUNTRY'],
                'name'  => $country['COUNTRYNAME'],
                'isdel' => 0
            );
        }

        $db = db(config('db'));
        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `sup_hmc_country` SET `isdel`=1") -> execute();
        if ($rs === false)
        {
            $db -> rollback();
            return false;
        }

        list($column, $sql, $value) = array_values(insert_array($countrys));
        $rs = $db -> prepare("REPLACE INTO `sup_hmc_country` {$column} VALUES {$sql};") -> execute($value);
        if ($rs === false)
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





    // 更新城市
    static function city($country)
    {
        $data = self::_request('qcountryareacity', array('p_country' => $country)); //var_dump($data); exit;

        $citys = array();

        if ($data['XML_RESULT']['RETURN_CODE'] == 0 && empty($data['XML_RESULT']['COUNTRIES'])) return true;

        $areas = $data['XML_RESULT']['COUNTRIES']['AREAS'];
        if (empty($areas[0])) $areas = array($areas);

        foreach ($areas as $area)
        {
            if (empty($area['CITIES'][0]))
                $area['CITIES'] = array($area['CITIES']);

            foreach ($area['CITIES'] as $city)
            {
                $citys[] = array(
                    'code'      => $city['CITY'],
                    'name'      => $city['CITYNAME'],
                    'country'   => $country,
                    'isdel'     => 0,
                );
            }
        }

        $db = db(config('db'));
        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `sup_hmc_city` SET `isdel`=1 WHERE `country`=:code") -> execute(array(':code'=>$country));
        if ($rs === false)
        {
            $db -> rollback();
            return false;
        }

        list($column, $sql, $value) = array_values(insert_array($citys));
        $rs = $db -> prepare("REPLACE INTO `sup_hmc_city` {$column} VALUES {$sql};") -> execute($value);
        if ($rs === false)
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
    // city






    // 转义房型键值
    static function roomkey($room)
    {
        return "{$room['hotel']}_{$room['room']}_{$room['bed']}";
    }
    // roomkey




    // 解析房型键值
    static function parsekey($key)
    {
        list($hotel, $room, $bed) = explode('_', $key);
        return array('hotel'=>$hotel, 'room'=>$room, 'bed'=>$bed);
    }
    // parsekey



    // 转义床型
    static function bed($bed)
    {
        if(in_array($bed, array('S', 'T', 'D')))
            return $bed;

        if(in_array($bed, array('DB', 'QU'))) return 'D';

        if($bed == 'K') return 'K';

        return 'O';
    }
    // bed



    // 转义宽带
    static function net($net)
    {
        $_net = 0;
        if($net['wifi'] == '无线上网(免费)')
            $_net .= 1;

        if($net['wifi'] == '无线上网(收费)')
            $_net .= 2;

        if($net['net'] == '宽带上网(免费)')
            $_net .= 3;

        if($net['net'] == '宽带上网(收费)')
            $_net .= 4;

        return (int)$_net;
    }
    // net



}

?>