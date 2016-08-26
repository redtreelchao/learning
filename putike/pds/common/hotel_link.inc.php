<?php
if (!defined("PT_PATH")) exit;

if($_POST){
    if (!empty($_POST['del']))
    {
        $id = (int)$_POST['id'];
        $rs = $db -> prepare("DELETE FROM `ptc_hotel_link` WHERE `id`=:id") -> execute(array(':id'=>$id));
        if ($rs)
            json_return(1);
        else
            json_return(null, 9, '操作失败，请重试..');
    }
    else
    {
        import(CLASS_PATH.'extend/string');
        if(!$_POST['name']) json_return(null, 1, '请输入来源名称');
        if(!string::check(trim($_POST['link']), 'url')) json_return(null, 1, '请输入规范的URL');

        if (!$_POST['id'])
        {
            $rs = $db -> prepare("INSERT INTO `ptc_hotel_link` (`hotel`,`name`, `link`) VALUES (:hotel, :name, :link)")
                      -> execute(array(':hotel'=>$_POST['hotel'],':name'=>trim($_POST['name']), ':link'=>trim($_POST['link'])));
        }
        else
        {
            $rs = $db -> prepare("UPDATE `ptc_hotel_link` SET `link`=:link, `name`=:name WHERE `id`=:id")
                      -> execute(array(':id'=>trim($_POST['id']), ':link'=>trim($_POST['link']),':name'=>trim($_POST['name'])));
        }

        if (false === $rs)
            json_return(null, 9, '操作失败，请重试');
        else
            json_return($rs);
    }
}

if (empty($_GET['id'])) redirect('./hotel.php');
$id = (int)$_GET['id'];

$hotel = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel` WHERE `id`=:id") -> execute(array(':id'=>$id));

$list = $db -> prepare("SELECT * FROM `ptc_hotel_link` WHERE `hotel`=:id ORDER BY `id` DESC;") -> execute(array(':id'=>$id));

template::assign('hotel', $hotel[0]);
template::assign('list', $list);
template::assign('page', '');
template::display('hotel/link');