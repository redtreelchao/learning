<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 房型配对列表</title>

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

                <h1 class="page-header">房型信息关联 <small><?php echo $hotel['name']; ?></small></h1>


                <div>
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom:20px;">
                        <?php foreach($supplies as $k => $v) { ?>
                        <li class="<?php if($k == $supply) echo ' active';; if(!$hotel[$k]) echo ' disabled'; ?>"><a href="#tab<?php echo $k; ?>" role="tab" data-toggle="tab"><?php echo $v['name']; if(!empty($v['unbind'])) echo ' <span class="badge badge-danger">'.$v['unbind'].'</span>'; ?></a></li>
                        <?php } ?>
                    </ul>

                    <div class="row">

                        <!-- tab content -->
                        <div class="tab-content col-md-8 col-lg-9">

                            <style type="text/css">
                            .btn-default-warning { color:#d9534f; border-color:#d9534f; }
                            .panel-heading { position:relative; }
                            .panel-heading > span { position:absolute; right:10px; top:5px; }
                            .empty-list span { display:inline-block; font-size:12px; padding:7px 10px; border:#ccc dashed 1px; border-radius:5px; line-height:100%; }
                            </style>

                            <?php foreach($supplies as $k => $v) { ?>
                            <div class="tab-pane<?php if($k == $supply) echo ' active' ?>" id="tab<?php echo $k ?>">

                                <?php if (!$roomtypes) { ?>
                                <div class="empty-list" style="line-height:40px; padding-bottom:10px;">
                                    <?php foreach($v['rooms'] as $room) { ?>
                                    <span><?php
                                        echo $room['name'], ' ';
                                        switch ($room['bed']){
                                            case 'S':
                                                $bed  = '(单人床)';
                                                break;
                                            case 'T':
                                                $bed  = '(双床)';
                                                break;
                                            case 'D':
                                                $bed  = '(大床)';
                                                break;
                                            case '2':
                                                $bed  = '(大/双床)';
                                                break;
                                            case '3':
                                                $bed  = '(三床)';
                                                break;
                                            case 'K':
                                                $bed  = '(超大床)';
                                                break;
                                            case 'C':;
                                                $bed  = '(圆床)';
                                                break;
                                            default:
                                                $bed  = '(其他)';
                                        }
                                        echo $bed;
                                    ?></span>
                                    <?php } ?>
                                </div>
                                <?php } ?>

                                <?php foreach($roomtypes as $type) { ?>
                                <div class="panel panel-default type-<?php echo $type['id']; ?>" data-type="<?php echo $type['id']; ?>">
                                    <div class="panel-heading">
                                        <b><?php echo $type['name']; ?></b>
                                        <span>
                                            <a href="javascript:editRoomType(<?php echo $hotel['id']; ?>, <?php echo $type['id']; ?>);" class="btn btn-sm btn-link"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                            <a href="javascript:delRoomType(<?php echo $type['id']; ?>);" class="btn btn-sm btn-link-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
                                        </span>
                                    </div>
                                    <div class="panel-body" style="line-height:40px;">
                                        <?php
                                        foreach($v['rooms'] as $room) {
                                            $class = array('room-'.$room['id']);

                                            if(!$room['type'] && !$room['isdel']) {
                                                $class[] = 'btn-default';
                                                $class[] = 'btn-default-warning';
                                            } else if ($room['type'] == $type['id']) {
                                                $class[] = 'btn-primary';
                                            } else {
                                                $class[] = 'btn-default';
                                            }

                                            if($room['isdel']) $class[] = 'disabled';

                                            switch ($room['bed']){
                                                case 'S':
                                                    $bed  = '(单人床)';
                                                    break;
                                                case 'T':
                                                    $bed  = '(双床)';
                                                    break;
                                                case 'D':
                                                    $bed  = '(大床)';
                                                    break;
                                                case '2':
                                                    $bed  = '(大/双床)';
                                                    break;
                                                case '3':
                                                    $bed  = '(三床)';
                                                    break;
                                                case 'K':
                                                    $bed  = '(超大床)';
                                                    break;
                                                case 'C':;
                                                    $bed  = '(圆床)';
                                                    break;
                                                default:
                                                    $bed  = '(其他)';
                                            }
                                        ?>
                                        <button class="btn btn-sm <?php echo implode(' ', $class); ?>" data-id="<?php echo $room['id']; ?>" <?php if($room['isdel']) echo 'disabled '; ?>><?php
                                            if ($room['isdel']) echo '<del>';
                                            echo $room['name'], ' ', $bed;
                                            if ($room['isdel']) echo '<del>';
                                        ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>

                                <div class="empty">
                                    如果没有合适房型，点此<a href="javascript:newRoomType(<?php echo $hotel['id']; ?>);">新建房型</a>
                                </div>

                            </div>
                            <?php } ?>
                        </div>
                        <!-- tab content -->

                        <!-- Right Bar -->
                        <div class="col-md-4 col-lg-3">

                            <!-- panel -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">操作记录</h3>
                                </div>
                                <ul class="list-group history">
                                    <?php if ($history) {  ?>
                                        <?php foreach ($history as $k => $v) { ?>
                                        <li class="list-group-item">
                                            <span class="log"><b><?php echo $v['username']; ?>&nbsp;</b><?php echo $v['intro']; ?></span>
                                            <span class="time"><?php echo date('m-d H:i', $v['time']); ?></span>
                                        </li>
                                        <?php } ?>
                                        <?php if (count($history) == 10) { ?>
                                        <li class="list-group-item text-center">
                                            <a class="c9" href="javascript:;" onclick="history(this)" data-url="<?php echo BASE_URL; ?>hotel.php?method=history&hotel=<?php echo $hotel['id']; ?>" data-page="2">查看更多记录</a>
                                        </li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <li class="list-group-item text-center c9">无操作记录</li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <!-- panel -->

                        </div>
                        <!-- Right Bar -->

                    </div>
                </div>


            </div>
        </div>
        <!-- end main -->

    </div>



    <div id="form-room" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">增加新房型</h4>
                </div>
                <form class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">名称</label>
                        <div class="col-sm-8">
                            <input type="text" id="room-name" name="name" class="form-control" value="" />
                        </div>
                        <div class="col-sm-10 col-md-offset-2">
                            <p class="help-block">为了便于自动匹配和动态房型，请勿输入"双床","大床","房","间"等字样，除"套房"等。</p>
                        </div>
                    </div>

                    <input type="hidden" id="hotel-id" name="hotel" value="" />
                    <input type="hidden" id="room-id" name="id" value="" />

                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" data-loading-text="保存中.." onclick="saveRoomType(this)">保存</button>
                </div>
            </div>
        </div>
    </div>



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
    function newRoomType(hotel){
        $("#form-room .modal-title").text('增加新房型');
        $("#hotel-id").val(hotel);
        $("#room-id").val(0);
        $("#room-name").val('');
        $("#form-room").modal();
    }
    function editRoomType(hotel, id){
        $("#form-room .modal-title").text('编辑房型');
        $("#hotel-id").val(hotel);
        $("#room-id").val(id);
        $("#room-name").val($(".type-"+id+":eq(0) .panel-heading b").text());
        $("#form-room").modal();
    }
    function delRoomType(id) {
        $.post("<?php echo BASE_URL; ?>room.php?method=del", {id:id}, function(data){
            if(data.s == 0){
                $('.type-'+id).fadeOut(500, function(){ $(this).remove(); });
            }else{
                alert(data.err);
            }
        }, "json");
    }
    function saveRoomType(btn){
        var name = $("#room-name").val();
        var id = $("#room-id").val();
        if (!name) alert("请输入内容", "", null, "#form-room .modal-body");
        $(btn).button('loading');
        $.post("<?php echo BASE_URL; ?>room.php?method=edit", $("#form-room form").serialize(), function(data){
            $(btn).button("reset");
            if(data.s == 0){
                if(id > 0){
                    $(".type-"+id+" .panel-heading b").text(data.rs.name);
                }else{
                    $(".tab-pane").each(function(){
                        if ($(this).find(".panel:eq(0)").length){
                            var tmp = $(this).find(".panel:eq(0)").clone();
                            tmp.removeClass().addClass("panel panel-default type-"+data.rs.id);
                            tmp.attr("data-type", data.rs.id).data("type", data.rs.id);
                            tmp.find(".panel-heading b").text(data.rs.name);
                            tmp.find(".panel-heading a:eq(0)").attr("href", "javascript:editRoomType(<?php echo $hotel['id']; ?>, "+data.rs.id+");");
                            tmp.find(".panel-heading a:eq(1)").attr("href", "javascript:delRoomType("+data.rs.id+");");
                            tmp.find(".panel-body .btn-primary").removeClass("btn-primary").addClass("btn-default");
                            $(this).find(".empty").before(tmp);
                        } else {
                            location.reload();
                        }
                    });
                }
                $("#form-room").modal("hide");
            }else{
                alert(data.err, "", null, "#form-room .modal-body");
            }
        }, "json");
    }
    $(function(){
        $(".tab-content .panel-body").on("click", ".btn-sm", function(){
            var btn = $(this);
            var panel = btn.parents(".panel").eq(0);
            var type = panel.data("type");
            var id = btn.data("id");
            btn.append("<span class=\"glyphicon glyphicon-refresh glyphicon-loading\" style=\"margin-left:5px;\"></span>");
            $.post("<?php echo BASE_URL; ?>room.php?method=bind", {id:id, type:type}, function(data){
                btn.children(".glyphicon-refresh").remove();
                if(data.s == 0){
                    console.log(btn.is(".btn-primary"));
                    if (btn.is(".btn-primary")) {
                        $(".panel-body .room-"+id).removeClass("btn-primary").addClass("btn-default btn-default-warning");
                    } else {
                        $(".panel-body .room-"+id).removeClass("btn-primary btn-default-warning").addClass("btn-default");
                        btn.addClass("btn-primary").removeClass("btn-default btn-default-warning");
                    }
                }else{
                    alert(data.err);
                }
            }, "json");
        });

        $(".nav-tabs a").on("shown.bs.tab", function (e) {
            location.hash = $(e.target).attr("href").substr(4);
        });

        if (location.hash) {
           $(".nav-tabs a[href='#tab"+location.hash.substr(1)+"']").tab("show");
        }
    });
    </script>
</body>
</html>
