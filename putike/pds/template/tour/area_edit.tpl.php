<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 产品线<?php echo $data ? '编辑' : '添加'; ?></title>

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

                <h1 class="page-header">产品线<?php echo $data ? '编辑' : '添加'; ?></h1>

                <!-- form -->
                <form class="row" id="form" role="form">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <div class="form-group">
                            <label class="control-label col-sm-2">产品线名称</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" class="form-control" value="<?php echo $data['name']; ?>"  />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">默认图片</label>
                            <div class="col-sm-9 image-upload">
                                <?php
                                if ($data) {
                                    $pics = explode(',', $data['pics']);
                                    foreach($pics as $v){
                                ?>
                                <span class="image" style="background-image:url(<?php echo $v; ?>!200X200); background-size:cover;">
                                    <input type="hidden" name="pics[]" value="<?php echo $v; ?>" />
                                    <div class="action">
                                        <a class="rm" href="javascript:;"><span class="glyphicon glyphicon-remove"></span></a>
                                    </div>
                                </span>
                                <?php
                                    }
                                }
                                ?>
                                <a href="javascript:;" id="upload" class="image image-add">添加图片</a>
                                <p class="help-block">建议上传尺寸：720 x 400</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">推荐国家/城市</label>
                            <div id="city" class="col-sm-5">
                                <?php
                                $cities = $data ? array_filter(explode('|', $data['cities'])) : null;
                                if ($cities) {
                                    foreach ($cities as $k => $v) {
                                ?>
                                <div class="input-group" style="margin-bottom:10px">
                                    <input type="text" class="form-control" name="cities[]" value="<?php echo $v; ?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <?php if ($k) {  ?>
                                            <span class="glyphicon glyphicon-remove"></span>
                                            <?php } else { ?>
                                            <span class="glyphicon glyphicon-plus"></span>
                                            <?php } ?>
                                        </button>
                                    </span>
                                </div>
                                <?php
                                    }
                                } else {
                                ?>
                                <div class="input-group" style="margin-bottom:10px">
                                    <input type="text" class="form-control" name="cities[]">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    </span>
                                </div>
                                <?php
                                }
                                ?>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">行程设计师</label>
                            <div class="col-sm-6">
                                <input type="text" readonly class="form-control" value="<?php if ($data) foreach($data['designers'] as $v) echo $v['nickname'].' '; ?>"  />
                            </div>
                        </div>

                    </div>

                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">

                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $data ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>
                            </div>

                            <div class="panel-footer text-right">
                                <a href="<?php echo BASE_URL?>tour.php?method=area" class="btn btn-default btn-sm" >返回</a>
                                <button type="button" class="btn btn-primary btn-sm" id="save">保存</button>
                            </div>

                        </div>
                        <!-- panel -->

                    </div>
                    <input type="hidden" name="id"  value="<?php echo $data['id']; ?>"  />
                </form>
                <!-- end form -->

            </div>

        </div>
        <!-- end main -->

    </div>


    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/plupload/plupload.full.min.js"></script>

    <script>
    $("#save").click(function(){
        var data = $('#form').serialize();
        $.post("<?php echo BASE_URL; ?>tour.php?method=area", data, function(data){
            if (data.s == 0){
               alert('修改成功', 'success');
            } else {
               alert(data.err, 'error');
            }
        }, "json");
    });


    $(function(){

        // 图片上传
        var uploader = new plupload.Uploader({
            runtimes : 'html5,flash,html4',
            browse_button : "upload",
            url : "<?php echo BASE_URL; ?>plupload.php",
            multi_selection: false,
            multipart_params: {},
            filters : {
                max_file_size : '5mb',
                mime_types: [{title : "Image files", extensions : "jpg,jpeg,png"}]
            },
            flash_swf_url : '<?php echo BASE_URL; ?>template/js/plupload/Moxie.swf',
            init: {
                Init: function(up) {
                    return true;
                },

                FilesAdded: function(up, files) {
                    $("#upload").before('<a id="'+files[0].id+'" class="image image-uploading">上传中</a>');
                    up.start();
                },

                UploadProgress: function(up, file) {
                    $("#"+file.id).text(file.percent+"%");
                },

                FileUploaded: function(up, file, res) {
                    var data = $.parseJSON(res.response);
                    if(!data.s){
                        var rs = data.rs;
                        $("#"+file.id).removeClass("image-uploading").css({"background-image":"url("+rs+"!200X200)", "background-size":"cover"}).html('<input type="hidden" name="pics[]" value="" /><div class="action"><a class="rm" href="javascript:;"><span class="glyphicon glyphicon-remove"></span></a></div>');
                        $("#"+file.id).find("input[name='pics[]']").val(rs);
                        up.refresh();
                    }else{
                        $("#"+file.id).toggleClass("image-uploading image-error").text("上传失败");
                        alert(data.err, 'error');
                    }
                }
            }
        });

        uploader.init();

        $(".image-upload").on("click", ".image .rm", function(){
            $(this).parents(".image").remove();
        });

        $("#city .input-group .btn").click(function(){
            var b = $(this), g = b.parents(".input-group").eq(0);
            if (g.index() == 0) {
                var c = g.clone(true);
                c.find("input").val('');
                c.find(".glyphicon").attr('class', 'glyphicon glyphicon-remove');
                g.parent().append(c);
            }else{
                g.fadeOut(200, function(){ g.remove(); } );
            }
        });
    });
    </script>



</body>
</html>
