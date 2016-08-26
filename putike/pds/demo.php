<?php
/**
 * 模板
 +-----------------------------------------
 * @author nolan.zhou
 * @category
 * @version $Id$
 */
/**   */
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


template::assign('nav', '');
template::assign('subnav', '');

if (isset($_GET['type']) && $_GET['type'] == 'form')
    template::display('_demo_form');
else if (isset($_GET['type']) && $_GET['type'] == 'my')
    template::display('_demo_my');
else
    template::display('_demo_list');

?>