<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 开发文档</title>

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

                <h1 class="page-header">开发文档</h1>

                <div style="line-height:2;">

                    <h2>请求方式</h2>

                    <ul>
                        <li>接口地址：http://<?php echo $_SERVER['HTTP_HOST']; ?>/api.php</li>
                        <li>请求方式：GET / POST</li>
                        <li>全局参数：
                            <ul>
                                <li>method -- 方法名</li>
                                <li>page -- 翻页（可选参数）</li>
                                <li>format -- 输出格式：xml / json / jsonp（默认json）</li>
                                <li>token -- 授权码（注册登录可不填）</li>
                            </ul>
                        </li>
                    </ul>


                    <h2>返回数据结构</h2>

                    <code>{data:{}, code:0, message:""}</code><br />
                    <ul>
                        <li>data -- 返回的数据包</li>
                        <li>code -- 状态代码，0为正常，其他均为错误</li>
                        <li>message -- 错误信息</li>
                    </ul>

                    <h2>模拟请求</h2>

                    <div class="row">
                        <form action="./api.php" method="GET" target="dev" class="col-md-5">
                            <div class="form-group">
                                <label>method</label>
                                <select name="method" class="form-control" onchange="if(this.value != '') location.href='./document.php?method='+this.value">
                                    <option value="">请选择..</option>
                                    <?php foreach($methods as $cla => $funs){ ?>
                                    <optgroup label="<?php echo $cla; ?>">
                                        <?php foreach($funs as $fun => $arg){ ?>
                                        <option <?php if ($cla.'_'.$fun == $_GET['method']) echo 'selected'; ?> value="<?php echo $cla.'_'.$fun; ?>"><?php echo $cla.'_'.$fun; ?>（<?php echo $doc[$cla][$fun]['name']; ?>）</option>
                                        <?php } ?>
                                    </optgroup>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-grou">
                                <label>token</label>
                                <input type="text" name="token" class="form-control" value="" />
                                <p class="help-block">身份验证码，user_register / user_login 两个接口不需要。</p>
                            </div>
                            <?php
                            if($args){
                                foreach($args as $k => $default){
                            ?>
                            <div class="form-group">
                                <label><?php echo $k; ?></label>
                                <input type="text" name="<?php echo $k; ?>" class="form-control" value="<?php echo $default; ?>" />
                                <p class="help-block"><?php echo $help[$k]; ?></p>
                            </div>
                            <?php
                                }
                            }
                            ?>
                            <div class="form-group">
                                <label>page</label>
                                <input type="text" name="page" class="form-control" value="" />
                                <p class="help-block">分页，无分页不需要</p>
                            </div>
                            <div class="form-group">
                                <label>format</label>
                                <input type="text" name="format" class="form-control" value="json" />
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">提交</button>
                            </div>
                        </form>

                        <div class="col-md-7">
                            <h3>结果：</h3>
                            <iframe name="dev" id="dev" frameborder="0" border="0" style=" width:100%; height:300px;">

                            </iframe>

                            <h3>错误代码：</h3>
                            <ul>
                                <?php foreach($error as $code => $v){ ?>
                                <li><?php echo $code ?> -- <?php echo $v; ?></li>
                                <?php } ?>
                            </ul>
                        </div>

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
