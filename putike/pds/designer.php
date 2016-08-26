<?php
/**
 * 订单
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
template::assign('nav', 'Tour');
template::assign('subnav', 'designer');

$method = !empty($_GET['method']) ? $_GET['method'] : 'list';
$method_map = [
        'edit'    =>'edit',
        'delete'  =>'delete',        
        'list'    =>'get_list',
        'save'    => 'save',
        'remove'  => 'remove',                
        
        ];


if(array_key_exists($method, $method_map))
{    
    //echo $method_map[$method];die;
    call_user_func($method_map[$method]);
}


function get_list()
{
    $data['users'] = user::get();
    $data['areas'] = area::get();
    $param = [];
    $count = designer::count($param);
    $page  = new page($count, 10);
    $limit = $page -> limit();
    $list  = designer::page_list($param,$limit); 
    template::assign('list', $list);
    template::assign('page', $page -> show());
    template::assign('data',$data);      
    template::display('tour/designer');
           
}

function edit()
{
    $id    = intval($_GET['id']);
    $data['designer']  = designer::find($id);
    $data['users']     = user::get();
    $data['areas']     = area::get();
    $data['arr_city']  = explode(',', $data['designer']['areas']);
    //var_dump($data);die;
   
    template::assign('data', $data);
    template::display('tour/designer_edit');   
           
}



function save()
{
    $data = $_POST;
   // var_dump($data);die;
    if(!empty($data['areas']))
    {
        $data['areas'] = implode(',', $data['areas']);
        
    }

    if(isset($data['id']))
    {
        $id   = $data['id'];
        unset($data['id']);
        $data['updatetime'] = time();

        $flag = designer::modify($id,$data);
    }else
    {
        
        //var_dump($data);die;
        $flag = designer::add($data);  
    }
    
    json_return($flag);
}

function remove()
{
    $id = (int)$_GET['id'];
    $flag = designer::remove($id);  
    json_return($flag);
}


function charge_lines($aread_ids,$areas)
{
    $area = array_flip(explode(',', $aread_ids));    
    $areas = array_column($areas, 'name', 'id'); 
    return array_intersect_key($areas, $area);
  
}


        



    

