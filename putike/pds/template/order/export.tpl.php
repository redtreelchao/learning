<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 订单管理</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/order.css" rel="stylesheet" />

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

                <h1 class="page-header">订单导出</h1>

                <form id="form" role="form" action="<?php echo BASE_URL; ?>order.php" method="POST" target="_blank">

                    <input type="hidden" name="method" value="export" />

                    <h3>导出字段</h3>

                    <div class="form-group row">
                        <div class="btn-group col-sm-12 checkbox-group" data-toggle="buttons">
                            <label class="btn btn-default active disabled" style="background-color:#e6e6e6; border-color:#adadad; box-shadow:inset 0 3px 5px rgba(0,0,0,.125)">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="order" readonly checked /> 订单号
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="from" /> 分销渠道
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="contact" /> 联系人
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="tel" /> 联系电话
                            </label>
                            <label class="btn btn-default price-type">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="floor" /> <span style="display:none;">单</span>底价
                            </label>
                            <label class="btn btn-default price-type active">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="total" checked /> <span style="display:none;">单</span>售价
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="refund" /> 退款
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="invoice" /> 是否开发票
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="paytype" /> 支付方式
                            </label>
                            <label class="btn btn-default active">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="status" checked /> 订单状态
                            </label>
                            <label class="btn btn-default active">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="time" checked /> 下单时间
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="btn-group col-sm-12 checkbox-group" data-toggle="buttons">
                            <label class="btn btn-default" id="field-product">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="product" /> 产品名
                            </label>
                            <label class="btn btn-default disabled">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="item" disabled /> 券/明细
                            </label>
                            <label class="btn btn-default payment-ticket disabled">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="num" disabled /> 购买券数
                            </label>
                            <label class="btn btn-default type-hotel" id="field-district">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="district" /> 地区信息
                            </label>
                            <label class="btn btn-default type-hotel" id="field-hotel">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="hotel" /> 酒店名
                            </label>
                            <label class="btn btn-default type-hotel" id="field-hotel-type">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="hoteltype" /> 酒店类型
                            </label>
                            <label class="btn btn-default type-hotel disabled">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="room" disabled /> 房型名
                            </label>
                            <label class="btn btn-default type-hotel disabled">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="nights" disabled /> 间夜数
                            </label>
                            <label class="btn btn-default type-hotel disabled">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="checkin" disabled /> 入住时间
                            </label>
                            <label class="btn btn-default type-flight" id="field-flight" style="display:none;">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="flight" /> 航班
                            </label>
                            <label class="btn btn-default type-view" id="field-view" style="display:none;">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="view" /> 景/体验
                            </label>
                            <label class="btn btn-default type-flight type-view disabled" id="field-people" style="display:none;">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="people" disabled /> 人数
                            </label>
                            <label class="btn btn-default type-goods" id="field-view" style="display:none;">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="goods" /> 商品
                            </label>
                            <label class="btn btn-default type-goods" id="field-view" style="display:none;">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="num" /> 数量
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="settle" /> 结算时间
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" autocomplete="off" name="fields[]" value="supply" /> 供应商
                            </label>
                        </div>
                    </div>



                    <h3>查询条件</h3>

                    <div id="search-timetype" class="form-group row condition">
                        <div class="btn-group col-sm-12" data-toggle="buttons">
                            <label class="btn btn-default active">
                                <input type="radio" name="time" value="booking" autocomplete="off" checked /> 预订时间
                            </label>
                            <label class="btn btn-default type-hotel">
                                <input type="radio" name="time" value="checkin" autocomplete="off" /> 入住时间
                            </label>
                            <label class="btn btn-default  type-hotel">
                                <input type="radio" name="time" value="appointment" autocomplete="off" /> 预约时间
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="time" value="refund" autocomplete="off" /> 申请退款时间
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="time" value="refunded" autocomplete="off" /> 退款完成时间
                            </label>
                        </div>
                    </div>

                    <div class="form-group row condition">
                        <div class="col-sm-6 col-lg-3">
                            <label>开始日期</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="start" class="form-control ui-datepicker" autocomplete="off" value="<?php echo date('Y-m-d', strtotime('-7 day')); ?>" />
                            </div>
                        </div>

                        <hr class="clear clear-xs" />

                        <div class="col-sm-6 col-lg-3">
                            <label>结束日期 (不包括)</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="end" class="form-control ui-datepicker" autocomplete="off" value="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>

                        <hr class="clear clear-xs clear-sm clear-md" />

                        <div class="col-sm-6 col-lg-3">
                            <label>酒店/产品</label>
                            <input type="text" class="form-control" name="name" value="" />
                        </div>

                        <hr class="clear clear-xs type-hotel" />

                        <div class="col-sm-6 col-lg-3 type-hotel">
                            <label>酒店类型</label>
                            <select class="form-control ui-select" name="type">
                                <option value="">请选择..</option>
                                <?php
                                $types = hotel::types();
                                foreach($types as $v){ ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <hr class="clear clear-xs clear-sm clear-md clear-lg" />

                        <div class="col-sm-6 col-lg-3">
                            <label>分销渠道</label>
                            <select class="form-control ui-select" name="from">
                                <option value="">请选择..</option>
                                <?php foreach($orgs as $v){ ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <hr class="clear clear-xs" />

                        <div class="col-sm-6 col-lg-3">
                            <label>供应商</label>
                            <select class="form-control ui-select" name="supply">
                                <option value="">请选择..</option>
                                <?php foreach($supplies as $k => $v){ ?>
                                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <hr class="clear clear-xs clear-sm clear-md" />

                        <div class="col-sm-6 col-lg-3">
                            <label>渠道款项</label>
                            <select class="form-control ui-select" name="clear">
                                <option value="">请选择..</option>
                                <option value="1">已结帐</option>
                                <option value="-1">未结帐</option>
                            </select>
                        </div>

                        <hr class="clear clear-xs clear-sm clear-md clear-lg" />

                        <div class="col-lg-6">
                            <label>产品类型</label>
                            <div class="form-group" id="pro-payment">
                                <div class="btn-group checkbox-group" data-toggle="buttons">
                                    <label class="btn btn-default active">
                                        <input type="radio" autocomplete="off" name="payment" value="ticket" checked /> 券类
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="radio" autocomplete="off" name="payment" value="prepay" /> 预付
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="pro-type">
                                <div class="btn-group checkbox-group" data-toggle="buttons">
                                    <?php foreach(producttypes() as $v){ ?>
                                    <label class="btn btn-default<?php if($v['disabled']) echo ' disabled'; ?>">
                                        <input type="radio" autocomplete="off" name="product" value="<?php echo $v['code']; ?>"<?php if($v['disabled']) echo ' disabled'; ?> /> <?php echo $v['abbr']; ?>
                                    </label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <hr class="clear clear-xs clear-sm" />

                        <div class="col-lg-6">
                            <label>订单状态</label>
                            <div class="form-group">
                                <div class="btn-group checkbox-group" data-toggle="buttons">
                                    <label class="btn btn-default">
                                        <input type="checkbox" autocomplete="off" name="status[]" value="unpay" /> 未支付
                                    </label>
                                    <label class="btn btn-default active">
                                        <input type="checkbox" autocomplete="off" name="status[]" value="paid" checked /> 已支付
                                    </label>
                                    <label class="btn btn-default active">
                                        <input type="checkbox" autocomplete="off" name="status[]" value="used" checked /> 已预约
                                    </label>
                                    <label class="btn btn-default active">
                                        <input type="checkbox" autocomplete="off" name="status[]" value="over" checked /> 已完成
                                    </label>
                                    <label class="btn btn-default active">
                                        <input type="checkbox" autocomplete="off" name="status[]" value="refund" checked /> 退款中/部分退款
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" autocomplete="off" name="status[]" value="refunded" /> 全额退款
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <blockquote>
                        <p>将有<b id="count">0</b>条数据导出</p>
                        <button type="button" id="export" class="btn btn-primary btn-lg"> 开始导出数据 </button>
                    </blockquote>

                </form>

            </div>


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>


<script>
$(function(){

    $("#pro-type input").change(function(){
        $(".type-hotel, .type-flight, .type-view, .type-goods").hide().find("input").prop("disabled", 1);
        $("#pro-type input:checked").each(function(){
            var val = $(this).val(), labels;
            switch(val){
                case '1': labels = $(".type-hotel"); break;
                case '2': labels = $(".type-hotel, .type-auto"); break;
                case '3': labels = $(".type-flight"); break;
                case '5': labels = $(".type-view"); break;
                case '4': labels = $(".type-hotel, .type-flight"); break;
                case '6': labels = $(".type-hotel, .type-view"); break;
                case '7': labels = $(".type-goods"); break;
                case '8': labels = $(".type-fight, .type-view"); break;
                case '9': labels = $(".type-hotel, .type-flight, .type-view"); break;
            }
            labels.show().find("input").prop("disabled", 0);
        });
    });

    $("#pro-payment").change(function(){
        $(".payment-ticket, .payment-prepay").hide().find("input").prop("disabled", 1);
        var val = $("#pro-payment input:checked").eq(0).val(), labels;
        switch(val){
            case 'ticket': labels = $(".payment-ticket"); break;
            case 'prepay': labels = $(".payment-prepay"); break;
        }
        labels.show().find("input").find("input").prop("disabled", 0);
    });

    var toggle = function(labels, disable){
        var inputs = labels.children("input");
        if (disable) {
            labels.addClass("disabled").removeClass("active");
            inputs.prop("checked", false).prop("disabled", true);
        }else{
            labels.removeClass("disabled");
            inputs.prop("checked", true).prop("disabled", false);
        }
    }

    $("#field-product, #field-hotel").change(function(){
        var _this = $(this);
        if (_this.is("#field-product"))
            var labels = _this.nextAll("label").slice(0,2);
        else
            var labels = _this.nextAll("label").slice(1,4);
        toggle(labels, !_this.children("input").prop("checked"));
    });

    $("#field-flight,#field-view").change(function(){
        var _this = $(this);
        var labels = $("#field-people");
        toggle(labels, !_this.children("input").prop("checked"));
    });

    $(".ui-datepicker").zdatepicker({viewmonths:1, disable:{0:['<?php echo date('Y-m-d', strtotime('+2 day')); ?>', '9999-01-01']}});

    $(".ui-select").chosen({width:'100%', disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});

    var count = function(){
        var formdata = $("#form").serialize() + "&count=1";
        $.post("<?php echo BASE_URL; ?>order.php?method=export", formdata, function(data){
            if (data.s == 0){
                $("#count").text(data.rs);
            }else{
                var del = $("<del> NULL </del>").attr("title", data.err);
                $("#count").html(del);
            }
        }, "json");
    };

    $("#form .condition input:text").blur(count);
    $("#form .condition input:checkbox, #form .condition input:radio").change(count);

    $("#export").click(function(){
        $("#form").submit();
    });
});
</script>


</body>
</html>
