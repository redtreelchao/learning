<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; HMC服务包列表</title>

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

                <h1 class="page-header">HMC服务包列表</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <input type="hidden" name="method" value="nation" />

                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo BASE_URL; ?>hmc.php?method=package&status=1">显示已关联</a></li>
                                <li><a href="<?php echo BASE_URL; ?>hmc.php?method=package&status=0">显示未关联</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="text" name="keyword" class="form-control" value="" />
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
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>服务包信息</th>
                                <th width="100">关联</th>
                                <th width="15%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>服务包信息</th>
                                <th>关联</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['code']; ?>">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['code']; ?>" /></td>
                                <td><?php echo $v['name']; if($v['new']){ ?> <span class="label label-danger label-xs">NEW</span><?php } ?></td>
                                <td>
                                    <?php if($v['bind']) { ?>
                                    <span class="label label-primary" data-toggle="tooltip" data-placement="top" title="<?php echo $v['bindname']; ?>">已关联</span>
                                    <?php }else{ ?>
                                    <span class="label label-default">未关联</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-default" href="javascript:bind('<?php echo $v['code']; ?>')"><span class="glyphicon glyphicon-link hidden-md"></span><span class="hidden-xs hidden-sm"> 关联</span></a>
                                    <a class="btn btn-sm btn-default" href="javascript:;" onclick="unbind('<?php echo $v['code']; ?>', this);"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm"> 不关联</span></a>
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
                                <li><a href="<?php echo BASE_URL; ?>hmc.php?method=package&status=1">显示已关联</a></li>
                                <li><a href="<?php echo BASE_URL; ?>hmc.php?method=package&status=0">显示未关联</a></li>
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




    <div class="modal fade" id="bind-search" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">搜索增值包</h4>
                </div>
                <div class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">关键字</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control" placeholder="套票/温泉/餐饮" autocomplete="off" value="" />
                                <span class="input-group-btn">
                                    <button class="btn btn-default search" type="button"><span class="glyphicon glyphicon-search"></span></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bind-search-ul" style="line-height:40px;"></div>
                    <div class="bind-search-page" style="text-align:right; margin-top:10px"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" data-loading-text="保存中..">确定</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
    function bind(code){
        $("#bind-search").modal('show');
        var li = $("#row-"+code);
        var name = li.children("td:eq(1)").clone();
        name.children(".label").remove();
        name = $.trim(name.text());
        $("#bind-search .bind-search-ul").html("<div style='text-align:center; color:#999;'><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> 正在加载自动匹配内容..</div>");
        $("#bind-search .form-group input:text").val(name);
        search(1);

        $("#bind-search .modal-footer .btn-primary").unbind("click").bind("click", function(){
            var sel = $("#bind-search .bind-search-ul .btn-primary");
            if (sel.length == 0){
                alert("请选择一个匹配内容", "error", null, "#bind-search .modal-body");
                return false;
            }
            var bind = sel.eq(0).data("code");
            var name = sel.eq(0).text();
            //console.log(code);
            $.post("<?php echo BASE_URL; ?>hmc.php?method=package", {code:code,bind:bind}, function(data){
                if (data.s == 0) {
                    var label = $('<span class="label label-primary" data-toggle="tooltip" data-placement="top" title="">已关联</span>');
                    label.attr("title", name);
                    $("#row-"+code+" td:eq(2)").html(label);
                    $("#row-"+code+" td:eq(1)").find(".label").remove();
                    label.tooltip({trigger:'hover'});
                    $("#bind-search").modal('hide');
                } else {
                    alert(data.err, "error", null, "#bind-search .modal-body");
                }
            },"json");
        });
    }

    function unbind(code, btn){
        $(btn).children(".glyphicon").removeClass("glyphicon-ok").addClass("glyphicon-refresh glyphicon-loading");
        $.post("<?php echo BASE_URL; ?>hmc.php?method=package", {code:code,bind:0}, function(data){
            $(btn).children(".glyphicon").removeClass("glyphicon-refresh").addClass("glyphicon-ok");
            if (data.s == 0) {
                $("#row-"+code+" td:eq(1)").find(".label").remove();
            } else {
                alert(data.err, "error");
            }
        },"json");
    }

    function search(page){
        var btn = $("#bind-search .search");
        if(page === undefined) page = 1;
        var keyword = btn.parent().prev("input").val();

        if (!keyword) {
            alert("请输入查询关键词", "error", null, "#bind-search .modal-body"); return false;
        }
        btn.html('<span class="glyphicon glyphicon-refresh glyphicon-loading"></span>').prop("disabled", true);
        $.get("<?php echo BASE_URL; ?>package.php", {keyword:keyword, page:page}, function(data){
            if (data.s == 0){
                var ul = $("#bind-search .bind-search-ul");
                var page = $("#bind-search .bind-search-page");
                ul.html(""); page.html("");
                btn.html('<span class="glyphicon glyphicon-search"></span>').prop("disabled", false);
                if(data.rs.list.length > 0){
                    for(x in data.rs.list){
                        var d = data.rs.list[x];
                        var li = $("<button class=\"btn btn-sm btn-default\"></button>");
                        li.text(d.name).data("code", d.id);
                        ul.append(li).append(" ");
                    }
                }else{
                    ul.append("<div style='text-align:center; color:#999;'>未找到任何数据，请尝试其他关键词或<a href=\"<?php echo BASE_URL; ?>package.php#new\" target=\"_blank\">添加</a></div></div>");
                }
                if(data.rs.page.total > 1) {
                    page.html('<ul class="pager"><li><a href="javascript:search('+(data.rs.page.now - 1)+');">上一页</a></li> <li><a href="javascript:search('+(data.rs.page.now + 1)+');">下一页</a></li></ul>');
                    if (data.rs.page.now <= 1) {
                        page.find("ul li:eq(0) a").attr("href", "javascript:;").parent().addClass("disabled");
                    }
                    if (data.rs.page.now >= data.rs.page.total) {
                        page.find("ul li:eq(1) a").attr("href", "javascript:;").parent().addClass("disabled");
                    }
                }
                ul.children("button").click(function(){
                    var btn = $(this);
                    if (btn.is(".btn-primary")){
                        btn.removeClass("btn-primary").addClass("btn-default");
                    } else {
                        ul.find(".btn-primary").removeClass("btn-primary").addClass("btn-default");
                        btn.addClass("btn-primary").removeClass("btn-default");
                    }
                });
            } else {
                alert(data.err, "error", null, "#bind-search .modal-body");
            }
        }, "json");
    }

    $(function(){

        $(".table .label-primary").tooltip({trigger:'hover'});

        $("#bind-search input:text").keyup(function(e){
            if (e.which == 13){
                search(1);
            }
        });

        $("#bind-search .search").click(function(){
            search(1);
        });
    });
    </script>
</body>
</html>
