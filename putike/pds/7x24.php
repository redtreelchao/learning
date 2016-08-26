<?php
/**
 * 要在nginx的pds虚拟主机上增加配置如下：
 * location / {
 *          if (!-e $request_filename) {
 *              rewrite ^/7x24(.*)\.html$ /7x24.php?type=$1 last;
 *              rewrite ^/(.*)$ /index.php last;
 *          }
 *      }
 * @date 2016/06/22
 */


$type = $_GET['type'];

$avail_types = array('callin', 'hangup');

// 将reqeust的key作为变量
extract($_REQUEST);


if( in_array( $type, $avail_types ) ){
	switch ( $type ){

		case 'hangup':

			$callNo = trim(strval($_GET['CallNo']));

			$calledNo = trim(strval($_GET['CalledNo']));

			$calledNos = ['4008870198'=>1,'4008216033'=>2,'4006121260'=>4];

			$channel = isset($calledNos[$calledNo]) ? $calledNos[$calledNo] : 0;

			$tempId = 8;

			header( 'Location:/sms.php?method=send&mobile='.$callNo.'&channel='.$channel.'&tempid='.$tempId);

		break;
		case 'callin':
			header( 'Location:/order.php?keyword='.$originCallNo);
		break;
	}

}

?>
