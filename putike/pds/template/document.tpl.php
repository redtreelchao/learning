<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 接口文档</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />

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

<div class="container" style="max-width:1000px">

    <h1>接口文档</h1>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?php if($type == 'business') echo 'active' ?>"><a href="?type=bus" role="tab">商户端</a></li>
        <li role="presentation" class="<?php if($type == 'personal') echo 'active' ?>"><a href="?type=per" role="tab">个人端</a></li>
    </ul>

    <h2>请求方式</h2>

    <ul>
        <li>接口地址：http://<?php echo $_SERVER['HTTP_HOST']; ?>/app.php</li>
        <li>请求方式：GET / POST</li>
        <li>全局参数：
            <?php if ($type == 'business') { ?>
            <ul>
                <li>appid -- AppId</li>
                <li>secret -- AppSecret 加密后密钥</li>
                <li>method -- 方法名</li>
                <li>page -- 翻页（可选参数）</li>
                <li>format -- 输出格式：xml / json / jsonp（默认json）</li>
            </ul>
            <?php } else { ?>
            <ul>
                <li>token -- Token</li>
                <li>method -- 方法名</li>
                <li>page -- 翻页（可选参数）</li>
                <li>format -- 输出格式：xml / json / jsonp（默认json）</li>
            </ul>
            <?php } ?>
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

    <form action="./app.php" method="POST" target="dev" class="row">
        <div class="form-group col-xs-7">
            <label>method</label>
            <select name="method" class="form-control" onchange="if(this.value != '') location.href='./document.php?type=<?php echo $type == 'business' ? 'bus' : 'pre'; ?>&method='+this.value">
                <option value="">请选择..</option>
                <?php
                $nowlimit = null;
                foreach ($methods as $cla => $funs) { ?>
                <optgroup label="<?php echo $cla; ?>">
                    <?php
                    foreach ($funs as $fun => $arg) {
                        if (($type == 'business' && $limit[$cla][$fun]['api']) || ($type == 'personal' && $limit[$cla][$fun]['user'])) {
                    ?>
                    <option <?php if ($cla.'_'.$fun == $_GET['method']) { echo 'selected'; $nowlimit = $limit[$cla][$fun]; }; ?> value="<?php echo $cla.'_'.$fun; ?>"><?php echo $cla.'_'.$fun; ?>（<?php echo $doc[$cla][$fun]['name']; ?>）</option>
                    <?php
                        }
                    }
                    ?>
                </optgroup>
                <?php } ?>
            </select>
            <p class="help-block">请选择相关接口名。</p>
        </div>
        <?php if ($type == 'business') { ?>
        <div class="form-group col-xs-7">
            <label>appid</label>
            <input type="text" name="appid" class="form-control" value="" />
        </div>
        <div class="form-group col-xs-7">
            <label>secret</label>
            <input type="text" name="secret" class="form-control" value="" />
        </div>
        <?php } else if (empty($nowlimit['untoken'])){ ?>
        <div class="form-group col-xs-7">
            <label>token</label>
            <input type="text" name="token" class="form-control" value="" />
            <p class="help-block">TOKEN 由用户登录(user_login接口)登陆后得到。</p>
        </div>
        <?php } ?>
        <?php
        if ($args) {
            foreach ($args as $k => $default) {
        ?>
        <div class="form-group col-xs-7">
            <label><?php echo $k; ?></label>
            <input type="text" name="<?php echo $k; ?>" class="form-control" value="<?php echo $default; ?>" />
            <p class="help-block"><?php echo $help[$k]; if (!empty($array[$k])) echo ' <span class="glyphicon glyphicon-question-sign" data-content=""></span>'; ?></p>
        </div>
        <?php
            }
        }
        ?>
        <div class="form-group col-xs-7">
            <label>page</label>
            <input type="text" name="page" class="form-control" value="" />
            <p class="help-block">分页，无分页不需要</p>
        </div>
        <div class="form-group col-xs-7">
            <label>format</label>
            <input type="text" name="format" class="form-control" value="json" />
            <p class="help-block">输出格式类型 json / jsonp / xml</p>
        </div>
        <div class="form-group col-xs-7">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>

    <h3>错误代码：</h3>
    <ul>
        <?php foreach($error as $code => $v){ ?>
        <li><?php echo $code ?> -- <?php echo $v; ?></li>
        <?php } ?>
    </ul>

    <h3>结果：</h3>
    <iframe name="dev" id="dev" frameborder="0" border="0" style=" width:100%; height:300px;">

    </iframe>

    <h3>请求记录：</h3>
    <a href="./document.php?view=log">点击查看 &gt;&gt;</a>
</div>

</body>
</html>