<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; Form Demo</title>

    <link rel="shortcut icon" href="/favicon.ico" />
F
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
                  <li><a href="<?php BASE_URL; ?>demo.php">List Demo</a></li>
                  <li class="active"><a href="<?php BASE_URL; ?>demo.php?type=form">Form Demo</a></li>
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
                <h2 class="sub-header">一般表单</h2>
                <!-- end 副标题 -->

                <!-- 数据表单 -->
                <div class="row">

                    <form role="form" class="col-md-8 col-lg-9 form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Text</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Email" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" placeholder="Password" />
                                <span class="help-block">提示性文本内容</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Select</label>
                            <div class="col-sm-4">
                                <select class="form-control ui-select">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Select-muli</label>
                            <div class="col-sm-4">
                                <select class="form-control ui-select-muli" multiple="multiple">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Select2</label>
                            <div class="col-sm-10">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Action</a></li>
                                        <li><a href="#">Another action</a></li>
                                        <li><a href="#">Something else here</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Checkbox</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label><input type="checkbox" /> checkbox1</label>&nbsp;
                                    <label><input type="checkbox" /> checkbox2</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Radio</label>
                            <div class="col-sm-10">
                                <div class="radio">
                                    <label><input type="radio" /> Radio1</label>&nbsp;
                                    <label><input type="radio" /> Radio2</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Checkbox/Radio</label>
                            <div class="col-sm-10">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default">Left</button>
                                    <button type="button" class="btn btn-default">Middle</button>
                                    <button type="button" class="btn btn-default">Right</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">File</label>
                            <div class="col-sm-10">
                                <input type="hidden" name="file" placeholder="可设为hidden" />
                                <button type="button" class="btn btn-default btn-upload">选择文件</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Image</label>
                            <div class="col-sm-10 image-upload">
                                <span class="image" style="background-image:url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjUwIiB5PSI1MCIgc3R5bGU9ImZpbGw6I2FhYTtmb250LXdlaWdodDpib2xkO2ZvbnQtc2l6ZToxMnB4O2ZvbnQtZmFtaWx5OkFyaWFsLEhlbHZldGljYSxzYW5zLXNlcmlmO2RvbWluYW50LWJhc2VsaW5lOmNlbnRyYWwiPjEwMHgxMDA8L3RleHQ+PC9zdmc+);">
                                    <input type="hidden" value="" />
                                    <div class="action">
                                        <a class="rm" href=""><span class="glyphicon glyphicon-remove"></span></a>
                                    </div>
                                </span>
                                <a class="image image-add">选择图片</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Money</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-addon">￥</div>
                                    <input class="form-control" type="number" placeholder="" />
                                    <div class="input-group-addon">.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">DateTime</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input class="form-control datepicker" type="text" placeholder="    -  -  " />
                                    <div class="input-group-addon"> </div>
                                    <input class="form-control timepicker" style="width:100px" type="text" placeholder="　:　" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Testarea</label>
                            <div class="col-sm-5">
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </form>

                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: 2014-07-01</p>
                                <p><span class="glyphicon glyphicon-user"></span> 发布人: </p>
                            </div>
                            <div class="panel-footer text-right"><button type="button" class="btn btn-primary btn-sm">保存</button></div>
                        </div>
                    </div>

                </div>
                <!-- end 数据表单 -->



                <hr class="clearfix" />



                <!-- 副标题（如果需要的话） -->
                <h2 class="sub-header">特殊表单</h2>
                <!-- end 副标题 -->

                <!-- 数据表单 -->
                <div class="row">

                    <form role="form" class="col-md-8 col-lg-9">
                        <div class="form-group">
                            <input type="email" class="form-control input-lg" placeholder="特殊的标题项">
                        </div>
                        <div class="form-group">
                            <textarea id="text-editor" class="form-control" rows="20"></textarea>
                        </div>
                    </form>

                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: 2014-07-01</p>
                                <p><span class="glyphicon glyphicon-user"></span> 发布人: </p>
                            </div>
                            <div class="panel-footer text-right"><button type="button" class="btn btn-primary btn-sm">保存</button></div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">产品图片</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <div class="col-sm-10 image-upload">
                                    <a class="image image-add">选择图片</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end 数据表单 -->




            </div>
        </div>
        <!-- end main -->

    </div>





    <!-- popup -->
    <div id="media-popup" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <!-- popup -->


    <!-- js 模板文件 -->
    <script type="text/html" id="image_tmpl">
    <span class="image" style="background-image:url({url});">
        <input type="hidden" value="{url}" />
        <div class="action">
            <a class="rm" href="javascript:;" onclick="$(this).parents('.image').hide(300, function(){ $(this).remove(); });"><span class="glyphicon glyphicon-remove"></span></a>
        </div>
    </span>
    </script>



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/timepicker.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.timepicker.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>

    <script src="<?php echo RESOURCES_URL; ?>js/tinymce/jquery.tinymce.min.js"></script>

    <script type="text/javascript">
    $(function(){
        // 下拉菜单
        $(".ui-select").chosen({no_results_text: "未找到..", placeholder_text_single:"请选择.."});
        // 多选下拉
        $(".ui-select-muli").chosen({no_results_text: "未找到..", max_selected_options:2, placeholder_text_multiple:"请选择.."});
        // 日期选择
        $(".datepicker").zdatepicker({viewmonths:1});
        // 时间选择
        $(".timepicker").timepicker({timeFormat:'H:i'});

        // 上传按钮
        $(".btn-upload").click(function(){
            var btn = $(this);
            var popup = $("#media-popup");
            popup.modal();
            // Get file data, custom
            popup.find(".modal-content").load("<?php echo BASE_URL; ?>index.php?method=media&select=1&link=1&_t=1", function(){
                popup.find(".modal-footer .btn-primary").unbind("click").bind("click", function(){
                    if($("#media-link").is(".active")){
                        var data = [$("#media-link-url").val()];
                    }else{
                        var data = [];
                        $("#media-library .sel").each(function(i){
                            data[i] = "<?php echo RESOURCES_URL; ?>" + $(this).data('file') + "_120x120.jpg";
                        });
                    }

                    btn.prev("input").val(data[0]);
                    btn.popover('destroy').popover({content:"<img width='100' height='100' src='"+data[0]+"' />", html:true, trigger:"hover"}).text("已选择").addClass("btn-primary");
                    popup.modal('hide');
                });
            });
        });

        // 图片上传
        $(".image-add").click(function(){
            var btn = $(this);
            var popup = $("#media-popup");
            popup.modal();
            // Get file data, custom
            popup.find(".modal-content").load("<?php echo BASE_URL; ?>index.php?method=media&select=0&link=1&_t=2", function(){
                popup.find(".modal-footer .btn-primary").unbind("click").bind("click", function(){
                    if($("#media-link").is(".active")){
                        var data = [$("#media-link-url").val()];
                    }else{
                        var data = [];
                        $("#media-library .sel").each(function(i){
                            data[i] = "<?php echo RESOURCES_URL; ?>" + $(this).data('file') + "_120x120.jpg";
                        });
                    }

                    var template = $("#image_tmpl").html();
                    var reg = new RegExp("\\{([0-9a-zA-Z_-]*?)\\}", 'gm');
                    for(x in data){
                        var d = {url:data[x]};
                        var image = $(template.replace(reg, function (node, key) { return d[key]; }));
                        btn.before(image);
                    }
                    popup.modal('hide');
                });
            });
        });

        // 大型编辑区
        $("#text-editor").tinymce({
            script_url : '<?php echo RESOURCES_URL; ?>/js/tinymce/tinymce.min.js',
            language : 'zh_CN',
            height: 450,
            inline_styles : true,
            menubar : false,
            plugins : 'autolink,link,image2,textcolor,paste',
            toolbar1 : "bold,italic,underline,strikethrough | bullist,numlist | blockquote | alignleft,aligncenter,alignright,alignjustify | link,unlink,image | forecolor | fontsizeselect | outdent,indent",
            content_css : "<?php echo RESOURCES_URL; ?>/js/tinymce/default.css", // 根据具体页面的运用，请设置该默认样式
            browser_url: "<?php echo BASE_URL; ?>index.php?method=media&select=0&link=1&_t=3",
            browser_loaded: function(popup, editor){
                popup.find(".modal-footer .btn-primary").unbind("click").bind("click", function(){
                    if($("#media-link").is(".active")){
                        var data = [$("#media-link-url").val()];
                    }else{
                        var data = [];
                        $("#media-library .sel").each(function(i){
                            data[i] = "<?php echo RESOURCES_URL; ?>" + $(this).data('file') + ".jpg";
                        });
                    }
                    for(x in data){
                        editor.execCommand('mceReplaceContent', false, '<img src="'+data[x]+'" />');
                    }
                    popup.modal('hide');
                });
            },
            paste_auto_cleanup_on_paste : true,
            valid_elements : "a[href|target],strong/b,em/i,strike,u,p[align],ol,ul,li,br,img[src|border=0|alt|align|class='img-thumbnail'],sub,sup,blockquote[style|class],table[border=0|cellspacing=0|cellpadding=0|align|class='table table-striped table-hover'],tr[rowspan],td[colspan|rowspan|width|height],span[align],pre[align],h3,h4,h5,h6[align],hr,video[src|controls]"
        });


    });
    </script>
</body>
</html>
