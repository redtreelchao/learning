<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; <?php echo $data ? '编辑' : '添加'; ?>产品信息</title>

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

                <h1 class="page-header"><?php echo $data ? '编辑' : '添加'; ?>产品信息</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <style>
                        #product-name { overflow:hidden; position:relative; }
                        #product-name:after { content:""; display:block; position:absolute; height:100%; border-left:#f0ad4e dashed 1px; top:0; left:310px; }
                        </style>

                        <h3>基本信息</h3>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品名称</label>
                            <div class="col-sm-6" id="product-name">
                                <input type="text" name="name" class="form-control" placeholder="中文名" value="<?php echo htmlspecialchars($data['name']); ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品类型</label>
                            <div class="col-sm-5">
                                <select id="type" name="type" class="form-control ui-select" <?php if($data) echo "disabled"; ?>>
                                    <?php foreach(producttypes() as $v){ ?>
                                    <option value="<?php echo $v['code']; ?>"<?php if($data['type'] == $v['code']) echo ' selected'; if($v['disabled']) echo ' disabled' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">支付类型</label>
                            <div class="col-sm-5">
                                <select id="payment" name="payment" class="form-control ui-select" <?php if($data) echo "disabled"; ?>>
                                    <option value="ticket" <?php if($data['payment'] == 'ticket') echo 'selected'; ?>>券类产品</option>
                                    <option value="prepay" <?php if($data['payment'] == 'prepay') echo 'selected'; ?>>预付产品（Ebooking）</option>
                                </select>
                            </div>
                        </div>

                        <?php $tags = array(); //$data ? explode(',', $data['tags']) : array(); ?>
                        <!--div class="form-group">
                            <label class="col-sm-2 control-label">套餐类型</label>
                            <div class="col-sm-5">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default <?php if(in_array('精品酒店', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="精品酒店" <?php if(in_array('精品酒店', $tags)) echo 'checked'; ?> /> 精品酒店
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('品牌酒店', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="品牌酒店" <?php if(in_array('品牌酒店', $tags)) echo 'checked'; ?> /> 品牌酒店
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('精美名宿', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="精美名宿" <?php if(in_array('精美名宿', $tags)) echo 'checked'; ?> /> 精美名宿
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-5 col-sm-offset-2">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default <?php if(in_array('国内度假', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="国内度假" <?php if(in_array('国内度假', $tags)) echo 'checked'; ?> /> 国内度假
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('海外自由行', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="海外自由行" <?php if(in_array('海外自由行', $tags)) echo 'checked'; ?> /> 海外自由行
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('特色体验', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="特色体验" <?php if(in_array('特色体验', $tags)) echo 'checked'; ?> /> 特色体验
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('特卖产品', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="特卖产品" <?php if(in_array('特卖产品', $tags)) echo 'checked'; ?> /> 特卖产品
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-2">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default <?php if($data['new']) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="new" value="1" <?php if($data['new']) echo 'checked'; ?>/> 新品
                                    </label>
                                    <label class="btn btn-default <?php if($data['exclusive']) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="exclusive" value="1" <?php if($data['exclusive']) echo 'checked'; ?> onchange="this.checked ? $('.form-exclusive').show() : $('.form-exclusive').hide()" /> 独家
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-exclusive" style="<?php if(!$data['exclusive']) echo 'display:none'; ?>">
                            <div class="col-sm-3 col-sm-offset-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="excstart" id="excstart" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $data['excstart'] ? date('Y-m-d', $data['excstart']) : ''; ?>" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="excend" id="excend" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $data['excend'] ? date('Y-m-d', $data['excend']) : ''; ?>" />
                                </div>
                            </div>
                        </div-->

                        <div class="form-group">
                            <label class="col-sm-2 control-label">主题类型</label>
                            <div class="col-sm-5">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default <?php if(in_array('亲子', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="亲子" <?php if(in_array('亲子', $tags)) echo 'checked'; ?> /> 亲子
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('海岛', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="海岛" <?php if(in_array('海岛', $tags)) echo 'checked'; ?> /> 海岛
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('自驾', $tags)) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="tag[]" value="自驾" <?php if(in_array('自驾', $tags)) echo 'checked'; ?> /> 自驾
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">售卖渠道</label>
                            <div class="col-sm-5">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default <?php if(in_array('1', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="1" <?php if(in_array('1', $data['org'])) echo 'checked'; ?> /> putike
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('2', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="2" <?php if(in_array('2', $data['org'])) echo 'checked'; ?> /> feekr
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('3', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="3" <?php if(in_array('3', $data['org'])) echo 'checked'; ?> /> 浙江旅游
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('4', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="4" <?php if(in_array('4', $data['org'])) echo 'checked'; ?> /> 美宿
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品经理</label>
                            <div class="col-sm-8">
                                <select name="bd[]" data-type="bd" class="form-control ui-select chosen-user" multiple="multiple" data-placeholder="请选择产品经理" id="bd" ></select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品助理</label>
                            <div class="col-sm-8">
                                <select name="ba[]" data-type="ba" class="form-control ui-select chosen-user" multiple="multiple" data-placeholder="请选择产品助理" id="ba"></select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">上架时间</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="start" id="start" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $data['start'] ? date('Y-m-d', $data['start']) : ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">下架时间</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="end" id="end" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $data['end'] ? date('Y-m-d', $data['end']) : ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品介绍</label>
                            <div class="col-sm-8">
                                <textarea name="intro" class="form-control" rows="6" /><?php echo $data['intro']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">使用要求</label>
                            <div class="col-sm-8">
                                <textarea name="rule" class="form-control" rows="6" /><?php echo $data['rule']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">退改规则</label>
                            <div class="col-sm-8">
                                <textarea name="refund" class="form-control" rows="6" /><?php echo $data['refund']; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">

                        <!-- product post -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $data ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>
                                <p><span class="glyphicon glyphicon-eye-open"></span> 状　态: <?php echo $data ? ($data['status'] > 0 ? '上架' : '下架') : '未发布'; ?></p>

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

                        <?php if($data){ ?>
                        <!-- product items -->
                        <div class="panel panel-default -hidden-sm -hidden-xs">
                            <div class="panel-heading">
                                <h3 class="panel-title">产品包内容</h3>
                            </div>

                            <ul id="item-list" class="list-group" style="font-size:12px;">
                                <?php
                                $btn = $data['payment'] == 'prepay' ? 'fa-calendar' : 'fa-rmb';
                                if($data['items']) {
                                    foreach($data['items'] as $item) {
                                        switch($item['objtype']) {
                                            case 'room':
                                ?>
                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-building-o"></span><?php echo $item['name']; ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="fa <?php echo $btn; ?>"></span></button></li>
                                <?php
                                            break;
                                            case 'flight':
                                ?>
                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-plane"></span>始发：<?php echo $item['name'], ($item['ext'] ? ' （往返）' : ''); ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="fa <?php echo $btn; ?>"></span></button></li>
                                <?php
                                            break;
                                            case 'auto':
                                ?>
                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-car"></span>出发：<?php echo $item['name']; ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="fa <?php echo $btn; ?>"></span></button></li>
                                <?php
                                            break;
                                            case 'goods':
                                ?>
                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-gift"></span><?php echo $item['name']; ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="fa <?php echo $btn; ?>"></span></button></li>
                                <?php
                                            break;
                                        }
                                    }
                                } else {
                                ?>
                                <li class="list-group-item"><div class="empty">未录入任何产品内容</div></li>
                                <?php
                                }
                                ?>
                            </ul>

                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="createitem(<?php echo $data['type'], ',', $data['id']; ?>)">添加</button>
                                <button type="button" class="btn btn-default btn-sm" data-mode="normal" onclick="editmode(this)">编辑模式</button>
                            </div>
                        </div>

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
                                        <a class="c9" href="javascript:;" onclick="history(this)" data-url="<?php echo BASE_URL; ?>product.php?method=history&product=<?php echo $data['id']; ?>">查看更多记录</a>
                                    </li>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <li class="list-group-item text-center c9">无操作记录</li>
                                <?php } ?>
                            </ul>
                        </div>

                        <?php } ?>


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
    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>

    <?php include(dirname(__FILE__).'/_item_modal.tpl.php'); ?>

    <script>
    // save product
    function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>product.php?method=edit", postdata, function(data){
            if(data.s == 0){
                <?php if($data){ ?>
                alert("保存成功", "success");
                <?php }else{ ?>
                location.href = "<?php echo BASE_URL; ?>product.php?method=edit&id="+data.rs+"#success";
                <?php } ?>
            }else{
                alert(data.err, 'error');
            }
        }, "json");
    }

    // save product's items base data
    function saveitem(){
        var form = $("#item-form form");
        $.post("<?php echo BASE_URL; ?>product.php?method=item", form.serialize(), function(data){
            if(data.s == 0){
                var type = data.rs.objtype;
                var id = $("#item-id").val();
                var list = $("#item-list");
                if (!id) {
                    id = data.rs.id;
                    list.find(".empty").parent().remove(); // if is empty
                    var li = $("<li class=\"list-group-item\"><div></div><button type=\"button\" class=\"btn btn-default btn-xs\"><span class=\"fa <?php echo $data ? $btn : ''; ?>\"></span></button></li>");
                    switch (type){
                        case "room":
                            li.children("div").text(data.rs.name).prepend("<span class=\"fa fa-building-o\"></span>");
                        break;
                        case "flight":
                            li.children("div").text("始发："+data.rs.name+(data.rs.ext ? " （往返）" : "")).prepend("<span class=\"fa fa-plane\"></span>");
                        break;
                        case "auto":
                            li.children("div").text("出发："+data.rs.name).prepend("<span class=\"fa fa-car\"></span>");
                        break;
                        case "goods":
                            li.children("div").text(data.rs.name).prepend("<span class=\"fa fa-gift\"></span>");
                        break;
                    }
                    li.children("button").data("code", id);
                    list.append(li);
                } else {
                    var item = $("#item-"+id).children("div");
                    var ico = item.children("span").clone();
                    switch (type){
                        case "room":    var name = data.rs.name; break;
                        case "flight":  var name = "始发："+data.rs.name+(data.rs.ext ? " （往返）" : ""); break;
                        case "auto":  var name = "出发："+data.rs.name; break;
                        case "goods":  var name = data.rs.name; break;
                    }
                    item.text(name).prepend(ico);
                }
                $("#item-form").modal("hide");
            }else{
                alert(data.err, 'error', null, "#item-form .modal-body");
            }
        }, "json");
    }

    // itmes list be toggle mode/calendar mode
    function editmode(btn){
        var btn = $(btn);
        if (btn.data("mode") == "normal") {
            var list = $("#item-list");
            btn.text("价格模式").data("mode", "edit");
            $("#item-list li button").each(function(){
                var _this = $(this);
                _this.html('<span class="glyphicon glyphicon-pencil"></span>').unbind("click").bind("click", function(){ edititem(this); });
                var code = _this.data("code");
                _this.after('<a data-code="'+code+'" class="supbtn red glyphicon glyphicon-remove" onclick="delitem(this)" href="javascript:;" title="删除"></a>');
            });
        }else{
            btn.text("编辑模式").data("mode", "normal");
            $("#item-list li button").html('<span class="fa <?php echo $data ? $btn : ''; ?>"></span>').unbind("click").bind("click", function(){ price(this); });
            $("#item-list li .glyphicon-remove").remove();
        }
    }

    $(function(){

        if(location.hash == "#success"){
            alert("信息保存成功!", "success");
            location.hash = "";
        }

        <?php if($method == 'edit'){ ?>

        var bd='<?php echo $data['bd'];?>'.split(',');//产品经理
        var ba='<?php echo $data['ba'];?>'.split(',');//产品助理
        var edit = true;
        <?php }else{ ?>
        var edit = false;
        <?php }?>



        $.get("<?php echo BASE_URL; ?>product.php?method=user",null,function (e) {
            if(e.s ==0){
                $(e.rs).each(function (k, v) {
                    $(".chosen-user").append("<option value='"+v.id+"'>"+v.name+"</option>");
                });

                if(edit == true){
                    $('#bd').val(bd);
                    $('#ba').val(ba);
                }

                $(".chosen-user").chosen({disable_search_threshold:10, width:"100%", no_results_text:"未找到..", placeholder_text_single:"请选择.."});




            }else{
                alert(e.err, '加载全局用户失败');
            }

        });


        $("#start, #excstart").zdatepicker({viewmonths:1, disable:{0:['1900-1-1','<?php echo date('Y-m-d', strtotime('-1 day')); ?>']}});

        $("#end").zdatepicker({viewmonths:1, disable:{0:['1900-1-1',$("#start")]}});

        $("#excend").zdatepicker({viewmonths:1, disable:{0:['1900-1-1',$("#excstart")]}});

        $("#item-list li button").bind("click", function(){
            price(this);
        });



    });
    </script>
</body>
</html>
