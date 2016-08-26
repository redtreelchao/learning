<?php
function getexpress($type)
{
    $arr = array(
        'youshuwuliu'   => '优速物流',
        'huitongkuaidi' => '汇通快递',
        'shunfeng'      => '顺丰',
        'zhaijisong'    => '宅急送',
        'youzhengguonei'=> '邮政快递',
        'ems'           => 'EMS',
        'tiantian'      => '天天快递',
        'yuantong'      => '圆通快递',
        'yunda'         => '韵达快递',
        'zhongtong'     => '中通快递',
        'shentong'      => '申通快递',
        'other'         => '其他快递',
        );

    return($arr[$type]);
}
?>
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

            <h1 class="page-header">发票管理</h1>

            <!-- page and operation -->
            <div class="row">
                <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">

                    <!--- filter -->
                    <div class="btn-group" style="margin:20px 0px; margin-right:10px;">

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
                        <th width="">编号</th>
                        <th>订单号</th>
                        <th>发票抬头</th>
                        <th>收件人</th>
                        <th>联系电话</th>
                        <th>收件地址</th>
                        <th>快递方式</th>
                        <th>快递单号</th>
                        <th>备注</th>
                        <th width="150">操作</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <!--<th><input type="checkbox" class="checkbox checked-all" value="" /></th>-->
                        <th width="">编号</th>
                        <th>订单号</th>
                        <th>发票抬头</th>
                        <th>收件人</th>
                        <th>联系电话</th>
                        <th>收件地址</th>

                        <th>快递方式</th>
                        <th>快递单号</th>
                        <th>备注</th>
                        <th width="150">操作</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php if(count($list)<=0){ ?>
                        <tr>
                            <td colspan="8">没有记录</td>
                        </tr>
                    <?php }else{foreach($list as $k => $v){ ?>
                        <tr id="row-<?php echo $v['orderid']; ?>">
                            <!--<td><input type="checkbox" class="checkbox" value="<?php /*echo $v['id']; */?>" /></td>-->
                            <td><?php echo $k+1; ?></td>
                            <td><?php echo $v['order']; ?></td>
                            <td><?php echo $v['payer']; ?></td>
                            <td><?php echo $v['receiver']; ?></td>
                            <td><?php echo $v['receivertel']; ?></td>
                            <td><?php echo $v['receiveraddr']; ?></td>
                            <td><?php echo empty($v['expresstype']) ? "未寄送" : getexpress($v['expresstype']); ?></td>
                            <td><?php echo empty($v['expresstype']) ? "暂无" : $v['expressno']; ?></td>
                            <td><?php echo $v['remark']; ?></td>
                            <td>
                                <?php if(is_null($v['expressno'])){?>
                                    <a data-orderid="<?php echo $v['orderid'];?>" id="invoice_btn_<?php echo $v['orderid'];?>" " class="btn btn-sm btn-info confirm_pay"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm">&nbsp;确认发件</span></a>
                                    <span class="txt-danger" id="modify_<?php echo $v['orderid'];?>" style="display: none;">已发出</span>
                                <?php }else{ ?>
                                    <span class="txt-danger">已发出</span>
                                <?php }?>
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
                <h4 class="modal-title">确认发件</h4>
            </div>
            <form class="modal-body form-horizontal modalshow">

                <div class="form-group">
                    <label class="col-sm-2 control-label">快递公司</label>
                    <div class="col-sm-8" id="">
                        <select class="form-control modal-ui-select" name="expresstype" id="expresstype" >
                            <option value="" >请选择快递公司</option>
                            <?php foreach ($expresstype as $k => $v){?>
                            <option value="<?php echo $k;?>" ><?php echo $v ?></option>
                            <?php  }?>
                        </select>
                    </div>


                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">快递单号</label>
                    <div class="col-sm-8" >
                        <input type="text" id="expressno" name="expressno" class="form-control" value="" required />
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

    $(".ui-select").chosen({width:'100%', disable_search_threshold:15, no_results_text:"未找到..", placeholder_text_single:"请选择.."});


    $(function(){


        $(".confirm_pay").on('click',function(){

            orderid = $(this).data('orderid');
            $("#current_id").val(orderid);
            $("#expressno").val('');
            $("#expressno").val('');

            $("#expresstype option:first").prop("selected", 'selected');
            $(".modal-ui-select").chosen({width:'100%', disable_search_threshold:15, no_results_text:"未找到..", placeholder_text_single:"请选择.."});

            $("#myButton").attr('disabled',false);
            $("#myButton").text('保存');

            $("#form-package .modal-title").text("确认发件");

            $("#form-package").modal("show");

        });


        $('#form-package').on('hidden.bs.modal', function (e) {
            $(".modal-ui-select").chosen('destroy');
        })

        $("#myButton").on('click', function(){
            orderid = $("#current_id").val();
            expresstype = $("#expresstype").val().trim();
            if(expresstype.length <=0){
                alert('请选择快递公司', 'error',null,'.modalshow');
                return false;
            }

            expressno = $("#expressno").val().trim();
            if(expressno.length <=0){
                alert('请输入快递单号', 'error',null,'.modalshow');
                return false;
            }
            //console.log(paytrade);
            $.ajax({
                url: "<?php echo BASE_URL; ?>finance.php?method=invoice",
                dataType:'json',
                type:'post',
                data:{orderid:orderid,expresstype:expresstype,expressno:expressno},
                beforeSend:function(){
                    $("#myButton").attr('disabled',true);
                    $("#myButton").text('保存中...');
                },
                //context: document.body,
                success: function(e){
                    if(e.s ==0){
                        location.reload();
                        /*//console.log(e);invoice_btn_
                        $("#modify_"+orderid).show();
                        $("#invoice_btn_"+orderid).remove();
                        $("#paytime_"+id).text(e.rs);
                        $("#myButton").attr('disabled',false);
                        $("#myButton").text('保存');
                        $("#form-package").modal("hide");*/

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
