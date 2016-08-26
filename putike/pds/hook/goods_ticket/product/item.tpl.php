<?php define('BASE_URL', '/'); ?>
<form role="form" class="form-horizontal">

    <div class="form-group">
        <label class="col-sm-2 control-label">产品名称</label>
        <div class="col-sm-9">
            <input type="text" name="name" class="form-control" value="<?php echo $data['name']; ?>" />
        </div>
    </div>

    <div id="goods" class="form-group">
        <label class="col-sm-2 control-label">关联商品</label>
        <div class="col-sm-9">
            <input id="goodsid" type="hidden" name="goods" value="<?php echo $data['objpid']; ?>" />
            <div class="input-group">
                <input type="text" class="form-control" value="<?php echo $data['goods_name']; ?>" <?php if($data['goods_name']) echo 'disabled'; ?> />
                <span class="input-group-btn">
                    <button class="button btn btn-default" type="button"><span class="glyphicon <?php echo $data['goods_name'] ? 'glyphicon-remove' : 'glyphicon-search'; ?>"></span></button>
                </span>
            </div>
            <ul class="dropdown-menu" role="menu" style="left:15px; right:15px; max-height:200px; overflow:auto;"></ul>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">产品说明</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="intro" rows="3"><?php echo $data['intro']; ?></textarea>
        </div>
    </div>

    <input type="hidden" name="type" value="goods" />
    <input type="hidden" id="item-pid" name="pid" value="<?php echo $pid; ?>" />
    <input type="hidden" id="item-id" name="id" value="<?php echo $data['id']; ?>" />
</form>

<script>
$(function(){

    var search = function(btn, page){
        var btn = $(btn);
        var input = btn.parent().prev("input");
        var ul = $("#goods .dropdown-menu");
        var keyword = input.val();
        if (keyword == "") {
            alert("请输入有效的关键词查询", "warning", null, "#item-form .modal-body");
            return false;
        }

        input.unbind("blur");
        if (page === undefined) page = 1;
        btn.prop("disabled", true).children(".glyphicon").removeClass("glyphicon-search").addClass("glyphicon-refresh glyphicon-loading");

        if (page == 1) {
            ul.stop(true).html("<li style=\"text-align:center; color:#999;\"><a href=\"javascript:;\">正在搜索..</a></li>").show();
        } else {
            ul.stop(true);
            var more = ul.children(".more");
            more.children("a").unbind("click").text("正在加载..");
        }

        // search list
        $.get("<?php echo BASE_URL; ?>goods.php", {method:"list", keyword:keyword, page:page}, function(data){
            if(data.s == 0){
                var list = data.rs.list;
                if (page == 1) {
                    ul.html("");
                } else {
                    var more = ul.children(".more");
                    more.remove();
                }

                for(x in list) {
                    ul.append("<li><a data-code=\""+list[x].id+"\" href=\"javascript:;\">"+list[x].name+"</a></li>");
                }

                if (data.rs.page.total > data.rs.page.now) {
                    ul.append("<li class=\"more\"><a href=\"javascript:;\">下一页</a></li>");
                    ul.children(".more").click(function(){
                        search(btn, data.rs.page.next);
                    });
                }

                input.focus().one("blur", function() {
                    ul.stop(true).delay(100).hide(1);
                });

                ul.find("li:not(.more) a").bind("click", function(){
                    var li = $(this);
                    ul.stop(true).hide();
                    input.val(li.text()).prop("disabled", true);
                    $("#goodsid").val(li.data("code"));
                    btn.children(".glyphicon").removeClass("glyphicon-search").addClass("glyphicon-remove");
                });

            }else{
                alert("搜索数据失败，请重试~", "warning", null, "#item-form .modal-body");
                ul.hide();
            }
            btn.prop("disabled", false).children(".glyphicon").addClass("glyphicon-search").removeClass("glyphicon-refresh glyphicon-loading");
        }, "json");
    }

    $("#goods .button").unbind("click").bind("click", function(){
        var btn = $(this);
        var icon = btn.children(".glyphicon");
        if (icon.is(".glyphicon-remove")) {
            btn.parent().prev("input").prop("disabled", false);
            icon.removeClass("glyphicon-remove").addClass("glyphicon-search");
        } else {
            search(this);
        }
    });

    $(".ui-select").chosen({disable_search_threshold:10, width:"100%", no_results_text:"未找到..", placeholder_text_single:"请选择.."});

});
</script>
