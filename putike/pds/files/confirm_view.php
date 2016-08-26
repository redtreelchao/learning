<?php
/**
 * 确认单
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */

// session start
define("SESSION_ON", true);

// define config file
define("CONFIG", '/conf/web.php');

// debug switch
define("DEBUG", true);

// include common
include('../common.php');

// include project common functions
include(COMMON_PATH.'web_func.php');

// defined resources url
define('RESOURCES_URL', config('web.resources_url'));

$id = $_GET['id'];
$key = $_GET['key'];
if (!$id || !$key)
    redirect('http://www.putike.cn/');

$s = chr2dec(substr($key, 3, -3));
if ($s != 1000000 && $s - 1000000 != $id)
    redirect('http://www.putike.cn/');

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.urlencode('行程确认单').'.pdf"');
readfile(dirname(__FILE__).'/confirm_pdf/'.$id.'.pdf');