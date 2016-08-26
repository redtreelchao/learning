<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 国家城市列表</title>

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

                <h1 class="page-header"><?php echo isset($country) ? '城市列表 <small>'.$country['name'].'</small>' : '国家列表'; ?></h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px; margin-right:10px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:add();">添加新<?php echo isset($country) ? '城市' : '国家'; ?></a></li>
                                <li><a href="#">移至回收站</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <?php echo isset($country) ? "<input type=\"hidden\" name=\"id\" value=\"{$country['id']}\" />" : ''; ?>
                            <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" />
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
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th><?php echo isset($country) ? '城市' : '国家'; ?>名</th>
                                <th>拼音</th>
                                <th>英文</th>
                                <?php if(isset($country)){ ?><th>坐标</th><?php } ?>
                                <?php if(isset($country)){ ?><th>州/省</th><?php } ?>
                                <th width="15%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th><?php echo isset($country) ? '城市' : '国家'; ?>名</th>
                                <th>拼音</th>
                                <th>英文</th>
                                <?php if(isset($country)){ ?><th>坐标</th><?php } ?>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['id']; ?>">
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']; ?>" /></td>
                                <td title="ID:<?php echo $v['id']; ?>"><?php echo $v['name']; ?></td>
                                <td><?php echo $v['pinyin']; ?></td>
                                <td><?php echo $v['en']; ?></td>
                                <?php if(isset($country)){ ?><td><?php echo $v['lng'],',',$v['lat']; ?></td><?php } ?>
                                <?php if(isset($country)){ ?><td><?php echo $v['province']; ?></td><?php } ?>
                                <td class="md-nowrap">
                                    <?php if(!isset($country)){ ?>
                                    <a href="<?php echo BASE_URL; ?>district.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-eye-open hidden-md"></span><span class="hidden-xs hidden-sm"> 城市</span></a>
                                    <?php } else { ?>
                                    <a href="<?php echo BASE_URL; ?>district_extend.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-eye-open hidden-md"></span><span class="hidden-xs hidden-sm"> 区域</span></a>
                                    <?php } ?>
                                    <a href="javascript:edit(<?php echo $v['id']; ?>);" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                </td>
                            </tr>
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
    <div id="form-district" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">新增<?php echo isset($country) ? '城市' : '国家'; ?></h4>
                </div>
                <form class="modal-body form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo isset($country) ? '城市' : '国家'; ?>名:</label>
                        <div class="col-sm-7">
                            <input name="name" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">拼音:</label>
                        <div class="col-sm-7">
                            <input name="pinyin" class="form-control" placeholder="字音首字母大写" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">英文:</label>
                        <div class="col-sm-7">
                            <input name="en" class="form-control" value="" />
                        </div>
                    </div>

                    <?php if(isset($country)){ ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">坐标:</label>
                        <div class="col-sm-7">
                            <input name="pos" class="form-control" placeholder="经度,纬度" value="" />
                        </div>

                        <div class="col-sm-7 col-sm-offset-3">
                            <span class="help-block">
                                <?php if($country['id'] == 1) { ?>
                                国内城市坐标请使用<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">百度地图工具</a>。
                                <?php } else { ?>
                                国际城市坐标请翻墙访问<a href="http://www.google.com/maps/" target="_blank">谷歌地图</a>，直接搜索地名。坐标将显示在地址栏。
                                <?php } ?>
                            </span>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if(isset($country)){ ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">州/省:</label>
                        <div class="col-sm-7">
                            <input name="province" class="form-control" placeholder="" value="" />
                        </div>
                    </div>
                    <?php } ?>

                    <input type="hidden" name="id" id="district-id" value="" />
                    <input type="hidden" name="pid" value="<?php echo isset($country) ? $country['id'] : '0'; ?>" />

                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" data-loading-text="保存中..">保存</button>
                </div>
            </div>
        </div>
    </div>



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script>
        function add(){
            $("#form-district .modal-title").text("新增<?php echo isset($country) ? '城市' : '国家'; ?>");
            $("#form-district form").get(0).reset();
            $("#form-district").modal("show");
        }

        function edit(id){
            $("#form-district .modal-title").text("修改<?php echo isset($country) ? '城市' : '国家'; ?>");
            $("#form-district").modal("show");
            $.get("<?php echo BASE_URL; ?>district.php", {method:"load", id:id}, function(data){
                if (data.s == 0){
                    $("#form-district input").each(function(){
                        var input = $(this);
                        var name = input.attr("name");
                        if (data.rs[name] !== undefined) input.val(data.rs[name]);
                        else if (name == "pos") input.val(data.rs['lng']+","+data.rs['lat']);
                        else input.val("");
                    });
                } else {
                    alert(data.err, "", null, "#form-district .modal-body");
                }
            }, "json");
        }


        $("#form-district .modal-footer .btn-primary").bind("click", function(){
            var btn = $(this);
            btn.button("loading");
            $.post("<?php echo BASE_URL; ?>district.php?method=save", $("#form-district form").serialize(), function(data){
                $(btn).button("reset");
                if(data.s == 0){
                    alert("保存成功，稍后将自动刷新页面..", "success", function(){ location.reload(); });
                    $("#form-district").modal("hide");
                }else{
                    alert(data.err, "", null, "#form-district .modal-body");
                }
            }, "json");
        });
    </script>
</body>
</html>
