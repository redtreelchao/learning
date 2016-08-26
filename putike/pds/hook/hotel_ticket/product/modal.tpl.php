
    <div id="item-booking" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">设置可预订库存</h4>
                </div>
                <div class="modal-body">
                    <div style="color:#999; text-align:center;"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span> 正在加载日历..</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="savebooking()">保存</button>
                </div>
            </div>
        </div>
    </div>

<script>

function booking(btn){
    var id = $(btn).data("code");
    $("#item-booking").modal("show");
    $("#item-booking .modal-body")
        .html("<div style=\"color:#999; text-align:center;\"><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> 正在加载信息..</div>")
        .load("<?php echo BASE_URL; ?>product.php?method=extend&ext=t1booking&id="+id)
        .data("url", "<?php echo BASE_URL; ?>product.php?method=extend&ext=t1booking&id="+id);
}

function savebooking(){
    var form = $("#item-booking form"),
        body = $("#item-booking .modal-body");
    $.post(body.data("url"), form.serialize(), function(data){
        if(data.s == 0){
            $("#item-booking").modal("hide");
        }else{
            alert(data.err, "error", null, "#item-booking .modal-body");
        }
    }, "json");
}
</script>