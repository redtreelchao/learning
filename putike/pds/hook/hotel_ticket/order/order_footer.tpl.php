<?php if ($mode == 'operate') { ?>
<!--modal-->
<div id="ticket-used" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">预约使用</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <?php //$enable=5; $night=2; // debug ?>
                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="ticket-used" />
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />

                    <!--num-->
                    <div class="form-group" id="form-num">
                        <label class="col-sm-3 control-label">使用券数</label>
                        <div class="col-sm-9">
                            <input type="hidden" name="num" value="1" />
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" style="min-width:100px;" data-toggle="dropdown"> 1张 <span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php for($i=1; $i<=$enable; $i++){ ?>
                                    <li><a href="javascript:change_num(<?php echo $i; ?>)"><?php echo $i; ?>张</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!--rooms-->
                    <div class="form-group" id="form-rooms">
                        <label class="col-sm-3 control-label">间夜数</label>
                        <div class="col-sm-9">
                            <input type="hidden" name="room" value="1" />
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" style="min-width:100px;" data-toggle="dropdown"> 1间/<?php echo $night; ?>晚 <span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="javascript:change_room(1,1)">1间/<?php echo $night; ?>晚</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!--date-->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">入住日期</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="checkin" id="checkin" class="form-control" value="" />
                            </div>
                        </div>
                    </div>

                    <!--price-->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">价格</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" readonly value="售价：¥<?php echo $floor + $profit; ?>　底价：¥<?php echo $floor; ?>" />
                        </div>
                    </div>

                    <!--date-->
                    <input type="hidden" id="supply" name="supply" value="HAND" />
                    <!--div class="form-group" id="supplies">
                        <label class="col-sm-2 control-label">供应商</label>
                        <div class="col-sm-9">
                            <input type="hidden" id="supply" name="supply" value="HAND" />
                            <input type="hidden" id="pricecode" name="pricecode" value="" />
                            <?php
                                $supplies = supplies();
                                foreach($supplies as $code => $name)
                                {
                            ?>
                            <button type="button" class="btn btn-default" disabled data-code="<?php echo $code; ?>" data-price=""><?php echo $name ?></button>
                            <?php
                                }
                            ?>
                            <button type="button" class="btn btn-default" disabled data-code="EBK">Ebook</button>
                            <button type="button" class="btn btn-primary hand">自签</button>
                        </div>
                    </div-->

                    <!--room-->
                    <div class="well room">

                        <h4>房间1</h4>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">住客数<i>*</i></label>
                            <div class="col-xs-6 col-sm-4">
                                <input type="text" name="adult[]" class="form-control" placeholder="成人" value="" />
                            </div>
                            <div class="col-xs-6 col-sm-4">
                                <input type="text" name="child[]" class="form-control" placeholder="儿童" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">入住人<i>*</i></label>
                            <div class="col-sm-8">
                                <input type="text" name="people[]" class="form-control" placeholder="入住人1" value="<?php echo $order['contact']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">儿童出生日期</label>
                            <div class="col-xs-8">
                                <input type="text" name="birth[]" class="form-control" placeholder="格式：2000-01-30" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">床型<i>*</i></label>
                            <div class="col-xs-6">
                                <select name="bed[]" class="form-control">
                                    <option value="T">双床</option>
                                    <option value="D">大床</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">联系邮箱</label>
                            <div class="col-xs-8">
                                <input type="text" name="email[]" class="form-control" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">联系电话</label>
                            <div class="col-xs-8">
                                <input type="text" name="tel[]" class="form-control" value="<?php echo $order['tel']; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 control-label">备　注</label>
                            <div class="col-xs-8">
                                <textarea name="require[]" class="form-control" rows="3" maxlength="100"></textarea>
                            </div>
                        </div>

                    </div>

                </form>

                <div class="rule-content">
                    <h3>使用要求</h3>
                    <?php echo nl2br($ticket['rule']); ?>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary save-used" data-loading-text="保存中..">保存</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->



<!--modal-->
<div id="ticket-booking" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">确认预订</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="ticket-book-submit" />
                    <input type="hidden" id="ticket-id" name="id" value="0" />

                    <!-- hotel orderid -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">酒店确认号</label>
                        <div class="col-sm-6">
                            <input type="text" name="confirmno" class="form-control" value="" />
                        </div>
                    </div>

                    <!-- supply order  -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">供应商订单号</label>
                        <div class="col-sm-6">
                            <input type="text" name="supplyorder" class="form-control" value="" />
                        </div>
                    </div>

                    <!-- submit cancel alert -->
                    <div class="alert alert-warning" role="alert" style="display:none;">
                        <h4>取消该订单</h4> 该操作将使预订取消，取消预订的券类产品将可以继续使用。<br />如确认取消，请再次点击取消预订按钮。
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-danger fail" data-status="1">失败并取消预约</button>
                <button type="button" class="btn btn-primary success">确认预订成功</button>
                <button type="button" class="btn btn-warning cancel" data-status="0" data-toggle="0">取消预订</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->



<!--modal-->
<div id="ticket-booked" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">确认取消预订</h4>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <h4>确认取消</h4> 订单已通过酒店确认，请核实并确认该订单可以被取消。
                </div>
                <label><input type="checkbox" id="cancel-id" class="checkbox" value="0" /> 如果确认请选中，然后点击下方的确认按钮</label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit" data-loading-text="保存中..">确认</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->



<!--modal-->
<div id="ticket-refund" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">券类退订</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="ticket-refund" />
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />

                    <!-- ticket num -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">退订数量</label>
                        <div class="col-sm-6">
                            <input type="text" name="num" class="form-control" value="1" />
                        </div>
                    </div>

                    <!-- reason -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">退款原因</label>
                        <div class="col-sm-7">
                            <textarea name="reason" rows="3" placeholder="不超过200字" class="form-control"></textarea>
                        </div>
                    </div>

                </form>

                <div class="rule-content">
                    <h3>退款协议</h3>
                    <?php echo nl2br($ticket['refund']); ?>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit" data-loading-text="确认中..">确认</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->




<script type="text/html" id="room-tmpl">

<div class="well room">

    <h4>房间{id}</h4>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">住客数<i>*</i></label>
        <div class="col-xs-6 col-sm-4">
            <input type="text" name="adult[]" class="form-control" placeholder="成人" value="" />
        </div>
        <div class="col-xs-6 col-sm-4">
            <input type="text" name="child[]" class="form-control" placeholder="儿童" value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">入住人<i>*</i></label>
        <div class="col-sm-8">
            <input type="text" name="people[]" class="form-control" placeholder="入住人1" value="<?php echo $order['contact']; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">儿童出生日期</label>
        <div class="col-xs-8">
            <input type="text" name="birth[]" class="form-control" placeholder="格式：2000-01-30" value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">床型<i>*</i></label>
        <div class="col-xs-6">
            <select name="bed[]" class="form-control">
                <option value="T">双床</option>
                <option value="D">大床</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">联系邮箱</label>
        <div class="col-xs-8">
            <input type="text" name="email[]" class="form-control" value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">联系电话</label>
        <div class="col-xs-8">
            <input type="text" name="tel[]" class="form-control" value="<?php echo $order['tel']; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 control-label">备　注</label>
        <div class="col-xs-8">
            <textarea name="require[]" class="form-control" rows="3" maxlength="100"></textarea>
        </div>
    </div>

</div>
</script>


<script>
var per_night = '<?php echo (int)$night; ?>';
var night = 1;
function change_num(i){
    $("#form-num button").html(' '+i+'张 <span class="caret"></span>');
    $("#form-num input").val(i);
    $("#form-rooms .dropdown-menu").html('');
    $("#form-rooms button").html(' 1间/'+(i*per_night)+'晚 <span class="caret"></span>');
    $("#form-rooms input").val(1);
    for (j=i; j>=1; j--){
        if (i%j > 0) continue;
        var n = i/j; night = n;
        $("#form-rooms .dropdown-menu").prepend('<li><a href="javascript:change_room('+j+','+n+')">'+j+'间/'+(n*per_night)+'晚</a></li>');
    }
}
function change_room(i,n){
    $("#form-rooms button").html(' '+i+'间/'+(n*per_night)+'晚 <span class="caret"></span>');
    $("#form-rooms input").val(i);
    night = n;
    var rooms = $("#ticket-used .room");
    var len = rooms.length;
    if (len >= i){
        rooms.slice(i).remove();
    }else{
        var template = $("#room-tmpl").html();
        for (j=len+1; j<=i; j++){
            var reg = new RegExp("\\{([0-9a-zA-Z_-]*?)\\}", 'gm');
            var newroom = $(template.replace(reg, function (node, key) { return j; }));
            $("#ticket-used .room:last").after(newroom);
        }
    }

    $("#ticket-used .modal-backdrop").height( $("#ticket-used .modal-content").height() + 65 );
}

function price_page(a, index, room){
    var _this = $(a);
    var popover = _this.parents(".popover").eq(0);
    popover.children(".popover-title").text(room);
    var sel = popover.children(".popover-content").children(":not(.popover-page)").hide().eq(index);
    sel.show();
    var code = sel.data("code");
    $("#pricecode").val(code);
    $("#supplies .btn-primary").data("price", index);
    _this.parent().children(".sel").removeClass("sel");
    _this.addClass("sel");
}

function ticket_booking(a, ticket){
    $("#ticket-id").val(ticket);
    $("#ticket-booking").modal("show");
}

function ticket_booked(a, ticket){
    $("#cancel-id").val(ticket);
    $("#ticket-booked").modal("show");
}

$(function(){

    // supplies' price
    var supply_price = function(date){

        var num = parseInt($("#form-rooms input").val(), 10);
        $("#supplies button").removeClass("btn-primary").addClass("btn-default").popover('hide');
        $("#supplies .hand").removeClass("btn-default").addClass("btn-primary");
        $("#supply").val("HAND");
        $("#pricecode").val("");
/*
        $("#supplies button").each(function(){
            var _this = $(this);
            var sup = _this.data("code");
            if (!sup) return true;
            _this.prepend("<span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span>");
            $.ajax({
                type    :   "POST",
                url     :   "<?php echo BASE_URL; ?>api.php?method=price",
                data    :   {supply:sup, checkin:date, night:(night*per_night), hotel:'<?php echo $hotel; ?>', room:'<?php echo $room; ?>'},
                success :   function(data){
                                _this.children("span").remove(); //console.log(data); return;
                                if (data.s == 0 && data.rs){
                                    var i = 0;
                                    var roomname = [];
                                    var codes = [];
                                    var contents = [];
                                    for(x in data.rs){
                                        var d = data.rs[x];
                                        var room = d.roomname;
                                        if (d["nation"] > 0) room += d["nationname"]+"<br />";
                                        if (d["advance"] > 0) room += "提前"+d["advance"]+"天预订<br />";
                                        if (d["min"] > 0) room += "至少连住"+d["min"]+"天";
                                        roomname.push(room);
                                        codes.push(d["code"]);

                                        div = "<div data-code=\""+d["code"]+"\" style=\""+(i > 0 ? "display:none" : "")+"\"><div class=\"min-calendar\">";

                                        for(y in d.prices){
                                            var p = d.prices[y];
                                            var w = p.day
                                            var _date = p.date;
                                            var cla = "";
                                            if (p.allot > num) cla = "allot";
                                            <?php if($floor){ ?> if (p.price > <?php echo $floor; ?>) cla = "warning"; <?php } ?>
                                            if (p.filled > 0) cla = "filled";
                                            var title = p.breakfast+"份早餐;";
                                            if (p.package > 0) title += p.packagename;
                                            var price = "<strong title=\""+title+"\" class=\""+cla+"\">"+p.price+"</strong>";

                                            div += "<span class=\"week"+w+"\"><b>"+_date+"</b>"+price+"</span>";
                                        }

                                        div += "</div></div>";
                                        contents.push(div);
                                        i++;
                                    }

                                    if (i > 0){
                                        _this.prop("disabled", false).data({price:0, contents:contents, titles:roomname, codes:codes})
                                        .unbind("click").bind("click", function(){
                                            if(!_this.is(".btn-primary")) {
                                                $("#supply").val(_this.data("code"));
                                                $("#pricecode").val(codes[0]);
                                                $("#supplies .btn-primary").removeClass("btn-primary").addClass("btn-default").popover('hide');
                                                _this.addClass("btn-primary").removeClass("btn-default").popover('show');
                                            } else {
                                                _this.popover('toggle');
                                            }
                                        });
                                    }else{
                                        _this.prop("disabled", true).unbind("click");
                                    }
                                } else {
                                    _this.prop("disabled", true).unbind("click");
                                }
                            },
                error   :   function(){
                                _this.children("span").remove();
                                _this.prop("disabled", true).unbind("click");
                            },
                dataType:   "json"
            });
        });*/
    }

    // hand price
    $("#supplies .hand").click(function(){
        $("#supply").val("HAND");
        $("#pricecode").val("");
        $("#supplies .btn-primary").removeClass("btn-primary").addClass("btn-default").popover('hide');
        $(this).addClass("btn-primary").removeClass("btn-default");
    });

    $("#supplies button").popover({
        title:function(){
            var _this = $(this);
            var index = _this.data("price");
            var titles = _this.data("titles");
            return titles[index];
        },
        content:function(){
            var _this = $(this);
            var index = _this.data("price");
            var contents = _this.data("contents");
            var titles = _this.data("titles");
            var content = contents[index];
            content += "<div class=\"popover-page\">";
            if (contents.length > 1) {
                for(i in contents){
                    content += "<a href=\"javascript:;\" onclick=\"price_page(this, "+i+", '"+titles[i]+"')\">"+(parseInt(i,10)+1)+"</a>";
                }
            }
            content += "</div>";
            return content;
        },
        html:true,
        placement:"top",
        trigger:"manual"
    });

    // save used
    $("#ticket-used .modal-footer .save-used").click(function(){
        var form = $("#ticket-used form"),
            btn = $(this).button('loading');
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            btn.button('reset');
            if (data.s == 0){
                alert("保存记录完成！", "success", function(){ $("#ticket-used").modal('hide'); location.reload(); }, "#ticket-used .modal-body");
            } else {
                alert(data.err, "error", null, "#ticket-used .modal-body");
            }
        }, "json");
    });

    // save refund
    $("#ticket-refund .modal-footer .submit").click(function(){
        var form = $("#ticket-refund form"),
            btn = $(this).button('loading');
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            btn.button('reset');
            if (data.s == 0){
                alert("已申请退款", "success", function(){ $("#ticket-refund").modal('hide'); location.reload(); }, "#ticket-refund .modal-body");
            } else {
                alert(data.err, "error", null, "#ticket-refund .modal-body");
            }
        }, "json");
    });

    // booked success
    $("#ticket-booking .modal-footer .success").click(function(){
        var form = $("#ticket-booking form");
        var id = $("#ticket-id").val();
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            if (data.s == 0){
                alert("保存操作完成！", "success", function(){ $("#ticket-booking").modal('hide'); location.reload(); }, "#ticket-booking .modal-body");
                $("#ticket-"+id).children(".st").text(<?php echo $status[8]; ?>);
            } else {
                alert(data.err, "error", null, "#ticket-booking .modal-body");
            }
        }, "json");
    });

    //  booked fail or cancel
    $("#ticket-booking .modal-footer .fail, #ticket-booking .modal-footer .cancel").click(function(){
        var _t = $(this);
        var id = $("#ticket-id").val();
        var status = _t.data("status");
        var toggle = _t.data("toggle");
        if (status == 0 && toggle == 0){
            $("#ticket-booking .alert-warning").slideDown();
            _t.text("确认取消此预订");
            _t.data("toggle", 1);
        }else{
            $.post("<?php echo BASE_URL; ?>order.php?method=save", {order:"<?php echo $order['order']; ?>", method:"ticket-book-cancel", id:id, status:status}, function(data){
                if (data.s == 0){
                    alert("取消操作完成！", "success", function(){ $("#ticket-booking").modal('hide'); location.reload(); }, "#ticket-booking .modal-body");
                } else {
                    alert(data.err, "error", null, "#ticket-booking .modal-body");
                }
            }, "json");
        }
    });

    // cancel booking
    $("#ticket-booked .modal-footer .submit").click(function(){
        var _t = $(this);
        var id = $("#cancel-id").val();
        var ck = $("#ticket-booked :checkbox");
        if (ck.prop("checked") == false){
            ck.parent().addClass("text-danger").attr("style", "position:relative; -webkit-animation:refuse 0.3s linear 3; -moz-animation:refuse 0.3s linear 3; -o-animation:refuse 0.3s linear 3; animation:refuse 0.3s linear 3;");
            return;
        }

        $.post("<?php echo BASE_URL; ?>order.php?method=save", {order:"<?php echo $order['order']; ?>", method:"ticket-book-cancel", id:id, status:0}, function(data){
            if (data.s == 0){
                alert("取消操作完成！", "success", function(){ $("#ticket-booked").modal('hide'); location.reload(); }, "#ticket-booked .modal-body");
            } else {
                alert(data.err, "error", null, "#ticket-booked .modal-body");
            }
        }, "json");

    });

    <?php
    $today = strtotime('today 00:00:00');
    $disabled = array();
    $disabled[0] = ($today + $advance * 86400 > $start) ? array('1900-1-1', date('Y-m-d', $today + ($advance - 1) * 86400)) : array('1900-1-1', date('Y-m-d', $start - 86400));
    if ($end)
    {
        $disabled[1] = array(date('Y-m-d', $end + 86400), '9999-1-1');
    }

    if ($end < $today)
    {
        $disabled = array();
    }
    ?>
    $("#checkin").zdatepicker({
        viewmonths: 1,
        disable: <?php echo json_encode($disabled); ?>,
        onFilter: function(date, month, year, week, cla, selected){
            if (!selected[0]) return cla;
            var _sel = selected[0].split("-");
            var temp = new Date();
            var select = new Date();
            temp.setFullYear(year, month-1, date);
            select.setFullYear(_sel[0], _sel[1]-1, _sel[2]);
            temp.setHours(0,0,0,0);
            select.setHours(0,0,0,0);
            if (temp.getTime() > select.getTime() && temp.getTime() <= select.getTime() + night * per_night * 86400000) {
                cla.push("affect");
            }
            return cla;
        },
        onReturn:function(date, dateObj, input, calendar, a, selected){
            $(input).val(date);
            calendar.find(".affect").removeClass("affect");
            $(a).parent().nextAll("span:lt("+night+")").each(function(){ $(this).children("a").addClass("affect"); });
            supply_price(date);
        }
    });

});
</script>
<?php } ?>
