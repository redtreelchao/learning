<?php
/**
 * hook
 * use for product's api/web
 +-----------------------------------------
 * @author nolan.chou
 * @category
 * @version $Id$
 */

$path = dirname(__FILE__);
include_once $path.'/hotel_ticket/hook.php';
include_once $path.'/hotel_prepay/hook.php';
include_once $path.'/hotel_flight_prepay/hook.php';
include_once $path.'/hotel_auto_prepay/hook.php';
include_once $path.'/goods_ticket/hook.php';
//include_once $path.'/view_ticket/hook.php';
