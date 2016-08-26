<?php
/**
 * 标签管理维护
 +-----------------------------------------
 * @category
 * @package tag
 * @author nolan.zhou
 * @version $Id$
 */
class tag extends api
{

    /**
     * Show Tag types
     +-----------------------------------------
     * @access public
     * @param  int  $type
     * @param  bool $tags
     * @return void
     */
    static function types($types=null, $tags=0)
    {
        $db = db(config('db'));

        $where = '`pid` IS NULL AND `default`=1';
        $condition = array();
        $_types = array(
            'amenity'   => '酒店设施',
            'facility'  => '房间设施',
            'service'   => '服务',
            'view'      => '周边',
            'design'    => '设计风格',
            'crowd'     => '人群',
            'atmosphere'        => '氛围',
            'characteristic'    => '其他特点',
            'catering'          => '食品饮料（室内）',
            'appliances'        => '多媒体科技',      // 电子电器
            'bathroom'          => '浴室',            // 浴室设施
            'washing'           => '备品品牌',        // 洗刷用品
            'othserve'          => '其他服务',
        );

        if ($types)
        {
            if (is_string($types))
                $types = explode(',', $types);

            $types = array_intersect($types, array_keys($_types));

            if ($types)
                $where .= ' AND `type` IN ("' . implode('","', $types) . '")';
            else
                $where .= ' AND 2=1';
        }

        $data = array();

        foreach($types as $v)
            $data[$v] = array('name'=>$_types[$v], 'key'=>$v, 'tags'=>array());

        if ($tags)
        {
            $tags = $db -> prepare("SELECT `id`, `code`, `name`, `type` FROM `ptc_tag` WHERE {$where} ORDER BY `type` ASC, `id` ASC;") -> execute();
            foreach($tags as $v)
            {
                $data[$v['type']]['tags'][] = $v;
            }
        }

        return array_values($data);
    }
    // types




    /**
     * Create a new tag
     +-----------------------------------------
     * @access public
     * @param  string   $name
     * @param  string   $type
     * @param  string   $editor
     * @param  int      $pid
     * @param  int      $default
     * @return void
     */
    static function create($name, $type, $editor='tag', $pid=null, $default=1)
    {
        $db = db(config('db'));

        if ((int)$pid)
            $check = $db -> prepare("SELECT * FROM `ptc_tag` WHERE `pid`=:pid AND `name`=:name") -> execute(array(':pid'=>(int)$pid, ':name'=>trim($name)));
        else
            $check = $db -> prepare("SELECT * FROM `ptc_tag` WHERE `pid` IS NULL AND `name`=:name") -> execute(array(':name'=>trim($name)));

        if ($check) return $check[0];

        $tag = array(
            'name'      => trim($name),
            'type'      => $type,
            'editor'    => $editor,
            'pid'       => empty($pid) ? null : (int)$pid,
            'default'   => $default,
        );

        $db -> beginTrans();

        list($column, $sql, $value) = array_values(insert_array($tag));
        $rs = $db -> prepare("INSERT INTO `ptc_tag` {$column} VALUES {$sql}") -> execute($value);
        if (!$rs)
        {
            $db -> rollback();
            return false;
        }

        $tag['id'] = $rs;
        $tag['code'] = 'T'.str_pad($rs, 2, '0', STR_PAD_LEFT);

        $rs = $db -> prepare("UPDATE `ptc_tag` SET `code`=:code WHERE `id`=:id") -> execute(array(':code'=>$tag['code'], ':id'=>$tag['id']));
        if (false === $rs || !$db -> commit())
        {
            $db -> rollback();
            return false;
        }

        return $tag;
    }
    // create




    /**
     * set value for tag(rel)
     +-----------------------------------------
     * @access public
     * @param int $id
     * @param string $value
     * @param string $text
     * @return void
     */
    static function setval($id, $value=null, $text=null)
    {
        $db = db(config('db'));
        $tag = $db -> prepare("SELECT r.*, t.`editor` FROM `ptc_tag_rel` r LEFT JOIN `ptc_tag` t ON t.`id` = r.`tag` WHERE r.`id`=:id") -> execute(array(':id'=>(int)$id));

        $db -> beginTrans();
        if (!is_null($value))
        {
            $rs = $db -> prepare("UPDATE `ptc_tag_rel` SET `value`=:value WHERE `id`=:id") -> execute(array(':id'=>(int)$id, ':value'=>$value));
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
        }

        if (!is_null($text) && $tag[0]['editor'] == 'text')
        {
            $rs = $db -> prepare("REPLACE INTO `ptc_tag_relext` (`id`, `text`) VALUES (:id, :text)") -> execute(array(':id'=>(int)$id, ':text'=>$text));
            if (false === $rs)
            {
                $db -> rollback();
                return false;
            }
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            return false;
        }

        return true;
    }
    // setvalue




