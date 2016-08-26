<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; <?php echo $id ? '编辑' : '添加'; ?>生鲜/商品信息</title>

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

                <h1 class="page-header"><?php echo $id ? '编辑' : '添加'; ?>生鲜/商品信息</h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">生鲜/商品名称</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" class="form-control"  value="<?php echo $data['name']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">类型</label>
                            <div class="col-sm-3">
                                <select name="type" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <option value="水果">水果</option>
                                    <option value="海鲜水产">海鲜水产</option>
                                    <option value="猪肉羊肉">猪肉羊肉</option>
                                    <option value="禽肉蛋品">禽肉蛋品</option>
                                    <option value="熟食腊味">熟食腊味</option>
                                    <option value="冷冻食品">冷冻食品</option>
                                    <option value="蔬菜">蔬菜</option>
                                    <option value="饮品甜品">饮品甜品</option>
                                    <option value="其他">其他</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">产地</label>
                            <div class="col-sm-3">
                                <input type="text" name="made" class="form-control"  value="<?php echo $data['made']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">供应商</label>
                            <div class="col-sm-3">
                                <input type="text" name="supply" class="form-control"  value="<?php echo $data['supply']; ?>" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $id ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>

                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
                            </div>
                            <?php if(!$data) { ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-primary btn-sm" onclick="save()">新建</button>
                            </div>
                            <?php }else{ ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="history.go(-1)">返回</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="save()">保存</button>
                            </div>
                            <?php } ?>
                        </div>


                    </div>

                </form>
                <!-- end form -->

            </div>
        </div>
        <!-- end main -->

    </div>


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script>
    function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>goods.php?method=edit", postdata, function(data){
            if(data.s == 0){
                <?php if($data){ ?>
                alert("保存成功", "success");
                <?php }else{ ?>
                location.href = "<?php echo BASE_URL; ?>goods.php?method=edit&id="+data.rs+"#success";
                <?php } ?>
            }else{
                alert(data.err, 'error');
            }
        }, "json");
    }

    $(function(){

        if(location.hash == "#success"){
            alert("信息保存成功!", "success");
            location.hash = "";
        }

        $("select[name=type]").val("<?php echo $data['type']; ?>");

        $(".ui-select").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});
    });
    </script>
</body>
</html>
