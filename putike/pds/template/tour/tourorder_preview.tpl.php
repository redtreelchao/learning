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
    <link href="<?php echo RESOURCES_URL; ?>css/common.css" rel="stylesheet" />
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
    
    <style>
        #main ,
        footer , .custom-pay-tel { position: absolute; width: 100%; }
        #main { top: 0; bottom: 0; left: 0; overflow: auto; -webkit-overflow-scrolling: touch; -webkit-box-sizing: border-box; box-sizing: border-box; }
        footer , .custom-pay-tel { position: absolute; bottom: 0; }
        footer { height: 49px; z-index: 111; }
        footer + #main { bottom: 49px; }
        .custom-pay-tel { height: 130px; }
        .custom-pay-tel + #main { bottom: 130px; }

        footer > .pull-left { width: 50%; }
        footer.clearfix:before { content: ''; width: 1px; height: 23px; position: absolute; left: 50%; top: 13px; background: #fff; }
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

                <h1 class="page-header">定制行程预览</h1>

                <div id="order-list" class="table-responsive">
                    <table class="table">
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
                                <td><?php echo sprintf("%.2f", $data['progress']);?>%</td>
                                <td>
                                    <a href="tourorder.php?method=edit&id=<?php echo $data['id']?>" class="btn btn-sm btn-default">
                                        <span class="glyphicon glyphicon-pencil hidden-md"></span>
                                        <span class="hidden-xs hidden-sm"> 编辑行程</span>
                                    </a>

                                    <a href="#" class="btn btn-sm btn-default">

                                        <span class="glyphicon glyphicon-pencil hidden-md"></span>
                                        <span class="hidden-xs hidden-sm" id="save"  data-card="<?php echo $data['card']?>" data-status="<?php echo $data['status']?>" >发布行程</span>
                                    </a>
                                </td>
                            <tr>    
                        </tbody>
                    </table>
                </div>
               
                <!-- tour begin -->
                <div class="tour-preview row">
                    

                    <form role="form" class="col-md-8 col-lg-9 form-horizontal">
                        
                        <!-- 定制卡预览 -->
                        <div id="preview">

                            <div id="main" class="custom-my clearfix">
                                
                                <div class="custom-my-header">
                                    <h2><?php echo $data['title']?></h2>
                                    <span><?php echo get_status($data['status'])?></span>
                                    
                                </div>
                            
                                <div class="custom-my-designer">
                                    <div class="custom-my-photo">
                                        <img src="<?php echo $data['avatar'];?>" width="52" alt="">
                                        <strong><?php echo $data['nickname'];?></strong>
                                    </div>
                                    <div class="custom-my-detail">
                                        <h2>尊敬的<?php echo $data['contact']?>，您好！</h2>
                                        <p>
                                            <?php echo $data['edge'];?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="custom-my-list custom-pay-step-list">
                                    
                                    <?php foreach ($details as  $v):
                                      if($v['seq']==0):                    
                                    ?>
                                    <div class="custom-pay-step-item">
                                        <label>Day <em><?php echo $v['day']?></em></label>
                                        <div class="custom-pay-step-price clearfix">
                                            <p><?php echo $v['summary']?></p>
                                            
                                        </div>
                                    </div>
                                    <?php 
                                    endif;
                                    endforeach;?>
                                    
                                

                                    <div class="custom-pay-step-item">
                                        <label class="icon"><span class="icon-m-pdf"></span></label>
                                        <div class="custom-pay-step-price clearfix">
                                            <p>行程说明</p>
                                        </div>
                                    </div>

                                    <div class="custom-pay-step-item">
                                        <label class="icon"><span class="icon-m-price"></span></label>
                                        <div class="custom-pay-step-price clearfix">
                                            <p>费用说明</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="custom-my-total custom-my-total2">
                                    
                                    <h2>￥<?php echo $data['total']?></h2>
                                    <strong>成人<?php echo $data['adults']?>名 儿童<?php echo $data['kids']?>名</strong>

                                </div>
                            </div>

                            
                            <div class="frame">
        
                                <header class="clearfix">
                                    <a href="" class="pull-left">

                                        <img src="<?php echo $data['avatar'];?>" width="52" alt=""><span><?php echo $data['nickname'];?></span>
                                    </a>
                                    <a href="" class="pull-right tel">电话沟通</a>
                                </header>
                                
                                <footer>
                                    <a href="javascript:;" class="btn btn-blue btn-font-small btn-close">关闭</a>
                                </footer>
                                <div id="page" class="custom-my clearfix">
                                        
                                    <div class="custom-my-list custom-pay-step-list">
                                    <?php foreach ($details as  $v):
                                        if($v['seq']==0):       
                                    ?>

                                        <div class="custom-pay-step-item">
                                            <label>Day <em><?php  echo $v['day']?></em></label>
                                            <div class="custom-pay-step-price clearfix">
                                                <p><?php echo $v['summary']?></p>
                                            </div>
                                        </div>
                                    <?php endif;?>
                                            
                                        <article>
                                            <p>
                                            <?php echo $v['title']?>
                                            </p>
                                            <p>
                                            <img src="<?php echo  $v['pic']?>" alt="">
                                            <?php echo $v['describe']?>
                                            </p>
                                        </article>
                                    <?php endforeach;?>

                                        <div class="custom-pay-step-item">
                                            <label class="icon"><span class="icon-m-pdf"></span></label>
                                            <div class="custom-pay-step-price clearfix">
                                                <p>行程说明</p>
                                            </div>
                                        </div>

                                        <article>
                                            <p><?php echo $data['intro']?></p>
                                        </article>

                                        <div class="custom-pay-step-item">
                                            <label class="icon"><span class="icon-m-price"></span></label>
                                            <div class="custom-pay-step-price clearfix">
                                                <p>费用说明</p>

                                            </div>
                                        </div>

                                        <article>
                                            <p><?php echo $data['include']?> <?php echo $data['without'];?></p>
                                        </article>
                                        

                                        

                                     
                                    </div>
                                </div>
                            </div>
                        

                        </div>

                            


                        <!--  -->

                    </form>

                    <!-- tour row -->
                  


                </div>
                <!-- tour end -->

            </div>

        </div>
        <!-- end main -->

    </div>


    



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>

    <script src="<?php echo RESOURCES_URL; ?>js/nicedit/nicEdit.js"></script>

<script>



    $(document).on('click',"span#save",function(){
        if($(this).data('status') == 11 || $(this).data('status') ==12){
            alert('客户已退款，禁止发布','error');
            return false;
        }

        var _this = $(this),
         card = _this.data("card")
         data = { id:<?php echo $id?>, status:6,card:card };
        $.post("<?php echo BASE_URL; ?>tourorder.php?method=save_preview", data, function(data){
            
            if (data.rs == 1){
               alert('修改成功','success');
               _this.text('已发布');
               _this.attr('id','');
                location.reload();
            } else {
               alert(data.err);
            }
        }, "json");
    });

    $(function(){
        var _frame = $('.frame');
        $('.custom-pay-step-item').on('click',function(){
            _frame.show();
        })
        $('.btn-close').on('click',function(){
            _frame.hide();
        })

        $('#my-msg').on('click',function(){
            $('.custom-before-msg').removeClass('hidden').prev().addClass('hidden');
            $('footer.msg').removeClass('hidden').prev().addClass('hidden');
        })

    })


    
</script>




</body>
</html>
