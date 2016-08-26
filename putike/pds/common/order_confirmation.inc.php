<?php

$order = trim($_POST['order']);
$group = intval($_POST['group']);

$confirmation = $db -> prepare("SELECT `id`,`org`,`product`,`tel`,`email`,`data` FROM `ptc_order_confirmation` WHERE `order`=:order AND `group`=:group")
                    -> execute(array(':order'=>$order, ':group'=>$group));

if ($confirmation)
{
    // Not have post other data
    if (!isset($_POST['contact']) && !isset($_POST['tel']) && !isset($_POST['email']) && !isset($_POST['send']))
        json_return($confirmation[0]['id']);

    $confirmation = $confirmation[0];

    if (isset($_POST['send']))
    {
        // sms
        if (!$confirmation['tel'])
            json_return(null, 1, '手机号码不能为空');

        if (!$confirmation['email'])
            json_return(null, 1, '邮箱不能为空');

        $data = json_decode($confirmation['data'], true);

        // 生成通用模板
        template::assign('mode', 'view');
        template::assign('data', $data);
        $tmpl = template::fetch('order/confirmation_tmpl');

        $styles = array(
            '<h1>'          => '<h1 style="font-size:16px; text-align:center; margin:0px; padding:30px 0px 20px; font-weight:bold;">',
            '<h2>'          => '<h2 style="font-size:14px; color:#337ab7; margin:0px; padding:40px 0px 20px; font-weight:bold; position:relative; clear:both;">',
            '<hr />'          => '<hr style="border:0px; height:1px; background:#ccc;" />',
            'class="logo"'  => 'style="height:67px; margin-bottom:-67px; background:#fff;"',
            'class="row"'   => 'style="margin-right:-15px; margin-left:-15px; clear:both;"',
            'class="col-md-2"'  => 'style="position:relative; min-height:1px; padding-right:15px; padding-left:15px; box-sizing:border-box; width:16.66666667%; float:left;"',
            'class="col-md-4"'  => 'style="position:relative; min-height:1px; padding-right:15px; padding-left:15px; box-sizing:border-box; width:33.33333333%; float:left;"',
            'class="col-md-10"' => 'style="position:relative; min-height:1px; padding-right:15px; padding-left:15px; box-sizing:border-box; width:83.33333333%; float:left;"',
            'class="table"'     => 'style="width: 100%; max-width: 100%; margin-bottom: 20px; border-spacing: 0; border-collapse: collapse;"',
        );

        preg_match_all('/<thead>.*?<\/thead>/is', $tmpl, $m);
        foreach ($m[0] as $v)
            $tmpl = str_replace($v, str_replace('<td>', '<td style="background:#f2f2f2; font-size:12px; padding:8px;">', $v), $tmpl);

        preg_match_all('/<tbody>.*?<\/tbody>/is', $tmpl, $m);
        foreach ($m[0] as $v)
            $tmpl = str_replace($v, str_replace('<td ', '<td style="border-top:#f2f2f2 solid 1px; font-size:12px; padding:8px;" ', $v), $tmpl);

        $tmpl = '<div id="content" style="border:#f2f2f2 solid 2px; margin:20px; padding:30px; font-size:12px; line-height:25px; max-width:780px; margin:20px auto; font:\'Microsoft Yahei\'">'
.str_replace(array_keys($styles), array_values($styles), $tmpl)
.'</div>';

        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>'
.$tmpl
.'</body></html>';

        // 生成邮件正文
        $mail = "{$data['contact']}，<br />　　您好！<br />　　感谢预订{$data['from']}的度假产品，您购买的{$confirmation['product']}已经确认，行程确认单如下："
                .$tmpl
                .'为方便打印携带，您可请下载附件，谢谢！<br /><br />如需任何帮助，请联系客服<br />客服热线：4008870198<br /><br />'.strtoupper($data['from']);

        // 开始生成，占位id
        $db -> beginTrans();

        $sendid = $db -> prepare("INSERT INTO `ptc_order_confirmation_send` (`confirmation`, `mailbody`, `smsbody`, `org`, `mail`, `tel`) VALUES (:conid, :mailbody, :smsbody, :org, :mail, :tel);")
                      -> execute(array(':conid'=>$confirmation['id'], ':mailbody'=>$mail, ':smsbody'=>'', ':org'=>$confirmation['org'], ':mail'=>$confirmation['email'], ':tel'=>$confirmation['tel']));
        if (!$sendid)
        {
            $db -> rollback();
            json_return(null, 1, '提交发送队列失败，请重试');
        }

        // 生成短链接
        import(CLASS_PATH.'extend/string');
        $url = 'http://confirm.putike.cn/confirm_view.php?key='. string::rand_string(3) . dec2chr($sendid + 1000000) . string::rand_string(3) . '&id=' . $sendid;
        $shortlink = curl_file_get_contents('http://s.putike.cn/api.php?url='.urlencode($url));
        if (!$shortlink)
        {
            $db -> rollback();
            json_return(null, 1, '短链接获取失败，请重试');
        }

        $shortlink = json_decode($shortlink, true);
        if ($shortlink['code'])
        {
            $db -> rollback();
            json_return(null, 2, '短链接获取失败，请重试');
        }

        $surl = 'http://s.putike.cn/'.$shortlink['shortlink'];

        // 生成短信正文
        $org  =  $db -> prepare("SELECT `smsfix` FROM `ptc_org` WHERE `id`=:org") -> execute(array(':org'=>$confirmation['org']));
        $sms  =  "{$org[0]['smsfix']}{$data['contact']}，您好，您购买的{$confirmation['product']}已确认，行程确认书单"
                . ($confirmation['email'] ? "已发送到您{$confirmation['email']}邮箱，请注意查收，也" : "")
                . "可点击 {$surl} 在线查看。如有疑问请联系客服，谢谢！";

        $rs = $db -> prepare("UPDATE `ptc_order_confirmation_send` SET `smsbody`=:smsbody WHERE `id`=:id;") -> execute(array(':smsbody'=>$sms, ':id'=>$sendid));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 1, '提交发送队列失败，请重试');
        }

        file_put_contents(PT_PATH.'files/confirm/'.$sendid.'.html', $html);

        // History
        if (!history($confirmation['id'], 'confirmation', '提交了发送队列', array('sendid'=>$sendid)))
        {
            $db -> rollback();
            json_return(null, 2, '提交发送队列失败，请重试');
        }

        if (!$db -> commit())
        {
            $db -> rollback();
            json_return(null, 9, '提交发送队列失败，请重试');
        }

        json_return($rs);
    }

    // Save Confirmation
    $db -> beginTrans();

    if (isset($_POST['contact']))
    {
        foreach (array('rooms', 'hotel', 'flight') as $name)
        {
            if (empty($_POST[$name])) continue;
            foreach ($_POST[$name] as $k => $v)
            {
                if ($k == 0) continue;
                if (!array_filter($v)) unset($_POST[$name][$k]);
            }
        }

        $data = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        $rs = $db -> prepare("UPDATE `ptc_order_confirmation` SET `data`=:data WHERE `id`=:id") -> execute(array(':data'=>$data, 'id'=>$confirmation['id']));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 2, '保存确认单失败，请重试');
        }

        $message = '修改了确认单';
        $log = $_POST;
    }
    else if (isset($_POST['tel']))
    {
        $message = '修改了手机号';
        $tel = trim($_POST['tel']);

        $log = json_decode($confirmation['data'], true);
        $log['_tel'] = $tel;

        $data = json_encode($log, JSON_UNESCAPED_UNICODE);
        $rs = $db -> prepare("UPDATE `ptc_order_confirmation` SET `data`=:data, `tel`=:tel WHERE `id`=:id") -> execute(array(':data'=>$data, ':tel'=>$tel, 'id'=>$confirmation['id']));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 2, '保存确认单失败，请重试');
        }
    }
    else if (isset($_POST['email']))
    {
        $message = '修改了邮箱';
        $email = trim($_POST['email']);

        $log = json_decode($confirmation['data'], true);
        $log['_email'] = $email;

        $data = json_encode($log, JSON_UNESCAPED_UNICODE);
        $rs = $db -> prepare("UPDATE `ptc_order_confirmation` SET `data`=:data, `email`=:email WHERE `id`=:id") -> execute(array(':data'=>$data, ':email'=>$email, 'id'=>$confirmation['id']));
        if ($rs === false)
        {
            $db -> rollback();
            json_return(null, 2, '保存确认单失败，请重试');
        }
    }

    // History
    if (!history($confirmation['id'], 'confirmation', $message, $log))
    {
        $db -> rollback();
        json_return(null, 2, '保存确认单失败，请重试');
    }

    if (!$db -> commit())
    {
        $db -> rollback();
        json_return(null, 9, '保存确认单失败，请重试');
    }

    json_return($rs);
}
else
{
    // Create New Confirmation
    $data = array();

    $sql = "SELECT o.`id`, o.`order`, o.`contact`, o.`tel`, o.`tel` AS `_tel`, o.`email` AS `_email`, o.`from` AS `org`, b.`name` AS `from`
            FROM `ptc_order` AS o
                LEFT JOIN `ptc_org` AS b ON o.`from` = b.`id`
            WHERE o.`order`=:code;";
    $order = $db -> prepare($sql) -> execute(array(':code'=>$order));
    if (!$order)
        json_return(null, 1, '订单不存在，请重试');

    $order = $order[0];

    $sql = "SELECT `pid`, `people`, `room`, `checkin`, `checkout`, `require`, `confirmno` FROM `ptc_order_room` WHERE `orderid`=:oid AND `group`=:group";
    $rooms = $db -> prepare($sql) -> execute(array(':oid'=>$order['id'], ':group'=>$group));
    if (!$rooms)
        json_return(null, 1, '预订信息不存在，请联系管理员');

    $require = array();
    foreach ($rooms as $k => $v)
    {
        $rooms[$k]['people'] = pinyin::get(trim($v['people'], ',')) . "\n" . trim($v['people'], ',');
        $require[] = $v['require'];
    }

    $sql = "SELECT  p.`product`, p.`producttype`, p.`productname`, p.`itemname`, p.`hotelname`, p.`roomname`, h.`address` AS `hoteladdress`, p.`checkin`, p.`checkout`, p.`supply`,
                    h.`tel` AS `hoteltel`, i.`intro`, p.`confirmno`
            FROM `ptc_order_hotel` AS p
                LEFT JOIN `ptc_hotel` AS h ON p.`hotel` = h.`id`
                LEFT JOIN `ptc_product` AS i ON p.`supplyid` = i.`id`
            WHERE p.`orderid`=:oid AND p.`id`=:pid";
    $hotel = $db -> prepare($sql) -> execute(array(':oid'=>$order['id'], ':pid'=>$rooms[0]['pid']));
    $hotel = $hotel[0];

    $flight = array();
    switch ($hotel['producttype'])
    {
        case '4':
            $sql = "SELECT  dc.`name` AS `depart_city`, dc.`en` AS `depart_city_en`,
                            tc.`name` AS `arrive_city`, tc.`en` AS `arrive_city_en`,
                            f.`code` AS `flight_code`, o.`date`, f.`takeoff`, f.`landing`, o.`back`, o.`backday`,
                            '' AS `terminal`, 'OK' AS `status`
                    FROM `ptc_order_flight` AS o
                        LEFT JOIN `ptc_flight` AS f ON o.`flight`=f.`id`
                        LEFT JOIN `ptc_flight_airport` AS d ON f.`depart`=d.`code`
                        LEFT JOIN `ptc_district` AS dc ON d.`city`=dc.`id`
                        LEFT JOIN `ptc_flight_airport` AS t ON f.`arrive`=t.`code`
                        LEFT JOIN `ptc_district` AS tc ON t.`city`=tc.`id`
                    WHERE o.`orderid`=:oid";
            $_flight = $db -> prepare($sql) -> execute(array(':oid'=>$order['id']));
            $_flight[0]['date'] = date('Y/m/d', $_flight[0]['date']);

            $flight[] = $_flight[0];

            if ($_flight[0]['back'])
            {
                $sql = "SELECT  dc.`name` AS `depart_city`, dc.`en` AS `depart_city_en`,
                                tc.`name` AS `arrive_city`, tc.`en` AS `arrive_city_en`,
                                '' AS `flight_code`, o.`date`, '' AS `takeoff`, '' AS `landing`, o.`back`, o.`backday`,
                                '' AS `terminal`, 'OK' AS `status`
                        FROM `ptc_order_flight` AS o
                            LEFT JOIN `ptc_flight` AS f ON o.`flight`=f.`id`
                            LEFT JOIN `ptc_flight_airport` AS d ON f.`depart`=d.`code`
                            LEFT JOIN `ptc_district` AS dc ON d.`city`=dc.`id`
                            LEFT JOIN `ptc_flight_airport` AS t ON f.`arrive`=t.`code`
                            LEFT JOIN `ptc_district` AS tc ON t.`city`=tc.`id`
                        WHERE o.`orderid`=:oid";
                $_flight = $db -> prepare($sql) -> execute(array(':oid'=>$order['id']));
                $_flight[0]['date'] = date('Y/m/d', $_flight[0]['date'] + $_flight[0]['backday'] * 86400);

                $flight[] = $_flight[0];
            }
            break;

        default:
            if ($hotel['supply'] == 'TICKET')
            {
                $hotel['checkin']  = $rooms[0]['checkin'];
                $hotel['checkout'] = $rooms[0]['checkout'];
                $hotel['confirmno']= $rooms[0]['confirmno'];
            }
            break;
    }

    $data = array_merge($order, $hotel);
    unset($data['checkin'], $data['checkout']);
    $data['rooms'] = $rooms;
    $data['hotel'] = array(array('hotelname'=>$hotel['hotelname'], 'roomname'=>$hotel['roomname'], 'checkin'=>date('Y/m/d', $hotel['checkin']), 'checkout'=>date('Y/m/d', $hotel['checkout']), 'require'=>implode(';', $require), 'confirmno'=>$hotel['confirmno']));
    $data['flight'] = $flight;

    $db -> beginTrans();

    $save = array(
        'orderid'   => $data['id'],
        'order'     => $data['order'],
        'group'     => $group,
        'org'       => $data['org'],
        'product'   => $data['productname'],
        'data'      => json_encode($data, JSON_UNESCAPED_UNICODE),
        'time'      => NOW,
        'email'     => $data['_email'],
        'tel'       => $data['_tel'],
        'uid'       => (int)$_SESSION['uid'],
    );
    list($column, $sql, $value) = array_values(insert_array($save));
    $rs = $db -> prepare("INSERT INTO `ptc_order_confirmation` {$column} VALUES {$sql};") -> execute($value);
    if (!$rs)
    {
        $db -> rollback();
        json_return(null, 1, '创建确认单失败，请重试');
    }

    // History
    if (!history($rs, 'confirmation', '创建了确认单', $data))
    {
        $db -> rollback();
        json_return(null, 2, '创建确认单失败，请重试');
    }

    if (!$db -> commit())
    {
        $db -> rollback();
        json_return(null, 9, '创建确认单失败，请重试');
    }

    json_return($rs);
}
