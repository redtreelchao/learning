<?php
if (!defined("PT_PATH")) exit;

if ($_POST)
{
    switch($_GET['action'])
    {
        case 'save':
            if (empty($_POST['name']) || empty($_POST['type']) || empty($_POST['editor']))
                json_return(null, 1, '标签资料不完善');

            if (empty($_POST['id']))
            {
                $default = isset($_POST['default']) ? 1 : 0;
                $tag = tag::create($_POST['name'], $_POST['type'], $_POST['editor'], $_POST['pid'], $default);
            }
            else
            {
            }

            if ($tag)
                json_return($tag);
            else
                json_return(null, 1, '标签保存失败，请重试');
            break;

        case 'set':
            $s = (int)$_POST['status'];
            if ($s)
                $rs = tag::bind($_POST['tag'], $_POST['hotel'], 'hotel', $_POST['pid'], '');
            else
                $rs = tag::unbind($_POST['tag'], $_POST['hotel'], 'hotel', $_POST['pid']);

            if ($rs)
                json_return($rs);
            else
                json_return(null, 1, '操作失败，请重试');
            break;

        case 'load':
            $tag = tag::load($_POST['id']);
            json_return($tag);
            break;

        case 'setval':
            $name = trim($_POST['name']);
            $value = trim($_POST['value']);
            if (!$name) json_return(null, 1, '提交的数据不完整');

            if (in_array($name, array('value', 'text')))
            {
                if ($name == 'value')
                    $rest = tag::setval($_POST['id'], $value);
                else
                    $rest = tag::setval($_POST['id'], null, $value);
            }
            else
            {
                $hotel = (int)$_POST['hotel'];
                if (!$db -> prepare("SELECT * FROM `ptc_hotel_ext` WHERE `id`=:id") -> execute(array(':id'=>$hotel)))
                {
                    $db -> prepare("INSERT INTO `ptc_hotel_ext` (`id`) VALUES (:id)") -> execute(array(':id'=>$hotel));
                }

                $rest = $db -> prepare("UPDATE `ptc_hotel_ext` SET `{$name}`=:value WHERE `id`=:id;")  -> execute(array(':value'=>$value, ':id'=>$hotel));
            }

            if($rest === false)
                json_return(null, 1, '保存失败，请重试');
            else
                json_return(1);
            break;
    }
    exit;
}

if (empty($_GET['id'])) redirect('./hotel.php');
$id = (int)$_GET['id'];

$hotel = $db -> prepare("SELECT e.*,h.`id`,h.`country`,h.`city`,h.`name` FROM `ptc_hotel` h LEFT JOIN `ptc_hotel_ext` e ON e.`id`=h.`id` WHERE h.`id`=:id") -> execute(array(':id'=>$id));
template::assign('hotel', $hotel[0]);

$sql = "SELECT t.*, r.`id` AS `rel` FROM `ptc_tag` t
            LEFT JOIN `ptc_tag_rel` r ON t.`id` = r.`tag` AND r.`objid`=:hotel AND r.`objtype`='hotel' AND r.`pid` IS NULL
        WHERE t.`pid` IS NULL AND t.`type`=:type AND t.`default`=1";

$country = $db -> prepare("SELECT * FROM `ptc_district` WHERE `pid`=0 ORDER BY `id` ASC;") -> execute();
template::assign('country', $country);

$city = $db -> prepare("SELECT `id`,`name` FROM `ptc_district` WHERE `pid`=:id") -> execute(array(':id'=>$hotel[0]['country']));
template::assign('city', $city);

$district = $db -> prepare("SELECT `id`,`name` FROM `ptc_district_ext` WHERE `pid`=:id") -> execute(array(':id'=>$hotel[0]['city']));
template::assign('district', $district);

$amenity = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel[0]['id'], ':type'=>'amenity'));
template::assign('amenity', $amenity);

$facility = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel[0]['id'], ':type'=>'facility'));
template::assign('facility', $facility);

$service = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel[0]['id'], ':type'=>'service'));
template::assign('service', $service);

$sql = "SELECT t.*, r.`id` AS `rel`, v.`type` FROM `ptc_tag` t
            LEFT JOIN `ptc_view` v ON t.`code` = CONCAT('V', v.id)
            LEFT JOIN `ptc_tag_rel` r ON t.`id` = r.`tag` AND r.`objid`=:hotel AND r.`objtype`='hotel' AND r.`pid` IS NULL
        WHERE t.`pid` IS NULL AND t.`type`=:type AND r.`id` IS NOT NULL";
$view = $db -> prepare($sql) -> execute(array(':hotel'=>$hotel[0]['id'], ':type'=>'view'));
template::assign('view', $view);

template::display('hotel/tag');