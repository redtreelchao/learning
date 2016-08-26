<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; <?php echo $data ? '编辑' : '添加'; ?>景区/体验</title>

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

                <h1 class="page-header"><?php echo $data ? '编辑' : '添加'; ?>景区/体验</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">景区/体验名</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" id="name" class="form-control"  value="<?php echo $data['name']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">拼音</label>
                            <div class="col-sm-6">
                                <input type="text" name="pinyin" class="form-control" placeholder="可不填写，系统将自动生成" value="<?php echo $data['pinyin']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">类型</label>
                            <div class="col-sm-4">
                                <select name="type" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <?php foreach(viewtypes() as $k => $v) { ?>
                                    <option value="<?php echo $k; ?>" <?php if($k == $data['type']) echo 'selected="selected"' ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                </select>
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
                            <label class="col-sm-2 control-label">电话</label>
                            <div class="col-sm-6">
                                <input type="text" name="tel" class="form-control" placeholder="(86)021-88888888" value="<?php echo $data['tel']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">地址</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" id="address" name="address" class="form-control" placeholder="" value="<?php echo $data['address']; ?>" />
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
                   </div>



                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $data ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>

                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
                            </div>
                            <?php if(!$data) { ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-primary btn-sm" onClick="save();">新建</button>
                            </div>
                            <?php }else{ ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onClick="history.go(-1)">返回</button>
                                <button type="button" class="btn btn-primary btn-sm" onClick="save()">保存</button>
                            </div>
                            <?php } ?>
                        </div>
                   </div>


                </form>
                <!-- end form -->

            </div>
        </div>
        <!-- end main -->

    </div>


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=<?php echo config('web.baidumap'); ?>"></script>
    <script>
     function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>view.php?method=edit", postdata, function(data){
            if(data.s == 0){
                <?php if($data){ ?>
                alert("保存成功", "success");
                <?php }else{ ?>
                location.href = "<?php echo BASE_URL; ?>view.php?method=edit&id="+data.rs+"#success";
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
            var local = new BMap.LocalSearch(city, {onSearchComplete:function(result){
                btn.prop("disabled", false).html('<span class="glyphicon glyphicon-map-marker"></span>');
                var pos = result.getPoi(0);
                if (pos === undefined){
                    alert("未搜索到相关地址", "warning");
                }
                marker.setPosition(pos.point);
                $("#position").val(pos.point.lng+","+pos.point.lat);
                map.centerAndZoom(pos.point, 17);
            }});
            var keyword = $("#address").val();
            local.search(keyword);
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
