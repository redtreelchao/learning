
    <div id="item-type" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">选择类型</h4>
                </div>
                <div class="modal-body bigico-btn type-select" style="text-align:center;">
                    <a class="btn btn-default type-hotel" data-code="room" href="javascript:;">
                        <span class="fa fa-building"></span><br />酒店
                    </a>
                    <a class="btn btn-default type-flight" data-code="flight" href="javascript:;">
                        <span class="fa fa-plane"></span><br />机票
                    </a>
                    <a class="btn btn-default type-auto" data-code="auto" href="javascript:;">
                        <span class="fa fa-car"></span><br />车辆
                    </a>
                    <a class="btn btn-default type-view" data-code="view" href="javascript:;">
                        <span class="fa fa-tree"></span><br />景点
                    </a>
                    <a class="btn btn-default type-goods" data-code="goods" href="javascript:;">
                        <span class="fa fa-gift"></span><br />商品
                    </a>
                </div>
            </div>
        </div>
    </div>


    <div id="item-form" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">添加组合内容</h4>
                </div>
                <div class="modal-body">
                    <div style="color:#999; text-align:center;"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span> 正在加载信息..</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="saveitem()">保存</button>
                </div>
            </div>
        </div>
    </div>


    <div id="item-price" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">设置库存及价格</h4>
                </div>
                <div class="modal-body">
                    <div style="color:#999; text-align:center;"><span class="glyphicon glyphicon-refresh glyphicon-loading"></span> 正在加载日历..</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="saveprice()">保存</button>
                </div>
            </div>
        </div>
    </div>


    <div id="item-delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">删除明细确认</h4>
                </div>
                <div class="modal-body">
                    确认删除该产品内容？<br />
                    该操作将删除该数据所有内容，包括条件、库存、价格等信息。
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger">删除</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    function createitem(type, pid){
        $("#item-type .type-select a").show();
        $("#item-type .type-select a").unbind("click").click(function(){ additem($(this).data("code"), pid); });

        $("#item-type .btn-default").hide();

        switch(type){
            case 1:
                additem("hotel", pid);
            break;
            case 2:
                $("#item-type .type-auto").show();
                $("#item-type").modal("show");
            break;
            case 3:
                additem("flight", pid);
            break;
            case 5:
                additem("view", pid);
            break;
            case 4:
                $("#item-type .type-hotel, #item-type .type-flight").show();
                $("#item-type").modal("show");
            break;
            case 6:
                $("#item-type .type-hotel, #item-type .type-view").show();
                $("#item-type").modal("show");
            break;
            case 7:
                additem("goods", pid);
            break;
            case 8:
                $("#item-type .type-flight, #item-type .type-view").show();
                $("#item-type").modal("show");
            break;
            case 9:
                $("#item-type .type-auto").show();
                $("#item-type").modal("show");
            break;
        }
    }

    function additem(type, id){
        $("#item-type").modal("hide");
        $("#item-form").modal("show");
        var form = $("#item-form .modal-body");
        var url = "<?php echo BASE_URL; ?>product.php?method=item&pid="+id+"&type="+type;
        if (form.data("url") != url) {
            form.html("<div style=\"color:#999; text-align:center;\"><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> 正在加载信息..</div>");
            form.data("url", url);
            form.load(url);
        }
    }

    function edititem(btn){
        var id = $(btn).data("code");
        $("#item-form").modal("show");
        var form = $("#item-form .modal-body");
        var url = "<?php echo BASE_URL; ?>product.php?method=item&id="+id;
        if (form.data("url") != url) {
            form.html("<div style=\"color:#999; text-align:center;\"><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> 正在加载信息..</div>");
            form.data("url", url);
            form.load(url);
        }
    }

    function delitem(btn){
        var id = $(btn).data("code"),
            modal = $("#item-delete"),
            btn   = modal.find(".modal-footer .btn-danger");
        btn.unbind("click").bind("click", function(){
            $.post("<?php echo BASE_URL; ?>product.php?method=del", {id:id, type:"item"}, function(data){
                modal.modal("hide");
                if (data.s == 0){
                    $("#item-"+id).fadeOut(500, function(){ $(this).remove(); });
                }else{
                    alert(data.err, "error");
                }
            }, "json");
        });
        modal.modal("show");
    }

    function price(btn){
        var id = $(btn).data("code");
        var type = $(btn).data("type");
        if (type == 'calendar') {
            $("#item-price .modal-dialog").addClass("modal-lg");
        }
        $("#item-price").modal("show");
        $("#item-price .modal-body").html("<div style=\"color:#999; text-align:center;\"><span class=\"glyphicon glyphicon-refresh glyphicon-loading\"></span> 正在加载信息..</div>");
        $("#item-price .modal-body").load("<?php echo BASE_URL; ?>product.php?method=price&id="+id);
    }

    // save prices
    function saveprice(){
        var form = $("#item-price form");
        // if($("#currency").val() == '')
        // {

        //     $("#price").val($("#price_rate").val() );
        // }
        $.post("<?php echo BASE_URL; ?>product.php?method=price", form.serialize(), function(data){
            if(data.s == 0){
                $("#item-price").modal("hide");
            }else{
                alert(data.err, "error", null, "#item-price .modal-body");
            }
        }, "json");
    }
    </script>

    <?php action::exec('product_item_manage_modal'); ?>