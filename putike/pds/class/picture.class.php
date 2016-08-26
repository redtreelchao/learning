<?php
/**
 * 图片库
 +-----------------------------------------
 * @category
 * @package picture
 * @author nolan.zhou
 * @version $Id$
 */
class picture extends api
{

    // error message
    static public $error_msg = array(
        1101  => '图片集名称不能为空',
        1102  => '请选择图片集城市',
        1103  => '上传图片类型不正确',
        1104  => '分类不能为空',
        1105  => '批量更改不得超过50张图片',
        1106  => '图片集不存在',
        1107  => '检索关键词不能为空',
        1108  => '上传服务器失败',
        1109  => '参数不能为空',
        1110  => '酒店不存在',
    );


    // 创建 or 修改图片集
    static public function gallery_update($id=null, $name=null, $city=null, $hotel=null)
    {
        $db = db(config('db'));


        $data = array(
            'name'          => $name,
            'city'          => (int)$city,
            'hotel'         => (int)$hotel,
            'updater'       => (int)$_SESSION['uid'],
            'updatetime'    => NOW,
        );

        if ($hotel && !$id)
        {
            $hotel = $db -> prepare("SELECT * FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$hotel));
            if (!$hotel)
                return !self::$error = 1110;

            if (!$data['city'])
                $data['city'] = $hotel[0]['city'];

            if (!$data['name'])
                $data['name'] = $hotel[0]['name'];
        }

        if (!$data['city']) unset($data['city']);
        if (!$data['hotel']) unset($data['hotel']);

        if ($id)
        {
            if (!$name) unset($data['name']);
            if (!$city) unset($data['city']);

            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = $id;
            $rs = $db -> prepare("UPDATE `ptc_picture_gallery` SET {$sql} WHERE `id`=:id;") -> execute($value);
        }
        else
        {
            if (!$name) return !self::$error = 1101;
            //if (!$city) return !self::$error = 1102;

            $data['creator'] = (int)$_SESSION['uid'];
            $data['createtime'] = NOW;

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_picture_gallery` {$column} VALUES {$sql};") -> execute($value);
        }

        return $rs === false ? false : ($id ? $id : $rs);
    }
    // gallery_update




    // 读取图片集
    static public function gallery($id=null, $type='', $order='update', $limit=10)
    {
        
        $db = db(config('db'));

        $sql = "SELECT g.`id`, g.`name`, p.`file` AS `cover`, u.`name` AS `updater`, u.`lastlogin`
                FROM `ptc_picture_gallery` AS g
                    LEFT JOIN `ptc_picture` AS p ON p.`id`=g.`cover`
                    LEFT JOIN `rbac_user` AS u ON g.`updater`=u.`id`
                WHERE g.`id`=:id";
        $gallery = $db -> prepare($sql) -> execute(array(':id'=>$id));
        if (!$gallery)
            return !self::$error = 1106;

        $gallery = $gallery[0];

        $where = "p.`gallery` = :id";

        $condition = array(':id'=> $id);

        if ($type)
        {
            $where .= " AND p.`type` = :type";
            $condition[':type'] = $type;
        }

        switch ($order)
        {
            case 'type':
                $order = 'p.`type` DESC';
                break;

            case 'update':
            default:
                $order = 'p.`update` DESC';
        }


        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_picture` AS p WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], $limit);
        $limit = $page -> limit();

        $sql = "SELECT p.`id`, p.`file`, p.`title`, p.`intro`, t.`name` AS `type`, p.`size`
                FROM `ptc_picture` AS p
                    LEFT JOIN `ptc_picture_type` AS t ON p.`type`=t.`id`
                WHERE {$where}
                ORDER BY {$order}
                LIMIT {$limit};";
        $pictures = $db -> prepare($sql) -> execute($condition);

        $gallery['pictures'] = $pictures;
        $gallery['page'] = $page -> show();

        return $gallery;
    }
    // gallery


