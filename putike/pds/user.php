<?php
/**
 * User
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */

// session start
define("SESSION_ON", true);

// define config file
define("CONFIG", './conf/web.php');

// debug switch
define("DEBUG", true);

// include common
include('./common.php');

// include project common functions
include(COMMON_PATH.'web_func.php');

// defined resources url
define('RESOURCES_URL', config('web.resources_url'));

// check permission
rbac_user();


$db = db(config('db'));

$method = empty($_GET['method']) ? 'user' : $_GET['method'];

template::assign('nav', 'User');

switch ($method)
{

    // Role
    case 'role':

        if ($_POST)
        {
            if (isset($_POST['name']))
            {
                if (!trim($_POST['name']))
                    json_return(null, 1, '角色名称不正确');

                $rs = $db -> prepare("INSERT INTO `rbac_role` (`name`) VALUES (:name)") -> execute(array(':name' => trim($_POST['name'])));
                if ($rs)
                    json_return($rs);
                else
                    json_return(null, 1, '保存失败，请重试');
            }
        }

        $sel = empty($_GET['id']) ? 0 : (int)$_GET['id'];
        template::assign('id', $sel);

        $role = $db -> prepare("SELECT * FROM `rbac_role` WHERE 1=1 ORDER BY `id` ASC") -> execute();
        template::assign('role', $role);

        $method = $db -> prepare("SELECT * FROM `rbac_method` WHERE `pid`='0' ORDER BY `id` ASC") -> execute();
        foreach ($method as $k => $v)
        {
            $method[$k]['sub'] = $db -> prepare("SELECT * FROM `rbac_method` WHERE `pid`=:pid ORDER BY `id` ASC") -> execute(array(':pid'=>$v['id']));
        }
        template::assign('method', $method);

        template::assign('subnav', 'role');
        template::display('user/role');
        break;



    // Method
    case 'method':
        if ($_POST)
        {
            $data = array(
                'id'        => '',
                'ico'       => '',
                'method'    => trim($_POST['method']),
                'name'      => trim($_POST['name']),
                'pid'       => trim($_POST['pid']),
                'default'   => 0,
            );

            if (!array_filter($data)) json_return(null, 1, '数据不完整，请重试');

            $sql = "SELECT a.*, COUNT(b.`id`) AS `count` FROM `rbac_method` AS a LEFT JOIN `rbac_method` AS b ON a.id = b.pid WHERE a.`id`=:id";
            $parent = $db -> prepare($sql) -> execute(array(':id'=>$data['pid']));
            if (!$parent) json_return(null, 1, '父级单元不存在');
            $parent = $parent[0];

            $data['id'] = $data['pid'].str_pad($parent['count'], 2, '0', STR_PAD_LEFT);

            list($column, $sql, $value) = array_values(insert_array($data));
            $rs = $db -> prepare("INSERT INTO `rbac_method` {$column} VALUES {$sql}") -> execute($value);

            if ($rs === false)
                json_return(null, 9, '保存失败，请重试');
            else
                json_return($data);
        }
        break;



    // User
    case 'user':
    default:

        // Edit & Save
        if ($_POST)
        {
            import(CLASS_PATH.'extend/string');
            $md = string::rand_string(4, 0);
            if ($_POST['id'])
            {
                $data = array(
                    'id'    => (int)$_POST['id'],
                    'tel'   => trim($_POST['tel']),
                    'email' => trim($_POST['email']),
                    'role'  => (int)$_POST['role'],
                );

                if ($_POST['password'])
                {
                    if ($_POST['password'] != $_POST['_password']) json_return(null, 1, '两次密码输入不一致');
                    $data['password'] = md5(md5($_POST['password']).$md);
                    $data['md'] = $md;
                }
            }
            else
            {
                $data = array(
                    'username'  => trim($_POST['username']),
                    'name'      => trim($_POST['name']),
                    'password'  => md5(md5($_POST['password']).$md),
                    'md'        => $md,
                    'tel'   => trim($_POST['tel']),
                    'email' => trim($_POST['email']),
                    'role'  => (int)$_POST['role'],
                );

                if (!$data['username']) json_return(null, 1, '用户名不能为空');
                if (string::check($_POST['username'], 'mobile')) json_return(null, 1, '用户名不能为手机号');
                if (string::check($_POST['username'], 'email')) json_return(null, 1, '用户名不能为邮箱');
                if (!$data['name']) json_return(null, 1, '姓名不能为空');
                if (!$_POST['password']) json_return(null, 1, '密码不能为空');
                if ($_POST['password'] != $_POST['_password']) json_return(null, 1, '两次密码输入不一致');
            }

            if (!$data['role'] && $_SESSION['role'] != 0) json_return(null, 1, '请设置用户系统角色（非管理员）');
            if ($_POST['tel'] && !string::check($_POST['tel'], 'mobile')) json_return(null, 1, '手机号码格式错误');
            if ($_POST['email'] && !string::check($_POST['email'], 'email')) json_return(null, 1, '邮箱格式错误');

            list($column, $seq, $value) = array_values(insert_array($data));
            $_columns = update_column(array_keys($data));
            $rs = $db -> prepare("INSERT INTO `rbac_user` {$column} VALUES {$seq} ON DUPLICATE KEY UPDATE {$_columns};") -> execute($value);
            if ($rs === false)
                json_return(null, 9, '保存失败，请重试');
            else
                json_return(1);
        }


        // Load User Infromation
        if (!empty($_GET['id']))
        {
            $user = $db -> prepare("SELECT `id`,`username`,`name`,`tel`,`email`,`role` FROM `rbac_user` WHERE `id`=:id") -> execute(array(':id'=>$_GET['id']));
            if (!$user) json_return(null, 1, '未找到用户信息');

            json_return($user[0]);
        }


        // Delete User
        if (!empty($_GET['del']))
        {
            if (delete('rbac_user', "`id`='{$_GET['del']}'"))
                json_return(1);
            else
                json_return(null, 1, '操作失败，请重试..');
        }


        // User list
        $where = '1=1';
        $condition = array();
        $keyword = '';

        if (!empty($_GET['keyword']))
        {
            $keyword = $_GET['keyword'];
            $where .= " AND a.`name` LIKE :keyword";
            $condition[':keyword'] = '%'.$keyword.'%';
        }
        template::assign('keyword', $keyword);

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `rbac_user` AS a WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT a.*, b.`name` AS `role` FROM `rbac_user` AS a LEFT JOIN `rbac_role` AS b ON a.`role` = b.`id` WHERE {$where} ORDER BY a.`id` ASC LIMIT {$limit};") -> execute($condition);
        template::assign('page', $page->show());
        template::assign('list', $list);

        $role = $db -> prepare("SELECT * FROM `rbac_role` WHERE 1=1") -> execute();
        template::assign('role', $role);

        template::assign('subnav', 'user');
        template::display('user/user');
}


?>