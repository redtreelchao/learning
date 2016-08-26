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

    <!--[if lt IE 9]><script src="<?php echo RESOURCES_URL; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>
     <script src="<?php echo RESOURCES_URL; ?>js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo RESOURCES_URL; ?>js/vue.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/vue-resource.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo RESOURCES_URL; ?>js/html5shiv.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/respond.min.js"></script>

    <![endif]-->
</head>
<style>
  .fancybox img{
        max-width: 800px;
        cursor: pointer;
    }
  .enlarge {
    width: 25px;
    height: 20px;
    position: absolute;
    left: 0%;
    top: 10%;
    background: black;
    border-radius: 0 10px 10px 0;
    opacity: 0.7;
}
.span2{
    width: 22%;
    float: left;
}
.pic-box img{
    max-width: 100%;
    height: auto
}
.enlarge .icon-search{
  margin-left: 5px;
}
</style>
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

            <h1 class="page-header">图片库</h1>
            <div id="demo">
        <hr/>
        <div class="row-fluid">
            <div class="span12">
                
                <!-- widget-box -->
                <div class="widget-box" v-for='item in list'>
                    
                    <div class="widget-title">

                        <a class="buttons"  href="/hotel_pic.php?method=gallery&id={{item.id}}">查看全部</a> 
                        
                        <span class="icon"> 
                            <i class="icon-picture"></i> 
                        </span>

                        <h5>{{item.name}}</h5>

                        <div class="time">{{item.time}}</div>

                    </div>
                    
                    <div class="widget-content clearfix">
                        <ul class="thumbnails">
                            <li class="span2" v-for='img in item.pictures' track-by='$index'><a href="/hotel_pic.php?method=detail&id={{img.id}}">
                            <div class="pic-box">
                                    <img :src="img.file+'!/both/320x200'" alt="" >
                                  </div>
                                <!--  <div class="enlarge">
                                    <a class="lightbox_trigger" href="" @mouseenter='showbigger(img,$index)'>
                                        <i class="icon-search"></i>
                                    </a> 
                                </div> -->
                                <div class="pick">
                                    <input  type="checkbox" class='picchoice' :data-file='img.file' :data-id='img.id' :data-title='img.title' />选取
                                </div>
                            </a></li>
                        </ul>
                    </div>
                </div>
                <!-- widget-box end-->   
                        <!-- widget-box -->
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
        <!-- widget-box end-->  
        <div style="display:none" class="downloadwrap" id='linkbox'><a v-for='item in downloadlist' :href='item.file' :download='item.title'></a></div>
                <div class="mask" v-show='bigger' @click='hidepic'></div>
                <div class="fancybox" style="display:none" @mouseleave='hidepic'>                  
                    <img :src="bigpic.file" alt="编辑" @click='goEdit'>
                     <div class="pick">
                    <input type="checkbox" id='bigchoice' @click='choicebigpic(bigpic)'/>选取
                </div>
                    <p>{{bigpic.title}}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid end -->
</div>

        </div>
    <!-- end main -->

</div>
<script type="text/javascript">

