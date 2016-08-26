<?php
/**
 * Currency
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 * api juhe.com  putike  putike0532
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

template::assign('nav', 'Currency');
template::assign('subnav', 'currency');

switch ($method)
{
       // 编辑
    case 'edit':
        // 保存
        if ($_POST)
        {
            $data = array(
                'code'   => (string)trim($_POST['code']),
                'rate'   => (string)trim($_POST['rate']),

            );

            if (!$data['code']) json_return(null, 1, '货币编号不能为空');



            $check = $db -> prepare("SELECT `id` FROM `ptc_currency` WHERE (`code`=:code )".($_POST['id'] ? " AND `id`!='{$_POST['id']}'" : ''))
                         -> execute(array(':code'=>$data['code']));
            if ($check)
                json_return(null, 1, '已存在相同货币数据，请检查并确认');

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = (int)$_POST['id'];
                $rs = $db -> prepare("UPDATE `ptc_currency` SET {$sql} WHERE `id`=:id;") -> execute($value);
            }
            else
            {

                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_currency` {$column} VALUES {$sql};") -> execute($value);
            }

            if ($rs)
                json_return($rs);
            else
                json_return(null, 1, '保存失败，请重试');
        }


        $id = $_GET['id'];
        $data = $db -> prepare("SELECT * FROM `ptc_currency` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if (!$data)
            template::assign('error', '货币信息不存在或已删除');

        $data = $data[0];

    // 新建
    case 'new':

        if (!isset($data))
        {
            $data = null;
            $city = array();
        }

        template::assign('data', $data);
        template::assign('id', $data ? $data['id'] : null);


        template::display('currency/edit');
        break;




    // 删除
    case 'del':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            if (delete('ptc_currency', "`id`='{$id}'"))
                json_return(1);
            else
                json_return(null, 1, '操作失败，请重试..');
        }

        break;

    // 删除
    case 'currency':
        if ($_POST)
        {
            $code = $_POST['code'];
            $currency = get_currency($code);
            json_return($currency, 0, '');
        }

        break;





    case 'list':
    default:

        if ($_POST)
        {
            // 美元USD 韩元 KRW 日元JPY
            $type = $_POST['type'];
            $money= $_POST['money'];

            $ch     = curl_init();
            $url    = "http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency=$type&toCurrency=CNY&amount=2";
            $header = array('apikey: 1cb87555f73f8fb817c1f616999a877c');

            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch , CURLOPT_URL , $url);
            $res = curl_exec($ch);

            $currency = json_decode($res, true)['retData']['currency'];
            $money    = number_format($currency * $money, 2, '.', '');

            header('Content-Type: application/json; charset=utf-8');
            exit(json_encode(array('currency'=>$currency, 'money'=>$money)));
        }

        $where = "`id` > 0";
        $condition = array();
        template::assign('keyword', '');
        template::assign('keyword','');

        if (!empty($_GET['keyword']))
        {
            if (is_numeric($_GET['keyword']))
            {
                $where .= " AND a.`id` = :id";
                $condition[':id'] = (int)$_GET['keyword'];
            }
            else
            {
                $keyword = '%'.$_GET['keyword'].'%';
                $where .= " AND ( `code` LIKE :keyword )";
                $condition[':keyword'] = $keyword;
            }
            template::assign('keyword', $_GET['keyword']);
        }


        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_currency` WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 10);
        $limit = $page -> limit();

        $list = $db -> prepare("SELECT * FROM `ptc_currency` WHERE {$where} ORDER BY `id` DESC LIMIT {$limit};") -> execute($condition);

        foreach ($list as $k => $v)
        {
            $list[$k]['currency'] = get_currency($v['code']);
        }
        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()), 0, $keyword);
        }



        template::assign('page', $page->show());
        template::assign('list', $list);

        template::display('currency/list');
    break;

}

function get_currency($code)
{
    $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency='.$code.'&toCurrency=CNY&amount=2';
    //echo $url;die;
    $header = array(
        'apikey: 1cb87555f73f8fb817c1f616999a877c',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);
    $currency = json_decode($res)->retData->currency;

    return $currency  ;
}


?>
