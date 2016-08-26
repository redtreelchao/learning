<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 角色权限设置</title>

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

<style type="text/css">
.panel .panel-heading { position:relative; }
.panel .ico { position:absolute; top:-6px; right:-5px; color:#BBB; font-size:28px; }
.method-label label { font-size:12px; font-weight:400; display:inline-block; margin-right:5px; }
.method-label input { position:relative; top:3px; }
</style>

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

                <h1 class="page-header">角色权限设置</h1>

                <!-- page and operation -->
                <form class="row" action="" method="GET" role="form">

                    <!-- role list -->
                    <div class="col-xs-3 col-md-2">
                        <div id="role-list" class="list-group">
                            <?php foreach($role as $v){ ?>
                            <a href="<?php echo BASE_URL; ?>user.php?method=role&id=<?php echo $v['id']; ?>" class="list-group-item<?php if($id == $v['id']){ echo ' active'; } ?>"><?php echo $v['name']; ?></a>
                            <?php } ?>
                            <a href="javascript:add()" class="list-group-item" style="color:#999; text-align:center;"> + 添加角色 </a>
                        </div>
                    </div>


                    <!-- method list -->
                    <div class="col-xs-9 col-md-10" id="methods">
                        <div class="row">

                            <!-- method box -->
                            <div class="col-md-6">
                                <?php foreach($method as $k => $m){ if($k >= count($method)/2) break; ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading"><span class="ico <?php echo $m['ico']; ?>"></span> <?php echo $m['name']; ?></div>
                                    <div class="panel-body method-label" id="method-<?php echo $m['id']; ?>">
                                        <?php foreach($m['sub'] as $s){ ?>
                                            <label><input type="checkbox" value="<?php echo $s['id']; ?>" /> <?php echo $s['name']; ?></label>
                                        <?php } ?>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <button type="button" class="btn btn-primary btn-sm" data-code="<?php echo $m['id'] ?>" data-method="<?php echo $m['method']; ?>">新建操作</button>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <div class="col-md-6">
                                <?php foreach($method as $k => $m){ if($k < count($method)/2) continue; ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading"><span class="ico <?php echo $m['ico']; ?>"></span> <?php echo $m['name']; ?></div>
                                    <div class="panel-body method-label" id="method-<?php echo $m['id']; ?>">
                                        <?php foreach($m['sub'] as $s){ ?>
                                            <label><input type="checkbox" value="<?php echo $s['id']; ?>" /> <?php echo $s['name']; ?></label>
                                        <?php } ?>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <button type="button" class="btn btn-primary btn-sm" data-code="<?php echo $m['id'] ?>" data-method="<?php echo $m['method']; ?>">新建操作</button>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>

                </form>
                <!--  page and operation  -->

            </div>
        </div>
        <!-- end main -->

    </div>


    <!-- modal -->
    <div id="form-role" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">增加角色</h4>
                </div>
                <div class="modal-body">

                    <form role="form">
                        <div class="form-group">
                            <label class="control-label">角色名</label>
                            <input type="text" id="role-name" name="name" class="form-control" value="" />
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" data-loading-text="保存中..">保存</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <div id="form-method" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">增加操作</h4>
                </div>
                <div class="modal-body">

                    <form role="form">
                        <div class="form-group">
                            <label class="control-label">操作名</label>
                            <input type="text" name="name" class="form-control" value="" />
                        </div>

                        <div class="form-group">
                            <label class="control-label">键值</label>
                            <input type="text" id="method-code" name="method" class="form-control" value="" />
                        </div>

                        <input type="hidden" id="method-pid" name="pid" value="" />
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" data-loading-text="保存中..">保存</button>
                </div>
            </div>
        </div>
    </div>



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
    function add(){
        $("#form-role").modal("show");
        $("#role-name").val("");
    }

    $(function(){

        // Create Role
        $("#form-role .modal-footer .btn-primary").bind("click", function(){
            var btn = $(this);
            var name = $("#role-name").val();
            btn.button("loading");
            $.post("<?php echo BASE_URL; ?>user.php?method=role", {name:name}, function(data){
                btn.button("reset");
                if(data.s == 0){
                    var a = $('<a href="<?php echo BASE_URL; ?>user.php?method=role&id='+data.rs+'" class="list-group-item"></a>');
                    a.text(name);
                    $("#role-list a:last-child").before(a);
                    $("#form-role").modal("hide");
                }else{
                    alert(data.err, "", null, "#form-role .modal-body");
                }
            }, "json");
        });

        // Open method form
        $("#methods .panel .btn").click(function(){
            var code = $(this).data("code"), method = $(this).data("method");
            $("#method-pid").val(code);
            $("#method-code").val(method+"/");
            $("#form-method").modal("show");
        });

        // Create Method
        $("#form-method .modal-footer .btn-primary").bind("click", function(){
            var btn = $(this), form = $("#form-method form").eq(0);
            btn.button("loading");
            $.post("<?php echo BASE_URL; ?>user.php?method=method", form.serialize(), function(data){
                btn.button("reset");
                if(data.s == 0){
                    var label = $('<label />');
                    label.append($("<input type=\"checkbox\" value=\"\" />").val(data.rs.id)).append(" "+data.rs.name);
                    $("#method-"+data.rs.pid).append(" ").append(label);
                    $("#form-method").modal("hide");
                } else {
                    alert(data.err, "", null, "#form-method .modal-body");
                }
            }, "json");
        });

    });
    </script>
</body>
</html>
