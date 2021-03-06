<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 城市区划列表</title>

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
    <?php include(dirname(__FILE__).'/../../header.tpl.php'); ?>
    <!-- end header -->


    <div class="container-fluid">
        <div class="row">

            <!-- sidebar -->
            <?php include(dirname(__FILE__).'/../../sidebar.tpl.php'); ?>
            <!-- end sidebar -->

            <!-- main -->
            <div class="col-sm-11 col-sm-offset-1 col-md-10 col-md-offset-2 main">

                <h1 class="page-header">城市区划 <?php echo ' <small>'.$city['name'].'</small>'; ?></h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:add();">添加新区划</a></li>
                                <li><a href="#">移至回收站</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="hidden" name="id" value="<?php echo $city['id']; ?>" />
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
                            <?php include(dirname(__FILE__).'/../../page.tpl.php');  ?>
                        </ul>
                        <!-- end page -->
                    </div>
                </div>
                <!--  page and operation  -->


                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>区域名</th>
                                <th>拼音</th>
                                <th>类型</th>
                                <th width="15%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>区域名</th>
                                <th>拼音</th>
                                <th>类型</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['id']; ?>">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td title="ID:<?php echo $v['id']; ?>"><?php echo $v['name']; ?></td>
                                <td><?php echo $v['pinyin']; ?></td>
                                <td><?php
                                    switch($v['type'])
                                    {
                                        case 'district': echo '行政区'; break;
                                        case 'business': echo '商务区'; break;
                                        case 'shopping': echo '购物休闲区'; break;
                                    }
                                ?></td>
                                <td class="md-nowrap">
                                    <a href="javascript:edit(<?php echo $v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
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
                            </ul>
                        </div>
                        <!-- end filter -->
                    </div>

                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-9 text-right">
                        <!-- page -->
                        <ul class="pagination">
                            <?php include(dirname(__FILE__).'/../../page.tpl.php');  ?>
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
    <div id="form-district" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">新增区域规划</h4>
                </div>
                <form class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">区域名:</label>
                        <div class="col-sm-7">
                            <input name="name" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">拼音:</label>
                        <div class="col-sm-7">
                            <input name="pinyin" class="form-control" placeholder="字音首字母大写" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">类型:</label>
                        <div class="col-sm-7">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active">
                                    <input type="radio" name="type" value="district" autocomplete="off" checked> 行政区
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="type" value="business" autocomplete="off"> 商务区
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="type" value="shopping" autocomplete="off"> 购物休闲区
                                </label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="district-id" value="" />
                    <input type="hidden" name="pid" value="<?php echo $city['id']; ?>" />

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
            $("#form-district .modal-title").text("新增区域规划");
            $("#form-district form").get(0).reset();
            $("#form-district").modal("show");
        }

        function edit(id){
            $("#form-district .modal-title").text("修改区域规划");
            $("#form-district").modal("show");
            $.get("<?php echo BASE_URL; ?>district_extend.php", {method:"load", id:id}, function(data){
                if (data.s == 0){
                    $("#form-district input").each(function(){
                        var input = $(this);
                        var name = input.attr("name");
                        if (name != "type") input.val(data.rs[name]);
                        else if (input.val() == data.rs["type"]) input.parent().click();
                    });
                } else {
                    alert(data.err, "", null, "#form-district .modal-body");
                }
            }, "json");
        }

        $("#form-district .modal-footer .btn-primary").bind("click", function(){
            var btn = $(this);
            btn.button("loading");
            $.post("<?php echo BASE_URL; ?>district_extend.php?method=save", $("#form-district form").serialize(), function(data){
                $(btn).button("reset");
                if(data.s == 0){
                    alert("保存成功", "success", function(){ location.reload(); });
                    $("#form-district").modal("hide");
                }else{
                    alert(data.err, "", null, "#form-district .modal-body");
                }
            }, "json");
        });
    </script>
</body>
</html>
