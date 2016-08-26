<?php
/**
 * 港捷旅供应商
 +-----------------------------------------
 * @category
 * @package cnb
 * @author nolan.zhou
 * @version $Id$
 */
class cnb extends supply implements supply_inf
{
    //全局网址
    //private static $url = 'http://test.api.cnbooking.net:6677/RESTServer.asmx/GetXmlData';
    private static $url = 'http://api.cnbooking.net:8003/RESTServer.asmx/GetXmlData';

    //验证信息
    private static $option = array('AppID'=>'1', 'SecurityKey'=>'d8bf0b5f-7b1c-4298-95b0-1077d97bf870', 'UserName'=>'putike', 'PassWord'=>'fu3ra67GAYJZkGs0hd9IGlwqy0E=', 'Signature'=>'jsXdpMVmyD4ZtSvqQfQyyzpM4OBxqFth3rCwSbPWwFnmdpjcMYIwp74AMN5+wdG/QBOcfXyrHNo20hKP4fO6u8tJQpEAug1H+/28r9oJ78PPKqc96gecOmno6huwuPLm+KF4eakH3N9jEJdQx7v0hTv6K8Y0tvLwK14raaMxlG8=');

    // 日志用名
    private static $log_name = '';


    // 发送请求
    public static function _request($method, $args=array(), $orderby='Id', $page=-1, $limit=0)
    {
        global $m;
        $condition = '';
        if ($args)
        {
            foreach ($args as $k => $v)
                $condition .= "        <{$k}>{$v}</{$k}>\r\n";
        }

        $request = '<?xml version="1.0" encoding="utf-8"?>
<CNRequest>
    <ActionName>'.$method.'</ActionName>
    <Header>
        <SessionID></SessionID>
        <Invoker></Invoker>
    </Header>
    <IdentityInfo>
        <AppID>'.self::$option['AppID'].'</AppID>
        <SecurityKey>'.self::$option['SecurityKey'].'</SecurityKey>
        <UserName>'.self::$option['UserName'].'</UserName>
        <PassWord>'.self::$option['PassWord'].'</PassWord>
        <Signature>'.self::$option['Signature'].'</Signature>
    </IdentityInfo>
    <ScrollingInfo>
        <DisplayReq>'.($page == -1 ? '40' : '30').'</DisplayReq>
        <PageItems>'.$limit.'</PageItems>
        <PageNo>'.($page == -1 ? '' : $page).'</PageNo>
        <OrderField>'.$orderby.'</OrderField>
        <OrderType>0</OrderType>
    </ScrollingInfo>'.($condition ? ("\r\n    <SearchConditions>\r\n{$condition}    </SearchConditions>\r\n") : '').'
</CNRequest>';

        if (!empty($_GET['debug']) && $_GET['debug'] == 'request') { header("Content-type:text/xml"); echo $request."\n"; exit; }

        //获取
        $postdata = "xmlRequest=".urlencode($request);

        $header   = array("Content-type: application/x-www-form-urlencoded", "Content-Length: ".strlen($postdata));

        $xmlStr = curl_file_get_contents(self::$url, $postdata, $header, 120);
        $xmlstring = htmlspecialchars_decode(substr($xmlStr, 84, -9));

        if (isset($_GET['debug']) && $_GET['debug'] == 'xml') { header("Content-type:text/xml"); echo $xmlstring; exit; }


        $xml = simplexml_load_string($xmlstring);
        if (!$xml)
        {
            self::error('获取数据异常：'.$xmlStr);
            return false;
        }

        $xmlArr = parse_xml($xml); //var_dump($xmlArr); exit;
        unset($xml, $xmlStr);


        if (isset($_GET['debug']) && $_GET['debug'] == 'arr') { var_dump($xmlArr); exit; }


        if ($xmlArr['MessageInfo']['Code'] != 30000)
        {
            if ($xmlArr['MessageInfo']['Code'] == 'S_010') return array();
            self::error("请求错误 Method:{$method} Args:".http_build_query($args)." Res:{$xmlstring}");
            return false;
        }

        return $xmlArr;
    }
    // _request




