<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 行程设计师</title>

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
    <script type="text/javascript" src=".<?php echo RESOURCES_URL; ?>js/plupload/plupload.full.min.js"></script>
    <style>
        table tr td > .btn-popover { padding: 1px 5px; }
        #remarklist { max-height:400px !important;  }
    </style>
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

                <h1 class="page-header">价格详情</h1>

                <div class="tourorder-price row">

                    <!-- tour row begin -->
                    <div class="col-md-12 col-lg-12 form-horizontal">


                        <div class="row">
                            <form class="col-md-4" name="form1" id="form1" >
                                <div class="well">

                                    <div class="well-title">
                                        <h3>行程总价</h3>
                                        <small><?php echo $data['contact']?>&emsp;/&emsp;<?php echo $data['title']?></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">底价</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="floor" placeholder="" value="<?php echo $data['floor'];?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">总报价</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="total" placeholder="" value="<?php echo $data['total']?>">
                                        </div>
                                    </div>
                                    <div class="form-group form-well-small">
                                        <label class="col-sm-3 control-label">出行人</label>
                                        <div class="col-sm-4">
                                            成人
                                            <input type="text" name="adults" class="form-control" placeholder="" value="<?php echo $data['adults']?>">
                                        </div>
                                        <div class="col-sm-4 col-sm-offset-1">
                                            儿童
                                            <input type="text" name="kids" class="form-control" placeholder="" value="<?php echo $data['kids']?>">
                                        </div>

                                    </div>
                                    <input type="hidden" name="id" value="<?php echo $data['id']?>"/>
                                    <input type="hidden" name="order" value="<?php echo $data['order']?>"/>
                                    <input type="button" class="btn btn-info btn-block" value="保存" id="save" <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>

                                </div>
                            </form>
                            <form class="col-md-4" id="form2">
                                <div class="well">
                                    <div class="well-title">
                                        <h3>支付状态（<?php echo get_status($data['status']);?>）</h3>
                                    </div>
                                    <div class="well-pay-info">
                                        <div class="pay-info">
                                            含<span><?php echo $pay['total']?></span>笔，已支付<span><?php echo $pay['paid']?></span>笔，待支付<span><?php echo $pay['no']?></span>笔
                                        </div>
                                        <div class="pay-info">
                                            已支付<span>￥<?php echo number_format($sum['paid'],2,'.','');?></span>，待支付<span class="red">￥<?php echo number_format($sum['no'],2,'.','');?></span>
                                        </div>
                                    </div>
                                    <input type="button" class="btn btn-info btn-block pay-step-add" value="增加分步支付" <?php echo ($steppay?'':'disabled') ?> <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?> >
                                </div>
                            </form>
                            <form class="col-md-4" id="form3">
                                <div class="well">
                                    <div class="well-title">
                                        <h3>操作备注</h3>
                                    </div>
                                    <div class="well-des">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="text" name="data" id="historydata" class="form-control" placeholder="请输入备注">
                                            </div>
                                        </div>
                                        <div class="clearfix">
                                            <input type="hidden" name="intro" value="修改了定制游价格备注" />
                                            <input type="hidden" name="type"  value="tour" />
                                            <input type="hidden" name="pk"    value="<?php echo $data['id']?>" />
                                            <a class="btn btn-default" href="<?php echo BASE_URL?>tourorder.php?method=edit&id=<?php echo $data['id'] ?> " >返回</a>
                                            <input type="button" class="btn btn-info pull-right" id="add3" value="提交" <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>
                                        </div>
                                        <!--2-->


                                        <!--2-->

                                        <!-- 备注 -->
                                        <div class="well-des-info" >
                                            <div class="panel-group" role="tablist" style="position: relative;z-index: 11;">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="collapseListGroupHeading1">
                                                        <h4 class="panel-title">


                                                            <a class="btn btn-default btn-block" role="button" data-toggle="collapse" href="#collapseListGroup1" aria-expanded="false" aria-controls="collapseListGroup1">
                                                                查看备注
                                                            </a>

                                                        </h4>
                                                    </div>
                                                    <div id="collapseListGroup1" class="panel-collapse collapse " style="overflow: auto" role="tabpanel" aria-labelledby="collapseListGroupHeading1" aria-expanded="false">
                                                        <ul class="list-group" id="remarklist">
                                                            <?php foreach ($history as $key => $value){

                                                                if(stripos((string)$value['data'],'{')===false){
                                                                ?>
                                                            <li class="list-group-item"><?php echo date('Y-m-d H:i:s',$value['time']),'<br>','['.$value['username'].']',$value['intro'],'<br>',$value['data']; ?></li>
                                                            <?php }}?>

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <h4>分步支付详情</h4>
                        <hr>

                        <div id="touroder-price" class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>支付项</th>
                                        <th>付款方式</th>
                                        <th>金额</th>
                                        <th>付款方</th>
                                        <!--<th>备注</th>         -->
                                        <th>状态</th>
                                        <th>操作&nbsp;&nbsp;<button type="button" data-toggle="modal" data-target="#refund" class="btn btn-sm btn-danger" <?php if($pay['paid']<=0){?>disabled<?php }?> <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>退款</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stepNum = 0;
                                    foreach ($paylist as $k => $v):
                                    ?>
                                    <tr>
                                        <td><?php echo $v['id']?></td>
                                        <td><?php

                                            if($v['deposit'] == 0){
                                                $stepNum++;
                                                if(empty($v['name'])){
                                                    echo "第{$stepNum}步";
                                                    //echo get_paytype($v['deposit']);
                                                }else{
                                                    echo $v['name'];
                                                }
                                            }else{
                                                if(empty($v['name'])){
                                                    echo get_paytype($v['deposit']);
                                                }else{
                                                    echo $v['name'];
                                                }
                                            }

                                            ?></td>
                                        <td><?php echo $paytype[$v['paytype']]?></td>
                                        <td>￥<?php echo $v['price']?></td>
                                        <td><?php echo $data['contact']?>(<?php echo $data['tel'];?>)</td>
                                        <!--<td><?php /*echo $v['remark'];*/?></td>-->
                                        <td>
                                            <?php if($v['deposit'] != -1){?>
                                            <button type="button" class="btn btn-sm btn-<?php if($v['status']==1) {echo 'info';}else{echo 'default';}?> btn-popover" data-toggle="popover"
                                                <?php if($v['status']==1){
                                                    if ($v['paytype'] == 'online') {
                                                        echo 'title="支付信息" data-content="流水号：' . explode(':', $v['paytrade'])[1].'"';
                                                    } else {
                                                        echo 'title="支付信息" data-content="流水号：' . $v['paytrade'].'"';
                                                    }
                                                    echo ' 支付时间：'.date('Y-m-d H:i:s',$v['paytime']);
                                                }else{
                                                    echo "disabled";
                                                } ?>
                                            >
                                                <?php if($v['status']==1) {?>
                                                    <span class="glyphicon glyphicon-ok hidden-md"></span>
                                                
                                                <?php }else{ ?>
                                                    <span class="glyphicon glyphicon-remove hidden-md"></span>
                                                <?php } ?>
                                                
                                                <span class="hidden-xs hidden-sm"><?php echo get_paystatus($v['status']);?></span>
                                            </button>
                                            <?php }?>
                                        </td>
                                        <td>
                                            <?php
                                            if($v['status']==1):
                                            ?>
                                            <!--<a href="javascript:void(0);" class="btn btn-sm btn-default refund" data-price="<?php /*echo $v['price']*/?>" data-id=<?php /*echo $v['id']*/?>>
                                                <span class="glyphicon glyphicon-repeat btn-repeat hidden-md"></span>
                                                <span class="hidden-xs hidden-sm">退款</span>
                                            </a>-->
                                            <?php
                                            endif;
                                            ?>
                                            <?php
                                                if($v['deposit']==1):
                                            ?>
                                            <a href="javascript:void(0);"  class="btn btn-sm btn-default remarkmodal" data-id=<?php echo $v['id'];?>>
                                                <span class="glyphicon glyphicon-book btn-pdf hidden-md"></span>
                                                <span class="hidden-xs hidden-sm">定金说明</span>
                                            </a>
                                            <?php
                                                endif;
                                            ?>

                                            <?php
                                            if ($v['deposit'] != -1) {

                                                if ($v['status'] == 0):
                                                    ?>
                                                    <a href="javascript:void(0);"
                                                       class="btn btn-sm btn-default btn-edit"
                                                       data-id=<?php echo $v['id'] ?> <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>
                                                        <span class="glyphicon glyphicon-edit hidden-md"></span>
                                                        <span class="hidden-xs hidden-sm">修改</span>
                                                    </a>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-default btn-del"
                                                       data-id=<?php echo $v['id'] ?> <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>
                                                        <span class="glyphicon glyphicon-trash hidden-md"></span>
                                                        <span class="hidden-xs hidden-sm">删除</span>
                                                    </a>
                                                    <?php
                                                endif;
                                            }
                                            ?>
                                            <?php
                                            if($v['paytype']=='offline'):
                                            ?>
                                             <button type="button" class="btn btn-sm btn-default btn-check" <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>
                                                <span class="glyphicon glyphicon-list-alt hidden-md"></span>
                                                <span class="hidden-xs hidden-sm">财务核账</span>
                                            </button>
                                            <?php
                                            endif;
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    endforeach;
                                    ?>


                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- tour row end -->

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="pay-step-add">
    <form id="paystepadd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">分步支付</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">类型</label>
                        <label class="col-sm-3 pay-type">
                            <input type="radio" name="deposit" class="type1" value="1">定金
                        </label>
                        <label class="col-sm-3 pay-type col-sm-offset-1">
                            <input type="radio" name="deposit" class="type2" checked value="0">其他类型
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付项</label>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control pay-form-type" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付金额</label>
                        <div class="col-sm-8">
                            <input type="text" name="price" class="form-control pay-form-num" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付方式</label>
                        <div class="col-sm-8">
                            <select name="paytype" id="" class="form-control pay-form-select">
                                <?php  foreach ($paytype as $k => $pt){ ?>
                                    <option value="<?php echo $k ;?>"><?php echo $pt;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" id="remaktxt" >定金说明</label>
                        <div class="col-sm-8">
                            <textarea name="remark" id="" cols="30" rows="10" class="form-control pay-form-earnest"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="orderid" value="<?php echo $data['id']?>"/>
                    <input type="hidden" name="order" value="<?php echo $data['order']?>"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary btn-step-add" id="savepaystep">保存</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
    </div><!-- /.modal -->

      <div class="modal fade" id="refundmodal">
        <form id="refunform">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">退款</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">退款金额</label>
                        <div class="col-sm-9">
                            <input type="text" name="reprice" id="reprice" class="form-control" readonly="" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">付款人</label>
                        <div class="col-sm-9">
                            <input type="text" name="contact"  class="form-control" readonly="" placeholder="" value="<?php echo $data['contact'];?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">账户</label>
                        <div class="col-sm-9">
                            <input type="text" name="account" class="form-control" placeholder="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">开户行</label>
                        <div class="col-sm-9">
                            <input type="text" name="bank" class="form-control"  >
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="payid" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary btn-refun-save">通知财务</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
    </div><!-- /.modal -->

    <div class="modal fade" id="pay-check">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">财务核账</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付渠道</label>
                        <div class="col-sm-9">
                            <input type="text" name="paytype" class="form-control" readonly="" placeholder="" value="12000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">付款方</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" readonly="" placeholder="" value="<?php echo $data['contact'];?>">
                        </div>
                    </div>
                    <div class="form-group  show">
                        <label class="col-sm-3 control-label">备注</label>
                        <div class="col-sm-9">
                            <textarea name="remark" id="" cols="30" rows="10" class="form-control" readonly="readonly"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary btn-check-send">确认核实</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="pay-step-edit">
        <form id="payeditform">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">修改支付信息</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">类型</label>
                        <label class="col-sm-3 pay-type">
                            <input type="radio" name="deposit" class="type1" value="1">定金
                        </label>
                        <label class="col-sm-3 pay-type col-sm-offset-1">
                            <input type="radio" name="deposit" class="type2" value="0">其他类型
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付项</label>
                        <div class="col-sm-8">
                            <input type="text" id="edit_pay_name" name="name" class="form-control" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付金额</label>
                        <div class="col-sm-8">
                            <input type="text" id="edit_pay_price" name="price" class="form-control" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">支付方式</label>
                        <div class="col-sm-8">
                            <select id="edit_pay_paytype" name="paytype"  class="form-control">
                                <?php  foreach ($paytype as $k => $pt){ ?>
                                    <option value="<?php echo $k ;?>"><?php echo $pt;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">定金说明</label>
                        <div class="col-sm-8">
                            <textarea name="remark" id="edit_pay_remark" cols="30" rows="10" class="form-control" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input name="id" type="hidden" id="edit_pay_id"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary btn-pay-edit">保存</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
    </div><!-- /.modal -->

        <div class="modal fade" id="remakmodal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body form-horizontal">




                    <div class="form-group">
                        <label class="col-sm-3 control-label">定金说明</label>
                        <div class="col-sm-9" id="remarktxt">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <!-- Button trigger modal -->
    

    <!-- Modal -->
    <div class="modal fade" id="refund" tabindex="-1" role="dialog" aria-labelledby="refundLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="refundLabel">退款</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">退款金额</label>
                        <div class="col-sm-8">
                            <input class="paidtotal" type="hidden" id="paidtotal" value="<?php echo $sum['paid'];?>"></input>
                            <input id="refundorderid" type="hidden" value="<?php echo $id;?>"> </input>
                            <input type="text" id="refundmoney" name="refundmoney" class="form-control" placeholder="" value="<?php echo $sum['paid'];?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">付款人</label>
                        <div class="col-sm-8">
                            <input type="text" id="refundname" name="refundname" class="form-control" placeholder="" value="<?php echo $data['contact']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">备注</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="refundremark" id="refundremark" placeholder="请填写收款人 、退款账号、退款渠道、开户行等有效信息" cols="30" rows="5"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="refundorder">通知财务</button>
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
$(".chosen-select").chosen();


    $("#save").click(function(){

        var data = $('#form1').serialize();
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=modify_price", data, function(data){

            if (data.s == 0){
               alert('操作成功','success');
               window.location.reload()
            } else {
               alert(data.err);
            }
        }, "json");
    });

    $("#savepaystep").click(function(){

        var data = $('#paystepadd').serialize();
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=pay_save", data, function(data){

            if (data.s == 0){
               alert('操作成功','success');
               window.location.reload()
            } else {
                //$('#pay-step-edit').modal('hide');
               //alert(data.err,'info',null,'#pay-step-edit');
                alert(data.err);
            }
        }, "json");
    });

    $("#add3").on('click',function (){
        var data = $('#form3').serialize();
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=save_history", data, function(rs){

            if (rs.s == 0){
                html = '<li class="list-group-item">'+
                        rs.rs.time+'<br>'+'['+rs.rs.username+']'+rs.rs.intro+'<br>'+rs.rs.data+
                        '</li>';
                console.log(html);
                $("#remarklist").before(html);


               alert('保存成功','success');

            } else {
               alert(rs.err);
            }
        }, "json");
    });



    $(function(){

        $('[data-toggle="popover"]').popover()

        $('.pay-step-add').on('click',function(){
            $('#pay-step-add').modal('show');
        })
        $('#pay-step-add .pay-type').on('click',function(){
            if($('.type1').prop('checked')){

                $('#remaktxt').text('定金说明') ;
            }else {
                $('#remaktxt').text('备注') ;
            }
        })

        // 删除
        $('.btn-del').on('click',function(){
            var _this = $(this),
                id =  $(this).data('id');
             $.get("<?php echo BASE_URL; ?>tourorder.php?method=pay_remove", {id:id}, function(rs){

            if (rs.s == 0){
               _this.closest('tr').fadeOut(function(){
                   $(this).remove();
                   location.reload();
                });
            } else {
               alert(rs.err);
            }
        }, "json");

        });


        $('.btn-edit').on('click',function(){
            var _this = $(this);
            var id    = _this.data('id');
            $.get('<?php echo BASE_URL; ?>tourorder.php?method=pay_edit',{id:id},function(rs){
                if(rs.s==0)
                {
                    $('#pay-step-edit').modal('show');
                    $('#edit_pay_name').val(rs.rs.name);
                    $('#edit_pay_price').val(rs.rs.price);
                    $('#edit_pay_id').val(id);
                    $('#edit_pay_paytype').val(rs.rs.paytype);
                    $('#edit_pay_remark').val(rs.rs.remark);
                    if(rs.rs.deposit==1){
                        $('.pay-type .type1').prop('checked',true);
                        $('.pay-type .type2').prop('checked',false);
                    }else {
                        $('.pay-type .type1').prop('checked',false);
                        $('.pay-type .type2').prop('checked',true);
                    }

                }
            });

        })

        // 添加的数据
        var _num = '1';

        $('.btn-step-add').on('click',function(){
            var _payAdd = $('#pay-step-add'),
            _payType = _payAdd.find('.pay-form-type').val(),
            _payNum = _payAdd.find('.pay-form-num').val(),
            _payDes = _payAdd.find('.pay-form-des').val(),
            _payEar = _payAdd.find('.pay-form-earnest').val(),
            _payOpt = _payAdd.find('.pay-form-select').find("option:selected").text();
            _num ++;

            $('#pay-step-add').modal('hide');
            if($('.type1').prop('checked')) {
                addtr(_num,_payType,_payOpt,_payNum,_payDes);
            }else {

            }
        })

        $('.btn-check').on('click',function(){
            $('#pay-check').modal('show');
        })

    })

    function addtr(num,type,paytype,paynum,des) {return true;
        $('#touroder-price .table').append('<tr>'+
                                        '<td>'+num+'</td>'+
                                        '<td>'+type+'</td>'+
                                        '<td>'+paytype+'</td>'+
                                        '<td>'+paynum+'</td>'+
                                        '<td>林志玲</td>'+
                                        '<td>'+des+'</td>'+
                                        '<td>'+
                                            '<button type="button" class="btn btn-sm btn-warning btn-popover">'+
                                                '<span class="glyphicon glyphicon-remove hidden-md"></span>'+
                                                '<span class="hidden-xs hidden-sm">未支付</span>'+
                                            '</button>'+
                                        '</td>'+
                                        '<td>'+
                                            '<button type="button" class="btn btn-sm btn-default btn-check">'+
                                                '<span class="glyphicon glyphicon-list-alt hidden-md"></span>'+' '+
                                                '<span class="hidden-xs hidden-sm">财务核账</span>'+
                                            '</button>'+' '+
                                            '<a href="" class="btn btn-sm btn-default">'+
                                                '<span class="glyphicon glyphicon-edit hidden-md"></span>'+' '+
                                                '<span class="hidden-xs hidden-sm">修改</span>'+
                                            '</a>'+' '+
                                            '<a href="" class="btn btn-sm btn-default">'+
                                                '<span class="glyphicon glyphicon-trash hidden-md"></span>'+' '+
                                                '<span class="hidden-xs hidden-sm">删除</span>'+
                                            '</a>'+' '+
                                        '</td>'+
                                    '</tr>')
    }
