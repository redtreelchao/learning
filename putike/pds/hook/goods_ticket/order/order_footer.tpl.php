<?php if ($mode == 'operate') { ?>
<!--modal-->
<div id="ticket-used" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">确认发货</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="goods-used" />
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />

                    <div class="form-group">
                        <label class="col-sm-3 control-label">快递公司</label>
                        <div class="col-sm-6">
                            <?php $express = expressname(); ?>
                            <select class="form-control ui-select" name="type">
                            <?php foreach($express as $code => $name){ ?>
                                <option value="<?php echo $code; ?>" <?php if($code == $extend['expresstype']) echo "selected"; ?>><?php echo $name; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">快递单号</label>
                        <div class="col-sm-6">
                            <input type="text" name="number" class="form-control" value="<?php echo $extend['expressno']; ?>" />
                        </div>
                    </div>

                </form>

                <?php if ($product) { ?>
                <div class="rule-content">
                    <h3>使用要求</h3>
                    <?php echo nl2br($product['rule']); ?>
                </div>
                <?php } ?>
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
<div id="ticket-refund" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">产品退订</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="goods-refund" />
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />

                    <!-- ticket num -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">退款金额</label>
                        <div class="col-sm-6">
                            <input type="text" name="price" class="form-control" value="<?php echo $item['total'] - $item['refund']; ?>" />
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

                <?php if ($product) { ?>
                <div class="rule-content">
                    <h3>退款协议</h3>
                    <?php echo nl2br($product['refund']); ?>
                </div>
                <?php } ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit" data-loading-text="确认中..">确认</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->


<script>
$(function(){

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


});
</script>
<?php } ?>
