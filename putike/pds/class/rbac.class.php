<?php
// Role-Base Access Control
class rbac extends base
{

    // user login data
    static function user($uid=null)
    {
        if (empty($_SESSION['uid']))
        {
            if (!empty($_COOKIE['sess']))
            {
                $s = $_COOKIE['sess'];
                $pos = strpos($s, 'p');
                $uid = substr($s, 0, $pos);
                $ck = $uid % 4;
                $password = substr($s, $pos+1, 32 - $ck);
                $md = substr($s, -$ck);

                $db = db(config('db'));

                $sql = "SELECT u.`id`, u.`name`, u.`password`, u.`md`, u.`role` FROM `rbac_user` AS u WHERE u.`id`=:uid";
                $user = $db -> prepare($sql) -> execute(array(':uid'=>$uid));

                if (!$user || substr($user[0]['password'], $ck) != $password || strtolower(substr($user[0]['md'], -$ck)) != $md)
                {
                    setcookie('sess', '', time()-3600);
                    redirect('/login.php');
                }
                else
                {
                    $_SESSION['uid'] = $user[0]['id'];
                    $_SESSION['name'] = $user[0]['name'];
                    $_SESSION['role'] = $user[0]['role'];
                }
            }
            else
            {
                redirect('/login.php');
            }
        }
    }
    // user




    // access
    static function access($method)
    {
        $uid  = (int)$_SESSION['uid'];
        $role = (int)$_SESSION['role'];

        $db = db(config('db'));

        $sql = 'SELECT r.`id`
                FROM `rbac_rel` AS r
                    LEFT JOIN `rbac_method` AS m ON r.`method` = m.`id`
                WHERE ((r.`id`=:uid AND r.`type`="user") OR (r.`id`=:role AND r.`type`="role")) AND m.`method`=:method';
        $access = $db -> prepare($sql) -> execute(array(':uid'=>$uid, ':role'=>$role, ':method'=>$method));

        return $access ? true : false;
    }
    // access





    // get methods
    static function method($method='', $level=0)
    {
        $uid  = (int)$_SESSION['uid'];
        $role = (int)$_SESSION['role'];

        $db = db(config('db'));

        if (!$method && !$level) return array();

        if (!$method)
        {
            $_m = $db -> prepare('SELECT * FROM `rbac_method` WHERE `method`=:method') -> execute(array(':method'=>$method));
            $id = $_m[0]['id'];
        }
        else
        {
            $id = '';
        }

        $code = $id . str_repeat('__', $level);

        $sql = 'SELECT m.`method` FROM `rbac_method` AS m
                    LEFT JOIN `rbac_rel` AS r ON r.`method`=m.`id`
                WHERE (m.`id`=:id OR m.`id` LIKE :code) AND ((r.`id`=:uid AND r.`type`="user") OR (r.`id`=:role AND r.`type`="role"))
                ORDER BY m.`pid` ASC, m.`id` ASC;';
        $_methods = $db -> prepare($sql) -> execute(array(':id'=>$id, ':code'=>$code, ':uid'=>$uid, ':role'=>$role));

        $methods = array();
        foreach ($_methods as $v)
        {
            $methods[$v['method']] = 1;
        }

        return $methods;
    }
    // method





}

?>