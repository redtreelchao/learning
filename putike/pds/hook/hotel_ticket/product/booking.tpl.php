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

                $label = $title = '';
                if (isset($date[$i]))
                {
                    $d = $date[$i];
                    $allot = $d['allot'] - $d['used'];

                    if (!$d['filled'] && $allot > 0)
                    {
                        $label = "<span class=\"label label-success hidden-xs\">{$d['allot']}</span>";
                        $class .= 'allot ';
                    }
                    else if (!$d['filled'])
                    {
                        $label = "<span class=\"label label-warning hidden-xs\">{$d['allot']}</span>";
                        $class .= 'request ';
                    }
                    else
                    {
                        $label = "<span class=\"label label-danger hidden-xs\">0</span>";
                        $class .= 'filled ';
                    }

                    $label .= "<input type=\"hidden\" name=\"allot[{$i}]\" value=\"". ($d['filled'] ? '-1' : $d['allot']) ."\" />";
                }
                else
                {
                    $d = array('sold' => 0);
                    $label .= "<span class=\"label\"></span>";
                    $label .= "<input type=\"hidden\" name=\"allot[{$i}]\" value=\"0\" />";
                }
            ?>
            <li class="<?php echo trim($class); ?>" data-date="<?php echo $i; ?>" data-sold="<?php echo $d['sold']; ?>"><b><?php echo date('j', $i); ?></b><?php echo $label; ?></li>
            <?php } ?>
        </ul>

        <div class="loading">
            <span class="glyphicon glyphicon-refresh glyphicon-loading"></span> 正在读取数据..
        </div>
    </div>

    <!-- setting -->
    <div id="booking-setting" class="col-sm-3 setting">

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

    $("#booking-setting .save").click(function(){
        var _t = $("#date-allot").val();
        if (_t != ""){
            var t = parseInt(_t, 10);
            if (isNaN(t)) {
                alert("库存数不正确", "error", null, "#item-booking .modal-body");
                return false;
            }
            $(".calendar .date .selected:not(.freeze)").each(function(){
                var li = $(this),
                    span = li.children("span"),
                    sold = li.data("sold");
                li.removeClass("allot request filled");
                span.removeClass("label-success label-warning label-danger");
                li.children("input").val(t <= 0 ? -1 : t);
                if (t <= 0) {
                    li.addClass("filled");
                    span.addClass("label-danger").text(0);
                } else if (t >= sold) {
                    li.addClass("allot");
                    span.addClass("label-success").text(t);
                } else {
                    li.addClass("request");
                    span.addClass("label-warning").text(t);
                }
            });
        }
    });

    $("#booking-setting .filled").click(function(){
        $(".calendar .date .selected:not(.freeze)").each(function(){
            var li = $(this);
            li.removeClass("allot request").addClass("filled");
            li.children("span").removeClass("label-success label-warning").addClass("label-danger").text(0);
            li.children("input").val(-1);
        });
    });

    $(".calendar .title .next, .calendar .title .prev").click(function(){
        var btn = $(this);
        if (btn.is(".next")){
            var month = "<?php echo date('Y-m', $first + 86400 * 31); ?>";
        }else {
            var month = "<?php echo date('Y-m', $first - 86400); ?>";
        }
        $(".calendar .loading").show();
        $("#item-booking .modal-body").load('./product.php?method=extend&ext=t1booking&id=<?php echo $item['id']; ?>&month='+month);
    });
});
</script>