<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 酒店图片库</title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
      <link href="<?php echo RESOURCES_URL; ?>css/matrix-style.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap2.min.css" rel="stylesheet" />


    <!--[if lt IE 9]><script src="<?php echo RESOURCES_URL; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo RESOURCES_URL; ?>js/ie10-viewport-bug-workaround.js"></script>
        <script src="<?php echo RESOURCES_URL; ?>js/vue.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/vue-resource.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>

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

            <h1 class="page-header">解救失联图片</h1>
             <div id="demo">
             <hr/>
        <div class="row-fluid">
            <div class="span12">
                
                <div class="widget-box">
                    
                    <div class="widget-title">
                        
                        <span class="icon"> 
                            <i class="icon-picture"></i> 
                        </span>

                        <h5>待关联图片集（{{totalnum}}）</h5>

                    </div>
                    
                    <div class="widget-content">
                        <ul class="thumbnails">
                            
                            <li class="span3" v-for='item in photolist' trackby='$index'>
                                <div class="picbox">
                                    <a v-link="{ name: 'gallerydetail',params: { id: item.id }}"><img :src="item.cover||blankpng" alt="" ></a>
                                </div>
                                <div class="pic-text">
                                    <span><span class="status">备用名：</span> {{item.name}}</span>
                                  
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
    
            </div>
        </div>
          <div class="alert alert-success" role="alert" style="display:none">
          <strong>关联成功</strong> 
        </div>
        <div class="alert alert-danger" role="alert" style="display:none">
          <strong>关联失败!</strong>
        </div>
        </div>
        </div>
    </div>
    <!-- end main -->

</div>
</body>
<script type="text/javascript">
    var demo = new Vue({
        el: '#demo',
     data:{ 
                url:'http://api.putike.cn/app.php',
                token:'<?php echo $token;?>',
                hotellist:[{value:'请选择',label:'请选择'}],
                blankpng:'http://p-product-pic.b0.upaiyun.com/2016/0325/1e65beac.png',
                        photolist:[],
                labelname:'关联酒店'
        },
        ready:function(){
                this.$http.post('http://pds.putike.cn/app.php',{method:'picture_unbind',token:this.token},{emulateJSON:true}).then(function(value){
                    var data=JSON.parse(value.data)
                      if(data.code==0){
                        this.photolist=data&&data.data||[]
                      }
                      //console.log(value)
                      return value
                    })
        },
        events:{
            dosearch:function(value){
                     this.$http.post(this.url,{method:'hotel_search',token:this.token,name:value},{emulateJSON:true}).then(function(value){
                    var data=JSON.parse(value.data)
                      if(data.code==0){
                        if (data&&data.data&&data.data.list[0]) {
                             this.hotellist=data&&data.data&&data.data.list
                            
                        }else{
                            this.hotellist=[{value:'没有匹配',label:'没有匹配'}]
                         }
                      }
                      //console.log(value)
                      return value
                    })
                },
                bind:function(value){
                   //console.log(value)
                   var res=value;
                     this.$http.post(this.url,{method:'picture_bind',token:this.token,hotel:res.value,id:value.num},{emulateJSON:true}).then(function(value){
                        console.log(value.data.data)
                        if(value.data.data==true){
                            $('.alert-success').show();
                            $('.status')[res.index].innerHTML='已关联:'
                            $('.status:eq('+res.index+')').addClass('succ')
                            $('.alert-success').show()
                            setTimeout(function(){
                                $('.alert-success').hide()
                            },2000)
                        }else{
                             $('.alert-danger').show();
                              $('.status')[res.index].innerHTML='关联失败:'
                              $('.status:eq('+res.index+')').addClass('fail')
                              $('.alert-danger').show()
                            setTimeout(function(){
                                $('.alert-danger').hide()
                            },2000)
                            //console.log(this.status)
                        }
                      //console.log(value)
                      return value
                    })
                }
        },
        methods:{
           
        },
        computed:{
            totalnum:function(){
                return this.photolist.length;
            },
            hotelOptions:function(){
                return this.hotellist.map(function(x){
                    return {value:x.id,label:x.name}
                })
            }
        }
})
</script>
</html>
