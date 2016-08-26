<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">产品明细</h3>
    </div>
    <div class="panel-body panel-sm">
        <?php $hotel['data'] = json_decode($hotel['data'], true); ?>
        <b>房间售价：</b> ¥<?php echo $hotel['data']['price'] + $hotel['data']['profit']; ?>/间<br />
        <b>　　底价：</b> ¥<?php echo $hotel['data']['price']; ?>/间<br />
        <hr style="margin:10px" />
    </div>
    <div class="panel-footer text-right">
        <?php
        switch ($order['status'])
        {
            case '3':
            case '4':
            case '13':
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#pro2-refund">退款</button> ';
                echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#pro2-booking">预订成功</button> ';
                break;

            case '10':
                echo '<label id="order-refund"><button type="button" class="btn btn-danger btn-sm order-refund"  data-status="0" data-type="hotel-auto-prepay" data-id="0">拒绝退款</button></label> ';
                echo '<button type="button" class="btn btn-primary btn-sm" onclick="submit_refund(\'hotel-auto-prepay\', 0)">已退款</button> ';
                break;
        }
        ?>
    </div>
</div>

