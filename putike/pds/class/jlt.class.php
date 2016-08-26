<?php
/**
 * 深捷旅供应商
 +-----------------------------------------
 * @category
 * @package jlt
 * @author nolan.zhou
 * @version $Id$
 */
class jlt extends supply implements supply_inf
{
    //全局网址
    private static $url = 'http://interface.jlfzg.com:90/commonQueryServlet';
    //private static $url = 'http://58.250.56.217:30000/commonQueryServlet'; // Test mode

    //验证信息
    public static $option = array('usercd'=>'SH19765', 'authno'=>'shptjltour');
    //public static $option = array('usercd'=>'SZ2747', 'authno'=>'123456'); // Test mode

    // 日志用名
    private static $log_name = '';

    // NATION id
    private static $nation_code = array(
        11 => '',
        12 => '中国大陆',
        13 => '日本',
        14 => '香港',
        15 => '俄罗斯',
        16 => '澳门',
    );


    // 发送请求
    public static function _request($method, $args=array())
    {
        $options = array_merge(array('querytype'=>$method), self::$option, $args);
        $query = json_encode($options);

        $header = array(
            'Content-type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($query)
        );

        $jsonStr = curl_file_get_contents(self::$url, $query, $header, 100000);

        if (isset($_GET['debug'])) { header("Content-type:text/html; charset=utf-8"); echo $jsonStr; exit; } //var_dump($jsonStr); exit;

        $json = json_decode($jsonStr, true);
        if (!$json)
        {
            if (strlen($jsonStr) >= 512000)
            {
                $file = PT_PATH.'log/jlt_'.$method.'_'.NOW.'.json';
                file_put_contents($file, $jsonStr);
                self::error('获取数据异常 Method:'.$method.' Args:'.http_build_query($args).' File:'.$file);
            }
            else
            {
                self::error('获取数据异常：'.$jsonStr);
            }
            return false;
        }

        unset($jsonStr);

        if ($json['success'] != 1)
        {
            self::error('获取数据异常：'.$json['msg']);
            return false;
        }

        return $json['data'];
    }
    // _request




    // _price
    static function _price($hotelcode, $roomcode='', $bedcode=null, $checkin=0, $checkout=0)
    {
        if(!$checkin) $checkin = strtotime("today");
        if(!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." -1 day +1 month");

        if($checkin >= $checkout)
            return false;

        // Time change
        $checkin = strtoupper(date('Y-m-d', $checkin));
        $checkout = strtoupper(date('Y-m-d', $checkout));

        $condition = array('checkindate'=>$checkin, 'checkoutdate'=>$checkout, 'hotelids'=>$hotelcode ? $hotelcode : '', 'roomtypeids'=>$roomcode ? str_replace(',', '/', $roomcode) : '');

        $data = self::_request('hotelpriceall', $condition);

        if(empty($data)) return array();

        $result = array();
        foreach ($data as $room)
        {
            $hotelcode = $room['hotelId'];
            $roomcode = $room['roomtypeId'];

            $nation = self::nation($room['noacceptcustomer'], $room['acceptcustomer']);

            foreach ($room['roomPriceDetail'] as $_price)
            {
                $package = $_price['ratetypename'];

                $time = strtotime(substr($_price['night'], 0, 10));

                $price = array(
                    'nation'    => $nation,
                    'min'       => $_price['termtype'] == 13 ? $_price['continuousdays'] : 0,           // 连住
                    'start'     => $_price['termtype'] == 12 ? strtotime(substr($_price['beginday'], 0, 10)) : 0,
                    'end'       => $_price['termtype'] == 12 ? strtotime(substr($_price['endday'], 0, 10)) : 0,
                    'advance'   => $_price['termtype'] == 11 ? $_price['advancedays'] : 0,              // 提前，连住和提前，存在于产品字段，应该属于长期不更新状态。
                    'breakfast' => (int)$_price['includebreakfastqty2'] == 34 ? 99 : ($_price['includebreakfastqty2'] == 10 ? 0 : floor($_price['includebreakfastqty2']/10)),
                    'package'   => $package,
                    'date'      => $time,
                    'price'     => (int)$_price['preeprice'],
                    'currency'  => $_price['currency'],
                    'rebate'    => 0,
                    'allot'     => $_price['qtyable'] > 0 ? ($_price['qtyable'] >= 9 ? 9 : $_price['qtyable']) : 0,
                    'filled'    => $_price['qtyable'] < 0 ? 1 : 0,
                );

                $price['key'] = $_price['keyid'];

                if (!isset($result[$time])) $result[$time] = array();

                if (isset($result[$time][$price['key']]) && self::_compare($result[$time][$price['key']], $price))
                    continue;

                $result[$time][$price['key']] = $price;
            }
        }

        return $result;
    }
    // price







