<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 定制需求卡</title>

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
    <script type="text/javascript" src=".<?php echo RESOURCES_URL; ?>js/plupload/plupload.full.min.js"></script>
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

                <h1 class="page-header">定制需求卡</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#search" data-toggle="modal" data-target="#search">高级检索</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="search" name="keyword" class="form-control" value="<?php echo $keyword; ?>" data-search=".table-responsive" />
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
                                <th>编号/提交时间</th>
                                <th>客户</th>
                                <th>目的地</th>
                                <th>出发时间/行程天数</th>
                                <th>预算</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>编号/提交时间</th>
                                <th>客户</th>
                                <th>目的地</th>
                                <th>出发时间/行程天数</th>
                                <th>预算</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            foreach ($list as $v) {
                            ?>
                            <tr <?php if($v['status'] == 3 ) echo 'style="opacity:.4"'; ?>>
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']?>" /></td>
                                <td><?php echo $v['code']; ?><br /><span class="info"><?php echo date('Y-m-d H:i', $v['createtime']); ?></span></td>
                                <td><?php echo $v['contact']; ?><br /><span class="info"><?php echo $v['tel']; ?></span></td>
                                <td><?php echo $v['area']; ?></td>
                                <td><?php echo date('Y-m-d', $v['departure']); ?> ( 共<?php echo $v['days']; ?>天 )</td>
                                <td><?php echo $v['budget'];?></td>
                                <td><?php
                                    $s = $status[$v['status']];
                                    switch ($v['status']) {
                                        case 1: // 待确认
                                        case 5: // 需要修改
                                            echo '<span class="label label-warning">'.$s.'</span>'; break;
                                        case 2: // 优先
                                        case 7: // 支付成功
                                            echo '<span class="label label-danger">'.$s.'</span>'; break;
                                        case 3: // 无效
                                        case 10: // 已过期
                                            echo '<span class="label label-default">'.$s.'</span>'; break;
                                        default:
                                            echo '<span class="label label-info">'.$s.'</span>'; break;
                                    }
                                ?>
                                </td>
                                <td class="md-nowrap">
                                    <a href="<?php echo BASE_URL; ?>tourcard.php?method=view&id=<?php echo $v['id'] ?>" class="btn btn-sm btn-default">
                                        <span class="glyphicon glyphicon-eye-open hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 查看</span>
                                    </a>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" <?php if($v['status'] > 3) echo 'disabled'; ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="glyphicon glyphicon-tag hidden-md"></span>
                                            标记 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <?php if ($v['status'] == 0) { ?>
                                            <li><a href="javascript:tag(<?php echo $v['id']; ?>, 1)">取消已核实</a></li>
                                            <?php } else { ?>
                                            <li><a href="javascript:tag(<?php echo $v['id']; ?>, 0)">已核实</a></li>

                                            <?php } ?>
                                            <?php if ($v['status'] == 2) { ?>
                                            <li><a href="javascript:tag(<?php echo $v['id']; ?>, 1)">取消优先</a></li>
                                            <?php } else { ?>
                                            <li><a href="javascript:tag(<?php echo $v['id']; ?>, 2)">优先</a></li>

                                            <?php } ?>
                                            <?php if ($v['status'] == 3) { ?>
                                            <li><a href="javascript:tag(<?php echo $v['id']; ?>, 1)">取消无效</a></li>
                                            <?php } else { ?>
                                            <li><a href="javascript:tag(<?php echo $v['id']; ?>, 3)">无效</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </td>
                            <tr>
                            <?php
                            }
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
                    <form role="form" action="<?php echo BASE_URL; ?>tourcard.php" method="GET" style="padding:10px 15px 0px">

                        <div id="search-timetype" class="form-group row">
                            <div class="btn-group col-sm-12" data-toggle="buttons">
                                <label class="btn btn-default <?php if (!$keywords['time'] || $keywords['time'] == 'all') echo "active"; ?>">
                                    <input type="radio" name="time" value="all" autocomplete="off" <?php if (!$keywords['time'] || $keywords['time'] == 'all') echo "checked"; ?> /> 所有时间
                                </label>
                                <label class="btn btn-default <?php if ($keywords['time'] == 'booking') echo "active"; ?>">
                                    <input type="radio" name="time" value="booking" autocomplete="off" <?php if ($keywords['time'] == 'booking') echo "checked"; ?> /> 预订时间
                                </label>
                                <label class="btn btn-default <?php if ($keywords['time'] == 'start') echo "active"; ?>">
                                    <input type="radio" name="time" value="start" autocomplete="off" <?php if ($keywords['time'] == 'start') echo "checked"; ?> /> 出发时间
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
                                <label>定制卡编号</label>
                                <input type="text" class="form-control" name="order" value="<?php echo $keywords['order']; ?>" />
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>目的地</label>
                                <input type="text" class="form-control" name="area" value="<?php echo $keywords['area']; ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>手机号</label>
                                <input type="text" class="form-control" name="tel" value="<?php echo $keywords['tel']; ?>" />
                            </div>
                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                            <div class="col-sm-6">
                                <label>联系人</label>
                                <input type="text" class="form-control" name="people" value="<?php echo $keywords['people']; ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>状态</label>
                                <select class="form-control ui-select" name="status">
                                    <option value="">请选择..</option>
                                    <?php foreach($status as $k => $v){ ?>
                                    <option value="<?php echo $k; ?>" <?php if($keywords['status'] == $k) echo 'selected'; ?>><?php echo $v; ?></option>
                                    <?php } ?>
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



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>

    <script>
    function tag(id, status) {
        $.post("<?php echo BASE_URL; ?>tourcard.php?method=tag", {id:id, status:status}, function(data){
            if (data.s == 0){
                alert('标记成功，页面即将刷新', 'success', function(){ location.reload(true); });
            } else {
                alert(data.err, 'error');
            }
        }, "json");
    }
    $(function(){

        $("#search-timetype .btn").click(function(){
            var val = $(this).children("input").val();
            if (val == 'all') $("#search-time").slideUp(100);
            else $("#search-time").slideDown(100);
        });

        $(".ui-datepicker").zdatepicker({viewmonths:1, disable:{0:['1900-1-1','<?php echo date('Y-m-d', strtotime('-1 day')); ?>']}});

        $(".ui-select").chosen({width:'100%', disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});
    });
    </script>
</body>
</html>
