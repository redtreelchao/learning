<?php
/**
 * NATION
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

$method = empty($_GET['method']) ? 'list' : $_GET['method'];

template::assign('nav', 'Hotel');
template::assign('subnav', 'nation');

switch ($method)
{
    case 'edit':
        if($_POST)
        {
            $accepted = array_unique(array_filter($_POST['accepted']));
            $unaccepted = array_unique(array_filter($_POST['unaccepted']));

            if (!$accepted && !$unaccepted)
                json_return(null, 1, '请选择国籍要求');

            $_a = $_b = $_ak = $_bk = '';
            if ($accepted)
            {
                sort($accepted);
                $_ak = implode('/', $accepted);
                $accepted = '"' . implode('","', $accepted) . '"';
                $_a = $db -> prepare("SELECT * FROM `ptc_nation_code` WHERE BINARY `code` IN ({$accepted});") -> execute();
                foreach ($_a as $k => $v) $_a[$k] = $v['name'];
                $_a = nation_name($_a);
            }

            if ($unaccepted)
            {
                sort($unaccepted);
                $_bk = implode('/', $unaccepted);
                $unaccepted = '"' . implode('","', $unaccepted) . '"';
                $_b = $db -> prepare("SELECT * FROM `ptc_nation_code` WHERE BINARY `code` IN ({$unaccepted});") -> execute();
                foreach ($_b as $k => $v) $_b[$k] = $v['name'];
                $_b = nation_name($_b);
            }

            $name = $_a . ($_b ? (($_a ? '（除' : '除') . $_b . ($_a ? '）' : '')) : '') . '宾客';
            $code = $_ak . ($_bk ? '-'.$_bk : '');
            //var_dump(array('name'=>$name, 'code'=>$code)); exit;

            if ($db -> prepare("SELECT * FROM `ptc_nation` WHERE `code`=:code") -> execute(array(':code'=>$code)))
                json_return(null, 8, '该国籍要求已存在');

            if (!$_POST['id'])
                $rs = $db -> prepare("INSERT INTO `ptc_nation` (`name`, `code`, `updatetime`) VALUES (:name, :code, :time);")
                          -> execute(array(':name'=>$name, ':code'=>$code, ':time'=>NOW));
            else
                $rs = $db -> prepare("UPDATE `ptc_nation` SET `name`=:name, `code`=:code, `updatetime`=:time WHERE `id`=:id")
                          -> execute(array(':id'=>$_POST['id'], ':name'=>$name, ':code'=>$code, ':time'=>NOW));

            if (false === $rs) json_return(null, 9, '保存失败，请重试');

            if ($rs > 4000)
            {
                import(CLASS_PATH.'extend/email');
                $email = new email('PUTIKE API SYSTEM<system@putike.cn>', 'smtp.exmail.qq.com', 25, 'system@putike.cn', 'ptk123456');
                $email -> send('nolan.zhou@putike.cn', "§§ Nation ID is close to the threshold", charset_convert("国籍要求数据ID已接近阈值，请及时调整。", 'utf-8', 'gb2312'), '', 'jacky.yan@putike.cn');
            }

            json_return(array('id'=>$rs, 'name'=>$name, 'code'=>$code));
        }

        redirect('/nation.php');
        break;





    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            if (delete('ptc_nation', "`id`='{$id}'"))
                json_return(1);
            else
                json_return(null, 1, '操作失败，请重试..');
        }

        break;





    case 'list':
    default:

        $where = "`id` > 0";
        $condition = array();
        template::assign('keyword', '');

        if (!empty($_GET['bind']))
        {
            $bind = trim(big2gb::chg_utfcode($_GET['bind'], 'gb2312'));
            $keyword = str_replace(
                array(' ',',','，','/','.','、','\\','(',')','（','）','人','市场','宾客','和','及','且'),
                '%',
                $bind
            );
            $keyword = str_replace(
                array('不适用于','不适用','适用','不包括','除外','除'),
                array('%除%','%除%','%','%除%','%除%','%除%'),
                $keyword
            );

            $keywords = array_values(array_filter(explode('%', $keyword)));
            $len = count($keywords) - 1;
            if ($keywords[$len] == '除' && array_search('除', $keywords) === $len)
            {
                unset($keywords[$len]);
                array_unshift($keywords, '除');
            }
            //var_dump($keywords);

            $pos = array_search('除', $keywords);
            if ($pos === false)
            {
                $_a = nation_name($keywords);
                $_b = '';
            }
            else
            {
                $_a = nation_name(array_slice($keywords, 0, $pos));
                $_b = nation_name(array_slice($keywords, $pos+1));
            }
            //var_dump($_a); var_dump($_b);

            $keyword = $_a . ($_b ? (($_a ? '（除' : '除') . $_b . ($_a ? '）' : '')) : '') . '宾客';
            $_GET['keyword'] = $keyword;
        }

        if (!empty($_GET['keyword']))
        {
            $keyword = '%'.$_GET['keyword'].'%';
            $where .= " AND `name` LIKE :keyword";
            $condition[':keyword'] = $keyword;
            template::assign('keyword', $_GET['keyword']);
        }

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_nation` WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT * FROM `ptc_nation` WHERE {$where} ORDER BY `id` DESC LIMIT {$limit};") -> execute($condition);

        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()), 0, $keyword);
        }

        // 关联信息
        $supplies = supplies();
        foreach ($list as $k => $v)
        {
            $list[$k]['binds'] = array();
            foreach ($supplies as $supkey => $sup)
            {
                $supply = strtolower($supkey);
                $bind = $db -> prepare("SELECT `code`,`name` FROM `sup_{$supply}_nation` WHERE `bind`=:bind") -> execute(array(':bind'=>$v['id']));
                if ($bind)
                {
                    foreach ($bind as $b)
                    {
                        $list[$k]['binds'][] = array('code'=>$b['code'], 'name'=>"{$sup}: {$b['name']}");
                    }
                }
            }
        }

        template::assign('page', $page->show());
        template::assign('list', $list);

        $code = $db -> prepare("SELECT * FROM `ptc_nation_code`") -> execute();
        template::assign('code', $code);

        template::display('nation/list');
    break;

}



// build nation name
function nation_name($data)
{
    $hk = array_search('香港', $data);
    $mh = array_search('澳门', $data);
    $tw = array_search('台湾', $data);
    $jp = array_search('日本', $data);
    $kr = array_search('韩国', $data);

    if (count($data) > 3)
    {
        if ($hk !== false && $mh !== false && $tw !== false)
        {
            unset($data[$hk], $data[$mh], $data[$tw]);
            $data[] = '港澳台';
        }
        else if ($hk !== false && $mh !== false)
        {
            unset($data[$hk], $data[$mh]);
            $data[] = '港澳';
        }
        else if ($hk !== false && $tw !== false)
        {
            unset($data[$hk], $data[$tw]);
            $data[] = '港台';
        }
        if ($jp !== false && $kr !== false)
        {
            unset($data[$jp], $data[$kr]);
            $data[] = '日韩';
        }
    }

    sort($data);
    $data = implode('、', $data);

    $n = substr_count($data, '、');
    if ($n >= 3)
    {
        $pos = strrpos($data, '、');
        $data = substr_replace($data, '及', $pos, 3);
    }

    return $data;
}
?>
