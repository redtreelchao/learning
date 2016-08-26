<?php

/**
 * User: Yuri
 * Date: 2016/6/22
 * Time: 12:05
 * 酒店周边类
 */
class around extends api
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
        '711'   => '电话号码格式不正确',
        '713'   => '已存在相似酒店数据，请检查并确认',


        '714'   => '未提交酒店ID',
        '715'   => '酒店信息不存在',


        '712'   => '坐标信息不正确',

        '730'   => '操作周边信息数据记录失败',
        '731'   => '周边中文名称不能为空',
        '732'   => '周边英文名称不能为空',
        '733'   => '国家不能为空',
        '734'   => '城市不能为空',
        '735'   => '未提周边ID',
        '736'   => '周边类型有误',
    );

    static private $type = array('景点','娱乐','餐饮','地铁','飞机火车','购物');



    /**
     * @param $name
     * @param $en
     * @param $type
     * @param $country
     * @param $city
     * @param $lng
     * @param $lat
     * @param int $status
     * @return bool
     * 创建酒店周边
     */

    public static function type(){
        return self::$type;
    }

    public static function create($name, $en, $type, $country, $city, $lng, $lat, $status = 1, $id = null)
    {

        $data = array(
            'name'      => trim($name),
            'en'        => trim($en),
            'type'      => (int)$type,
            'country'   => (int)$country,
            'city'      => (int)$city,
            'lng'       => (string)$lng,
            'lat'       => (string)$lat,
            'status'    => (int)$status,
            'updatetime'=> NOW,
            'createtime'=> NOW,
        );


        //数据检测

        if(empty($data['name']))
            return !self::$error = '731';


        if(!$data['en'])
            return !self::$error = '732';


        if(!$data['country'])
            return !self::$error = '733';


        if(!$data['city'])
            return !self::$error = '734';

        $type = self::$type;

        if(!$data['type'] || !isset($type[$data['type']-1]))
            return !self::$error = '736';

        if(empty($data['lng']) || empty($data['lat']))
            return !self::$error = '712';



        $db = db(config('db'));
        $db -> beginTrans();

        if($id)
        {
            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = $id;
            $rs = $db -> prepare("UPDATE `ptc_around` SET {$sql} WHERE `id`=:id;") -> execute($value);
            $modity = '修改';

        }else{
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_around` {$column} VALUES {$sql};") -> execute($value);
            $modity = '创建';
            $id = $rs;
        }

        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '730';
        }

        // History
        if (!history($id, 'around', $modity.'了酒店周边信息', array_merge($value)))
        {
            $db -> rollback();
            return !self::$error = '502';
        }

        if ($db -> commit())
        {
            return $rs;
        }
        else
        {
            $db -> rollback();
            return false;
        }


    }

    //修改酒店周边
    public static function update($id, $name, $en, $type, $country, $city, $lng, $lat, $status = 1)
    {
        $id = intval($id); 
        if(!$id) return !self::$error = '735';

        return self::create($name, $en, $type, $country, $city, $lng, $lat, $status, $id);

    }

    //删除周边
    public static function delete($id)
    {

        if (delete('ptc_around', "`id`='{$id}'"))
            json_return(null,0,'');
        else
            json_return(null, 1, '操作失败，请重试..');

    }

    //搜索周边信息
    public static function search($keywords='', $hotel_id = null, $distance=0, $limit=10)
    {

        $value = [];
        $where = '';

        if($keywords)
        {
            //输入的是id
            if(intval($keywords) === $keywords)
            {
                $where = $where.' WHERE `id` = :id';
                $value[':id'] = $keywords;
            }else{
                $where = $where. "WHERE (`name` like :name OR `en` like :name";
                $value[':name'] = '%'.$keywords.'%';
                //输入的为类型
                if(in_array($keywords, static::$type))
                {
                    $where = $where." OR `type` = :type ";
                    $value[':type'] = array_search($keywords, static::$type);
                }

                $where = $where.')';
            }
        }

        $sql_count = "SELECT COUNT(*) AS c FROM `ptc_around` ". $where;

        $db = db(config('db'));
        $type = self::$type;
        $type_case = '';
        foreach ($type as $k => $v){
            $type_case = $type_case. " WHEN ".($k+1)." THEN '".$v."' ";
        }
        $type_case = ",CASE `type` ".$type_case." ELSE ''  END AS `type`,";
        if($hotel_id)
        {
            $hotel_condition = [
                ':hotel_id'=> $hotel_id
            ];

            $coordinates =  $db->prepare("SELECT `lng`, `lat` FROM `ptc_hotel` WHERE `id` = :hotel_id ;")->execute($hotel_condition);

            if($coordinates === false || empty($coordinates))
                return !self::$error = '715';

            $value[':lng'] = $coordinates[0]['lng'];
            $value[':lat'] = $coordinates[0]['lat'];
            if($distance){
                $value[':distance'] = $distance;
                $sql_count = "SELECT COUNT(*) AS c FROM `ptc_around` ". $where. " AND ROUND(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-:lat*pi()/180)/2),2)+cos(lat*pi()/180)*cos(:lat*pi()/180)* pow(sin( (lng*pi()/180-:lng*pi()/180)/2),2)))*1000) <=:distance ";
                $sql = "SELECT `id`, `country`,`city`,`name` ".$type_case." ROUND(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-:lat*pi()/180)/2),2)+cos(lat*pi()/180)*cos(:lat*pi()/180)* pow(sin( (lng*pi()/180-:lng*pi()/180)/2),2)))*1000) AS distance FROM `ptc_around` ".$where." HAVING `distance` <=:distance ORDER BY `distance` ASC";
            }else{
                $sql = "SELECT `id`, `country`,`city`,`name` ".$type_case." ROUND(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-:lat*pi()/180)/2),2)+cos(lat*pi()/180)*cos(:lat*pi()/180)* pow(sin( (lng*pi()/180-:lng*pi()/180)/2),2)))*1000) AS distance FROM `ptc_around` ".$where."  ORDER BY `distance` ASC";
            }



        }else{
            $sql = "SELECT `id`, `country`,`city`,`name`, `en` ".$type_case." `lng`, `lat`, `status` FROM `ptc_around` ". $where;
        }




        $count = $db -> prepare($sql_count) -> execute($value);
        $_GET['page'] = self::$page;
        $page = new page($count[0]['c'], $limit);
        $limit = $page -> limit();
        $sql = $sql." LIMIT {$limit}";
        $list = $db -> prepare($sql) -> execute($value);
        return array('page'=>$page->show(), 'list'=>$list);

    }

}