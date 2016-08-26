<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 行程规划</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/order.css" rel="stylesheet" />

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
    <style>
        .tour-day-row {
            position: relative;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            min-height: 100px;
        }
        .tour-day-num {
            position: absolute;
            width: 60px;
            height: 60px;
            line-height: 60px;
            text-align: center;
            border-radius: 50%;
            background: #f5f5f5;
            cursor: move;
        }
        .tour-day-row .btn-group {
            position: absolute;
            top: 90px;
            left: 6px;
            z-index: 111;
        }
        textarea.form-control {
            /*height: 100px;*/
        }
        .image-upload .image {
            cursor: pointer;
            background-size: cover;
            background-position: center;
        }

        .tour-name-drop {
            display: none;
            position: absolute;
            z-index: 1010;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            padding: 5px 0px;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 6px 12px rgba(0,0,0,.175);
        }

        .tour-name-drop ul {
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            margin: 0 0px 4px 0;
            padding: 4px 0 0 0px;
            max-height: 240px;
            -webkit-overflow-scrolling: touch;
        }

        .tour-name-drop li {
            padding: 5px 15px;
            list-style: none;
            line-height: 1.5;
            cursor: pointer;
        }

        .tour-name-drop li.active {
            background: #f0f0f0;
        }
        .fileset span.glyphicon-equalizer {
            display: none;
        }
        .fileset:hover span.glyphicon-equalizer {
            display: inline-block;
        }

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

                <h1 class="page-header">定制行程详情</h1>

                <div id="order-list" class="table-responsive well">
                    <table class="table">.
                        <thead>
                            <tr>
                                <th>行程编号</th>
                                <th>客户姓名</th>
                                <th>目的地类型</th>
                                <th>行程名称</th>
                                <th>定制卡</th>
                                <th>状态</th>
                                <th>付款进度</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $data['order'];?></td>
                                <td><?php echo $data['contact'];?></td>
                                <td><span><?php echo  $data['area_name'];?></span></td>
                                <td><?php echo ($data['title']?$data['title']:'设计中')?></td>
                                <td><a href="<?php echo BASE_URL ?>tourcard.php?method=view&id=<?php echo $data['card']?>"><?php echo $data['code']?></a></td>
                                <td><?php echo get_status($data['status'])?></td>
                                <td><?php if(in_array($data['status'],[11,12])){echo '0.00%';}else{echo sprintf("%.2f", $data['progress']);}?>%</td>
                                <td>
                                    <a href="tourorder.php?method=price&id=<?php echo $data['id']?>" class="btn btn-sm btn-default">
                                        <span class="glyphicon glyphicon-yen hidden-md"></span>
                                        <span class="hidden-xs hidden-sm">价格与备注</span>
                                    </a>

                                    <a href="tourorder.php?method=preview&id=<?php echo $data['id']?>" class="btn btn-sm btn-primary">
                                        <span class="glyphicon glyphicon-eye-open hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 预览</span>
                                    </a>
                                </td>
                            <tr>
                        </tbody>
                    </table>
                </div>

                <!-- tour begin -->

                <div class="tourorder-edit row">

                    <div class="col-md-8 col-lg-9 form-horizontal">
                    <!-- tour row begin -->
                        <form name="form1" id="form1" role="form">
                            <input name="doc" type="hidden"/>
                                    <h3 class="page-header">行程概要</h3>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">行程名称</label>
                                        <div class="col-sm-5">
                                            <input type="text" value="<?php echo $data['title'] ?>" name="title" class="form-control" placeholder="请输入行程名称">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-10 col-sm-offset-2 image-upload" id="container">
                                                <?php
                                                if($data['cover']):
                                                ?>
                                                <a class="image" id="cover" style="background-image:url(<?php echo $data['cover'];?>!200X200);"></a>
                                                <!-- <img src="<?php echo $data['cover'];?>" width="95" /> -->
                                                <?php else: ?>
                                                <a class="image image-add" id="cover">选择图片</a>
                                                <?php endif;?>
                                            <input name="cover" type="hidden" value="<?php echo $data['cover']?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">行程亮点</label>
                                        <div class="col-sm-4">
                                            <textarea name="edge"  class="form-control" rows="3"><?php echo $data['edge']?></textarea>
                                        </div>
                                        <label class="col-sm-2 control-label">行程说明</label>
                                        <div class="col-sm-4">
                                            <textarea name="intro" class="form-control" rows="3"><?php echo $data['intro']?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">费用包含</label>
                                        <div class="col-sm-4">
                                            <textarea name="include" class="form-control" rows="3"><?php echo $data['include'];?></textarea>
                                        </div>
                                        <label class="col-sm-2 control-label">费用不包含</label>
                                        <div class="col-sm-4">
                                            <textarea name="without" class="form-control" rows="3"><?php echo $data['without'];?></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="<?php echo  $data['id']?>" />
                                    <input type="hidden" name="order" value="<?php echo  $data['order']?>" />
                        </form>

                        <input id="days" value="<?php echo $days;?>" type="hidden"/>

                        <form name="form2" id="form2" role="form" class="">
                            <h3 class="page-header">行程详情</h3>
                            <input type="hidden" name="order" value="<?php echo  $data['order']?>" />
                            <div class="tour-day">
                                <?php if(empty($lists) ){ ?>
                                <div class="tour-day-row">

                                        <div class="tour-day-num">Day 1</div>
                                        <div class="btn-group btn-group-xs" role="group">
                                            <button type="button" class="btn btn-default btn-up">
                                                <span class="glyphicon glyphicon-chevron-up"></span>
                                            </button>
                                            <button type="button" class="btn btn-default btn-down">
                                                <span class="glyphicon glyphicon-chevron-down"></span>
                                            </button>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">当天行程概要</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="detail[1][summary]" rows="2"></textarea>
                                            </div>
                                        </div>

                                        <div class="tour-code">

                                            <div class="fileset">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        <span class="glyphicon glyphicon-equalizer"></span>
                                                        行程点类型
                                                    </label>
                                                    <div class="col-sm-5">
                                                        <select name="detail[1][type][]" id="" class="chosen-select form-control">
                                                            <?php foreach ($tourtype as $tour_k => $tour_v){?>
                                                            <option value="<?php echo $tour_k;?>" ><?php echo $tour_v;?></option>
                                                            <?php } unset($tour_k);unset($tour_v);?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group tour-name">
                                                    <label class="col-sm-2 control-label">行程点名称</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="detail[1][title][]" class="form-control" value="" placeholder="请按回车键查询">
                                                    </div>
                                                </div>
                                                <div class="form-group tour-des">
                                                    <label class="col-sm-2 control-label">行程点描述</label>
                                                    <div class="col-sm-7">
                                                        <textarea rows="5" name="detail[1][describe][]" class="form-control"></textarea>

                                                        <label class="save-this">
                                                            <input type="hidden" value="0" name="detail[1][template][]">
                                                            <input type="checkbox"  name="">
                                                            <span class="btn btn-default btn-sm">保存当前版本</span>
                                                        </label>

                                                        <span class="btn btn-default btn-sm delete-this">删除当前行程点</span>
                                                    </div>
                                                    <div class="col-sm-2 image-upload">
                                                        <a class="image image-add" id="uploader_1<?php echo time()?>" data-days="1">选择图片</a>
                                                    </div>
                                                </div>

                                                <hr>

                                                <input  name="detail[1][id][]" value="" type="hidden" />
                                            </div>


                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-2">
                                                <button type="button" class="btn btn-default tour-add">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                    添加行程节点
                                                </button>
                                            </div>
                                        </div>

                                </div>

                                <?php }else{
                                foreach ($lists as $key => $value){ ?>
                                <div class="tour-day-row">
                                    <div class="tour-day-num">Day&nbsp;<?php echo $key;?></div>
                                    <div class="btn-group btn-group-xs" role="group">
                                        <button type="button" class="btn btn-default btn-up">
                                            <span class="glyphicon glyphicon-chevron-up"></span>
                                        </button>
                                        <button type="button" class="btn btn-default btn-down">
                                            <span class="glyphicon glyphicon-chevron-down"></span>
                                        </button>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">当天行程概要</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control" name="detail[<?php echo $key;?>][summary]" rows="3"><?php echo $value[0]['summary']; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="tour-code">
                                        <?php foreach ($value as $k => $v){ ?>
                                        <div class="fileset">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">
                                                    <span class="glyphicon glyphicon-equalizer"></span>
                                                    行程点类型
                                                </label>
                                                <div class="col-sm-5">
                                                    <select name="detail[<?php echo $key;?>][type][]" id="" class="chosen-select form-control">
                                                        <?php foreach ($tourtype as $tour_k => $tour_v){?>
                                                        <option value="<?php echo $tour_k;?>" <?php if($v['type'] ==$tour_k){echo "selected";}?>><?php echo $tour_v;?></option>
                                                        <?php } unset($tour_k);unset($tour_v);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group tour-name">
                                                <label class="col-sm-2 control-label">行程点名称</label>
                                                <div class="col-sm-5">
                                                    <input type="text" name="detail[<?php echo $key;?>][title][]" class="form-control" value="<?php echo $v['title'];?>">
                                                </div>
                                            </div>

                                            <div class="form-group tour-des">
                                                <label class="col-sm-2 control-label">行程点描述</label>
                                                <div class="col-sm-7">
                                                    <textarea rows="5" name="detail[<?php echo $key;?>][describe][]" class="form-control"><?php echo $v['describe'];?></textarea>
                                                    <label class="save-this">
                                                        <input type="hidden" value="<?php echo $v['template'];?>" name="detail[<?php echo $key;?>][template][]">
                                                        <input type="checkbox"  <?php if($v['template'] == 1) {echo 'checked';}?>><span class="btn btn-default btn-sm">保存当前版本</span>
                                                    </label>
                                                    <span class="delete-this btn btn-default btn-sm" data-id="<?php echo $v['id'];?>">删除当前行程点</span>

                                                </div>
                                                <div class="col-sm-2 image-upload">
                                                    <?php
                                                    if($v['pic']):
                                                    ?>
                                                    <a class="image" style="background-image: url(<?php echo $v['pic'];?>!200X200); background-size: cover;" id="uploader_<?php echo time().$k.$key; ?>" data-days="<?php echo $key;?>" ></a>
                                                    <input  name="detail[<?php echo $key;?>][pic][]" value="<?php echo $v['pic'];?>" type="hidden" />
                                                    <?php
                                                    else:
                                                    ?>
                                                    <a class="image image-add" id="uploader_<?php echo time().$k.$key; ?>" data-days="<?php echo $key;?>" >选择图片</a>
                                                    <?php
                                                    endif;
                                                    ?>
                                                </div>
                                            </div>

                                            <hr />
                                            <input  name="detail[<?php echo $key;?>][id][]" value="<?php echo $v['id'];?>" type="hidden" />

                                        </div>

                                        <?php } ?>

                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-3">
                                            <button type="button" class="btn btn-default tour-add">
                                                <span class="glyphicon glyphicon-plus"></span>
                                                添加行程节点
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                <?php }
                                }?>
                            </div>

                        <br>

                        <div class="col-sm-12">
                            <button type="button" class="btn btn-info tour-day-add btn-block">
                                <span class="glyphicon glyphicon-plus"></span>
                                添加行程天数
                            </button>
                        </div>


                        </form>

                    </div>
                    <!-- tour row end -->
                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">保存</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 时间: <?php echo $data['updatetime']?date('Y-m-d H:i:s',$data['updatetime']):date('Y-m-d H:i:s',$data['addtime']) ?></p>
                                <p><span class="glyphicon glyphicon-user"></span> 设计师: <?php echo $designer['username']?> </p>
                            </div>
                            <div class="panel-footer text-right">

                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-default" <?php if ($data['status'] == 7){?>  id="doc"<?php }else{?>disabled="disabled" <?php }?>><?php echo  $data['doc']?'修改':'上传' ?>定制旅行服务书</button>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" id="pdfview" target="_blank">查看</a></li>
                                        <li><a href="#" id="pdfdel">删除</a></li>
                                    </ul>
                                </div>

                                <!-- <button type="button" class="btn btn-default btn-sm" id="doc" >上传定制旅行服务书</button> -->
                                <a href="<?php echo BASE_URL?>tourorder.php?method=history&id=<?php echo $data['id']?>" class="btn btn-default btn-sm">查看更改日志</a>
                                <button type="button" class="btn btn-primary btn-sm" id="save" <?php if(in_array($data['status'],[11, 12])) echo 'disabled'; ?>>保存</button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- tour end -->

                <div class="tour-name-drop">
                    <ul class="tour-name-results">
                        <!-- <li data-des="我是描述信息我是描述信息1" data-src="https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/logo_white_fe6da1ec.png">西班牙</li>
                        <li data-des="我是描述信息我是描述信息2" data-src="https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/logo_white_fe6da1ec.png">西班牙</li>
                        <li data-des="我是描述信息我是描述信息3" data-src="https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/logo_white_fe6da1ec.png">西班牙</li>
                        <li data-des="我是描述信息我是描述信息4" data-src="https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/logo_white_fe6da1ec.png">西班牙</li>
                     --></ul>
                </div>
            </div>

        </div>
        <!-- end main -->

    </div>






    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <link href="<?php echo RESOURCES_URL; ?>css/chosen.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/zdatepicker.css" rel="stylesheet" />
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.chosen.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.zdatepicker.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/nicedit/nicEdit.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.sortable.js"></script>