    // price
    static function price($hotelcode='', $roomcode='', $bedcode=null, $checkin=0, $checkout=0)
    {
        if (!$checkin) $checkin = strtotime("today");
        if (!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." -1 day +1 month");

        if ($checkin >= $checkout)
            return false;

        // Time change
        $checkin = strtoupper(date('Y-m-d', $checkin));
        $checkout = strtoupper(date('Y-m-d', $checkout));

        $condition = array('checkindate'=>$checkin, 'checkoutdate'=>$checkout, 'hotelids'=>$hotelcode ? $hotelcode : '', 'roomtypeids'=>$roomcode ? $roomcode : '');

        $data = self::_request('hotelpriceall', $condition);

        if (empty($data)) return array();

        $result = array();
        foreach ($data as $room)
        {
            // Nation
            $nationname = self::nation($room['noacceptcustomer'], $room['acceptcustomer']);
            $nation = self::_data($room['acceptcustomer'].'-'.$room['noacceptcustomer'], 'nation', $nationname);
            if($nation === false) continue;

            // 搜索自己的房型数据，未配对，未添加的跳过
            $ourroom = self::_room(($hotelcode ? $hotelcode : $room['hotelId']), $room['roomtypeId'], $room['roomtypeName']);
            if (!$ourroom || !$ourroom['type']) continue;

            foreach ($room['roomPriceDetail'] as $_price)
            {
                if ($_price['pricingtype'] == 11) continue;

                // package
                $package = $_price['ratetypename'];
                $package = self::_data($_price['ratetype'], 'package', $_price['ratetypename']);
                if($package === false) continue;

                // currency
                $currency = self::_data($_price['currency'], 'currency');
                if ($currency === false) continue;

                $time = strtotime(substr($_price['night'], 0, 10));

                // 保存服务及相关订单所需资料
                $standby = array(
                    'KEY'   => $_price['keyid'],
                );

                $price = array(
                    'payment'   => $_price['pricingtype'] == 11 ? 0 : 1,
                    'hotel'     => $ourroom['hotel'],
                    'room'      => $ourroom['id'],
                    'bed'       => $ourroom['bed'],
                    'roomtype'  => $ourroom['type'],
                    'nation'    => $nation,
                    'package'   => $package,
                    'date'      => $time,
                    'price'     => (int)$_price['preeprice'],
                    'currency'  => $currency,
                    'rebate'    => 0,
                    'breakfast' => (int)$_price['includebreakfastqty2'] == 34 ? -1 : ($_price['includebreakfastqty2'] == 10 ? 0 : floor($_price['includebreakfastqty2']/10)),
                    'supply'    => 'JLT',
                    'supplyid'  => $_price['supplierid'],
                    'start'     => $_price['termtype'] == 12 ? strtotime(substr($_price['beginday'], 0, 10)) : 0,
                    'end'       => $_price['termtype'] == 12 ? strtotime(substr($_price['endday'], 0, 10)) : 0,
                    'min'       => $_price['termtype'] == 13 ? $_price['continuousdays'] : 0,           // 连住
                    'advance'   => $_price['termtype'] == 11 ? $_price['advancedays'] : 0,              // 提前，连住和提前，存在于产品字段，应该属于长期不更新状态。
                    'addbf'     => 0,
                    'addbe'     => 0,
                    'net'       => self::_net(array('type'=>$_price['internetprice'], 'price'=>$_price['netcharge'])),
                    'filled'    => $_price['qtyable'] < 0 ? 1 : 0,
                    'allot'     => $_price['qtyable'] > 0 ? ($_price['qtyable'] >= 9 ? 9 : $_price['qtyable']) : 0,
                    'standby'   => json_encode($standby, JSON_UNESCAPED_UNICODE),
                    'cutoff'    => 0,
                    'update'    => NOW,
                    'close'     => 0,
                );

                // KEY :       日期6,房型6,国籍3,预付/现付1,范围/提前/连住3,早餐数1,价格包3,供应商3,子供应商(合同号)-
                // uncombine : 房型3,国籍3,预付/现付1,范围/提前/连住3,子供应商6
                // combine :   早餐数1,价格包3
                $price['key'] = date('ymd', $time)
                    .str_pad(strtoupper(dechex($ourroom['id'])), 6, 0, STR_PAD_LEFT)
                    .str_pad(strtoupper(dechex($nation)), 3, 0, STR_PAD_LEFT)
                    .$price['payment']
                    .int2chr(round(($price['end']-$price['start'])/86400))
                    .int2chr($price['advance'])
                    .int2chr($price['min'])
                    .int2chr($price['breakfast'])
                    .str_pad(strtoupper(dechex($package)), 3, 0, STR_PAD_LEFT)
                    .'JLT'
                    .$price['supplyid'];

                $price['uncombine'] = substr($price['key'], 6, 13).substr($price['key'], 23);

                $price['combine'] = substr($price['key'], 19, 4);

                if (isset($result[$price['key']]) && self::_compare($result[$price['key']], $price))
                    continue;

                $result[$price['key']] = $price;
            }
        }

        return $result;
    }
    // price





