<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; <?php echo $data ? '编辑' : '添加'; ?>产品利润</title>

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

                <h1 class="page-header"><?php echo $data ? '编辑' : '添加'; ?>产品利润</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">渠道</label>
                            <div class="col-sm-5">
                                <select id="org" name="org" class="form-control ui-select">
                                    <option value="">请选择..</option>
                                    <option value="0" <?php if($data['org'] === '0') echo 'selected'; ?>>全局</option>
                                    <?php foreach($orgs as $v) { ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $data['org']) echo 'selected' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php if(!$data || $data['org'] == 0){ ?>
                        <div id="replace" class="form-group" style="display:none;">
                            <label class="col-sm-2 control-label">覆盖</label>
                            <div class="col-sm-10">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default active">
                                        <input type="radio" name="replace" value="all" autocomplete="off" checked> 覆盖所有渠道
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="radio" name="replace" value="same" autocomplete="off"> 仅覆盖相同利润的渠道
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="radio" name="replace" value="none" autocomplete="off"> 不覆盖任何渠道
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品类型</label>
                            <div class="col-sm-5">
                                <select id="objtype" name="objtype" class="form-control ui-select" <?php if($data) echo "disabled"; ?>>
                                    <option value="">请选择..</option>
                                    <option value="hotel" <?php     if($data['objtype'] == 'hotel' || $data['objtype'] == 'room') echo 'selected'; ?>>酒店产品</option>
                                    <option value="flight" <?php    if($data['objtype'] == 'flight') echo 'selected'; ?> disabled>机票产品</option>
                                    <option value="view" <?php      if($data['objtype'] == 'view') echo 'selected'; ?>>景点/体验产品</option>
                                    <option value="goods" <?php     if($data['objtype'] == 'goods') echo 'selected'; ?>>生鲜/商品产品</option>
                                    <option value="product2" <?php  if($data['objtype'] == 'product2') echo 'selected'; ?>>酒店+车辆产品</option>
                                    <option value="product4" <?php  if($data['objtype'] == 'product4') echo 'selected'; ?>>酒店+机票产品</option>
                                    <option value="product6" <?php  if($data['objtype'] == 'product6') echo 'selected'; ?> disabled>酒店+景点产品</option>
                                    <option value="product8" <?php  if($data['objtype'] == 'product8') echo 'selected'; ?> disabled>机票+景点产品</option>
                                    <option value="product9" <?php  if($data['objtype'] == 'product9') echo 'selected'; ?> disabled>机票+酒店+景点产品</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">支付类型</label>
                            <div class="col-sm-5">
                                <select id="payment" name="payment" class="form-control ui-select" <?php if($data) echo "disabled"; ?>>
                                    <option value="">请选择..</option>
                                    <option value="ticket" <?php if($data['payment'] == 'ticket') echo 'selected'; ?>>券类产品</option>
                                    <option value="prepay" <?php if($data['payment'] == 'prepay') echo 'selected'; ?>>预付产品</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">指定产品</label>
                            <div class="col-sm-7">
                                <select id="product" name="objid" class="form-control" <?php if($data) echo "disabled"; ?>>
                                    <?php if (!$data){ ?>
                                    <option disabled>请先选择 “产品类型” 及 “支付类型” ..</option>
                                    <?php }else{ ?>
                                    <option value="0" <?php if($data['objid'] == '0') echo "selected" ?>>全部产品</option>
                                        <?php foreach ($data['products'] as $v) { ?>
                                    <option value="<?php echo $v['id'] ?>" <?php if ($v['id'] == $data['objid']) echo 'selected'; ?>><?php echo "{$v['name']} [{$v['id']}]"; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="item-select" style="display:none;">
                            <label class="col-sm-2 control-label">指定产品子项</label>
                            <div class="col-sm-7">
                                <select id="item" name="item" class="form-control ui-select" <?php if($data) echo "disabled"; ?>>
                                    <option value="">不指定</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">利润类型</label>
                            <div class="col-sm-4">
                                <select id="type" name="type" class="form-control ui-select">
                                    <option value="amount" <?php if($data['type'] == 'amount') echo 'selected'; ?>>金额</option>
                                    <option value="percent" <?php if($data['type'] == 'percent') echo 'selected'; ?>>百分比</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">利润</label>
                            <div class="col-sm-4">
                                <div class="input-group price">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" name="profit" class="form-control" value="<?php echo $data['profit']; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">儿童利润</label>
                            <div class="col-sm-4">
                                <div class="input-group price">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" name="child" class="form-control" value="<?php echo $data['child']; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">婴儿利润</label>
                            <div class="col-sm-4">
                                <div class="input-group price">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" name="baby" class="form-control" value="<?php echo $data['baby']; ?>" />
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
                                <button type="button" class="btn btn-primary btn-sm" onclick="save()">新建</button>
                            </div>
                            <?php }else{ ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="history.go(-1)">返回</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="save()">保存</button>
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
    <script>
    function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>profit.php?method=edit", postdata, function(data){
            if(data.s == 0){
                <?php if($data){ ?>
                alert("保存成功", "success");
                <?php }else{ ?>
                location.href = "<?php echo BASE_URL; ?>profit.php?method=edit&id="+data.rs+"#success";
                <?php } ?>
            }else{
                alert(data.err, 'error');
            }
        }, "json");
    }

    $(function(){

        var select_child = function(item){
            var objtype = $("#objtype").val();
            var payment = $("#payment").val();
            var pid = $("#product").val();
            var type = objtype == 'hotel' ? 'room' : 'item';
            $.post("<?php echo BASE_URL; ?>profit.php?method=product", {type:type, payment:payment, pid:pid}, function(data){
                if (data.s == 0) {
                    $("#item").html('<option value="0">全部子项</option>');
                    for(x in data.rs){
                        var opt = $("<option />");
                        opt.text(data.rs[x].name).attr("value", data.rs[x].id);
                        if (data.rs[x].id == item) opt.attr("selected", "selected");
                        $("#item").append(opt);
                    }
                    $("#item").trigger('chosen:updated');
                }
            }, "json");
        }

        $("#org").change(function(){
            var val = $(this).val();
            if (val == '0'){
                $("#replace").show();
            }else{
                $("#replace").hide();
            }
        });

        <?php if($data['org'] == '0'){ ?>
        $("#replace").show();
        <?php } ?>

        $("#objtype, #payment").change(function(){
            var type = $("#objtype").val();
            var payment = $("#payment").val();
            if (!type || !payment) return;
            $.post("<?php echo BASE_URL; ?>profit.php?method=product", {type:type, payment:payment}, function(data){
                if (data.s == 0) {
                    $("#product").html('<option value="0">全部产品</option>');
                    for(x in data.rs){
                        var opt = $("<option />");
                        opt.text(data.rs[x].name + "(ID:" + data.rs[x].id + ")").attr("value", data.rs[x].id);
                        $("#product").append(opt);
                    }
                    $("#product").trigger('chosen:updated').unbind("change");
                    if(type == 'hotel' || type == 'goods' || type == 'product4' || type == 'product2'){
                        $("#item-select").show();
                        $("#product").bind("change", function(){ select_child(0); });
                    }else{
                        $("#item-select").hide();
                    }
                }else{
                    alert("产品读取异常，请重试", "error");
                }
            }, "json");
        });

        var type = $("#objtype").val();
        if(type == 'hotel' || type == 'goods' || type == 'product4' || type == 'product2'){
            $("#item-select").show();
            select_child(<?php echo $item; ?>);
        }

        if(location.hash == "#success"){
            alert("信息保存成功!", "success");
            location.hash = "";
        }

        $(".ui-select").chosen({width:'100%', disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});

        $("#product").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请先选择 “产品类型” 及 “支付类型” ..", search_contains:true});

    });
    </script>
</body>
</html>