    // _price
    static function _price($hotelcode, $roomcode='', $bedcode=null, $checkin=0, $checkout=0)
    {
        if (!$checkin) $checkin = strtotime("today");
        if (!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." +27 day");

        if ($checkin >= $checkout)
            return false;

        // Time change
        $checkin = date('Y-m-d', $checkin);
        $checkout = date('Y-m-d', $checkout);

        $condition = array('CheckInDate'=>$checkin, 'CheckOutDate'=>$checkout, 'PayMode'=>'06002', 'HotelCode'=>$hotelcode, 'RoomTypeCode'=>$roomcode, 'RateMin'=>0, 'RateMax'=>0, 'Currency'=>'CNB', 'Obligatestr1'=>'', 'Obligatestr2'=>'');

        $data = self::_request('SimpleRoomPriceInfo', $condition);

        // Clear Old Data
        if (!isset($data['Data']['HotelsInfo']['RoomTypes']))
            return array();

        if (isset($data['Data']['HotelsInfo']['RoomTypes']['RoomType'][0]))
            $prices = &$data['Data']['HotelsInfo']['RoomTypes']['RoomType'];
        else
            $prices = array(&$data['Data']['HotelsInfo']['RoomTypes']['RoomType']);

        $result = array();
        foreach ($prices as $_price)
        {
            // time
            $time = strtotime($_price['TheDate']);

            $breakfast = 0;
            switch ($_price['breakfast'])
            {
                case '25001': $breakfast = 0; break; //不含早  25001
                case '25002': $breakfast = 1; break; //单早    25002
                case '25003': $breakfast = 2; break; //双早    25003
                case '25004': $breakfast = -1; break; //单双早  25004
                case '25001': $breakfast = -1; break; //床位早  25007
                case '25001': $breakfast = 3; break; //含三早  25008
                case '25001': $breakfast = 6; break; //含六早  25010
                case '25001': $breakfast = 4; break; //含四早  25011
            }

            $price = array(
                'nation'    => '',
                'package'   => '',
                'date'      => $time,
                'price'     => (int)$_price['SalePrice'],
                'currency'  => $_price['currency'] == 'CNB' ? 'RMB' : $_price['currency'],
                'rebate'    => 0,
                'breakfast' => $breakfast,
                'start'     => 0,
                'end'       => 0,
                'min'       => 0,
                'advance'   => (int)$_price['InAdvanceDayNum'],              // 提前，连住和提前，存在于产品字段，应该属于长期不更新状态。
                'filled'    => $_price['RoomState'] == 'C' ? 1 : 0,
                'allot'     => is_numeric($_price['RoomState']) ? $_price['RoomState'] : 0,
            );

            // KEY :       日期6,房型6,国籍3,预付/现付1,范围/提前/连住3,早餐数1,价格包3,供应商3,子供应商(合同号)-
            // uncombine : 房型3,国籍3,预付/现付1,范围/提前/连住3,子供应商6
            // combine :   早餐数1,价格包3
            $price['key'] = date('ymd', $time).$_price['PriceDetailID'];

            if (!isset($result[$time])) $result[$time] = array();

            if (isset($result[$time][$price['key']]) && self::_compare($result[$time][$price['key']], $price))
                continue;

            $result[$time][$price['key']] = $price;
        }

        return $result;
    }
    // price






