<?php
/**
 * Reports
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

$method = empty($_GET['method']) ? 'home' : $_GET['method'];

template::assign('nav', 'Reports');

define('BASE_URL', '/');

if (isset($_GET['plugins']))
{
    $data = array();
    $plugins = $_GET['plugins'];
    foreach ($plugins as $plugin)
    {
        $template = 'reports/'.$plugin;
        $html = '';
        $script = array();

        switch ($plugin)
        {
            case 'product-change':

                $start = empty($_GET['start']) ? 0 : (int)strtotime($_GET['start']);
                $end   = empty($_GET['end']) ? 0 : (int)strtotime($_GET['end']);

                if (!$start) $start = strtotime(date('Y-m-1'));
                if (!$end) $end = strtotime(date('Y-m-1').' +1 month -1 day');

                template::assign(array('start'=>$start, 'end'=>$end));

                $online = $db   -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `onlinetime`>=:start AND `onlinetime`<:end")
                                -> execute(array(':start'=>$start, ':end'=>$end));

                $offline = $db  -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_product` WHERE `offlinetime`>=:start AND `offlinetime`<:end")
                                -> execute(array(':start'=>$start, ':end'=>$end));

                $script = array('js/amcharts/amcharts.js', 'js/amcharts/pie.js', 'js/jquery.zdatepicker.js', 'css/zdatepicker.css');

                template::assign(array('online'=>$online[0]['c'], 'offline'=>$offline[0]['c']));
                break;

            case 'order-area':

                $start = empty($_GET['start']) ? 0 : (int)strtotime($_GET['start']);
                $end   = empty($_GET['end']) ? 0 : (int)strtotime($_GET['end']);

                if (!$start) $start = strtotime(date('Y-m-1'));
                if (!$end) $end = strtotime(date('Y-m-1').' +1 month -1 day');

                template::assign(array('start'=>$start, 'end'=>$end));

                $script = array('js/ammap/ammap.css', 'js/ammap/ammap.js', 'js/ammap/lang/zh.js');

                if (isset($_GET['country']))
                {

                    $sql = 'SELECT c.`id`, COUNT(*) AS `num`, c.`name`, c.`lat`, c.`lng`
                            FROM `ptc_order_hotel` AS h
                                LEFT JOIN `ptc_order` AS o ON o.`id` = h.`orderid`
                                LEFT JOIN `ptc_district` AS c ON h.`city` = c.`id`
                            WHERE o.`create`>=:start AND o.`create`<:end AND o.`status` > 3 AND o.`status` != 11 AND h.`country` = 1
                            GROUP BY h.city';

                    $script[] = 'js/ammap/maps/js/chinaHigh.js';

                    template::assign('map', 'chinaHigh');
                }
                else
                {
                    $sql = 'SELECT c.`id`, COUNT(*) AS `num`, c.`name`, c.`lat`, c.`lng`
                            FROM `ptc_order_hotel` AS h
                                LEFT JOIN `ptc_order` AS o ON o.`id` = h.`orderid`
                                LEFT JOIN `ptc_district` AS c ON h.`country` = c.`id`
                            WHERE o.`create`>=:start AND o.`create`<:end AND o.`status` > 3 AND o.`status` != 11
                            GROUP BY h.`country`';

                    $script[] = 'js/ammap/maps/js/worldLow.js';

                    template::assign('map', 'worldLow');
                }

                $country = $db -> prepare($sql) -> execute(array(':start'=>$start, ':end'=>$end));

                template::assign('data', $country ? $country : array());
                break;

            case 'order-area-type':

                $start = empty($_GET['start']) ? 0 : (int)strtotime($_GET['start']);
                $end   = empty($_GET['end']) ? 0 : (int)strtotime($_GET['end']);

                if (!$start) $start = strtotime(date('Y-m-1'));
                if (!$end) $end = strtotime(date('Y-m-1').' +1 month -1 day');

                template::assign(array('start'=>$start, 'end'=>$end));

                $sql = 'SELECT COUNT(*) AS `count`,
                            IF(c.`pid` != 1, "海外", IF(c.`province` IN ("江苏","浙江") OR c.`id`=3, "华东", "国内小长线")) AS `name`,
                            IF(c.`pid` != 1, "#5bc0de", IF(c.`province` IN ("江苏","浙江") OR c.`id`=3, "#f0ad4e", "#5cb85c")) AS `color`
                        FROM `ptc_order_hotel` AS h
                            LEFT JOIN `ptc_order` AS o ON o.`id` = h.`orderid`
                            LEFT JOIN `ptc_district` AS c ON h.`city` = c.`id`
                        WHERE o.`create`>=:start AND o.`create`<:end AND o.`status` > 3 AND o.`status` != 11
                        GROUP BY IF(c.`pid` != 1, 1, IF(c.`province` IN ("江苏","浙江") OR c.`id`=3, 2, 3))';
                $types = $db -> prepare($sql) -> execute(array(':start'=>$start, ':end'=>$end));
                template::assign('data', $types ? $types : array());

                $script = array('js/amcharts/amcharts.js', 'js/amcharts/pie.js');
                break;


            case 'product-count':
                $product = $db  -> prepare("SELECT COUNT(*) AS `c`, `type`, `payment` FROM `ptc_product` WHERE `status`=1 GROUP BY `type`, `payment`") -> execute();
                $_data = array(
                    '1-ticket'  => array('name' => '酒店券类', 'count' => 0, 'color'=>'#f0ad4e'),
                    '7-ticket'  => array('name' => '生鲜/商品', 'count' => 0, 'color'=>'#5cb85c'),
                    //'1-prepay'  => array('name' => '酒店预付', 'count' => 0, 'color'=>'#337ab7'),
                    '2-prepay'  => array('name' => '车加酒',   'count' => 0, 'color'=>'#5bc0de'),
                    '4-prepay'  => array('name' => '机加酒',   'count' => 0, 'color'=>'#d9534f'),
                );

                foreach ($product as $v)
                {
                    $_key = $v['type'].'-'.$v['payment'];
                    if (empty($_data[$_key])) continue;
                    $_data[$_key]['count'] = $v['c'];
                }

                template::assign('data', array_values($_data));

                $script = array('js/amcharts/amcharts.js', 'js/amcharts/pie.js');
                break;

            case 'hotel-count':

                $sql = "SELECT COUNT(*) AS c, `type`, `payment`
                        FROM
                            (
                            SELECT i.`objpid` AS `hotel`, p.`type`, p.`payment` FROM `ptc_product_item` AS i
                                LEFT JOIN `ptc_product` AS p ON i.pid  = p.id
                            WHERE p.`status`=1
                            GROUP BY i.`objpid`, p.`type`, p.`payment`
                            ) AS s
                        GROUP BY `type`, `payment`";

                $hotel = $db  -> prepare($sql) -> execute();
                $_data = array(
                    '1-ticket'  => array('name' => '酒店券类', 'count' => 0, 'color'=>'#f0ad4e'),
                    //'7-ticket'  => array('name' => '生鲜/商品', 'count' => 0, 'color'=>'#5cb85c'),
                    //'1-prepay'  => array('name' => '酒店预付', 'count' => 0, 'color'=>'#337ab7'),
                    '2-prepay'  => array('name' => '车加酒',   'count' => 0, 'color'=>'#5bc0de'),
                    '4-prepay'  => array('name' => '机加酒',   'count' => 0, 'color'=>'#d9534f'),
                );
                foreach ($hotel as $v)
                {
                    $_key = $v['type'].'-'.$v['payment'];
                    if (empty($_data[$_key])) continue;
                    $_data[$_key]['count'] = $v['c'];
                }

                template::assign('data', array_values($_data));

                $script = array('js/amcharts/amcharts.js', 'js/amcharts/pie.js');
                break;
        }

        $html = template::fetch($template);
        template::clear();

        $data[$plugin] = array('html'=>$html, 'script'=>$script);
    }

    json_return($data);
    exit;
}




template::display('reports/index');