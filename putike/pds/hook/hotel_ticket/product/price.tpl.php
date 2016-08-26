<?php define('BASE_URL', '/'); ?>
<form role="form" class="form-horizontal">

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
                <input type="text" name="price" class="form-control" <?php if ($product[0]['status'] > 0) echo 'readonly'; ?> value="<?php echo $item['price']; ?>" />
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">售价</label>
        <div class="col-sm-6 input-total">
            <div class="input-group">
                <span class="input-group-addon">RMB &nbsp;&nbsp; </span>
                <input type="text" name="total" class="form-control" <?php if ($product[0]['status'] > 0) echo 'readonly'; ?> value="<?php echo is_null($profit) ? '' : ($item['price'] + $profit); ?>" placeholder="仅用于默认渠道" />
                <span class="input-group-btn">
                    <button type="button" data-default="<?php echo $item['default'];?>" title="使用此价格作为产品默认显示价格" class="btn btn-setdefault <?php echo $item['default'] ? 'btn-success' : 'btn-default'; ?>"><?php echo $item['default'] ? '已显示' : '默认显示'; ?></button>
                </span>
            </div>
            <p class="help-block"><?php if (!is_null($profit) && $item['price']) echo '毛利率='.number_format($profit / ($item['price'] + $profit) * 100, 2, '.', '').'%'; ?></p>
        </div>
    </div>

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
        <label class="col-sm-2 control-label">库存</label>
        <div class="col-sm-6">
            <div class="input-group input-allot">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background:#eee;">
                        无限制 <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:;" data-allot="0">固定库存</a></li>
                        <li><a href="javascript:;" data-allot="999">无限制</a></li>
                    </ul>
                </div>
                <input type="text" name="allot" class="form-control allot" maxlength="3" readonly value="<?php echo $item['allot']; ?>" />
            </div>
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

    $(".input-allot .dropdown-menu a").click(function(){
        var a = $(this), val = a.data("allot"), inp = $(".input-allot input");
        if (val == 0) {
            inp.val('').prop("readonly", false);
        } else {
            inp.val(val).prop("readonly", true);
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

    $(".btn-setdefault").click(function(){
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

