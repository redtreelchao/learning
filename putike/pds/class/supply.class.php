<?php
/**
 * 供应商基础类
 +-----------------------------------------
 * @category
 * @package supply
 * @author nolan.zhou
 * @version $Id$
 */
abstract class supply
{


    // 异常处理
    static function error($error, $email=false)
    {
        $supply = get_called_class();
        $date = date('H:i:s', NOW);
        error_log("[{$date}] {$error}\r\n", 3, PT_PATH."log/supply_{$supply}_".date('Ymd').'.log');

        if ($email)
        {
            import(CLASS_PATH.'extend/email');
            $email = new email('PUTIKE API SYSTEM<system@putike.cn>', 'smtp.exmail.qq.com', 25, 'system@putike.cn', 'ptk123456');
            $email -> send('nolan.zhou@putike.cn', "Api {$supply} Auto-Email", charset_convert("{$supply} : {$error}", 'utf-8', 'gb2312'), '', 'jacky.yan@putike.cn');
        }
    }
    // error





    // 日志记录
    static function log($filename, $message=null)
    {
        $file = PT_PATH.'log/'.$filename.'.log';
        if ($message === null)
        {
            // 判断当前文件是否正在写入
            return is_file($file);
        }
        else if ($message === true)
        {
            // 记录完成，修改文件名称
            rename($file, PT_PATH.'log/'.$filename.date('_Ymd_His').'.log');
        }
        else
        {
            file_put_contents($file, $message."\n\n", FILE_APPEND);
        }
    }
    // log






    // refresh
    static function _refresh($hotelcode='', $roomcode='', $checkin=0, $checkout=0)
    {
        //return true;
        if(!$checkin) $checkin = strtotime("today");
        if(!$checkout) $checkout = strtotime(date('Y-m-d', $checkin)." -1 day +1 month");

        $supply = strtoupper(get_called_class());

        $db = db(config('db'));
        $where = '';
        $condition = array();

        if ($checkin && $checkout)
        {
            $where = " AND `date`>=:checkin AND `date`<:checkout";
            $condition = array(':checkin'=>$checkin, ':checkout'=>$checkout);
        }

        // 开始记录事务
        $db -> beginTrans();

        // 关闭旧的价格数据
        if ($roomcode)
        {
            $_rooms = $db -> prepare("SELECT `id`,`hotel` FROM `ptc_hotel_room` WHERE `supply`='{$supply}' AND `key` IN ({$roomcode});") -> execute();
            if (!$_rooms) return true;

            $rooms = array();
            foreach ($_rooms as $v)
                $rooms[] = $v['id'];

            if (!$rooms) return true;
            $rooms = implode(',', $rooms);

            $close_sql = "UPDATE `ptc_hotel_price_date` SET `close`=1 WHERE `supply`='{$supply}' AND `room` IN ({$rooms}) {$where};";
        }
        else
        {
            $_hotels = $db -> prepare("SELECT `id` FROM `ptc_hotel` WHERE `{$supply}` = '{$hotelcode}';") -> execute();
            if (!$_hotels) return true;

            $close_sql = "UPDATE `ptc_hotel_price_date` SET `close`=1 WHERE `supply`='{$supply}' AND `hotel` = '{$_hotels[0]['id']}' {$where};";
        }

        // 获取更新新的酒店数据
        $supply = strtolower($supply);
        $data = $supply::price($hotelcode, $roomcode, '', $checkin, $checkout);
        if (false === $data) return false;


        // 先关闭所有相关价格
        $rs = $db -> prepare($close_sql) -> execute($condition);
        if (false === $rs)
        {
            $db -> rollback();
            return false;
        }

        // 更新恢复数据
        if ($data)
        {
            // 解析数据
            $data = array_values($data);
            list($column, $sql, $value) = array_values(insert_array($data));

            $_columns = update_column(array_keys($data[0]));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_price_date` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE {$_columns};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
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
    // refresh



    // 房型追加或更新
    static function _update_room($hotel, $key, $roomname, $bed, $net)
    {
        $supply = strtoupper(get_called_class());
        $db = db(config('db'));

        $hotel = $db -> prepare("SELECT `id` FROM `ptc_hotel` WHERE `{$supply}`=:hotel") -> execute(array(':hotel'=>$hotel));
        if (!$hotel) return true;

        $room = $db -> prepare("SELECT * FROM `ptc_hotel_room` WHERE `supply`=:supply AND `key`=:key AND `isdel`=0") -> execute(array(':supply'=>$supply, ':key'=>$key));

        $data = array(
            'type'  => 0,
            'name'  => $roomname,
            'bed'   => $supply::bed($bed),
            'net'   => $supply::net($net),
        );

        if (!$room)
        {
            // 检索酒店
            $data['hotel']  = $hotel[0]['id'];
            $data['supply'] = $supply;
            $data['key']    = $key;
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_room` {$column} VALUES {$sql};") -> execute($value);
        }
        else
        {
            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = (int)$room[0]['id'];
            $rs = $db -> prepare("UPDATE `ptc_hotel_room` SET {$sql} WHERE `id`=:id;") -> execute($value);
        }

        return $rs === false ? false : true;
    }
    // _update_room




