<?php
/**
 * 酒店房型维护
 +-----------------------------------------
 * @category
 * @package room
 * @author nolan.zhou
 * @version $Id$
 */
class room extends api
{

    // error message
    static public $error_msg = array(
        '808'   => '提交内容不正确',
        '809'   => '客房概括不能为空',
        '814'   => '未提交酒店ID',
        '821'   => '有产品关联，请撤销关联后删除',
    );

    /**检测房型是否可删除
     * @param $id
     * @return bool 可删除返回true
     */
    static public function candel($id)
    {
        $db = db(config('db'));

        $rs = $db -> prepare("SELECT COUNT(*) AS c FROM `ptc_product_item` WHERE `objid`=:room AND `objtype`='room'") -> execute(array(':room' => intval($id)));

        if ($rs[0]['c'] > 0)
            return !self::$error = '821';

        return true;
    }



    /**
     * Load all rooms
     +-----------------------------------------
     * @access public
     * @param  int  $hotel
     * @return void
     */
    static public function load($hotel)
    {
        if (!$hotel)
            return !self::$error = '814';

        $db = db(config('db'));

        $data = array();
        $roomsummary = $db -> prepare("SELECT `roomsummary` FROM `ptc_hotel_ext` WHERE `id`=:id") -> execute(array(':id'=>$hotel));

        $data['roomsummary'] = $roomsummary ? $roomsummary[0]['roomsummary'] : '';

        $sql = "SELECT t.`id`, t.`name`, t.`type`
                FROM `ptc_tag_rel` AS r
                    LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                WHERE r.`objtype`='hotel' AND r.`objid`=:id AND t.`type` IN ('facility','catering','appliances','bathroom','washing','othserve')
                GROUP BY r.`tag`";
        $data['tags'] = $db -> prepare($sql) -> execute(array(':id'=>$hotel));

        $sql = "SELECT *
                FROM `ptc_hotel_room_type` AS r
                    LEFT JOIN `ptc_hotel_room_type_ext` AS e ON r.`id`=e.`id`
                WHERE r.`hotel`=:hotel";
        $rooms = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel));

        foreach($rooms as $k => $v)
        {
            $rooms[$k]['pics'] = json_decode($v['pics']);
        }

        $data['rooms'] = $rooms;

        return $data;
    }
    // load





    /**
     * Save all rooms
     +-----------------------------------------
     * @access public
     * @param  int     $hotel
     * @param  string  $roomsummary
     * @param  array   $rooms
     * @return void
     */
    static public function save($hotel, $roomsummary, $tags, $rooms)
    {
        if (!$hotel)
            return !self::$error = '814';

        if (!$roomsummary)
            return !self::$error = '809';

        $db = db(config('db'));

        $db -> beginTrans();

        $rs = $db -> prepare("INSERT INTO `ptc_hotel_ext` (`roomsummary`, `id`) VALUES (:roomsummary, :id) ON DUPLICATE KEY UPDATE `roomsummary`=VALUES(`roomsummary`);")
                  -> execute(array(':roomsummary'=>$roomsummary, ':id'=>$hotel));
        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '591';
        }

        $ids = array();
        foreach ($rooms as $v)
        {
            if ($v['id'])
            {
                $rs = self::create($hotel, $v['name'], $v['en'], $v['pics'], $v['area'], $v['floor'], $v['adult'], $v['child'], $v['bed'], $v['smoke'], $v['pet'], $v['addbe'], $v['scenery'], $v['intro'],  $v['id'], false);
                $ids[] = $v['id'];
            }
            else
            {
                $rs = self::create($hotel, $v['name'], $v['en'], $v['pics'], $v['area'], $v['floor'], $v['adult'], $v['child'], $v['bed'], $v['smoke'], $v['pet'], $v['addbe'], $v['scenery'], $v['intro'],  null, false);
                $ids[] = $rs;
            }

            if (!$rs)
                return false;
        }

        // 检查是否有关联酒店券
        /*  可以修改 关联房间
        $sql = "SELECT i.*
                FROM `ptc_product_item` AS i
                    LEFT JOIN `ptc_product` AS p ON i.`pid` = p.`id`
                WHERE i.`objtype` = 'room' AND i.`objpid` = :hotel AND i.`objid` NOT IN (".implode(',', $ids).") AND p.`status` = 1;";

        $rs = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel));
        if ($rs)
        {
            return !self::$error = '821';
        }
        */

        $rs = $db -> prepare("DELETE FROM `ptc_hotel_room_type` WHERE `hotel`=:hotel" . ( $ids ? " AND `id` NOT IN (".implode(',', $ids).")" : '' )) -> execute(array(':hotel'=>$hotel));
        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '592';
        }

        // Bind tags
        if (!is_null($tags))
        {
            $sql = "DELETE r.* FROM `ptc_tag_rel` AS r
                        LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                    WHERE t.`type` IN ('facility','catering','appliances','bathroom','washing','othserve') AND r.`objtype`='hotel' AND r.`objid`=:id;";
            $rs = $db -> prepare($sql) -> execute(array(':id'=>$hotel));
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = '593';
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
                    return !self::$error = '594';
                }
            }
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = '599';
        }

        return true;
    }
    // save





    /**
     * Create room
     +-----------------------------------------
     * @access public
     * @param mixed $hotel
     * @param mixed $name
     * @param mixed $en
     * @param mixed $pics
     * @param mixed $area
     * @param mixed $floor
     * @param mixed $adult
     * @param mixed $child
     * @param mixed $bed
     * @param mixed $smoke
     * @param mixed $pet
     * @param mixed $addbe
     * @param string $scenery
     * @param mixed $intro
     * @param array $tags
     * @param mixed $id
     * @return void
     */
    static public function create($hotel, $name, $en=null, $pics, $area, $floor, $adult, $child=0, $bed, $smoke, $pet, $addbe, $scenery='', $intro, $id=null, $trans=true)
    {
        $name = str_replace(array('（', '）'), array('(', ')'), $name);

        $args = document::$func['room']['save']['array']['rooms'];
        foreach ($args as $k => $v)
        {
            if ($k && !$$k && !in_array($k, array('en','scenery', 'id','smoke','pet','addbe','child')))
            {
                $name = substr($v, 0, strpos($v, '，'));
                self::$error_msg['808_'.$k] = $name . self::$error_msg['808'];
                return !self::$error = '808_'.$k;
            }
        }

        $data = array(
            'hotel'     => (int)$hotel,
            'name'      => trim($name),
            'en'        => trim($en),
            'area'      => (string)$area,
            'floor'     => (int)$floor,
            'adult'     => (int)$adult,
            'child'     => (int)$child,
            'bed'       => (string)$bed,
            'smoke'     => (int)$smoke ? 1 : 0,
            'pet'       => (int)$pet ? 1 : 0,
            'addbe'     => (int)$addbe ? 1 : 0,
        );

        if (!$id && !$data['hotel'])
        {
            self::$error_msg['808_hotel'] = '酒店ID' . self::$error_msg['808'];
            return !self::$error = '808_hotel';
        }

        import(CLASS_PATH.'extend/string');

        $ext = array(
            'pics'      => json_encode(array_filter($pics)),
            'scenery'   => string::text($scenery, 60),
            'intro'     => trim($intro),
        );

        $db = db(config('db'));

        if ($trans)
            $db -> beginTrans();

        if ($id)
        {
            $id = (int)$id;

            $old = $db -> prepare("SELECT `name` FROM `ptc_hotel_room_type` WHERE `id`=:id;") -> execute(array(':id'=>$id));

            unset($data['hotel']);
            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = $id;
            $rs = $db -> prepare("UPDATE `ptc_hotel_room_type` SET {$sql} WHERE `id`=:id;") -> execute($value);

            $history_message = "更新了房型信息";
            $history_data    = array_merge($data, $ext);
        }
        else
        {
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_room_type` {$column} VALUES {$sql};") -> execute($value);
            $id = $data['id'] = $rs;

            $history_message = "创建了房型";
            $history_data    = array_merge($data, $ext);
        }

        if (false === $rs)
        {
            $db -> rollback();
            return !self::$error = '501';
        }

        // History
        if (!history($hotel, 'room', $history_message, $history_data))
        {
            $db -> rollback();
            return !self::$error = '502';
        }

        // Save extend data
        $update_column = update_column(array_keys($ext));
        $ext['id'] = $id;
        list($column, $sql, $value) = array_values(insert_array($ext));
        $ers = $db -> prepare("INSERT INTO `ptc_hotel_room_type_ext` {$column} VALUES {$sql} ON DUPLICATE KEY UPDATE {$update_column};") -> execute($value);
        if (false === $ers)
        {
            $db -> rollback();
            return !self::$error = '503';
        }



        if ($trans && !$db -> commit())
        {
            $db -> rollback();
            return !self::$error = '599';
        }

        return $id;
    }
    // create



    /*
     * Update room
     +-----------------------------------------
     * @access public
     */
    static public function update( $id, $name, $en, $pics, $area, $floor, $adult, $child, $bed, $smoke, $pet, $addbe, $scenery='', $intro )
    {
        if (!$id)
            return !self::$error = '814';

        return self::create( $hotel=null, $name, $en=null, $pics, $area, $floor, $adult, $child, $bed, $smoke, $pet, $addbe, $scenery, $intro, $id );
    }
    // update


}