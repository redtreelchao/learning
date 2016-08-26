<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 酒店参考链接</title>

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

                <h1 class="page-header" id="nameheader">酒店参考链接 <small><?php echo $hotel['name'];?></small></h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">

                         <div class="btn-group" style="margin:20px 0px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:add();">添加</a></li>
                            </ul>
                        </div>

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
                                <th width="15%">来源</th>
                                <th>链接</th>
                                <th width="20%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>来源</th>
                                <th>链接</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php if(!$list){ ?>
                            <tr>
                                <td class="empty" colspan="4"><div>还没有添加任何链接</div></td>
                            </tr>

                            <?php } ?>
                            <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['id']; ?>">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td id="hname-<?php echo $v['id']; ?>"><?php echo $v['name']; ?></td>
                                <td id="link-<?php echo $v['id']; ?>"><a href="<?php echo $v['link']; ?>" target="_blank"><span class="glyphicon glyphicon-link"></span> <?php echo strlen($v['link']) > 70 ? substr($v['link'], 0, 70).'…' : $v['link']; ?></a></td>
                                <td>
                                    <a href="javascript:;" onclick="edit(<?php echo $v['id']; ?>, this);" data-name="<?php echo $v['name']; ?>" data-link="<?php echo $v['link']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                    <a href="javascript:;" onclick="del(<?php echo $v['id']; ?>);" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
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
                            </ul>
                        </div>
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
    <div id="form-link" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">新增增值属性</h4>
                </div>
                <form class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">来源</label>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">链接</label>
                        <div class="col-sm-8">
                            <input type="text" name="link" class="form-control" value="" />
                        </div>
                    </div>

                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="hotel" value="<?php echo $hotel['id']; ?>" />

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
            $("#form-link .modal-title").text("添加参考链接");
            var data = {id:0, name:"", link:""};
            $("#form-link input").each(function(){
                var name = $(this).attr("name");
                if(name == "hotel") return true;
                $(this).val(data[name]);
            });
            $("#form-link").modal("show");
        }

        function edit(id, a){
            $("#form-link .modal-title").text("修改参考链接");
            var data = {id:id, name:$(a).data("name"), link:$(a).data("link")};
            $("#form-link input").each(function(){
                var name = $(this).attr("name");
                if(name == "hotel") return true;
                $(this).val(data[name]);
            });
            $("#form-link").modal("show");
        }

        function del(id){
            $.post("<?php echo BASE_URL; ?>hotel.php?method=link", {del:id}, function(data){
                if(data.s == 0){
                    $('#row-'+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err);
                }
            }, "json");
        }

        $(function(){

            $("#form-link .modal-footer .btn-primary").bind("click", function(){
                var btn = $(this);
                btn.button("loading");
                $.post("<?php echo BASE_URL; ?>hotel.php?method=link", $("#form-link form").serialize(), function(data){
                    btn.button("reset");
                    if(data.s == 0){
                        $("#form-link").modal("hide");
                        alert("保存成功", "success");
                        location.reload();
                    }else{
                        alert(data.err, "", null, "#form-link .modal-body");
                    }
                }, "json");
            });
        });
    </script>
</body>
</html>
