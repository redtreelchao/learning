<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 行程定制</title>

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

                <h1 class="page-header">行程定制</h1>
                   <!-- page and operation -->
                <div class="row">
                    <form class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline" action="" method="GET" role="form">
                        
                     

                        <!-- search -->
                        <div class="input-group hidden-xs hidden-sm">
                            <input type="search" name="keyword" class="form-control" value="<?php echo $keyword; ?>" data-search=".table-responsive" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                        <!-- end search -->
                    </form>

                    <div class="col-xs-8 col-sm-6 col-md-6 col-lg-4 text-right">
                        <!-- page -->
                        <ul class="pagination">
                            <?php include(dirname(__FILE__).'/../page.tpl.php');  ?>
                        </ul>
                        <!-- end page -->
                    </div>
                </div>
                <!--  page and operation  -->

             
                <div id="order-list" class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>编号</th>
                                <th>提交时间</th>
                                <th>客户姓名</th>
                                <th>目的地</th>
                                <th>出发时间</th>
                                <th>行程天数</th>
                                <th>预算</th>
                                <th>状态</th>                                
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><input type="checkbox" class="checkbox checked-all" value="" /></th>
                                <th>编号</th>
                                <th>提交时间</th>
                                <th>客户姓名</th>
                                <th>目的地</th>
                                <th>出发时间</th>
                                <th>行程天数</th>
                                <th>预算</th>
                                <th>状态</th>                                
                                <th>操作</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php 
                            foreach ($list as $v):
                            ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox" value="<?php echo $v['id']?>" /></td>
                                <td><?php echo $v['order'];?></td>
                                <td><?php echo date('Y-m-d H:i',$v['addtime']);?></td>
                                <td><?php echo $v['contact'];?></td>
                                <td><?php echo $v['area_name']?></td>
                                <td><?php echo $v['departure'];?></td>
                                <td><?php echo $v['days'];?></td>
                                <td><?php echo $v['budget'];?></td>

                                <td><?php echo get_status($v['status']);?></td>
                                <td class="md-nowrap">
                                        <a href="/tourorder.php?method=edit&id=<?php echo $v['id'] ?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 编辑</span></a>
                                    </td>
                            <tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>


                <!-- page and operation -->
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                        <!--- filter -->
                        <!-- <div class="btn-group" style="margin:20px 0px;">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                            </ul>
                        </div> -->
                        <!-- end filter -->
                    </div>

                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-9 text-right">
                        <!-- page -->
                        <ul class="pagination">
                            <?php include(dirname(__FILE__).'/../page.tpl.php');  ?>
                        </ul>
                        <!-- end page -->
                    </div>
                </div>
                <!--  page and operation  -->


            </div>
        </div>
        <!-- end main -->

    </div>


    

    



    <script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo RESOURCES_URL; ?>js/admin.js"></script>


<script>
$("#add").on('click',function(){

    $("#addmodal").modal('show');
});

 $("#savebtn").click(function(){
        
        var data = $('#addform').serialize();
        $.post("<?php echo BASE_URL; ?>area.php?method=save", data, function(data){
            
            if (data.s == 0){
               alert('添加成功');
            } else {
               alert(data.err);
            }
        }, "json");
    });

</script>
</body>
</html>
