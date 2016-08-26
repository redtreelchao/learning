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
        <link href="<?php echo RESOURCES_URL; ?>css/bootstrap2.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/matrix-style.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />

    <!--[if lt IE 9]><script src="<?php echo RESOURCES_URL; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo RESOURCES_URL; ?>js/ie10-viewport-bug-workaround.js"></script>
        <script src="<?php echo RESOURCES_URL; ?>js/vue.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/vue-resource.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.bootpag.min.js"></script>
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

            <h1 class="page-header">图集详情</h1>
             <div id="demo">
             <hr/>
        <div class="row-fluid">
            <div class="span12">
                
                <!-- widget-box -->
                <div class="widget-box">
                    
                    <div class="widget-title">
                        
                        <span class="icon"> 
                            <i class="icon-picture"></i> 
                        </span>

                        <h5>图片编辑</h5>

                    </div>
                    
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span8 upload-box">
                                
                                <div class="upload-modify">
                                    <a>
                                        <img :src='info.file' alt="" >
                                    </a>
                                </div>
                                
                                <!-- widget-box -->
                                <div class="widget-box">
                                    
                                    <div class="widget-title device-btn">

                                        <div class="btns">
                                            <a class="btn btn-danger" @click='delpic(info.id)'>删除</a>
                                            <a :href='info.file' download="{{info.title}}.jpg">下载图片</a>

                                        </div> 


                                    </div>
                                  
                                </div>
                                <!-- widget-box end-->

                            </div>

                            <div class="span4 info-upload">
                                <form action="#" method="get" class="form-horizontal">
                                <div class="control-group">
                                        <label class="control-label">所属图片集：</label>
                                        <span>{{info.galleryname}}</span>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">图片标题：</label>
                                        <div class="controls">
                                            <input type="text" placeholder="图片标题" v-model='info.title' class="span11">
                                        </div>
                                    </div>


                                    <div class="control-group">
                                        <label class="control-label">选择图片分类：</label>
                                        <div class="controls">
                                            <select v-model='type'>
                                                <option value="0">一级分类</option>
                                                <option v-for='option in typelist' trackby='$index' :value='option.id'>{{option.name}}</option>
                                                
                                            </select>
                                            <select v-model='subtype'>
                                                <option value="0">二级分类</option>
                                                <option :value="suboption.id" v-for='suboption in subtypelist'>{{suboption.name}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">图片描述：</label>
                                        <div class="controls">
                                            <input type="text" placeholder="图片描述" class="span11" v-model='info.intro'>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">其他信息：</label>
                                        <div class="controls">
                                            <p>像素 {{info.size}};</p>
                                            <p>坐标 {{info.lng}} {{info.lat}};</p>
                                            <p>创建：{{updateTime}} by {{info.uploader}};</p>
                                            <p>更新：{{updateTime}} by {{info.uploader}};</p>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <a  class="btn btn-success span12" @click='editsubmit'>保存</a>
                                    </div>

                                </form>
                            </div>

      、                  </div>

                    </div>
                </div>
                <!-- widget-box end-->    

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
        data:{
                url:'http://api.putike.cn/app.php',
                id:'<?php echo $id;?>',
                token:'<?php echo $token;?>',
                typelist:{},
                type:0,
                subtype:0,
                keyword:'',
                maskshow:false,
                choicelength:0,
                galleryid:'',
                info:{}
        },
        ready:function(){
           // console.log()
             this.$http.post(this.url,{method:'picture_type',token:this.token},{emulateJSON:true})
            .then(function(value){
                var data=JSON.parse(value.data)
                this.typelist=data.data
            })
             this.$http.post(this.url,{method:'picture_load',token:this.token,id:this.id},{emulateJSON:true}).then(function(value){
                var data=JSON.parse(value.data)
                      if(data.code==0){
                        this.info=data&&data.data;
                        this.galleryid=this.info.gallery;
                        this.type=this.info.type;
                        this.subtype=this.info.subtype;
                      }
                      //console.log(value)
                      return value
                    })
            $('.current').html('图片编辑')
        },
        computed:{
            updateTime:function(){
                var time=this.info.update+'000'-0;
                return new Date(time).toLocaleString()
            },
            subtypelist:function(){
                for(item in this.typelist){
                    if(this.typelist[item].id==this.type){
                        return this.typelist[item].sub
                    }
                }
            }
        },
        methods:{
            delpic:function(picid){
             this.$http.post('http://pds.putike.cn/app.php',{method:'picture_delete',token:this.token,id:picid,page:'',format:'json'},{emulateJSON:true})
                    .then(function(value){
                var data=JSON.parse(value.data)
                    if(data.data==true){
                        alert('删除成功')
                        window.location.href='/hotel_pic.php?method=gallery&id='+this.galleryid
                    }else{
                        alert(value.data.message)
                    }
                 })
            },
            choicepic:function(){
                this.choicelength=$('.picchoice:checked').length;
           },
           editsubmit:function(){
            var editdata={
                method:'picture_edit',
                token:this.token,
                id:this.id,
                title:this.info.title,
                intro:this.info.intro,
                gallery:this.galleryid,
                type:this.type,
                subtype:this.subtype,
            }
            var that=this;
            this.$http.post(this.url,editdata,{emulateJSON:true}).then(function(value){
                    //console.log(that.nextedit[0])
                        var data=JSON.parse(value.data)
                        if(data.data==true){
                        window.location.href='/hotel_pic.php?method=gallery&id='+this.galleryid    
                        }else{
                             alert(value.data.message)
                            
                         }
                      return value
                    })
           }
        }
})
</script>
</body>
</html>
