<?php

require_once './lib//Sns.php';

if ( isset($_GET['callback']) )
{
	$wxUser = new User();

    $wxUser->getOpenidByWeixinCallback();
}