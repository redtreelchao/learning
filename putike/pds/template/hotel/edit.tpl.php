<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; <?php echo $id ? '编辑' : '添加'; ?>酒店信息</title>

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

                <h1 class="page-header"><?php echo $id ? '编辑' : '添加'; ?>酒店信息</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <?php if (!$id) { ?>
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4>重要信息</h4>
                            <p>
                            酒店数据是重要的基本信息，请先从采集网站（艺龙）找到该酒店。<br />并将网址完整复制到输入框后，点击<b>采集</b>按钮。
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">酒店地址</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="url" name="url" class="form-control" placeholder="http://hotel.elong.com/..." value="" />
                                    <span class="input-group-btn">
                                        <button id="collection" class="btn btn-default" type="button"><span class="glyphicon glyphicon-cloud-download"></span></button>
                                    </span>
                                </div>
                                <p class="help-block">仅支持 hotel.elong.com 及 globalhotel.elong.com 为域名的地址</p>
                            </div>
                        </div>

                        <?php } ?>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">酒店名称</label>
                            <div class="col-sm-6">
                                <input type="text" id="name" name="name" class="form-control" placeholder="中文名" value="<?php echo $data['name']; ?>" <?php if(!$id) echo 'readonly'; ?> />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">拼音</label>
                            <div class="col-sm-6">
                                <input type="text" name="pinyin" class="form-control" placeholder="可不填写，系统将自动生成" value="<?php echo $data['pinyin']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">外文名称</label>
                            <div class="col-sm-6">
                                <input type="text" name="en" class="form-control" placeholder="外文名" value="<?php echo $data['en']; ?>" <?php if(!$id) echo 'readonly'; ?> />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">国家</label>
                            <div class="col-sm-5">
                                <select id="country" name="country" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <?php foreach($country as $v) { ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $data['country']) echo 'selected="selected"' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">城市</label>
                            <div class="col-sm-5">
                                <select id="city" name="city" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <?php foreach($city as $v) { ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $data['city']) echo 'selected="selected"' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">前台电话</label>
                            <div class="col-sm-6">
                                <input type="text" id="tel" name="tel" class="form-control" placeholder="(86)021-88888888" value="<?php echo $data['tel']; ?>" <?php if(!$id) echo 'readonly'; ?> />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">酒店地址</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" id="address" name="address" class="form-control" placeholder="" value="<?php echo $data['address']; ?>" <?php if(!$id) echo 'readonly'; ?> />
                                    <span class="input-group-btn">
                                        <button id="searchPos" class="btn btn-default" type="button"><span class="glyphicon glyphicon-map-marker"></span></button>
                                    </span>
                                </div>
                                <p class="help-block">地址请勿重复填写省市信息</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">位置坐标</label>
                            <div class="col-sm-6">
                                <input type="text" id="position" name="position" class="form-control" placeholder="" value="<?php echo intval($data['lat']) ? ($data['lng'].','.$data['lat']) : ''; ?>" />
                                <p class="help-block">请注意谷歌的坐标为纬度,经度坐标，应反转</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label hidden-xs"> </label>
                            <div class="col-sm-9">
                                <div style="padding:10px; padding:10px; border:#ddd solid 1px; border-radius:10px;">
                                    <div id="map" style="height:300px"></div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="elong" name="ELG" value="" />

                    </div>

                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">

                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $id ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>

                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
                            </div>
                            <?php if(!$data) { ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-primary btn-sm" onclick="save()">新建</button>
                            </div>
                            <?php }else{ ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="history.go(-1)">返回</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="save()">保存</button>
                            </div>
                            <?php } ?>
                        </div>

                        <?php if ($id) { ?>
                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">操作记录</h3>
                            </div>
                            <ul class="list-group history">
                                <?php if($history){  ?>
                                    <?php foreach($history as $k => $v){ ?>
                                    <li class="list-group-item">
                                        <span class="log"><b><?php echo $v['username']; ?>&nbsp;</b><?php echo $v['intro']; ?></span>
                                        <span class="time"><?php echo date('m-d H:i', $v['time']); ?></span>
                                    </li>
                                    <?php } ?>
                                    <?php if (count($history) == 10) { ?>
                                    <li class="list-group-item text-center">
                                        <a class="c9" href="javascript:;" onclick="history(this)" data-url="<?php echo BASE_URL; ?>hotel.php?method=history&hotel=<?php echo $data['id']; ?>">查看更多记录</a>
                                    </li>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <li class="list-group-item text-center c9">无操作记录</li>
                                <?php } ?>
                            </ul>
                        </div>
                        <!-- panel -->
                        <?php } ?>

                    </div>

                </form>
                <!-- end form -->

            </div>
        </div>
        <!-- end main -->

    </div>
                                        <?php if (false) { ?>
                                        <a class="supbtn glyphicon glyphicon-repeat" title="查看历史版本" href="<?php echo BASE_URL; ?>hotel.php?method=edit&id=<?php echo $data['id'] ?>&history=<?php echo $v['id']; ?>" target="_blank"></a>
                                        <?php } ?>

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=<?php echo config('web.baidumap'); ?>"></script>
    <script>
    function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>hotel.php?method=edit", postdata, function(data){
            if(data.s == 0){
                <?php if($data){ ?>
                alert("保存成功", "success");
                <?php }else{ ?>
                location.href = "<?php echo BASE_URL; ?>hotel.php?method=edit&id="+data.rs+"#success";
                <?php } ?>
            }else{
                alert(data.err, 'error');
            }
        }, "json");
    }

    $(function(){


        var map = new BMap.Map("map");
        var point = new BMap.Point(121.479275,31.239035);
        map.centerAndZoom(point, 11);
        map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}))

        <?php if(intval($data['lat'])) { // 通过坐标直接定位 ?>
        point = new BMap.Point(<?php echo "{$data['lng']},{$data['lat']}"; ?>);
        map.centerAndZoom(point, 17);
        var marker = new BMap.Marker(point);
        map.addOverlay(marker);
        <?php } else { ?>
        var marker = new BMap.Marker(point);
        map.addOverlay(marker);
        <?php } ?>

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

            var name = $("#name").val();
            var address = $("#address").val();

            var local = new BMap.LocalSearch(city, {onSearchComplete:function(result){
                btn.prop("disabled", false).html('<span class="glyphicon glyphicon-map-marker"></span>');
                var pos = result.getPoi(0);
                if (pos === undefined || pos.point === undefined) {
                    if(result.keyword != address) {
                        local.search(address);
                    } else {
                        alert("地址定位未找到..", "warning");
                    }
                    return;
                }
                marker.setPosition(pos.point);
                $("#position").val(pos.point.lng+","+pos.point.lat);
                map.centerAndZoom(pos.point, 17);
            }});

            local.search(name);
        });


        $("#collection").click(function(){
            var btn = $(this);
            var url = $("#url").val();
            if (!url) { alert("请输入采集地址", "error"); return false; }
            btn.prop("disabled", true).html('<span class="glyphicon glyphicon-refresh glyphicon-loading"></span>');
            $.post("<?php echo BASE_URL; ?>hotel.php?method=collection", {url:url}, function(data){
                if(data.s == 0) {
                    $("#name").val(data.rs.name);
                    $("#tel").val(data.rs.tel);
                    $("#address").val(data.rs.address);
                    if (data.rs.elg !== undefined) $("#elong").val(data.rs.elg);
                    if (data.rs.pos !== undefined) {
                        $("#position").val(data.rs.pos);
                        var pos = data.rs.pos.split(","),
                            lng = pos[0],
                            lat = pos[1];
                        point = new BMap.Point(lng, lat);
                        map.centerAndZoom(point, 17);
                        var marker = new BMap.Marker(point);
                        map.addOverlay(marker);
                    }
                } else {
                    alert(data.err, "error");
                }
                btn.prop("disabled", false).html('<span class="glyphicon glyphicon-cloud-download"></span>');
            }, "json");
        });


        if(location.hash == "#success"){
            alert("信息保存成功!", "success");
            location.hash = "";
        }

        <?php if(isset($error)){ ?>
        alert("<?php echo $error; ?>", "error");
        <?php } ?>

        $(".ui-select").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});

    });
    </script>
</body>
</html>
