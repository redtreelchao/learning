<?php define('BASE_URL', '/'); ?>
<form role="form" class="form-horizontal">

    <div class="form-group">
        <label class="col-sm-2 control-label">价格</label>
        <div class="col-sm-5">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                <input type="text" name="price" id="price" class="form-control" value="<?php echo $item['price']; ?>" />
                <span class="input-group-btn">
                    <button type="button" id="btn-setdefault" data-default="<?php echo $item['default'];?>" title="使用此价格作为产品默认显示价格" class="btn <?php echo $item['default'] ? 'btn-success' : 'btn-default'; ?>"><?php echo $item['default'] ? '已显示' : '默认显示'; ?></button>
                </span>
            </div>
        </div>
    </div>

    <!--
    <div class="form-group">
        <label class="col-sm-2 control-label">目标货币</label>
        <div class="col-sm-5">
            <div class="input-group">
                 <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="currencyname">
                        <?php echo  (empty($item['currency'])?'选择货币':$item['currency'] )?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <?php
                        foreach ($item['currencys'] as $k => $v)
                        {
                        ?>
                      <li><a data-code='<?php echo $v['code']?>' data-rate="<?php echo $v['rate'];?>"><?php echo $v['code']?></a></li>
                      <?php
                         }
                      ?>

                    </ul>
                </div>
            </div>
        </div>
    </div>-->

    <input type="hidden" name="currency" class="form-control" value="<?php echo $item['currency']; ?>" />

    <div class="form-group" style="display:none">
        <label class="col-sm-2 control-label">儿童价格</label>
        <div class="col-sm-5">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                <input type="text" name="child" class="form-control" value="<?php echo $item['child']; ?>" />
            </div>
        </div>
    </div>

    <div class="form-group" style="display:none">
        <label class="col-sm-2 control-label">婴儿价格</label>
        <div class="col-sm-5">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-rmb"></span></span>
                <input type="text" name="baby" class="form-control" value="<?php echo $item['baby']; ?>" />
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">购买限制</label>

        <div class="col-sm-4">
            <div class="input-group">
                <span class="input-group-addon">最少</span>
                <input type="text" name="min" class="form-control" value="<?php echo $item['min'] ? $item['min'] : '1'; ?>">
                <span class="input-group-addon">张</span>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="input-group">
                <span class="input-group-addon">最多</span>
                <input type="text" name="max" class="form-control" value="<?php echo $item['max'] ? $item['max'] : '99'; ?>">
                <span class="input-group-addon">张</span>
            </div>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-2 control-label">库存数</label>
        <div class="col-sm-4">
            <input type="text" name="allot" class="form-control" value="<?php echo $item['allot']; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">售出数</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" disabled value="<?php echo $item['sold']; ?>" />
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />

   <!-- <input type="hidden" name="price" id="price" class="form-control" value="<?php echo $item['price']; ?>" /> -->
   <input type="hidden" name="currency" id="currency" class="form-control" value="<?php echo $item['currency']; ?>" />

</form>
<script type="text/javascript">
$(function(){
/*
    $(".dropdown-menu").on('click', 'li a', function(){
      // $("#currencyname").text($(this).text());
      // var rate = $(this).data('rate');
      // var price = $("#price_rate").val();
      // $("#price").val(price);
      // $("#currency").val($(this).text());
      // var price_rate = price/rate;
      // $("#price_rate").val(price_rate.toFixed(4));
   });
*/

    $("#btn-setdefault").click(function(){
        var btn = $(this),
            def = btn.data("default"),
            itemid = btn.parents('form').find('input[name=id]').val();
        if (def) {
            alert("已设置为默认显示，如取消请设置其他价格为默认。", "warning", null, "#item-price .modal-body"); return;
        }
        $.post("<?php echo BASE_URL; ?>product.php?method=default", {"item":itemid}, function(data){
            if (data.s != 0) {
                alert(data.err, "error", null, "#item-price .modal-body");
            }else{
                btn.text('已显示').toggleClass("btn-success btn-default").data("default", 1);
            }
        }, "json");
    });

});
</script>

