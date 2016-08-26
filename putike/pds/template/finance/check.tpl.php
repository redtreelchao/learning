<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 财务操作</title>

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

            <h1 class="page-header">线下支付核对</h1>

            <!-- page and operation -->
            <div class="row">
                <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">

                    <!--- filter -->
                    <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                        <!--<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            操作 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:add();">添加</a></li>
                            <li><a href="">移至回收站</a></li>
                        </ul>-->

                        <select class="form-control ui-select" name="status" id="paystatus" data-method="<?php echo $method;?>" data-status = "<?php echo $status;?>">
                            <option value="-99" <?php if(!in_array($status, [0,1])){echo 'selected';}?>>全部支付订单</option>
                            <option value="1" <?php if($status==1){echo 'selected';}?>>已核实</option>
                            <option value="0" <?php if($status==0){echo 'selected';}?>>待核实</option>
                        </select>
                    </div>
                    <!-- end filter -->


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
                <table id="package-list" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <!--<th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>-->
                        <th width="150">序号</th>
                        <th>客户姓名</th>
                        <th>行程名称</th>
                        <th>价格</th>
                        <th>联系方式</th>
                        <th>付款方式</th>
                        <th>审核时间</th>
                        <th width="150">操作</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <!--<th><input type="checkbox" class="checkbox checked-all" value="" /></th>-->
                        <th width="150">序号</th>
                        <th>客户姓名</th>
                        <th>行程名称</th>
                        <th>价格</th>
                        <th>联系方式</th>
                        <th>付款方式</th>
                        <th>审核时间</th>
                        <th width="150">操作</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php if(count($list)<=0){ ?>
                        <tr>
                            <td colspan="8">没有记录</td>
                        </tr>
                    <?php }else{foreach($list as $k => $v){ ?>
                        <tr id="row-<?php echo $v['id']; ?>">
                            <!--<td><input type="checkbox" class="checkbox" value="<?php /*echo $v['id']; */?>" /></td>-->
                            <td><?php echo $v['order'],'-',$v['id']; ?></td>
                            <td><?php echo $v['contact']; ?></td>
                            <td><?php echo $v['title']; ?></td>
                            <td>￥<?php echo number_format($v['price'], 2, '.', ''); ?></td>
                            <td><?php echo $v['tel']; ?></td>
                            <td><?php echo $paytype[$v['paytype']]; ?></td>
                            <td id="paytime_<?php echo $v['id']; ?>"><?php if(in_array($v['status'], [-1,1]) ){ echo $v['paytime'] ? date('Y-m-d H:i:s', $v['paytime']) : '';} ?></td>
                            <td>
                                <?php if($v['deposit'] == -1){?>
                                    <span class="txt-default">尾款</span>
                            <?php }else { ?>
                                    <?php if ($v['status'] == 0) { ?>
                                        <?php if ($v['paytype'] == 'online') { ?>
                                            <span class="txt-info">待付款</span>
                                        <?php } else { ?>
                                            <a data-id="<?php echo $v['id']; ?>"
                                               id="confirm_pay_btn_<?php echo $v['id']; ?>"
                                               data-showpaytype="<?php echo $paytype[$v['paytype']]; ?>"
                                               class="btn btn-sm btn-info confirm_pay"><span
                                                    class="glyphicon glyphicon-ok hidden-md"></span><span
                                                    class="hidden-xs hidden-sm">&nbsp;确认到账</span></a>
                                        <?php } ?>
                                        <span class="txt-danger" id="modify_<?php echo $v['id']; ?>"
                                              style="display: none;">已核实</span>
                                    <?php } elseif ($v['status'] == -1) { ?>
                                        <span class="txt-warning">已退款</span>
                                    <?php } else { ?>
                                        <span class="txt-danger">已核实</span>
                                    <?php }
                                }?>
                            </td>
                        </tr>
                    <?php } }?>
                    </tbody>
                </table>
            </div>


            <!-- page and operation -->
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                    <!--- filter -->
                    <div class="btn-group" style="margin:20px 0px;">
                        <!--<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            操作 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:add();">添加</a></li>
                            <li><a href="">移至回收站</a></li>
                        </ul>-->
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
<div id="form-package" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">线下支付核对</h4>
            </div>
            <form class="modal-body form-horizontal modalshow">

                <div class="form-group">
                    <label class="col-sm-2 control-label">支付方式</label>
                    <div class="col-sm-8" id="show_paytype">
                        --
                    </div>


                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">流水号</label>
                    <div class="col-sm-8" >
                        <input type="text" id="paytrade" name="paytrade" class="form-control" value="" required />
                        <input type="hidden" id="current_id" value="" />
                    </div>

                </div>



            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" data-loading-text="保存中.." id="myButton">保存</button>
            </div>
        </div>
    </div>
</div>

<link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />

<script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
<script>
    $(".ui-select").chosen({width:'100%', disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});






    $(function(){
        $("#paystatus").change(function(){
            method = $(this).data('method');
            status = $(this).val();
            url = window.location.origin+window.location.pathname+'?method='+method+'&status='+status;
            window.location.href =url;
        });

        $(".confirm_pay").on('click',function(){

            id = $(this).data('id');
            $("#current_id").val(id);
            $("#paytrade").val('');

            $("#myButton").attr('disabled',false);
            $("#myButton").text('保存');

            showpaytype = $(this).data('showpaytype');
            $("#form-package .modal-title").text("线下支付确认");
            $("#form-package #show_paytype").text(showpaytype);

            $("#form-package").modal("show");

        });

        $("#myButton").on('click', function(){
            id = $("#current_id").val();
            paytrade = $("#paytrade").val().trim();
            if(paytrade.length <=0){
                alert('支付流水号不能为空', 'error',null,'.modalshow');
                return false;
            }
            //console.log(paytrade);
            $.ajax({
                url: "<?php echo BASE_URL; ?>finance.php?method=check",
                dataType:'json',
                type:'post',
                data:{id:id,paytrade:paytrade},
                beforeSend:function(){
                    $("#myButton").attr('disabled',true);
                    $("#myButton").text('保存中...');
                },
                //context: document.body,
                success: function(e){
                    if(e.s ==0){
                        //console.log(e);
                        $("#modify_"+id).show();
                        $("#confirm_pay_btn_"+id).remove();
                        $("#paytime_"+id).text(e.rs);
                        $("#myButton").attr('disabled',false);
                        $("#myButton").text('保存');
                        $("#form-package").modal("hide");

                    }else{
                        alert(e.err, 'error',null,'.modalshow');
                        $("#myButton").attr('disabled',false);
                        $("#myButton").text('保存');
                    }

                },
                complete:function(){
                    //$("#modify_"+id).show();
                    //$(this).remove();
                    //$("#myButton").attr('disabled',false);
                    //$("#myButton").text('保存');

                }
            });


            /*

             */

        });


    });

    function confirm_pay(id,show_paytype){



    }
</script>
</body>
</html>