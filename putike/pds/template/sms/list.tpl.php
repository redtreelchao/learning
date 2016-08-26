<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 短信模板列表</title>

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

                <h1 class="page-header">发送短信</h1>

                <!-- page and operation -->
                <div class="row">
                    

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
                    <table id="nation-list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="30%">模板名称</th>
                                <th>模板内容</th>
                                <th width="150">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>模板名称</th>
                                <th>模板内容</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr sid="<?php echo $v['id']; ?>" org="<?php echo $v['org'];?>">
                                <td><span class="label label-default" style="background:#777777; font-size:12px;"><?php echo $orgs[$v['org']];?></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['type']; ?></td>
                                <td class="tmpl"><?php echo preg_replace('/【.*】/', '', $v['tmpl']);?></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-default sendsms"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 发送短信</span></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>


                <!-- page and operation -->
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
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


    <!-- modal -->
    <div id="form-nation" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">发送短信</h4>
                </div>
                <form class="modal-body form-horizontal" id="sendForm">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">channel</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <?php 
                                 foreach( $orgs as $key=>$val ) {
                                    echo '<label><input type="radio" name="channel" value="'.$key.'"> '.$val.'</label>&nbsp;&nbsp;';
                                 }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">手机号码</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="mobile" placeholder="请输入对方的手机号码">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">短信内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" name="content" placeholder="请输入您需要发送的短信内容"></textarea>
                        </div>
                    </div>

                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" data-loading-text="保存中..">保存</button>
                </div>
            </div>
        </div>
    </div>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script>
        
        $(function(){

            $("#nation-list .sendsms").bind("click",function(){

                $('#sendForm')[0].reset()

                $("#form-nation").modal("show");

                var channel = $(this).closest('tr').attr('org');

                $("#form-nation input[name=channel][value="+channel+"]").attr("checked",true);

                $("#form-nation textarea[name=content]").html($(this).closest('tr').find('td.tmpl').html())

            });

            $("#form-nation .modal-footer .btn-primary").bind("click", function(){
                var btn = $(this);
                var channel = $("#form-nation input[name=channel]:checked").val();
                var content = $("#form-nation textarea[name=content]").html();
                var mobile = $("#form-nation input[name=mobile]").val();
                btn.button("loading");
                $.post("<?php echo BASE_URL; ?>sms.php?method=sendsms", {channel:channel, content:content, mobile:mobile}, function(data){
                    $(btn).button("reset");
                    if(data.s == 0){
                        
                        alert(data.err, "success");

                        $("#form-nation").modal("hide");

                    }else{

                        alert(data.err, "", null, "#form-nation .modal-body");
                    }

                }, "json");
            });
        })

        <?php 

            if ( !empty($defaultTpl) ) {
        ?>
                $("#form-nation").modal("show");

                var elm = $("#nation-list tbody tr[sid="+<?php echo $defaultTpl['id'];?>+"]");

                var mobile = "<?php echo $defaultTpl['mobile'];?>";

                var channel = "<?php echo $defaultTpl['channel'];?>";

                if ( elm !== undefined ) {

                    $("#form-nation input[name=mobile]").val(mobile)

                    $("#form-nation input[name=channel][value="+channel+"]").attr("checked",true);

                    $("#form-nation textarea[name=content]").html($(elm).find('td.tmpl').html())
                }
                
        <?php
            }
        ?>

    </script>
</body>
</html>
