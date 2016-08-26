<div id="product-count-chart" class="chart">

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

chart.write("product-count-chart");

</script>