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

                <h1 class="page-header">订单管理</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php if(!empty(array_filter($keywords))){ ?>
                                <li><a href="<?php echo BASE_URL; ?>order.php">返回所有订单</a></li>
                                <?php } ?>
                                <?php if (isset($_SESSION['_order_view']) && $_SESSION['_order_view'] == 'pay') { ?>
                                <li><a href="<?php echo BASE_URL; ?>order.php?view=all">显示未付款</a></li>
                                <?php } else { ?>
                                <li><a href="<?php echo BASE_URL; ?>order.php?view=pay">仅显示已付款</a></li>
                                <?php } ?>
                                <li><a href="#search" data-toggle="modal" data-target="#search">高级检索</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="text" name="keyword" class="form-control" value="<?php if(isset($keyword)) echo $keyword; ?>" />
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

                <div id="order-list" class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>产品/酒店</th>
                                <th>券/房型</th>
                                <th>住离店</th>
                                <th>数量</th>
                                <th>小计</th>
                                <th width="15%">状态</th>
                                <th width="8%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>产品/酒店</th>
                                <th>券/房型</th>
                                <th>住离店</th>
                                <th>数量</th>
                                <th>小计</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            $tr = dirname(__FILE__).'/_tr.tpl.php';
                            include $tr;
                            ?>
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
                                <?php if(!empty(array_filter($keywords))){ ?>
                                <li><a href="<?php echo BASE_URL; ?>order.php">返回所有订单</a></li>
                                <?php } ?>
                                <?php if (isset($_SESSION['_order_view']) && $_SESSION['_order_view'] == 'pay') { ?>
                                <li><a href="<?php echo BASE_URL; ?>order.php?view=all">显示未付款</a></li>
                                <?php } else { ?>
                                <li><a href="<?php echo BASE_URL; ?>order.php?view=pay">仅显示已付款</a></li>
                                <?php } ?>
                                <li><a href="#search" data-toggle="modal" data-target="#search">高级检索</a></li>
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


    <!-- modal -->
    <div id="search" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">高级搜索</h4>
                </div>
                <div class="modal-body">
                    <form role="form" action="<?php echo BASE_URL; ?>order.php" method="GET" style="padding:10px 15px 0px">

                        <div id="search-timetype" class="form-group row">
                            <div class="btn-group col-sm-12" data-toggle="buttons">
                                <label class="btn btn-default <?php if (!$keywords['time'] || $keywords['time'] == 'all') echo "active"; ?>">
                                    <input type="radio" name="time" value="all" autocomplete="off" <?php if (!$keywords['time'] || $keywords['time'] == 'all') echo "checked"; ?> /> 所有时间
                                </label>
                                <label class="btn btn-default <?php if ($keywords['time'] == 'booking') echo "active"; ?>">
                                    <input type="radio" name="time" value="booking" autocomplete="off" <?php if ($keywords['time'] == 'booking') echo "checked"; ?> /> 预订时间
                                </label>
                                <label class="btn btn-default <?php if ($keywords['time'] == 'checkin') echo "active"; ?>">
                                    <input type="radio" name="time" value="checkin" autocomplete="off" <?php if ($keywords['time'] == 'checkin') echo "checked"; ?> /> 入住时间
                                </label>
                            </div>
                        </div>

                        <div id="search-time" class="form-group row" style="<?php if (!$keywords['time'] || $keywords['time'] == 'all') echo 'display:none;'; ?>">
                            <div class="col-sm-6">
                                <label>开始日期</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="start" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $keywords['start'] ? date('Y-m-d', $keywords['start']) : date('Y-m-d'); ?>" />
                                </div>
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>结束日期 (不包括)</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="end" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $keywords['end'] ? date('Y-m-d', $keywords['end']) : date('Y-m-d', strtotime('tomorrow')); ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>订单号</label>
                                <input type="text" class="form-control" name="order" value="<?php echo $keywords['order']; ?>" />
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>酒店/产品</label>
                                <input type="text" class="form-control" name="name" value="<?php echo $keywords['name']; ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>手机号</label>
                                <input type="text" class="form-control" name="tel" value="<?php echo $keywords['tel']; ?>" />
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>联系人/入住人</label>
                                <input type="text" class="form-control" name="people" value="<?php echo $keywords['people']; ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>分销渠道</label>
                                <select class="form-control ui-select" name="from">
                                    <option value="">请选择..</option>
                                    <?php foreach($orgs as $v){ ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($keywords['from'] == $v['id']) echo 'selected'; ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>供应商</label>
                                <select class="form-control ui-select" name="supply">
                                    <option value="">请选择..</option>
                                    <?php foreach($supplies as $k => $v){ ?>
                                    <option value="<?php echo $k; ?>" <?php if($keywords['supply'] == $k) echo 'selected'; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>订单状态</label>
                                <select class="form-control ui-select" name="status">
                                    <option value="">请选择..</option>
                                    <?php foreach($status as $k => $v){ ?>
                                    <option value="<?php echo $k; ?>" <?php if($keywords['status'] == $k) echo 'selected'; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>操作员</label>
                                <select class="form-control ui-select" name="operator">
                                    <option value="">请选择..</option>
                                    <?php foreach($operators as $k => $v){ ?>
                                    <option value="<?php echo $k; ?>" <?php if($keywords['operator'] == $k) echo 'selected'; ?>><?php echo $v; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>发票</label>
                                <select class="form-control ui-select" name="invoice">
                                    <option value="">请选择..</option>
                                    <option value="1" <?php if($keywords['invoice'] == 1) echo 'selected'; ?>>已发</option>
                                    <option value="-1" <?php if($keywords['invoice'] == -1) echo 'selected'; ?>>未发</option>
                                </select>
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>渠道款项</label>
                                <select class="form-control ui-select" name="clear">
                                    <option value="">请选择..</option>
                                    <option value="1" <?php if($keywords['clear'] == 1) echo 'selected'; ?>>已结算</option>
                                    <option value="-1" <?php if($keywords['clear'] == -1) echo 'selected'; ?>>未结算</option>
                                </select>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="$('#search form').get(0).reset();">清除</button>
                    <button type="button" class="btn btn-primary" onclick="$('#search form').submit();">搜索</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->


    <!-- important -->
    <div id="order-remind">
        <?php
        foreach($important as $v) {
            if(is_numeric($v['status'])) {
        ?>
        <a id="status-<?php echo $v['status']; ?>" href="<?php echo BASE_URL; ?>order.php?status=<?php echo $v['status']; ?>"><?php echo $status[$v['status']]; ?><b><?php echo $v['count']; ?></b></a>
        <?php
            } else if ($v['status'] == 'invoice') {
        ?>
        <a id="status-<?php echo $v['status']; ?>" href="<?php echo BASE_URL; ?>order.php?invoice=-1">待寄送发票<b><?php echo $v['count']; ?></b></a>
        <?php
            } else if ($v['status'] == 'clear') {
        ?>
        <a id="status-<?php echo $v['status']; ?>" href="<?php echo BASE_URL; ?>order.php?clear=-1">待结算<b><?php echo $v['count']; ?></b></a>
        <?php
        }}
        ?>
    </div>



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>


<script>
function unlock(a, order, item){
    var _this = $(a);
    _this.children(".fa").removeClass("fa fa-unlock-alt").addClass("glyphicon glyphicon-refresh glyphicon-loading");
    $.get("<?php echo BASE_URL; ?>order.php", {method:"unlock", order:order, item:item, _t:new Date().getTime()}, function(data){
        if (data.s == 0) {
            if (_this.nextAll("a").length == 0)
                _this.after('<a target="" href="<?php echo BASE_URL; ?>order.php?order='+order+'&pr='+item+'&method=operate" class="btn btn-sm btn-primary"><span class="fa fa-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 操作</span></a> ');
            _this.remove();
        } else {
            alert(data.err, "error");
        }
    }, "json");
}

var doctitle = document.title;

function order_message(time) {
    $.get('<?php echo BASE_URL; ?>order.php', {'method':'remind', 'time':time}, function(data) {
        if(data.s == 0){

            if (time != data.rs.time) $("#order-list .important").removeClass("important");

            var time = data.rs.time;

            var reminds = {'4':0, '10':0, '13':0, '15':0, 'invoice':0};
            for(x in data.rs.update) {
                var d = data.rs.update[x];
                if (x == 0){
                    $("body").append('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" title="" height="0" width="0"><param name="movie" value="<?php echo BASE_URL; ?>template/media/sms.swf"><param name="quality" value="BEST"><embed src="<?php echo BASE_URL; ?>template/media/sms.swf" quality="BEST" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" menu="false" height="0" width="0"></object>');
                    startFlash(1);
                }
                var s = d.status;
                var oid = d.order;
                var order_abbr = $("#order-"+oid+" td:eq(2) abbr");
                switch(s){
                    case "4": var status = '<?php echo $status[4]; ?>';   order_abbr.css({"color":"#c9302c"}).text(status); reminds['4']++; break;
                    case "10": var status = '<?php echo $status[10]; ?>'; order_abbr.css({"color":"#c9302c"}).text(status); reminds['10']++; break;
                    case "13": var status = '<?php echo $status[13]; ?>'; order_abbr.css({"color":"#c9302c"}).text(status); reminds['13']++; break;
                    case "15": var status = '<?php echo $status[15]; ?>'; order_abbr.css({"color":"#c9302c"}).text(status); reminds['15']++; break;
                    case "invoice": reminds['invoice']++; break;
                }
            }

            for(s in reminds) {
                if (!reminds[s]) continue;
                var num = parseInt($("#status-"+s+" b").text(), 10) + reminds[s];
                $("#status-"+s+" b").text(num).addClass("change").delay(1000).show(1, function(){ $(this).removeClass("change"); });
            }

            if(data.rs.html){
                $("#order-list tbody").prepend(data.rs.html);
            }

            setTimeout("order_message('"+time+"')", 2000);
        }else{
            setTimeout("order_message('"+time+"')", 2000);
        }
    }, 'json');
}

//Flash Title
function startFlash(on) {
    if(on == 40) {
        document.title = doctitle;
        return;
    }
    if(on % 2 == 1) {
        document.title = "【新订单】 "+doctitle;
    }else{
        document.title = "【　　　】 "+doctitle;
    }
    setTimeout('startFlash('+(on+1)+')', 1000);
}

$(function(){

    $(".btn-unlock").tooltip({placement:"left"});

    $("#search-timetype .btn").click(function(){
        var val = $(this).children("input").val();
        if (val == 'all') $("#search-time").slideUp(100);
        else $("#search-time").slideDown(100);
    });

    $(window).scroll(function(){
        if($(this).scrollTop() > 80)
            $("#order-remind").addClass("show");
        else
            $("#order-remind").removeClass("show");
    });

    $(".ui-datepicker").zdatepicker({viewmonths:1});

    $(".ui-select").chosen({width:'100%', disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});

    order_message(<?php echo NOW; ?>);
});
</script>


</body>
</html>
