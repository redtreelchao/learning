<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 查看产品信息</title>

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

            <h1 class="page-header">查看产品</h1>

            <!-- form -->
            <form class="row" id="form" role="form">

                <div class="col-md-7 col-lg-8 order-paper">
                    <div class="paper">
                        <div class="from">基本信息</div>
                        <div class="info"><b>产品名称：</b> <?php echo htmlspecialchars($data['name']); ?></div>
                        <div class="info"><b>产品类型：</b> <?php foreach(producttypes() as $v){  if($data['type'] == $v['code'])  {echo $v['name'];}} ?></option></div>
                        <div class="info"><b>支付类型：</b> <?php if($data['payment'] == 'ticket') echo '券类产品'; ?><?php if($data['payment'] == 'prepay') echo '预付产品（Ebooking）'; ?></div>
                        <div class="info"><b>主题类型：</b> &nbsp;</div>
                        <div class="info"><b>售卖渠道：</b>
                                <?php
                                if(in_array('1', $data['org'])) echo 'putike&nbsp;&nbsp;';
                                if(in_array('2', $data['org'])) echo 'feekr&nbsp;&nbsp;';
                                if(in_array('3', $data['org'])) echo '浙江旅游&nbsp;&nbsp;';
                                if(in_array('4', $data['org'])) echo '美宿&nbsp;&nbsp;';
                                 ?>
                        </div>
                        <div class="info"><b>产品经理：</b> <span id="bd">&nbsp;<?php foreach ($data['bdname'] as $k => $v){ echo "<span>{$v['username']}</span>&nbsp;&nbsp;";}?></span></div>
                        <div class="info"><b>产品助理：</b> <span id="ba">&nbsp;<?php foreach ($data['baname'] as $k => $v){ echo "<span>{$v['username']}</span>&nbsp;&nbsp;";}?></span></div>
                        <div class="info"><b>上架时间：</b> <?php echo $data['start'] ? date('Y-m-d', $data['start']) : ''; ?></div>
                        <div class="info"><b>下架时间：</b> <?php echo $data['end'] ? date('Y-m-d', $data['end']) : ''; ?></div>




                        <!-- product list -->
                        <div class="pro-list">
                            <div class="title"><strong>产品介绍</strong></div>

                            <div class="info" style="line-height:20px; padding:0px 20px;">
                                <?php echo nl2br($data['intro']);?>
                            </div>
                        </div>

                        <div class="pro-list">
                            <div class="title"><strong>使用要求</strong></div>

                            <div class="info" style="line-height:20px; padding:0px 20px;">
                                <?php echo nl2br($data['rule']);?>
                            </div>
                        </div>

                        <div class="pro-list">
                            <div class="title"><strong>退改规则</strong></div>

                            <div class="info" style="line-height:20px; padding:0px 20px;">
                                <?php echo nl2br($data['refund']);?>
                            </div>
                        </div>




                        <hr />

                        <!-- rule -->
                        <div class="info">&copy; 2015 Putike | 保留所有权利</div>


                    </div>

                </div>

                <!-- Right Bar -->
                <div class="col-md-5 col-lg-4">

                    <!-- panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">信息</h3>
                        </div>
                        <div class="panel-body panel-sm">
                            <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $data ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>
                            <p><span class="glyphicon glyphicon-eye-open"></span> 状　态: <?php echo $data ? ($data['status'] > 0 ? '上架' : '下架') : '未发布'; ?></p>

                            <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
                        </div>

                    </div>
                    <!-- panel -->

                    <!-- panel -->
                    <?php if($data){ ?>
                        <!-- product items -->
                        <div class="panel panel-default -hidden-sm -hidden-xs">
                            <div class="panel-heading">
                                <h3 class="panel-title">产品包内容</h3>
                            </div>

                            <ul id="item-list" class="list-group" style="font-size:12px;">
                                <?php
                                //$btn = $data['payment'] == 'prepay' ? 'fa-calendar' : 'fa-calendar';
                                $btn = 'glyphicon-eye-open';
                                if($data['items']) {
                                    foreach($data['items'] as $item) {
                                        switch($item['objtype']) {
                                            case 'room':
                                                ?>
                                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-building-o"></span><?php echo $item['name']; ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="glyphicon <?php echo $btn; ?>"></span></button></li>
                                                <?php
                                                break;
                                            case 'flight':
                                                ?>
                                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-plane"></span>始发：<?php echo $item['name'], ($item['ext'] ? ' （往返）' : ''); ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="glyphicon <?php echo $btn; ?>"></span></button></li>
                                                <?php
                                                break;
                                            case 'auto':
                                                ?>
                                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-car"></span>出发：<?php echo $item['name']; ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="glyphicon <?php echo $btn; ?>"></span></button></li>
                                                <?php
                                                break;
                                            case 'goods':
                                                ?>
                                                <li id="item-<?php echo $item['id']; ?>" class="list-group-item"><div><span class="fa fa-gift"></span><?php echo $item['name']; ?></div><button type="button" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $data['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-default btn-xs"><span class="glyphicon <?php echo $btn; ?>"></span></button></li>
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
                    <!-- panel -->


                </div>

            </form>
            <!-- end form -->

        </div>

    </div>
    <!-- end main -->

</div>





<div id="item-form" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">查看组合内容</h4>
            </div>
            <div class="modal-body">
                <div style="color:#999; text-align:center;" id="loading"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span> 正在加载信息..</div>
                <div id="show" style="display: none;"></div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

<link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
<link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
<script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/nicedit/nicEdit.js"></script>

<script>
    function product_prieview(proid,channelname) {
        window.open("http://www.putike.cn/product_preview.php?proid="+proid+"&channelname="+channelname,"","height=667,width=375,left=200,top=100,status=no,toolbar=no,menubar=no,location=no");
    }


    $(function(){
        $('#item-list').on('click', 'button', function () {
            var _this = $(this);
            var id = _this.data("code");
            $("#item-form #show").hide();
            $("#item-form #loading").show();
            $("#item-form").modal("show");

            var form = $("#item-form .modal-body #show");
            var url = "<?php echo BASE_URL; ?>product.php?method=item&id="+id;

            if (form.data("url") != url) {
                form.html("<div style=\"color:#999; text-align:center;\"><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> 正在加载信息..</div>");
                form.data("url", url);
                form.load(url, function () {
                    var _tar = $("#item-form #show");
                    var showdata = new Array();
                    showdata.push({key:'券名',value:_tar.find('input[name=name]').val(), tar:'name'});
                    showdata.push({key:'供应商',value:_tar.find('input[name=supply]').next().find('input').val(), tar:'supply', supplyid:_tar.find('input[name=supply]').val()});

                    showdata.push({key:'关联酒店',value:_tar.find('input[name=hotel]').next().find('input').val(), tar:'hotel',hotelid:_tar.find('input[name=hotel]').val()});
                    showdata.push({key:'关联房型',value:_tar.find('select[name=room] option:selected').text(), tar:'room'});
                    showdata.push({key:'床型',value:_tar.find('select[name=bed] option:selected').text(), tar:'bed'});

                    showdata.push({key:'价格包含',value:_tar.find('input[name=ext]').val()+'晚', tar:'ext'});
                    showdata.push({key:'产品说明',value:_tar.find('textarea[name=intro]').val(), tar:'intro'});
                    showdata.push({key:'必填信息',value:_tar.find('select[name=userdata] option:selected').text(), tar:'userdata'});
                    showdata.push({key:'Booking Code',value:_tar.find('select[name=bookingcode] option:selected').text(), tar:'bookingcode'});
                    showdata.push({key:'提前预订',value:_tar.find('input[name=advance]').val()+'天', tar:'advance'});
                    showdata.push({key:'售卖日期',value:_tar.find('input[name=online]').val()+'~~'+_tar.find('input[name=offline]').val(), tar:'online'});
                    showdata.push({key:'预订时间',value:_tar.find('input[name=start]').val()+'~~'+_tar.find('input[name=end]').val(), tar:'start'});
                    showdata.push({key:'底价',value:_tar.find('input[name=price]').val()+'(RMB)', tar:'price'});
                    showdata.push({key:'售价',value:_tar.find('input[name=total]').val()+'(RMB)', tar:'total'});
                    showdata.push({key:'利率',value:_tar.find('input[name=total]').parent().parent().find('.help-block').html(), tar:'help-block'});
                    showdata.push({key:'库存',value:_tar.find('input[name=allot]').val(99), tar:'allot'});
                    showdata.push({key:'操作说明',value:_tar.find('textarea[name=remark]').val(), tar:'remark'});

                    var _html = '<div class="paper">';


                    console.log(showdata);



                    //console.log(showdata);
                    $(showdata).each(function (k, v) {

                        if(v.tar =='supply' && parseInt(v.supplyid.trim())>0) {
                            link_before = '<a href="http://pds.putike.cn/supply.php?method=edit&id='+v.supplyid + '" target="_blank">';
                            link_after = '</a>';
                        }else if(v.tar =='hotel' && parseInt(v.hotelid.trim())>0) {
                            link_before = '<a href="http://info.putike.cn/index.html#!/basicinfo/'+ v.hotelid + '" target="_blank">';
                            link_after = '</a>';
                        }else{
                            link_before = link_after = '';
                        }


                        _html = _html+'<div class="row"><div class="col-md-3 col-lg-3 col-xs-4 col-sm-4"> <b>'+ v.key +'：</b></div><div class="col-md-9 col-lg-9 col-xs-8 col-sm-8" >'+ link_before + v.value + link_after + '</div></div>';
                    });
                    _html = _html+'</div>';
                    //console.log(_html);
                    $("#item-form #show").html(_html);

                    $("#item-form #show").show();
                    $("#item-form #loading").hide();
                });
            }else{
                $("#item-form #show").show();
                $("#item-form #loading").hide();
            }

            $.get(url, function(data){
                if(data.s == 0){
                    //$('#row-'+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err);
                }
            }, "json");



        });






    });
</script>

<?php action::exec('order_manage_tpl_footer', $select, $order, $mode); ?>


</body>
</html>