</script>
<script type="text/javascript">
// Custom example logic

var uploader = new plupload.Uploader({
    runtimes : 'html5,flash,silverlight,html4',
    browse_button : 'pickfiles', // you can pass an id...
    container: document.getElementById('container'), // ... or DOM Element itself
    url : 'plupload.php',
    flash_swf_url : '../js/Moxie.swf',
    silverlight_xap_url : '../js/Moxie.xap',

    filters : {
        max_file_size : '10mb',
        mime_types: [
            {title : "Image files", extensions : "jpg,gif,png"},
            {title : "Zip files", extensions : "zip"}
        ]
    },

    init: {
        PostInit: function() {


            document.getElementById('uploadfiles').onclick = function() {
                uploader.start();
                return false;
            };
        },

        FilesAdded: function(up, files) {
            plupload.each(files, function(file) {
                //document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
            });
        },

        UploadProgress: function(up, file) {
           // document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
        },

        Error: function(up, err) {
            //document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
        },

        FileUploaded: function(up, file, res) {
                      var data = $.parseJSON(res.response);
                      console.log(data);
                      console.log(data.file);
                      if(!data.s){
                          var rs = data.rs;
                          $('.img-info').append('<label class="check-img">'+
                                                '<img src="'+data.file+'" width="65" height="65">'+
                                                '<input type="hidden" name="pics[]" value="'+data.file+'">'+
                                                '<input type="checkbox">'+
                                                '<span class="glyphicon"></span>'+
                                                '</label>');
                          // $("#upload1").before(jQuery("<input type='hidden' name='cover' value='' />").val(data.rs));
                          // $("#upload1").popover('destroy').popover({content:"<img width='100' height='100' src='"+data.rs+"' />", html:true, trigger:"hover"}).text("已上传").addClass("btn-primary");
                          up.refresh();
                      }else{
                          // $("#upload1").text("上传失败，请重试").prevAll("input").remove();
                      }
        }
    }
});

