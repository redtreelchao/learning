<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 报告</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/reports.css" rel="stylesheet" />

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

                <h1 class="page-header">报告</h1>

                <div class="row" id="reports">
                    <!-- block -->
                    <div class="col-xs-12 col-sm-6 col-md-4 block" data-plugin="product-change">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#product-change">
                                <h3 class="panel-title">产品增减</h3>
                            </div>
                            <div id="product-change" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="loading"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- block -->
                    <!-- block -->
                    <div class="col-xs-12 col-sm-6 col-md-4 block" data-plugin="order-area">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#order-area">
                                <h3 class="panel-title">目的地订单</h3>
                                <button class="maximize glyphicon glyphicon-search"></button>
                            </div>
                            <div id="order-area" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="loading"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- block -->
                    <!-- block -->
                    <div class="col-xs-12 col-sm-6 col-md-4 block" data-plugin="order-area-type">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#order-area">
                                <h3 class="panel-title">订单目的地分类</h3>
                                <button class="maximize glyphicon glyphicon-search"></button>
                            </div>
                            <div id="order-area-type" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="loading"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- block -->
                    <!-- block -->
                    <div class="col-xs-12 col-sm-6 col-md-4 block" data-plugin="product-count">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#product-count">
                                <h3 class="panel-title">当前产品数</h3>
                            </div>
                            <div id="product-count" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="loading"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- block -->
                    <!-- block -->
                    <div class="col-xs-12 col-sm-6 col-md-4 block" data-plugin="hotel-count">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#hotel-count">
                                <h3 class="panel-title">当前酒店数</h3>
                            </div>
                            <div id="hotel-count" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="loading"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- block -->
                </div>

            </div>
        </div>
        <!-- end main -->

    </div>


    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <script src="<?php echo RESOURCES_URL; ?>js/masonry.pkgd.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/roo.js"></script>
    <script>
    $(function(){
        $("#reports").masonry({
            itemSelector: '.block'
        });

        $("#reports .panel-collapse").on("shown.bs.collapse hidden.bs.collapse", function(){
            $("#reports").masonry();
        });

        $("#reports .panel-collapse").on("show.bs.collapse hide.bs.collapse", function(e){
            var block = $(this).parents(".block").eq(0);
            if (block.data("resize")) {
                block.data("resize", false);
                return false;
            }
        });

        $("#reports .panel-heading .maximize").click(function(){
            var btn = $(this),
                block = btn.parents(".block").eq(0);
            block.data("resize", true);
            if (block.is(".col-sm-6")) {
                block.removeClass("col-sm-6 col-md-4").addClass("col-sm-12 col-md-12");
            } else {
                block.removeClass("col-sm-12 col-md-12").addClass("col-sm-6 col-md-4");
            }
            $("#reports").data("masonry").layout();
            btn.focus();
        });

        roo.config = {base:"<?php echo RESOURCES_URL; ?>"};

        window.loaddata = function(plugin, data){
            if (plugin === undefined) {
                var plugins = [];
                $("#reports .block").each(function(){
                    plugins.push($(this).data("plugin"));
                });
                var postdata = {plugins:plugins};
            } else {
                if (data === undefined) data = {};
                var postdata = $.extend({plugins:[plugin]}, data);
            }
            $.get("<?php echo BASE_URL; ?>reports.php", postdata, function(data){

                if( data.s == 0 ){
                    var rec = [];
                    $.each( data.rs, function( name ){
                        rec.push( name );
                    });

                    roo.recursive( rec, function( plugin, recursive ){
                        var scripts = data.rs[ plugin ].script, template = data.rs[ plugin ].html;
                        if( scripts && scripts.length ){
                            roo.use( scripts, function(){
                                $("#"+plugin+" .panel-body").html(template);
                                recursive();
                            });
                        } else {
                            $("#"+plugin+" .panel-body").html(template);
                        }

                        $("#reports").masonry();

                    });
                }
                else{
                    alert("读取数据失败，请刷新重试", "error");
                }

            }, "json");
        };

        window.loaddata();

    });



    </script>
</body>
</html>
