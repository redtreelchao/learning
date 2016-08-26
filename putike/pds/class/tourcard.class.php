<?php
/**
 * 行程定制卡
 +-----------------------------------------
 * @category
 * @package designer
 * @author bzs
 * @version $Id$
 */
class tourcard extends api
{

    static $table = 'ptc_tour_card';

    

    public static function db() {
        $db = db(config('db'));
        return $db;
    }


    
    static function count($map=[])
    {
        $where = self::scope_array($map);
        
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
        $where = self::scope_array($map);        
        $sql   = 'SELECT * FROM '.self::$table.'   WHERE 1 = 1  '.$where.' ORDER BY id DESC LIMIT '.$limit;        
        $list = self::db() -> prepare($sql) -> execute( );
        return $list;
    }

    public static function find($id)
    {
        // type
        $where = "`id`=:id";
        $condition[':id'] = (int)$id;   
        $sql = 'SELECT * FROM '.self::$table." WHERE {$where} LIMIT 0,1";        
        $rs = self::db() -> prepare($sql) -> execute($condition);
        if(isset($rs)) return $rs[0];
        return $rs;
    }

    public static function findbysql($sql)
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
     
        $sql = "SELECT * ".self::$table ;
               
        $users = self::db() -> prepare($sql) -> execute();
        return $users;
    }

    # 获取两个时间戳相差的天数
    static function get_days($timestamp1,$timestamp2)
    {
         //echo $timestamp1,$timestamp2;
         $time1 = new DateTime(); // string date
         $time1->setTimestamp($timestamp1);
         $time2 = new DateTime();
         $time2->setTimestamp($timestamp2); // timestamps,  it can be string date too.
         $interval =  $time2->diff($time1);
         //var_dump($interval);die;
         return  $interval->days;
    }

    // 将数组拆分成查询条件
    public static function scope_array($map,$operator='AND')
    {
        $where = ' ';
        if($map)
        {
            foreach ($map as $k => $v)
            {
                if($v[0]=='in')
                {
                    $val = "(".$v[1].")";
                }elseif($v[0]=='between')
                {
                    $val = $v[1];
                }elseif($v[0]=='FIND_IN_SET')
                {
                    $val = "FIND_IN_SET($v[1],$k)";
                    $k   ='';
                    $v[0] = '';
                }
                else
                {
                    $val = "'".$v[1]."'";
                }
                $where .= $operator.' '.$k.' '.$v[0].' '.$val.' ';
            }
        }

        return $where;
    }



   


}
