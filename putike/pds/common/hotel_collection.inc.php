<?php
if (!defined("PT_PATH")) exit;

if (!$_POST['url'])
    json_return(null, 1, '网页地址为空或不存在');

set_time_limit(0);

$url = trim($_POST['url']);
$parse = parse_url($url);
if (empty($parse['host']))
    json_return(null, 1, '地址不正确，无法解析');

switch ($parse['host'])
{
    case 'hotel.elong.com':
        $path = explode('/', $parse['path']);
        $code = $path[2];

        elg::domestic_hotel($code);
        break;

    case 'globalhotel.elong.com':
        $code = substr($parse['path'], 8, -5);

        elg::international_hotel($code);
        break;

    default:
        json_return(null, 1, '地址不符合解析规则');
}

if (!is_numeric($code))
    json_return(null, 1, '地址不符合解析规则');

// try to load new data
$hotel = $db -> prepare("SELECT `name`, `address`, `tel` FROM `sup_elg_hotel` WHERE `id`=:code") -> execute(array(':code'=>$code));
if ($hotel)
{
    $hotel[0]['elg'] = $code;
    json_return($hotel[0]);
}

// fail to collection data from url
else
{
    $func = str_replace('.', '_', $parse['host']);
    $data = collection::$func($url);
    if ($data)
    {
        $data['elg'] = $code;
        json_return($data);
    }
    else
    {
        json_return(collection::$_response, 1, '信息采集失败，请重试');
    }
}
