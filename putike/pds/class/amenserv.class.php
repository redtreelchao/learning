<?php
/**
 * 酒店设施 & 服务
 +-----------------------------------------
 * @category
 * @package amenserv
 * @author nolan.zhou
 * @version $Id$
 */
class amenserv extends api
{

    // error message
    static public $error_msg = array(
        '1201'   => '未提交设施名称',
        '1214'   => '未提交酒店ID',
    );


    /**
     * Load amenity
     +-----------------------------------------
     * @access public
     * @param mixed $hotel
     * @return void
     */
    static public function amenity($hotel)
    {
        if (!(int)$hotel)
            return !self::$error = '1214';

        $db = db(config('db'));

        $amenities = $db -> prepare("SELECT `name`,`hotel`,`intro`,`checked` FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel AND `type`='amenity' ORDER BY `id` ASC;") -> execute(array(':hotel'=>$hotel));
        if (!$amenities) $amenities = array();

        foreach ($amenities as $k => $v)
        {
            $amenities[$k]['intro'] = json_decode($v['intro'], true);
        }

        // $sql = "SELECT t.`id`, t.`name`, t.`type`
        //         FROM `ptc_tag_rel` AS r
        //             LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
        //         WHERE r.`objtype`='hotel' AND r.`objid`=:id AND t.`type`='service'
        //         GROUP BY r.`tag`";
        // $tags = $db -> prepare($sql) -> execute(array(':id'=>$hotel));
        // $amenities['tags'] = $tags;
        return $amenities;
    }
    // amenity



    /**
     * Update amenity
     +-----------------------------------------
     * @access public
     * @param  int      $hotel
     * @param  array    $amenity
     * @return void
     */
    static public function update_amenity($hotel, $amenities=array())
    {
        if (!(int)$hotel)
            return !self::$error = '1214';

        $data = array();
        foreach ($amenities as $v)
        {
            if (!trim($v['name'])) continue;

            $_intro = array();
            if (!empty($v['intro']))
            {
                foreach ($v['intro'] as $s)
                {
                    $_intro[] = array('pic'=>$s['pic'] ? $s['pic'] : null, 'text'=>$s['text']);
                }
            }

            $data[] = array(
                'hotel'     => (int)$hotel,
                'name'      => trim($v['name']),
                'type'      => 'amenity',
                'intro'     => json_encode($_intro, JSON_UNESCAPED_UNICODE),
                'checked'   => (int)$v['checked'],
                'opentime'  => trim($v['opentime']),
            );
        }

        $db = db(config('db'));

        $db -> beginTrans();

        $rs = $db -> prepare("DELETE FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel AND `type`='amenity'") -> execute(array(':hotel'=>$hotel));
        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '501';
        }

        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_amenity` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = '502';
            }
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = '599';
        }

        return true;
    }
    // update amenity




    /**
     * Load activity
     +-----------------------------------------
     * @access public
     * @param mixed $hotel
     * @return void
     */
    static public function activity($hotel)
    {
        if (!(int)$hotel)
            return !self::$error = '1214';

        $db = db(config('db'));

        $activities = $db -> prepare("SELECT `name`,`hotel`,`intro`,`opentime` FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel AND `type`='activity' ORDER BY `id` ASC;") -> execute(array(':hotel'=>$hotel));
        if (!$activities) $activities = array();

        foreach ($activities as $k => $v)
        {
            $_intro = json_decode($v['intro'], true);
            unset($activities[$k]['intro']);
            $activities[$k]['pic']  = $_intro['pic'];
            $activities[$k]['text'] = $_intro['text'];
        }

        return $activities;
    }
    // activity



    /**
     * Update activity
     +-----------------------------------------
     * @access public
     * @param  int      $hotel
     * @param  array    $activities
     * @return void
     */
    static public function update_activity($hotel, $activities=array())
    {
        if (!(int)$hotel)
            return !self::$error = '1214';

        $data = array();
        foreach ($activities as $v)
        {
            if (!trim($v['name'])) continue;

            $_intro = array('pic'=>$v['pic'] ? $v['pic'] : null, 'text'=>$v['text']);

            $data[] = array(
                'hotel' => (int)$hotel,
                'name'  => trim($v['name']),
                'type'  => 'activity',
                'intro' => json_encode($_intro, JSON_UNESCAPED_UNICODE),
                'opentime'  => trim($v['opentime']),
            );
        }

        $db = db(config('db'));

        $db -> beginTrans();

        $rs = $db -> prepare("DELETE FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel AND `type`='activity'") -> execute(array(':hotel'=>$hotel));
        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '501';
        }

        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_amenity` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = '502';
            }
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = '599';
        }

        return true;
    }
    // update activity




    /**
     * Load service
     +-----------------------------------------
     * @access public
     * @param mixed $hotel
     * @return void
     */
    static public function service($hotel)
    {
        if (!(int)$hotel)
            return !self::$error = '1214';

        $db = db(config('db'));

        $services = $db -> prepare("SELECT `name`,`hotel`,`intro`,`opentime` FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel AND `type`='service' ORDER BY `id` ASC;") -> execute(array(':hotel'=>$hotel));
        if (!$services) $services = array();

        foreach ($services as $k => $v)
        {
            $_intro = json_decode($v['intro'], true);
            unset($services[$k]['intro']);
            $services[$k]['pic'] = $_intro['pic'];
            $services[$k]['text'] = $_intro['text'];
        }

        $sql = "SELECT t.`id`, t.`name`, t.`type`
                FROM `ptc_tag_rel` AS r
                    LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                WHERE r.`objtype`='hotel' AND r.`objid`=:id AND t.`type`='service'
                GROUP BY r.`tag`";
        $tags = $db -> prepare($sql) -> execute(array(':id'=>$hotel));

        return array('services'=>$services, 'tags'=>$tags);
    }
    // amenity





    /**
     * Update service
     +-----------------------------------------
     * @access public
     * @param  int      $hotel
     * @param  array    $services
     * @param  array    $tags
     * @return void
     */
    static public function update_service($hotel, $services=array(), $tags=array())
    {
        if (!(int)$hotel)
            return !self::$error = '1214';

        $data = array();
        foreach ($services as $v)
        {
            if (!trim($v['name'])) continue;

            $_intro = array('pic'=>$v['pic'] ? $v['pic'] : null, 'text'=>$v['text']);

            $data[] = array(
                'hotel' => (int)$hotel,
                'name'  => trim($v['name']),
                'type'  => 'service',
                'intro' => json_encode($_intro, JSON_UNESCAPED_UNICODE),
                'opentime'  => trim($v['opentime']),
            );
        }

        $db = db(config('db'));

        $db -> beginTrans();

        $rs = $db -> prepare("DELETE FROM `ptc_hotel_amenity` WHERE `hotel`=:hotel AND `type`='service'") -> execute(array(':hotel'=>$hotel));
        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '501';
        }

        if ($data)
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_amenity` {$column} VALUES {$sql};") -> execute($value);
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = '502';
            }
        }

        // Delete Tags
        $sql = "DELETE r.* FROM `ptc_tag_rel` AS r
                    LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                WHERE t.`type`='service' AND r.`objtype`='hotel' AND r.`objid`=:id;";
        $rs = $db -> prepare($sql) -> execute(array(':id'=>$hotel));
        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '515';
        }

        $_tags = array();
        foreach ($tags as $v)
        {
            if (!(int)$v) continue;
            $_tags[] = array('tag'=>(int)$v, 'objtype'=>'hotel', 'objid'=>$hotel, 'value'=>'');
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

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = '599';
        }

        return true;
    }
    // update service

}