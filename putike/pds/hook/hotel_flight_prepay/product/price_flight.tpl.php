<?php define('BASE_URL', '/'); ?>
<form role="form" class="row">

    <!-- calendar -->
    <div class="col-sm-9 calendar">
        <div class="title">
            <a class="prev" href="javascript:;"><span class="fa fa-angle-double-left"></span></a>
            <a class="next" href="javascript:;"><span class="fa fa-angle-double-right"></span></a>
            <div><?php echo date('Y-m', $first); ?></div>
        </div>

        <div class="week">
            <span class="sun"><b class="fa fa-check-square-o"></b>日</span><span class="mon"><b class="fa fa-check-square-o"></b>一</span><span class="tue"><b class="fa fa-check-square-o"></b>二</span><span class="wed"><b class="fa fa-check-square-o"></b>三</span><span class="thu"><b class="fa fa-check-square-o"></b>四</span><span class="fri"><b class="fa fa-check-square-o"></b>五</span><span class="sat"><b class="fa fa-check-square-o"></b>六</span>
        </div>

        <ul class="date">
            <?php
            for($i = $start; $i <= $end; $i = $i + 86400)
            {
                $class = 'w'.date('N', $i).' ';
                if (date('Y-m', $i) != date('Y-m', $first)) $class .= 'opacity ';
                if ($i < NOW) $class .= 'disabled ';

                $price = $title = '';
                if (isset($date[$i]))
                {
                    $d = $date[$i];

                    if (!$d['filled'] && ($d['allot']-$d['sold'] > 0))
                    {
                        $price = "<span class=\"label label-success hidden-xs\">{$d['price']}</span>";
                        $class .= 'allot ';
                    }
                    else if (!$d['filled'])
                    {
                        $price = "<span class=\"label label-warning hidden-xs\">{$d['price']}</span>";
                        $class .= 'request ';
                    }
                    else
                    {
                        $price = "<span class=\"label label-danger hidden-xs\">{$d['price']}</span>";
                        $class .= 'filled ';
                    }

                    $price .= "<input type=\"hidden\" name=\"price[{$i}]\" value=\"{$d['price']}\" />";
                    $price .= "<input type=\"hidden\" name=\"adultfuel[{$i}]\" value=\"{$d['adultfuel']}\" />";
                    $price .= "<input type=\"hidden\" name=\"adulttax[{$i}]\" value=\"{$d['adulttax']}\" />";
                    $price .= "<input type=\"hidden\" name=\"child[{$i}]\" value=\"{$d['child']}\" />";
                    $price .= "<input type=\"hidden\" name=\"childfuel[{$i}]\" value=\"{$d['childfuel']}\" />";
                    $price .= "<input type=\"hidden\" name=\"childtax[{$i}]\" value=\"{$d['childtax']}\" />";
                    $price .= "<input type=\"hidden\" name=\"baby[{$i}]\" value=\"{$d['baby']}\" />";
                    $price .= "<input type=\"hidden\" name=\"babyfuel[{$i}]\" value=\"{$d['babyfuel']}\" />";
                    $price .= "<input type=\"hidden\" name=\"babytax[{$i}]\" value=\"{$d['babytax']}\" />";
                    $price .= "<input type=\"hidden\" name=\"allot[{$i}]\" value=\"". ($d['filled'] ? '-1' : $d['allot']) ."\" />";
                }
                else
                {
                    $d = array('sold' => 0);
                    $price .= "<span class=\"label\"></span>";
                    $price .= "<input type=\"hidden\" name=\"price[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"adultfuel[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"adulttax[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"child[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"childfuel[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"childtax[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"baby[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"babyfuel[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"babytax[{$i}]\" value=\"0\" />";
                    $price .= "<input type=\"hidden\" name=\"allot[{$i}]\" value=\"0\" />";
                }

            ?><li class="<?php echo trim($class); ?>" data-date="<?php echo $i; ?>" data-sold="<?php echo $d['sold']; ?>"><b><?php echo date('j', $i); ?></b><?php echo $price; ?></li><?php } ?>
        </ul>

        <div class="loading">
            <span class="glyphicon glyphicon-refresh glyphicon-loading"></span> 正在读取数据..
        </div>
    </div>

    <style>
        #calendar-setting .form-group { position:relative; }
        #calendar-setting .form-group .form-control { z-index:0; }
        #calendar-setting .form-group .form-control:focus { z-index:1; }
        #calendar-setting .input-group-addon .fa { width:16px; }
        #calendar-setting .price-fuel,
        #calendar-setting .price-tax    { margin-top:-1px; }
        #calendar-setting .price-fuel .input-group-addon,
        #calendar-setting .price-fuel .form-control { border-radius:0px; }
        #calendar-setting .price-tax .form-control { border-top-right-radius:0px; }
        #calendar-setting .price-tax .input-group-addon { border-top-left-radius:0px; }
        #calendar-setting .price-plane .form-control { border-bottom-right-radius:0px; }
        #calendar-setting .price-plane .input-group-addon { border-bottom-left-radius:0px; }
        #calendar-setting .form-group .visible-xs-inline-block { width:70px; }
    </style>

    <!-- setting -->
    <div id="calendar-setting" class="col-sm-3 setting">

        <div class="form-group">
            <label>成人</label>
            <div class="input-group price-plane">
                <div class="input-group-addon" title="机票"><span class="fa fa-plane"></span><span class="visible-xs-inline-block">机票</span></div>
                <input type="text" class="form-control" id="date-price" placeholder="" value="" />
            </div>
            <div class="input-group price-fuel">
                <div class="input-group-addon" title="燃油费"><span class="fa fa-tint"></span><span class="visible-xs-inline-block">燃油</span></div>
                <input type="text" class="form-control" id="date-adultfuel" placeholder="" value="" />
            </div>
            <div class="input-group price-tax">
                <div class="input-group-addon" title="税/机建费"><span class="fa fa-ticket"></span><span class="visible-xs-inline-block">税/机建</span></div>
                <input type="text" class="form-control" id="date-adulttax" placeholder="" value="" />
            </div>
        </div>

        <div class="form-group">
            <label>儿童</label>
            <div class="input-group price-plane">
                <div class="input-group-addon" title="机票"><span class="fa fa-plane"></span><span class="visible-xs-inline-block">机票</span></div>
                <input type="text" class="form-control" id="date-child" placeholder="" value="" />
            </div>
            <div class="input-group price-fuel">
                <div class="input-group-addon" title="燃油费"><span class="fa fa-tint"></span><span class="visible-xs-inline-block">燃油</span></div>
                <input type="text" class="form-control" id="date-childfuel" placeholder="" value="" />
            </div>
            <div class="input-group price-tax">
                <div class="input-group-addon" title="税/机建费"><span class="fa fa-ticket"></span><span class="visible-xs-inline-block">税/机建</span></div>
                <input type="text" class="form-control" id="date-childtax" placeholder="" value="" />
            </div>
        </div>

        <div class="form-group">
            <label>婴儿</label>
            <div class="input-group price-plane">
                <div class="input-group-addon" title="机票"><span class="fa fa-plane"></span><span class="visible-xs-inline-block">机票</span></div>
                <input type="text" class="form-control" id="date-baby" placeholder="" value="" />
            </div>
            <div class="input-group price-fuel">
                <div class="input-group-addon" title="燃油费"><span class="fa fa-tint"></span><span class="visible-xs-inline-block">燃油</span></div>
                <input type="text" class="form-control" id="date-babyfuel" placeholder="" value="" />
            </div>
            <div class="input-group price-tax">
                <div class="input-group-addon" title="税/机建费"><span class="fa fa-ticket"></span><span class="visible-xs-inline-block">税/机建</span></div>
                <input type="text" class="form-control" id="date-babytax" placeholder="" value="" />
            </div>
        </div>

        <div class="form-group">
            <label>库存</label>
            <input type="text" class="form-control" id="date-allot" placeholder="" value="" />
        </div>

        <div class="form-group text-right">
            <button type="button" class="btn btn-default save">设置</button>
            <button type="button" class="btn btn-default filled">快速满房</button>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" name="start" value="<?php echo $start; ?>" />
    <input type="hidden" name="end" value="<?php echo $end; ?>" />

</form>

<script>
$(function(){
    $(".calendar .date li").click(function(e){
        var li = $(this);
        if (li.is(".disabled")) return;
        if (li.is(".selected")) {
            li.removeClass("selected");
            var type = "remove";
        } else {
            li.addClass("selected");
            var type = "add";
        }
        if (e.shiftKey) {
            var list = $(".calendar .date li");
            var start = $(".calendar .date .start");
            if (start.length) {
                var s = list.index(start.eq(0));
                var e = list.index(li);
                var li = (s <= e) ? list.slice(s, e) : list.slice(e, s);
                if (type == "add") li.addClass("selected");
                else li.removeClass("selected");
                start.removeClass("start");
                li.focus().addClass("start");
            }
        } else {
            $(".calendar .date .start").removeClass("start");
            li.addClass("start");
        }
    });

    $(".calendar .week span").click(function(){
        var w = $(this);
        var i = w.prevAll("span").length;
        if (i == 0) i = 7;
        if (w.is(".freeze")) {
            $(".calendar .date .w"+i).removeClass("freeze");
            w.removeClass("freeze");
            w.children("b").removeClass("fa-square-o").addClass("fa-check-square-o");
        } else {
            $(".calendar .date .w"+i).addClass("freeze");
            w.addClass("freeze");
            w.children("b").removeClass("fa-check-square-o").addClass("fa-square-o");
        }
    });

    $("#calendar-setting .save").click(function(){
        var data = {"price":"", "adultfuel":"", "adulttax":"", "child":"", "childfuel":"", "childtax":"", "baby":"", "babyfuel":"", "babytax":"", "allot":""};
        var name = {"price":"成人票价", "adultfuel":"成人燃油费", "adulttax":"成人附加费", "child":"儿童票价", "childfuel":"儿童燃油费", "childtax":"儿童附加费",
                    "baby":"婴儿票价", "babyfuel":"婴儿燃油费", "babytax":"婴儿附加费", "allot":"库存"};
        var i = 0;
        for(x in data){
            var _t = $("#date-"+x).val();
            if (x == "allot" && _t == "") {
                _t = "0";
            }
            if (_t != ""){
                var t = parseInt(_t, 10);
                if (isNaN(t)) {
                    alert(name[x]+"不正确", "error", null, "#item-price .modal-body");
                    return false;
                }
                $(".calendar .date .selected:not(.freeze)").each(function(){
                    var li = $(this);
                    var span = li.children("span");
                    var p = li.children("input:eq(0)").val();
                    if (x == "price") p = t;
                    if (p <= 0) {
                        if (x == "price") {
                            span.removeClass("label-success label-warning label-danger").hide();
                            li.removeClass("allot request filled");
                            li.children("input").val("0");
                        }
                        return true;
                    }

                    li.children("input:eq("+i+")").val(t);
                    if (x == "price") {
                        span.html(p).show();
                    }
                    else if (x == "allot") {
                        li.removeClass("allot request filled");
                        span.removeClass("label-success label-warning label-danger");
                        if (t < 0) {
                            li.addClass("filled");
                            span.addClass("label-danger");
                        } else if (t > 0) {
                            li.addClass("allot");
                            span.addClass("label-success");
                        } else {
                            li.addClass("request");
                            span.addClass("label-warning");
                        }
                    }
                });
            }
            i ++;
        }
    });

    $("#calendar-setting .filled").click(function(){
        $(".calendar .date .selected:not(.freeze)").each(function(){
            var li = $(this);
            var p = li.children("input:eq(0)").val();
            if (p > 0) {
                li.removeClass("allot request").addClass("filled");
                li.children("span").removeClass("label-success label-warning").addClass("label-danger");
                li.children("input:eq(9)").val(-1);
            }
        });
    });

    $(".calendar .date li b").popover({trigger:"hover", placement:"top", container:"#item-price", html:true, content:function(){
        var li = $(this).parent();
        var inp = li.children("input");
        var d = {
            price       : inp.eq(0).val(),
            adultFuel    : inp.eq(1).val(),
            adultTax     : inp.eq(2).val(),
            child       : inp.eq(3).val(),
            childFuel   : inp.eq(4).val(),
            childTax    : inp.eq(5).val(),
            baby        : inp.eq(6).val(),
            babyFuel    : inp.eq(7).val(),
            babyTax     : inp.eq(8).val(),
            allot       : inp.eq(9).val()
        };
        if (d.price == "0") return "";

        var content = "";
        if (d.price) {
            content += "票价："+d.price+" (+"+d.adultFuel+", +"+d.adultTax+")<br />";
            if (d.child > 0) content += "儿童："+d.child+" (+"+d.childFuel+", +"+d.childTax+")<br />";
            if (d.baby > 0) content += "婴儿："+d.baby+" (+"+d.babyFuel+", +"+d.babyTax+")<br />";
            var sold  = parseInt(li.data("sold"), 10);
            var allot = parseInt(d.allot, 10);
            content += "库存："+(allot-sold)+" (卖"+sold+")<br />";
            if (allot - sold > 0)
                content += "状态：【即时】";
            else if (allot - sold == 0)
                content += "状态：【问询】";
            else
                content += "状态：【满房】";
        }
        return "<div style=\"font-size:12px; margin:0px -4px;\">"+content+"</div>";
    }});

    $(".calendar .title .next, .calendar .title .prev").click(function(){
        var btn = $(this);
        if (btn.is(".next")){
            var month = "<?php echo date('Y-m', $first + 86400 * 31); ?>";
        }else {
            var month = "<?php echo date('Y-m', $first - 86400); ?>";
        }
        $(".calendar .loading").show();
        $("#item-price .modal-body").load('./product.php?method=price&id=<?php echo $item['id']; ?>&month='+month);
    });
});
</script>