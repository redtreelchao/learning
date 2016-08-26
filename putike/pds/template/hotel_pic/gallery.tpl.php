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
    <script src="<?php echo RESOURCES_URL; ?>js/exif.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo RESOURCES_URL; ?>js/html5shiv.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/respond.min.js"></script>

    <![endif]-->
    <style type="text/css">
        .pic-box img {
            max-width: 100%;
            height: auto;
        }
        a{
            cursor: pointer;
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

            <h1 class="page-header">图集详情</h1>
            <div id="demo">
                        <hr/>
        
        <!-- widget-box -->
        <div class="widget-box">
            <!-- widget-title -->
            <div class="widget-title"> 
                <span class="icon"> <i class="icon-picture"></i></span>
                <h5 >{{photolist.name}}</h5>

                <div class="sort span9">
                    <ul class="thumbnails">
                        <li class="span3">
                            <select v-model='sortKey' @change='resort'>
                                <option value="update">上传时间</option>
                                <option value="type">照片类型</option>
                            </select>
                        </li>
                        <li class="span3">                        
                            <select v-model='filterKey' @change='refilter'>
                                <option value="">全部大类</option>
                                <option value="{{item.id}}" v-for='item in typelist'>{{item.name}}</option>
                            </select>
                        </li>
                        <li class="span3"><span>{{photolength}}张图片</span></li>
                    </ul>
                </div>
            </div>
            <!-- widget-title end -->
            <!-- widget-content -->
            <div class="widget-content">                        
                <div class="row-fluid">
                    <div class="span12">
        
                        <ul class="thumbnails hotel-photos">
                            <!-- <li class="span2" v-for='item in photolist | orderBy sortKey |filterBy filterKey in "type"'> -->
                            <li class="span2" v-for='item in photolist.pictures' trackby='$index'>
                                <a href="/hotel_pic.php?method=detail&id={{item.id}}">
                                <div class="pic-box">
                                    <img :src="item.file+'!/both/320x200'" alt="" >
                                  </div>
                               <!--  <div class="enlarge">
                                    <a class="lightbox_trigger" href="" @mouseenter='showbigger(item,$index)'>
                                        <i class="icon-search"></i>
                                    </a> 
                                </div> -->
                                    <p><span>{{item.size}}</span>
                                        <bdo>{{item.title}}</bdo>
                                    </p>
                                </a>
                                <div class="pick">
                                    <input  type="checkbox" class='picchoice' @click='choicepic(item,$index)'/>选取
                                </div>
                                
                            </li>
                            <li class="span2 add">
                                <a @click='upload'>
                                    <input type="file" name="" multiple="true" @change='uploadhandle' id="upbtn" style="display: none">
                                    <span class="icon"> <i class="icon-plus"></i></span>添加图片
                                </a>    
                            </li>
                        </ul>   
                        <div id="callBackPager" class="pagination">
                        </div> 
                    </div>
                </div>
            </div>
            <!-- widget-content -->
        </div>
         <div class="widget-box">
            
            <div class="widget-title device-btn">

                <div class="btns">
                    <a class="btn btn-danger" @click='multipledelpic'>删除</a>
                    <a class="btn btn-success" disabled>使用</a>
                    <button  id='downbtn' @click='downloadfiles'>下载图片</button>
                </div> 

                <div class="text">已选择<bdo>{{choicelength}}</bdo>张图片</div>

            </div>
          
        </div>
        <div style="display:none" class="downloadwrap" id='linkbox'><a v-for='item in downloadlist' :href='item.file' download></a></div> 
            </div>

        </div>
    </div>
    <!-- end main -->

</div>
<script type="text/javascript">
    var demo = new Vue({
        el: '#demo',
         data:{
                id:'<?php echo $id;?>',
                url:'http://api.putike.cn/app.php',
                token:'<?php echo $token;?>',
                photolist:{pictures:[]},
                typelist:'',
                sortKey:'update',
                choicelength:0,
                filterKey:0,
                downloadlist:[],
                bigpic:'',
                bigger:false,
                shownum:0
        },
        ready:function(){
            this.$http.post(this.url,{method:'picture_type',id:this.id,token:this.token},{emulateJSON:true})
                    .then(function(value){
                    var data=JSON.parse(value.data)
                    this.typelist=data.data
                    return value
                 });
                 $('.current').html(this.photolist.name);
                    var that=this;
                  this.$http.post(this.url,{method:'picture_gallery',id:this.id,token:this.token,limit:50,order:this.sortKeys,type:this.filterKey},{emulateJSON:true})
                    .then(function(value){
                    var data=JSON.parse(value.data)
                    this.photolist=data.data
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
                // .then(function(value){
                //     that.piclocate(that)              
                  
                // })

                 if($('.current')){
                  $('.current').html(this.photolist.name)
                 }
        },
        computed:{
            photolength:function(){
                return this.photolist.pictures.length
            },

        },
        methods:{
           // sortBy:function(key){
           //  this.sortKeys=this.key;
           // }
           upload:function(){
            return $('#upbtn').click()
           },
           uploadhandle:function(e){
                var that=this;
                var fileList = e.target.files;
                if(fileList.length==0){
                    return false
                }
                for(var item in fileList){
                    (function(item){
                    var lat,lng;
                    EXIF.getData(fileList[item], function() {
                       var latArray=EXIF.getTag(fileList[item],'GPSLatitude');     
                       var lngArray=EXIF.getTag(fileList[item],'GPSLongitude');
                       if(latArray){lat=latArray[0]+latArray[1]/60+latArray[2]/3600;}
                        if(lngArray){ lng=lngArray[0]+lngArray[1]/60+lngArray[2]/3600;}
                       
                        //console.log(JSON.stringify(EXIF.getAllTags(fileList[item])))
                        });

                          // var reader=new FileReader();
                          // //  console.log(that.num)
                          //   if(fileList[0]&& fileList[0].type&& fileList[0].type.match('image.*')){
                          //       reader.readAsDataURL(fileList[0]);
                          //     }
                          //   reader.onload = (function(file){
                          //       return function(e){
                          //          // console.log(that.rooms[that.isShow])
                          //           if(that.rooms[that.isShow].pics==null){
                          //               that.rooms[that.isShow].pics=[];
                          //           }
                          //         var dataURL = reader.result;
                          //            var data = {
                          //                   method: 'picture_upload',
                          //                   token: localStorage['token'],
                          //                   file: dataURL,
                          //                   name:fileList[0].name
                          //               }
                          //               var jqPromise = $.post('http://api.putike.cn/app.php', data);
                          //               var realPromise = Promise.resolve(jqPromise);
                          //               realPromise.then(function(value){
                          //                   var value= JSON.parse(value);
                          //                    that.rooms[that.isShow].pics.push(value.data.file);
                          //               }) 
                          //       }
                          //   // console.log(reader.result)
                          //   // console.log(dataURL)

                          //   //  var output = document.getElementById('output');
                          //   // for(var item in input.files){

                          //     // }
                          //    //  var str = "<img data-id='123145' src='"+dataURL+"'>";
                          //    //  $('#output').append(str);
                          //    // console.log(str)
                          //    //  output.src = dataURL;
                          //   })(fileList[0])


                      var reader = new FileReader();
                     
                      if(fileList[item]&& fileList[item].type&& fileList[item].type.match('image.*')){
                        reader.readAsDataURL(fileList[item]);
                        // reader0.readAsBinaryString(fileList[item])
                      }
                      reader.onload = function(){
                      var dataURL = reader.result;
                      
                      that.$http.post(that.url,{method:'picture_upload',token:that.token,lng:lng,lat:lat,file:dataURL,name:fileList[item].name},{emulateJSON:true})
                      .then(function(value){
                        var data=JSON.parse(value.data)
                        //console.log(value)
                        that.photolist.pictures.push({id:data.data.id,name:fileList[item].name,file:data.data.file})
                      })
                    };
                    
                    })(item)
                }
           },
           delpic:function(picid){
            this.$http.post('http://pds.putike.cn/app.php',{method:'picture_delete',id:picid,token:this.token},{emulateJSON:true})
                    .then(function(value){
                    var data=JSON.parse(value.data)
                    if(data.data==true){
                        for(pic in this.photolist.pictures){
                            //console.log(this.photolist.pictures[pic]['id'])
                            if(picid==this.photolist.pictures[pic]['id']){
                                this.photolist.pictures.splice(pic,1)
                                //console.log(pic)
                            }
                          //  console.log(pic)
                        }
                    }
                 })
           },
           multipledelpic:function(){
                for (i in this.downloadlist){
                    this.delpic(this.downloadlist[i].id)
                }
           },
           goEdit:function(){
            this.bigger=false;
            $('.fancybox').hide();
            this.$route.router.go({ name: 'galleryedit',params: {id:this.bigpic.id}})
           },
           hidepic:function(){
            this.bigger=false;
            $('.fancybox').hide(200);
            $('#bigchoice')[0].checked=false;
           },
           showbigger:function(item,num){
                this.bigpic=item;
                this.shownum=num;
                this.bigger=true;
                if($('.picchoice')[this.shownum].checked){
                    $('#bigchoice')[0].checked=true
                }else{
                    $('#bigchoice')[0].checked=false
                }
                  var regx=/\w+/;
                 var rs=regx.exec(item.size).toString();
                 rs=rs<1000?rs:1000;
                 var promise=new Promise(function(resolve,reject){
                    resolve('SUCCESS');
                 });
                 promise
                 .then(function(value){
                    $('.fancybox').css('margin-left',-rs/2+'px');
                    $('.fancybox').css('margin-top','-20%');
                    return value
                 })
                 .then(function(value){
                    $('.fancybox').show(400)
                 })
                
                 
           },
           downloadfiles:function(){
                var box=document.getElementById('linkbox');
                var link=box.getElementsByTagName('a')
               console.log(link)
              // link.click();
                for(var i=0;i<link.length;i++){
                    link[i].click();
                  //  a.click();
                }
           },
           choicebigpic:function(item){
                if($('.picchoice')[this.shownum].checked){
                    $('.picchoice')[this.shownum].checked=false
                    this.downloadlist.splice(this.downloadlist.indexOf(item),1)
                    
                }else{
                    $('.picchoice')[this.shownum].checked=true
                    this.downloadlist.push(item)
                }
                this.choicelength=$('.picchoice:checked').length;
           },
           choicepic:function(item,num){
               // console.log(that)
                this.choicelength=$('.picchoice:checked').length;
                if($('.picchoice')[num].checked){
                    this.downloadlist.push(item)
                }else{
                     this.downloadlist.splice(this.downloadlist.indexOf(item),1)
                }
                // if(this.downloadlist.indexOf(file)==-1){
                //     //console.log(this.downloadlist)
                //     this.downloadlist.push(file)
                // }else{
                //     this.downloadlist.splice(this.downloadlist.indexOf(file),1)
                // }

           },
           resort:function(){
             this.$http.post(this.url,{method:'picture_gallery',id:this.id,token:this.token,limit:50,order:this.sortKey,type:this.filterKey},{emulateJSON:true})
                    .then(function(value){
                       var data=JSON.parse(value.data)
                    this.photolist=data.data
                    return value
                 })
                  // .then(function(value){
                  //     this.piclocate(this)
                  // })
           },
           refilter:function(){
            this.$http.post(this.url,{method:'picture_gallery',id:this.id,token:this.token,limit:50,order:this.sortKeys,type:this.filterKey},{emulateJSON:true})
                    .then(function(value){
                    var data=JSON.parse(value.data)
                    this.photolist=data.data
                 })
                  // .then(function(value){
                  //   this.piclocate(this)
                  // })
           },
           showpage:function(value){
            var that=this,data=JSON.parse(value.data);
            $('#callBackPager').bootpag({
                             total: data.data.page.total,
                             page:1,
                             maxVisible: 10
                        }).on("page", function(event, num){
                             //that.search({page:num},1)
                               that.$http.post(that.url,{method:'picture_gallery',id:that.id,token:that.token,limit:50,order:that.sortKeys,type:that.filterKey,page:num},{emulateJSON:true})
                                    .then(function(value){
                                       var data=JSON.parse(value.data)
                                    that.photolist=data.data;
                                    return value
                                    })
                                    .then(function(value){
                                         that.piclocate(that)
                                        return value;
                                    })
                        });
           }
        }
})
</script>
</body>

</html>
