<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 艺龙API接口管理</title>

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
                    <h1>艺龙</h1>
                    <p>艺龙旅行网 (NASDAQ: LONG)是中国领先的在线住宿服务提供商之一，致力于为消费者打造专注专业、物超所值、智能便捷的住宿预订平台。通过手机和平板客户端、手机艺龙网（m.eLong.com）和PC电脑网站（eLong.com）、24小时预订热线（4009-333-333），为消费者提供酒店、公寓、客栈、机票及火车票等预订服务。截至2014年6月，艺龙旅行网可提供全球200多个国家超过32.5万家酒店、公寓、客栈的预订服务。艺龙旅行网排名前两位的大股东是Expedia, Inc.（Nasdaq: EXPE）和腾讯公司（HKSE:0700）……</p>
                    <p><a class="btn btn-primary btn-lg" href="http://www.elong.com" target="_blank" role="button">访问网站</a></p>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-lg-4">
                        <h2>地区接口</h2>
                        <p>该接口将读取供应商所提供的国家、城市信息。　　　　　　　　　　　　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>elg.php?method=city" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>酒店接口</h2>
                        <p>该接口将读取供应商所提供的酒店信息，并可对单个酒店房型进行更新。　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>elg.php?method=hotel" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>国籍接口</h2>
                        <p>该接口将读取供应商所提供的入住国籍信息维护，该接口会自动更新，经注意维护。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>elg.php?method=nation" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>服务包接口</h2>
                        <p>该接口将读取供应商所提供的组合服务信息(如带门票、机票等)维护，该接口会随价格更新自动更新，请注意检查。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>elg.php?method=package" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>报价房态接口</h2>
                        <p>该接口将读取供应商所提供酒店的价格及房态的实时查询，并可对酒店进行手动更新。　　　　　　　　　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>elg.php?method=price" role="button">访问 »</a></p>
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
