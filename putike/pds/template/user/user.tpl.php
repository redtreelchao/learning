<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 操作员列表</title>

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

                <h1 class="page-header">操作员列表</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">

                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:add();">添加</a></li>
                                <li><a href="">移至回收站</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" />
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
                    <table id="user-list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th width="20%">用户名</th>
                                <th>姓名</th>
                                <th>手机号码</th>
                                <th>邮箱</th>
                                <th>角色</th>
                                <th width="150">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>用户名</th>
                                <th>姓名</th>
                                <th>手机号码</th>
                                <th>邮箱</th>
                                <th>角色</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['id']; ?>">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td><?php echo $v['username']; ?></td>
                                <td><?php echo $v['name']; ?></td>
                                <td><?php echo $v['tel']; ?></td>
                                <td><?php echo $v['email']; ?></td>
                                <td><?php echo $v['role']; ?></td>
                                <td>
                                    <a href="javascript:edit(<?php echo $v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 修改</span></a>
                                    <a href="javascript:del(<?php echo $v['id']; ?>);" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
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
                                <li><a href="javascript:add();">添加</a></li>
                                <li><a href="">移至回收站</a></li>
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


    <!-- modal -->
    <div id="form-user" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">修改资料</h4>
                </div>
                <form class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">用户名</label>
                        <div class="col-sm-8">
                            <input type="text" id="user-username" name="username" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">姓名</label>
                        <div class="col-sm-8">
                            <input type="text" id="user-name" name="name" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">密码</label>
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">确认密码</label>
                        <div class="col-sm-8">
                            <input type="password" name="_password" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">手机号码</label>
                        <div class="col-sm-8">
                            <input type="text" id="user-tel" name="tel" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">邮箱</label>
                        <div class="col-sm-8">
                            <input type="text" id="user-email" name="email" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">角色</label>
                        <div class="col-sm-8">
                            <select name="role" id="user-role" class="form-control">
                                <?php
                                    foreach($role as $r){
                                        echo '<option value="'.$r['id'].'">'.$r['name'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="user-id" name="id" value="" />

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
        function add(){
            $("#form-user .modal-title").text("添加用户");
            $("#form-user input:password").eq(0).removeAttr("placeholder");
            $("#form-user input").prop("disabled", false).val("");
            $("#form-user select").val("").trigger('chosen:updated');;
            $("#form-user").modal("show");
        }

        function edit(id){
            $("#form-user .modal-title").text("修改资料");
            $("#form-user input:password").eq(0).attr("placeholder", "不修改密码无需填写");
            $("#form-user").modal("show");
            $.get("<?php echo BASE_URL; ?>user.php", {id:id}, function(data){
                if(data.s == 0){
                    for(x in data.rs){
                        $("#user-"+x).val(data.rs[x]);
                    }
                    $("#user-role").trigger('chosen:updated');
                    $("#user-name, #user-username").prop("disabled", true);
                }else{
                    alert(data.err, "error", null, "#form-user .modal-body")
                }
            }, "json");
        }

        function del(id){
            $.get("<?php echo BASE_URL; ?>user.php", {del:id}, function(data){
                if(data.s == 0){
                    $('#row-'+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err);
                }
            }, "json");
        }

        $(function(){
            $("#user-role").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择..", width:"100%"});

            $("#form-user .modal-footer .btn-primary").click(function(){
                var btn = $(this);
                var data = $("#form-user form").serialize();
                btn.button("loading");
                $.post("<?php echo BASE_URL; ?>user.php", data, function(data){
                    if (data.s == 0){
                        $("#form-user").modal("hide");
                        alert("保存成功！", "success");
                        <?php if($page['now'] == $page['total']){ ?>location.reload(); <?php } ?>
                    }else{
                        alert(data.err, "error", null, "#form-user .modal-body");
                    }
                    btn.button('reset');
                }, "json");
            });
        });
    </script>
</body>
</html>
