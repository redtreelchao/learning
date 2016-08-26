<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 房型管理</title>

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

                <h1 class="page-header">房型管理</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php if(!isset($unbind)){ ?>
                                <li><a href="<?php echo BASE_URL; ?>room.php?unbind=1">查看含未配对房型酒店</a></li>
                                <?php }else{ ?>
                                <li><a href="<?php echo BASE_URL; ?>room.php">查看全部酒店</a></li>
                                <?php } ?>
                                <?php foreach($supplies as $k => $v) { ?>
                                <li><a target="_blank" href="javascript:refresh('<?php echo $k; ?>');">同步<?php echo $v; ?>房型</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <?php if(isset($unbind)){ ?><input type="hidden" name="unbind" value="1" /><?php } ?>
                            <input type="text" name="keyword" class="form-control" value="<?php if(isset($keyword)) echo $keyword; ?>" />
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
                                <th>酒店</th>
                                <th width="20%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>酒店</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr>
                                <td colspan="2" class="bgf9">
                                    <span title="ID:<?php echo $v['id']; ?>"><?php echo $v['name']; ?></span><?php if($v['unbind']){ echo ' <span class="badge badge-danger" title="未配对数量">'.$v['unbind'].'</span>'; } ?>
                                    <span class="hidden-xs hidden-sm">&nbsp;&nbsp;</span>
                                    <span class="info visible-xs-block visible-sm-block visible-md-inline visible-lg-inline">
                                        <?php echo $v['country'].' '.$v['province'].' '.$v['city'].' '.$v['address']; ?>
                                    </span>
                                </td>
                                <td class="md-nowrap bgf9">
                                    <a href="javascript:newRoomType(<?php echo $v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-plus hidden-md"></span><span class="hidden-xs hidden-sm"> 新建房型</span></a>
                                    <?php if($v['HMC'] || $v['JLT'] || $v['CNB']){ ?>
                                    <a href="<?php echo BASE_URL; ?>room.php?method=bind&hotel=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-link hidden-md"></span><span class="hidden-xs hidden-sm"> 关联匹配</span></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php if(!$v['rooms']){ ?>
                            <tr class="rooms-<?php echo $v['id']; ?>">
                                <td colspan="3" class="empty">
                                    <div>没有房型信息，点此<a href="javascript:newRoomType(<?php echo $v['id']; ?>);">新建房型</a></div>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php foreach($v['rooms'] as $room) { ?>
                            <tr id="room-<?php echo $room['id']; ?>" class="rooms-<?php echo $v['id']; ?> item">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $room['id']; ?>" /></td>
                                <td title="ID:<?php echo $room['id']; ?>">
                                    <?php echo $room['name']; ?>
                                    <span class="info">　　<?php echo $room['name'] != str_replace(array('阁','间','房','别墅'), '', $room['name']) ? '房型不聚合' : ''; ?></span>
                                </td>
                                <td class="md-nowrap">
                                    <a href="javascript:editRoomType(<?php echo $v['id']; ?>, <?php echo $room['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                    <a href="javascript:delRoomType(<?php echo $room['id']; ?>);" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php } ?>
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
                                <?php foreach($supplies as $k => $v) { ?>
                                <li><a target="_blank" href="javascript:refresh('<?php echo $k; ?>');">同步<?php echo $v; ?>房型</a></li>
                                <?php } ?>
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
                            <p class="help-block">为了便于自动匹配和动态房型，请勿输入"双床","大床","房","间"等字样。</p>
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


    <div id="refresh-room" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">同步数据</h4>
                </div>
                <form class="modal-body">
                   <iframe id="iframe" src="" frameborder="0" style="width:100%; height:300px;"></iframe>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>



    <script type="text/html" id="room-tpl">
    <tr id="room-{id}" class="rooms-{hotel}">
        <td><input type="checkbox" class="checkbox" value="{id}" /></td>
        <td title="ID:{id}">{name}</td>
        <td class="md-nowrap">
            <a href="javascript:editRoomType({hotel}, {id});" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
            <a href="javascript:delRoomType({id});" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash hidden-md"></span><span class="hidden-xs hidden-sm"> 删除</span></a>
        </td>
    </tr>
    </script>

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
    var scrollTimer;
    function refresh(sup){
        $("#refresh-room").modal();
        $("#iframe").attr("src", "<?php echo BASE_URL; ?>room.php?method=refresh&sup="+sup);
        $("#iframe").load(function(){
            ($("#iframe")[0].contentWindow || $("#iframe")[0]).scroll(0, 99999);
            clearTimeout(scrollTimer);
        });
        if(!scrollTimer) scrollTime();
    }
    function scrollTime(){
        ($("#iframe")[0].contentWindow || $("#iframe")[0]).scroll(0, 99999);
        scrollTimer = setTimeout('scrollTime()', 1000);
    }
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
        $("#room-name").val($.trim($("#room-"+id+" td:eq(1)").text()));
        $("#form-room").modal();
    }
    function delRoomType(id) {
        $.post("<?php echo BASE_URL; ?>room.php?method=del", {id:id}, function(data){
            if(data.s == 0){
                $('#room-'+id).fadeOut(500, function(){ $(this).remove(); });
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
                    $("#room-"+id+" td:eq(1)").text(data.rs.name);
                }else{
                    var tmpl = $("#room-tpl").html();
                    var reg = new RegExp("\\{([0-9a-zA-Z_-]*?)\\}", 'gm');
                    var row = $(tmpl.replace(reg, function (node, key) { return data.rs[key]; }));
                    $(".rooms-"+data.rs.hotel+":last").after(row);
                    $(".rooms-"+data.rs.hotel+" .empty").parent().remove();
                }
                $("#form-room").modal("hide");
            }else{
                alert(data.err, "", null, "#form-room .modal-body");
            }
        }, "json");
    }
    </script>
</body>
</html>
