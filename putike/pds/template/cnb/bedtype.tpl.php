<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 港捷旅床型列表</title>

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

                <h1 class="page-header">港捷旅床型列表</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <input type="hidden" name="method" value="nation" />

                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:refresh()">更新接口</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="text" name="keyword" class="form-control" value="" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                        <!-- end search -->
                    </form>

                    <div class="col-xs-8 col-sm-6 col-md-6 col-lg-4 text-right">
                        <!-- page -->
                        <ul class="pagination">
                            <?php include(dirname(__FILE__).'/../page.tpl.php');  ?>
                        </ul>
                        <!-- end page -->
                    </div>
                </div>
                <!--  page and operation  -->


                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>Code</th>
                                <th>床型描述</th>
                                <th width="100">关联</th>
                                <th width="100">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>Code</th>
                                <th>床型描述</th>
                                <th>关联</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['code']; ?>" /></td>
                                <td><?php echo $v['code']; ?></td>
                                <td><?php echo $v['name']; ?></td>
                                <td>
                                    <?php
                                    switch ($v['type'])
                                    {
                                        case 'T': echo '双床'; break;
                                        case 'D': echo '大床'; break;
                                        case '2': echo '大/双床'; break;
                                        case 'S': echo '单人床'; break;
                                        case 'K': echo '超大床'; break;
                                        case 'C': echo '圆床'; break;
                                        case 'O': echo '其他床型'; break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-default" href="javascript:bind()"><span class="glyphicon glyphicon-link hidden-md"></span><span class="hidden-xs hidden-sm"> 关联</span></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>


                <!-- page and operation -->
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:refresh()">更新接口</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->
                    </div>

                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-9 text-right">
                        <!-- page -->
                        <ul class="pagination">
                            <?php include(dirname(__FILE__).'/../page.tpl.php');  ?>
                        </ul>
                        <!-- end page -->
                    </div>
                </div>
                <!--  page and operation  -->


            </div>
        </div>
        <!-- end main -->

    </div>

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
    function refresh(){
        var di = $("<div class=\"alert alert-warning\" role=\"alert\" style=\"display:none\"></div>");
        di.html("<strong>提示</strong> 系统正在更新数据，稍后会自动刷新页面");
        $("h1.page-header").after(di);
        di.slideDown(300);
        $.post("<?php echo BASE_URL; ?>cnb.php?method=bedtype", {refresh:1}, function(data){
            if (data.s == 0){
                alert("更新成功，马上跳转", "success");
                di.slideUp(300, function(){ location.reload(); });
            }else{
                alert("更新失败，请重试", "error");
            }
        }, "json");
    }
    </script>
</body>
</html>