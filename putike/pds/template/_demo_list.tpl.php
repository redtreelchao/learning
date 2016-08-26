<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; List Demo</title>

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
    <link href="<?php echo RESOURCES_URL; ?>js/respond-proxy.html" id="respond-proxy" rel="respond-proxy" />
    <![endif]-->
</head>
<body>

    <!-- header -->
    <?php include(dirname(__FILE__).'/header.tpl.php'); ?>
    <!-- end header -->


    <div class="container-fluid">
        <div class="row">

            <!-- sidebar -->
            <?php include(dirname(__FILE__).'/sidebar.tpl.php'); ?>
            <!-- end sidebar -->

            <!-- main -->
            <div class="col-sm-11 col-sm-offset-1 col-md-10 col-md-offset-2 main">

                <h1 class="page-header">Demo</h1>
                <!-- 复制代码请保留 非 中文注释 -->

                <ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px;">
                  <li class="active"><a href="<?php BASE_URL; ?>demo.php">List Demo</a></li>
                  <li><a href="<?php BASE_URL; ?>demo.php?type=form">Form Demo</a></li>
                </ul>

                <!-- 状态提示 -->
                <div class="alert alert-success" role="alert">
                    <strong>成功!</strong> 资料保存成功，正在返回列表..
                </div>
                <div class="alert alert-warning" role="alert">
                    <strong>警告!</strong> 数据已发生更改，<a href="" class="alert-link" target="_blank">点击查看</a>最新资料。
                </div>
                <div class="alert alert-danger" role="alert">
                    <strong>错误!</strong> 资料保存失败，请重试~
                </div>
                <!-- end 状态提示 -->


                <!-- 副标题（如果需要的话） -->
                <h2 class="sub-header">一般列表</h2>
                <!-- end 副标题 -->


                <!-- 数据列表 -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>Header</th>
                                <th>Header</th>
                                <th>状态</th>
                                <th>
                                    操作
                                    <button type="button" disabled="disabled" class="btn btn-sm btn-default">不可用</button>
                                    <button type="button" disabled="disabled" class="btn btn-sm btn-link">不可用</button>
                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>Header</th>
                                <th>Header</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>Lorem</td>
                                <td>ipsum</td>
                                <td>
                                    <span class="label label-default">正常</span>
                                </td>
                                <td class="md-nowrap">
                                    <button type="button" class="btn btn-sm btn-default">默认</button>
                                    <button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 默认</button>
                                    <button type="button" class="btn btn-sm btn-link">链接</button>
                                    <button type="button" class="btn btn-sm btn-link btn-link-default">彩字</button>
                                    <button type="button" class="btn btn-sm btn-link btn-gray-default">灰调</button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>amet</td>
                                <td>consectetur</td>
                                <td>
                                    <span class="label label-primary">主要</span>
                                </td>
                                <td class="md-nowrap">
                                    <button type="button" class="btn btn-sm btn-primary">主要</button>
                                    <button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-asterisk hidden-md"></span><span class="hidden-xs hidden-sm"> 主要</span></button>
                                    <button type="button" class="btn btn-sm btn-link">链接</button>
                                    <button type="button" class="btn btn-sm btn-link btn-link-primary">彩字</button>
                                    <button type="button" class="btn btn-sm btn-link btn-gray-primary">灰调</button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>Integer</td>
                                <td>nec</td>
                                <td>
                                    <span class="label label-success">成功</span>
                                </td>
                                <td class="md-nowrap">
                                    <button type="button" class="btn btn-sm btn-success">通过</button>
                                    <button type="button" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok hidden-md"></span><span class="hidden-xs hidden-sm"> 通过</span></button>
                                    <button type="button" class="btn btn-sm btn-link">链接</button>
                                    <button type="button" class="btn btn-sm btn-link btn-link-success">彩字</button>
                                    <button type="button" class="btn btn-sm btn-link btn-gray-success">灰调</button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>Integer</td>
                                <td>nec</td>
                                <td>
                                    <span class="label label-info">信息</span>
                                </td>
                                <td class="md-nowrap">
                                    <button type="button" class="btn btn-sm btn-info">资料</button>
                                    <button type="button" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-th-list hidden-md"></span><span class="hidden-xs hidden-sm"> 资料</span></button>
                                    <button type="button" class="btn btn-sm btn-link">链接</button>
                                    <button type="button" class="btn btn-sm btn-link btn-link-info">彩字</button>
                                    <button type="button" class="btn btn-sm btn-link btn-gray-info">灰调</button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>Integer</td>
                                <td>nec</td>
                                <td>
                                    <span class="label label-warning">警告</span>
                                </td>
                                <td class="md-nowrap">
                                    <button type="button" class="btn btn-sm btn-warning">警告</button>
                                    <button type="button" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-warning-sign hidden-md"></span><span class="hidden-xs hidden-sm"> 警告</span></button>
                                    <button type="button" class="btn btn-sm btn-link">链接</button>
                                    <button type="button" class="btn btn-sm btn-link btn-link-warning">彩字</button>
                                    <button type="button" class="btn btn-sm btn-link btn-gray-warning">灰调</button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>Integer</td>
                                <td>nec</td>
                                <td>
                                    <span class="label label-danger">危险</span>
                                </td>
                                <td class="md-nowrap">
                                    <button type="button" class="btn btn-sm btn-danger">危险</button>
                                    <button type="button" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove hidden-md"></span><span class="hidden-xs hidden-sm"> 危险</span></button>
                                    <button type="button" class="btn btn-sm btn-link">链接</button>
                                    <button type="button" class="btn btn-sm btn-link btn-link-danger">彩字</button>
                                    <button type="button" class="btn btn-sm btn-link btn-gray-danger">灰调</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end 数据列表 -->




                <!-- 副标题（如果需要的话） -->
                <h2 class="sub-header">高级列表</h2>
                <!-- end 副标题 -->


                <!-- 数据列表 -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th width="120">缩略图</th>
                                <th>Header</th>
                                <th>状态</th>
                                <th>时间</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>缩略图</th>
                                <th>Header</th>
                                <th>状态</th>
                                <th>时间</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>
                                    <img data-src="holder.js/80x80" class="img-thumbnail" alt="80x80" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCI+PHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iNDAiIHk9IjQwIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEzcHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+ODB4ODA8L3RleHQ+PC9zdmc+" style="width:80px; height: 80px;" />
                                </td>
                                <td>
                                    标题
                                    <p class="row-action">
                                        <button type="button" class="btn btn-sm btn-link">链接</button> |
                                        <button type="button" class="btn btn-sm btn-link btn-link-primary">彩字</button> |
                                        <button type="button" class="btn btn-sm btn-link btn-gray-primary">灰调</button>
                                    </p>
                                </td>
                                <td>
                                    <span class="label label-default">正常</span>
                                </td>
                                <td>
                                    <abbr title="2014-07-01 12:00">2014/7/1</abbr>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="" /></td>
                                <td>
                                    <img data-src="holder.js/80x80" class="img-thumbnail" alt="80x80" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCI+PHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iNDAiIHk9IjQwIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEzcHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+ODB4ODA8L3RleHQ+PC9zdmc+" style="width:80px; height: 80px;" />
                                </td>
                                <td>
                                    标题
                                    <p class="row-action">
                                        <button type="button" class="btn btn-sm btn-link">链接</button> |
                                        <button type="button" class="btn btn-sm btn-link btn-link-primary">彩字</button> |
                                        <button type="button" class="btn btn-sm btn-link btn-gray-primary">灰调</button>
                                    </p>
                                </td>
                                <td>
                                    <span class="label label-primary">主要</span>
                                </td>
                                <td>
                                    <abbr title="2014-07-01 12:00">2014/7/1</abbr>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end 数据列表 -->



                <!-- 副标题（如果需要的话） -->
                <h2 class="sub-header">分页&amp;筛选</h2>
                <!-- end 副标题 -->


                <!-- 筛选操作 -->
                <div class="row">

                    <div class="col-md-6">
                        <ol class="breadcrumb">
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Library</a></li>
                            <li class="active">Data</li>
                        </ol>
                    </div>

                    <div class="col-md-6">
                        <div class="text-right form-inline">
                            <!--- filter -->
                            <div class="input-group">
                                <input type="text" class="form-control" />
                                <span class="input-group-addon"> -- </span>
                                <input type="text" class="form-control" />
                            </div>


                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    所有分类 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">分类一</a></li>
                                    <li><a href="#">分类二</a></li>
                                    <li><a href="#">分类三</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">其他</a></li>
                                </ul>
                            </div>

                            <!-- search -->
                            <div class="input-group">
                                <input type="text" class="form-control">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">搜索</button>
                                </span>
                            </div>
                            <!-- end search -->
                        </div>
                    </div>

                </div>
                <!-- end 筛选操作 -->


                <!-- 分页 -->
                <div class="row">

                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                        <!--- filter -->
                        <div class="btn-group" style="margin:20px 0px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">编辑</a></li>
                                <li><a href="#">移至回收站</a></li>
                            </ul>
                        </div>
                        <!-- end filter -->
                    </div>

                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-9 text-right">
                        <!-- page -->
                        <ul class="pagination">
                            <li><a href="#">&laquo;</a></li>
                            <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                            <li><a href="#">&raquo;</a></li>
                        </ul>
                        <!-- end page -->
                    </div>

                </div>
                <!-- end 分页 -->

            </div>
        </div>
        <!-- end main -->

    </div>


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
</body>
</html>
