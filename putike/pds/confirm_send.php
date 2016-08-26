<?php
/**
 * 发送确认单
 *
 * @author Nolan
 * @category Project
 * @copyright Copyright(c) 2016
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

$id = (int)$_GET['id'];

if ($_GET['debug'] != md5('putikeconfirmation'.date('Ymd'))) exit('0');

$db = db(config('db'));

$sql = "SELECT s.*, c.`product`
        FROM `ptc_order_confirmation_send` AS s
            LEFT JOIN `ptc_order_confirmation` AS c ON s.`confirmation`=c.`id`
        WHERE s.`id`=:id";
$data = $db -> prepare($sql) -> execute(array(':id'=>$id));
if (!$data) exit('0');

$data = $data[0];
$history = '执行发送队列';

// 发送短信
if ($data['tel'])
{
    $tel = $data['tel'];
    $sms_rs = sms::confirmation($data['org'], $tel, $data['smsbody']);
    if (!$sms_rs) $history .= '【短信发送失败】';
}

// 发送邮件
if ($data['mail'])
{
    $org = $db -> prepare("SELECT `mailserver` FROM `ptc_org` WHERE `id`=:org") -> execute(array(':org'=>$data['org']));
    if ($org && $org[0]['mailserver'])
    {
        $server = json_decode($org[0]['mailserver'], true);

        $mail = new mail($server['mail'], $server['server'], $server['port'], $server['usr'], $server['pwd']);
        $subject = charset_convert("行程确认单－{$data['product']}", 'utf-8', 'gb2312');

        $mail -> isHTML();
        $mail -> addAttachment(PT_PATH . 'files/confirm_pdf/' . $id . '.pdf', '行程确认单.pdf');
        $mail_rs = $mail -> send($data['mail'], $subject, charset_convert($data['mailbody'], 'utf-8', 'gb2312'));
        if (!$mail_rs) $history .= '【邮件发送失败】';
    }
    else
    {
        $history .= '【邮件服务未设置】';
    }
}

$rs = $db -> prepare("UPDATE `ptc_order_confirmation_send` SET `status`=1 WHERE `id`=:id") -> execute(array(':id'=>$id));
echo (int)$rs;

$_SESSION['uid'] = 0;
$_SESSION['name'] = '系统自动';
history($data['confirmation'], 'confirmation', $history, array('sendid'=>$id));

