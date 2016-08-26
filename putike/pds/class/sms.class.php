<?php
// 短信发送
class sms
{
    static $apikey = '';

    static function send($order='', $tmpl='', $item=array(), $tel='')
    {
        $db = db(config('db'));

        $order = order::_order($order); //var_dump($order);
        if (!$order) return false;

        $org = $db -> prepare("SELECT `smskey`, `smsfix` FROM `ptc_org` WHERE `id`=:org") -> execute(array(':org'=>$order['from']));
        if (!$org || !$org[0]['smskey']) return false;

        $template = $db -> prepare("SELECT `tmpl` FROM `ptc_sms_tmpl` WHERE `org`=:org AND `type`=:tmpl") -> execute(array(':org'=>$order['from'], ':tmpl'=>$tmpl));
        if (!$template) return false;

        try
        {
            // 临时替换某模板
            if (!empty($item['hotel']) && $item['hotel'] == '3927' && $tmpl == 'hotel_ticket_booking_success')
            {
                $template[0]['tmpl'] = $org[0]['smsfix'].'{$order[\'contact\']}，您好，{$item[\'productname\']}，已预约日期为".date(\'Y-m-d\', $item[\'checkin\'])."，如有疑问请致电：'.substr($template[0]['tmpl'], -13);
            }

            eval('$message="'.$template[0]['tmpl'].'";');

            $message = str_replace(array('别墅'), array('别 墅'), $message);

            $tel = $tel ? $tel : $order['tel'];

            $_message = urlencode($message);
            $query = "apikey={$org[0]['smskey']}&text={$_message}&mobile={$tel}";

            $header = array('Content-type: application/x-www-form-urlencoded', 'Content-Length: '.strlen($query));

            $rsjson = curl_file_get_contents('http://yunpian.com/v1/sms/send.json', $query, $header, 10); //var_dump($rsjson);

            $db -> prepare("INSERT INTO `ptc_sms_log` (`message`, `result`, `time`) VALUES (:message, :result, :time);") -> execute(array(':message'=>$message, ':result'=>$rsjson, ':time'=>NOW));

            return $rsjson ? json_decode($rsjson, true) : false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }


    // Cursor Message
    static function confirmation($org, $tel, $body='')
    {
        $db = db(config('db'));

        try
        {
            $org = $db -> prepare("SELECT `smskey` FROM `ptc_org` WHERE `id`=:org") -> execute(array(':org'=>$org));
            if (!$org || !$org[0]['smskey']) return false;

            $_message = urlencode($body);
            $query = "apikey={$org[0]['smskey']}&text={$_message}&mobile={$tel}"; //echo $query;

            $header = array('Content-type: application/x-www-form-urlencoded', 'Content-Length: '.strlen($query));

            $rsjson = curl_file_get_contents('http://yunpian.com/v1/sms/send.json', $query, $header, 10); //var_dump($rsjson);

            $db -> prepare("INSERT INTO `ptc_sms_log` (`message`, `result`, `time`) VALUES (:message, :result, :time);") -> execute(array(':message'=>$body, ':result'=>$rsjson, ':time'=>NOW));

            return $rsjson ? json_decode($rsjson, true) : false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }


    // 定制游
    static function tour($org, $tel, $designer='', $designer_mobile='')
    {
        $db = db(config('db'));

        try
        {
            $org = $db -> prepare("SELECT `smskey` FROM `ptc_org` WHERE `id`=:org") -> execute(array(':org'=>$org));
            if (!$org || !$org[0]['smskey']) return false;

            $body = '【璞缇客】您好！您的行程设计师'.$designer.'（手机号：'.$designer_mobile.'）给您发来了一条新的消息，请进入［璞瑅客微信公众号］-［境外］-［轻奢定制］-［我的定制行程］中进行查看！';
            $_message = urlencode($body);
            $query = "apikey={$org[0]['smskey']}&text={$_message}&mobile={$tel}"; //echo $query;

            $header = array('Content-type: application/x-www-form-urlencoded', 'Content-Length: '.strlen($query));

            $rsjson = curl_file_get_contents('http://yunpian.com/v1/sms/send.json', $query, $header, 10); //var_dump($rsjson);

            $db -> prepare("INSERT INTO `ptc_sms_log` (`message`, `result`, `time`) VALUES (:message, :result, :time);") -> execute(array(':message'=>$body, ':result'=>$rsjson, ':time'=>NOW));

            return $rsjson ? json_decode($rsjson, true) : false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }




}
