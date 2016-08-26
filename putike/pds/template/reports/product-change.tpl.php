<p class="form-inline">
    <label class="visible-xs-block">开始日期：</label>
    <label class="hidden-xs">日期：</label>
    <input name="start" class="form-control input-sm ui-datepicker" value="<?php echo date('Y-m-d', $start); ?>" title="按回车检索数据" />
    <label class="visible-xs-block">结束日期：</label>
    <label class="hidden-xs"> - </label>
    <input name="end" class="form-control input-sm ui-datepicker" value="<?php echo date('Y-m-d', $end); ?>" title="按回车检索数据" />
</p>

<div class="row">
    <div class="col-xs-6 text-center">
        <div class="circle" style="background:#5cb85c;">
            <span><sub>新增</sub><?php echo (int)$online; ?></span>
        </div>
        <p><a class="btn btn-sm btn-default" href="<?php echo BASE_URL; ?>product.php?type=online&start=<?php echo date('Y-m-d', $start); ?>&end=<?php echo date('Y-m-d', $end); ?>" role="button">访问明细 &raquo;</a></p>
    </div>
    <div class="col-xs-6 text-center">
        <div class="circle" style="background:#d9534f;">
            <span><sub>减少</sub><?php echo (int)$offline; ?></span>
        </div>
        <p><a class="btn btn-sm btn-default" href="<?php echo BASE_URL; ?>product.php?type=offline&start=<?php echo date('Y-m-d', $start); ?>&end=<?php echo date('Y-m-d', $end); ?>" role="button">访问明细 &raquo;</a></p>
    </div>
</div>

<div class="reloading">
    <span class="glyphicon glyphicon-refresh glyphicon-loading"></span>
</div>

<script>
$(function(){
    $(".ui-datepicker")
        .zdatepicker({viewmonths:1, disable:[['1900-01-01','2015-03-31']]})
        .keydown(function(e){
            if (e.which == 13) {
                $("#product-change .reloading").show();
                var start = $("#product-change .ui-datepicker:eq(0)").val();
                var end = $("#product-change .ui-datepicker:eq(1)").val();
                window.loaddata('product-change', {country:1, start:start, end:end});
            }
        });
});
</script>
