<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 深圳捷旅API接口管理</title>

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
                    <h1>深圳捷旅假期</h1>
                    <p>2001年,深圳市捷旅国际旅行社有限公司成立于深圳，地处全国境内游客接待量最大的广东省，毗邻港澳。公司初期以广东省、香港、澳门酒店批发为核心，逐步扩展到全国乃至全球范围。经过十几年发展，深圳捷旅已成为中国地区最先进和最具规模的酒店B2B分销平台……</p>
                    <p><a class="btn btn-primary btn-lg" href="http://www.jl-tour.com" target="_blank" role="button">访问网站</a></p>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-lg-4">
                        <h2>酒店接口</h2>
                        <p>该接口将读取供应商所提供的酒店信息，并可对单个酒店房型进行更新。　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>jlt.php?method=hotel" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>国籍接口</h2>
                        <p>该接口将读取供应商所提供的入住国籍信息维护，该接口会自动更新，经注意维护。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>jlt.php?method=nation" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>服务包接口</h2>
                        <p>该接口将读取供应商所提供的组合服务信息(如带门票、机票等)维护，该接口会随价格更新自动更新，请注意检查。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>jlt.php?method=package" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>报价房态接口</h2>
                        <p>该接口将读取供应商所提供酒店的价格及房态的实时查询，并可对酒店进行手动更新。　　　　　　　　　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>jlt.php?method=price" role="button">访问 »</a></p>
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
