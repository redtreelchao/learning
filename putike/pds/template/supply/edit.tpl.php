<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; <?php echo (!empty($data) ? '编辑' : '添加'); ?>供应商</title>

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
        .red { color: red; padding-right: 5px }
        .type .chosen-container { width: auto !important; }
        .well { position: relative; }
        .well .glyphicon-trash { position: absolute; top: 10px; right: 10px; }
        .add-well { padding: 5px 0px; margin-top: 10px; font-size: 12px; border: #ccc dashed 1px; text-align: center; text-decoration: underline; cursor: pointer; }
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
                <h1 class="page-header"><?php echo (!empty($data) ? '编辑' : '添加'); ?>供应商</h1>

                <!-- form -->
                <div class="row"  role="form">

                    <div class="col-md-8 col-lg-9">

                        <!-- 基础信息 支付信息 form start -->
                        <form action="" id="form">
                            <div class="form-horizontal">

                                <div class="page-header-hr"><h3>基础信息</h3></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>供应商ID</label>
                                    <div class="col-sm-6">
                                        <input required type="text" name="code"  class="form-control"  value="<?php echo (!empty($data)?$data['code']:NOW)?>" <?php echo (!empty($data)?'readonly':'')?> />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>供应商名称</label>
                                    <div class="col-sm-6">
                                        <input required type="text" name="name"  class="form-control"  value="<?php echo (!empty($data)?$data['name']:'')?>" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>关联区域</label>
                                    <div class="col-sm-4">
                                        <select name="area[]" class="form-control ui-select" multiple id="">
                                            <option value="">请选择</option>
                                            <?php
                                            if(!empty($data))
                                            {
                                                $_areas = explode(',', $data['area']);

                                            }
                                            foreach ($areas as $k => $v) { ?>
                                            <option name="area[]" value="<?php echo  $v['id']?>" <?php echo (isset($_areas)&&in_array($v['id'], $_areas))?'selected':'';?> ><?php echo $v['name']?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>办公地址</label>
                                    <div class="col-sm-2">
                                        <select name="country" id="country" class="form-control ui-select">
                                            <option>请选择..</option>
                                            <?php foreach ($countries as $k => $v) { ?>
                                            <option value="<?php echo $k?>" <?php echo (isset($data['pid'])&&$data['pid']==$k)?'selected':'' ?> ><?php echo $v?></option>
                                            <?php }?>
                                        </select>
                                    </div>

                                    <div class="col-sm-2">
                                        <select name="city" id="city" class="form-control ui-select">
                                            <option>请选择..</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="address" value="<?php echo (!empty($data)?$data['address']:'')?>" class="form-control" placeholder="请输入详细地址">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>办公电话</label>
                                    <?php
                                        if(isset($data['tel']))
                                        {
                                            $tel = explode(' ', $data['tel']);
                                        }
                                    ?>
                                    <div class="col-sm-1">
                                        <input value="<?php echo isset($tel)?$tel[0]:'';?>" type="tel" name="tel1" class="form-control" placeholder="区号">
                                    </div>
                                    <div class="col-sm-2">
                                        <input value="<?php echo isset($tel[1])?$tel[1]:''?>" type="tel" name="tel2" class="form-control" placeholder="电话号码">
                                    </div>
                                    <div class="col-sm-1">
                                        <input value="<?php echo isset($tel[2])?$tel[2]:''?>" type="tel" name="tel3" class="form-control" placeholder="分机号">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">法人姓名</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="corporation"  class="form-control"  value="<?php echo (!empty($data)?$data['corporation']:'')?>" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>从事领域</label>
                                    <div class="col-sm-2">
                                        <select  required name="type" id="" class="ui-select form-control">
                                            <?php foreach ($types as $k => $v) { ?>

                                            <option value="<?php echo $k?>" <?php echo (isset($data['type'])&&$data['type']==$k)?'selected':''?>><?php echo $v?></option>
                                            <?php }?>
                                        </select>
                                    </div>

                                    <!-- <label class="col-sm-2 control-label">BookingCode</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="海外产品请填写">
                                    </div> -->
                                </div>

                            </div>

                            <div class="form-horizontal">

                                <div class="page-header-hr"><h3>支付信息</h3></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>合作方式</label>
                                    <div class="col-sm-2">
                                        <select required  name="mode" class="form-control ui-select">
                                            <option value="1" <?php echo (isset($data['mode'])&&$data['mode']==1)?'selected':''?>>直采</option>
                                            <option value="2" <?php echo (isset($data['mode'])&&$data['mode']==2)?'selected':''?>>分销</option>
                                        </select>
                                    </div>
                                    <label class="col-sm-2 control-label"><small class="red">*</small>结算方式</label>
                                    <div class="col-sm-2">
                                        <select required name="payby" class="form-control ui-select" id="payby">
                                            <?php foreach ($paybys as $k=>$v) {?>
                                                <option value="<?php echo $k;?>" <?php echo (isset($data['payby'])&&$data['payby']==$k)?'selected':''?>><?php echo $v;?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <input value="<?php echo (!empty($data)?$data['period'].'天':'')?>" <?php echo  (isset($data['payby'])&&$data['payby']==3)?'readonly=readonly':''?> type="text" name="period" class="form-control" placeholder="请填写天数" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>开户行</label>
                                    <div class="col-sm-6">
                                        <input value="<?php echo (!empty($data)?$data['bank']:'')?>" type="text" name="bank" id="" class="form-control" placeholder="请填写开户行" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>开户名称</label>
                                    <div class="col-sm-6">
                                        <input value="<?php echo (!empty($data)?$data['bankaccount']:'')?>"  type="text" name="bankaccount"  class="form-control" placeholder="请填写开户名称" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><small class="red">*</small>银行账号</label>
                                    <div class="col-sm-6">
                                        <input value="<?php echo (!empty($data)?$data['bankcode']:'')?>" type="text" name="bankcode" id="" class="form-control" placeholder="请填写银行账号" />
                                    </div>
                                </div>

                            </div>
                            <input value="<?php echo (!empty($data)?$data['id']:'')?>" type="hidden" name="id"  />
                        </form>
                        <!-- 基础信息 支付信息 form end -->

                        <!-- 联系人信息 form start -->
                        <form action="" id="contact-list">

                            <div class="form-horizontal">

                                <div class="page-header-hr">
                                    <button type="button" class="btn btn-sm btn-default pull-right contact-add" data-id="1" data-pjax-container="#main">
                                        <span class="glyphicon glyphicon-plus hidden-md"></span>
                                        <span class="hidden-xs hidden-sm" > 添加</span>
                                    </button>
                                    <h3>业务联系人</h3>
                                </div>

                                <?php
                                if(!empty($business))
                                {
                                    foreach ($business as $k => $v)
                                    {
                                ?>
                                <div class="well row contact-well">
                                    <span class="glyphicon glyphicon-trash" data-id="<?php echo $v['id'];?>"></span>
                                    <div class="col-sm-3">
                                        负责区域: <span><?php echo $v['area'];?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <small class="red">*</small>姓名: <span><?php echo $v['name'];?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <small class="red">*</small>手机: <span><?php echo $v['mobile'];?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        固话: <span><?php echo $v['tel']?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        邮箱: <span><?php echo $v['email'];?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        传真: <span><?php echo $v['fax'];?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        抄送: <span><?php echo $v['cc'];?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        其他: <span><?php echo $v['other'];?></span>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                ?>
                                <div class='add-well row'>点击添加联系人</div>
                            </div>

                            <div class="form-horizontal">

                                <div class="page-header-hr">
                                    <button type="button" class="btn btn-sm btn-default pull-right contact-add" data-id="2" data-pjax-container="#main">
                                        <span class="glyphicon glyphicon-plus hidden-md"></span>
                                        <span class="hidden-xs hidden-sm" > 添加</span>
                                    </button>
                                    <h3>财务联系人</h3>
                                </div>

                                <?php
                                if(!empty($finance))
                                {
                                    foreach ($finance as $k => $v)
                                    {
                                ?>
                                <div class="well row contact-well">
                                    <span class="glyphicon glyphicon-trash" data-id="<?php echo $v['id'];?>"></span>
                                    <div class="col-sm-3">
                                        负责内容: <span><?php echo $v['area'];?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <small class="red">*</small>姓名: <span><?php echo $v['name'];?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <small class="red">*</small>手机: <span><?php echo $v['mobile'];?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        固话: <span><?php echo $v['tel']?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        邮箱: <span><?php echo $v['email'];?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        传真: <span><?php echo $v['fax'];?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        抄送: <span><?php echo $v['cc'];?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        其他: <span><?php echo $v['other'];?></span>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                ?>
                                <div class='add-well row'>点击添加联系人</div>
                                        
                            </div>

                            <div class="form-horizontal">

                                <div class="page-header-hr">
                                    <button type="button" class="btn btn-sm btn-default pull-right contact-add" data-id="3" data-pjax-container="#main">
                                        <span class="glyphicon glyphicon-plus hidden-md"></span>
                                        <span class="hidden-xs hidden-sm" > 添加</span>
                                    </button>
                                    <h3>客服联系人</h3>
                                </div>

                                <?php
                                if(!empty($service))
                                {
                                    foreach ($service as $k => $v)
                                    {
                                ?>
                                <div class="well row contact-well">
                                    <span class="glyphicon glyphicon-trash" data-id="<?php echo $v['id'];?>"></span>
                                    <div class="col-sm-3">
                                        负责内容: <span><?php echo $v['area'];?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        姓名: <span><?php echo $v['name'];?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        手机: <span><?php echo $v['mobile'];?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        固话: <span><?php echo $v['tel']?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        邮箱: <span><?php echo $v['email'];?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        传真: <span><?php echo $v['fax'];?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        抄送: <span><?php echo $v['cc'];?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        其他: <span><?php echo $v['other'];?></span>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                ?>
                                <div class='add-well row'>点击添加联系人</div>

                            </div>

                        </form>
                        <!-- 联系人信息 form end -->

                    </div>



                    <!-- Right Bar -->
                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">发布</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 上次更新: <?php echo (!empty($data) ? date('Y-m-d H:i:s', $data['updatetime']) : '无'); ?></p>


                            </div>
                            <?php if(empty($data)) { ?>
                            <div class="panel-footer text-right">
                                <button type="button" class="btn btn-primary btn-sm" onClick="save();">新建</button>
                            </div>
                            <?php }else{ ?>
                            <div class="panel-footer text-right">
                                <a href="/supply.php?method=list" class="btn btn-default btn-sm" onClick="history.go(-1)">返回</a>
                                <button type="button" class="btn btn-primary btn-sm" onClick="save()">保存</button>
                            </div>
                            <?php } ?>
                        </div>

                        <!-- <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title">操作记录</h3>
                            </div>
                            <div class="panel-body">
                            <?php
                            if(isset($history)){
                            foreach ($history as $v) { ?>

                                <p>
                                    <?php echo  $v['username'].$v['intro']?>
                                    <br>
                                    <small><?php echo date('Y年m月d日 H:i:s',$v['time'])?></small>
                                </p>
                            <?php
                                 }
                            }
                            ?>
                            </div>
                        </div> -->

                   </div>


                </div>
                <!-- end form -->

                <!-- modal start -->
                <div class="modal fade" id="contact-add-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">添加联系人</h4>
                            </div>
                            <form id="contactform">
                            <div class="modal-body form-horizontal">

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">负责区域</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="area" placeholder="请输入负责区域" required class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label"><small class="red">*</small>姓名</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="name" placeholder="请输入姓名" required class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label"><small class="red">*</small>手机号</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mobile" placeholder="请输入手机号" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">固话</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="tel" placeholder="固定电话和传真至少填写一个" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">邮箱</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="email" placeholder="请填写html地址,支持多个" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">传真</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="fax" placeholder="固定电话和传真至少填写一个" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">抄送邮箱</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="cc" placeholder="请填写html地址,支持多个" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">其他</label>
                                    <div class="col-sm-2 type">
                                        <select name="othertype" class="form-control ui-select">
                                            <option value="qq">QQ</option>
                                            <option value="微信">微信</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="other" placeholder="请输入其他联系方式" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="type" value="<?php echo (!empty($data)?$data['type']:'')?>"/>
                                <input type="hidden" name="pid" value="<?php echo (!empty($data)?$data['id']:'')?>"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                <button type="button" class="btn btn-primary" id="contact-save">保存</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
                <!-- modal end -->

    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>
    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script>
     function save(){
        var postdata = $("#form").serialize();
        $.post("<?php echo BASE_URL; ?>supply.php?method=edit", postdata, function(data){

            if(data.s == 0){
                <?php if(!empty($data)){ ?>
                alert("保存成功", "success");
                <?php }else{ ?>
                location.href = "<?php echo BASE_URL; ?>supply.php?method=edit&id="+data.rs;
                <?php } ?>
            }else{
                alert(data.err, 'error');
            }
        }, "json");
    }

    $(function(){

        <?php
        if(isset($data['city']) && !empty($data['city'])){
        ?>


           var pid = <?php echo $data['pid']?>;
            $.post("./index.php?method=city", {pid:pid}, function(data){
                if (data.s == 0) {
                    $("#city").html('<option>请选择..</option>');
                    for(x in data.rs){
                        var opt = $("<option />");
                        opt.text(data.rs[x].name).attr("value", data.rs[x].id);
                        if(data.rs[x].id == <?php echo $data['city']?>)
                        {
                            opt.attr('selected','selected')
                        }
                        $("#city").append(opt);
                    }
                    $("#city").trigger('chosen:updated');
                }else{
                    alert("城市读取异常，请重试", "error");
                }
            }, "json");



        <?php } ?>

        $("#country").change(function(){
            var pid = $(this).val();
            $.post("./index.php?method=city", {pid:pid}, function(data){
                if (data.s == 0) {
                    $("#city").html('<option>请选择..</option>');
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
        });

        $("#payby").change(function (){
            var payby = $(this).val();
            if(payby!=1)
            {
                $("input[name=period]").attr("readonly","true");
            }else
            {
                $("input[name=period]").removeAttr("readonly");
            }

        });

        // 添加联系人 data-id=1 --> 业务联系人

        //            data-id=2 --> 财务联系人
        //            data-id=3 --> 客服联系人


        $('.contact-add').on('click',function(){
            var _this   =   $(this),
                _id     =   _this.data('id'),
                _modal  =   $('#contact-add-modal'),
                _group  =   _modal.find('.form-group').eq(0);

            if( _this.data('id') == '1' ) {
                _group.find('label').text('负责区域');
                _group.find('input').attr('placeholder','请填写负责区域');
            } else {
                _group.find('label').text('负责内容');
                _group.find('input').attr('placeholder','请填写负责内容');
            }

            _modal.attr('data-id',_this.data('id'));
            _modal.modal('show');
            console.log(_this.data('id'));
            console.log(_modal.attr('data-id'));
        })

        $('.add-well').on('click',function(){
            var _this = $(this);
            _this.parent().find('.contact-add').click();
        })

        $('#contact-save').on('click',function(){

            var pid =  $("input[name=id]").val();
            if(!pid)
            {
                alert('请先保存供应商信息','error');
                return false;
            }

            // 保存所有值
            var _id     =   $(this).closest('.modal').attr('data-id'),
                _index  =   _id-1,
                _area   =   $('input[name="area"]').val(),
                _name   =   $('input[name="name"]').val(),
                _phone  =   $('input[name="phone"]').val(),
                _tel    =   $('input[name="tel"]').val(),
                _email  =   $('input[name="email"]').val(),
                _fax    =   $('input[name="fax"]').val(),
                _cc     =   $('input[name="cc"]').val(),
                _other  =   $('.modal .chosen-single span').text().trim(),
                _num    =   $('input[name="other"]').val();

               if(_id=='1'){
                    $('input[name="type"]').val('business');
               }else if(_id=='2'){
                    $('input[name="type"]').val('finance');
               }else{
                    $('input[name="type"]').val('service');
               }
            console.log('id'+$(this).closest('.modal').data('id'));
            console.log('index'+_index);
            var other = $('.modal .chosen-single span').text().trim()+':'+$('input[name="other"]').val();
            $('input[name="other"]').val(other);

            var postdata = $("#contactform").serialize();
            $.post("<?php echo BASE_URL; ?>supply.php?method=edit", postdata, function(data){

                console.log(data);
                if(data.err == '操作成功'){
                    if(_id == '1') {
                        console.log('1');
                        contact('区域',_index,_area,_name,_phone,_tel,_email,_fax,_cc,_other,_num,data.s);
                    } else if (_id == '2') {

                        contact('内容',_index,_area,_name,_phone,_tel,_email,_fax,_cc,_other,_num,data.s);
                    } else if (_id == '3') {

                        contact('内容',_index,_area,_name,_phone,_tel,_email,_fax,_cc,_other,_num,data.s);
                    } else {
                        alert('请求失败 请重试','error')
                    }
                }else{
                    alert(data.err, 'error');
                }
            }, "json");

            $('#contact-add-modal .modal-body').find('input').val('');
            $('#contact-add-modal').modal('hide');

        })

        // 删除方法 id可以给到well上一个data-id
        $(document).on('click','.glyphicon-trash',function(){
            var _this   =   $(this),
                _id     =   _this.attr('data-id');
            $.get("<?php echo BASE_URL; ?>supply.php?method=del", {id:_id}, function(data){
                if(data.s==1) {
                    alert(data.err , 'error');
                } else {
                    _this.closest('.well').slideUp(function(){
                        $(this).remove();
                    })

                }
            }, "json");

            // var _form = _this.closest('.form-horizontal');
            // if(_form.find('.well').length==0) {
            //     $('.add-well').fadeIn();
            // }else{
            //     console.log(_form.find('.well').length)
            // }

        })

        function contact(type,index,area,name,phone,tel,email,fax,cc,ss,num,s) {

            $('#contact-list .form-horizontal').eq(index).find('.add-well').before( '<div class="well row contact-well">'+
                                            '<span class="glyphicon glyphicon-trash" data-id="'+s+'"></span>'+
                                            '<div class="col-sm-3">负责'+type+': <span>'+area+'</span></div>'+
                                            '<div class="col-sm-2">姓名: <span>'+name+'</span></div>'+
                                            '<div class="col-sm-4">手机: <span>'+phone+'</span></div>'+
                                            '<div class="col-sm-3">固话: <span>'+tel+'</span></div>'+
                                            '<div class="col-sm-3">邮箱: <span>'+email+'</span></div>'+
                                            '<div class="col-sm-2">传真: <span>'+fax+'</span></div>'+
                                            '<div class="col-sm-4">抄送: <span>'+cc+'</span></div>'+
                                            '<div class="col-sm-3">其他: <span>'+ss+': '+num+'</span></div>'+
                                        '</div>'
                                    );
            // $('#contact-list .form-horizontal').eq(index).find('.add-well').fadeOut();


        }

        $(".ui-select").chosen({disable_search_threshold:10, no_results_text:"未找到..", placeholder_text_single:"请选择.."});
    });
    </script>
</body>
</html>