<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 订单管理 - <?php echo $order['order']; ?></title>

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

                <h1 class="page-header">查看订单</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-7 col-lg-8 order-paper">
                        <div class="paper">
                            <div class="from"><?php echo $order['org_name']; ?></div>
                            <div class="info"><b>订单号：</b> <?php echo $order['order']; ?></div>
                            <div class="info"><b>订购时间：</b> <?php echo date('Y-m-d H:i:s', $order['create']); ?></div>
                            <div class="info"><b>订单状态：</b> <?php echo $status[$order['status']]; ?></div>

                            <?php
                            switch ($order['status'])
                            {
                                case '1':
                                    echo '<div class="status status1"></div>';
                                    break;
                                case '2':
                                    echo '<div class="status status2"></div>';
                                    break;
                                case '3':
                                    echo '<div class="status status3"></div>';
                                    break;
                                case '4':
                                    echo '<div class="status status1"></div>';
                                    echo '<div class="status status3"></div>';
                                    break;
                                case '5':
                                    echo '<div class="status status5"></div>';
                                    break;
                                case '6':
                                    echo '<div class="status status3"></div>';
                                    echo '<div class="status status6"></div>';
                                    break;
                                case '7':
                                    echo '<div class="status status2"></div>';
                                    echo '<div class="status status7"></div>';
                                    break;
                                case '8':
                                    echo '<div class="status status3"></div>';
                                    echo '<div class="status status8"></div>';
                                    break;
                                case '9':
                                    echo '<div class="status status3"></div>';
                                    echo '<div class="status status8"></div>';
                                    break;
                            }
                            ?>

                            <!-- product list -->
                            <div class="pro-list">
                                <div class="title"><strong>产品</strong><b>金额</b></div>
                                <?php
                                $select = array();

                                if (!empty($order['hotels'])) {
                                    foreach($order['hotels'] as $hotel) {
                                ?>
                                    <div <?php if('h'.$hotel['id'] == $_GET['pr']) { $select = $hotel; echo 'class="select"'; } ?>>
                                        <strong>
                                            <?php
                                                echo $hotel['supply'] == 'TICKET' ? '<span class="fa fa-tags"></span> ' : '';
                                                echo !empty($hotel['productname']) ? $hotel['productname'] : $hotel['hotelname'];
                                                echo ' - ';
                                                echo !empty($hotel['itemname']) ? $hotel['itemname'] : $hotel['roomname'];
                                                $num = count($hotel['rooms']);
                                                if ($num > 1)
                                                    echo " x {$num}";
                                            ?>
                                            <?php if(!empty($hotel['itemname'])){ ?>
                                                <a href="javascript:void(0)" onclick="product_prieview(<?php echo "'",$hotel['ticket'],"','",$order['org_name'],"'";?>)">查看</a>
                                            <?php } ?>

                                        </strong>

                                        <b>
                                            ¥<?php echo $hotel['total']; ?>
                                        </b>
                                    </div>
                                <?php
                                    }
                                }

                                if (!empty($order['flight'])) {
                                    foreach($order['flight'] as $flight) {
                                ?>
                                    <div <?php if('f'.$flight['id'] == $_GET['pr']) { $select = $flight; echo 'class="select"'; } ?>>
                                        <strong>
                                            <?php
                                                echo $flight['supply'] == 'TICKET' ? '<span class="fa fa-tags"></span> ' : '';
                                                echo $flight['productname'] ? $flight['productname'] : $flight['flightname'];
                                                echo ' - ';
                                                echo $flight['flightcode'];
                                                $num = count($flight['passengers']);
                                                if ($num > 1)
                                                    echo " x {$num}";
                                            ?>
                                        </strong>
                                        <b>
                                            ¥<?php echo $flight['total']; ?>
                                        </b>
                                    </div>
                                <?php
                                    }
                                }

                                if (!empty($order['goods'])) {
                                    foreach($order['goods'] as $goods) {
                                ?>
                                    <div <?php if('g'.$goods['id'] == $_GET['pr']) { $select = $goods; echo 'class="select"'; } ?>>
                                        <strong>
                                            <?php
                                                echo $goods['supply'] == 'TICKET' ? '<span class="fa fa-tags"></span> ' : '';
                                                echo $goods['productname'] ? $goods['productname'] : $goods['goodsname'];
                                                echo !empty($goods['itemname']) ? ' - ' . $goods['itemname'] : '';
                                                echo " x {$goods['num']}";
                                            ?>
                                        </strong>
                                        <b>
                                            ¥<?php echo $goods['total']; ?>
                                        </b>
                                    </div>
                                <?php
                                    }
                                }

                                if (!empty($order['products'])) {
                                    foreach($order['products'] as $product) {
                                ?>
                                    <div <?php if('p'.$product['id'] == $_GET['pr']) { $select = $product; echo 'class="select"'; } ?>>
                                        <strong>
                                            <?php
                                                echo $product['payment'] == 'ticket' ? '<span class="fa fa-tags"></span> ' : '';
                                                echo $product['name'];
                                                echo ' - ';

                                                $total = 0;
                                                foreach ($product['items'] as $item)
                                                {
                                                    switch ($item['type'])
                                                    {
                                                        case 'hotel':
                                                            echo $item['item'], '&nbsp;&nbsp;';
                                                            break;
                                                        case 'flight':
                                                            echo $item['item'], '出发&nbsp;&nbsp;';
                                                            break;
                                                        case 'auto':
                                                            echo $item['item'], '出发&nbsp;&nbsp;';
                                                            break;
                                                        case 'goods':
                                                            echo $item['item'], '&nbsp;&nbsp;';
                                                            break;
                                                        case 'view':
                                                            break;
                                                    }

                                                    $total += $item['total'];
                                                }
                                            ?>
                                        </strong>
                                        <b>
                                            ¥<?php echo $total; ?>
                                        </b>
                                    </div>
                                <?php
                                    }
                                }
                                ?>
                                    <div class="total">总计：¥<?php echo $order['total']; ?><br />底价：¥<?php echo $order['floor'];
                                        if ($order['refund']){ echo '<br />退款：¥'.$order['refund']; }
                                        if ($order['rebate']){ echo '<br />含优惠：¥'.$order['rebate']; }
                                        if ($order['status'] > 2){ ?><span class="paytype <?php echo $extend['paytype']; ?>"></span><?php } ?></div>
                            </div>

                            <!-- product infomation -->
                            <?php
                            if(!empty($select))
                            {
                            ?>
                            <div class="til">产品内容/要求</div>
                            <?php
                                switch ($select['type'])
                                {
                                    case 'hotel':
                                        $sels           = array($select);
                                        break;
                                    case 'flight':
                                        $sels           = array($select);
                                        break;
                                    case 'goods':
                                        $sels           = array($select);
                                        break;
                                    default:
                                        $sels = $select['items'];
                                }

                                foreach ($sels as $item)
                                {
                                    switch ($item['type'])
                                    {
                                        case 'hotel':
                            ?>
                            <div class="info"><b>酒店：</b><?php echo $item['hotelname'];
                                echo " &nbsp;<a target=\"_blank\" href=\"http://info.putike.cn/index.html#!/basicinfo/{$item['hotel']}\">信息库</a> ";
                                if ($mode == 'operate') {
                                    foreach ($links as $link) {
                                        echo " &nbsp;<a target=\"_blank\" href=\"{$link['link']}\">{$link['name']}</a> ";
                                    }
                                }
                            ?></div>
                            <div class="info"><b>房型：</b><?php echo $item['roomname']; echo $item['supply'] == 'TICKET' ? '' : (' x '.count($item['rooms']).'间'); ?></div>
                            <?php
                                            action::exec('order_manage_tpl_extend', $item, $order, 'hotel', $mode);
                                        break;
                                        case 'flight':
                            ?>
                            <div class="info"><b>航班：</b><?php echo $item['flightname']; ?></div>
                            <div class="info"><b>客舱：</b><?php echo $item['class'] ? $item['class'] : '默认舱位'; echo $item['supply'] == 'TICKET' ? '' : ' x ',count($item['passengers']),'人'; echo $item['back'] ? ' (往返)' : ''; ?></div>
                            <?php
                                            action::exec('order_manage_tpl_extend', $item, $order, 'flight', $mode);
                                        break;
                                        case 'auto':
                            ?>
                            <div class="info"><b>车辆：</b><?php echo $item['autoname']; ?></div>
                            <?php
                                            action::exec('order_manage_tpl_extend', $item, $order, 'auto', $mode);
                                        break;
                                        case 'goods':
                            ?>
                            <div class="info"><b>商品：</b><?php echo $item['goodsname']; ?></div>
                            <?php
                                            action::exec('order_manage_tpl_extend', $item, $order, 'goods', $mode);
                                        break;
                                    }
                                }
                            }
                            ?>
                            <!-- contact -->
                            <div class="til">联系信息</div>
                            <div class="info"><b>联系人：</b> <?php echo $order['contact']; ?>&nbsp; (<?php echo $order['tel']; ?>)</div>
                            <div class="info"><b>访问IP：</b> <?php if($order['ip']){ echo $order['ip']; ?> <a href="javascript:;" onclick="search_ip(this, '<?php echo $order['ip']; ?>')">查询</a><?php } else { echo '未知'; } ?></div>

                            <!-- invoice -->
                            <div class="til">支付/发票信息</div>
                            <?php if ($order['status'] > 2) { ?>
                            <div class="info"><b>支付方式：</b> <?php echo $extend['paytype']; ?></div>
                            <?php if ($extend['payaccount']) { ?><div class="info"><b>支付账号：</b> <?php echo $extend['payaccount']; ?></div><?php } ?>
                            <div class="info"><b>流水号：</b> <?php echo $extend['paytrade']; ?></div>
                            <div class="info"><b>支付时间：</b> <?php echo date('Y-m-d H:i:s', $extend['paytime']); ?></div>
                            <hr style="margin:10px 0px 0px;" />
                            <?php } ?>
                            <?php if (!$order['invoice']) { ?>
                            <div class="info"><b>需开发票：</b> 否</div>
                            <?php }else{ ?>
                            <div class="info"><b>需开发票：</b> 是</div>
                            <div class="info"><b>发票抬头：</b> <?php echo $extend['payer']; ?></div>
                            <div class="info"><b>发票项目：</b> <?php echo $extend['item']; ?></div>
                            <div class="info"><b>发票收件人：</b> <?php echo $extend['receiver']; ?>&nbsp; (<?php echo $extend['receivertel']; ?>)</div>
                            <div class="info"><b>发票收件地址：</b> <?php echo $extend['receiveraddr']; ?></div>
                            <?php } ?>

                            <hr />

                            <!-- rule -->
                            <div class="info">查看该产品 <a id="btn-refund" href="javascript:;" data-toggle="modal" data-target="#refund-rule">退款协议</a> 和 <a id="btn-rule" href="javascript:;" data-toggle="modal" data-target="#use-rule">使用要求</a></div>
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
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $order ? date('Y-m-d H:i:s', $order['update']) : '无'; ?></p>

                                <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                            </div>
                            <?php if($mode == 'view') { ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="location.href='<?php echo empty($_SERVER['HTTP_REFERER']) ? BASE_URL.'order.php' : $_SERVER['HTTP_REFERER']; ?>'">返回</button>
                            </div>
                            <?php }else{ ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="location.href='<?php echo empty($_SERVER['HTTP_REFERER']) ? BASE_URL.'order.php' : $_SERVER['HTTP_REFERER']; ?>'">返回并保持锁定</button>
                                <button type="button" class="btn btn-default btn-sm" onclick="unlock()">返回并解锁</button>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- panel -->


                        <!-- panel -->
                        <?php action::exec('order_manage_tpl_operation', $select, $order, $mode); ?>
                        <!-- panel -->


                        <!-- panel -->
                        <?php if ($confirmations) { ?>
                        <div id="order-confirmation" class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">行程单</h3>
                            </div>
                            <ul class="list-group">
                                <?php foreach ($confirmations as $k => $v) { ?>
                                <li class="list-group-item">
                                    <a style="float:right;" href="javascript:;" data-group="<?php echo $v['group']; ?>" target="_blank" class="btn btn-xs btn-primary">查看</a>
                                    行程确认单 <?php echo $k + 1; ?>　
                                    <?php if ($v['send']) { ?>
                                    <span class="text-default">已发送</span>
                                    <?php } else { ?>
                                    <span class="text-danger">未发送</span>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                        <!-- panel -->


                        <?php if (in_array($order['status'], array(8,9)) && $order['invoice']){ ?>
                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发票状态</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <?php if ($extend['expresstype']) { ?>
                                <b>发票状态：</b> 已寄出<br />
                                <b>快递方式：</b> <?php echo expressname($extend['expresstype']); ?><br />
                                <b>快递单号：</b> <?php if (!$extend['expressno']) { echo "未填写"; } elseif ($extend['expresstype'] != 'other') { ?><a target="_blank" href="http://www.kuaidi100.com/chaxun?com=<?php echo $extend['expresstype']; ?>&nu=<?php echo $extend['expressno']; ?>"><?php echo $extend['expressno']; ?></a><?php } else { echo $extend['expressno']; } ?><br />
                                <b>快递费用：</b> ¥<?php echo $extend['expressfloor']; ?><br />
                                <?php } else { ?>
                                <b>发票状态：</b> 未寄出<br />
                                <?php } ?>
                            </div>
                            <?php if($mode == 'operate') { ?>
                                <?php if ($extend['expresstype']) { ?>
                                <div class="panel-footer text-right">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#order-express">修改</button>
                                </div>
                                <?php } else { ?>
                                <div class="panel-footer text-right">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#order-express">填写快递</button>
                                </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <!-- panel -->
                        <?php } ?>


                        <?php if (in_array($order['status'], array(1,2)) && $mode == 'operate'){ ?>
                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">线下支付</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#order-pay">线下支付</button>
                            </div>
                        </div>
                        <!-- panel -->
                        <?php } ?>


                        <?php if (isset($qa)){ ?>
                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#qa-panel">
                                <h3 class="panel-title">产品QA</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p>
                                    <b>供应商：</b> <?php echo $supply['supplyname'] ? $supply['supplyname'] : '<span style="color:#999">未填写</span>'; ?><br />
                                    <?php if($supply['bookingcode']){ ?><b>BookingCode：</b> <?php echo $supply['bookingcode']; ?><br /><?php } ?>
                                    <?php if($supply['supplyrule']){ ?><b>预定要求：</b> <?php echo $supply['supplyrule']; ?><br /><?php } ?>
                                    <?php if($supply['contact1'] && array_filter($supply['contact1'])){ ?>
                                        <b>联系人(1)</b><br />
                                        <?php if($supply['contact1']['name']){ ?><b>　联系人：</b> <?php echo $supply['contact1']['name']; ?><br /><?php } ?>
                                        <?php if($supply['contact1']['fax']){ ?><b>　传真：</b> <?php echo $supply['contact1']['fax']; ?><br /><?php } ?>
                                        <?php if($supply['contact1']['email']){ ?><b>　邮箱：</b> <?php echo $supply['contact1']['email']; ?><br /><?php } ?>
                                        <?php if($supply['contact1']['cc']){ ?><b>　抄送：</b> <?php echo $supply['contact1']['cc']; ?><br /><?php } ?>
                                        <?php if($supply['contact1']['tel']){ ?><b>　电话：</b> <?php echo $supply['contact1']['tel']; ?><br /><?php } ?>
                                        <?php if($supply['contact1']['other']){ ?><b>　其他：</b> <?php echo $supply['contact1']['other']; ?><br /><?php } ?>
                                    <?php } ?>
                                    <?php if($supply['contact2'] && array_filter($supply['contact2'])){ ?>
                                        <b>联系人(2)</b><br />
                                        <?php if($supply['contact2']['name']){ ?><b>　联系人：</b> <?php echo $supply['contact2']['name']; ?><br /><?php } ?>
                                        <?php if($supply['contact2']['fax']){ ?><b>　传真：</b> <?php echo $supply['contact2']['fax']; ?><br /><?php } ?>
                                        <?php if($supply['contact2']['email']){ ?><b>　邮箱：</b> <?php echo $supply['contact2']['email']; ?><br /><?php } ?>
                                        <?php if($supply['contact2']['cc']){ ?><b>　抄送：</b> <?php echo $supply['contact2']['cc']; ?><br /><?php } ?>
                                        <?php if($supply['contact2']['tel']){ ?><b>　电话：</b> <?php echo $supply['contact2']['tel']; ?><br /><?php } ?>
                                        <?php if($supply['contact2']['other']){ ?><b>　其他：</b> <?php echo $supply['contact2']['other']; ?><br /><?php } ?>
                                <?php } ?>
                                </p>
                            </div>

                            <hr />

                            <div id="qa-panel" class="panel-collapse collapse in">
                                <ul id="product-qa" class="list-group history">
                                    <?php if(empty($qa)) { ?>
                                    <li class="list-group-item empty text-center"><div>没有任何数据</div></li>
                                    <?php } ?>
                                    <?php foreach($qa as $v) { ?>
                                    <li class="list-group-item">
                                        <span class="log"><?php echo bbcode($v['remark']); ?></span>
                                        <span class="time"><?php echo date('m-d H:i', $v['time']); ?></span>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php if($mode == 'operate') { ?>
                                <div class="panel-body product-remark">
                                    <textarea id="qa-message" placeholder="填写关于产品的提问" style="width:100%;" class="einstance"></textarea>
                                </div>
                                <div class="panel-footer text-right">
                                    <button type="button" class="btn btn-primary btn-sm" data-loading-text="保存中.." id="saveqa">保存</button>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- panel -->
                        <?php } ?>


                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#log-panel">
                                <h3 class="panel-title">操作记录</h3>
                            </div>
                            <div id="log-panel" class="panel-collapse collapse in">
                                <?php if($mode == 'operate') { ?>
                                <div class="panel-body order-remark">
                                    <!--div id="log-panel" class="epanel"></div>
                                    <div id="log-message" class="einstance"></div-->
                                    <textarea id="log-message" placeholder="填写备注" style="width:100%;" class="einstance"></textarea>
                                </div>
                                <div class="panel-footer text-right">
                                    <button type="button" class="btn btn-primary btn-sm" data-loading-text="保存中.." id="savelog">保存</button>
                                </div>
                                <?php } ?>
                                <ul id="order-log" class="list-group history">
                                    <?php foreach($log as $v){ ?>
                                    <li class="list-group-item">
                                        <span class="log"><b><?php echo $v['username']; ?>&nbsp;</b><?php echo substr($v['remark'], 0, 2) == 'r:' ? bbcode(substr($v['remark'], 2)) : $v['remark']; ?></span>
                                        <span class="time"><?php echo date('m-d H:i', $v['time']); ?></span>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <!-- panel -->


                    </div>

                </form>
                <!-- end form -->

            </div>

        </div>
        <!-- end main -->

    </div>


    <?php if($mode == 'operate') { ?>
    <!--modal-->
    <div id="order-express" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">发票快递</h4>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal" role="form">

                        <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                        <input type="hidden" name="method" value="order-express" />

                        <div class="form-group">
                            <label class="col-sm-3 control-label">快递公司</label>
                            <div class="col-sm-6">
                                <?php $express = expressname(); ?>
                                <select class="form-control ui-select" name="type">
                                <?php foreach($express as $code => $name){ ?>
                                    <option value="<?php echo $code; ?>" <?php if($code == $extend['expresstype']) echo "selected"; ?>><?php echo $name; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">快递单号</label>
                            <div class="col-sm-6">
                                <input type="text" name="number" class="form-control" value="<?php echo $extend['expressno']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">快递费用</label>
                            <div class="col-sm-6">
                                <input type="text" name="floor" class="form-control" value="<?php echo $extend['expressfloor'] ? $extend['expressfloor'] : 6; ?>" />
                            </div>
                        </div>

                        <input type="hidden" name="fee" value="0" />

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary submit" data-loading-text="保存中..">保存</button>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->


        <?php if (in_array($order['status'], array(1,2))) { ?>
    <!--modal-->
    <div id="order-pay" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">线下支付</h4>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal" role="form">

                        <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                        <input type="hidden" name="method" value="order-pay" />

                        <div class="form-group">
                            <label class="col-sm-3 control-label">应付金额</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled value="¥<?php echo $order['total']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">支付方式</label>
                            <div class="col-sm-6">
                                <select class="form-control ui-select" name="type">
                                    <option value="支付宝">支付宝</option>
                                    <option value="银行汇款">银行汇款</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">对方帐号</label>
                            <div class="col-sm-6">
                                <input type="text" name="account" class="form-control" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">流水号</label>
                            <div class="col-sm-6">
                                <input type="text" name="trade" class="form-control" value="" />
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary submit" data-loading-text="保存中..">确认支付</button>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->
        <?php } ?>


    <!--modal-->
    <div id="order-submit-refund" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">确认退款操作</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <h4>退款已转账</h4> 点击确认按钮就代表款项已经通过汇款等形式退回到客户账户。系统将发送短信通知客户，并结束该单所有可用操作。<br />
                    </div>
                    <label><input type="checkbox" class="checkbox" /> 如果确认请选中，然后点击下方的确认按钮</label>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary submit" data-loading-text="保存中..">确认</button>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->

    <?php } ?>


    <!--modal-->
    <div id="use-rule" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">使用要求</h4>
                </div>

                <div class="modal-body">
                    <div class="rule-content"><?php echo filter::apply('order_use_rule', '无使用要求'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->

    <!--modal-->
    <div id="refund-rule" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">退改协议</h4>
                </div>

                <div class="modal-body">
                    <div class="rule-content"><?php echo filter::apply('order_refund_rule', '无退改内容'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <!--modal-->



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
function unlock(){
    $.get("<?php echo BASE_URL; ?>order.php", {method:"unlock", order:'<?php echo $order['order']; ?>', item:'<?php echo empty($_GET['pr']) ? '' : $_GET['pr']; ?>', _t:new Date().getTime()}, function(data){
        if (data.s == 0) {
            location.href = "<?php echo empty($_SERVER['HTTP_REFERER']) ? BASE_URL.'order.php' : $_SERVER['HTTP_REFERER']; ?>";
        } else {
            alert(data.err, "error");
        }
    }, "json");
}

function submit_refund(type, id){
    $("#order-submit-refund").modal("show");
    $("#order-submit-refund .modal-footer .submit").data({status:1, type:type, id:id});
}

function submit_settle(type, id){
    $.post("<?php echo BASE_URL; ?>order.php?method=save", {method:"order-settle", order:"<?php echo $order['order']; ?>", id:id, type:type}, function(data){
        if (data.s == 0){
            alert("结算操作完成", "success", function(){ location.reload(); });
        } else {
            alert(data.err, "error");
        }
    }, "json");
}

$(function(){



    <?php if($mode == 'operate'){ ?>

    $("#order-pay .modal-footer .submit").click(function(){
        var btn = $(this);
        var form = $("#order-pay form");
        btn.button('loading');
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            btn.button('reset');
            if (data.s == 0){
                $("#order-pay").modal("hide");
                alert("支付信息保存完成", "success", function(){ location.reload(); }, "#order-pay .modal-body");
            } else {
                alert(data.err, "error", null, "#order-pay .modal-body");
            }
        }, "json");
    });

    $("#order-express .modal-footer .submit").click(function(){
        var btn = $(this);
        var form = $("#order-express form");
        btn.button('loading');
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            btn.button('reset');
            if (data.s == 0){
                alert("发票信息保存完成", "success", function(){ $("#order-express").modal("hide"); location.reload(); }, "#order-express .modal-body");
            } else {
                alert(data.err, "error", null, "#order-express .modal-body");
            }
        }, "json");
    });

    $("#order-submit-refund .modal-footer .submit, #order-refund .order-refund").click(function(){
        var status = $(this).data("status");
        var id = $(this).data("id");
        var type = $(this).data("type");
        if (status == 1){
            var ck = $("#order-submit-refund :checkbox");
            if (ck.prop("checked") == false){
                ck.parent().addClass("text-danger").attr("style", "position:relative; -webkit-animation:refuse 0.3s linear 3; -moz-animation:refuse 0.3s linear 3; -o-animation:refuse 0.3s linear 3; animation:refuse 0.3s linear 3;");
                return;
            }
            $("#order-submit-refund").modal("hide");
        }

        $.post("<?php echo BASE_URL; ?>order.php?method=save", {method:"order-refund", status:status, order:"<?php echo $order['order']; ?>", id:id, type:type}, function(data){
            if (data.s == 0){
                alert("退款操作完成", "success", function(){ location.reload(); });
            } else {
                alert(data.err, "error");
            }
        }, "json");
    });

    $("#order-confirmation li a").click(function(){
        var a = $(this), group = a.data("group");
        if (a.is(".disabled")) return;
        a.addClass("disabled").text('读取中..');
        $.post("<?php echo BASE_URL; ?>order.php?method=confirmation", {method:"confirmation", order:"<?php echo $order['order']; ?>", group:group}, function(data){
            if (data.s == 0) {
                location.href = "<?php echo BASE_URL ?>order.php?method=confirmation&order=<?php echo $order['order'] ?>&group="+group;
            }else{
                alert(data.err, "error");
                a.removeClass("disabled");
            }
        }, "json");
    });

    /*var nicE = new nicEditor({buttonList:["smiley","bold","forecolor"]});
    nicE.setPanel("log-panel");
    nicE.addInstance("log-message");*/

    $("#savelog").click(function(){
        var btn = $(this);
        //var editor = nicE.instanceById("log-message");
        var log = $("#log-message").val(); //editor.getContent();
        //var tmp = $("<div />"); tmp.text(log);
        if ($.trim(log) == "") return;
        btn.button('loading');
        $.post("<?php echo BASE_URL; ?>order.php?method=save", {method:"order-log", order:"<?php echo $order['order']; ?>", message:log}, function(data){
            btn.button('reset');
            if (data.s == 0){
                $("#log-message").val(''); //editor.setContent("<br>");
                var li = $("<li class=\"list-group-item\" style=\"display:none\"><span class=\"log\"><b></b></span><span class=\"time\"></span></li>");
                li.children(".log").children("b").text(data.rs.username + "　").after(data.rs.message);
                li.children(".time").text(data.rs.time);
                $("#order-log").prepend(li);
                li.slideDown(200);
            } else {
                alert(data.err, "error", null, ".order-remark");
            }
        }, "json");
    });
    <?php } ?>

    <?php if (isset($qa)){ ?>
    $("#saveqa").click(function(){
        var btn = $(this);
        var remark = $("#qa-message").val();
        if ($.trim(remark) == "") return;
        btn.button('loading');
        $.post("<?php echo BASE_URL; ?>order.php?method=save", {method:"order-qa", product:"<?php echo $qa_product; ?>", order:"<?php echo $order['order']; ?>", message:remark}, function(data){
            btn.button('reset');
            if (data.s == 0){
                $("#qa-message").val('');
                var li = $("<li class=\"list-group-item\" style=\"display:none\"><span class=\"log\"></span><span class=\"time\"></span></li>");
                li.children(".log").append(data.rs.message);
                li.children(".time").text(data.rs.time);
                $("#product-qa").prepend(li);
                li.slideDown(200);
            } else {
                alert(data.err, "error", null, ".product-remark");
            }
        }, "json");
    });
    <?php } ?>


    $(".ui-select").chosen({width:"100%",disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});

    <?php if (!empty($error)){ ?>
    alert("<?php echo $error; ?>", "error");
    <?php } ?>
});
</script>

<?php action::exec('order_manage_tpl_footer', $select, $order, $mode); ?>


</body>
</html>