<script>

    // $(".chosen-select").chosen({disable_search_threshold:100});

    // $(document).on('chosen:ready','.chosen-select',function(){
    //     $('.chosen-select').chosen({disable_search_threshold:100});
    // })


    $("#save").click(function(){

        var data = $("#form1, #form2").serialize();
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=save", data, function(data){

            if (data.s == 0){
               alert('修改成功','success');
                location.reload();
            } else {
               alert(data.err);
            }
        }, "json");
    });



    $(function(){


        // var _tourDay = $('.tour-day');

        // _tourDay.sortable().on('sortupdate',function(){
        //     var _this = $(this) , _length = _this.find('.tour-day-row').length;
        //     console.log(_length);
        //     for (var i = 0; i < _length; i++) {
        //         _this.find('.tour-day-row').eq(i).find('.tour-day-num').text('Day '+(parseInt(i+1)));
        //     };
        // });

        // 点击箭头挪动上下顺序

        function btnNum(){
            for (var i = 0; i < $('.tour-day-row').length; i++) {
                $('.tour-day-row').eq(i).find('.tour-day-num').text('Day '+(parseInt(i+1)));
            };
        }

        function btnDis(){
            $('.tour-day .btn-group').find('button').attr('disabled',false);
            $('.btn-up').first().attr('disabled',true);
            $('.btn-down').last().attr('disabled',true);
        }
        btnDis();

        $('.tour-code').sortable();
        // 首开展开每一天内容
        $(document).on('click','.tour-day-row .tour-day-num',function(){
            $(this).nextAll().toggle();
        })

        $(document).on('click','.tour-add',function(){
            var days = $("#days").val();
            var _dayval = $(this).closest('.tour-day-row').find('.tour-day-num').text();
            var _days = '';
            if(_dayval.length==5) {
                _days = _dayval.substring(4,5);
            }else if(_dayval.length==6) {
                _days = _dayval.substring(4,6);
            }else {
                return false;
            }

            var _this = $(this),
                _tmpl = $('<div class="fileset">'+
                            '<div class="form-group">'+
                                '<label class="col-sm-2 control-label"><span class="glyphicon glyphicon-equalizer"></span> 行程点类型</label>'+
                                '<div class="col-sm-5">'+
                                    '<select name="detail['+_days+'][type]" id="" class="chosen-select form-control">'+
                                    <?php foreach ($tourtype as $tour_k => $tour_v){?>
                                    '<option value="<?php echo $tour_k;?>"><?php echo $tour_v;?></option>'+
                                    <?php } unset($tour_k);unset($tour_v);?>
                                    '</select>'+
                                '</div>'+
                            '</div>'+
                            '<div class="form-group tour-name">'+
                                '<label class="col-sm-2 control-label">行程点名称</label>'+
                                '<div class="col-sm-5">'+
                                    '<input type="text" name="detail['+_days+'][title][]" class="form-control" requried>'+
                                '</div>'+
                            '</div>'+
                            '<div class="form-group tour-des">'+
                                '<label class="col-sm-2 control-label">行程点描述</label>'+
                                '<div class="col-sm-7">'+
                                    '<textarea rows="5" name="detail['+_days+'][describe][]" class="form-control"></textarea>'+
                                    '<label class="save-this"><input type="hidden" value="0" name="detail['+_days+'][template][]"><input type="checkbox"><span class="btn btn-default btn-sm">保存当前版本</span></label>'+' '+
                                    '<span class="btn btn-default btn-sm delete-this">删除当前行程点</span>'+
                                '</div>'+
                                '<div class="col-sm-2 image-upload">'+
                                    '<a class="image image-add" data-days="'+_days+'">选择图片</a>'+
                                '</div>'+
                            '</div>'+
                            '<hr />'+
                            '<input  name="detail['+_days+'][id][]" value="" type="hidden" />'+
                        '</div>');
            var imgadd = _tmpl.find(".image-add"),
                id = "uploader_"+new Date().getTime();
            imgadd.attr("id", id);
            _this.closest('.form-group').prev().append(_tmpl);
            btnDis();
            // _this.closest('.tour-code').append(_tmpl);

            $('.tour-code').sortable();
            inituploader(id);
        });

        // init uploader on document ready
        $(".image").each(function(){
            var id = $(this).attr("id");
            inituploader(id);
        });

        // 删除行程点
        $(document).on('click','.delete-this',function(){
            //console.log(parseInt($(this).data('id0')));
            id = parseInt($(this).data('id'));
            console.log(id);
            if(isNaN(id) == false){
                console.log('s');
                var tar = $(this);

                $.ajax({
                    url: "<?php echo BASE_URL; ?>tourorder.php?method=deldetail",
                    dataType:'json',
                    type:'post',
                    data:{id:id},
                    //beforeSend:function(){},
                    //context: document.body,
                    success: function(e){
                        if(e.s ==0){
                            tar.closest('.fileset').fadeOut(function(){
                                $(this).remove();
                            })

                        }else{
                            alert(e.err, 'error',null);
                            //$("#myButton").attr('disabled',false);
                            //$("#myButton").text('保存');
                        }

                    },
                    complete:function(){
                        //$("#modify_"+id).show();
                        //$(this).remove();
                        //$("#myButton").attr('disabled',false);
                        //$("#myButton").text('保存');

                    }
                });


            }else{
                $(this).closest('.fileset').fadeOut(function(){
                    $(this).remove();
                })
            }


        })


        //var temp = 1;
        var temp = $("#days").val();
        $('.tour-day-add').on('click',function(){
            temp++;
            $("#days").val(Number(temp));
            //console.log(temp);
            var _tmplDay = $('<div class="tour-day-row">'+
                                    '<div class="tour-day-num">Day '+temp+'</div>'+
                                        '<div class="btn-group btn-group-xs" role="group">'+
                                            '<button type="button" class="btn btn-default btn-up">'+
                                                '<span class="glyphicon glyphicon-chevron-up"></span>'+
                                            '</button>'+
                                            '<button type="button" class="btn btn-default btn-down">'+
                                                '<span class="glyphicon glyphicon-chevron-down"></span>'+
                                            '</button>'+
                                        '</div>'+
                                        '<div class="form-group">'+
                                            '<label class="col-sm-2 control-label">当天行程概要</label>'+
                                            '<div class="col-sm-7">'+
                                                '<textarea class="form-control" name="detail['+temp+'][summary]" rows="2"></textarea>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="tour-code">'+
                                            '<div class="fileset">'+
                                                '<div class="form-group">'+
                                                    '<label class="col-sm-2 control-label"><span class="glyphicon glyphicon-equalizer"></span> 行程点类型</label>'+
                                                    '<div class="col-sm-5">'+
                                                        '<select name="detail['+temp+'][type][]" id="" class="chosen-select form-control">'+
                                                            '<option value="flight">航班</option>'+
                                                            '<option value="traffic">交通</option>'+
                                                            '<option value="hotel">酒店</option>'+
                                                            '<option value="dining">餐饮</option>'+
                                                            '<option value="view">景点</option>'+
                                                            '<option value="play">娱乐</option>'+
                                                            '<option value="other">其他体验</option>'+
                                                        '</select>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="form-group tour-name">'+
                                                    '<label class="col-sm-2 control-label">行程点名称</label>'+
                                                    '<div class="col-sm-5">'+
                                                        '<input type="text" name="detail['+temp+'][title][]" class="form-control">'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="form-group tour-des">'+
                                                    '<label class="col-sm-2 control-label">行程点描述</label>'+
                                                    '<div class="col-sm-7">'+
                                                        '<textarea rows="5" class="form-control" name="detail['+temp+'][describe][]"></textarea>'+
                                                        '<label class="save-this"><input type="hidden" value="0" name="detail['+temp+'][template][]"><input type="checkbox"><span class="btn btn-default btn-sm">保存当前版本</span></label>'+' '+
                                                        '<span class="btn btn-default btn-sm delete-this">删除当前行程点</span>'+
                                                    '</div>'+
                                                    '<div class="col-sm-2 image-upload">'+
                                                        '<a class="image image-add" data-days="'+temp+'">选择图片</a>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<hr />'+
                                                '<input  name="detail['+temp+'][id][]" value="" type="hidden" />'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="form-group">'+
                                              '<div class="col-sm-4 col-sm-offset-2">'+
                                                    '<button type="button" class="btn btn-default tour-add">'+
                                                        '<span class="glyphicon glyphicon-plus"></span>'+
                                                        '添加行程节点'+
                                                    '</button>'+
                                              '</div>'+
                                        '</div>'+
                                    '</div>');
            var imgadd = _tmplDay.find('.image-add'),
                id = "uploader_"+new Date().getTime();
            imgadd.attr('id',id);
            $('.tour-day').append(_tmplDay);
            btnDis();
            // $('.tour-day').sortable();

            inituploader(id);
        })

        //
        $(document).on('click','.save-this input[type="checkbox"]',function(){
            var _this = $(this);
            if(_this.prop('checked')){
                _this.prev().val('1');
            }else{
                _this.prev().val('0');
            }
        })

        // $('.tour-name input').keyup(function(){
        $(document).on('keyup','.tour-name input',function(event){

            var _this = $(this) ,
                _txt  = _this.val();
                _offset = _this.closest('.form-group').offset() ,
                _box = $('.tour-name-drop');

            if(event.which==13) {
                    $.get("<?php echo BASE_URL; ?>tourorder.php?method=search",{txt:_txt},function(rs){
                        if(rs.rs.length>0)
                        {
                            $(".tour-name-results").empty();
                            var str = ''
                            $.each(rs.rs,function(i,item){
                                str += '<li data-des="'+item.describe+'" data-src="'+item.pic+'">'+item.title+'</li>'
                            });
                            $(".tour-name-results").append(str);
                        }else {
                            $('.tour-name-drop').hide();
                        }
                    },'JSON');
                $('.tour-name').removeClass('focusin');
                _this.closest('.tour-name').addClass('focusin');
                _box.show().css({
                    'top':_offset.top-10,
                    'left':_offset.left-110,
                    'width':_this.outerWidth()
                });
            }

            _this.blur(function(){
                setTimeout(function(){
                    _box.fadeOut();
                },500)
            })

        })

        $(document).on('click','.tour-name-drop li',function(){
            var _this = $(this),
                _box = $('.focusin'),
                _des = _this.attr('data-des'),
                _src = _this.attr('data-src');
            _this.addClass('active').siblings().removeClass('active');
            _box.find('input').val(_this.text());
            _box.next().find('textarea').val(_des);
            _box.next().find('.image-upload a').removeClass('image-add').empty().append('<img src="'+_src+'" width="100px" height="100px" />')
            _this.closest('.tour-name-drop').hide();
        })

        // 上传定制旅行服务书

        if($('#doc').text().trim()=='上传定制旅行服务书') {
            $('#doc').next().addClass('disabled');
        }



        $(document).on('click','.btn-up',function(){
            var _this = $(this),
                _parent = _this.closest('.tour-day-row'),
                _code = _parent.clone(true),
                _id = _parent.find('.image').attr('id');
                _parent.prev().before(_code).end().remove();


            btnDis();
            btnNum();
            inituploader(_id);
        })

        $(document).on('click','.btn-down',function(){
            var _this = $(this),
                _parent = _this.closest('.tour-day-row'),
                _code = _parent.clone(true),
                _id = _parent.find('.image').attr('id');
                _parent.next().after(_code).end().remove();
            btnDis();
            btnNum();
            inituploader(_id);

        })

        $('.chosen-container').on('click',function(){
            console.log('s');
            $(".chosen-select").chosen({disable_search_threshold:100});
        })

    })

