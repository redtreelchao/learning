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
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />

    <!--[if lt IE 9]><script src="<?php echo RESOURCES_URL; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo RESOURCES_URL; ?>js/ie10-viewport-bug-workaround.js"></script>
        <script src="<?php echo RESOURCES_URL; ?>js/vue.min.js"></script>
         <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.bootpag.min.js"></script>
        <script src="<?php echo RESOURCES_URL; ?>js/vue-resource.js"></script>
   

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

            <h1 class="page-header">流程通知</h1>
            <div id="demo">
                  <hr>
        <div class="row-fluid">
        
            <div class="span12">
            
                <!-- widget-box -->
                <div class="widget-box">
                    <!-- widget-title -->
                    <div class="widget-title"> 
                        <span class="icon"> <i class="icon-briefcase"></i></span>
                        <h5 >有 {{processlist.length}} 个酒店图片集任务</h5>
                    </div>
                    <!-- widget-title end -->
                    
                    <!-- widget-content -->
                    <div class="widget-content">                        
                        <div class="row-fluid">
                            <div class="span12">
                            <table class="table table-bordered table-invoice-full">
                                <thead>
                                    <tr>
                                        <th class="head0">编号</th>
                                        <th class="head1">地区</th>
                                        <th class="head0">酒店名称</th>
                                        <th class="head1">创建时间</th>
                                        <th class="head0">创建人</th>
                                        <th class="head1">备注</th>
                                        <th class="head0">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for='item in processlist' trackby='$index'>
                                        <td>{{item.id}}</td>
                                        <td>{{item.countryname}}-{{item.cityname}}</td>
                                        <td class="right">{{item.name}}</td>
                                        <td class="right">{{createtime[$index]}}</td>
                                        <td class="right">{{item.creator}}</td>
                                        <td class="right">{{item.remarks}}</td>
                                        <td class=""><a @click='bind(item)'>+上传图片</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <!-- widget-content -->
                </div>
                <!-- widget-box end -->
                <div id="callBackPager" class="pagination">
                </div>
            </div>
        </div>
            </div>

        </div>
    </div>
    <!-- end main -->

</div>
<script type="text/javascript">
var demo = new Vue({
        el: '#demo',
    data:function(){
            return {
                url:'http://api.putike.cn/app.php',
                token:'<?php echo $token;?>',
                processlist:[
                ]
            }
        },
        ready:function(){
              var that=this
                 this.$http.post(this.url,{method:'picture_queue',token:this.token,limit:20},{emulateJSON:true}).then(function(value){
                     var data=JSON.parse(value.data)
                      if(data.code==0){
                        this.processlist=data.data&&data.data.list;
                      }
                      //console.log(value)
                      return value
                    })
                    .then(function(value){
                      var pageInterval=setInterval(function(){
                        if(!$('#callBackPager')[0]){
                          return;
                        }else{
                          that.showpage(value);
                          clearInterval(pageInterval)
                        }
                      },50)
                        return value;
                })
        },
        methods:{
            // bind:function(item){
                   
            //          this.$http.post('http://pds.putike.cn/app.php',{method:'picture_gallery_update',token:this.token,name:item.name,city:item.cityname},{emulateJSON:true})
            //                 .then(function(value){
            //                     var data=JSON.parse(value.data)
            //                     if(data.code==0){
            //                          var galleryid=data.data;
            //                          this.$http.post(this.url,{method:'picture_bind',token:this.token,hotel:item.id,id:value.data.data},{emulateJSON:true}).then(function(value){
            //                             //console.log(value.data.data)
            //                             if(value.data.data==true){
            //                                 // console.log('创建成功')
            //                                 this.$route.router.go({ name: 'upload',params: {albumid:galleryid},query:{name:item.name}})
            //                             }else{
            //                                 alert(value.data.message)
            //                             }
            //                           //console.log(value)
            //                           return value
            //                         })
            //                     }
                                
            //              })
            //     },
            showpage:function(value){
            var data=JSON.parse(value.data)
            var that=this;
            $('#callBackPager').bootpag({
                             total: data.data.page.total,
                             page:1,
                             maxVisible: 10
                        }).on("page", function(event, /* page number here */ num){
                             //that.search({page:num},1)
                               that.$http.post(that.url,{method:'picture_queue',token:that.token,limit:20,page:num},{emulateJSON:true})
                                    .then(function(value){
                                     var data=JSON.parse(value.data)
                                    if(data.code==0){
                                        that.processlist=data.data&&data.data.list;
                                      }
                                    return value
                                    })
                        });
           }
        },
        computed:{
            createtime:function(){
               return this.processlist.map(function(x){
                return  new Date(x.createtime+'000'-0).toLocaleString();
                })
            }
        }
})
</script>
</body>
</html>
