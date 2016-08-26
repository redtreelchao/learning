<?php
/**
 * 供应商
 +-----------------------------------------
 * @author bzs
 * @category
 * @version $Id$
 */
// debug switch
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

template::assign('nav', 'Supply');
template::assign('subnav', 'list');
template::assign('method', $method);


switch ($method)
{
    case 'area':

        if($_POST)
        {
            $do = empty($_POST['do']) ? null : $_POST['do'];
            switch ($do)
            {
                case 'edit':
                    $name = empty($_POST['name']) ? null : trim($_POST['name']);
                    //判断字段非空
                    if(!$name)
                        json_return(null,1,'请输入区域名称');

                    //查询是否已重复
                    $check = $db->prepare('SELECT COUNT(*) AS `c` FROM `ptc_area`  WHERE `name` = :name;')->execute(array(':name'=> $name));
                    if($check[0]['c']>0){
                        json_return(null,1,'重复的区域名称');
                    }

                    $id = empty($_POST['id']) ? null : intval(trim($_POST['id']));

                    $db -> beginTrans();

                    if($id)
                    {
                        //修改
                        $actionName = '修改';
                        $rs = $db->prepare("UPDATE `ptc_area` SET `name`=:name WHERE `id`=:id;")->execute(array(':name'=>$name,':id'=> $id));
                        $index = $id;
                    }
                    else
                    {
                        //添加
                        $actionName = '添加';
                        $rs = $db -> prepare("INSERT INTO `ptc_area` (`name`) VALUES (:name)") -> execute(array(':name'=> $name));
                        $index = $rs;
                    }

                    if(!$rs)
                    {
                        $db -> rollback();
                        json_return(null, 1, $actionName.'失败');
                    }

                    $history = history($rs, 'area', $actionName.'区域：'.$name , array('id'=>$index,'name'=>$name));

                    if ($history === false)
                    {
                        $db -> rollback();
                        json_return(null, 1, $actionName.'失败');
                    }

                    if (!$db -> commit())
                    {
                        $db -> rollback();
                        json_return(null, 9, $actionName.'失败');
                    }

                    json_return($name);
                    break;


                case 'del':

                    $id = empty($_POST['id']) ? null : intval(trim($_POST['id']));
                    if(!$id)
                        json_return(null,1,'错误的ID！');

                    $db -> beginTrans();

                    //修改supply表中区域记录
                    $areasql = "UPDATE `ptc_supply` SET `area` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `area`, ','), CONCAT(',',:id,','), ',')) WHERE FIND_IN_SET(:id, `area`)";
                    $areareplace = $db -> prepare($areasql) -> execute(array(':id'=>$id));
                    if($areareplace === false)
                    {
                        $db -> rollback();
                        json_return(null,1,'删除失败');
                    }

                    $rs = delete('ptc_area', 'id = '.$id,false);
                    if($rs === false)
                    {
                        $db -> rollback();
                        json_return(null,1,'删除失败');
                    }

                    // History
                    if (!history($id, 'area', '删除了区域', array('id'=>$id)))
                    {
                        $db -> rollback();
                        json_return(null, 1, '删除失败，请重试');
                    }

                    if (!$db -> commit())
                    {
                        $db -> rollback();
                        json_return(null, 9, $actionName.'失败');
                    }

                    json_return(array('id'=>$id));
                    break;
            }


        }

        $where = '1 = 1';
        $condition = array();
        $keyword = null;

        if (!empty($_GET['keyword']))
        {
            $keyword = trim($_GET['keyword']);
            $where .= " AND ( `name` LIKE :keyword )";
            $condition[':keyword'] = '%'.$keyword.'%';
        }
        template::assign('keyword', $keyword);

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_area` WHERE {$where};") -> execute($condition);
        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT `id`,`name` FROM `ptc_area` WHERE {$where} ORDER BY `id` ASC LIMIT {$limit};";
        $areas = $db -> prepare($sql) -> execute($condition);

        template::assign('page', $page->show());
        template::assign('areas', $areas);

        template::assign('subnav', 'area');
        template::display('supply/area');
        break;



    // 供应商信息
    case 'edit':
        $supply_contact = array(
            'business' => '业务',
            'finance'  => '财务',
            'service'  => '客服',
        );

        $countries = $db -> prepare("SELECT `id`,`name` FROM `ptc_district` WHERE `pid`=0") -> execute();
        $countries = array_column($countries, 'name', 'id');
        template::assign('countries', $countries);

        if ($_POST && empty($_POST['pid']))
        {
            $id = (int)$_POST['id'];

            $data = array(
                'code'        => trim($_POST['code']),
                'area'        => isset($_POST['area'])?implode(',',$_POST['area']):'',
                'name'        => (string)$_POST['name'],
                'org'         => intval($_POST['org']),
                'city'        => trim($_POST['city']),
                'tel'         => (string)$_POST['tel1'].' '.(string)$_POST['tel2'].' '.(string)$_POST['tel3'],
                'address'     => (string)$_POST['address'],
                'corporation' => (string)$_POST['corporation'],
                'type'        => (string)$_POST['type'],
                'mode'        => (string)$_POST['mode'],
                'payby'       => (string)$_POST['payby'],
                'period'      => (string)$_POST['period'],
                'bank'        => (string)$_POST['bank'],
                'bankaccount' => (string)$_POST['bankaccount'],
                'bankcode'    => (string)$_POST['bankcode'],
                'updatetime'  => NOW,
                'updater'     => $_SESSION['uid'],
            );

            if (!$data['code']) json_return(null, 1, '供应商ID不能为空');
            if (!$data['name']) json_return(null, 1, '供应商名称不能为空');
            if (!$_POST['area'] ) json_return(null, 1, '关联区域不能为空');
            if (!$data['city'] || $data['city'] == '请选择..' ) json_return(null, 1, '办公地址不能为空');
            if (!$_POST['tel1'] ) json_return(null, 1, '电话号码区号不能为空');
            if (!$_POST['tel2'] ) json_return(null, 1, '办公电话号码不能为空');
            if (!$data['type'] ) json_return(null, 1, '从事领域不能为空');
            if (!$data['mode'] ) json_return(null, 1, '合作方式不能为空');
            if (!$data['payby'] ) json_return(null, 1, '结算方式不能为空');
            if (!$data['bank'] ) json_return(null, 1, '开户行不能为空');
            if (!$data['bankaccount'] ) json_return(null, 1, '开户名称不能为空');
            if (!$data['bankcode'] ) json_return(null, 1, '银行账号不能为空');

            $check = $db -> prepare("SELECT `id` FROM `ptc_supply` WHERE (`name`=:name AND `address`=:address)".($id ? " AND `id`!='{$id}'" : ''))
                         -> execute(array(':name'=>$data['name'], ':address'=>$data['address']));
            if ($check)
                json_return(null, 1, '已存在相似供应商数据，请检查并确认');


            $db -> beginTrans();

            if ($id)
            {
                list($sql, $value) = array_values(update_array($data));
                $value[':id'] = $id;
                $rs = $db -> prepare("UPDATE `ptc_supply` SET {$sql} WHERE `id`=:id;") -> execute($value);
                if (false === $rs)
                {
                    $db -> rollback();
                    json_return(null, 2, '保存失败，请重试');
                }

                history($id, 'supply', '更新了供应商', $data);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($data));
                $rs = $db -> prepare("INSERT INTO `ptc_supply` {$column} VALUES {$sql};") -> execute($value);
                if (!$rs) json_return(null, 2, '保存失败，请重试');

                // History
                if (!history($rs, 'supply', '创建了供应商', $data))
                {
                    $db -> rollback();
                    json_return(null, 3, '保存失败，请重试');
                }


            }

            if (!$db -> commit())
            {
                $db -> rollback();
                json_return(null, 9, '保存失败，请重试');
            }

            json_return($rs);
        }elseif ($_POST&&$_POST['pid'])
        {
            $contact = array(
                'pid'    => (int)$_POST['pid'],
                'name'   => (string)$_POST['name'],
                'area'   => (string)$_POST['area'],
                'mobile' => (string)$_POST['mobile'],
                'tel'    => (string)$_POST['tel'],
                'email'  => (string)$_POST['email'],
                'cc'     => (string)$_POST['cc'],
                'fax'    => (string)$_POST['fax'],
                'other'  => (string)$_POST['other'],
                'type'   => (string)$_POST['type'],
            );

            //if (!$contact['area']) json_return(null, 1, '负责区域不能为空');
            if (!$contact['name']) json_return(null, 1, '姓名不能为空');
            if (!$contact['mobile']) json_return(null, 1, '手机不能为空');

            if ($_POST['id'])
            {
                list($sql, $value) = array_values(update_array($contact));
                $value[':id'] = $id;
                $rs = $db -> prepare("UPDATE `ptc_supply_contact` SET {$sql} WHERE `id`=:id;") -> execute($value);
                if (false === $rs)
                {

                    json_return(null, 2, '保存失败，请重试');
                }
                history($contact['pid'], 'supply', '更新了供应商联系人', $contact);
            }
            else
            {
                list($column, $sql, $value) = array_values(insert_array($contact));
                $rs = $db -> prepare("INSERT INTO `ptc_supply_contact` {$column} VALUES {$sql};") -> execute($value);
                if (!$rs)
                {
                    json_return(null, 2, '保存失败，请重试');
                }else{
                     json_return(null, $rs, '操作成功');
                }
                // History
                if (!history($contact['pid'], 'supply', '创建了供应商'.$supply_contact[$contact['type']].'联系人', $contact))
                {

                    json_return(null, 3, '保存失败，请重试');
                }

            }

        }elseif(!empty($_GET['id']))
        {
            $id = $_GET['id'];
            $sql  = "SELECT a.*,b.`pid` FROM `ptc_supply` AS a
                     LEFT JOIN `ptc_district` AS b ON a.`city` = b.`id` WHERE a.`id`=:id";
            $data = $db -> prepare($sql) -> execute(array(':id'=>$id));
            if (!$data)
            {
               template::assign('error', '供应商信息不存在或已删除');
            }
            $data = $data[0];

            template::assign('data', $data);

            $business =  $db -> prepare("SELECT * FROM `ptc_supply_contact` WHERE `pid`=:id AND type='business' ") -> execute(array(':id'=>$id));
            $finance  =  $db -> prepare("SELECT * FROM `ptc_supply_contact` WHERE `pid`=:id AND type='finance' ") -> execute(array(':id'=>$id));
            $service  =  $db -> prepare("SELECT * FROM `ptc_supply_contact` WHERE `pid`=:id AND type='service' ") -> execute(array(':id'=>$id));
            template::assign('business', $business);
            template::assign('finance', $finance);
            template::assign('service', $service);

            # 区域
            $areas = $db -> prepare("SELECT `id`,`name` FROM `ptc_area` ") -> execute();
            template::assign('areas', $areas);

            # 渠道
            $orgs = $db -> prepare("SELECT `id`,`name` FROM `ptc_org` ") -> execute();
            template::assign('orgs', $orgs);

            # 从事领域
            $types = array(
                    'hotel'     =>'酒店',
                    'food'      =>'美食',
                    'play'      =>'玩乐',
                    'traffic'   =>'交通',
            );
            template::assign('types', $types);

            # 支付方式
            $paybys = array(
                    1=>'预付',
                    2=>'周结',
                    4=>'月结',
                    3=>'一单一结',
                    
            );
            template::assign('paybys', $paybys);

            #操作记录
            $history = $db -> prepare("SELECT `id`,`time`,`intro`,`username` FROM `ptc_history` WHERE `pk`=:id AND `type`='supply' ORDER BY `time` DESC LIMIT 0,10;") -> execute(array(':id'=>$id));
            template::assign('history', $history);

            template::display('supply/edit');
        }else
        {
            # 区域
            $areas = $db -> prepare("SELECT `id`,`name` FROM `ptc_area` ") -> execute();
            template::assign('areas', $areas);

            # 渠道
            $orgs = $db -> prepare("SELECT `id`,`name` FROM `ptc_org` ") -> execute();
            template::assign('orgs', $orgs);

            # 从事领域
            $types = array(
                    'hotel'     =>'酒店',
                    'food'      =>'美食',
                    'play'      =>'玩乐',
                    'traffic'   =>'交通',
            );
            template::assign('types', $types);

            # 支付方式
            $paybys = array(
                    1=>'预付',
                    2=>'周结',
                    4=>'月结',
                    3=>'一单一结',
                    
            );
            template::assign('paybys',$paybys);
            template::display('supply/edit');
        }


        break;

    // Delete data
    case 'del':

        if ($_POST['pid']) #删除供应商
        {
            $id = (int)$_POST['pid'];
            $db -> beginTrans();
            $rs = delete('ptc_supply', "`id`='{$id}'", false);
            $rs = delete('ptc_supply_contact', "`pid`='{$id}'", false);
            if (!$rs)
            {
                $db -> rollback();
                json_return(null, 1, '操作失败，请重试..');
            }

            if ($db -> commit())
                json_return(1);
            else
                json_return(null, 9, '操作失败，请重试..');
        }else #删除联系人
        {
            $id  = (int)$_GET['id'];


            $rs = delete('ptc_supply_contact', "`id`='{$id}'", false);
            if (!$rs)
            {
                json_return(null, 1, '操作失败，请重试..');
            }else
            {
                history($pid, 'supply', '删除了供应商'.$supply_contact[$contact['type']].'联系人', '');
                json_return(null, 0, '操作成功');
            }


        }

        break;







    // Supply list
    case 'list':
    default:

        $countries = $db -> prepare("SELECT `id`,`name` FROM `ptc_district` WHERE `pid`=0 ORDER BY `id` ASC;") -> execute();
        $countries = array_column($countries, 'name', 'id');
        template::assign('countries', $countries);

        $areas = $db -> prepare("SELECT `id`,`name` FROM `ptc_area` ORDER BY `id` ASC") -> execute();
        $areas = array_column($areas, 'name', 'id');
        template::assign('areas', $areas);

        $where     = "1=1";
        $condition = array();
        $param     = array();
        template::assign('keyword', '');

        if (!empty($_GET['keyword']))
        {
            if (is_numeric($_GET['keyword']))
            {
                $where .= " AND a.id = :id";
                $condition[':id'] = (int)$_GET['keyword'];

            }
            else
            {
                $keyword = '%'.$_GET['keyword'].'%';
                $where .= " AND ( a.`name` LIKE :keyword  OR a.`address` LIKE :keyword OR a.`teL` LIKE :keyword )";
                $condition[':keyword'] = $keyword;
            }
            template::assign('keyword', $_GET['keyword']);
        }
        if (!empty($_GET['country']))
        {
            $where .= " AND c.pid = :country";
            $condition[':country'] = (int)$_GET['country'];
            $param['country'] = $_GET['country'];
        }
        if (!empty($_GET['city']))
        {
            $where .= " AND a.city = :city";
            $condition[':city'] = (int)$_GET['city'];
            $param['city'] = $_GET['city'];
        }
        if (!empty($_GET['area']))
        {
            $where .= " AND a.area = :area";
            $condition[':area'] = (int)$_GET['area'];
            $param['area'] = $_GET['area'];
        }
        if (!empty($_GET['code']))
        {
            $where .= " AND a.code = :code";
            $condition[':code'] = (string)$_GET['code'];
            $param['code'] = $_GET['code'];
        }
        if (!empty($_GET['name']))
        {
            $name   = '%'.$_GET['name'].'%';
            $where .= " AND a.name LIKE :name";
            $condition[':name'] = $name;
            $param['name'] = $_GET['name'];
        }
        if (!empty($_GET['payby']))
        {
            $where .= " AND a.payby = :payby";
            $condition[':payby'] = (int)$_GET['payby'];
            $param['payby'] = $_GET['payby'];
        }


        $join = "   LEFT JOIN `ptc_area` AS b ON a.`area` = b.`id`
                    LEFT JOIN `ptc_district` AS c ON a.`city` = c.`id`
                    LEFT JOIN `ptc_org` AS d ON a.`org` = d.`id`
                ";

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_supply` AS a {$join} WHERE {$where};") -> execute($condition);

        $page = new page($count[0]['c'], 15);
        $limit = $page -> limit();

        $sql = "SELECT a.* ,b.`name` AS `areaname`, c.`name` AS `city`, c.`pid` AS `countryid`,
                d.`name` AS `orgname`
                FROM `ptc_supply` AS a
                    {$join}
                WHERE {$where}
                ORDER BY a.`id` DESC
                LIMIT {$limit};";
        //echo $sql;die;
        $list = $db -> prepare($sql) -> execute($condition);

        if (IS_AJAX)
        {
            json_return(array('list'=>$list, 'page'=>$page->show()), 0, $keyword);
        }

        $payby = array(
            1=>'预付',
            2=>'后付',
            3=>'一单一结',
        );
        template::assign('payby',$payby);
        template::assign('page', $page->show());
        template::assign('list', $list);
        template::assign('param',$param);


        template::display('supply/list');
        break;


}