    // price
    static function price($hotelcode, $roomcode='', $bedcode='', $checkin=0, $checkout=0)
    {
        if (!$checkin) $checkin = strtotime("today");
        if (!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." +27 day");

        if ($checkin >= $checkout)
            return false;

        // Time change
        $checkin = date('Y-m-d', $checkin);
        $checkout = date('Y-m-d', $checkout);

        $condition = array('CheckInDate'=>$checkin, 'CheckOutDate'=>$checkout, 'PayMode'=>'06002', 'HotelCode'=>$hotelcode, 'RoomTypeCode'=>$roomcode, 'RateMin'=>0, 'RateMax'=>0, 'Currency'=>'CNB', 'Obligatestr1'=>'', 'Obligatestr2'=>'');

        $data = self::_request('SimpleRoomPriceInfo', $condition);

        // Clear Old Data
        if (!isset($data['Data']['HotelsInfo']['RoomTypes']))
            return array();

        $prices = isset($data['Data']['HotelsInfo']['RoomTypes']['RoomType'][0]) ? $data['Data']['HotelsInfo']['RoomTypes']['RoomType'] : array($data['Data']['HotelsInfo']['RoomTypes']['RoomType']);
        unset($data);

        $result = array();

        foreach ($prices as $_price)
        {
            // 搜索自己的房型数据，未配对，未添加的跳过
            $ourroom = self::_room($hotelcode, $_price['RoomTypeID'], $_price['RoomName']);
            if (!$ourroom || !$ourroom['type']) continue;

            // currency
            $currency = self::_data($_price['currency'] == 'CNB' ? 'RMB' : $_price['currency'], 'currency');
            if ($currency === false) continue;

            // time
            $time = strtotime($_price['TheDate']);

            // 保存服务及相关订单所需资料
            $standby = array('PriceDetailID'=>$_price['PriceDetailID']);

            $breakfast = 0;
            switch ($_price['breakfast'])
            {
                case '25001': $breakfast = 0; break; //不含早  25001
                case '25002': $breakfast = 1; break; //单早    25002
                case '25003': $breakfast = 2; break; //双早    25003
                case '25004': $breakfast = -1; break; //单双早  25004
                case '25001': $breakfast = -1; break; //床位早  25007
                case '25001': $breakfast = 3; break; //含三早  25008
                case '25001': $breakfast = 6; break; //含六早  25010
                case '25001': $breakfast = 4; break; //含四早  25011
            }

            $addbf = 0;
            $addbe = 0;
            $addition = isset($_price['Additions']['Addition'][0]) ? $_price['Additions']['Addition'] : array($_price['Additions']['Addition']);
            foreach ($addition as $add)
            {
                if ($add['CategoryCode'] == '28001')
                {
                    $standby['ABF'] = "{$add['Name']},{$add['TuoteLimit']},{$add['Currency']}";
                    $addbf = $add['Price'];
                }
                if ($add['CategoryCode'] == '28002')
                {
                    $standby['ABE'] = "{$add['Name']},{$add['TuoteLimit']},{$add['Currency']}";
                    $addbe = $add['Price'];
                }
            }

            $_roomdata = $ourroom['data'] ? json_decode($ourroom['data'], true) : null;

            $price = array(
                'payment'   => 1,
                'hotel'     => $ourroom['hotel'],
                'room'      => $ourroom['id'],
                'bed'       => $ourroom['bed'],
                'roomtype'  => $ourroom['type'],
                'nation'    => empty($_roomdata['nation']) ? 0 : (int)$_roomdata['nation'],
                'package'   => empty($_roomdata['package']) ? 0 : (int)$_roomdata['package'],
                'date'      => $time,
                'price'     => (int)$_price['SalePrice'],
                'currency'  => $currency,
                'rebate'    => 0,
                'breakfast' => $breakfast,
                'supply'    => 'CNB',
                'supplyid'  => '',
                'start'     => 0,
                'end'       => 0,
                'min'       => empty($_roomdata['min']) ? 0 : (int)$_roomdata['min'],
                'advance'   => empty($_roomdata['advance']) ? (int)$_price['InAdvanceDayNum'] : (int)$_roomdata['advance'],              // 提前，连住和提前，存在于产品字段，应该属于长期不更新状态。
                'addbf'     => $addbf,
                'addbe'     => $addbe,
                'net'       => $ourroom['net'],
                'filled'    => $_price['RoomState'] == 'C' ? 1 : 0,
                'allot'     => is_numeric($_price['RoomState']) ? $_price['RoomState'] : 0,
                'standby'   => json_encode($standby, JSON_UNESCAPED_UNICODE),
                'cutoff'    => strtotime($_price['CutOffTime']) - strtotime('00:00:00'),
                'update'    => NOW,
                'close'     => 0,
            );

            // KEY :       日期6,房型6,国籍3,预付/现付1,范围/提前/连住3,早餐数1,价格包3,供应商3,子供应商(合同号)-
            // uncombine : 房型3,国籍3,预付/现付1,范围/提前/连住3,子供应商6
            // combine :   早餐数1,价格包3
            $price['key'] = date('ymd', $time)
                .str_pad(strtoupper(dechex($ourroom['id'])), 6, 0, STR_PAD_LEFT)
                .str_pad(strtoupper(dechex($price['nation'])), 3, 0, STR_PAD_LEFT)
                .$price['payment']
                .'0'
                .int2chr($price['advance'])
                .int2chr($price['min'])
                .int2chr($price['breakfast'])
                .str_pad(strtoupper(dechex($price['package'])), 3, 0, STR_PAD_LEFT)
                .'CNB';

            $price['uncombine'] = substr($price['key'], 6, 13).substr($price['key'], 23);

            $price['combine'] = substr($price['key'], 19, 4);

            if (isset($result[$price['key']]) && self::_compare($result[$price['key']], $price))
                continue;

            $result[$price['key']] = $price;
        }

        return $result;
    }
    // price