    // 自房型检索
    static function _room($hotelkey, $key='', $roomname='')
    {
        static $cache = array();

        $supply = strtoupper(get_called_class());
        $skey = $supply.'_'.$key;

        if(isset($cache[$skey]))
            return $cache[$skey];

        $db = db(config('db'));
        $rooms = $db -> prepare("SELECT * FROM `ptc_hotel_room` WHERE `supply`=:supply AND `key`=:key AND `isdel`=0") -> execute(array(':supply'=>$supply, ':key'=>$key));

        if(!$rooms)
        {
            $cache[$skey] = false;
            $hotel = $db -> prepare("SELECT `id` FROM `ptc_hotel` WHERE `{$supply}`=:code") -> execute(array(':code'=>$hotelkey));
            if ($hotel)
                self::error("Hotel:{$hotelkey} Have a NEW Room:{$skey} - {$roomname}");

            return false;
        }

        if(count($rooms) > 1)
        {
            $cache[$skey] = false;
            self::error("Hotel:{$hotelkey} Have a DUPLICATE Room:{$skey} - {$roomname}", true);
            return false;
        }

        $cache[$skey] = $rooms[0];
        return $rooms[0];
    }
    // room





    // 数据检索
    static function _data($code, $type, $txt='')
    {
        // 原始数据，防止单进程多次访问
        static $data = array();
        if(!isset($data[$type])) $data[$type] = array();

        $supply = strtolower(get_called_class());

        $newcode = $supply.'_'.($code ? $code : md5($txt));
        if(isset($data[$type][$newcode])) return $data[$type][$newcode];

        $db = db(config('db'));

        switch($type)
        {
            // 货币
            case 'currency':
                $currency = $db -> prepare("SELECT * FROM `ptc_currency` WHERE `code`=:code") -> execute(array(':code'=>$code));
                if ($currency)
                {
                    $data[$type][$newcode] = $currency[0]['id'];
                    return $currency[0]['id'];
                }
                else
                {
                    self::error('New Currency :'.$code);
                    return false;
                }

            // 国籍
            case 'nation':
                $nation = $db -> prepare("SELECT * FROM `sup_{$supply}_nation` WHERE `code`=:code") -> execute(array(':code'=>$code));
                if ($nation)
                {
                    if ($nation[0]['new']) return false;
                    $data[$type][$newcode] = $nation[0]['bind'];
                    return $nation[0]['bind'];
                }
                break;

            // 服务包
            case 'package':
                if ($code)
                    $package = $db -> prepare("SELECT * FROM `sup_{$supply}_package` WHERE `code`=:code") -> execute(array(':code'=>$code));
                else
                    $package = $db -> prepare("SELECT * FROM `sup_{$supply}_package` WHERE `name`=:name") -> execute(array(':name'=>$txt));

                if ($package)
                {
                    if ($package[0]['new']) return false;
                    $data[$type][$newcode] = $package[0]['bind'];
                    return $package[0]['bind'];
                }
                break;
        }

        $db -> prepare("INSERT INTO `sup_{$supply}_{$type}` (`code`,`name`,`new`) VALUES (:code,:name,1)") -> execute(array(':code'=>$code, ':name'=>$txt));
        $data[$type][$newcode] = false;
        return false;
    }




    // 比较同参数价格，判断 price2 价格是否可替换 price1
    protected static function _compare($price1, $price2)
    {
        $price1['allot'] = $price1['allot'] ? 1 : 0;
        $price2['allot'] = $price2['allot'] ? 1 : 0;

        return $price1['allot'] > $price2['allot'] // 价格1及时，价格2非及时
                || ($price1['allot'] == $price2['allot'] && $price1['price'] <= $price2['price']); //新价格更高或相等
    }
    // _compare


}

interface supply_inf
{
    static function roomkey($room);

    static function bed($bed);

    static function net($net);
}
?>
