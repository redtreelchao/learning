<?php
/**
 * 行程设计师
 +-----------------------------------------
 * @category
 * @package designer
 * @author bzs
 * @version $Id$
 */
class designer extends api
{

    static $table = 'ptc_tour_designer';
    

    // error message
    static public $error_msg = array(
        '701'   => '查询日期不正确',
    );

  
   

    

    public static function db() {
        $db = db(config('db'));
        return $db;
    }


    
    static function count($map=[])
    {
        $where = scope_array($map);
        
        $sql   = "SELECT COUNT(id) AS num FROM ".self::$table." WHERE 1 = 1  $where ";
        $count = self::db() -> prepare($sql) -> execute();

        if($count)
        {
            return $count[0]['num'];
        }else
        {
            return 0;
        }
        
    }

    public static function page_list($map=[],$limit=10)
    {        
        $where = scope_array($map);        
        $sql   = 'SELECT a.*,b.name FROM '.self::$table.' AS a INNER JOIN `rbac_user` AS b ON a.uid = b.id  WHERE 1 = 1  '.$where.' LIMIT '.$limit;        
        $list = self::db() -> prepare($sql) -> execute( );
        return $list;
    }

    public static function find($id)
    {
        // type
        $where = "`id`=:id";
        $condition[':id'] = (int)$id;   
        $sql = 'SELECT * FROM '.self::$table." WHERE {$where} LIMIT 0,1"; 
        //echo $sql;die;       
        $rs = self::db() -> prepare($sql) -> execute($condition);
        //var_dump($rs);die;
        if(isset($rs)) return $rs[0];
        return $rs;
    }

    public static function find_by_sql($sql)
    {               
        $rs = self::db() -> prepare($sql) -> execute();
        return $rs;
    }

    public static function  add($data)
    {
        
        list($column, $value, $param) = array_values(insert_array($data));
        $sql = "INSERT INTO ".self::$table." {$column} VALUES {$value};";
        $rs  = self::db() -> prepare($sql) -> execute($param);
        if (false === $rs)
        {            
            return false;
        }
            return $rs;
       
    }


    public static function  modify($id,$data)
    {
        list($field, $value) = array_values(update_array($data));
        $value[':id'] = $id;
        $sql = "UPDATE ".self::$table." SET {$field} WHERE `id`=:id;";
        $rs = self::db() -> prepare($sql) -> execute($value);
        if (false === $rs)
        {           
            return !self::$error = '501';
        }
        return $rs;
    }

    public static function  remove($id)
    {
        $param[':id'] = $id;
        $sql = "DELETE FROM ".self::$table ." WHERE id=:id";
        
        $rs = self::db()->prepare($sql)->execute($param);
        if (false === $rs)
        {           
            return !self::$error = '501';
        }
        return $rs;
    }

    static function get()
    {
     
        $sql = "SELECT a.*,b.name FROM ".self::$table." AS a INNER JOIN `rbac_user` AS b ON a.uid = b.id " ;
               
        $users = self::db() -> prepare($sql) -> execute();
        return $users;
    }

    static function modify_by_area($area_id,$designers)
    {
        foreach ($designers as $k => $v)
        {
            $sql = "SELECT `area_ids` FROM ".self::$table." WHERE uid = $v LIMIT 0,1 ";
            $area_ids = self::db() -> prepare($sql) -> execute();

            if($area_ids)
            {
                //var_dump($area_ids);
                $areas = explode(',', $area_ids[0]['area_ids']);
                if(!in_array($area_id, $areas))
                {
                    $flag = self::modify_cities($area_id,$v,$area_ids[0]['area_ids']);
                }
            }else
            {
                    $flag = self::modify_cities($area_id,$v,$area_ids[0]['area_ids']);
            }
        }

        return $flag;
        
    }


    static function modify_cities($area_id,$uid,$area_ids)
    {
                $areas = empty($area_ids)?$area_id:$area_ids.','.$area_id;

                $sql = "UPDATE ".self::$table." SET area_ids = '$areas' WHERE `uid`=$uid;";
                //echo $sql;die;
                $rs = self::db() -> prepare($sql) -> execute();
                if (false === $rs)
                {           
                    return !self::$error = '501';
                }
                return $rs;
    }



   


}
