<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">券类明细</h3>
    </div>
    <div class="panel-body panel-sm" style="line-height:22px;">
        <b>当前产品状态：</b> <?php echo $ticket['status'] > 0 ? '在线' : '下架'; ?><br />
        <b>单券间夜数：</b> 1间/<?php echo $night; ?>晚<br />
        <hr style="margin:10px" />
        <b>券售价：</b> ¥<?php echo $floor + $profit; ?>/张<br />
        <b>券底价：</b> ¥<?php echo $floor; ?>/张<br />
    </div>

    <?php if ($mode == 'operate') { ?>
    <div class="panel-footer text-right">
        <?php
        if ($enable > 0) {
            if ($end && NOW >= $end + 86400) {
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#ticket-refund">退款</button> ';
                echo '<button type="button" class="btn btn-default btn-sm" ondblclick="$(\'#ticket-used\').modal(\'toggle\');">已过期</button>';
            } else {
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#ticket-refund">退款</button> ';
                echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ticket-used">预约使用</button>';
            }
        } else {
            echo '<button type="button" class="btn btn-default btn-sm" disabled>已用光</button>';
        }
        ?>
    </div>
    <?php } ?>
</div>


<?php if ($need_refund && $mode == 'operate') { ?>
<div id="order-refund" class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">申请退款</h3>
    </div>
    <div class="panel-body panel-sm">
        <b>申请数量：</b> <?php echo $need_refund; ?>张　　　　　
        <b>退款费用：</b> <?php echo $item['refund']; ?><br />
    </div>
    <div class="panel-footer text-right">
        <button type="button" class="btn btn-danger btn-sm order-refund" data-status="0" data-type="hotel-ticket" data-id="<?php echo $item['id']; ?>">拒绝退款</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="submit_refund('hotel-ticket', '<?php echo $item['id']; ?>')">已退款</button>
    </div>
</div>
<?php } ?>


<?php if ($settles && $mode == 'operate') { ?>
<div id="order-refund" class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">结算</h3>
    </div>
    <div class="panel-body panel-sm">
        <?php
        $done = $todo = 0;
        $settletime = array();
        if (isset($settles['all'])) {
            if ($settles['all']['status']) {
                $done = count($item['rooms']);
            } else {
                $todo = count($item['rooms']);
                $settletime[] = $settles['all']['date'];
            }
        } else {
            foreach ($settles as $v)
                if ($v['status']) {
                    $done++;
                } else {
                    $todo++;
                    $settletime[] = $v['date'];
                }
        }
        ?>
        <b>待结算：</b> <?php echo $todo; ?>张<br />
        <b>已结算：</b> <?php echo $done; ?>张<br />
        <?php if ($settletime) { ?>
        <b class="text-danger">结算截至日：</b> <?php echo date('Y-m-d', min($settletime)); ?>
        <?php } ?>
    </div>
    <?php if ($todo) { ?>
    <div class="panel-footer text-right">
        <button type="button" class="btn btn-primary btn-sm" onclick="submit_settle('hotel-ticket', '<?php echo $item['id']; ?>')">已结算</button>
    </div>
    <?php } ?>
</div>
<?php } ?>