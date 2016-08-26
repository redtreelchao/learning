<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 定制行程规划</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/tour.css" rel="stylesheet" />

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
、
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

                <h1 class="page-header">定制行程规划</h1>

                <div class="table-responsive well">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>行程编号</th>
                                <th>客户姓名</th>
                                <th>目的地类型</th>
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
                                <td><span><?php echo  $data['areaname'];?></span></td>
                                <td><?php echo substr($data['order'], 1); ?></td>
                                <td><?php
                                    $s = $status[$data['status']];
                                    switch ($data['status']) {
                                        case 1: // 待确认
                                        case 5: // 需要修改
                                            echo '<span class="label label-warning">'.$s.'</span>'; break;
                                        case 2: // 优先
                                        case 7: // 支付成功
                                            echo '<span class="label label-danger">'.$s.'</span>'; break;
                                        case 3: // 无效
                                        case 10: // 已过期
                                            echo '<span class="label label-default">'.$s.'</span>'; break;
                                        default:
                                            echo '<span class="label label-info">'.$s.'</span>'; break;
                                    }
                                ?></td>
                                <td><?php echo $data['progress']; ?>%</td>
                                <td>
                                    <a href="tourorder.php?method=price&id=<?php echo $data['id']?>" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-yen hidden-md"></span>
                                        <span class="hidden-xs hidden-sm">价格与备注</span>
                                    </a>

                                    <a href="tourorder.php?method=preview&id=<?php echo $data['id']?>" class="btn btn-sm btn-default">
                                        <span class="glyphicon glyphicon-eye-open hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 预览</span>
                                    </a>
                                </td>
                            <tr>
                        </tbody>
                    </table>
                </div>

                <!-- tour begin -->
                <form class="row" style="margin-top:30px;">

                    <div class="col-md-8 col-lg-9 form-horizontal">

                        <h3 class="page-header">Step1.行程概要</h3>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">行程名称</label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo $data['title'] ?>" name="title" class="form-control" placeholder="请输入行程名称" />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-2 image-upload">
                                <?php if ($data['cover']) { ?>
                                <a class="image" id="cover" style="background-image:url(<?php echo $data['cover']; ?>!200X200);">
                                    <input name="cover" type="hidden" value="<?php echo $data['cover']; ?>"/>
                                </a>
                                <?php } else { ?>
                                <a class="image image-add" id="cover">选择图片</a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">行程亮点</label>
                            <div class="col-md-4 col-sm-10">
                                <textarea name="edge"  class="form-control" rows="3"><?php echo $data['edge']?></textarea>
                            </div>

                            <div class="hidden-md hidden-lg col-xs-12 clearfix">&nbsp;</div>

                            <label class="col-sm-2 control-label">行程说明</label>
                            <div class="col-md-4 col-sm-10">
                                <textarea name="intro" class="form-control" rows="3"><?php echo $data['intro']?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">费用包含</label>
                            <div class="col-sm-4">
                                <textarea name="include" class="form-control" rows="3"><?php echo $data['include'];?></textarea>
                            </div>

                            <div class="hidden-md hidden-lg col-xs-12 clearfix">&nbsp;</div>

                            <label class="col-sm-2 control-label">费用不包含</label>
                            <div class="col-sm-4">
                                <textarea name="without" class="form-control" rows="3"><?php echo $data['without'];?></textarea>
                            </div>
                        </div>


                        <h3 class="page-header" style="margin-top:30px;">Step2.行程详情</h3>

                        <?php
                        $day = 0;
                        if (!$details)
                            $details = array(array('id'=>'', 'day'=>1, 'summary'=>'', 'type'=>'', 'title'=>'', 'describe'=>'', 'pic'=>''));

                        foreach ($details as $v) {
                            if ($day != $v['day']) {
                        ?>
                            <div class="detail-day row">

                                <div class="day col-xs-12">
                                    <div class="num">Day&nbsp;<?php echo $v['day']; ?></div>

                                    <div class="btn-group btn-group-xs" role="group">
                                        <button type="button" class="btn btn-default btn-up">
                                            <span class="glyphicon glyphicon-chevron-up"></span>
                                        </button>
                                        <button type="button" class="btn btn-default btn-down">
                                            <span class="glyphicon glyphicon-chevron-down"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="col-sm-2 hidden-md hidden-lg control-label">行程概要</label>
                                        <div class="col-sm-9 col-md-offset-2">
                                            <textarea class="form-control" name="summary[]" maxlength="30" placeholder="当天行程概要，最多30字" rows="3"><?php echo $v['summary']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                                <div class="point col-xs-12">

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">行程点类型</label>
                                        <div class="col-sm-5">
                                            <select name="type[]" class="ui-select form-control">
                                                <option>请选择..</option>
                                                <?php foreach ($type as $tk => $tv){?>
                                                <option value="<?php echo $tk;?>" <?php if($tk==$v['type']) echo 'selected'; ?>><?php echo $tv;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group tour-name">
                                        <label class="col-sm-2 control-label">行程点名称</label>
                                        <div class="col-sm-5">
                                            <input type="text" name="title[]" class="form-control" value="<?php echo $v['title']; ?>" placeholder="请按回车键查询" />
                                        </div>
                                    </div>
                                    <div class="form-group tour-des">
                                        <label class="col-sm-2 control-label">行程点描述</label>
                                        <div class="col-sm-7">
                                            <textarea rows="5" name="describe[]" class="form-control"><?php echo $v['describe']; ?></textarea>
                                            <label class="template">
                                                <input type="hidden" value="0" name="template[]" />
                                                <input type="checkbox" /> 保存当前版本
                                            </label>
                                        </div>
                                        <div class="col-sm-2 image-upload">
                                            <?php if ($v['pid']) { ?>
                                            <a class="image" style="background-image: url(<?php echo $v['pic'];?>!200X200); background-size: cover;" id="uploader_<?php echo time().$k.$key; ?>" data-days="<?php echo $key;?>" >
                                                <input name="pic[]" value="<?php echo $v['pic'];?>" type="hidden" />
                                            </a>
                                            <?php } else { ?>
                                            <a class="image image-add" id="uploader_<?php echo $v['day'].$v['id']; ?>" data-days="1">选择图片</a>
                                            <?php } ?>
                                        </div>

                                        <hr class="col-sm-9 col-sm-offset-2" />
                                    </div>

                                    <input name="id[]" value="" type="hidden" value="<?php echo $v['id']; ?>" />
                                    <input name="day[]" value="<?php echo $v['day']; ?>" type="hidden" />
                                </div>

                            <?php
                            if ($day != $v['day']) {
                                $day = $v['day'];
                            ?>
                                <div class="point col-xs-12 form-group">
                                    <div class="col-sm-9 col-sm-offset-2">
                                        <a class="create-point" href="javascript:;">
                                            <span class="glyphicon glyphicon-plus"></span> 添加行程节点
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>

                            <div class="detail-day row">
                                <div class="col-xs-12">
                                    <a class="create-day" href="javascript:;">
                                        <span class="glyphicon glyphicon-plus"></span> 添加行程天数
                                    </a>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                    </div>
                    <!-- tour row end -->

                    <div class="col-md-4 col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">行程规划</h3>
                            </div>
                            <div class="panel-body panel-sm">
                                <p><span class="glyphicon glyphicon-time"></span> 时间: <?php echo $data ? date('Y-m-d H:i:s',$data['updatetime']) : '未保存'; ?></p>
                                <p><span class="glyphicon glyphicon-user"></span> 设计师: <?php echo $data ? $data['nickname'] : '未保存'; ?></p>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="hidden" name="id" value="<?php echo  $data['id']?>" />
                                <input type="hidden" name="order" value="<?php echo  $data['order']?>" />

                                <button type="button" class="btn btn-default btn-sm" id="doc">上传定制旅行服务书</button>
                                <button type="button" class="btn btn-primary btn-sm" id="save">保存</button>
                            </div>
                        </div>
                    </div>

                </form>

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
$(".chosen-select").chosen({disable_search_threshold:100});

    // $(document).on('chosen:ready','.chosen-select',function(){
    //     $('.chosen-select').chosen({disable_search_threshold:100});
    // })


    $("#save").click(function(){

        var data = $("#form1, #form2").serialize();
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=save", data, function(data){

            if (data.s == 0){
               alert('修改成功','success');
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

        $(document).on('click','.btn-up',function(){
            var _this = $(this),
                _parent = _this.closest('.tour-day-row'),
                _code = _parent.clone(true);
                _parent.prev().before(_code).end().remove();
            // if(_parent.length == 1) {
            //     _this.attr('disabled',true);
            // }
            // _parent().next().after().append(_parent.clone(true));
            btnDis();
            btnNum();
        })

        $(document).on('click','.btn-down',function(){
            var _this = $(this),
                _parent = _this.closest('.tour-day-row'),
                _code = _parent.clone(true);
                _parent.next().after(_code).end().remove();
            // if (_parent.index() == parseInt($('.tour-day-row').length-1)){
            //     _this.attr('disabled',true);
            // }
            btnDis();
            btnNum();
        })

        function btnDis(){
            $('.btn-group').find('button').attr('disabled',false);
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
                                                '<label class="col-sm-2 control-label">行程点类型</label>'+
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
                                                    '<label class="save-this"><input type="hidden" value="0" name="detail['+_days+'][template][]"><input type="checkbox">保存当前版本</label>'+
                                                '</div>'+
                                                '<div class="col-sm-2 image-upload">'+
                                                    '<a class="image image-add" data-days="'+temp+'">选择图片</a>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        '<hr />'+
                                        '<input  name="detail['+_days+'][id][]" value="" type="hidden" />');
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
                                                    '<label class="col-sm-2 control-label">行程点类型</label>'+
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
                                                        '<label class="save-this"><input type="hidden" value="0" name="detail['+temp+'][template][]"><input type="checkbox">保存当前版本</label>'+
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



            // var url ='',
            //     key = _this.val()
            // $.post(url, key, function(data, status) {
            //     if (data.rs) {
            //         $(".tour-name-drop").empty();
            //         for (var i = 0; i < i.length; i++) {
            //             var _li = $("<li data-des="+data.des+" data-src="+data.des+">" + items[i] + "</li>");
            //             $(".tour-name-drop").append(_li);
            //         }
            //     }
            // });

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
                //document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },

            FileUploaded: function(up, file, res) {
                           var data = $.parseJSON(res.response),
                                imgarr = [];
                          console.log(data);
                          //console.log(data.file);
                          if(!data.s){
                              var rs = data.rs,
                                  _up = $("#"+id),
                                  _days = _up.data("days");

                            if (_days > 0)
                            {
                                _up.empty().removeClass('image-add');
                                // _up.remove();
                                if($('input[name="detail['+_days+'][pic][]"]')){
                                    $('input[name="detail['+_days+'][pic][]"]').val(data.rs);
                                }else {
                                    _up.parent().append('<input name="detail['+_days+'][pic][]" type="hidden" value="'+data.rs+'">');
                                }
                                // _up.append('<input name="detail['+_days+'][pic][]" value='+data.file+' />');
                                // console.log('ss');
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
            mime_types: [
                {title: "All files", extensions: "doc,pdf,docx"}
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
                //document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },

            FileUploaded: function(up, file, res) {
                            var data = $.parseJSON(res.response);
                            console.log(data);
                            if(data.s){
                                console.log('file'+''+data.file);

                                $('#doc').remove();
                                $('#save').before('<div class="btn-group btn-group-sm">'+
                                        '<button type="button" class="btn btn-default" id="doc">重新上传旅行服务书</button>'+
                                        '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                                            '<span class="caret"></span>'+
                                            '<span class="sr-only"></span>'+
                                        '</button>'+
                                        '<ul class="dropdown-menu">'+
                                            '<li><a href="#">查看</a></li>'+
                                            '<li><a href="#">删除</a></li>'+
                                        '</ul>'+
                                    '</div>'+' ')
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




</script>



</body>
</html>
