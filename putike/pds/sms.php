<?php
/**
 * 组合产品
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

if ( !in_array( $_SESSION['role'], array('1','4') ) ) {

    $_GET['method'] = 'error';

}

$db = db(config('db'));

$method = empty($_GET['method']) ? 'list' : strval($_GET['method']);

$items = [];

$list = 'list';

$orgs = [1=>'FEEKR',2=>'PUTIKE',3=>'TOURZJ',4=>'MEISU'];

switch ($method)
{
    case "list":

        $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM `ptc_sms_admin_log`") -> execute();

        $page = new page($count[0]['c'], 10);

        $limit = $page -> limit();

        $items = $db->prepare('SELECT * FROM `ptc_sms_admin_log` order by id desc limit '.$limit)->execute();

        template::assign('nav', 'sms');
        
        template::assign('list', $items);

        template::assign('page', $page -> show());

        template::assign('subnav', 'list');

        template::display('sms/log_list');

    break;

    case "send":

        $items = $db->prepare('SELECT `id`, `tmpl`, `type`,`org` FROM `ptc_sms_tmpl` order by id')->execute();

        $default = [];

        if ( isset($_GET['mobile']) && isset($_GET['tempid']) && isset($_GET['channel']) ){

            $default = ['mobile'=>trim(strval($_GET['mobile'])),'channel'=>intval( $_GET['channel'] ),'id'=>intval( $_GET['tempid'] )];

        }
        

        template::assign('defaultTpl', $default);

        template::assign('nav', 'sms');

        template::assign('list', $items);
        
        template::assign('orgs', $orgs);

        template::assign('subnav', 'send');

        template::display('sms/list');

    break;

    case "sendsms":
        
        $channel = intval($_POST['channel']);

        $content = trim(strval($_POST['content']));

        $mobile = trim(strval($_POST['mobile']));

        if ( !preg_match('/^(13[0-9]|14[57]|15[0-35-9]|18[0-9])\\d{8}$/',$mobile) ) {
                json_return(null, 1, '请输入正确的手机号码');
        }

        if ( $content === '' ) {

            json_return(null, 1, '请输入您需要发送的短信内容');
        }

        $tips = [1=>'【Feekr旅行】', 2=>'【璞缇客】', 3=>'【浙江旅游】',4=>'【美 宿】'];

        if ( !isset($orgs[$channel]) || !isset($tips[$channel]) )
        {
            json_return(null, 1, 'channel不正确');
        }

        $org_id = $channel === 2 ? 1 : ($channel ===1 ? 2 : $channel);

        $org = $db -> prepare("SELECT `smskey` FROM `ptc_org` WHERE `id`=:org") -> execute(array(':org'=>$org_id));

        if (!$org || $org[0]['smskey'] === '') {

            json_return(null, 1, '请先配置短信smskey');

        }

        $content = $tips[$channel].$content;

        $_content = urlencode($content);

        $query = "apikey={$org[0]['smskey']}&text={$_content}&mobile={$mobile}";

        $header = array('Content-type: application/x-www-form-urlencoded', 'Content-Length: '.strlen($query));

        $rsjson = curl_file_get_contents('http://yunpian.com/v1/sms/send.json', $query, $header, 10);

        $db -> prepare("INSERT INTO `ptc_sms_admin_log` (`uid`,`name`, `message`, `result`, `time`) VALUES (:uid, :name, :message, :result, :time);") -> execute(array(':uid'=>$_SESSION['uid'],':name'=>$_SESSION['name'],':message'=>$content, ':result'=>$rsjson, ':time'=>NOW));

        if ( !empty($rsjson) ) {

            $rsjson = json_decode($rsjson);

            if ( isset($rsjson->code) && $rsjson->code === 0 ) {

                json_return(null, 0, '短信发送成功');
            
            }

            if ( isset($rsjson->detail) && isset($rsjson->msg) )
            {
                json_return(null, 1, '短信发送失败：'.strval($rsjson->msg).','.strval($rsjson->detail) );
            } 
        }

        json_return(null, 1, '短信发送失败');

    break;

    default:
        echo '<h1>404</h1>';
}