uploader.init();


$('.remarkmodal').on('click',function(){
    var id = $(this).data('id');
    $.get("<?php echo BASE_URL; ?>tourorder.php?method=get_remark", {id:id}, function(rs){

            if (rs.s == 0){
               $('#remakmodal').modal('show');
               $("#remarktxt").text(rs.rs.remark);
            } else {
               alert(rs.err);
            }
        }, "json");
});


$("#refundorder").on('click',function () {
    paidtotal = $("#paidtotal").val();
    refundmoney = $("#refundmoney").val();//申请退款金额
    refundname = $("#refundname").val();
    if(refundmoney > paidtotal){
        alert('退款金额不能大于付款金额','error');
        return false;
    }

    if(refundname.length<=0){
        alert('付款人不能为空','error');
        return false;
    }

    //$('#refund').modal('hide');
    console.log('AAA');
    $.post(
        "<?php echo BASE_URL; ?>tourorder.php?method=apply_refund",{
            'refundmoney':refundmoney,
            'refundname':refundname,
            'refundremark':$("#refundremark").val(),
            'refundorderid' :$("#refundorderid").val()
        },
        function(rs){
            if (rs.s == 0){
                $('#refund').modal('hide');
                alert('操作成功','success',function () {location.reload();});

                //location.reload();
            } else {
                $('#refund').modal('hide');
                alert(rs.err);
            }
    }, "json");

})

