<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; HMC房型列表</title>

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

                <h1 class="page-header">HMC房型列表 <small><?php echo $hotel['name']; ?></small></h1>

                <div class="row">
                    <div class="col-xs-12 form-inline">
                        <a class="btn btn-default" style="margin-bottom:20px;" href="javascript:history.go(-1);" role="button">« 返回酒店</a>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>房型名称</th>
                                <th>床型</th>
                                <th>WIFI</th>
                                <th>宽带</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>房型名称</th>
                                <th>床型</th>
                                <th>WIFI</th>
                                <th>宽带</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['room']; ?>" /></td>
                                <td><abbr title="<?php echo $v['room']; ?>"><?php echo $v['roomname']; ?></abbr></td>
                                <td><abbr title="<?php echo $v['bed']; ?>"><?php echo $v['bedname']; ?></abbr></td>
                                <td><?php echo $v['wifi']; ?></td>
                                <td><?php echo $v['net']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-xs-12 form-inline">
                        <a class="btn btn-default" style="margin-bottom:20px;" href="javascript:history.go(-1);" role="button">« 返回酒店</a>
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
