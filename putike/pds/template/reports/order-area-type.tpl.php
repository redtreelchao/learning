<p class="form-inline">
    <label class="visible-xs-block">开始日期：</label>
    <label class="hidden-xs">日期：</label>
    <input name="start" class="form-control input-sm ui-datepicker" value="<?php echo date('Y-m-d', $start); ?>" title="按回车检索数据" />
    <label class="visible-xs-block">结束日期：</label>
    <label class="hidden-xs"> - </label>
    <input name="end" class="form-control input-sm ui-datepicker" value="<?php echo date('Y-m-d', $end); ?>" title="按回车检索数据" />
</p>

<div id="order-area-type-chart" class="chart">

</div>

<div class="reloading">
    <span class="glyphicon glyphicon-refresh glyphicon-loading"></span>
</div>

<script>

var chart = new AmCharts.AmPieChart();
chart.dataProvider = <?php echo json_encode($data); ?>;
chart.titleField = "name";
chart.valueField = "count";
chart.colorField = "color";
chart.balloonText = "[[title]]:[[value]]";
chart.allLabels = [{ color:"#FFFFFF", bold:true }];
chart.labelRadius = -40;
chart.labelText = "[[percents]]%";
chart.outlineColor = "#FFFFFF";
chart.outlineAlpha = 0.8;
chart.outlineThickness = 1;
chart.pullOutRadius = "10%";
chart.groupedTitle = "";

// LEGEND
legend = new AmCharts.AmLegend();
legend.align = "left";
legend.markerType = "circle";
legend.maxColumns = 1;
chart.addLegend(legend);

chart.write("order-area-type-chart");

$(function(){
    $(".ui-datepicker")
        .zdatepicker({viewmonths:1, disable:[['1900-01-01','2015-03-31']]})
        .keydown(function(e){
            if (e.which == 13) {
                $("#order-area-type .reloading").show();
                var start = $("#order-area-type .ui-datepicker:eq(0)").val();
                var end = $("#order-area-type .ui-datepicker:eq(1)").val();
                window.loaddata('order-area-type', {country:1, start:start, end:end});
            }
        });
});
</script>