</script>
<script type="text/javascript">
// Custom example logic
var _imgarr = [];
Array.prototype.indexOf = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return i;
    }
    return -1;
};
Array.prototype.remove = function(val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};
function inituploader(id){
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : id, // you can pass an id...
        url : 'plupload.php',
        flash_swf_url : '../js/Moxie.swf',
        silverlight_xap_url : '../js/Moxie.xap',

        filters : {
            max_file_size : '10mb',
            mime_types: [
                {title : "Image files", extensions : "jpg,gif,png"},
                {title : "Zip files", extensions : "zip"}
            ]
        },

        init: {
            PostInit: function() {

                $('#uploadcover').onclick = function() {
                    uploader.start();
                    return false;
                };

            },

            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    uploader.start();
                    return false;
                   // document.getElementById('container').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function(up, file) {
               // document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function(up, err) {
                alert('文件大小超过限制，请上传10M以下的文件。');
                //document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },

            FileUploaded: function(up, file, res) {
                           var data = $.parseJSON(res.response),
                                imgarr = [];
                          console.log($(this));
                          //console.log(data.file);
                          if(!data.s){
                              var rs = data.rs,
                                  _up = $("#"+id),
                                  _days = _up.data("days");

                            if (_days > 0)
                            {
                                _up.empty().removeClass('image-add');
                                // _up.remove();
                                // if($('input[name="detail['+_days+'][pic][]"]')){
                                //     $('input[name="detail['+_days+'][pic][]"]').val(data.rs);
                                // }else{
                                    _up.parent().find('input[type="hidden"]').remove();
                                    _up.parent().append('<input name="detail['+_days+'][pic][]" type="hidden" value="'+data.rs+'">');
                                // }
                                // _up.append('<input name="detail['+_days+'][pic][]" value='+data.file+' />');
                            }
                            else
                            {
                                $('[name="cover"]').val(data.rs);
                            }

                            _up.empty().removeClass('image-add').css({'background-image':'url('+data.rs+'!200X200)'});


                            up.refresh();
                          }else{
                              // $("#upload1").text("上传失败，请重试").prevAll("input").remove();
                          }
            }
        }
    });

    uploader.init();

    return uploader;
}


