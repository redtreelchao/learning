<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 行程设计师</title>

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

                <h1 class="page-header">编辑</h1>

                <!-- form -->
                <form class="row" id="form" role="form" method="post">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <div class="form-group">
                            <label class="control-label col-sm-2">姓名</label>
                            <div class="col-sm-4">
                                <select name="uid" class="form-control ui-select">
                                    <option>请选择..</option>
                                    <?php foreach ($users as $v) { ?>
                                    <option value="<?php echo $v['id']?>" <?php if ($v['disabled']) echo 'disabled'; else if($data['uid'] == $v['id']) echo 'selected'; ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">昵称</label>
                            <div class="col-sm-4">
                                <input type="text" name="nickname" class="form-control" value="<?php echo $data['nickname']; ?>"  />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">头像</label>
                            <div class="col-sm-6 image-upload">
                                <?php if ($data && $data['avatar']) { ?>
                                <a class="image" style="background:url(<?php echo $data['avatar']; ?>!200X200); background-size:cover;" id="upload"><input type="hidden" name="avatar" value="<?php echo $data['avatar']; ?>" /></a>
                                <?php } else { ?>
                                <a class="image image-add" id="upload">选择图片<input type="hidden" name="avatar" value="" /></a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">设计师介绍</label>
                            <div class="col-sm-6">
                                <textarea name="description" class="form-control" maxlength="30"><?php echo $data['description']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">负责线路</label>
                            <div class="col-sm-6">
                                <select multiple class="form-control ui-select" name="areas[]">
                                    <?php foreach ($areas as $d) { ?>
                                    <option value="<?php echo $d['id']?>" <?php echo in_array($d['id'], explode(',', $data['areas'])) ? 'selected' : ''; ?>><?php echo $d['name']?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">微信号</label>
                            <div class="col-sm-6">
                                <input type="text" name="wechat" class="form-control" value="<?php echo $data['wechat']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">邮箱</label>
                            <div class="col-sm-6">
                                <input type="text" name="email" class="form-control" value="<?php echo $data['email']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">电话</label>
                            <div class="col-sm-6">
                                <input type="text" name="mobile" class="form-control" value="<?php echo $data['mobile']; ?>" />
                            </div>
                        </div>

                    </div>

                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">

                        <!-- panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">信息</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo $data ? date('Y-m-d H:i:s', $data['updatetime']) : '无'; ?></p>
                            </div>

                            <div class="panel-footer text-right">
                                <a href="<?php echo BASE_URL?>tour.php?method=designer" class="btn btn-default btn-sm" >返回</a>
                                <button type="button" class="btn btn-primary btn-sm" id="save">保存</button>
                            </div>

                            <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />

                        </div>
                        <!-- panel -->

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

    <script src="<?php echo RESOURCES_URL; ?>js/plupload/plupload.full.min.js"></script>

    <script type="text/javascript">
    $(".ui-select").chosen({no_results_text: "未找到..", placeholder_text_single:"请选择..", placeholder_text_multiple:"请选择.."});

    $("#save").click(function(){

        var data = $('#form').serialize();
        $.post("<?php echo BASE_URL; ?>tour.php?method=designer", data, function(data){

            if (data.s == 0){
               alert('修改成功','success');
               location.reload(true);
            } else {
               alert(data.err);
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
                    $("#upload").removeClass("image-add").addClass("image-uploading").text('上传中');
                    up.start();
                },

                UploadProgress: function(up, file) {
                    $("#upload").text(file.percent+"%");
                },

                FileUploaded: function(up, file, res) {
                    var data = $.parseJSON(res.response);
                    if(!data.s){
                        var rs = data.rs;
                        $("#upload").removeClass("image-uploading").css({"background-image":"url("+rs+"!200X200)", "background-size":"cover"}).html('<input type="hidden" name="avatar" value="" />');
                        $("#upload").find("input[name='avatar']").val(rs);
                    }else{
                        $("#upload").toggleClass("image-uploading image-error").text("上传失败");
                        alert(data.err, 'error');
                    }
                }
            }
        });

        uploader.init();
    });

    </script>


</body>
</html>
