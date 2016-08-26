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
    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap2.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/admin.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/matrix-style.css" rel="stylesheet" />

    <!--[if lt IE 9]><script src="<?php echo RESOURCES_URL; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo RESOURCES_URL; ?>js/ie10-viewport-bug-workaround.js"></script>
         <script src="<?php echo RESOURCES_URL; ?>js/vue.min.js"></script>
         <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
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

            <h1 class="page-header">类别管理</h1>
            <div id="demo">
            <hr>
        <div class="row-fluid">
        
            <div class="col-md-12">
            
                <!-- widget-box -->
                <div class="widget-box">
                    <!-- widget-title -->
                    <div class="widget-title"> 
                        <a class="buttons btn btn-info btn-mini" @click='newtype'>新建类别</a>
                        <span class="icon"> <i class="icon-briefcase"></i></span>
                        <h5 >图片库分类</h5>
                    </div>
                    <!-- widget-title end -->
                    
                    <!-- widget-content -->
                    <div class="widget-content">                        
                        <div class="row-fluid">
                            <div class="span12">
                            
                            <table class="table table-bordered table-invoice-full text-center" v-for='item in typelist' trackby='$index'>
                                <thead>
                                    <tr>
                                        <th class="head0">{{item.id}}</th>
                                        <th class="head1">{{item.name}}</th>
                                        <th class="head0">
                                            <a class="tip"  data-original-title="编辑" @click='typeedit(item.id,item.name)'><span>编辑</span></a> 
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for='sub in item.sub'>
                                        <td>{{sub.pid}}{{sub.id}}</td>
                                        <td>{{sub.name}}</td>
                                        <td>                                            
                                            <a class="tip" data-original-title="编辑" @click='subtypeedit(sub.pid,sub.id,sub.name)'><span>编辑</span></a> 
                                           <!--  <a class="tip" data-original-title="删除"><i class="icon-remove"></i></a> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <!-- widget-content -->
                </div>
                <!-- widget-box end -->
            
            </div>
        </div>
        <div class="mask" v-show='maskshow' @click='endtype' style="display: none"></div>
        <div class="popbox span6" v-show='typestyle' style="display: none">
    
    <div class="widget-box">
        <div class="widget-title"> 
            <span class="icon"> 
                <i class="icon-align-justify"></i>
            </span>
            <h5>新增类别</h5>
        </div>
        <div class="widget-content nopadding toppadding">
            <form action="#" method="get" class="form-horizontal">

                <div class="control-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="controls">
                        <label>
                            <div class="radio" >
                                <span class=""><input type="radio" name="typechoice" v-model='typechoice' style="" value="0"></span>
                                新建大类
                            </div>
                        </label>
                        <label>
                            <div class="radio">
                                <span class=""><input type="radio" name="typechoice" style="" v-model='typechoice' value="1"></span>
                                新建小类
                            </div>
                        </label>
                    </div>
                </div>
                
         <div class="control-group" v-show='typechoice=="1"'>
                    <label class="control-label">所属大类 :</label>
                    <div class="controls">
                        <select v-model='newtypeobj.pid'>
                            <option value="0">请选择</option>
                            <option v-for='item in typelist' :value="item.id">{{item.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">类别名称 :</label>
                    <div class="controls">
                        <input type="text" class="" placeholder="类别名称" v-model='newtypeobj.name'>
                    </div>
                </div>

               <!--  <div class="control-group">
                    <label class="control-label">类别序号 :</label>
                    <div class="controls">
                        <input type="text" class="" placeholder="类别序号" v-model='newtypeobj.id'>
                    </div>
                </div> -->

                <div class="form-actions">
                    <a  class="btn btn-success" @click='newtypeSubmit'>保存类别</a>
                </div>
            </form>
        </div>
    </div>

</div>

<div class="popbox span6" v-show='typeeditshow' style="display:none">
    
    <div class="widget-box">
        <div class="widget-title"> 
            <span class="icon"> 
                <i class="icon-align-justify"></i>
            </span>
            <h5>修改类别</h5>
        </div>
        <div class="widget-content nopadding toppadding">
            <form action="#" method="get" class="form-horizontal">
                
                <div class="control-group" v-show='typechoice=="1"'>
                    <label class="control-label">所属大类 :</label>
                    <div class="controls">
                        <select v-model='newtypeobj.pid'>
                            <option value="0">请选择</option>
                            <option v-for='item in typelist' :value="item.id">{{item.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">类别名称 :</label>
                    <div class="controls">
                        <input type="text" class="" placeholder="类别名称" v-model='newtypeobj.name'>
                    </div>
                </div>
<!-- 
                <div class="control-group">
                    <label class="control-label">类别序号 :</label>
                    <div class="controls">
                        <input type="text" class="" placeholder="类别序号" v-model='newtypeobj.id'>
                    </div>
                </div> -->

                <div class="form-actions">
                    <a  class="btn btn-success" @click='newtypeSubmit'>保存类别</a>
                </div>
            </form>
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
                typelist:{},
                typestyle:false,
                typechoice:0,
                newtypeobj:{pid:0,name:'',id:0},
                edittypeobj:{pid:0,name:'',id:0},
                typeeditshow:0
            }
        },
        ready:function(){
              this.$http.post(this.url,{method:'picture_type',token:this.token},{emulateJSON:true}).then(function(value){
                    var data=JSON.parse(value.data)
                      if(data.code==0){
                        this.typelist=data.data;
                      }
                      //console.log(value)
                      return value
                    })
        },
        computed:{
            maskshow:function(){
                return this.typeeditshow||this.typestyle
            }
        },
        methods:{
           newtype:function(){
                this.typestyle=true
           },
           endtype:function(){
            this.typestyle=false;
            this.typeeditshow=false
           },
           typeedit:function(id,name){
             this.typeeditshow=1;
             this.newtypeobj.id=id;
             this.newtypeobj.name=name;
           },
           subtypeedit:function(pid,id,name){
            this.typeeditshow=1;
             this.newtypeobj.id=id;
             this.newtypeobj.name=name;
             this.newtypeobj.pid=pid;
           },
           newtypeSubmit:function(){
            var that=this
            this.$http.post(this.url,{method:'picture_type_update',token:this.token,name:this.newtypeobj.name,pid:this.newtypeobj.pid,id:this.newtypeobj.id},{emulateJSON:true}).then(function(value){
                        var data= JSON.parse(value.data)
                      if(data.code==0){
                           that.$http.post(this.url,{method:'picture_type',token:this.token},{emulateJSON:true}).then(function(value){
                             var data= JSON.parse(value.data)
                          if(data.code==0){
                            that.typelist=data.data;
                          }
                          //console.log(value)
                          return value
                        })
                        that.typestyle=false;
                        that.typeeditshow=false;
                        that.newtypeobj={pid:0,name:'',id:0}
                      }
                      return value
                    })
           }
        }
})
</script>
</body>
</html>
