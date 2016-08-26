<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 供应商列表</title>

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
    <style>
        .table .btn { margin-top: 4px; }
        .form-search .control-label { margin-top: 7px; }
        #search label { display: block; }
        #search .chosen-container { width: 100% !important; }
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


                <h1 class="page-header">供应商列表</h1>

                <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">

                         <div class="btn-group" style="margin:20px 0px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo BASE_URL; ?>supply.php?method=edit" data-pjax-container="#main">添加供应商</a></li>
                                <li class="search"><a href="javascript:void(0);">高级搜索</a></li>
                            </ul>
                        </div>

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
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

                <!-- <form class="row form-search" >
                    <div class="col-md-2 form-group">
                        <label class="col-xs-5 control-label">选择国家</label>
                        <div class="col-xs-7">
                            <select name="country" id="country" class="form-control ui-select">
                                <option value="">请选择国家</option>
                                <?php foreach ($countries as $k => $v) { ?>
                                <option value="<?php echo $k?>" <?php echo (isset($param['country'])&&$param['country']==$k)?'selected':'' ?>><?php echo $v?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 form-group">
                        <label class="col-xs-5 control-label">选择城市</label>
                        <div class="col-xs-7">
                            <select name="city" id="city" class="form-control ui-select">
                                <option value="">请选择城市</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="col-xs-5 control-label">选择区域</label>
                        <div class="col-xs-7">
                            <select name="area" id="selCity" class="form-control ui-select">
                                <option value=""></option>
                                <?php foreach ($areas as $k => $v) { ?>
                                    <option value="<?php echo $k?>"><?php echo $v?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 form-group">
                        <input value="<?php echo  isset($param['code'])?$param['code']:''; ?>" type="text" name="code" class="form-control" placeholder="供应商ID">
                    </div>
                    <div class="col-md-1 form-group">
                        <input value="<?php echo  isset($param['name'])?$param['name']:''; ?>" type="text" name="name" class="form-control" placeholder="供应商名称">
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="col-xs-5 control-label">结算方式</label>
                        <div class="col-xs-7">
                            <select name="payby" id="" class="form-control ui-select">
                            <?php foreach ($payby as $k => $v) { ?>
                                <option value="<?php echo $k?>" <?php echo  (isset($param['payby'])&&$param['payby']==$k)?'selected':''; ?> >
                                    <?php echo $v?>
                                </option>
                            <?php }?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                    </div>
                </form> -->

                <div class="table-responsive">
                    <table class="table table-striped table-hover f12">
                        <thead>
                            <tr>
                                <th>供应商</th>
                                <th>结算方式</th>
                                <th width="20%">操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>供应商</th>
                                <th>结算方式</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach($list as $k => $v){ ?>
                            <tr id="row-<?php echo $v['id'];?>">
                                <td title="ID:<?php echo $v['code']?>">
                                    <?php echo $v['name']?><br />
                                    <span class="info">
                                        <?php echo isset($countries[$v['countryid']])?$countries[$v['countryid']]:'' ?> <?php echo $v['city']?> <?php echo $v['areaname']?>
                                    </span>
                                </td>
                                <td><?php echo $payby[$v['payby']];?></td>
                                <td class="md-nowrap" >
                                    <a href="<?php echo BASE_URL; ?>supply.php?method=edit&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-default" data-pjax-container="#main"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                    <a href="javascript:del(<?php echo $v['id'];?>)"  class="btn btn-sm btn-danger" data-pjax-container="#main">
                                        <span class="glyphicon glyphicon-trash hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 删除</span>
                                    </a>
                                </td>
                            </tr>
                        <?php  } ?>

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
                                 <li><a href="<?php echo BASE_URL; ?>supply.php?method=edit" data-pjax-container="#main">添加供应商</a></li>
                            </ul>
                        </div>
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

                 <!-- modal -->
                <div id="search" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

                    <form class="row form-search">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">高级搜索</h4>
                                </div>
                                <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>选择国家</label>
                                                <select name="country" id="country" class="form-control ui-select">
                                                    <option value="">请选择国家</option>
                                                    <?php foreach ($countries as $k => $v) { ?>
                                                    <option value="<?php echo $k?>" <?php echo (isset($param['country'])&&$param['country']==$k)?'selected':'' ?>><?php echo $v?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                                            <div class="col-sm-6">
                                                <label>选择城市</label>
                                                <select name="city" id="city" class="form-control ui-select">
                                                    <option value="">请选择城市</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>选择区域</label>
                                                <select name="area" id="selCity" class="form-control ui-select">
                                                    <option value=""></option>
                                                    <?php foreach ($areas as $k => $v) { ?>
                                                        <option value="<?php echo $k?>"><?php echo $v?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                                            <div class="col-sm-6">
                                                <label>供应商ID</label>
                                                <input value="<?php echo  isset($param['code'])?$param['code']:''; ?>" type="text" name="code" class="form-control" placeholder="供应商ID">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>供应商名称</label>
                                                <input value="<?php echo  isset($param['name'])?$param['name']:''; ?>" type="text" name="name" class="form-control" placeholder="供应商名称">
                                            </div>
                                            <hr class="hidden-sm hidden-md hidden-lg" style="margin:0px; border:0px; margin-top:15px;" />
                                            <div class="col-sm-6">
                                                <label>结算方式</label>
                                                <select name="payby" id="" class="form-control ui-select">
                                                    <?php foreach ($payby as $k => $v) { ?>
                                                        <option value="<?php echo $k?>" <?php echo  (isset($param['payby'])&&$param['payby']==$k)?'selected':''; ?> >
                                                            <?php echo $v?>
                                                        </option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default reset">清除</button>
                                    <button type="submit" class="btn btn-primary">搜索</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- modal -->

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script>
        function del(id){
            $.post("<?php echo BASE_URL; ?>supply.php?method=del", {pid:id}, function(data){
                if(data.s == 0){
                    $('#row-'+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err);
                }
            }, "json");
        }

        $(function(){
            $(".ui-select").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});
        })


        $('.reset').click(function(){
            $('#search form').get(0).reset();
            $('#search').modal('hide');
        })

        $("#country").change(function(){
            var pid = $(this).val();
            if(pid>0)
            {
                $.post("./index.php?method=city", {pid:pid}, function(data){
                    if (data.s == 0) {
                        $("#city").html('');
                        for(x in data.rs){
                            var opt = $("<option />");
                            opt.text(data.rs[x].name).attr("value", data.rs[x].id);
                            $("#city").append(opt);
                        }
                        $("#city").trigger('chosen:updated');
                    }else{
                        alert("城市读取异常，请重试", "error");
                    }
                }, "json");
            }

        });

        $('.search').click(function(){
            $('#search').modal('show');
        })

    </script>

</body>
</html>