function pdfuploader(){
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : 'doc', // you can pass an id...
        url : 'plupload.php',
        flash_swf_url : '../js/Moxie.swf',
        silverlight_xap_url : '../js/Moxie.xap',

        filters : {
            max_file_size : '10mb',  
            chunk_size : '1mb',
            mime_types: [
                {title: "All files", extensions: "pdf"}
            ]
        },

        init: {
            PostInit: function() {

                $('#uploadcover').onclick = function() {
                    uploader.start();
                    return false;
                };

            },

            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    uploader.start();
                    return false;
                   // document.getElementById('container').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function(up, file) {
                // console.log(file);
               // document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function(up, err) {
                // console.log(err);
                alert('文件大小超过限制，请上传10M以下的PDF文件。');
                // document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },

            FileUploaded: function(up, file, res) {
                            var data = $.parseJSON(res.response);
                            console.log(data);
                            if(data.s==0){
                                $("input[name='doc']").val(data.rs);
                                $("#pdfview").attr('href',data.rs);
                                $('#doc').text('重新上传旅行服务书').next().removeClass('disabled');

                                // $('#save').before(tmpl);
                                // if($('#save').prev(''))
                                alert('上传成功','success')

                            }else {
                                console.log('error');
                            }
            }
        }
    });

    uploader.init();

    return uploader;
}
pdfuploader();/**/


$("#pdfdel").on('click',function(){
    $("input[name='doc']").val();
    alert('删除成功','success');
    return false;
});

</script>



</body>
</html>
