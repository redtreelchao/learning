<?php define('BASE_URL', '/'); ?>
<form role="form" class="form-horizontal">

    <div id="page1" class="page">

        <div class="form-group">
            <label class="col-sm-2 control-label">产品名称</label>
            <div class="col-sm-9">
                <input type="text" name="name" class="form-control" value="<?php echo $data['name']; ?>" placeholder="不填默认为房型名" />
            </div>
        </div>

        <div id="hotel" class="form-group">
            <label class="col-sm-2 control-label">关联酒店</label>
            <div class="col-sm-9">
                <input type="hidden" id="hotelid" name="hotel" value="<?php echo $data['objpid']; ?>" />
                <?php if(empty($data['hotel_name'])) { ?>
                <div class="input-group">
                    <input type="text" class="form-control" value="" />
                    <span class="input-group-btn">
                        <button class="button btn btn-default" type="button" data-type="hotel"><span class="glyphicon glyphicon-search"></span></button>
                    </span>
                </div>
                <?php }else{ ?>
                <input type="text" class="form-control" value="<?php echo $data['hotel_name']; ?>" disabled />
                <?php } ?>
                <ul class="dropdown-menu" role="menu" style="left:15px; right:15px; max-height:200px; overflow:auto;"></ul>
            </div>
        </div>

        <div id="room" class="form-group">
            <label class="col-sm-2 control-label">关联房型</label>
            <div class="col-sm-6">
                <select name="room" class="form-control ui-select">
                    <?php if(!isset($rooms)){ ?>
                    <option value="">请选择..</option>
                    <?php }else{ ?>
                        <?php foreach($rooms as $r) { ?>
                    <option value="<?php echo $r['id']; ?>"<?php if($r['id'] == $data['objid']) echo " selected"; ?>><?php echo roomname($r['name'], 2); ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label">床型</label>
            <div class="col-sm-3">
                <select name="bed" class="form-control ui-select">
                    <option value="0">不限</option>
                    <option value="2">大/双床</option>
                    <option value="3">单/大/双床</option>
                    <option value="T">双床</option>
                    <option value="D">大床</option>
                    <option value="S">单人床</option>
                    <option value="K">超大床</option>
                    <option value="C">圆床</option>
                    <option value="O">特殊床型</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">提前预订</label>
            <div class="col-sm-5">
                <select name="advance" class="ui-select form-control">
                    <option value="-1">无限制</option>
                    <option value="0" selected>无提前要求</option>
                    <option value="1" selected>有提前要求</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">要求连住</label>
            <div class="col-sm-5">
                <select name="min" class="ui-select form-control">
                    <option value="-1">无限制</option>
                    <option value="0" selected>无连住要求</option>
                    <option value="1" selected>有连住要求</option>
                </select>
            </div>
        </div>

        <div id="nation" class="form-group">
            <label class="col-sm-2 control-label">国籍</label>
            <div class="col-sm-9">
                <select name="nation" class="ui-select form-control">
                    <option value="-1" selected>无限制</option>
                    <option value="1">大陆可用</option>
                </select>
            </div>
        </div>

        <div id="package" class="form-group">
            <label class="col-sm-2 control-label">增值包</label>
            <div class="col-sm-9">
                <select name="package" class="ui-select form-control">
                    <option value="-1" selected>无限制</option>
                    <option value="0">不含增值包</option>
                    <option value="1">包含增值包</option>
                </select>
            </div>
        </div>

        <div id="supply" class="form-group">
            <label class="col-sm-2 control-label">供应商</label>
            <div class="col-sm-9">
                <select name="supply[]" class="ui-select form-control" multiple>
                    <?php foreach(supplies() as $k => $v) { ?>
                    <option value="<?php echo $k; ?>" selected><?php echo $v; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

    </div>

    <input type="hidden" name="type" value="hotel" />
    <input type="hidden" id="item-pid" name="pid" value="<?php echo $pid; ?>" />
    <input type="hidden" id="item-id" name="id" value="<?php echo $data['id']; ?>" />
</form>

<script>
$(function(){

    var search = function(btn, type, page){
        var btn = $(btn);
        var input = btn.parent().prev("input");
        var ul = $("#"+type+" .dropdown-menu");
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

        var url = "";
        switch(type){
            case "hotel": url = "hotel.php"; break;
            case "nation": url = "nation.php"; break;
            case "package": url = "package.php"; break;
        }
        // search list
        $.get("<?php echo BASE_URL; ?>"+url, {method:"list", keyword:keyword, page:page}, function(data){
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
                        search(btn, type, data.rs.page.next);
                    });
                }

                input.focus().one("blur", function() {
                    ul.stop(true).delay(100).hide(1);
                });

                ul.find("li:not(.more) a").bind("click", function(){
                    var li = $(this);
                    ul.stop(true).hide();
                    input.val(li.text()).prop("disabled", true);
                    $("#"+type+"id").val(li.data("code"));
                    btn.children(".glyphicon").removeClass("glyphicon-search").addClass("glyphicon-remove");

                    // search rooms
                    if (type == "hotel") {
                        var select = $("#room select");
                        select.html("<option selected>正在读取..</option>").trigger("chosen:updated");
                        $.get("<?php echo BASE_URL; ?>room.php?method=load", {id:li.data("code")}, function(data){
                            if (data.s == 0) {
                                select.html("");
                                for(x in data.rs) {
                                    var opt = $("<option />");
                                    opt.attr("value", data.rs[x].id).text(data.rs[x].name);
                                    select.append(opt);
                                }
                                select.trigger("chosen:updated");
                            } else {
                                select.html("<option selected>读取失败，请重试..</option>").trigger("chosen:updated");
                            }
                        }, "json");
                    }
                });

            }else{
                alert("搜索数据失败，请重试~", "warning", null, "#item-form .modal-body");
                ul.hide();
            }
            btn.prop("disabled", false).children(".glyphicon").addClass("glyphicon-search").removeClass("glyphicon-refresh glyphicon-loading");
        }, "json");
    }

    $("#hotel .button").unbind("click").bind("click", function(){
        var btn = $(this);
        var type = btn.data("type");
        var icon = btn.children(".glyphicon");
        if (icon.is(".glyphicon-remove")) {
            btn.parent().prev("input").prop("disabled", false);
            icon.removeClass("glyphicon-remove").addClass("glyphicon-search");
        } else {
            search(this, type);
        }
    });

    $(".ui-select").chosen({disable_search_threshold:10, width:"100%", no_results_text:"未找到..", placeholder_text_single:"请选择.."});

});
</script>