    // 根据hotel获取图集
    static public function gallery_hotel($hotel=null,$order='update', $limit=10)
    {
        
        $db      = db(config('db'));
        $gallery = array();
        if ($hotel)
        {
            $sql = "SELECT g.`id`, g.`name`, p.`file` AS `cover`, u.`name` AS `updater`, u.`lastlogin`
                FROM `ptc_picture_gallery` AS g
                    LEFT JOIN `ptc_picture` AS p ON p.`id`=g.`cover`
                    LEFT JOIN `rbac_user` AS u ON g.`updater`=u.`id`
                WHERE g.`hotel`=:hotel";
            $gallery = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel));
            if (!$gallery)
                return !self::$error = 1106;

            $gallery = $gallery[0];
            $where   = "g.`hotel` = :hotel";
            $condition = array(':hotel'=> $hotel);      
            $order = 'p.`update` DESC';
            $count = $db -> prepare("SELECT COUNT(p.`id`) AS `c` FROM `ptc_picture_gallery` AS g
                        LEFT JOIN `ptc_picture` AS p ON p.`gallery` = g.`id`
                                    WHERE {$where};") -> execute($condition);

            $page = new page($count[0]['c'], $limit);
            $limit = $page -> limit();

            $sql = "SELECT p.`id`, p.`file`, p.`title`, p.`intro`,  p.`size`, p.`tags`,g.`cover`
                    FROM `ptc_picture_gallery` AS g
                        LEFT JOIN `ptc_picture` AS p ON p.`gallery` = g.`id`
                    WHERE {$where}
                    ORDER BY {$order}
                    LIMIT {$limit};";
            $pictures = $db -> prepare($sql) -> execute($condition);

            $gallery['pictures'] = $pictures;
            $gallery['page'] = $page -> show();  
        }else
        {
            $where     = " `gallery` = :gallery";
            $condition = array(':gallery'=> 0); 
            $order     = ' `update` DESC';    
            $count     = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_picture`
                                    WHERE {$where};") -> execute($condition);
            $page  = new page($count[0]['c'], $limit);
            $limit = $page -> limit();
            $sql = "SELECT `id`, `file`, `title`, `intro`,  `size`, `tags`
                    FROM  `ptc_picture` 
                    WHERE {$where}
                    ORDER BY {$order}
                    LIMIT {$limit};";
            $pictures = $db -> prepare($sql) -> execute($condition);

            $gallery['pictures'] = $pictures;
            $gallery['page'] = $page -> show();  
        } 
        return $gallery;
    }
    // gallery

    // 最近的图片
    static public function recently($limit=3, $picture=4)
    {
        $db = db(config('db'));

        $sql = "SELECT `id`, `name` FROM `ptc_picture_gallery` ORDER BY `updatetime` DESC LIMIT 0,".(int)$limit;
        $list = $db -> prepare($sql) -> execute($value);

        $sql = "SELECT p.`id`, p.`file`, p.`title`, p.`intro`, t.`name` AS `type`, p.`size`
                FROM `ptc_picture` AS p
                    LEFT JOIN `ptc_picture_type` AS t ON p.`type`=t.`id`
                WHERE p.`gallery`=:gallery
                ORDER BY p.`update` DESC
                LIMIT 0,".(int)$picture;
        $db -> prepare($sql);

        foreach($list as $k => $v)
        {
            $list[$k]['pictures'] = $db -> execute(array(':gallery'=>$v['id']));
        }

        return $list;
    }
    // recently





    // 检索图片
    static public function search($keyword='', $type='picture', $limit=10)
    {
        $db = db(config('db'));

        if (!$keyword) return !self::$error = 1107;

        if ($type == 'picture')
        {
            $where = 'h.`name` LIKE :keyword OR p.`title` LIKE :keyword';
            $condition = array(':keyword' => "%{$keyword}%");
            $join = 'LEFT JOIN `ptc_picture_gallery` AS g ON p.`gallery`=g.`id`
                     LEFT JOIN `ptc_hotel` AS h ON g.`hotel`=h.`id`';

            if ($limit)
            {
                $sql = "SELECT COUNT(*) AS c FROM `ptc_picture` AS p {$join} WHERE {$where}";
                $count = $db -> prepare($sql) -> execute($condition);

                $page = new page($count[0]['c'], $limit);
                $limit = $page -> limit();
            }

            $sql = "SELECT p.`id`, p.`file`, p.`title`, p.`intro`, p.`size`, h.`name` AS `hotel`
                    FROM `ptc_picture` AS p
                        {$join}
                    WHERE {$where}
                    ORDER BY p.`update` DESC ". ($limit ? "LIMIT {$limit}" : '');
            $list = $db -> prepare($sql) -> execute($condition);

            return array('list'=>$list, 'page'=>$limit ? $page->show() : null);
        }
        else if ($type == 'gallery')
        {
            $where = 'g.`name` LIKE :keyword';
            $condition = array(':keyword' => "%{$keyword}%");

            if ($limit)
            {
                $sql = "SELECT COUNT(*) AS c FROM `ptc_picture_gallery` AS g WHERE {$where}";
                $count = $db -> prepare($sql) -> execute($condition);

                $page = new page($count[0]['c'], $limit);
                $limit = $page -> limit();
            }

            $sql = "SELECT g.*, p.`file` AS `cover`
                    FROM `ptc_picture_gallery` AS g
                        LEFT JOIN `ptc_picture` AS p ON p.`id`=g.`cover`
                    WHERE {$where}
                    ORDER BY g.`updatetime` DESC ". ($limit ? "LIMIT {$limit}" : '');
            $list = $db -> prepare($sql) -> execute($condition);

            return array('list'=>$list, 'page'=>$limit ? $page->show() : null);
        }

        return null;
    }
    // search





    // 待上传
    static public function queue($limit=10)
    {
        $db = db(config('db'));
/*
        $sql = "SELECT COUNT(*) AS c FROM (
                SELECT g.`id` FROM `ptc_picture_gallery` AS g
                    LEFT JOIN `ptc_picture` AS p ON p.`gallery`=g.`id`
                WHERE p.`id` IS NULL
                GROUP BY g.`id`) AS s";
        $count = $db -> prepare($sql) -> execute($condition);

        $page = new page($count[0]['c'], $limit);
        $limit = $page -> limit();

        $sql = "SELECT g.`id`, g.`name`, c.`name` AS `city`, c.`province`, d.`name` AS `country`, u.`name` AS `creator`, g.`createtime`
                FROM `ptc_picture_gallery` AS g
                    LEFT JOIN `ptc_picture` AS p ON p.`gallery`=g.`id`
                    LEFT JOIN `ptc_district` AS c ON g.`city`=c.`id`
                    LEFT JOIN `ptc_district` AS d ON d.`id`=c.`pid`
                    LEFT JOIN `rbac_user` AS u ON g.`creator`=u.`id`
                WHERE p.`id` IS NULL
                GROUP BY g.`id`
                ORDER BY g.`id` DESC
                LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);
*/
        $sql = "SELECT COUNT(*) AS c
                FROM `ptc_hotel` AS h
                    LEFT JOIN `ptc_picture_gallery` AS g ON g.`hotel`=h.`id`
                WHERE g.`id` IS NULL";
        $count = $db -> prepare($sql) -> execute($condition);

        $page = new page($count[0]['c'], $limit);
        $limit = $page -> limit();

        $sql = "SELECT h.`id`, h.`country`, d.`name` AS `countryname`, h.`city`, c.`name` AS `cityname`, h.`name`, u.`name` AS `creator`, h.`createtime`
                FROM `ptc_hotel` AS h
                    LEFT JOIN `ptc_district` AS c ON h.`city`=c.`id`
                    LEFT JOIN `ptc_district` AS d ON h.`country`=d.`id`
                    LEFT JOIN `ptc_picture_gallery` AS g ON g.`hotel`=h.`id`
                    LEFT JOIN `rbac_user` AS u ON h.`creator`=u.`id`
                LIMIT {$limit};";

        $list = $db -> prepare($sql) -> execute($condition);

        return array('page'=>$page->show(), 'list'=>$list);
    }
    // queue



    // 失联图片集
    static public function unbind()
    {
        $db = db(config('db'));

        $sql = "SELECT g.*, p.`file` AS `cover`
                FROM `ptc_picture_gallery` AS g
                    LEFT JOIN `ptc_picture` AS p ON p.`id`=g.`cover`
                WHERE g.`hotel` IS NULL;";
        $list = $db -> prepare($sql) -> execute();
        return $list;
    }
    // unbind



    // 关联图片集
    static public function bind($id, $hotel)
    {
        $db = db(config('db'));

        if (!(int)$id || !(int)$hotel)
            return !self::$error = 1109;

        $rs = $db -> prepare("UPDATE `ptc_picture_gallery` SET `hotel`=:hotel WHERE `id`=:id;") -> execute(array(':id'=>(int)$id, ':hotel'=>(int)$hotel));
        return $rs === false ? false : true;
    }
    // unbind



    // 创建 or 修改标签
    static public function type_update($id=null, $name, $pid=0)
    {
        $db = db(config('db'));

        $data = array('name'=>trim($name));
        if ($pid == 2) return false;

        if ($id)
        {
            list($sql, $value) = array_values(update_array($data));
            $value[':id'] = $id;
            $rs = $db -> prepare("UPDATE `ptc_picture_type` SET {$sql} WHERE `id`=:id;") -> execute($value);
        }
        else
        {
            $data['pid'] = (int)$pid;
            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `ptc_picture_type` {$column} VALUES {$sql};") -> execute($value);
        }

        return $rs === false ? false : ($id ? $id : $rs);
    }
    // type_update


    // 删除type
    static public function type_delete($id)
    {
        if (!(int)$id )
            return !self::$error = 1109;
        $db = db(config('db'));
        $value[':id'] = $id;
        $rs = $db -> prepare("DELETE FROM `ptc_picture_type` WHERE `id`=:id;") -> execute($value);         

        if($rs === false)
            return !self::$error = 501;

        return true;
    }
    // type_update





    // 读取标签列表
    static public function type($gallery=null)
    {
        $db = db(config('db'));

        $rs = $db -> prepare("SELECT * FROM `ptc_picture_type` ORDER BY `pid` ASC, `id` ASC;") -> execute();

        $list = array();
        foreach($rs as $key => $v)
        {
            if (empty($v['pid']))
            {
                unset($v['pid']);
                $v['sub'] = array();

                if ($v['id'] == 2 && $gallery)
                {
                    $sql = "SELECT r.`id`, r.`name`
                                FROM `ptc_picture_gallery` AS g
                            LEFT JOIN `ptc_hotel_room_type` AS r ON g.`hotel`=r.`hotel`
                            WHERE g.`id`=:gallery";
                    $roomtype = $db -> prepare($sql) -> execute(array(':gallery'=>$gallery));
                    $v['sub'] = $roomtype ? $roomtype : array();
                }

                $list[$v['id']] = $v;
            }
            else
            {
                $list[$v['pid']]['sub'][] = $v;
            }
        }

        return $list;
    }
    // type





    // 读取可用标签
    static public function tags($keyword='', $num=10)
    {
        $db = db(config('db'));

        $where = "`type` NOT IN ('district', 'view', 'transport', 'price')";
        $condition = array();

        if ($keyword)
        {
            $where .= ' AND `name` like :name';
            $condition = array(':name'=>"%{$keyword}%");
        }

        $tags = $db -> prepare("SELECT * FROM `ptc_tag` WHERE {$where} LIMIT 0,".(int)$num) -> execute($condition);

        return $tags;
    }
    // tags



    // UPYUN 账号密码
    static public $upyun_usr = 'putike';

    static public $upyun_pwd = 'putike654312';


    // 上传图片
    static public function upload($file, $name, $lng, $lat,$gallery=0)
    {
        $exists = true;
        $path = 'files/picture/'.date('Ymd').'/';
        if (!is_dir($path)) mkdir($path, 755);

        $mime = substr($file, 5, strpos($file, ';') - 5);
        list($t, $ext) = explode('/', $mime);
        if (!in_array($ext, array('jpg', 'jpeg', 'png')))
            return !self::$error = 1103;

        if ($ext == 'jpeg') $ext = 'jpg';

        while ($exists)
        {
            $name = substr(md5($name.rand(0, 99999)), 0, 8) . '.' . $ext;
            $true_path = PT_PATH . $path . $name;
            $exists = file_exists( $true_path );
        }

        $body = base64_decode(substr($file, strpos($file, ';') + 8));
        file_put_contents( $true_path , $body );

        // api
        $uri_path = '/' . date('Y/md') . '/' . $name;
        $uri = '/p-product-pic' . $uri_path;
        $ch = curl_init("http://v0.api.upyun.com{$uri}");
        $_headers = array('Expect:', 'Content-MD5' => md5($body));

        $length = strlen($body);
        $fh = fopen($true_path, 'rb');

        array_push($_headers, "Content-Length: {$length}");
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, $length);

		$date = gmdate('D, d M Y H:i:s \G\M\T');

		$sign = 'UpYun ' . self::$upyun_usr . ':' . md5("PUT&{$uri}&{$date}&{$length}&" . md5(self::$upyun_pwd));
        array_push($_headers, "Authorization: {$sign}");
        array_push($_headers, "Date: {$date}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POST, 1);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code == 0)
            return !self::$error = 1108;

        curl_close($ch);
        fclose($fh);

        if ($http_code != 200)
            return !self::$error = 1108;

        $db = db(config('db'));

        list($width, $height, $type, $attr) = getimagesize($true_path);
        $path = 'http://p-product-pic.b0.upaiyun.com'.$uri;

        $data = array(
            ':file'     => 'http://p-product-pic.b0.upaiyun.com'.$uri_path,
            ':title'    => trim($name),
            ':gallery'  => (int)$gallery,
            ':lng'      => trim($lng),
            ':lat'      => trim($lat),
            ':size'     => "{$width}*{$height}",
            ':uid'      => (int)$_SESSION['uid'],
            ':time'     => NOW,
        );
        $rs = $db -> prepare("INSERT INTO `ptc_picture` (`file`, `title`,`gallery`, `lng`, `lat`, `size`, `uploader`, `update`) VALUES (:file, :title, :gallery, :lng, :lat, :size, :uid, :time)") -> execute($data);
        if (!$rs)
            return !self::$error = 501;

        return array('id'=>$rs, 'file'=>$data[':file']);
    }
    // upload



    // 读取图片
    static public function load($id)
    {
        $db = db(config('db'));

        $sql = "SELECT p.*, g.`name` AS `galleryname`, u.`name` AS `uploadername`
                FROM `ptc_picture` AS p
                    LEFT JOIN `ptc_picture_gallery` AS g ON p.`gallery` = g.`id`
                    LEFT JOIN `rbac_user` AS u ON p.`uploader` = u.`id`
                WHERE p.`id`=:id";
        $pic = $db -> prepare($sql) -> execute(array(':id'=>$id));
        if (!$pic) return null;

        $pic[0]['tags'] = $pic[0]['tags'] ? explode('|', $pic[0]['tags']) : null;
/*
        $sql = "SELECT t.`id`, t.`name`
                FROM `ptc_tag_rel` AS r
                    LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                WHERE r.`objtype`='picture' AND r.`objid`=:id;";
        $pic[0]['tags'] = $db -> prepare($sql) -> execute(array(':id'=>$id));
*/

        return $pic[0];
    }
    // load



    // 修改图片
    static public function edit($id, $title, $intro='', $gallery, $type, $subtype, $copyright='', $tags=null)
    {
        $data = array(
            'title'     => trim($title),
            'intro'     => trim($intro),
            'gallery'   => (int)$gallery,
            'type'      => (int)$type,
            'subtype'   => (int)$subtype,
            'tags'      => $tags ? implode('|', $tags) : null,
            'copyright' => $copyright,
            'update'    => NOW,
        );

        $db = db(config('db'));

        $db -> beginTrans();

        if ($gallery)
        {
            $_gallery = $db -> prepare("SELECT `cover` FROM `ptc_picture_gallery` WHERE `id`=:id") -> execute(array(':id'=>$gallery));
            if (!$_gallery)
                return !self::$error = 1106;

            if (empty($_gallery[0]['cover']))
            {
                $rs = $db -> prepare("UPDATE `ptc_picture_gallery` SET `cover`=:cover WHERE `id`=:id") -> execute(array(':id'=>$gallery, ':cover'=>$id));
                if ($rs === false)
                {
                    $db -> rollback();
                    return !self::$error = 500;
                }
            }
        }

        list($sql, $value) = array_values(update_array($data));
        $value[':id'] = $id;
        $rs = $db -> prepare("UPDATE `ptc_picture` SET {$sql} WHERE `id`=:id;") -> execute($value);
        if ($rs === false)
        {
            $db -> rollback();
            return !self::$error = 501;
        }

        // Bind tags
        /*
        if (!is_null($tags))
        {
            $sql = "DELETE r.* FROM `ptc_tag_rel` AS r
                        LEFT JOIN `ptc_tag` AS t ON r.`tag`=t.`id`
                    WHERE r.`objtype`='picture' AND r.`objid`=:id;";
            $rs = $db -> prepare($sql) -> execute(array(':id'=>$id));
            if (false === $rs)
            {
                $db -> rollback();
                return !self::$error = 502;
            }

            $_tags = array();
            foreach ($tags as $v)
            {
                if (!(int)$v) continue;
                $_tags[] = array('tag'=>(int)$v, 'objtype'=>'picture', 'objid'=>$id, 'value'=>'');
            }

            if ($_tags)
            {
                list($column, $sql, $value) = array_values(insert_array($_tags));
                $rs = $db -> prepare("INSERT INTO `ptc_tag_rel` {$column} VALUES {$sql};") -> execute($value);
                if (false === $rs)
                {
                    $db -> rollback();
                    return !self::$error = 503;
                }
            }
        }*/

        $rs2 = $db -> prepare("UPDATE `ptc_picture_gallery` SET `updatetime`=:time, `updater`=:uid WHERE `id`=:id") -> execute(array(':id'=>$gallery, ':uid'=>$_SESSION['uid'], ':time'=>NOW));
        if ($rs2 === false)
        {
            $db -> rollback();
            return !self::$error = 591;
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = 599;
        }

        return true;
    }
    // edit





    // 修改多个图片
    static public function edit_multi($ids, $titles, $gallery, $type, $subtype)
    {
        $db = db(config('db'));

        $db -> beginTrans();

        if (count($ids) > 50)
            return !self::$error = 1105;

        foreach ($ids as $k => $v)
        {
            $data = array(
                ':id'       => $v,
                ':title'    => $titles[$k],
                ':gallery'  => (int)$gallery,
                ':type'     => (int)$type,
                ':subtype'  => (int)$subtype,
                ':update'   => NOW,
            );

            $rs = $db -> prepare("UPDATE `ptc_picture` SET `title`=:title, `gallery`=:gallery, `type`=:type, `subtype`=:subtype, `update`=:update WHERE `id`=:id") -> execute($data);
            if ($rs === false)
            {
                $db -> rollback();
                return !self::$error = 500 + (int)$k;
            }
        }

        $rs2 = $db -> prepare("UPDATE `ptc_picture_gallery` SET `updatetime`=:time, `updater`=:uid WHERE `id`=:id") -> execute(array(':id'=>$gallery, ':uid'=>$_SESSION['uid'], ':time'=>NOW));
        if ($rs2 === false)
        {
            $db -> rollback();
            return !self::$error = 591;
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return !self::$error = 599;
        }

        return true;
    }
    // edit_multi


    // 删除图片
    static public function delete($ids)
    {
        if (!$ids) return !self::$error = 1109;
        $db = db(config('db'));

        $sql = "DELETE FROM `ptc_picture` WHERE `id` IN (:ids);";
        $rs = $db -> prepare($sql) -> execute(array(':ids'=>$ids));

        if($rs === false)
            return !self::$error = 501;

        return true;
    }
   

}