$('.refund').on('click',function(){
    var _this = $(this),
        id = _this.data('id'),
        price = _this.data('price');
    $.get("<?php echo BASE_URL; ?>tourorder.php?method=pay_refund", {id:id}, function(rs){

            if (rs.s == 0){
               $('#refundmodal').modal('show');
               $("#reprice").val(price);
               $("#payid").val(id);
            } else {
               alert(rs.err);
            }
        }, "json");
});

$('.btn-refun-save').on('click',function(){
    var _this = $(this),

       data   = $("#refunform").serializeArray();

    $.post("<?php echo BASE_URL; ?>tourorder.php?method=save_refund", data, function(rs){

            if (rs.s == 0){
               alert('操作成功','success');
               $('#refundmodal').modal('hide');
            } else {
               alert(rs.err);
            }
        }, "json");
});


$('.btn-pay-edit').on('click',function(){
    var _this = $(this),
       data   = $("#payeditform").serializeArray();

    $.post("<?php echo BASE_URL; ?>tourorder.php?method=pay_save", data, function(rs){

            if (rs.s == 0){        //return false;
               alert('操作成功' ,'success');
               $('#pay-step-edit').modal('hide');
                location.reload();
            } else {
                //$('#pay-step-edit').modal('hide');
                alert(rs.err,'info',null,'#pay-step-edit .modal-body');
               alert(rs.err);
            }
        }, "json");
});




</script>



</body>
</html>
