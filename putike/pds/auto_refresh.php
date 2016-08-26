<?php

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

ignore_user_abort(true);
set_time_limit(120);

@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);
ob_start();
echo(str_repeat(' ', 2048));


$today = strtotime(date('Y-m-d').' 00:00:00');

$sql = 'SELECT * FROM
(
    SELECT h.`id`, h.`name`, p.`supply`, MAX(p.`update`) AS `lastupdate`, h.`HMC`, h.`JLT`, h.`CNB`, h.`ELG`
    FROM `ptc_hotel_price_date` AS p
        LEFT JOIN `ptc_hotel` AS h ON p.`hotel` = h.`id`
    WHERE p.`date` > '.$today.' AND p.`close` = 0 AND p.`supply` != "EBK"
    GROUP BY p.`hotel`, p.`supply`
) AS s
WHERE s.`lastupdate` < '.(NOW - 3600);

$db = db(config('db'));
$list = $db -> prepare($sql) -> execute();

foreach ($list as $k => $v)
{
    $supply = strtolower($v['supply']);
    if (empty($v[$v['supply']])) continue;

    echo (round(($k + 1) / count($list) * 100)) , '% ['.$supply.'] ';
    echo curl_file_get_contents('http://121.199.13.135/'.$supply.'.php?method=refresh&id='.$v['id'].'&auto='.md5('auto'.date('Y-m-d')), null, null, 60), '<br />';

    ob_flush();
    flush();
    usleep( 100000 );
}

?>
