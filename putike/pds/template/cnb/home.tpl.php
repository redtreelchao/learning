<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 龙腾捷旅API接口管理</title>

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
                    <h1>龙腾捷旅</h1>
                    <p>龙腾捷旅订房集团（CN Hotel booking Centre）, 是目前中国颇具规模的、专业的酒店预订（B2B&B2C）集团，在中国各大主要城市均设有分公司及办事处，并拥有提供大中华酒店及自由行产品资源的在线预订平台（B2B）。是目前国内营销网络布局最广及“预付”产品资源最丰富的同业批发商……</p>
                    <p><a class="btn btn-primary btn-lg" href="http://www.cnbooking.net" target="_blank" role="button">访问网站</a></p>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-lg-4">
                        <h2>地区接口</h2>
                        <p>该接口将读取供应商所支持的海内外国家及城市信息，并可根据国家及城市信息更新酒店相关数据。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>cnb.php?method=city" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>酒店接口</h2>
                        <p>该接口将读取供应商所提供的酒店信息，并可对单个酒店房型进行更新。　　　　　　　　　　　　</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>cnb.php?method=hotel" role="button">访问 »</a></p>
                    </div>
                    <!--div class="col-xs-6 col-lg-4">
                        <h2>服务包接口</h2>
                        <p>该接口将读取供应商所提供的组合服务信息(如带门票、机票等)维护，该接口会随价格更新自动更新，请注意检查。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>cnb.php?method=package" role="button">访问 »</a></p>
                    </div-->
                    <div class="col-xs-6 col-lg-4">
                        <h2>床型接口</h2>
                        <p>该接口将读取供应商所提供的酒店床型信息，该接口会随价格更新自动更新，请注意检查。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>cnb.php?method=bedtype" role="button">访问 »</a></p>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <h2>报价房态接口</h2>
                        <p>该接口将读取供应商所提供酒店的价格及房态的实时查询，并可对酒店进行手动更新。</p>
                        <p><a class="btn btn-default" href="<?php echo BASE_URL; ?>cnb.php?method=price" role="button">访问 »</a></p>
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