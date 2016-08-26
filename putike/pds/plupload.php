<?php
/**
 * 上传
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


$path = 'uploads/temp/';

import(CLASS_PATH.'extend/upload');
$upload = new upload();
$rs = $upload -> save(PT_PATH.$path, WEB_PATH.$path);

if ($upload -> file_upload_count)
{
    $file = PT_PATH . $path . $rs['file']['savename'];
    $body = file_get_contents($file);

    // api
    $uri_path = '/tour/' . date('Y/md') . '/' . $rs['file']['savename'];
    $uri = '/p-product-pic' . $uri_path;
    $ch = curl_init("http://v0.api.upyun.com{$uri}");
    $_headers = array('Expect:', 'Content-MD5' => md5($body));

    $length = strlen($body);
    $fh = fopen($file, 'rb');

    array_push($_headers, "Content-Length: {$length}");
    curl_setopt($ch, CURLOPT_INFILE, $fh);
    curl_setopt($ch, CURLOPT_INFILESIZE, $length);

    $date = gmdate('D, d M Y H:i:s \G\M\T');

    $sign = 'UpYun ' . picture::$upyun_usr . ':' . md5("PUT&{$uri}&{$date}&{$length}&" . md5(picture::$upyun_pwd));
    array_push($_headers, "Authorization: {$sign}");
    array_push($_headers, "Date: {$date}");

    curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POST, 1);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    fclose($fh);

    if ($http_code == 0)
        json_return(null , 1108, '上传失败，请重试');

    if ($http_code != 200)
        json_return(null , $http_code, '上传失败，请重试');

    $path = 'http://p-product-pic.b0.upaiyun.com'.$uri_path;
    json_return($path);
}
else
{
    json_return('', ($rs['file']['error'] ? $rs['file']['error'] : $upload->error), $upload -> get_error($rs['file']['error']));
}
