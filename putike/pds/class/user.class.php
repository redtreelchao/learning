<?php
/**
 * 用户类
 +-----------------------------------------
 * @category putike
 * @package  api
 * @author   Page7
 * @version  $Id$
 */

class user extends api
{

    // error message
    static public $error_msg = array(
        '601'   => '用户名/手机号码不能为空',
        '602'   => '用户名不能为手机号',
        '603'   => '用户名不能为邮箱',
        '604'   => '手机号码格式错误',
        '605'   => '邮箱格式错误',
        '606'   => '昵称不能为空',
        '607'   => '密码不能为空',
        '608'   => '用户名、邮箱、手机号码存在重复用户',
        '609'   => '邮箱、手机号码存在重复',
        '610'   => '账号或密码错误',
        '611'   => '密码错误',
        '614'   => '账号不存在或已停用',
    );



    // 用户登录
    static function login($username, $password)
    {
        import(CLASS_PATH.'extend/string');

        if (string::check($username, 'email')) $key = 'email';
        else if (string::check($username, 'mobile')) $key = 'tel';
        else $key = 'username';

        $db = db(config('db'));

        $sql = "SELECT `id`, `name`, `username`, `tel`, `email`, `password`, `md`, `isdel` FROM `rbac_user` WHERE `{$key}`=:user";
        $user = $db -> prepare($sql) -> execute(array(':user'=>$username));

        if (!$user || $user[0]['password'] != md5(md5($password).$user[0]['md']))
        {
            return !self::$error = '610';
        }
        else
        {
            $user = $user[0];

            if ($user['isdel'] > 0)
                return !self::$error = '612';

            // token
            $user['token'] = 'U'.$user['id'].'M'.authcode($user['password'].NOW, $user['md'], 'ENCODE');

            if (!$user['email']) $user['email'] = '';

            unset($user['password'], $user['md']);
            // logintime
            $rs = $db -> prepare("UPDATE `rbac_user` SET `lastlogin`=:time WHERE `id`=:id") -> execute(array(':id'=>$user['id'], ':time'=>NOW));
            if ($rs === false)
            {
                $db -> rollback();
                return !self::$error = '501';
            }

            return $user;
        }
    }
    // login



    /**
     * verify account
     +-----------------------------------------
     * @access protected
     * @param string $token
     * @return void
     */
    static function verify($token)
    {
        $token = str_replace(' ', '+', $token);

        $pos = strpos($token, 'M');
        $uid = substr($token, 1, $pos-1);
        $token = substr($token, $pos+1);

        if (!$uid || !$token)
            self::format(NULL, '411', parent::$error_msg['411']);

        $db = db(config('db'));

        $user = $db -> prepare("SELECT `id`,`username`,`name`,`password`,`md`,`lastlogin` FROM `rbac_user` WHERE `id`=:id") -> execute(array(':id'=>$uid));
        if (!$user)
            self::format(NULL, '409', parent::$error_msg['409']);

        $mdstr = authcode($token, $user[0]['md'], 'DECODE');

        //if (substr($mdstr, -10) != $user[0]['lastlogin'])
          //  self::format(NULL, '413', parent::$error_msg['413']);

        if (substr($mdstr, 0, -10) != $user[0]['password'])
            self::format(NULL, '411', parent::$error_msg['411']);

        return $user[0];
    }
    // verify





    // 个人资料
    static function info($uid=0)
    {
        if (!$uid)
            $uid = array((int)$_SESSION['uid']);
        else if (is_numeric($uid))
            $uid = array((int)$uid);
        else
            $uid = array_filter(array_map('intval', explode(',', $uid)));

        $db = db(config('db'));

        $sql = "SELECT `id`, `username`, `name`, `email`
                FROM `rbac_user`
                WHERE `id` IN (".implode(',', $uid).") AND `isdel`=0;";
        $users = $db -> prepare($sql) -> execute();

        if (!$users)
            return !self::$error = '614';

        return count($uid) == 1 && count($users) == 1 ? $users[0] : $users;
    }
    // info

    static function get()
    {
        $db = db(config('db'));
        $sql = "SELECT `id`, `username`, `name`, `email`
                FROM `rbac_user`
                ";
        $users = $db -> prepare($sql) -> execute();
        return $users;
    }


}