var demo = new Vue({
        el: '#demo',
        data:{
                url:'http://api.putike.cn/app.php',
                token:'<?php echo $token;?>',
                list:[],
                bigpic:'',
                downloadlist:[],
                choicelength:0,
                bigger:false,
                shownum:0
        },
        ready:function(){
          var that=this;
            $('.container-fluid').on('click','.picchoice',function(){
              //console.log($('.picchoice:checked').length)
              that.choicelength=$('.picchoice:checked').length;
              var now={id:$(this).data('id'),file:$(this).data('file'),title:$(this).data('title')}
                if(this.checked){
                    that.downloadlist.push(now)
                   // $('#linkbox a').attr('download')
                }else{
                     that.downloadlist.splice(that.downloadlist.indexOf(now),1)
                }
            })
          var count=0;
          //实现某人很二的图片居中需求
                //     for(j in this.list){
                //     for(i in this.list[j].pictures){
                //     var size=this.list[j].pictures[i].size.split('*');
                //     var width=size[0]-0;
                //     var height=size[1]-0;
                //     console.log(size)
                //      if(width>4*height/3){
                //              $('.pic-box img:eq('+count+')').css('height','100%');
                //             // var picwidth=$('.pic-box').width();
                //             // var pocheight=$('.pic-box').height();
                //             // if(width>3*picwidth){ 
                //             //   height=height/width*(3*picwidth)
                //             //   width=3*picwidth;
                //             // }
                //             // console.log(width)
                //             // var left=(width-picwidth)/2;
                //             // var top=(height-pocheight)/2
                //             // $('.pic-box img:eq('+i+')').css('left',-left);
                //             // $('.pic-box img:eq('+i+')').css('top',-top);
                //             // console.log(0)
                //             var picwidth=$('.pic-box').width();
                //             var pocheight=$('.pic-box').height();
                //             var newwidth=width*pocheight/height;
                //             console.log(newwidth)
                //             var left=(newwidth-picwidth)/2;
                //             $('.pic-box img:eq('+count+')').css('left',-left);
                            
                //             console.log($('.pic-box img:eq('+count+')').css('height'))
                //           }else{
                //             $('.pic-box img:eq('+count+')').css('width','100%');
                //             console.log(1)
                //           }
                //           count++;
                //     //console.log(that.photolist.pictures[item].size.split('*'))
                //   }
                // }
                this.load();
        },
        methods:{
           load:function(){
            this.$http.post(this.url,{method:'picture_recently',token:this.token,limit:10},{emulateJSON:true}).then(function(value){
                    // console.log(value.data)
                    var data=JSON.parse(value.data)
                      if(data.code==0){
                        this.list=data.data;
                        console.log(this.list)
                      }
                      //console.log(value)
                      return value
                    })
                //     .then(function(value){
                //       var count=0;
                //     for(j in this.list){
                //       for(i in this.list[j].pictures){
                //        var size=this.list[j].pictures[i].size.split('*');
                //        var width=size[0]-0;
                //        var height=size[1]-0;
                //        console.log(size)
                //        //实现某人很二的图片居中需求
                //         if(width>4*height/3){
                //           $('.pic-box img:eq('+count+')').css('height','100%');
                //             // var picwidth=$('.pic-box').width();
                //             // var pocheight=$('.pic-box').height();
                //             // if(width>3*picwidth){ 
                //             //   height=height/width*(3*picwidth)
                //             //   width=3*picwidth;
                //             // }
                //             // console.log(width)
                //             // var left=(width-picwidth)/2;
                //             // var top=(height-pocheight)/2
                //             // $('.pic-box img:eq('+i+')').css('left',-left);
                //             // $('.pic-box img:eq('+i+')').css('top',-top);
                //             // console.log(0)
                //             var picwidth=$('.pic-box').width();
                //             var pocheight=$('.pic-box').height();
                //             var newwidth=width*pocheight/height;
                //             console.log(newwidth)
                //             var left=(newwidth-picwidth)/2;
                //             $('.pic-box img:eq('+count+')').css('left',-left);
                            
                //             console.log($('.pic-box img:eq('+count+')').css('height'))
                //           }else{
                //             $('.pic-box img:eq('+count+')').css('width','100%');
                //             console.log(1)
                //           }
                //           count++;
                //     //console.log(that.photolist.pictures[item].size.split('*'))
                //   }
                // }
                //     })
           },
           delpic:function(picid){
            this.$http.post('http://pds.putike.cn/app.php',{method:'picture_delete',id:picid,token:this.token},{emulateJSON:true})
                    .then(function(value){
                    if(value.data.data==true){
                        // for(pic in this.downloadlist){
                        //     //console.log(this.photolist.pictures[pic]['id'])
                        //     if(picid==this.list.pictures[pic]['id']){
                        //         this.photolist.pictures.splice(pic,1)
                        //         //console.log(pic)
                        //     }
                        //   //  console.log(pic)
                        // }
                        this.load();
                        //删除图片过后重载一次
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
                
                // if(this.downloadlist.indexOf(file)==-1){
                //     //console.log(this.downloadlist)
                //     this.downloadlist.push(file)
                // }else{
                //     this.downloadlist.splice(this.downloadlist.indexOf(file),1)
                // }

           },
           //  showbigger:function(item,num){
           //      this.bigpic=item;
           //      this.shownum=num;
           //      this.bigger=true;
           //      if($('.picchoice')[this.shownum].checked){
           //          $('#bigchoice')[0].checked=true
           //      }else{
           //          $('#bigchoice')[0].checked=false
           //      }
           //        var regx=/\w+/;
           //       var rs=regx.exec(item.size).toString();
           //       rs=rs<1000?rs:1000;
           //       var promise=new Promise(function(resolve,reject){
           //          resolve('SUCCESS');
           //       });
           //       promise
           //       .then(function(value){
           //          $('.fancybox').css('margin-left',-rs/2+'px');
           //          $('.fancybox').css('margin-top','-20%');
           //          return value
           //       })
           //       .then(function(value){
           //          $('.fancybox').show(400)
           //       })
                
                 
           // }
        }

  // el: '#demo',

  // data: {
  //   branches: ['master', 'dev'],
  //   currentBranch: 'master',
  //   commits: null
  // },

  // created: function () {
  //   this.fetchData()
  // },

  // watch: {
  //   currentBranch: 'fetchData'
  // },

  // filters: {
  //   truncate: function (v) {
  //     var newline = v.indexOf('\n')
  //     return newline > 0 ? v.slice(0, newline) : v
  //   },
  //   formatDate: function (v) {
  //     return v.replace(/T|Z/g, ' ')
  //   }
  // },

  // methods: {
  //   fetchData: function () {
  //     var xhr = new XMLHttpRequest()
  //     var self = this
  //     xhr.open('GET', apiURL + self.currentBranch)
  //     xhr.onload = function () {
  //       self.commits = JSON.parse(xhr.responseText)
  //     }
  //     xhr.send()
  //   }
  // }
})
</script>
</body>
</html>
