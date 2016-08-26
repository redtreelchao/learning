<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 增值属性列表</title>

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

                <h1 class="page-header">增值属性</h1>

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
                    <table id="package-list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th width="30%">增值属性信息</th>
                                <th>关联</th>
                                <th width="150">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>增值属性信息</th>
                                <th>关联</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['id']; ?>">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td id="package-<?php echo $v['id']; ?>"><?php echo $v['name']; ?></td>
                                <td>
                                    <?php
                                    if (!$v['binds']) {
                                        echo '<span class="inline-block info">无关联</span>';
                                    } else {
                                        foreach ($v['binds'] as $bk => $b)
                                        {
                                            echo "<abbr class=\"inline-block f12\" title=\"{$b['code']}\">{$b['name']}</abbr>";
                                            if ($bk != count($v['binds']) -1)
                                                echo '<span class="info"> / </span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="javascript:edit(<?php echo $v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
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
    <div id="form-package" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">新增增值属性</h4>
                </div>
                <form class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">增值属性</label>
                        <div class="col-sm-8">
                            <input type="text" id="package-name" name="name" class="form-control" value="" />
                        </div>
                        <div class="col-sm-10 col-md-offset-2">
                            <p class="help-block">标准形式如：请勿填入提前、连住、床型、早餐等信息</p>
                        </div>
                    </div>

                    <input type="hidden" id="package-id" value="" />

                </form>
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
            $("#form-package").modal("show");
            $("#form-package .modal-title").text("新增增值属性");
            $("#package-name").val("");
            $("#package-id").val("");
        }

        function edit(id){
            var name = $("#package-"+id).text();
            $("#form-package .modal-title").text("修改增值属性");
            $("#package-name").val(name);
            $("#package-id").val(id);
            $("#form-package").modal("show");
        }

        function del(id){
            $.post("<?php echo BASE_URL; ?>package.php?method=del", {id:id}, function(data){
                if(data.s == 0){
                    $('#row-'+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err);
                }
            }, "json");
        }

        $(function(){

            if(location.hash == '#new') add();

            $("#form-package .modal-footer .btn-primary").bind("click", function(){
                var btn = $(this);
                var name = $("#package-name").val();
                var id = $("#package-id").val();
                btn.button("loading");
                $.post("<?php echo BASE_URL; ?>package.php?method=edit", {id:id, name:name}, function(data){
                    $(btn).button("reset");
                    if(data.s == 0){
                        if (!id){
                            id = data.rs;
                            <?php if($page['now'] == 1){ ?>
                            $("#package-list tbody").prepend("<tr><td><input type=\"checkbox\" class=\"checkbox\" value=\""+id+"\" /></td><td>"+name.val()+"</td><td><span class=\"inline-block info\">无关联</span></td><td></td></tr>");
                            <?php } ?>
                        }else{
                            $("#package-"+id).text(name);
                        }
                        alert("保存成功", "success");
                        $("#form-package").modal("hide");
                    }else{
                        alert(data.err, "", null, "#form-package .modal-body");
                    }
                }, "json");
            });
        });
    </script>
</body>
</html>
