<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#222222">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PUTIKE &rsaquo; 行程确认单 - <?php echo $data['order']; ?></title>

    <link rel="shortcut icon" href="/favicon.ico" />

    <link href="<?php echo RESOURCES_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo RESOURCES_URL; ?>css/order-confirmation.css" rel="stylesheet" />

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

    <div class="container">
        <div class="main row">

            <form class="content col-md-9">

                <?php include dirname(__FILE__).'/confirmation_tmpl.tpl.php'; ?>

                <input type="hidden" name="from" value="<?php echo $data['from']; ?>" />
                <input type="hidden" name="order" value="<?php echo $order; ?>" />
                <input type="hidden" name="group" value="<?php echo $group; ?>" />
                <input type="hidden" name="_tel" value="<?php echo $data['_tel']; ?>" />
                <input type="hidden" name="_email" value="<?php echo $data['_email']; ?>" />
                <input type="hidden" name="method" value="confirmation" />

            </form>

            <div class="operate col-md-3">

                <div class="viewmode">
                    <div class="sendto">
                        <label>Email</label>
                        <div><span><?php echo $data['_email']; ?></span><a data-toggle="modal" data-target="#editEmail" href="javascript:;">修改邮箱地址</a></div>
                    </div>
                    <div class="sendto">
                        <label>MP</label>
                        <div><span><?php echo $data['_tel']; ?></span><a data-toggle="modal" data-target="#editTel" href="javascript:;">修改手机号</a></div>
                    </div>

                    <button id="send" class="btn btn-primary btn-block">发送确认单</button>
                    <button id="editor" class="btn btn-default btn-block">编辑确认单</button>

                    <h5>确认单记录</h5>
                    <ul>
                        <?php foreach($history as $v){ ?>
                        <li>
                            <b><?php echo $v['username']; ?>&nbsp;</b><?php echo $v['intro']; ?>
                            <span class="time"><?php echo date('m-d H:i', $v['time']); ?></span>
                        </li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="editmode" style="padding-top:20px; display:none;">
                    <button class="btn btn-primary btn-block">保存并预览</button>
                    <button class="btn btn-link btn-block">取消编辑</button>
                </div>

            </div>

        </div>
    </div>


    <div class="modal fade" id="editEmail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">修改邮箱</h4>
                </div>
                <form class="modal-body">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" value="" />
                    </div>
                    <input type="hidden" name="order" value="<?php echo $order; ?>" />
                    <input type="hidden" name="group" value="<?php echo $group; ?>" />
                    <input type="hidden" name="method" value="confirmation" />
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary">保存</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTel" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">修改手机号</h4>
                </div>
                <form class="modal-body">
                    <div class="form-group">
                        <label>手机号</label>
                        <input type="text" class="form-control" name="tel" value="" />
                    </div>
                    <input type="hidden" name="order" value="<?php echo $order; ?>" />
                    <input type="hidden" name="group" value="<?php echo $group; ?>" />
                    <input type="hidden" name="method" value="confirmation" />
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary">保存</button>
                </div>
            </div>
        </div>
    </div>

<script src="<?php echo RESOURCES_URL; ?>js/jquery.min.js"></script>
<script src="<?php echo RESOURCES_URL; ?>js/bootstrap.min.js"></script>
<script>
$(function(){
    // Send
    $("#send").click(function(){
        var btn = $(this);
        btn.prop("disabled", true);
        $.post("./order.php?method=confirmation", {send:1, order:"<?php echo $order; ?>", group:"<?php echo $group; ?>", method:"confirmation"}, function(data){
            btn.prop("disabled", false);
            if (data.s == 0){
                alert("发送成功！", "success");
                location.reload();
            }else{
                alert(data.err, "error");
            }
        }, "json");
    });

    // Editor mode
    $("#editor").click(function(){
        $(".operate .viewmode").hide();
        $(".operate .editmode").show();
        $(".content h2 a").show();
        $("[data-editor]").each(function(){
            var t = $(this), w = t.width(), h = t.height(), v = $.trim(t.text()),
                n = t.data("name"),
                i = t.data("editor") == 'input' ? $("<input />") : $("<textarea />");
            i.attr("name", n).val(v).width(w).height(h);
            t.html(i);
        });
    });

    // Add more info
    $(".content h2 a").click(function(){
        var a = $(this), t = a.data("target"), tg = $("#"+t);
        switch(t){
            case 'rooms':
                var c1 = tg.children(".col-md-2"), c2 = tg.children(".col-md-4"), l = c1.length,
                    cl1 = c1.eq(0).clone(), cl2 = c2.eq(0).clone();
                cl1.children("b").text("Room "+(l+1));
                cl2.children("textarea").attr("name", "rooms["+l+"][people]");
                tg.append(cl1);
                tg.append(cl2);
                break;
            case 'hotels':
            case 'flights':
                var tb = tg.children("tbody"), tr = tb.children("tr"), l = tr.length, c = tr.eq(0).clone();
                c.find("input").each(function(){
                    var i = $(this), n = i.attr("name");
                    i.attr("name", n.replace("0", l)).val('');
                });
                tb.append(c);
                break;
        }
    });

    // Cancel
    $(".editmode .btn-link").click(function(){
        location.reload();
    });

    // Submit
    $(".editmode .btn-primary").click(function(){
        var btn = $(this),
            postdata = $("form.content").serialize();
        btn.prop("disabled", true);
        $.post("./order.php?method=confirmation", postdata, function(data){
            btn.prop("disabled", false);
            if (data.s == 0){
                location.reload();
            }else{
                alert(data.err, "error");
            }
        }, "json");
    });

    // Change Email/Tel
    $(".modal .modal-footer .btn-primary").click(function(){
        var btn = $(this), modal = btn.parents(".modal"),
            postdata = modal.find("form").serialize();
        btn.prop("disabled", true);
        $.post("./order.php?method=confirmation", postdata, function(data){
            btn.prop("disabled", false);
            if (data.s == 0){
                location.reload();
            }else{
                alert(data.err, "error");
            }
        }, "json");
    });
});
</script>

</body>
</html>