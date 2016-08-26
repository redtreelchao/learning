<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">产品明细</h3>
    </div>
    <div class="panel-body panel-sm" style="line-height:22px;">
        <b>当前产品状态：</b> <?php echo $product['status'] > 0 ? '在线' : '下架'; ?><br />
        <hr style="margin:10px" />
        <b>销售单价：</b> ¥<?php echo $item['total']; ?><br />
        <b>购买数量：</b> <?php echo $item['num']; ?><br />
    </div>

    <?php if ($mode == 'operate') { ?>
    <div class="panel-footer text-right">
        <?php
        switch ($item['status'])
        {
            case 3:
            case 4:
            case 16:
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#ticket-refund">退款</button> ';
                echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ticket-used">发货</button>';
                break;

            case 8:
            case 9:
                echo '<button type="button" class="btn btn-default btn-sm" disabled>已发货</button>';
                break;

            default:
                echo '<button type="button" class="btn btn-default btn-sm" disabled>不可用</button>';
        }
        ?>
    </div>
    <?php } ?>
</div>


<?php if ($item['status'] == 10 && $mode == 'operate') { ?>
<div id="order-refund" class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">申请退款</h3>
    </div>
    <div class="panel-body panel-sm">　
        <b>退款费用：</b> <?php echo $item['refund']; ?><br />
    </div>
    <div class="panel-footer text-right">
        <button type="button" class="btn btn-danger btn-sm order-refund" data-status="0" data-type="goods-ticket" data-id="<?php echo $item['id']; ?>">拒绝退款</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="submit_refund('goods-ticket', '<?php echo $item['id']; ?>')">已退款</button>
    </div>
</div>
<?php } ?>