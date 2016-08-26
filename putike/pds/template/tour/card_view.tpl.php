<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 定制卡详情</title>

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
    <script type="text/javascript" src=".<?php echo RESOURCES_URL; ?>js/plupload/plupload.full.min.js"></script>

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

                <h1 class="page-header">定制卡详情 <small><?php echo $data['code']; ?></small></h1>

                <div id="order-list" class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="150">客户姓名</th>
                                <th width="150">目的地</th>
                                <th>行程名称</th>
                                <th width="150">状态</th>
                                <th width="250">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $data['contact']; ?></td>
                                <td><?php echo $data['areaname']; ?></td>
                                <td><?php echo $order ? $order['title'] : $data['areaname'].$data['days'].'日定制游'; ?></td>
                                <td><?php
                                    $s = $status[$data['status']];
                                    switch ($data['status']) {
                                        case 1: // 待确认
                                        case 5: // 需要修改
                                            echo '<span class="label label-warning">'.$s.'</span>'; break;
                                        case 2: // 优先
                                        case 7: // 支付成功
                                            echo '<span class="label label-danger">'.$s.'</span>'; break;
                                        case 11: // 已退款
                                            echo '<span class="label label-default">'.$s.'</span>'; break;
                                        case 3: // 无效
                                        case 10: // 已过期
                                            echo '<span class="label label-default">'.$s.'</span>'; break;
                                        default:
                                            echo '<span class="label label-info">'.$s.'</span>'; break;
                                    }
                                ?>
                                </td>

                                <td class="md-nowrap">
                                    <?php if($is_designer==1):?>
                                    <?php if ( !$order ) { ?>
                                    <a href="javascript:create(<?php echo $data['id'] ?>);" class="btn btn-sm btn-primary">
                                        <span class="glyphicon glyphicon-pencil hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 制作行程</span>
                                    </a>
                                    <?php } else { ?>
                                    <a href="/tourorder.php?method=edit&id=<?php echo $order['id'] ?>" class="btn btn-sm btn-default">
                                        <span class="glyphicon glyphicon-eye-open hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 查看行程</span>
                                    </a>
                                    <?php } ?>
                                    <div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default dropdown-toggle" <?php if($data['status'] > 3) echo 'disabled'; ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="glyphicon glyphicon-tag hidden-md"></span>
                                            标记 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <?php if ($data['status'] == 2) { ?>
                                            <li><a href="javascript:tag(<?php echo $data['id']; ?>, 1)">取消优先</a></li>
                                            <?php } else { ?>
                                            <li><a href="javascript:tag(<?php echo $data['id']; ?>, 2)">优先</a></li>
                                            <?php } ?>
                                            <?php if ($data['status'] == 3) { ?>
                                            <li><a href="javascript:tag(<?php echo $data['id']; ?>, 1)">取消无效</a></li>
                                            <?php } else { ?>
                                            <li><a href="javascript:tag(<?php echo $data['id']; ?>, 3)">无效</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php endif;?>
                                </td>
                            <tr>
                        </tbody>
                    </table>

                    <!-- form -->
                    <div class="well well-md well-break" style="line-height:30px;">

                        <div class="row">

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">出发时间</label>
                                <div class="col-sm-4 col-md-8"><?php echo date('Y-m-d',$data['departure']); ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">返程时间</label>
                                <div class="col-sm-4 col-md-8"><?php echo date('Y-m-d',$data['return']); ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">旅行预算</label>
                                <div class="col-sm-4 col-md-8"><?php echo $data['budget']; ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">出发城市</label>
                                <div class="col-sm-4 col-md-8"><?php echo $data['from']; ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">必去城市</label>
                                <div class="col-sm-4 col-md-8"><?php echo $data['city'].($data['other']?','.$data['other']:''); ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">同行人数</label>
                                <div class="col-sm-4 col-md-8">成人<?php echo $data['adults']; ?>  儿童<?php echo $data['kids']; ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">其他特殊要求</label>
                                <div class="col-sm-4 col-md-8"><?php echo $data['request']; ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">酒店及航班</label>
                                <div class="col-sm-4 col-md-8"><?php echo $options[$data['hotel']]; ?> / <?php echo $options[$data['flight']]; ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">手机</label>
                                <div class="col-sm-4 col-md-8"><?php echo $data['tel']; ?></div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <label class="col-sm-3 control-label">邮箱</label>
                                <div class="col-sm-4 col-md-8"><?php echo $data['email']; ?></div>
                            </div>

                        </div>

                    </div>

                </div>


                <h3 class="page-header">消息通知</h3>

                <form class="row form-horizontal" id="msgform">

                    <input name="card" type="hidden" value="<?php echo $data['id']?>" />

                    <div class="form-group">
                        <label class="col-sm-2 control-label">消息内容</label>
                        <div class="col-sm-6">
                            <textarea name="message" class="form-control" rows="5" cols="80">您好，<?php echo $data['contact']?>，根据您的定制卡，我给您的行程建议如下：此行目的地为欧洲，共<?php echo $data['days']?>天，出行人数为<?php echo ($data['adults']+$data['kids']);?>人</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">短信通知</label>
                        <div class="col-sm-6">
                            <input name="tel" value="<?php echo $data['tel']; ?>" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-2">
                            <a href="<?php echo BASE_URL; ?>tourcard.php?method=message&card=<?php echo $data['id'];?>" class="btn btn-default">查看消息记录</a>
                            <button type="button" class="btn btn-info" onclick="send()">发送</button>
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
    function send(){
        var data = $('#msgform').serialize();
        $.post("<?php echo BASE_URL; ?>tourcard.php?method=message", data, function(data){
            if (data.s == 0) {
               alert('信息保存成功', 'success');
            } else {
               alert(data.err, 'error');
            }
        }, "json");
    };

    function tag(id, status) {
        $.post("<?php echo BASE_URL; ?>tourcard.php?method=tag", {id:id, status:status}, function(data){
            if (data.s == 0){
                alert('标记成功，页面即将刷新', 'success', function(){ location.reload(true); });
            } else {
                alert(data.err, 'error');
            }
        }, "json");
    }

    function create(id) {
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=make", {id:id}, function(data){
            if (data.s == 0){
                location.href = "<?php echo BASE_URL; ?>tourorder.php?method=edit&id=" + data.rs;
            } else {
                alert(data.err, 'error');
            }
        }, "json");
    }
    </script>



</body>
</html>
