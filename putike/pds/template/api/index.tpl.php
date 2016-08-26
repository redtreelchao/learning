<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; API接口管理</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />

    <!--[if lt IE 9]><script src="<?php echo RESOURCES_URL; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo RESOURCES_URL; ?>js/ie10-viewport-bug-workaround.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo RESOURCES_URL; ?>js/html5shiv.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <!-- header -->
    <?php include(dirname(__FILE__).'/../header.tpl.php'); ?>
    <!-- end header -->


    <div class="container-fluid">
        <div class="row">

            <!-- sidebar -->
            <?php include(dirname(__FILE__).'/../sidebar.tpl.php'); ?>
            <!-- end sidebar -->

            <!-- main -->
            <div class="col-sm-11 col-sm-offset-1 col-md-10 col-md-offset-2 main">

                <div class="jumbotron">
                    <h1>API中心</h1>
                    <p>API中心包括供应、分销等各个渠道的api支持服务，产品部门请随时关注各个接口上的提示信息，并及时跟进。</p>
                </div>

                <div class="row text-center">
                    <div class="col-lg-4 col-sm-6">
                        <img class="img-circle" src="<?php echo RESOURCES_URL; ?>/imgs/api-hmc.jpg" style="width: 140px; height: 140px;">
                        <h2>供应商 HMC</h2>
                        <p>　　华闽旅游预订有限公司成立于一九八七年。公司总部位于香港，现有上海，北京以及广州三家分公司，我们训练有素的员工为客户提供最专业，最高效率的优质服务。华闽预订为市场上提供一站式全面旅游服务的批发供货商……</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>hmc.php" role="button">访问页面 &raquo;</a></p>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <img class="img-circle" src="<?php echo RESOURCES_URL; ?>/imgs/api-jlt.jpg" style="width: 140px; height: 140px;">
                        <h2>供应商 深圳捷旅假期</h2>
                        <p>　　2001年,深圳市捷旅国际旅行社有限公司成立于深圳，地处全国境内游客接待量最大的广东省，毗邻港澳。公司初期以广东省、香港、澳门酒店批发为核心，逐步扩展到全国乃至全球范围…… 　　　　　　　　　　　　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>jlt.php" role="button">访问页面 &raquo;</a></p>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <img class="img-circle" src="<?php echo RESOURCES_URL; ?>/imgs/api-cnb.jpg" style="width: 140px; height: 140px;">
                        <h2>供应商 龙腾捷旅</h2>
                        <p>　　龙腾捷旅订房集团（CN Hotel booking Centre）, 是目前中国颇具规模的、专业的酒店预订（B2B&B2C）集团，在中国各大主要城市均设有分公司及办事处，并拥有提供大中华酒店及自由行产品资源的在线预订平台（B2B）…… </p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>cnb.php" role="button">访问页面 &raquo;</a></p>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <img class="img-circle" src="<?php echo RESOURCES_URL; ?>/imgs/api-elg.jpg" style="width: 140px; height: 140px;">
                        <h2>供应商 艺龙</h2>
                        <p>　　艺龙旅行网 (NASDAQ: LONG)是中国领先的在线住宿服务提供商之一，致力于为消费者打造专注专业、物超所值、智能便捷的住宿预订平台。通过手机和平板客户端、手机艺龙网（m.eLong.com）和PC电脑网站（eLong.com）…… </p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>elg.php" role="button">访问页面 &raquo;</a></p>
                    </div>
                </div>

            </div>
        </div>
        <!-- end main -->

    </div>

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
    </script>
</body>
</html>