    // 转义宽带（价格用）
    protected static function _net($net)
    {
        if ($net['type'] == 8) return 0;

        if ($net['type'] == 3)
        {
            return $net['price'] ? 1 : 2;
        }
        else
        {
            return $net['price'] ? 3 : 4;
        }
    }
    // net





    // 更新价格
    // 可以单独更新一个酒店或多个房型
    static function refresh($hotelcode='', $roomcode=null, $checkin=0, $checkout=0)
    {
        if (is_array($roomcode))
            $roomcode = implode(',', $roomcode);

        return self::_refresh($hotelcode, $roomcode, $checkin, $checkout);
    }
    // refresh





    // 更新酒店列表
    static function hotel($id = null)
    {
        // 正在更新，禁止重复更新
        self::$log_name = 'jlt_hotel_refresh'.($id ? "_{$id}" : '');
        if (self::log(self::$log_name)) return false;

        $db = db(config('db'));
        $db -> beginTrans();

        if (!$id)
        {
            $rs = $db -> prepare("UPDATE `sup_jlt_hotel` SET `isdel`=1 WHERE 1=1") -> execute();
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
            $start = 1;
            $end   = 30000;
        }
        else
        {
            $start = $id;
            $end   = $id;
        }

        for ($i=$start; $i<=$end; $i++)
        {
            $data = self::_request('hotelinfo', array('hotelids'=>$i));
            if ($data)
            {
                foreach ($data as $v)
                {
                    $hotel = array(
                        'id'        => (string)$v['hotelid'],
                        'name'      => (string)$v['namechn'],
                        'country'   => (string)$v['country'],
                        'city'      => (string)$v['city'],
                        'address'   => (string)$v['addresschn'],
                        'tel'       => (string)$v['centraltel'],
                        'isdel'     => $v['active'] == 1 ? 0 : 1,
                    );

                    $old = $db -> prepare("SELECT `name`,`address`,`tel` FROM `sup_jlt_hotel` WHERE `id`=:id;") -> execute(array(':id'=>$v['hotelid']));
                    if (!$old)
                        self::log(self::$log_name, "NEW HOTEL: {$hotel['id']} - {$hotel['name']}");
                    else if ($up = array_diff_assoc($old[0], $hotel))
                        self::log(self::$log_name, "UPDATE HOTEL: {$hotel['id']} - {$hotel['name']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));

                    list($column, $sql, $value) = array_values(insert_array($hotel));
                    $rs = $db -> prepare("REPLACE INTO `sup_jlt_hotel` {$column} VALUES {$sql};") -> execute($value);
                    if (false === $rs)
                    {
                        $db -> rollback();
                        return false;
                    }

                    $rs = self::room($v['rooms'], $v['hotelid'], $db);
                    if (!$rs)
                    {
                        $db -> rollback();
                        return false;
                    }
                }
            }
            else
            {
                if($data === false) self::error('更新酒店错误：'.$i, true);
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
        $rs = $db -> prepare("UPDATE `sup_jlt_room` SET `isdel`=1 WHERE `hotel`=:hotel") -> execute(array(':hotel'=>$hotelid));
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        $bed = array(
            'single'    => '单床',
            'double'    => '双床',
            'big'       => '大床',
            'cir'       => '圆床',
            'sindou'    => '单床/双床',
            'bigdou'    => '大床/双床',
            'bigsing'   => '大床/单床',
        );

        $data = array();
        foreach($rooms as $room)
        {
            $key = $room['roomtypeid'];
            $data[$key] = array(
                'hotel'     => $hotelid,
                'room'      => $room['roomtypeid'],
                'roomname'  => $room['namechn'],
                'bed'       => $room['bedtype'],
                'bedname'   => isset($bed[$room['bedtype']]) ? $bed[$room['bedtype']] : '',
                'net'       => (string)$room['nettype'],
                'isdel'     => $room['active'] == 8 ? 1 : 0,
            );

            $old = $db  -> prepare("SELECT `roomname`, `bedname` FROM `sup_jlt_room` WHERE `room`=:room;") -> execute(array(':room'=>$room['roomtypeid']));
            if (!$old)
                self::log(self::$log_name, "\tNEW ROOM: {$data[$key]['room']} - {$data[$key]['roomname']}");
            else if ($up = array_diff_assoc($old[0], $data[$key]))
                self::log(self::$log_name, "\tUPDATE ROOM: {$data[$key]['room']} - {$data[$key]['roomname']} OLD : ".json_encode($up, JSON_UNESCAPED_UNICODE));
        }

        $data = array_values($data);
        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("REPLACE INTO `sup_jlt_room` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
        }

        return true;
    }
    // room





    // 国籍要求组合
    private static function nation($nonaccept, $accept)
    {
        $nonaccept = explode(',', $nonaccept);
        $_nonaccept = array();
        foreach ($nonaccept as $v)
        {
            if (!$v) continue;
            $_nonaccept[] = empty(self::$nation_code[$v]) ? $v : self::$nation_code[$v];
        }

        $accept = explode(',', $accept);
        $_accept = array();
        foreach ($accept as $v)
        {
            if (!$v || $v == 11) continue;
            $_accept[] = empty(self::$nation_code[$v]) ? $v : self::$nation_code[$v];
        }

        $name = '';
        if ($_accept)
        {
            $name = implode(',', $_accept);
        }

        if ($_nonaccept)
        {
            $_nonaccept = implode(',', $_nonaccept);
            $name .= $name ? "（除{$_nonaccept}）" : "除{$_nonaccept}";
        }

        return $name ? $name.'宾客' : '';
    }
    // nation








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
        $beds = array(
            'single'    => 'S',
            'double'    => 'T',
            'big'       => 'D',
            'cir'       => 'C',
            'sindou'    => '2',
            'bigdou'    => '2',
            'bigsing'   => 'D',
        );

        return isset($beds[$bed]) ? $beds[$bed] : 'O';
    }
    // bed



    // 转义宽带
    static function net($net)
    {
        if ($net['net'] == 8) return 0;
        return 5;
    }
    // net




}

?>
