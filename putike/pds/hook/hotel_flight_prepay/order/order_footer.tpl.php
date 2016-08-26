
<!--modal-->
<div id="pro4-hotel-booking" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">确认预订</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="pro4-hotel-booking" />
                    <input type="hidden" name="id" value="<?php echo $hotel['id']; ?>" />

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

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit">确认预订成功</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->



<!--modal-->
<div id="pro4-flight-booking" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">确认预订</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="pro4-flight-booking" />
                    <input type="hidden" name="id" value="<?php echo $flight['id']; ?>" />

                    <?php foreach($flight['passengers'] as $k => $v){ ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">票号<?php echo $k + 1; ?></label>
                        <div class="col-sm-8">
                            <input type="text" name="serial[<?php echo $v['id']; ?>]" placeholder="<?php echo $v['people']; ?>" class="form-control" value="" />
                        </div>
                    </div>
                    <?php } ?>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit">确认预订成功</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->


<!--modal-->
<div id="pro4-refund" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">申请退款</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="pro4-refund" />
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>" />

                    <div class="form-group">
                        <label class="col-sm-3 control-label">小计金额</label>
                        <div class="col-sm-6">
                            <input type="text" readonly class="form-control" value="<?php echo $product['total']; ?>" />
                        </div>
                    </div>

                    <!-- price -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">申请退款金额</label>
                        <div class="col-sm-6">
                            <input type="text" name="price" class="form-control" value="" />
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
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit">确认申请退款</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->


<!--modal-->
<div id="pro4-refund-submit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">申请退款</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal" role="form">

                    <input type="hidden" name="order" value="<?php echo $order['order']; ?>" />
                    <input type="hidden" name="method" value="pro4-refund-submit" />
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>" />

                    <div class="form-group">
                        <label class="col-sm-3 control-label">小计金额</label>
                        <div class="col-sm-6">
                            <input type="text" readonly class="form-control" value="<?php echo $product['total']; ?>" />
                        </div>
                    </div>

                    <!-- price -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">申请退款金额</label>
                        <div class="col-sm-6">
                            <input type="text" name="price" class="form-control" value="" />
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
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary submit">确认申请退款</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->

<script>
$(function(){

    $("#pro4-hotel-booking .modal-footer .submit").click(function(){
        var form = $("#pro4-hotel-booking form");
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            if (data.s == 0){
                alert("保存操作完成", "success", function(){ $("#pro4-hotel-booking").modal('hide'); location.reload(); }, "#pro4-hotel-booking .modal-body");
            } else {
                alert(data.err, "error", null, "#pro4-hotel-booking .modal-body");
            }
        }, "json");
    });

    $("#pro4-flight-booking .modal-footer .submit").click(function(){
        var form = $("#pro4-flight-booking form");
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            if (data.s == 0){
                alert("保存操作完成", "success", function(){ $("#pro4-flight-booking").modal('hide'); location.reload(); }, "#pro4-flight-booking .modal-body");
            } else {
                alert(data.err, "error", null, "#pro4-flight-booking .modal-body");
            }
        }, "json");
    });

    $("#pro4-refund .modal-footer .submit").click(function(){
        var form = $("#pro4-refund form");
        $.post("<?php echo BASE_URL; ?>order.php?method=save", form.serialize(), function(data){
            if (data.s == 0){
                alert("保存操作完成", "success", function(){ $("#pro4-refund").modal('hide'); location.reload(); }, "#pro4-refund .modal-body");
            } else {
                alert(data.err, "error", null, "#pro4-refund .modal-body");
            }
        }, "json");
    });
});
</script>