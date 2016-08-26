<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 账户设置</title>

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
    <?php include(dirname(__FILE__).'/header.tpl.php'); ?>
    <!-- end header -->


    <div class="container-fluid">
        <div class="row">

            <!-- sidebar -->
            <?php include(dirname(__FILE__).'/sidebar.tpl.php'); ?>
            <!-- end sidebar -->

            <!-- main -->
            <div class="col-sm-11 col-sm-offset-1 col-md-10 col-md-offset-2 main">

                <h1 class="page-header">设置</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">新密码</label>
                            <div class="col-sm-4">
                                <input type="password" name="password" class="form-control" placeholder="" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">确认密码</label>
                            <div class="col-sm-4">
                                <input type="password" name="_password" class="form-control" placeholder="" value="" />
                            </div>
                        </div>

                        <hr />

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="button" class="btn btn-primary" onclick="save()">保存</button>
                            </div>
                        </div>

                    </div>


                </form>
                <!-- end form -->

            </div>
        </div>
        <!-- end main -->

    </div>


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <script>
    function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>setting.php", postdata, function(data){
            if(data.s == 0){
                alert("保存成功", "success");
            }else{
                alert(data.err, 'error');
            }
        }, "json");
    }

    $(function(){

        if(location.hash == "#success"){
            alert("信息保存成功!", "success");
            location.hash = "";
        }

        <?php if(isset($error)){ ?>
        alert("<?php echo $error; ?>", "error");
        <?php } ?>

    });
    </script>
</body>
</html>
