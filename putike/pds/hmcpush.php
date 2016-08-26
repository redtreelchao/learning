<?php
/**
 * HMC PUSH
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


ini_set('memory_limit', '256M');
ignore_user_abort(true);
set_time_limit(0);
//exit;

$db = db(config('db'));

$path = PT_PATH.'log/hmc/';
$files = scandir($path); //var_dump($files); exit;

// 备份推送文件
$bkdir = date('Ymd', NOW);

if(!is_dir($path.'_bak/'.$bkdir))
    mkdir($path.'_bak/'.$bkdir);

// 检索酒店信息
function _hotel($code)
{
    static $hotels;

    global $db;

    if (isset($hotels[$code]))
        return $hotels[$code];

    $_hotel = $db -> prepare("SELECT `id`,`name` FROM `ptc_hotel` WHERE `HMC`=:code;") -> execute(array(':code'=>$code));

    $hotels[$code] = $_hotel ? $_hotel[0] : false;

    return $hotels[$code];
}


$hotels = array();

// 遍历文件
foreach ($files as $file)
{
    if(false === strpos($file, '.xml')) continue;

    if(false === strpos($file, 'HMC_PUSHALLOT') && false === strpos($file, 'HMC_PUSHRATE')) // && false === strpos($file, 'HMC_PUSHBLOCKHT'))
    {
        unlink($path.$file);
        continue;
    }

    $_data  =  explode('_', $file);

    if(strpos($file, 'PUSHALLOT'))
    {
        // 更新房态
        $code = $_data[2];
    }
    else if (strpos($file, 'PUSHRATE'))
    {
        // 更新价格
        $code = $_data[3];
    }

    $hotel = _hotel($code);
    if (!$hotel)
    {
        unlink($path.$file);
        continue;
    }

    $xml = file_get_contents($path.$file);

    $dates = array();
    preg_match_all('/<STAYDATE>([0-9-]+)<\/STAYDATE>/', $xml, $dates);

    $min = $max = 0;
    $dates = isset($dates[1]) ? $dates[1] : $dates[0];
    foreach ($dates as $v)
    {
        list($date, $month, $year) = explode('-', $v);
        $time = mktime(0, 0, 0, (int)$date, (int)$month, "20{$year}");
        if (!$time) echo $v.'<br />';

        if ($min === 0 || $time < $min) $min = $time;
        if ($max === 0 || $time > $max) $max = $time + 86400;
    }

    // 文件改名,改名失败就忽略
    $newfile = $path.'_bak/'.$bkdir.'/'.$file;
    if (!rename($path.$file, $newfile)) continue;

    // 存值
    if (empty($hotels[$hotel['id']]))
    {
        $hotels[$hotel['id']] = array(
            'id'    => $hotel['id'],
            'code'  => $code,
            'name'  => $hotel['name'],
            'start' => $min,
            'end'   => $max,
            //'file'  => $file,
        );
    }
    else
    {
        if ($min < $hotels[$hotel['id']]['min'])
            $hotels[$hotel['id']]['min'] = $min;

        if ($max > $hotels[$hotel['id']]['max'])
            $hotels[$hotel['id']]['max'] = $max;
    }
}


@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);
ob_start();
echo(str_repeat(' ', 2048));

foreach ($hotels as $hotel)
{
    if (!$hotel['start'] || !$hotel['end'])
        $hotel['start'] = $hotel['end'] = 0;

    $rs = hmc::refresh($hotel['code'], null, $hotel['start'], $hotel['end']);

    echo $hotel['id'], ':', $hotel['name'], ' (', date('Y-m-d', $hotel['start']), '/', date('Y-m-d', $hotel['end']), ')',' ---- ', ($rs ? 'success' : 'fail'), '<br />';

    ob_flush();
    flush();
    usleep( 100000 );
}

?>