    // 更新价格
    // 仅能更新一个酒店
    static function refresh($hotelcode, $roomcode='', $checkin=0, $checkout=0)
    {
        //$roomcode = '';
        return self::_refresh($hotelcode, $roomcode, $checkin, $checkout);
    }
    // refresh






    // 更新酒店列表
    static function hotel($country, $city=null, $hotelkey=null)
    {
        // request hotel api
        if (!$hotelkey)
        {
            self::$log_name = 'cnb_hotel_refresh_'.($country.($city ? "_{$city}" : ''));
            if (self::log(self::$log_name)) return false;
        }

        $db = db(config('db'));

        // search city
        $where = "`country`=:country";
        $condition = array(':country' => $country);
        if ($city)
        {
            $where .= " AND `city`=:city";
            $condition[':city'] = $city;
        }
        else
        {
            // ready for citys name
            $_citys = $db -> prepare("SELECT * FROM `sup_cnb_city` WHERE `country`=:country;") -> execute(array(':country'=>$country));
            $citys = array();
            foreach ($_citys as $v)
            {
                $citys[$v['name']] = $v['code'];
            }
            unset($_citys);
        }

        if ($hotelkey)
        {
            $where = "`id`=:id";
            $condition = array(':id' => $hotelkey);
        }

        $db -> beginTrans();

        // close all hotel
        $rs = $db -> prepare("UPDATE `sup_cnb_hotel` SET `isdel`=1 WHERE {$where}") -> execute($condition);
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        $condition = array('HotelCode'=>'', 'HotelName'=>'', 'Country'=>$country, 'Province'=>'', 'City'=>'', 'District'=>'', 'RoomTypeCode'=>'', 'StarLevel'=>'');
        if ($city)
            $condition['City'] = $city;

        if ($hotelkey)
        {
            $condition['HotelCode'] = $hotelkey;
            $condition['Country'] = '';
            $condition['City'] = '';
        }

        // search 200 hotels every times;
        $j = 1;
        for ($i = 1; $i <= $j; $i++)
        {
            $data = self::_request('HotelSearch', $condition, 'Id', $i, 200);

            if(!isset($data["Data"]["HotelsInfo"]["HotelList"])) return false;

            if ($i == 1)
            {
                $count = (int)$data["Data"]["HotelsInfo"]["HotelNumber"];
                $j = ceil($count / 200);
            }

            $hotels = empty($data['Data']['HotelsInfo']['HotelList'][0]) ? array($data['Data']['HotelsInfo']['HotelList']) : $data['Data']['HotelsInfo']['HotelList'];
            unset($data);

            foreach ($hotels as $k => $v)
            {
                $hotel = array(
                    'id'        => (string)$v['HotelCode'],
                    'name'      => (string)$v['CnName'],
                    'country'   => (string)$country,
                    'city'      => $city ? $city : (empty($citys[$v['City']]) ? '' : $citys[$v['City']]),
                    'address'   => (string)$v['CnAddress'],
                    'tel'       => '',
                    'lng'       => (string)$v['Longitude'],
                    'lat'       => (string)$v['Latitude'],
                    'isdel'     => 0,
                );

                $old = $db -> prepare("SELECT `name`,`address`,`tel` FROM `sup_cnb_hotel` WHERE `id`=:id;") -> execute(array(':id'=>$v['HotelCode']));
                if (!$hotelkey)
                {
                    if (!$old)
                        self::log(self::$log_name, "NEW HOTEL: {$hotel['id']} - {$hotel['name']}");
                    else if ($up = array_diff_assoc($old[0], $hotel))
                        self::log(self::$log_name, "UPDATE HOTEL: {$hotel['id']} - {$hotel['name']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));
                }

                list($column, $sql, $value) = array_values(insert_array($hotel));
                $rs = $db -> prepare("REPLACE INTO `sup_cnb_hotel` {$column} VALUES {$sql};") -> execute($value);
                if (false === $rs)
                {
                    $db -> rollback();
                    return false;
                }

                if (!$v['RoomTypes']['RoomType']) continue;

                $rs = self::room($v['RoomTypes']['RoomType'], $v['HotelCode'], $db);
                if (!$rs)
                {
                    $db -> rollback();
                    return false;
                }
            }
        }

        if ($db -> commit())
        {
            if (!$hotelkey) self::log(self::$log_name, true);
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

        $rs = $db -> prepare("UPDATE `sup_cnb_room` SET `isdel`=1 WHERE `hotel`=:hotel") -> execute(array(':hotel'=>$hotelid));
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        $data = array();
        foreach($rooms as $room)
        {
            $key = $room['RoomTypeID'];
            $data[$key] = array(
                'hotel'     => $hotelid,
                'room'      => $room['RoomTypeID'],
                'roomname'  => $room['RoomName'],
                'bed'       => $room['Sys_BedType_ID'],
                'bedname'   => self::bed($room['Sys_BedType_ID']),
                'net'       => $room['IsHasNet'] ? '1' : '0',
                'netfee'    => $room['IsNetHasFee'] ? (string)$room['NetFeeNum'] : ($room['IsHasNet'] ? '免费' : ''),
            );

            $old = $db  -> prepare("SELECT `roomname`,`bed`,`net`,`netfee` FROM `sup_cnb_room` WHERE `hotel`=:hotel AND `room`=:room AND `bed`=:bed;")
                        -> execute(array(':hotel'=>$hotelid, ':room'=>$room['RoomTypeID'], ':bed'=>$room['Sys_BedType_ID']));

            if (self::$log_name)
            {
                if (!$old)
                    self::log(self::$log_name, "\tNEW ROOM: {$data[$key]['room']} - {$data[$key]['roomname']}");
                else if ($up = array_diff_assoc($old[0], $data[$key]))
                    self::log(self::$log_name, "\tUPDATE ROOM: {$data[$key]['room']} - {$data[$key]['roomname']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));
            }
        }

        $data = array_values($data);
        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("REPLACE INTO `sup_cnb_room` {$column} VALUES {$sql};") -> execute($value);
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
    static function bedtype()
    {
        $data = self::_request('GetBedType');

        $_beds = isset($data['BedTypeList']['BedType'][0]) ? $data['BedTypeList']['BedType'] : array($data['BedTypeList']['BedType']);

        $beds = array();
        foreach ($_beds as $v)
        {
            $name = $v['BedTypeName'];

            $type = '';
            $type .= strpos($name, '双') ? 'T' : '';
            $type .= strpos($name, '大') ? 'D' : '';
            $type .= strpos($name, '单') ? 'S' : '';
            $type .= strpos($name, '圆') ? 'C' : '';

            if ($type == 'TD') $type = 2;
            else if ($type == 'TS') $type = 'T';
            else if ($type == 'DS') $type = 'D';
            else if ($type == 'TDS') $type = 2;
            else if (strlen($type) > 1) $type = 'O';
            else if (!$type) $type = 'O';

            $beds[] = array('code'=>$v['BedTypeID'], 'type'=>$type, 'name'=>$v['BedTypeName']);
        }

        $db = db(config('db'));

        list($column, $sql, $value) = array_values(insert_array($beds));
        $rs = $db -> prepare("REPLACE INTO `sup_cnb_bed` {$column} VALUES {$sql};") -> execute($value);

        return $rs === false ? false : true;
    }
    // bedtype





    // 国籍
    static function nation()
    {
        /*
            外宾情况仅用于下单，查询时不显示
            0  内宾
            1  外宾
            2  内外宾
         */
        return true;
    }
    // nation





    // 更新国家
    static function country()
    {
        $data = self::_request('DistrictSearch');

        $countrys = array();
        foreach ($data['Countries']['Country'] as $country)
        {
            $countrys[] = array(
                'code'  => $country['CountryID'],
                'name'  => $country['CountryName'],
                'isdel' => 0
            );
        }

        $db = db(config('db'));
        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `sup_cnb_country` SET `isdel`=1") -> execute();
        if ($rs === false)
        {
            $db -> rollback();
            return false;
        }

        list($column, $sql, $value) = array_values(insert_array($countrys));
        $rs = $db -> prepare("REPLACE INTO `sup_cnb_country` {$column} VALUES {$sql};") -> execute($value);
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
        $data = self::_request('SearchDistrictInfo', array('CountryCode' => $country)); //var_dump($data); exit;

        $citys = array();

        $areas = $data['Data']['DistrictInfo']['DistrictList'];
        if (empty($areas[0])) $areas = array($areas);

        foreach ($areas as $area)
        {
            if (empty($area['Citys']['City'][0]))
                $area['Citys']['City'] = array($area['Citys']['City']);

            foreach ($area['Citys']['City'] as $city)
            {
                $citys[] = array(
                    'code'      => $city['CityCode'],
                    'name'      => $city['CityName'],
                    'country'   => $country,
                    'isdel'     => 0,
                );
            }
        }

        $db = db(config('db'));
        $db -> beginTrans();

        $rs = $db -> prepare("UPDATE `sup_cnb_city` SET `isdel`=1 WHERE `country`=:code") -> execute(array(':code'=>$country));
        if ($rs === false)
        {
            $db -> rollback();
            return false;
        }

        list($column, $sql, $value) = array_values(insert_array($citys));
        $rs = $db -> prepare("REPLACE INTO `sup_cnb_city` {$column} VALUES {$sql};") -> execute($value);
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
        return $room['room'];
    }
    // roomkey




    // 解析房型键值
    static function parsekey($key)
    {
        return array('room'=>$key);
    }
    // parsekey



    // 转义床型
    static function bed($bed)
    {
        static $beds = array();

        if (!$bed) return 'O';

        if (isset($beds[$bed])) return $beds[$bed];

        $db = db(config('db'));
        $_bed = $db -> prepare("SELECT `code`, `type` FROM `sup_cnb_bed` WHERE `code`=:code;") -> execute(array(':code'=>$bed));
        if (!$_bed)
        {
            self::error('供应商床型信息需要更新！'.var_export($bed, true), true);
            return false;
        }

        $beds[$bed] = $_bed[0]['type'];
        return $_bed[0]['type'];
    }
    // bed




    // 转义宽带
    static function net($net)
    {
        $_net = 0;
        if (!$net['net']) return $_net;

        if($net['netfee'] == '免费' || $net['netfee'] == '0.00' || $net['netfee'] == '0')
            $_net = 3;
        else
            $_net = 4;

        return (int)$_net;
    }
    // net



}

?>
