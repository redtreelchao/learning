<?php
/**
 * 城市国家
 +-----------------------------------------
 * @category
 * @package district
 * @author nolan.zhou
 * @version $Id$
 */
class district extends api
{

    // error message
    static public $error_msg = array(
            '701'   => '未提交国家代码'
        );


    // Country
    static public function country()
    {
        $db = db(config('db'));
        $list = $db -> prepare("SELECT `id`,`name`,`pinyin`,`lng`,`lat` FROM `ptc_district` WHERE `pid`=0 ORDER BY `id` ASC;") -> execute();

        $country = array();
        foreach($list as $v)
            $country[$v['id']] = $v;

        return $country;
    }


    // City
    static public function city($country=0)
    {
        if (!$country)
            return !self::$error = '701';

        $db = db(config('db'));
        $list = $db -> prepare("SELECT `id`,`name`,`pinyin`,`lng`,`lat`,`province` FROM `ptc_district` WHERE `pid`=:pid ORDER BY `id` ASC;")
                    -> execute(array(':pid'=>$country));

        $city = array();
        foreach ($list as $v)
            $city[$v['id']] = $v;

        return $city;
    }


    // district
    static public function area($city=0, $type='district')
    {
        if (!$city)
            return !self::$error = '701';

        $db = db(config('db'));
        $list = $db -> prepare("SELECT `id`,`name`,`pinyin` FROM `ptc_district_ext` WHERE `pid`=:pid AND `type`=:type ORDER BY `id` ASC;")
                    -> execute(array(':pid'=>$city, ':type'=>$type));

        $district = array();
        foreach ($list as $v)
            $district[$v['id']] = $v;

        return $district;
    }


}