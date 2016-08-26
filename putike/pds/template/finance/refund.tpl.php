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

            <h1 class="page-header">线下支付退款</h1>

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


            <div class="table-responsive">
                <table id="package-list" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="150">编号</th>
                        <th>客户姓名</th>
                        <th>行程名称</th>
                        <th>退款金额</th>
                        <th>退款渠道</th>
                        <th>收款人</th>
                        <th>退款帐号</th>
                        <th>开户行</th>
                        <th>退款时间</th>
                        <th>流水号</th>
                        <th>操作</th>
                        <th width="150">备注</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>

                        <th width="150">编号</th>
                        <th>客户姓名</th>
                        <th>行程名称</th>
                        <th>退款金额</th>
                        <th>退款渠道</th>
                        <th>收款人</th>
                        <th>退款帐号</th>
                        <th>开户行</th>
                        <th>退款时间</th>
                        <th>流水号</th>
                        <th>操作</th>
                        <th width="150">备注</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php if(count($list)<=0){ ?>
                        <tr>
                            <td colspan="10">没有记录</td>
                        </tr>
                    <?php }else{foreach($list as $k => $v){ ?>
                        <tr id="row-<?php echo $v['id']; ?>">
                            <!--<td><input type="checkbox" class="checkbox" value="<?php /*echo $v['id']; */?>" /></td>-->
                            <td><?php echo $v['order'],'-',$v['id']; ?></td>
                            <td><?php echo $v['contact']; ?></td>
                            <td><?php echo $v['title']; ?></td>
                            <td>￥<?php echo number_format($v['apply_total'], 2, '.', ''); ?></td>
                            <td><?php echo $v['refundtype']; ?></td>
                            <td>
                                <span id="apply_refund_name_<?php echo $v['id'];?>">
                                    <?php echo $v['apply_refund_name'];?>
                                </span>
                            </td>
                            <td>
                                <span id="refundaccount_<?php echo $v['id'];?>"><?php echo $v['refundaccount'];?>

                                </span>
                            </td>
                            <td><span id="refundbankaccount_<?php echo $v['id'];?>"><?php echo $v['refundbankaccount'];?></span></td>
                            <td id="refundtime_<?php echo $v['id']; ?>"><?php if($v['status'] == 1){ echo date('Y-m-d H:i:s', $v['refundtime']);} ?></td>
                            <td id="">
                                <span id="refundtrade_<?php echo $v['id'];?>">
                                    <?php echo $v['refundtrade'];?>
                                </span>
                            </td>
                            <td>
                                <?php if($v['status']==0){?>
                                    <a data-id="<?php echo $v['id'];?>"
                                       id="confirm_refund_btn_<?php echo $v['id'];?>"
                                       data-showrefundtype="<?php echo $paytype[$v['paytype']]; ?>"
                                       data-refundname="<?php echo $v['apply_refund_name'];?>"
                                       class="btn btn-sm btn-info confirm_refund">
                                        <span class="glyphicon glyphicon-ok hidden-md"></span>
                                        <span class="hidden-xs hidden-sm">&nbsp;确认退款</span>
                                    </a>
                                    <span class="txt-danger" id="modify_<?php echo $v['id'];?>" style="display: none;">已退款</span>
                                <?php }else { ?>
                                    <span class="txt-danger">已退款</span>
                                <?php }?>
                            </td>
                            <td><?php echo $v['apply_fefund_remark'];?></td>
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
                <h4 class="modal-title">支付退款</h4>
            </div>
            <form class="modal-body form-horizontal modalshow">

                <div class="form-group">
                    <label class="col-sm-2 control-label">退款渠道</label>
                    <div class="col-sm-8">
                        <input type="text" id="refundtype"  class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">收款人</label>
                    <div class="col-sm-8">
                        <input type="text" id="refundname"  class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">退款帐号</label>
                    <div class="col-sm-8">
                        <input type="text" id="refundaccount"  class="form-control"  required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">开户行</label>
                    <div class="col-sm-8">
                        <input type="text" id="refundbankaccount"  class="form-control"  required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">流水号</label>
                    <div class="col-sm-8" >
                        <input type="text" id="refundtrade" name="refundtrade" class="form-control" value="" required />
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

        $(".confirm_refund").on('click',function(){

            id = $(this).data('id');
            refundname = $(this).data('refundname');



            $("#myButton").attr('disabled',false);
            $("#myButton").text('保存');

            //showrefundtype = $(this).data('showrefundtype');
            $("#form-package .modal-title").text("线下支付退款");
            $("#form-package input").val('');
            $("#current_id").val(id);
            $("#refundname").val(refundname);

            $("#form-package").modal("show");

        });

        $("#myButton").on('click', function(){
            id = $("#current_id").val();
            refundtype = $("#refundtype").val().trim();
            refundname = $("#refundname").val().trim();
            refundaccount = $("#refundaccount").val().trim();
            refundbankaccount = $("#refundbankaccount").val().trim();
            refundtrade = $("#refundtrade").val().trim();
            if(refundtype.length <=0){
                alert('退款渠道不能为空！', 'error',null,'.modalshow');
                return false;
            }

            if(refundname.length <=0){
                alert('收款人不能为空！', 'error',null,'.modalshow');
                return false;
            }

            if(refundaccount.length <=0){
                alert('退款帐号不能为空！', 'error',null,'.modalshow');
                return false;
            }

            if(refundtrade.length <=0){
                alert('流水号不能为空！', 'error',null,'.modalshow');
                return false;
            }
            //console.log(paytrade);
            $.ajax({
                url: "<?php echo BASE_URL; ?>finance.php?method=refund",
                dataType:'json',
                type:'post',
                data:{
                    id:id,
                    refundtype:refundtype,//退款渠道
                    refundname:refundname,//收款人
                    refundaccount:refundaccount,//退款帐号
                    refundbankaccount:refundbankaccount,//开户行
                    refundtrade:refundtrade,//流水号

                },
                beforeSend:function(){
                    $("#myButton").attr('disabled',true);
                    $("#myButton").text('保存中...');
                },
                //context: document.body,
                success: function(e){
                    if(e.s ==0){
                        location.reload();

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

</script>
</body>
</html>
