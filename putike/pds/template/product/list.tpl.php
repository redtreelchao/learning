<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 产品组合列表</title>

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

                <h1 class="page-header">产品组合列表</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline"  action="" method="GET" role="form">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作<?php if($remind){ echo ' <span class="badge badge-sm badge-danger">'.$remind.'</span>'; }  ?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php if(isset($_GET['status'])){ ?>
                                <li><a href="<?php echo BASE_URL; ?>product.php">返回所有产品</a></li>
                                <?php } ?>
                                <li><a href="<?php echo BASE_URL; ?>product.php?status=stopselling">即将下架<?php if($remind){ echo ' <span class="badge badge-sm badge-danger">'.$remind.'</span>'; }  ?></a></li>
                                <li><a href="#flight-calculator" data-toggle="modal">机酒计算器</a></li>
                                <li><a href="<?php echo BASE_URL; ?>product.php?method=new">添加新产品</a></li>
                                <li><a href="#search" data-toggle="modal" data-target="#search">高级检索</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="hidden" name="status" value="<?php echo $status; ?>" />
                            <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" data-search=".table-responsive" />
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


                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th width="22%">产品名称</th>
                                <th></th>
                                <th width="10%">类型</th>
                                <th width="10%">状态</th>
                                <th width="25%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>产品名称</th>
                                <th></th>
                                <th>类型</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <?php foreach($list as $k => $v) { ?>
                        <tbody class="product">
                            <tr id="row-<?php echo $v['id']; ?>" data-payment="<?php echo $v['payment']; ?>">
                                <td class="bgf9"><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td class="bgf9" colspan="2" title="ID:<?php echo $v['id']; ?>">
                                    <?php
                                    switch($v['type']){
                                        case 1:
                                            echo '<span class="fa fa-building"></span> '; break;
                                        case 2:
                                            echo '<span class="fa fa-building"></span> + <span class="fa fa-car"></span> '; break;
                                        case 3:
                                            echo '<span class="fa fa-plane"></span> '; break;
                                        case 5:
                                            echo '<span class="fa fa-tree"></span> '; break;
                                        case 4:
                                            echo '<span class="fa fa-building"></span> + <span class="fa fa-plane"></span> '; break;
                                        case 6:
                                            echo '<span class="fa fa-building"></span> + <span class="fa fa-tree"></span> '; break;
                                        case 7:
                                            echo '<span class="fa fa-gift"></span> '; break;
                                        case 8:
                                            echo '<span class="fa fa-plane"></span> + <span class="fa fa-tree"></span> '; break;
                                        case 9:
                                            echo '<span class="fa fa-building"></span> + <span class="fa fa-plane"></span> + <span class="fa fa-tree"></span> '; break;
                                    }
                                    echo '<b>'.$v['name'].'</b>';
                                    echo ' <span class="info hidden-md hidden-lg">(ID:',$v['id'],')</span>';
                                    ?>
                                </td>
                                <td class="bgf9"><?php echo $v['payment'] == 'ticket' ? '<span class="fa fa-tags"></span> 券类' : '<span class="fa fa-clock-o"></span> 预订'; ?></td>
                                <td id="status-<?php echo $v['id']; ?>" class="bgf9">
                                    <?php
                                    switch ($v['audit'])
                                    {
                                        case 0:
                                            if ($v['status'] >= 0)
                                                echo '<span class="label label-default">未提交</span>';
                                            else
                                                echo '<span class="label label-default">下架</span>';
                                            break;
                                        case 1: echo '<span class="label label-warning">审核中</span>'; break;
                                        case 2:
                                            if ($v['status'] > 0)
                                                echo '<span class="label label-primary">正常</span>';
                                            else
                                                echo '<span class="label label-default">下架</span>';
                                            break;
                                        case 3: echo '<span class="label label-warning">申请修改</span>'; break;
                                        case 4:
                                            if ($v['status'] > 0)
                                                echo '<span class="label label-primary">正常</span>';
                                            else if ($v['status'] == 0)
                                                echo '<span class="label label-default">未提交</span>';
                                            else
                                                echo '<span class="label label-default">下架</span>';
                                            break;
                                        case -1: echo '<span class="label label-info">修改中</span>'; break;
                                        case -2: echo '<span class="label label-danger">审核失败</span>'; break;
                                    }
                                    ?>
                                </td>
                                <td class="md-nowrap bgf9">
                                <?php
                                if ($v['status'] == 0)
                                {
                                    // 未发布
                                    if (in_array($_SESSION['role'], [0,2,3,4]) && $v['audit'] <= 0)
                                    {
                                        // 未审核时，产品可操作
                                ?>
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_URL; ?>product.php?method=edit&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 修改</span></a>
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="javascript:audit(<?php echo $v['id']; ?>, 1, this)">提交审核</a></li>
                                            <li><a href="<?php echo BASE_URL; ?>profit.php?payment=<?php echo $v['payment']; ?>&type=<?php echo $v['type']; ?>&id=<?php echo $v['id']; ?>" target="_blank">设置利润</a></li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;" onclick="createitem(<?php echo $v['type'],',',$v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-plus hidden-md"></span><span class="hidden-xs hidden-sm"> 添加</span></a>
                                    <!--<a href="javascript:preview(<?php /*echo $v['id']; */?>)" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-eye-open hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>-->
                                    <a href="<?php echo BASE_URL; ?>product.php?method=preview&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>
                                    <a href="javascript:del(<?php echo $v['id']; ?>)" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
                                    <a href="javascript:channel(<?php echo $v['id']; ?>,'<?php echo $v['org'];?>')" class="btn btn-sm btn-default" ><span class="glyphicon glyphicon-channel hidden-md"></span><span class="hidden-xs hidden-sm"> 渠道</span></a>
                                    
                                <?php
                                    }
                                    else if (in_array($_SESSION['role'], [0,3,4]) && $v['audit'] == 1)
                                    {
                                        // 未审核时，运营可查看审核
                                ?>
                                    <a href="<?php echo BASE_URL; ?>product.php?method=edit&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 修改</span></a>
                                    <a href="<?php echo BASE_URL; ?>product.php?method=preview&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, 2)" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm"> 通过</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, -2)" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove hidden-md"></span><span class="hidden-xs hidden-sm"> 拒绝</span></a>
                                    <a href="javascript:channel(<?php echo $v['id']; ?>,'<?php echo $v['org'];?>')" class="btn btn-sm btn-default" ><span class="glyphicon glyphicon-channel hidden-md"></span><span class="hidden-xs hidden-sm"> 渠道</span></a>
                                <?php
                                    }
                                }
                                else if ($v['status'] == 1)
                                {
                                    // 已发布
                                ?>
                                    <a href="javascript:;" onclick="status(<?php echo $v['id']; ?>, -1, this);" class="btn btn-sm btn-default modalInfo"><span class="glyphicon glyphicon-save hidden-md"></span><span class="hidden-xs hidden-sm"> 下架</span></a>
                                    <?php
                                    if (in_array($_SESSION['role'], [0,2,3,4]) && in_array($v['audit'], [2,4]))
                                    {
                                        // 已审核上线，产品可发起修改申请
                                    ?>
                                    <a href="javascript:audit_prompt(<?php echo $v['id']; ?>)" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 修改</span></a>
                                    <!--<a href="javascript:preview(<?php /*echo $v['id']; */?>)" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-eye-open hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>-->
                                    <a href="<?php echo BASE_URL; ?>product.php?method=preview&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>
                                    <a href="javascript:copy(<?php echo $v['id']; ?>, 'product')" class="btn btn-sm btn-default"><span class="fa fa-copy hidden-md"></span><span class="hidden-xs hidden-sm"> 复制</span></a>
                                    <a href="javascript:channel(<?php echo $v['id']; ?>,'<?php echo $v['org'];?>')" class="btn btn-sm btn-default" ><span class="glyphicon glyphicon-channel hidden-md"></span><span class="hidden-xs hidden-sm"> 渠道</span></a>
                                    <?php
                                    }
                                    else if (in_array($_SESSION['role'], [0,1,3,4]) && $v['audit'] == 3)
                                    {
                                    ?>
                                    <a href="<?php echo BASE_URL; ?>product.php?method=preview&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, -1)" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm" title="<?php echo $v['reason']; ?>"> 通过</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, 4)" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove hidden-md"></span><span class="hidden-xs hidden-sm" title="<?php echo $v['reason']; ?>"> 拒绝</span></a>
                                    <?php
                                    }
                                    ?>
                                <?php
                                }
                                else
                                {
                                    // 已下架
                                    if (in_array($_SESSION['role'], [0,2,3,4]) && ($v['audit'] <= 0 || in_array($v['audit'], [2,4])))
                                    {
                                        // 已下架，审核失败、可修改
                                ?>
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_URL; ?>product.php?method=edit&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 修改</span></a>
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="javascript:audit(<?php echo $v['id']; ?>, 1, this)">提交审核</a></li>
                                            <li><a href="<?php echo BASE_URL; ?>profit.php?payment=<?php echo $v['payment']; ?>&type=<?php echo $v['type']; ?>&id=<?php echo $v['id']; ?>" target="_blank">设置利润</a></li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;" onclick="createitem(<?php echo $v['type'],',',$v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-plus hidden-md"></span><span class="hidden-xs hidden-sm"> 添加</span></a>
                                    <a href="javascript:copy(<?php echo $v['id']; ?>, 'product')" class="btn btn-sm btn-default"><span class="fa fa-copy hidden-md"></span><span class="hidden-xs hidden-sm"> 复制</span></a>
                                    <?php
                                    }
                                    else if (in_array($_SESSION['role'], [0,1,3,4]) && $v['audit'] == 3)
                                    {
                                    ?>
                                    <a href="<?php echo BASE_URL; ?>product.php?method=preview&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, -1)" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm"> 通过</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, 4)" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove hidden-md"></span><span class="hidden-xs hidden-sm"> 拒绝</span></a>
                                    <?php
                                    }
                                    else if (in_array($_SESSION['role'], [0,3,4]) && $v['audit'] == 1)
                                    {
                                        // 待审核时，运营可查看审核
                                    ?>
                                    <a href="<?php echo BASE_URL; ?>product.php?method=edit&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 修改</span></a>
                                    <a href="<?php echo BASE_URL; ?>product.php?method=preview&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 预览</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, 2)" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm"> 通过</span></a>
                                    <a href="javascript:audit(<?php echo $v['id']; ?>, -2)" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove hidden-md"></span><span class="hidden-xs hidden-sm"> 拒绝</span></a>
                                    <?php
                                    }
                                    ?>
                                <?php
                                }
                                ?>
                                </td>
                            </tr>
                            <?php if(!$v['items']){ ?>
                            <tr class="product-<?php echo $v['id']; ?> item">
                                <td colspan="6" class="empty">
                                    <div style="border:#ccc dashed 1px;">没有产品明细，点此<a href="javascript:createitem(<?php echo $v['type'],',',$v['id']; ?>);">添加产品内容</a></div>
                                </td>
                            </tr>
                            <?php } else { ?>

                                <?php foreach($v['items'] as $item) { ?>
                                <tr id="item-<?php echo $item['id']; ?>" class="product-<?php echo $v['id']; ?> type-<?php echo $item['objtype']; ?> item <?php echo $item['status'] ? '' : 'hidden'; ?>" data-id="<?php echo $item['id']; ?>" data-pid="<?php echo $v['id']; ?>">
                                    <td>
                                        <div class="move" <?php if($v['status'] != 0) echo 'style="visibility:hidden"'; ?>>
                                            <span class="glyphicon glyphicon-option-vertical"></span>
                                            <span class="glyphicon glyphicon-option-vertical"></span>
                                        </div>
                                    </td>
                                    <td title="ID:<?php echo $item['id']; ?>">
                                        <?php
                                        switch($item['objtype']) {
                                            case 'room':
                                                echo '<span class="fa fa-building-o"></span>',$item['name'];
                                            break;
                                            case 'flight':
                                                echo '<span class="fa fa-plane"></span>始发：',$item['name'];
                                            break;
                                            case 'auto':
                                                echo '<span class="fa fa-car"></span>始发：',$item['name'];
                                            break;
                                            case 'goods':
                                                echo '<span class="fa fa-gift"></span>',$item['name'];
                                            break;
                                        }
                                        ?>
                                    </td>
                                    <td colspan="2" title="<?php if($v['payment'] == 'ticket') echo "预定有效期:".date('Y-m-d', $item['start']).'至'.date('Y-m-d', $item['end']); ?>">
                                        <?php
                                        switch($item['objtype']) {
                                            case 'room':
                                                echo '<span class="info">',$item['hotelname'],'  ',roomname($item['roomname'], 2),'，',$item['ext'],'晚</span>';
                                                if ($item['online'] || $item['offline'])
                                                {
                                                    echo '<br class="hidden-lg" /><span class="info visible-lg-inline">，</span><span class="info">', date('Y/m/d', $item['online']), '-', date('Y/m/d', $item['offline']) ,'</span>';
                                                }
                                            break;
                                            case 'flight':
                                                echo '<span class="info">航班 ',$item['flight'],' ',$item['ext2'],' 舱，',($item['objid'] ? "含第{$item['ext']}天返程" : ''),'</span>';
                                            break;
                                            case 'auto':
                                                //echo '';
                                            break;
                                            case 'goods':
                                                //echo '<span class="info">航班 ',$item['flight'],' ',$item['ext2'],' 舱，',($item['objid'] ? "含第{$item['ext']}天返程" : ''),'</span>';
                                            break;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $item['status'] ? '<span class="info">可售</span>' : '<span class="f12 text-danger">不可售</span>'; ?>
                                    </td>
                                    <td class="md-nowrap">
                                        <a href="javascript:;" onclick="price(this);" data-code="<?php echo $item['id']; ?>" data-type="<?php echo $v['payment'] == 'prepay' ? 'calendar' : 'form'; ?>" class="btn btn-sm btn-default"><span class="fa fa-<?php echo $v['payment'] == 'prepay' ? 'calendar' : 'rmb'; ?> hidden-md"></span><span class="hidden-xs hidden-sm"> 价格</span></a>
                                        <?php action::exec('product_list_item_manage_tpl', $v, $item); ?>
                                        <?php if ($v['status'] <= 0 && $v['audit'] <= 0) { ?>
                                        <a href="javascript:;" onclick="edititem(this);" data-code="<?php echo $item['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                        <a href="javascript:;" onclick="copy(<?php echo $item['id'] ?>, 'item');" class="btn btn-sm btn-default"><span class="fa fa-copy hidden-md"></span><span class="hidden-xs hidden-sm"> 复制</span></a>
                                        <a href="javascript:;" onclick="delitem(this);" data-code="<?php echo $item['id']; ?>" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>

                                <?php if($item['status'] == 0) { ?>
                                <tr class="product-<?php echo $v['id']; ?>">
                                    <td colspan="6" class="empty">
                                        <div style="padding:5px 0px; font-size:12px; border:#ccc dashed 1px;"><a href="javascript:show(<?php echo $v['id']; ?>);">查看全部内容</a></div>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                        <?php } ?>
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



    <script type="text/html" id="item-tmpl">
        <td>
            <div class="move">
                <span class="glyphicon glyphicon-option-vertical"></span>
                <span class="glyphicon glyphicon-option-vertical"></span>
            </div>
        </td>
        <td title="ID:{id}">
            {name}
        </td>
        <td colspan="3">
            {info}
        </td>
        <td class="md-nowrap">
            <a href="javascript:;" onclick="price(this);" data-code="{id}" data-type="{payment}" class="btn btn-sm btn-default"><span class="fa fa-{btn} hidden-md"></span><span class="hidden-xs hidden-sm"> 价格</span></a>
            <a href="javascript:;" onclick="edititem(this);" data-code="{id}" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
            <a href="javascript:;" onclick="delitem(this);" data-code="{id}" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
        </td>
    </script>



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.highlightRegex.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.event.move.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.sortable.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>

    <?php include(dirname(__FILE__).'/_item_modal.tpl.php'); ?>


    <!-- modal search-->
    <div id="search" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">高级搜索</h4>
                </div>
                <div class="modal-body">
                    <form role="form" action="<?php echo BASE_URL; ?>product.php" method="GET" target="_self" style="padding:10px 15px 0px">

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>关键字</label>
                                <input type="text" class="form-control" name="keyword" value="<?php echo $keyword; ?>" />
                                <input type="hidden" name="action" value="">
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>分销渠道</label>
                                <select class="form-control ui-select" name="from">
                                    <option value="">请选择..</option>
                                    <?php foreach($orgs as $v){ ?>
                                        <option value="<?php echo $v['id']; ?>" <?php if($search['from'] == $v['id']) echo 'selected'; ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>产品类型</label>
                                <select id="type" name="type" class="form-control ui-select" >
                                        <option value="">请选择..</option>
                                    <?php foreach(producttypes() as $v){ ?>
                                        <option value="<?php echo $v['code']; ?>"<?php if($search['type'] == $v['code']) echo ' selected'; if($v['disabled']) echo ' disabled' ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>状态</label>
                                <select class="form-control ui-select" name="status">
                                    <option value="">全部状态</option>
                                    <option value="uncommitted" <?php if($search['status'] == 'uncommitted') echo 'selected'; ?>>未提交</option>
                                    <option value="offline" <?php if($search['status'] == 'offline') echo 'selected'; ?>>下架</option>
                                    <option value="inexamine" <?php if($search['status'] == 'inexamine') echo 'selected'; ?>>审核中</option>
                                    <option value="on" <?php if($search['status'] == 'on') echo 'selected'; ?>>正常</option>
                                    <option value="am" <?php if($search['status'] == 'am') echo 'selected'; ?>>申请修改</option>
                                    <option value="revising" <?php if($search['status'] == 'revising') echo 'selected'; ?>>修改中</option>
                                    <option value="af" <?php if($search['status'] == 'af') echo 'selected'; ?>>审核失败</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>上线时间</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="start" id="start" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $search['start'] ? date('Y-m-d', $search['start']) : ''; ?>" />
                                </div>

                            </div>
                            <?php //申请修改(Application modification)  审核失败<!--(Audit failure)--> ?>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>下线时间</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    <input type="text" name="end" id="end" class="form-control ui-datepicker" autocomplete="off" value="<?php echo $search['end'] ? date('Y-m-d', $search['end']) : ''; ?>" />
                                </div>

                            </div>
                        </div>



                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="$('#search form').get(0).reset();">清除</button>
                    <button type="button" class="btn btn-primary" onclick="javascript:advance_search();">搜索</button>
                    <button type="button" class="btn btn-success" onclick="javascrpt:search_export();">导出</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal search-->

    <!-- Modal product view -->
    <div class="modal fade" id="product-preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">产品预览</h4>
                </div>
                <div class="modal-body text-center">
                    <iframe src="" width="320" height="568" frameborder="0" scrolling="no" style="border:4px #ccc solid; border-radius:4px; box-shadow:5px 5px 10px rgba(0,0,0,0.5); margin:0 auto;" sandbox="allow-scripts allow-same-origin">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="channelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">渠道设置</h4>
                </div>
                <div class="modal-body">
                <form class="modal-body form-horizontal" id="channel_form">
                    <div class="form-group row">                    
                            <div class="col-md-9">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default <?php if(in_array('1', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="1" <?php if(in_array('1', $data['org'])) echo 'checked'; ?> /> putike
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('2', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="2" <?php if(in_array('2', $data['org'])) echo 'checked'; ?> /> feekr
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('3', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="3"  />浙江旅游
                                    </label>
                                    <label class="btn btn-default <?php if(in_array('4', $data['org'])) echo 'active'; ?>">
                                        <input type="checkbox" autocomplete="off" name="org[]" value="4"  /> 美宿
                                    </label>
                                </div>
                            </div>
                    </div>
                    <input name='product_id' value='' id="product_id" type="hidden" />
                </form>            
                </div>            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                <button type="button" class="btn btn-primary save-channel" data-loading-text="保存中..">保存</button>
                            </div>
                </div>
            </div>
    </div>

    <!-- Modal flight-calculator -->
    <div class="modal fade" id="flight-calculator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">机+酒计算器</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">单人价</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" data-room="1" data-flight="1" class="form-control" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">单房差</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" data-room="1" data-flight="0" class="form-control" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">双人价</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" data-room="2" data-flight="2" class="form-control" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">单人机票</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" data-room="0" data-flight="1" class="form-control" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">房费</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                                    <input type="text" data-room="2" data-flight="0" class="form-control" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
    </div>
    <!-- Modal product delete -->
    <div class="modal fade" id="product-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">产品删除确认</h4>
                </div>
                <div class="modal-body text-center">
                    <h4></h4>
                    <p>（删掉了的话，找甩头哥也没用的，所以三思吧骚年~</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">算了</button>
                    <button type="button" class="btn btn-danger">签字画押</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal product edit -->
    <div class="modal fade" id="product-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">产品修改确认</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-sm-2">修改原因</label>
                        <div class="col-sm-9">
                            <textarea name="remark" cols="30" rows="5" maxlength="200" class="remark form-control" placeholder="修改申请将提交给客服部门确认"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">算了</button>
                    <button type="button" class="btn btn-warning">确认</button>
                </div>
            </div>
        </div>
    </div>

    <div id="offline" class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">下架</h4>
                </div>
                <form class="modal-body form-horizontal" id="info_form">
                    <div class="form-group">
                        <label class="control-label col-sm-2">下架理由</label>
                        <div class="col-sm-9">
                            <select name="reason" class="reason form-control ui-select">
                                <option value="信息有误，替换上线">信息有误，替换上线</option>
                                <option value="延期，替换上线">延期，替换上线</option>
                                <option value="套餐无效，直接下架">套餐无效，直接下架</option>
                                <option value="下架">下架</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">下架原因</label>
                        <div class="col-sm-9">
                            <textarea name="remark" cols="30" rows="5" maxlength="200" class="remark form-control"></textarea>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary btn-save" data-loading-text="保存中..">保存</button>
                </div>
            </div>
        </div>
    </div>


    <div id="order-remind" class="show">
        <?php foreach ($important as $v) { ?>
            <a href="<?php echo BASE_URL; ?>product.php?state=<?php echo $v['state']; ?>"><?php echo $v['type']; ?><b><?php echo $v['count']; ?></b></a>
        <?php } ?>
    </div>

    <script>
        function advance_search() {
            $('#search form').prop('target','_self');
            $('#search form input[name=action]').val('');
            $('#search form').submit();
        }

        $('#search').on('show.bs.modal', function (e) {
            $('#search form').prop('target','_self');
            $('#search form input[name=action]').val('');
        });

        function search_export() {
            //console.log('export');

            $('#search form').prop('target','_blank');
            $('#search form input[name=action]').val('export');
            $('#search form').submit();
            //return false;
            //console.log('export');

            /*var _this = $('#search form');
            var searchdata = {
                    keyword: _this.find('input[name=keyword]').val(),
                    from: _this.find('select[name=from]').val(),
                    type: _this.find('select[name=type]').val(),
                    start: _this.find('input[name=start]').val(),
                    end: _this.find('input[name=end]').val()
                };

            console.log(searchdata);
            return false;

            $.get(_url, postdata, function(data){
                btn.prop("disabled", false);
                if (data.s == 0){
                    alert("操作成功，正在刷新页面..", "success", function(){ location.reload(); });
                }else{
                    alert(data.err, "error");
                }
            }, "json");*/
        }
        
    function show(id, a){
        $("tr.product-"+id+" .hidden").show();
        $("tr.product-"+id+":not(.item) a").data("open").remove();
    }

    // save product's items base data
    function saveitem(){
        var form = $("#item-form form");
        $.post("<?php echo BASE_URL; ?>product.php?method=item", form.serialize(), function(data){
            if(data.s == 0){
                var pid = $("#item-pid").val();
                var id = $("#item-id").val();
                var type = data.rs.objtype;
                var payment = $("#row-"+pid).data("payment");
                var btn = payment == "ticket" ? "rmb" : "calendar";
                var tmpldata = {id:data.rs.id, name:"", info:"", payment:payment, btn:btn};
                switch (type){
                    case "room":
                        tmpldata.name = '<span class="fa fa-building-o"></span>'+data.rs.name;
                        tmpldata.info = '<span class="info">'+data.rs.hotel_name+' '+data.rs.room_name+'，'+data.rs.ext+'晚</span>';
                    break;
                    case "flight":
                        tmpldata.name = '<span class="fa fa-plane"></span>始发：'+data.rs.name;
                        tmpldata.info = '<span class="info">航班 '+data.rs.flight+' '+data.rs.ext2+' 舱，'+(data.rs.objid ? '含第'+data.rs.ext+'天返程' : '')+'</span>';
                    break;
                }

                var tmpl = $("#item-tmpl").html();
                var reg = new RegExp("\\{([0-9a-zA-Z_-]*?)\\}", 'gm');
                var item = tmpl.replace(reg, function (node, key) { return tmpldata[key]; });

                if (!id) {
                    var tr = $('<tr id="item-'+id+'" class="product-'+pid+' type-'+type+' item" data-pid=\"'+pid+'\"></tr>');
                    tr.html(item);
                    $(".product-"+pid).last().before(tr);
                    tr.parent("tbody").sortable("destroy").sortable({items:".item", handle:".move", forcePlaceholderSize:true});
                } else {
                    $("#item-"+id).html(item);
                }
                $("#item-form").modal("hide");
            }else{
                alert(data.err, 'error', null, "#item-form .modal-body");
            }
        }, "json");
    }

    $("#offline .btn-save").on('click',function (){
        var modal = $("#offline"),
            btn   = modal.data("btn");
        status(modal.data("id"), -101, modal.data("btn"))
    });

    // update status
    function status(id, status, btn){
        var btn = $(btn),
            postdata = {id:id, status:status};

        if ( status == -1 ) {
            $('#offline').data({id:id, btn:btn.get(0)}).modal('show');
            return false;
        } else if ( status == -101 ){
            postdata.status = -1;
            postdata.reason = $("#offline .reason").val();
            postdata.remark = $("#offline .remark").val();
            $("#offline").modal("hide");
        }

        btn.prop("disabled", true).children(".glyphicon").attr("class", "glyphicon glyphicon-refresh glyphicon-loading");
        $.post("<?php echo BASE_URL; ?>product.php?method=status", postdata, function(data){
            btn.prop("disabled", false);
            if (data.s == 0){
                alert("操作成功，正在刷新页面..", "success", function(){ location.reload(); });
            }else{
                alert(data.err, "error");
            }
        }, "json");
    }

    // update audit
    function audit(id, audit, btn, remark){
        var btn = $(btn),
            postdata = {id:id, audit:audit, remark:remark};

        btn.prop("disabled", true).children(".glyphicon").attr("class", "glyphicon glyphicon-refresh glyphicon-loading");
        $.post("<?php echo BASE_URL; ?>product.php?method=audit", postdata, function(data){
            btn.prop("disabled", false);
            if (audit == 3) $("#product-edit").modal("hide");
            if (data.s == 0){
                alert("操作成功，正在刷新页面..", "success", function(){ location.reload(); });
            }else{
                alert(data.err, "error");
            }
        }, "json");
    }

    function audit_prompt(id){
        var modal = $("#product-edit"), btn = modal.find(".modal-footer .btn-warning"), remark = modal.find("textarea");
        btn.unbind("click").bind("click", function(){
            audit(id, 3, btn, remark.val());
        });
        modal.modal("show");
    }

    function del(id){
        var name = $("#row-"+id+" td:eq(1) b").text(),
            modal = $("#product-delete"),
            btn   = modal.find(".modal-footer .btn-danger");
        modal.find(".modal-body h4").text("要删除产品“"+name+"”？SURE？");
        btn.unbind("click").bind("click", function(){
            $.post("<?php echo BASE_URL; ?>product.php?method=del", {id:id, type:"product"}, function(data){
                if(data.s == 0){
                    modal.modal("hide");
                    $('#row-'+id).fadeOut(500, function(){ $(this).remove(); });
                    $('.product-'+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err, "error", null, "#product-delete .modal-body");
                }
            }, "json");
        });
        modal.modal("show");
    }

    function copy(id, type){
        $.post("<?php echo BASE_URL; ?>product.php?method=clone", {id:id, type:type}, function(data){
            if(data.s == 0){
                if (type == 'product')
                    location.href = "<?php echo BASE_URL; ?>product.php?method=edit&id="+data.rs+"#copy";
            }else{
                alert(data.err, "error");
            }
        }, "json");
    }

    function preview(id){
        var modal = $("#product-preview");
        modal.modal("show");
        modal.find("iframe").attr("src", "<?php echo BASE_URL; ?>product.php?method=preview&id="+id);
    }

    function channel(id,org)
    {       
        var modal = $("#channelModal");
        $("#product_id").val(id);
        modal.modal("show");
        var arr = org.split(',');
        $('#channelModal').find('.btn-default').removeClass('active');
        for( var i = 0; i < arr.length ; i++ ) {
            $('#channelModal').find('.btn-group').find('.btn').eq(arr[i]-1).addClass('active');
        }
    }

    $(".save-channel").on('click',function(){
        var data = $("#channel_form").serialize();
        $.post('<?php echo BASE_URL; ?>product.php?method=channel',data,function(rs){
            if(rs.s==0)
            {
              $("#channelModal").modal("hide");  
            }else{
                alert(data.err, "error");
            }
        });
    });

    $(function(){

        $("#start, #end").zdatepicker({viewmonths:1});

        // $('#awayInfo').modal('show');

        $('#awayInfo .form-group textarea').keyup(function(){
            var _this = $(this);
                _curLength= _this.val().length;
            if(_curLength>200){
                var _val =_this.val().substr(0,200);
                _this.val(_val);
            }else{
                $("#awayInfo .description em").text(_this.val().length);
            }
        })

        $(".product").sortable({items:".item", handle:".move", forcePlaceholderSize:true});

        $(".product").on("sortupdate", function(e, items){
            var sel = $(items.item[0]), _this = $(this), items = _this.children(".item"), seq = [], id = 0, product = 0;
            items.each(function(i){
                id = $(this).data("id");
                product = $(this).data("pid");
                seq.push(id);
            });
            sel.find(".move").css("visibility", "visible").html("<span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span>");
            _this.sortable("disabled");
            $.post("<?php echo BASE_URL; ?>product.php?method=sort", {"sort":seq, "product":product}, function(data){
                if (data.s != 0) {
                    alert(data.err, "error");
                }
                _this.sortable("enable");
                sel.find(".move").css("visibility", "hidden").html("<span class=\"glyphicon glyphicon-option-vertical\"></span> <span class=\"glyphicon glyphicon-option-vertical\"></span>");
            }, "json");
        });

        $("#flight-calculator input").blur(function(){
            var x = 0, y = 0, z = false, r = false; cache=[];
            $("#flight-calculator input").each(function(){
                var _this = $(this),
                    r = parseInt(_this.data("room"), 10),
                    f = parseInt(_this.data("flight"), 10);
                if (_this.val()) {
                    var val = parseInt(_this.val(), 10);
                    cache.push([r, f, val]);
                    if (z !== false) {
                        var a1 = cache[z][0], b1 = cache[z][1], c1 = cache[z][2], a2 = r, b2 = f, c2 = val;
                        var now = [r, f, val];
                        if (a2 == 0) {
                            y = c2 / b2;
                            x = c1 / a1 - (c2 * b2) / (a1 * b2);
                        } else {
                            y  = (c1 - c2 * a1 / a2) * a2 / (b1 * a2 - b2 * a1);
                            x  = (c2 - b2 * y)  / a2;
                        }
                    }
                    //console.log(x, y);
                    z = cache.length - 1;
                }else{
                    cache.push([r, f, _this]);
                }
            });

            if (!isNaN(x) && x && y) {
                for (i in cache) {
                    if (isNaN(cache[i][2])) {
                        var a = cache[i][0], b = cache[i][1];
                        cache[i][2].val(a * x + b * y);
                    }
                }
            }
        });
    });

    </script>
</body>
</html>
