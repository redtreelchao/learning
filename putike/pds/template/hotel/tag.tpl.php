<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 扩展酒店信息</title>

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

                <h1 class="page-header">扩展酒店信息 <small><?php echo $hotel['name']; ?></small></h1>

                <!-- form -->
                <div class="form-horizontal" id="form" style="position:relative; z-index:0">

                    <h3 class="col-sm-12">基本信息</h3>


                    <div class="col-sm-5 form-group">
                        <label class="col-sm-4 control-label">开业时间</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="opening" class="form-control input-sm ui-date" value="<?php echo $hotel['opening']; ?>" data-autosave />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-5 form-group">
                        <label class="col-sm-4 control-label">装修时间</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="redecorate" class="form-control input-sm ui-date" value="<?php echo $hotel['redecorate']; ?>" data-autosave />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-5 form-group">
                        <label class="col-sm-4 control-label">入住时间</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                <input type="text" name="checkin" class="form-control input-sm" max="5" value="<?php echo $hotel['checkin']; ?>" data-autosave />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-5 form-group">
                        <label class="col-sm-4 control-label">离店时间</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                <input type="text" name="checkout" class="form-control input-sm" max="5" value="<?php echo $hotel['checkout']; ?>" data-autosave />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-5 form-group">
                        <label class="col-sm-4 control-label">区域/地名</label>
                        <div class="col-sm-8">
                            <select name="" class="form-control ui-select" multiple="multiple" data-autosave>
                                <?php foreach($district as $v) { ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                <?php } ?>
                                <option value="">添加新区域</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-5 form-group">
                        <label class="col-sm-4 control-label">房间数</label>
                        <div class="col-sm-8">
                            <input type="text" name="roomnum" class="form-control input-sm" value="<?php echo $hotel['roomnum']; ?>" data-autosave />
                        </div>
                    </div>

                    <style>
                    .tagList { line-height:38px; }
                    </style>

                    <h3 class="col-sm-12">酒店设施</h3>

                    <div class="col-sm-11 col-sm-offset-1 tagList">
                        <?php foreach($amenity as $v) { ?>
                        <div class="btn-group">
                            <?php if(!$v['rel']){ ?>
                            <button type="button" class="btn btn-default btn-sm" data-tag="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></button>
                            <?php }else{ ?>
                            <button type="button" class="btn btn-primary btn-sm" id="tag-<?php echo $v['rel']; ?>" data-tag="<?php echo $v['id']; ?>" data-id="<?php echo $v['rel']; ?>"><?php echo $v['name']; ?></button>
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle"><span class="caret"></span><span class="sr-only">Introduce..</span></button>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <!--button type="button" class="btn btn-default btn-sm btn-virtual" data-type="amenity" data-tag="0">添加新标签</button-->
                    </div>



                    <h3 class="col-sm-12">房间设施</h3>

                    <div class="col-sm-11 col-sm-offset-1 tagList">
                        <?php foreach($facility as $v) { ?>
                        <div class="btn-group">
                            <?php if(!$v['rel']){ ?>
                            <button type="button" class="btn btn-default btn-sm" data-tag="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></button>
                            <?php }else{ ?>
                            <button type="button" class="btn btn-primary btn-sm" id="tag-<?php echo $v['rel']; ?>" data-tag="<?php echo $v['id']; ?>" data-id="<?php echo $v['rel']; ?>"><?php echo $v['name']; ?></button>
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle"><span class="caret"></span><span class="sr-only">Introduce..</span></button>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <!--button type="button" class="btn btn-default btn-sm btn-virtual" data-type="facility" data-tag="0">添加新标签</button-->
                    </div>



                    <h3 class="col-sm-12">服务</h3>

                    <div class="col-sm-11 col-sm-offset-1 tagList">
                        <?php foreach($service as $v) { ?>
                        <div class="btn-group">
                            <?php if(!$v['rel']){ ?>
                            <button type="button" class="btn btn-default btn-sm" data-tag="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></button>
                            <?php }else{ ?>
                            <button type="button" class="btn btn-primary btn-sm" id="tag-<?php echo $v['rel']; ?>" data-tag="<?php echo $v['id']; ?>" data-id="<?php echo $v['rel']; ?>"><?php echo $v['name']; ?></button>
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle"><span class="caret"></span><span class="sr-only">Introduce..</span></button>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <!--button type="button" class="btn btn-default btn-sm btn-virtual" data-type="service" data-tag="0">添加新标签</button-->
                    </div>


                    <h3 class="col-sm-12">周边</h3>

                    <?php
                    foreach(viewtypes() as $k => $type) {
                    ?>
                    <h4 class="col-sm-12">　 周边<?php echo $type; ?></h4>

                    <div class="col-sm-11 col-sm-offset-1 tagList">
                        <?php
                        foreach($view as $v) {
                            if ($v['type'] != $k) continue;
                        ?>
                        <div class="btn-group">
                            <?php if(!$v['rel']){ ?>
                            <button type="button" class="btn btn-default btn-sm" data-tag="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></button>
                            <?php }else{ ?>
                            <button type="button" class="btn btn-primary btn-sm" id="tag-<?php echo $v['rel']; ?>" data-tag="<?php echo $v['id']; ?>" data-id="<?php echo $v['rel']; ?>"><?php echo $v['name']; ?></button>
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle"><span class="caret"></span><span class="sr-only">Introduce..</span></button>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm btn-virtual" data-type="view" data-view="<?php echo $k; ?>">添加新标签</button>
                    </div>
                    <?php } ?>


                </form>
                <!-- end form -->

            </div>
        </div>
        <!-- end main -->

    </div>


    <!--modal Edit/Create Tag-->
    <div id="editTag" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">添加/编辑标签</h4>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal" role="form">

                        <ol class="breadcrumb"></ol>

                        <input type="hidden" name="id" value="0" />
                        <input type="hidden" name="pid" value="0" />
                        <input type="hidden" name="type" value="" />

                        <div class="form-group">
                            <label class="col-sm-3 control-label">名称</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="name" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">类型</label>
                            <div class="col-sm-6">
                                <select class="form-control ui-select" name="editor">
                                    <option value="">请选择..</option>
                                    <option value="tag">多级标签</option>
                                    <option value="text">文本内容</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">应用范围</label>
                            <div class="col-sm-6">
                                <label>
                                    <input type="checkbox" checked class="form-control" name="default" /> 全局可用
                                </label>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary submit" data-loading-text="保存中..">确认</button>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->



    <!--modal Tag Extend -->
    <div id="tagExtend" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">扩展关键词</h4>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal" role="form">

                        <ol class="breadcrumb"></ol>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">简述</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="value" value="" data-autosave />
                            </div>
                        </div>

                        <div class="form-group introduce">
                            <label class="col-sm-2 control-label">介绍</label>
                            <div class="col-sm-9">
                                <textarea class="form-control input-sm" rows="5" name="text" data-autosave></textarea>
                            </div>
                        </div>

                        <div class="form-group tags">
                            <label class="col-sm-2 control-label">关键词</label>
                            <div class="col-sm-9 tagList">
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    <!--modal-->




    <!--modal Search View Tag-->
    <div id="searchView" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">搜索周边</h4>
                </div>

                <div class="modal-body">
                    <div class="form-horizontal" role="form" style="padding-bottom:100px;">

                        <div id="hotel" class="form-group">
                            <label class="col-sm-2 control-label">关键词</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" value="" />
                                    <span class="input-group-btn">
                                        <button class="button btn btn-default" type="button" data-type="view"><span class="glyphicon glyphicon-search"></span></button>
                                    </span>
                                </div>
                                <ul class="dropdown-menu" role="menu" style="left:15px; right:15px; max-height:200px; overflow:auto;"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary submit">添加新周边</button>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->



    <!--modal Edit/Create View Tag-->
    <div id="editViewTag" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">添加/编辑周边</h4>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal" role="form">

                        <input type="hidden" name="type" id="viewType" value="" />

                        <div class="form-group">
                            <label class="col-sm-3 control-label">名称</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" id="name" class="form-control"  value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">拼音</label>
                            <div class="col-sm-6">
                                <input type="text" name="pinyin" class="form-control" placeholder="可不填写，系统将自动生成" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">国家</label>
                            <div class="col-sm-5">
                                <select id="country" name="country" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <?php foreach($country as $v) { ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $hotel['country']) echo 'selected="selected"' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">城市</label>
                            <div class="col-sm-5">
                                <select id="city" name="city" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <?php foreach($city as $v) { ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $hotel['city']) echo 'selected="selected"' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">联系电话</label>
                            <div class="col-sm-6">
                                <input type="text" name="tel" class="form-control" placeholder="(86)021-88888888" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">地址</label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <input type="text" id="address" name="address" class="form-control" placeholder="" value="" />
                                    <span class="input-group-btn">
                                        <button id="searchPos" class="btn btn-default" type="button"><span class="glyphicon glyphicon-map-marker"></span></button>
                                    </span>
                                </div>
                                <p class="help-block">地址请勿重复填写省市信息</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">位置坐标</label>
                            <div class="col-sm-6">
                                <input type="text" id="position" name="position" class="form-control" placeholder="" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label hidden-xs"> </label>
                            <div class="col-sm-7">
                                <div style="padding:10px; padding:10px; border:#ddd solid 1px; border-radius:10px;">
                                    <div id="map" style="height:200px"></div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary submit" data-loading-text="保存中..">确认</button>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>

    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=<?php echo config('web.baidumap'); ?>"></script>

    <script>
    $(function(){

        // Map some methods;
        var map = new BMap.Map("map");
        var point = new BMap.Point(121.479275, 31.239035);
        map.centerAndZoom(point, 11);
        map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}))
        var marker = new BMap.Marker(point);
        map.addOverlay(marker);

        map.addEventListener("click", function(e){
            var point = new BMap.Point(e.point.lng, e.point.lat);
            marker.setPosition(point);
            $("#position").val(e.point.lng+","+e.point.lat);
        });

        $("#country").change(function(){
            var pid = $(this).val();
            $.post("./index.php?method=city", {pid:pid}, function(data){
                if (data.s == 0) {
                    $("#city").html('<option>请选择..</option>');
                    for(x in data.rs){
                        var opt = $("<option />");
                        opt.text(data.rs[x].name).attr("value", data.rs[x].id);
                        $("#city").append(opt);
                    }
                    $("#city").trigger('chosen:updated');
                }else{
                    alert("城市读取异常，请重试", "error");
                }
            }, "json");
        });

        $("#searchPos").click(function(){
            var btn = $(this);
            var city = $("#city option:selected").text();
            if (!city) { alert("请选择一个城市", "error"); return false; }
            btn.prop("disabled", true).html('<span class="glyphicon glyphicon-refresh glyphicon-loading"></span>');
            var local = new BMap.LocalSearch(city, {onSearchComplete:function(result){
                btn.prop("disabled", false).html('<span class="glyphicon glyphicon-map-marker"></span>');
                var pos = result.getPoi(0);
                if (pos === undefined || pos.point === undefined){
                    alert("未搜索到相关地址", "warning", null, "#editViewTag .modal-body");
                }
                marker.setPosition(pos.point);
                $("#position").val(pos.point.lng+","+pos.point.lat);
                map.centerAndZoom(pos.point, 17);
            }});
            var keyword = $("#address").val();
            local.search(keyword);
        });

        var btnExt = "<button type=\"button\" class=\"btn btn-primary btn-sm dropdown-toggle\"><span class=\"caret\"></span><span class=\"sr-only\>Introduce..</span></button>";

        var createBtn = function(tag, name, rel, pid){
            var btn = $("<button type=\"button\" class=\"btn btn-default btn-sm\"></button>");
            btn.data("tag", tag).text(name);
            var btnGroup = $("<div class=\"btn-group\"></div>").append(btn);
            if (rel !== undefined && rel) {
                btn.data("id", rel).attr("id", "tag-"+rel).toggleClass("btn-default btn-primary");
                btnGroup.append(btnExt);
            }
            if (pid !== undefined) {
                btn.data("pid", pid);
            }
            return btnGroup;
        }

        // View Tag
        // Search
        $("#searchView button").click(function(){
            var btn    = $(this)
                , m    = $("#searchView")
                , type = btn.data("type")
                , inp  = btn.parent().prev("input")
                , ul   = inp.parent().next("ul")
                , keyword = inp.val();

            if (keyword == "") {
                alert("请输入有效的关键词查询", "warning", null, "#searchView .modal-body");
                return false;
            }

            ul.stop(true).html("<li style=\"text-align:center; color:#999;\"><a href=\"javascript:;\">正在搜索..</a></li>").show();

            inp.focus().one("blur", function() {
                ul.stop(true).delay(100).hide(1);
            });

            m.find(".submit").data("type", type);

            $.get("<?php echo BASE_URL; ?>view.php", {method:"list", keyword:keyword, type:type, page:1}, function(data){
                if (data.s == 0) {
                    ul.html("");
                    var list = data.rs.list, li;
                    for(x in list) {
                        li = $("<li />").append( $("<a href=\"javascript:;\" />").text(list[x].name+" ("+list[x].city+")").data({tag:list[x].tag, name:list[x].name}) );
                        ul.append(li);
                    }
                    if (data.rs.page.total > 1) {
                        ul.append("<li style=\"text-align:center; color:#999\">还有"+(data.rs.page.rows-15)+"条记录.</li>");
                    }

                    ul.find("a").bind("click", function(){
                        var target = m.data("target")
                            , t = $(this).data()
                            , newBtn = createBtn(t.tag, t.name);

                        ul.stop(true).hide();
                        $(target).before(newBtn).before(" ");
                        m.modal("hide");
                    });
                } else {
                    alert("检索失败，请重试", "warning", null, "#searchView .modal-body");
                }
            }, "json");

        });

        // Create new view tag
        $("#searchView .submit").click(function(){
            var m       = $("#editViewTag")
                , type  = $(this).data("type");

            $("#viewType").val(type);

            $("#searchView").modal("hide");
            m.modal("show");

            var btn = m.find(".submit");
            btn.unbind("click").bind("click", function(){
                var postdata = $("#editViewTag form").serialize();
                $.post("<?php echo BASE_URL; ?>view.php?method=edit", postdata, function(data){
                    if(data.s == 0){
                        m.modal("hide");
                        var newBtn = createBtn(data.rs.id, data.rs.name);
                        $(btn).before(newBtn).before(" ");
                    }else{
                        alert(data.err, 'error', null, "#editViewTag .modal-body");
                    }
                }, "json");
            });
        });


        // Save normal tag
        $("#editTag .submit").click(function(){
            var btn = $(this), form = $("#editTag form");
            btn.button("loading");
            $.post("<?php echo BASE_URL; ?>hotel.php?method=tag&action=save", form.serialize(), function(data){
                btn.button("reset");
                if(data.s == 0){
                    $("#editTag").modal("hide");
                    var target = $("#editTag").data("target")
                        , pid = target.data("pid")
                        , newBtn = createBtn(data.rs.id, data.rs.name, null, pid);

                    $(target).before(newBtn).before(" ");

                    // if query from a modal, callback the modal.
                    if ($(target).parents(".modal").length) {
                        $("#tagExtend").modal("show");
                    }
                }else{
                    alert(data.err, "error", null, "#editTag .modal-body");
                }
            }, "json");
        });

        // Create a new tag
        $("body").on("click", ".tagList .btn-virtual", function(){
            var m = $("#editTag")
                , breadcrumb = m.find(".breadcrumb")
                , btn = $(this)
                , type = btn.data("type")
                , tag = btn.data("tag");

            m.find("input,select").val("");
            m.find("select").trigger("chosen:updated");

            if (type == 'view') {
                m = $("#searchView");
                $("#searchView button").data("type", btn.data("view"));
            } else {
                m.find("input[name='type']").val(type);
                m.find(".modal-title").text("添加新标签");
            }

            // if add new tag from other tag extend, toggle modals.
            if ( btn.parents(".modal").length ) {
                $("#tagExtend").modal("hide");
                m.find("input[name='pid']").val(tag);
            }

            // breadcrumb
            if ( tag == 0 ) {
                breadcrumb.hide();
                var root = btn.parent().prev("h3,h4").text();
                breadcrumb.html($('<li />').text(root));
            } else {
                breadcrumb.html($("#tagExtend .breadcrumb").html());
            }

            m.data("target", btn).modal("show");
        });

        // Extend tag data or (un)bind to object
        $(".tagList").on("click", ".btn-group .btn", function(){
            var _t = $(this);
            // (Un)bind to hotel
            if (_t.index() == 0){
                var tag  = _t.data("tag")
                    , pid    = _t.data("pid") ? _t.data("pid") : 0
                    , status = _t.is(".btn-default") ? 1 : 0
                    , inModal = _t.parents(".modal").length ? 1: 0;
                $.post(
                    "<?php echo BASE_URL; ?>hotel.php?method=tag&action=set",
                    {tag:tag, hotel:<?php echo $hotel['id']; ?>, pid:pid, status:status},
                    function(data){
                        _t.blur();
                        if(data.s == 0){
                            if(_t.is(".btn-primary"))
                                _t.removeClass("btn-primary").addClass("btn-default").removeData("id").removeAttr("id").next(".btn").remove();
                            else
                                _t.removeClass("btn-default").addClass("btn-primary").data("id", data.rs.id).attr("id", "tag-"+data.rs.id).parent().append(btnExt);
                        }else{
                            alert(data.err, "error", null, inModal ? "#tagExtend .modal-body" : undefined);
                        }
                    },
                    "json"
                );

            // Extend tag
            }else{
                // close other popover
                $(".btn-group ._pop").not(this).popover("hide");
                if (_t.is("._pop")){
                    _t.popover("toggle");
                }else{
                    var container = _t.parents(".modal").length ? "#tagExtend .tagList" : "#form";
                    _t.addClass("_pop").popover({content:function(){
                        var id  = _t.prev(".btn").data("id")
                            , tag = _t.prev(".btn").data("tag")
                            , tmp = $("<div style=\"width:240px; text-align:left;\"><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> loading..</div>");

                        $.post("<?php echo BASE_URL; ?>hotel.php?method=tag&action=load", {id:id},
                            function(data){
                                if (data.s !== 0 || data.rs === false) {
                                    alert("读取失败，请重试", "error");
                                    _t.popover("hide").removeClass("_pop");
                                    return;
                                }

                                tmp.html("");
                                if (data.rs.value.length) {
                                    tmp.append($("<p style=\"font-size:12px;\" />").text(data.rs.value));
                                }
                                if (data.rs.tags) {
                                    var p = $("<p />");
                                    for(x in data.rs.tags) {
                                        var t = data.rs.tags[x];
                                        if (!t.rel) continue;
                                        p.append($("<span class=\"label label-info\" />").text(t.name)).append(" ");
                                    }
                                    tmp.append(p);
                                }
                                if (data.rs.text !== undefined) {
                                    tmp.append($("<p style=\"font-size:12px; color:#999;\" />").text(data.rs.text));
                                }

                                if (tmp.text() == "") tmp.html("<span style=\"color:#CCC\">未设置内容</span>");
                                var setbtn = $("<button type=\"button\" class=\"btn btn-link btn-xs tag-child-setting\">设置</button>");
                                setbtn.data({tag:tag, id:id, data:data.rs});
                                tmp.append(setbtn);
                            },
                            "json");
                        return tmp;
                    }, html:true, placement:'bottom', trigger:'manual', container:container}).popover("show");
                }
            }
        });

        // Popover tag setting
        $("body").on("click", ".popover .tag-child-setting, .modal .breadcrumb a", function(){
            var _t      = $(this)
                , id    = _t.data("id")
                , tag   = _t.data("tag")
                , data  = _t.data("data")
                , from = $("#tag-"+id)
                , modal = $("#tagExtend")
                , breadcrumb = modal.find(".breadcrumb")
                , modalTags  = modal.find(".tags")
                , modalIntro = modal.find(".introduce")
                , tagList    = modalTags.children(".tagList")
                , pInModal   = _t.parents(".modal").length ? 1 : 0;

            from.next(".btn").popover("hide");
            modal.modal("show");

            if (_t.is(".modal .breadcrumb a")) {
                if (_t.parents("#editTag").length) {
                    $("#editTag").modal("hide");
                    _t = breadcrumb.children(".b-"+id);
                }
                _t.parent().nextAll("li").remove();
            } else {
                if (pInModal) {
                    var t = from.text();
                    var li = $("<li />").append( $("<a href=\"javascript:;\" />").text(t).data({id:id, tag:tag, data:data}) ).attr("class", "b-"+id);
                    breadcrumb.append(li);
                } else {
                    var root = from.parents(".tagList").eq(0).prev("h3,h4").text();
                    breadcrumb.html($("<li />").text(root));
                    var li = $("<li />").append( $("<a href=\"javascript:;\" />").text(from.text()).data({id:id, tag:tag, data:data}) ).attr("class", "b-"+id);
                    breadcrumb.append(li);
                }
            }

            modal.find("input, textarea").val('').data("id", id);
            if (data.value.length) {
                modal.find("input").val(data.value);
            }

            if (data.tags) {
                tagList.html('');
                for (x in data.tags) {
                    var newBtn = createBtn(data.tags[x].id, data.tags[x].name, data.tags[x].rel, id);
                    tagList.append(newBtn).append(" ");
                }
                var b = $("<button type=\"button\" class=\"btn btn-default btn-sm btn-virtual\">添加新标签</button>").data({type:"other", tag:tag, pid:id});
                tagList.append(" ").append(b);
                modalTags.show();
            } else {
                modalTags.hide();
            }

            if (data.text !== undefined) {
                modalIntro.find("textarea").val(data.text);
                modalIntro.show();
            } else {
                modalIntro.hide();
            }
        });

        // Autosave Input by blur event
        $(document).on('blur', '[data-autosave]', function(e){
            var _inp    = $(this)
                , input  = _inp.parent().is(".input-group") ? _inp.parent().parent() : _inp.parent()
                , group  = input.parent()
                , icon   = input.children(".glyphicon")
                , loadcss = "glyphicon glyphicon-refresh glyphicon-loading form-control-feedback"
                , name   = _inp.attr("name")
                , value  = _inp.val()
                , id     = _inp.parents(".modal").length ? _inp.data("id") : 0
                , postdata = {name:name, value:value, hotel:<?php echo $hotel['id']; ?>, id:id};

            if (_inp.attr("value") == "" && value == "") return;

            group.addClass("has-feedback");
            if (icon.length){
                icon.attr("class", loadcss);
            }else{
                icon = $("<span class=\""+loadcss+"\" aria-hidden=\"true\"></span>");
                input.append(icon);
            }

            _inp.prop("readonly", true);
            $.post("<?php echo BASE_URL; ?>hotel.php?method=tag&action=setval", postdata, function(data){
                _inp.prop("readonly", false);
                icon.removeClass("glyphicon-refresh glyphicon-loading");
                if (data.s == 0){
                    icon.addClass("glyphicon-ok");
                    input.removeClass("has-error").addClass("has-success");
                }else{
                    icon.addClass("glyphicon-remove").attr("title", data.err);
                    input.removeClass("has-success").addClass("has-error");
                }
            }, "json");
        });

        $(".ui-select").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_multiple:"请选择..", width:"100%"});
    });


    </script>
</body>
</html>
