<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 酒店配对列表</title>

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

                <h1 class="page-header">酒店信息关联 <small><?php echo $supplies[$supply]; ?></small></h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <input type="hidden" name="method" value="bind" />
                        <input type="hidden" name="sup" value="<?php echo $supply; ?>" />

                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo BASE_URL; ?>hotel.php">返回</a></li>
                                <?php foreach($supplies as $k => $v) { ?>
                                <li><a href="<?php echo BASE_URL; ?>hotel.php?method=bind&sup=<?php echo $k; ?>"><?php echo $v; ?></a></li>
                                <?php } ?>
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
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th width="30%">酒店</th>
                                <th><?php echo $supplies[$supply]; ?> 酒店</th>
                                <td width="1"></td>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>酒店</th>
                                <th><?php echo $supplies[$supply]; ?> 酒店</th>
                                <td></td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td>
                                    <abbr title="ID:<?php echo $v['id']; ?>"><?php echo $v['name']; ?></abbr><br />
                                    <span class="info">
                                        <?php echo $v['country'].' '.$v['province'].' '.$v['city'].' '.$v['address']; ?><br />
                                        <?php echo $v['tel']; ?>
                                    </span>
                                </td>
                                <td data-id="<?php echo $v['id']; ?>">
                                    <?php if($v['city'] && ($v['tel'] || $v['country'] != '中国') && $v['address']) { ?>
                                    <button class="btn btn-sm btn-default search"><span class="glyphicon glyphicon-search"></span></button>
                                    <?php
                                        foreach($v['binds'] as $s)
                                        {
                                            if ($v[$supply] == $s['id'])
                                            {
                                                echo '<button class="btn btn-sm btn-primary bind-'.$s['id'].'" data-toggle="popover" title="'.$s['name'].'" data-content="'.$s['address'].'<br />'.$s['tel'].'" data-code="'.$s['id'].'">'.$s['name'].' <span class="badge badge-sm">'.number_format($s['seq'], 1, '.', '').'</span></button> ';
                                            }
                                            else
                                            {
                                                echo '<button class="btn btn-sm btn-default bind-'.$s['id'].'" data-toggle="popover" title="'.$s['name'].'" data-content="'.$s['address'].'<br />'.$s['tel'].'" data-code="'.$s['id'].'">'.$s['name'].' <span class="badge badge-sm">'.number_format($s['seq'], 1, '.', '').'</span></button> ';
                                            }
                                        }
                                    } else {
                                        echo '<a class="btn btn-sm btn-danger" href="'.BASE_URL.'hotel.php?method=edit&id='.$v['id'].'">点击完善酒店信息：',
                                                !$v['city'] ? '城市 ' : '',
                                                !$v['tel'] ? '电话 ' : '',
                                                !$v['address'] ? '地址 ' : '',
                                                '</a> ';
                                    } ?>
                                </td>
                                <td></td>
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
                                <li><a href="<?php echo BASE_URL; ?>hotel.php">返回</a></li>
                                <?php foreach($supplies as $k => $v) { ?>
                                <li><a href="<?php echo BASE_URL; ?>hotel.php?method=bind&sup=<?php echo $k; ?>"><?php echo $v; ?></a></li>
                                <?php } ?>
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
                    <h4 class="modal-title">搜索酒店</h4>
                </div>
                <div class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">关键字</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control" placeholder="酒店名/地址/供应商ID" autocomplete="off" value="" />
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
    $(function(){
        $(".table .btn").popover({trigger:"hover", placement:"top", html:true});

        $(".table").on("click", ".btn", function(){
            var btn = $(this);
            var td = btn.parent();
            var id = td.data("id");
            if (btn.is(".btn-danger")) return;
            if (btn.is(".search")) {
                // search hotels
                $("#bind-search").modal('show');
                $("#bind-search .modal-footer .btn-primary").unbind("click").bind("click", function(){
                    var b = $(this);
                    var sel = $("#bind-search .bind-search-ul .btn-primary");
                    if (!sel.length){
                        alert("请选择一个酒店", "error", null, "#bind-search .modal-body");
                        return false;
                    }
                    b.button('loading');
                    var code = sel.data("code");
                    $.post("<?php echo BASE_URL; ?>hotel.php?method=bind", {id:id, supply:"<?php echo $supply; ?>", code:code}, function(data){
                        b.button('reset');
                        if(data.s == 0){
                            if(td.find(".bind-"+code).length){
                                td.find(".btn-primary").removeClass("btn-primary").addClass("btn-default");
                                td.find(".bind-"+code).addClass("btn-primary").removeClass("btn-default");
                            }else{
                                var clone = $("#bind-search .bind-search-ul .btn-primary").clone(true);
                                clone.append("<span class=\"badge badge-sm\">0.0</span>").addClass("bind-"+code);
                                btn.after(clone).after(" ");
                            }
                            $("#bind-search").modal('hide');
                            $("#bind-search .bind-search-ul, #bind-search .bind-search-page").html("");
                        }else{
                            alert(data.err, "error", null, "#bind-search .modal-body");
                        }
                    }, "json");
                });
            }else{
                // bind hotel
                var code = btn.data("code");
                btn.append("<span class=\"glyphicon glyphicon-refresh glyphicon-loading\" style=\"margin-left:5px;\"></span>");
                $.post("<?php echo BASE_URL; ?>hotel.php?method=bind", {id:id, supply:"<?php echo $supply; ?>", code:code}, function(data){
                    btn.children(".glyphicon-refresh").remove();
                    if(data.s == 0){
                        if (btn.is(".btn-primary")){
                            btn.removeClass("btn-primary").addClass("btn-default");
                        } else {
                            td.find(".btn-primary").removeClass("btn-primary").addClass("btn-default");
                            btn.addClass("btn-primary").removeClass("btn-default");
                        }
                    }else{
                        alert(data.err);
                    }
                }, "json");
            }
        });

        var search = function(page){
            if(page === undefined) page = 1;
            var btn = $("#bind-search .search");
            var keyword = btn.parent().prev("input").val();
            if (!keyword){
                alert("请输入查询关键词", "error", null, "#bind-search .modal-body"); return false;
            }
            btn.html('<span class="glyphicon glyphicon-refresh glyphicon-loading"></span>').prop("disabled", true);
            $.get("<?php echo BASE_URL.strtolower($supply); ?>.php", {method:"hotel", keyword:keyword, page:page}, function(data){
                if (data.s == 0){
                    var ul = $("#bind-search .bind-search-ul");
                    var page = $("#bind-search .bind-search-page");
                    ul.html(""); page.html("");
                    btn.html('<span class="glyphicon glyphicon-search"></span>').prop("disabled", false);
                    if(data.rs.list.length > 0){
                        for(x in data.rs.list){
                            var d = data.rs.list[x];
                            var li = $("<button class=\"btn btn-sm btn-default\" data-toggle=\"popover\"></button>");
                            li.text(d.name).popover({trigger:"hover", placement:"top", html:true, content:"<div style='line-height:1.42857143;'>"+d.address+"<br />"+d.tel+"</div>", title:d.name}).data("code", d.id);
                            ul.append(li).append(" ");
                        }
                    }else{
                        ul.append("<div style='text-align:center; color:#999;'>未找到任何数据，请尝试其他关键词</div>");
                    }
                    if(data.rs.page.total > 1) {
                        page.html('<ul class="pager"><li><a href="javascript:;">上一页</a></li> <li><a href="javascript:;">下一页</a></li></ul>');
                        page.find("a:eq(0)").click(function(){ search(data.rs.page.now - 1); });
                        page.find("a:eq(1)").click(function(){ search(data.rs.page.now + 1); });
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
