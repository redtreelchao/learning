<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; HMC报价与房态</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/month.css" rel="stylesheet" />

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

                <h1 class="page-header">HMC报价与房态</h1>

                <form class="form-inline" action="<?php echo BASE_URL; ?>hmc.php" method="GET" style="padding-bottom:10px; line-height:40px;">
                    <input type="hidden" name="method" value="price" />

                    <div class="form-group">
                        <label class="sr-only">选择酒店</label>
                        <select id="hotel" name="hotel" class="form-control">
                            <option value="">选择酒店..</option>
                            <?php foreach($hotels as $h) { ?>
                            <option <?php echo $h['id'] == $hotel ? 'selected="selected"' : ''; ?> value="<?php echo $h['id'] ?>"><?php echo $h['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="sr-only">选择房型</label>
                        <select id="room" name="room" class="form-control" style="min-width:200px;">
                            <option value="">选择房型..</option>
                            <?php
                            if (isset($rooms)) {
                                foreach($rooms as $r) {
                            ?>
                            <option <?php echo $r['room'].'_'.$r['bed'] == $room ? 'selected="selected"' : ''; ?> value="<?php echo $r['room'].'_'.$r['bed']; ?>"><?php echo $r['roomname']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="clear-fix hidden-lg"></div>

                    <div class="form-group">
                        <label class="sr-only">入住日期</label>
                        <input id="checkin" class="form-control ui-datepicker" name="checkin" value="<?php echo date('Y-m-d', $checkin); ?>" placeholder="入住日期" />
                    </div>

                    <div class="form-group">
                        <label class="sr-only">离店日期</label>
                        <input id="checkout" class="form-control ui-datepicker" name="checkout" value="<?php echo date('Y-m-d', $checkout); ?>" placeholder="离店日期" />
                    </div>

                    <button type="submit" class="btn btn-primary hidden-xs"><span class="glyphicon glyphicon-search"></span></button>
                    <button type="submit" class="btn btn-primary btn-block visible-xs-block"><span class="glyphicon glyphicon-search"></span> 搜索</button>
                </form>

                <div class="table-responsive">

                </div>

                <div class="row">
                    <div class="col-xs-12 month" id="month">
                        <?php
                            $today = strtotime('today');
                            $start = strtotime(date('Y-m-1', $checkin));
                            $end = strtotime(date('Y-m-t', $checkout));
                            $space_s = date('N', $start) - 1;
                        ?>
                        <ul><li class="week">一</li><li class="week">二</li><li class="week">三</li><li class="week">四</li><li class="week">五</li><li class="week weekend">六</li><li class="week weekend">日</li>
                            <?php
                            for ($i = $start; $i <= $end; $i = $i + 86400)
                            {
                                if ($i == $start)
                                {
                                    for($j = 1; $i <= $space_s; $j ++)
                                    {
                                        echo '<li class="space"></li>';
                                    }
                                }

                                $class = '';
                                if ($i < $today) $class .= ' disabled';


                                echo '<li class="'.$class.'"><b>'.date('j', $i).'</b><div>';

                                if (isset($data[$i]))
                                {
                                    foreach ($data[$i] as $price)
                                    {
                                        $title = "早餐{$price['breakfast']}份; "
                                                .($price['advance'] ? "需提前{$price['advance']}日预订; " : "")
                                                .($price['min'] ? "需连住{$price['min']}日; " : "")
                                                .($price['start'] && !$price['end'] ? date('n月j', $price['start']).'日起可预订; ' : '')
                                                .($price['start'] && $price['end'] ? date('n月j', $price['start']).'日至'.date('n月j', $price['end']).'可预订; ' : '')
                                                .(!$price['start'] && $price['end'] ? date('n月j', $price['end']).'日前可预订; ' : '')
                                                .($price['nation'] ? "{$price['nation']}; " : "")
                                                .($price['package'] ? "{$price['package']}; " : "");

                                        if ($price['allot'])
                                        {
                                            echo '<span class="label label-success" data-toggle="tooltip" data-placement="top" title="'.$title.'">¥'.$price['price'].'</span>';
                                        }
                                        else if ($price['filled'])
                                        {
                                            echo '<span class="label label-danger" data-toggle="tooltip" data-placement="top" title="'.$title.'">¥'.$price['price'].'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="label label-warning" data-toggle="tooltip" data-placement="top" title="'.$title.'">¥'.$price['price'].'</span>';
                                        }
                                    }
                                }

                                echo '</div></li>';

                                if ($i == date('t', $i))
                                {
                                    echo '</ul><ul><li class="week">一</li><li class="week">二</li><li class="week">三</li><li class="week">四</li><li class="week">五</li><li class="week weekend">六</li><li class="week weekend">日</li>';
                                }
                            }
                            ?>
                        </ul>
                        <?php

                        ?>
                    </div>
                </div>


            </div>
        </div>
        <!-- end main -->

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
        $("#hotel").chosen({disable_search_threshold:10, no_results_text: "未找到..", placeholder_text_single:"选择酒店.."});
        $("#room").chosen({disable_search_threshold:10, no_results_text: "未找到..", placeholder_text_single:"选择房型.."});

        $("#hotel").change(function(){
            var hotel = $(this).val();
            $.get("<?php echo BASE_URL; ?>hmc.php", {id:hotel,method:"room"}, function(data){
                if (data.s == 0){
                    var room = $("#room");
                    room.html("<option value=\"\">选择房型..</option>");
                    for(x in data.rs){
                        room.append("<option value=\""+data.rs[x].room+"_"+data.rs[x].bed+"\">"+data.rs[x].roomname+"("+data.rs[x].bedname+")</option>");
                    }
                    room.trigger('chosen:updated');
                }else{
                    alert("读取房型失败，请重试", "error");
                }
            }, "json");
        });

        $("#checkin").zdatepicker({
            viewmonths:1,
            disable:{0:{0:"1970-1-1",1:"<?php echo date('Y-m-d', strtotime('yesterday')); ?>"}},
            area:{0:"", 1:$("#checkout")}
        });

        $("#checkout").zdatepicker({
            viewmonths:1,
            initmonth:$("#checkin"),
            disable:{0:{0:"1970-1-1",1:"<?php echo date('Y-m-d', strtotime('yesterday')); ?>"}},
            area:{0:$("#checkin"), 1:""}
        });

        $("#month .label").tooltip({trigger:"hover",container:"body"});

    });
    </script>
</body>
</html>
