<?php define('BASE_URL', '/'); ?>
<form role="form" class="form-horizontal">

    <div id="page1" class="page">

        <div class="form-group">
            <label class="col-sm-2 control-label">券名</label>
            <div class="col-sm-9">
                <input type="text" name="name" class="form-control" value="<?php echo $data['name']; ?>" />
            </div>
        </div>

        <div id="supply" class="form-group">
            <label class="col-sm-2 control-label">供应商</label>
            <div class="col-sm-9">
                <input type="hidden" id="supplyid" name="supply" value="<?php echo $data['supply']; ?>" />
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo $extend['supply_name']; ?>" <?php if($extend['supply_name']) echo 'disabled'; ?> />
                    <span class="input-group-btn">
                        <button class="button btn btn-default" type="button" data-type="supply"><span class="glyphicon <?php echo $extend['supplyname'] ? 'glyphicon-remove' : 'glyphicon-search'; ?>"></span></button>
                    </span>
                </div>
                <ul class="dropdown-menu" role="menu" style="left:15px; right:15px; max-height:200px; overflow:auto;"></ul>
            </div>
        </div>

        <div id="hotel" class="form-group">
            <label class="col-sm-2 control-label">关联酒店</label>
            <div class="col-sm-9">
                <input type="hidden" id="hotelid" name="hotel" value="<?php echo $data['objpid']; ?>" />
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo $data['hotel_name']; ?>" <?php if($data['hotel_name']) echo 'disabled'; ?> />
                    <span class="input-group-btn">
                        <button class="button btn btn-default" type="button" data-type="hotel"><span class="glyphicon <?php echo $data['hotel_name'] ? 'glyphicon-remove' : 'glyphicon-search'; ?>"></span></button>
                    </span>
                </div>
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
                    <option value="2" <?php if($data['ext2'] == '2') echo "selected"; ?>>大/双床</option>
                    <option value="3" <?php if($data['ext2'] == '3') echo "selected"; ?>>单/大/双床</option>
                    <option value="T" <?php if($data['ext2'] == 'T') echo "selected"; ?>>双床</option>
                    <option value="D" <?php if($data['ext2'] == 'D') echo "selected"; ?>>大床</option>
                    <option value="S" <?php if($data['ext2'] == 'S') echo "selected"; ?>>单人床</option>
                    <option value="K" <?php if($data['ext2'] == 'K') echo "selected"; ?>>超大床</option>
                    <option value="C" <?php if($data['ext2'] == 'C') echo "selected"; ?>>圆床</option>
                    <option value="O" <?php if($data['ext2'] == 'O') echo "selected"; ?>>特殊床型</option>
                </select>
            </div>
        </div>

        <div id="room" class="form-group">
            <label class="col-sm-2 control-label">价格包含</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" name="ext" class="form-control" value="<?php echo $data['ext']; ?>" />
                    <span class="input-group-addon">晚</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">产品说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="intro" rows="3"><?php echo $data['intro']; ?></textarea>
            </div>
        </div>

        <div class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">儿童说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="childstd" rows="1"><?php echo $data['childstd']; ?></textarea>
            </div>
        </div>

        <div class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">婴儿说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="babystd" rows="1"><?php echo $data['babystd']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">必填信息</label>
            <div class="col-sm-9">
                <select name="userdata" class="form-control ui-select">
                    <option value="2" <?php if($v['userdata'] == 'name') echo "selected"; ?>>国内酒店：姓名</option>
                    <option value="3" <?php if($v['userdata'] == 'name+id') echo "selected"; ?>>国内机酒：姓名+有效证件</option>
                    <option value="T" <?php if($v['userdata'] == 'name+num') echo "selected"; ?>>国内景酒：姓名+身份证号</option>
                    <option value="D" <?php if($v['userdata'] == 'en') echo "selected"; ?>>国外酒店：英文名</option>
                    <option value="S" <?php if($v['userdata'] == 'en+num') echo "selected"; ?>>国外机酒：英文名+有效证件</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Booking Code</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="bookingcode" rows="1"><?php echo $data['bookingcode']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">提前预订</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" name="advance" class="form-control" value="<?php echo $extend['advance']; ?>" />
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>

        <div class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">要求连住</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" name="min" class="form-control" value="<?php echo $extend['min']; ?>" />
                    <span class="input-group-addon">晚</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">售卖日期</label>
            <div class="col-xs-6 col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    <input type="text" name="online" id="online" class="form-control" placeholder="开始时间" value="<?php echo $data['online'] ? date('Y-m-d', $data['online']) : ''; ?>" />
                </div>
            </div>
            <div class="col-xs-6 col-sm-4">
                <input type="text" name="offline" id="offline" class="form-control" placeholder="结束时间" value="<?php echo $data['offline'] ? date('Y-m-d', $data['offline']) : ''; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">预订时间</label>
            <div class="col-xs-6 col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    <input type="text" name="start" id="bookstart" class="form-control" placeholder="开始时间" value="<?php echo $data['start'] ? date('Y-m-d', $data['start']) : ''; ?>" />
                </div>
            </div>
            <div class="col-xs-6 col-sm-4">
                <input type="text" name="end" id="bookend" class="form-control" placeholder="结束时间" value="<?php echo $data['end'] ? date('Y-m-d', $data['end']) : ''; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">底价</label>
            <div class="col-sm-6 input-price">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background:#eee;">
                            RMB <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:;">RMB</a></li>
                        </ul>
                    </div>
                    <input type="text" name="price" class="form-control" value="<?php echo $data['price']; ?>" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">售价</label>
            <div class="col-sm-6 input-total">
                <div class="input-group">
                    <span class="input-group-addon">RMB &nbsp;&nbsp; </span>
                    <input type="text" name="total" class="form-control" value="<?php echo is_null($profit) ? '' : ($data['price'] + $profit); ?>" placeholder="仅用于默认渠道" />
                </div>
                <p class="help-block"><?php if (!is_null($profit) && $data['price']) echo '毛利率='.number_format($profit / ($data['price'] + $profit) * 100, 2, '.', '').'%'; ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">最小倍率</label>
            <div class="col-sm-6 input-total">
                <div class="input-group">
                    <input type="text" name="rate" class="form-control" value="<?php echo $data['rate']; ?>" placeholder="购买最小倍率" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">库存</label>
            <div class="col-sm-6 input-total">
                <div class="input-group">

                    <input type="text" name="allot" class="form-control" value="<?php echo $data['allot']?$data['allot']:'99' ?>"  />
                </div>

            </div>
        </div>

        <div id="nation" class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">国籍</label>
            <div class="col-sm-9">
                <input type="hidden" id="nationid" name="nation" value="<?php echo $extend['nation']; ?>" />
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo $extend['nation_name']; ?>" <?php if($data['hotel_name']) echo 'disabled'; ?> />
                    <span class="input-group-btn">
                        <button class="button btn btn-default" type="button" data-type="nation"><span class="glyphicon <?php echo $data['hotel_name'] ? 'glyphicon-remove' : 'glyphicon-search'; ?>"></span></button>
                    </span>
                </div>
                <ul class="dropdown-menu" role="menu" style="left:15px; right:15px; max-height:200px; overflow:auto;"></ul>
            </div>
        </div>


        <div id="package" class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">增值包</label>
            <div class="col-sm-9">
                <input type="hidden" id="packageid" name="package" value="<?php echo $extend['package']; ?>" />
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo $extend['package_name'] ?>" <?php if($data['hotel_name']) echo 'disabled'; ?> />
                    <span class="input-group-btn">
                        <button class="button btn btn-default" type="button" data-type="package"><span class="glyphicon <?php echo $data['hotel_name'] ? 'glyphicon-remove' : 'glyphicon-search'; ?>"></span></button>
                    </span>
                </div>
                <ul class="dropdown-menu" role="menu" style="left:15px; right:15px; max-height:200px; overflow:auto;"></ul>
            </div>
        </div>

        <!-- 0无 1无线免费 2无线收费 3有线免费 4有线收费 -->
        <div class="form-group" style="display:none">
            <label class="col-sm-2 control-label">有线宽带</label>
            <div class="col-sm-9">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default active">
                        <input type="radio" name="net" value="3" autocomplete="off" checked> 免费
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="net" value="4" autocomplete="off"> 收费
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="net" value="0" autocomplete="off"> 无
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group" style="display:none">
            <label class="col-sm-2 control-label">无线宽带</label>
            <div class="col-sm-9">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default active">
                        <input type="radio" name="wifi" value="1" autocomplete="off"> 免费
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="wifi" value="2" autocomplete="off"> 收费
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="wifi" value="0" autocomplete="off" checked> 无
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">加早价格</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon">¥</span>
                    <input type="text" name="addbf" class="form-control" value="<?php echo $extend['addbf']; ?>" />
                </div>
            </div>
        </div>

        <div class="form-group" style="display:none;">
            <label class="col-sm-2 control-label">加床价格</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon">¥</span>
                    <input type="text" name="addbe" class="form-control" value="<?php echo $extend['addbe']; ?>" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">操作说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="remark" rows="3"><?php echo $data['remark']; ?></textarea>
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
            case "supply": url = "supply.php"; break;
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

    $("#hotel .button, #nation .button, #package .button, #supply .button").unbind("click").bind("click", function(){
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

    $(".input-price input, .input-total input").keyup(function(){
        var p = $(".input-price input").val(), t = $(".input-total input").val();
        p = parseInt(p, 10);
        t = parseInt(t, 10);
        if (p && t && p < t) {
            var v =  Math.round((t - p) / t * 10000) / 100;
            $(".input-total .help-block").text("毛利率=" + v + "%");
        } else {
            $(".input-total .help-block").text("");
        }
    });

    $(".ui-select").chosen({disable_search_threshold:10, width:"100%", no_results_text:"未找到..", placeholder_text_single:"请选择.."});

    $(".pager li a").click(function(){
        $(".pager").prevAll(".page").addClass("hidden");
        var target = $(this).attr("href");
        $(target).removeClass("hidden");
        $(".pager li").toggleClass("hidden");
    });

    $("#bookstart, #online").zdatepicker({viewmonths:1, disable:{0:['1900-1-1','<?php echo date('Y-m-d', strtotime('-1 day')); ?>']}});

    $("#bookend").zdatepicker({viewmonths:1, disable:{0:['1900-1-1',$("#bookstart")]}});

    $("#offline").zdatepicker({viewmonths:1, disable:{0:['1900-1-1',$("#online")]}});

});
</script>
