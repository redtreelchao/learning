<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 产品预览</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/preview.css" rel="stylesheet" />

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
        <!--导航 start-->
        <nav id="navOrder" class="navbar navbar-default navbar-fixed-top" role="navigation"role="tablist" aria-multiselectable="true">
            <div class="container-fluid panel panel-default">
                <div class="navbar-header">
                    <h2><?php echo string::text($product['name'], 6, '..'); ?></h2>
                </div>
            </div>
        </nav>

        <!--导航高度-->
        <div class="navHeight"></div>
        <div class="navDown"></div>
        <div id="shade_w"></div>


        <!-- 交易体 -->

<div class="intro text-center" style="padding:20px 0 0;">
    <p class="t font18 fColor5">
        <?php echo $product['name']; ?>
        <small class="fColor666 font12 normal"><?php echo $items[0]['hotelname']; ?></small>
    </p>
    <p>
        <span class="pr">￥<b><?php echo (int)$minprice; ?></b></span>
        <small class="v_b">起</small>
    </p>
    <div class="infoUl">
        <ul class="list-unstyled">
            <li><?php echo str_replace('<br />', '</li><li>', nl2br($product['intro'])); ?></li>
        </ul>
    </div>
    <a class="more" style="border-color:#ccc; color:#ccc;" href="javascript:;">查看详情</a>
</div>

<div class="bgg" style="border-top:0; height:1px;"></div>
<div class="tabModule" role="tabpanel">
    <div class="tab-title">
        <ul class="nav nav-tabs" role="tablist">
            <li class="col-xs-4"><a href="#useintro" aria-controls="useintro" role="tab" data-toggle="tab"><span>使用说明<i class="fa fa-angle-down"></i></span></a></li>
            <li class="divider"></li>
            <li class="col-xs-4"><a href="#bookrule" aria-controls="bookrule" role="tab" data-toggle="tab"><span>预订须知<i class="fa fa-angle-down"></i></span></a></li>
            <li class="divider"></li>
            <li class="col-xs-4"><a href="#refundrule" aria-controls="refundrule" role="tab" data-toggle="tab"><span>退改规则<i class="fa fa-angle-down"></i></span></a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="useintro">
            <ol>
                <li><?php echo str_replace('<br />', '</li><li>', nl2br($product['intro'])); ?></li>
            </ol>
        </div>
        <div role="tabpanel" class="tab-pane" id="bookrule">
            <ol>
                <li><?php echo str_replace('<br />', '</li><li>', nl2br($product['rule'])); ?></li>
            </ol>
        </div>
        <div role="tabpanel" class="tab-pane" id="refundrule">
            <ol>
                <li><?php echo str_replace('<br />', '</li><li>', nl2br($product['refund'])); ?></li>
            </ol>
        </div>
    </div>
</div>


