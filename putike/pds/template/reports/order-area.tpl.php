<p class="form-inline">
    <label class="visible-xs-block">开始日期：</label>
    <label class="hidden-xs">日期：</label>
    <input name="start" class="form-control input-sm ui-datepicker" value="<?php echo date('Y-m-d', $start); ?>" title="按回车检索数据" />
    <label class="visible-xs-block">结束日期：</label>
    <label class="hidden-xs"> - </label>
    <input name="end" class="form-control input-sm ui-datepicker" value="<?php echo date('Y-m-d', $end); ?>" title="按回车检索数据" />
</p>

<div id="order-area-chart" class="chart chart-map">
</div>

<?php if ($map != 'worldLow'){ ?>
<button style="position:absolute; bottom:20px; right:20px; z-index:0;" class="world btn btn-default">全球地图</button>
<?php } ?>

<div class="reloading">
    <span class="glyphicon glyphicon-refresh glyphicon-loading"></span>
</div>

<script>
(function(){

    <?php if ($map == 'worldLow'){ ?>
    var minBulletSize = 10;
    var maxBulletSize = 30;
    <?php }else{ ?>
    var minBulletSize = 5;
    var maxBulletSize = 20;
    <?php } ?>
    var min = Infinity;
    var max = -Infinity;

    // create AmMap object
    var map = new AmCharts.AmMap();
    map.pathToImages = "/template/js/ammap/images/";

    var dataProvider = {
        mapVar: AmCharts.maps.<?php echo $map; ?>,
        getAreasFromMap: true,
        zoomLevel: 2,
        zoomLongitude: 105,
        zoomLatitude: 35,
        images: []
    }

    var data = <?php echo json_encode($data); ?>;

    for(x in data){
        var value = data[x].num;
        if (value < min) {
            min = value;
        }
        if (value > max) {
            max = value;
        }
    }

    // it's better to use circle square to show difference between values, not a radius
    var maxSquare = maxBulletSize * maxBulletSize * 2 * Math.PI;
    var minSquare = minBulletSize * minBulletSize * 2 * Math.PI;

    for(x in data){
        // calculate size of a bubble
        var value = data[x].num;
        var square = (value - min) / (max - min) * (maxSquare - minSquare) + minSquare;
        if (square < minSquare) {
            square = minSquare;
        }

        var size = Math.sqrt(square / (Math.PI * 2));

        dataProvider.images.push({
            type: "circle",
            width: size,
            height: size,
            alpha: 0.5,
            color: "#337ab7",
            longitude: data[x].lng,
            latitude: data[x].lat,
            title: data[x].name + "(" + data[x].num + ")",
            value: data[x].num
        });
    }

    map.dataProvider = dataProvider;

    map.language = 'zh';

    map.mouseWheelZoomEnabled = true;

    map.areasSettings = {
        autoZoom: false,
        color: "#eeeeee",
        maxZoomLevel: 15,
        rollOverOutlineColor: "#333333",
        <?php if ($map == 'worldLow'){ ?>
        selectable: true,
        selectedColor: "#CCCCCC"
        <?php } else { ?>
        balloonText: ""
        <?php } ?>
    };

    map.zoomControl = {
        panControlEnabled: false,
        buttonSize: 12,
        buttonFillColor: "#777777",
        buttonColorHover: "#555555",
        buttonRollOverColor: "#337ab7",
        buttonBorderAlpha: 0,
        buttonCornerRadius: 3,
        gridAlpha: 0,
        gridHeight: 100,
        maxZoomLevel : 17
    };

    map.write("order-area-chart");

    <?php if ($map == 'worldLow'){ ?>
    var sel;
    map.addListener("clickMapObject", function(event){
        if (event.mapObject.id == 'CN' && sel == 'CN') {
            map.clearMap();
            $("#order-area .reloading").show();
            window.loaddata('order-area', {country:1, start:"<?php echo date('Y-m-d', $start); ?>", end:"<?php echo date('Y-m-d', $end); ?>"});
        }
        sel = event.mapObject.id;
    });
    <?php } ?>

})();

$(function(){
    $(".ui-datepicker")
        .zdatepicker({viewmonths:1, disable:[['1900-01-01','2015-03-31']]})
        .keydown(function(e){
            if (e.which == 13) {
                $("#order-area .reloading").show();
                var start = $("#order-area .ui-datepicker:eq(0)").val(),
                    end = $("#order-area .ui-datepicker:eq(1)").val(),
                    data = {start:start, end:end};
                <?php if ($map != 'worldLow'){ ?>
                data.country = 1;
                <?php } ?>
                window.loaddata('order-area', data);
            }
        });

    $("#order-area .world").click(function(){
        $("#order-area .reloading").show();
        window.loaddata('order-area', {start:"<?php echo date('Y-m-d', $start); ?>", end:"<?php echo date('Y-m-d', $end); ?>"});
    });
})
</script>