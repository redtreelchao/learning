<?php
/**
 * CNB PUSH
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */

// session start
define("SESSION_ON", false);

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

set_time_limit(30);

$key = $iv = 'U+qFnPhJbiOTkX+e';

$md =  empty($GLOBALS["HTTP_RAW_POST_DATA"]) ? '' : $GLOBALS["HTTP_RAW_POST_DATA"];
//file_put_contents(PT_PATH.'log/test.log', $md);

$encrypted = pack("H*", $md);
$td = mcrypt_module_open( MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '' );
mcrypt_generic_init( $td, $key, $iv );
if (!$md) exit('30000');

$decrypted = mdecrypt_generic( $td, $encrypted );
mcrypt_generic_deinit( $td );
mcrypt_module_close( $td );

$xml = simplexml_load_string($decrypted);
if (!$xml)
{
    cnb::error('获取数据异常：'.$md);
    echo '30000'; exit;
}

$xmlArr = parse_xml($xml);
unset($xml);

switch ($xmlArr['Type'])
{
    case 1:
    case 2:
        cnb::hotel('', '', $xmlArr['HotelID']);
        $rs = true;
        break;

    case 3:
    case 4:
    case 5:
        $rs = cnb::refresh($xmlArr['HotelID'], $xmlArr['RoomID']);
        break;

    case 6:
    default:
        $rs = true;
}


if ($rs)
    echo '30000';
else
    echo '0';

?>