<div class="fh-con">
    <form id="form" action="" method="post">

        <div class="title" style="border-top:0;"><span class="tc">套餐选择</span></div>

        <div class="reservation container-fluid">

            <div class="row">
                <div class="package-content titlebg">
                    <div class="panel-group shpping-cart-package" id="accordion">

                    <?php
                    foreach($items as $item)
                    {
                    ?>
                    <div class="panel panel-default shpping-cart-panel padding22" id="dingwei" data-num="1" data-price="<?php echo $item['price']; ?>" data-allot="<?php echo $item['allot'] - $item['sold']; ?>">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <div class="col-xs-8"><?php echo $item['name']; ?></div>
                                <div class="col-xs-4">
                                    <span class="red">
                                        <?php if($item['allot'] - $item['sold'] > 0){ ?>
                                        ￥<span class="priceT"><?php echo $item['price']; ?></span></span>
                                        <?php }else{ ?>
                                        已售完
                                        <?php } ?>
                                    <span class="option"></span>
                                </div>
                            </h4>
                        </div>
                        <div class="panel-collapse">
                            <div class="panel-body">
                                <div class="subAdd">
                                    <div class="pull-left">
                                        <span>预订<i class="num">1</i>份</span>
                                    </div>
                                    <div class="pull-right">
                                        <span class="btnSub"></span>
                                        <span class="btnAdd"></span>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-con">
                                    <p><?php echo nl2br($item['intro']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="title" style="border-top:0;"><span class="contact_img">联系人信息</span></div>
    <div class="contact container-fluid form-horizontal">
        <div class="form-group has-feedback">
            <label class="col-xs-3 control-label">姓名</label>
            <div class="col-xs-9">
                <div class="input-group">
                    <input type="text" name="contact" class="contactName form-control" value="" placeholder="联系人姓名" />
                    <div class="feedback" style="display:none;"></div>
                </div>
            </div>
        </div>
        <div class="form-group has-feedback">
            <label class="col-xs-3 control-label">手机号</label>
            <div class="col-xs-9">
                <div class="input-group">
                    <input type="tel" name="tel" class="contactTel form-control" value="" placeholder="用于接收短信" />
                    <div class="feedback" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <div class="c64 clearfix" style="height:44px"></div>

    <!--购物车 start-->
    <div class="shopping-cart">
        <div class="shoppingList">
            <ul class="list-unstyled">
            </ul>
        </div>
        <div class="shopping-cart-bottom">
            <div class="col-xs-8">
                <span>￥<b class="allPrice">0</b></span>
                <span class="tip">
                    <i class="triangle"></i>
                </span>
                <div class="clearfix"></div>
            </div>
            <div class="col-xs-4 text-right">
                <button class="btn bookBtn">立即预订</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <!--购物车 end-->
</div>

<!--scroll-->
<style>
.scroll-bar { position:fixed; right:0px; top:0px; width:10px; background:transparent; z-index:99999; overflow:hidden; }
.scroll-bar .handle { position:absolute; left:0px; width:8px; border-radius:10px; overflow:hidden; background:rgba(0,0,0,0.4); cursor:pointer; }
</style>
<div class="scroll-bar"><b class="handle"></b></div>

<script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/mobile-scroll.js"></script>

<script type="text/javascript">
$(function() {

    $(".scroll-bar").mobileScroll();

    var allPrice = 0;

    //加减按钮
    $('.btnAdd').bind('click',function(){
        var index=$(this).index();
        var oParent = $(this).parents('.shpping-cart-panel').eq(0);
        var tickPrice = parseInt(oParent.data('price'), 10);
        var tickNum   = parseInt(oParent.data('num'), 10);
        var allot     = parseInt(oParent.data('allot'), 10);

        tickNum++;
        oParent.data('num', tickNum);

        if(tickNum > allot){
            alert('库存不足');
            return false;
        }

        oParent.find('.num').text(tickNum);
        if (oParent.find('.active').length > 0)
        {
            allPrice=allPrice+tickPrice;
            $('.allPrice').text(allPrice);
            if (allPrice > 20000)
                alert('订单总额大于支付限额，建议分开下单');
        }
    });
    $('.btnSub').bind('click',function(){
        var index   = $(this).index();
        var oParent = $(this).parents('.shpping-cart-panel').eq(0);
        var tickPrice = parseInt(oParent.data('price'), 10);
        var tickNum   = parseInt(oParent.data('num'), 10);
        if(tickNum == 1) return false;

        tickNum--;
        oParent.data('num', tickNum);

        oParent.find('.num').text(tickNum);
        if(oParent.find('.active').length > 0)
        {
            allPrice=allPrice-tickPrice;
            $('.allPrice').text(allPrice);
        }
    });
    $('.reservation .panel-heading').bind('click',function(){
        var oParent   = $(this).parents('.shpping-cart-panel').eq(0);
        var tickPrice = parseInt(oParent.data('price'), 10);
        var tickNum   = parseInt(oParent.data('num'), 10);
        var nowPrice  = tickNum * tickPrice;
        if(oParent.find('.active').length>0)
        {
            allPrice = allPrice - nowPrice;
            $(this).next().find('.proState').val('0');
        }
        else
        {
            allPrice = allPrice + nowPrice;
            $(this).next().find('.proState').val('1');
        }
        $('.allPrice').text(allPrice);
    });

    //点击 样式
    $('.reservation .panel-heading').bind('click',function(){
        //判断是否展开
        if($(this).hasClass('active'))
        {
            $(this).removeClass('active').siblings('.panel-collapse').slideUp();

        }
        else
        {
            $(this).addClass('active').siblings('.panel-collapse').slideDown();
        }
    })
    //shopping cart
    $('.shpping-cart-package .shpping-cart-panel').each(function(){
        var _panelThis=$(this);
        $(this).find('.panel-heading').bind('click',function(){
          var shoppingList=$('.shoppingList ul li');
            if($(this).hasClass('active'))
            {
               shoppingList.eq(_panelThis.index()).show();
            }
            else
            {
               shoppingList.eq(_panelThis.index()).hide();
            }
        })
    })
    $('.tip').click(function(){
       var shoppingList=$('.shoppingList');
       var iHeight=shoppingList.height();
       if(parseInt(shoppingList.css('top'))==-iHeight)
        {
            $('.shoppingList').animate({'top':'0px'});
        }
        else
        {
            $('.shoppingList').animate({'top':-iHeight});
        }
        $('.shpping-cart-package .shpping-cart-panel').each(function(){
            var shoppingList=$('.shoppingList ul li');
            var index=$(this).index();
           if( $(this).find('input').is(':checked'))
           {
            var num=$(this).find('.priceT .num').text();
            shoppingList.eq(index).find('.shopNum').text(num);
           }

        });
        event.stopPropagation();
    })
    $(document).click(function(){
        $('.shoppingList').animate({'top':'123px'})
    })
})
</script>




</body>
</html>