    /**
     * Bind tag to object
     +-----------------------------------------
     * @access public
     * @param mixed $tag
     * @param mixed $objid
     * @param mixed $objtype
     * @param int $pid
     * @param mixed $value
     * @return void
     */
    static function bind($tag, $objid, $objtype, $pid=null, $value=null)
    {
        $db = db(config('db'));

        $sql = "SELECT * FROM `ptc_tag_rel` WHERE `tag`=:tag AND `objid`=:objid AND `objtype`=:objtype AND `pid`";
        $data = array(':tag'=>(int)$tag, ':objid'=>(int)$objid, ':objtype'=>trim($objtype));

        if ((int)$pid)
        {
            $sql .= '=:pid';
            $data[':pid'] = $pid;
        }
        else
        {
            $pid = null;
            $sql .= ' IS NULL';
        }

        $check = $db -> prepare($sql) -> execute($data);
        if ($check) return $check[0];

        $relation = array(
            'tag'       => (int)$tag,
            'objid'     => (int)$objid,
            'objtype'   => trim($objtype),
            'value'     => $value,
            'pid'       => empty($pid) ? null : (int)$pid,
        );

        list($column, $sql, $value) = array_values(insert_array($relation));
        $rs = $db -> prepare("INSERT INTO `ptc_tag_rel` {$column} VALUES {$sql}") -> execute($value);

        if ($rs)
            $relation['id'] = $rs;
        else
            $relation = false;

        return $relation;
    }
    // bind




    /**
     * Unbind tag from object
     +-----------------------------------------
     * @access public
     * @param mixed $tag
     * @param mixed $objid
     * @param mixed $objtype
     * @param mixed $pid
     * @return void
     */
    static function unbind($tag, $objid, $objtype, $pid)
    {
        $db = db(config('db'));

        $sql = "SELECT * FROM `ptc_tag_rel` WHERE `tag`=:tag AND `objid`=:objid AND `objtype`=:objtype AND `pid`";
        $data = array(':tag'=>(int)$tag, ':objid'=>(int)$objid, ':objtype'=>trim($objtype));

        if ((int)$pid)
        {
            $sql .= '=:pid';
            $data[':pid'] = $pid;
        }
        else
        {
            $sql .= ' IS NULL';
        }

        $check = $db -> prepare($sql) -> execute($data);

        if (!$check) return true;

        $rs = $db -> prepare("DELETE FROM `ptc_tag_rel` WHERE `id`=:id") -> execute(array(':id'=>$check[0]['id']));

        return $rs === false ? false : true;
    }
    // unbind




    /**
     * Load data from a tag
     +-----------------------------------------
     * @access public
     * @param  int $id
     * @return void
     */
    static function load($id)
    {
        $db = db(config('db'));

        $sql = "SELECT r.`id`, t.`id` AS `tag`, t.`code`, t.`name`, t.`editor`, r.`objid`, r.`objtype`, r.`value` FROM `ptc_tag_rel` AS r
                    LEFT JOIN `ptc_tag` AS t ON r.`tag` = t.`id`
                WHERE r.`id`=:id";
        $tag = $db -> prepare($sql) -> execute(array(':id'=>(int)$id));

        if (!$tag) return false;
        $tag = $tag[0];

        if ($tag['editor'] == 'tag')
        {
            $sql = "SELECT t.*, r.`id` AS `rel` FROM `ptc_tag` t
                        LEFT JOIN `ptc_tag_rel` r ON t.`id` = r.`tag` AND r.`objid`=:objid AND r.`objtype`=:objtype AND r.`pid`=:pid
                    WHERE
                        ((t.`pid`=:tag OR t.`pid` IS NULL) AND t.`type` IN ('other', 'price', 'transport') AND t.`default`=1) OR (r.`pid`=:pid)
                    GROUP BY t.`id`;";
            $data = array(
                ':tag'      => $tag['tag'],
                ':objid'    => $tag['objid'],
                ':objtype'  => $tag['objtype'],
                ':pid'      => $tag['id'],
            );

            $tags = $db -> prepare($sql) -> execute($data);
            $tag['tags'] = $tags;
        }
        else if ($tag['editor'] == 'text')
        {
            $intro = $db -> prepare("SELECT `text` FROM `ptc_tag_relext` WHERE `id`=:id") -> execute(array(':id'=>$id));
            $tag['text'] = $intro ? $intro[0]['text'] : '';
        }

        return $tag;
    }
    // load


}
