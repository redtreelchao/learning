<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">产品明细</h3>
    </div>
    <div class="panel-body panel-sm">
        <?php $hotel['data'] = json_decode($hotel['data'], true); ?>
        <b>房间售价：</b> ¥<?php echo $hotel['data']['price'] + $hotel['data']['profit']; ?>/间<br />
        <b>　　底价：</b> ¥<?php echo $hotel['data']['price']; ?>/间<br />
        <hr style="margin:10px" />
        <?php $flight['data'] = json_decode($flight['data'], true); ?>
        <b>机票售价：</b> ¥<?php echo $flight['data']['price'] + $flight['data']['profit_adult']; ?>　<b>燃油：</b> ¥<?php echo $flight['data']['adultfuel']; ?>　<b>税/建：</b> ¥<?php echo $flight['data']['adulttax']; ?><br />
        <b>　　底价：</b> ¥<?php echo $flight['data']['price']; ?><br />
        <b>儿童票价：</b> ¥<?php echo $flight['data']['child'] + $flight['data']['profit_child']; ?>　<b>燃油：</b> ¥<?php echo $flight['data']['childfuel']; ?>　<b>税/建：</b> ¥<?php echo $flight['data']['childtax']; ?><br />
        <b>　　底价：</b> ¥<?php echo $flight['data']['child']; ?><br />
        <b>婴儿票价：</b> ¥<?php echo $flight['data']['baby'] + $flight['data']['profit_baby']; ?>　<b>燃油：</b> ¥<?php echo $flight['data']['babyfuel']; ?>　<b>税/建：</b> ¥<?php echo $flight['data']['babytax']; ?><br />
        <b>　　底价：</b> ¥<?php echo $flight['data']['baby']; ?><br />
        <?php //echo $rule; ?>
    </div>
    <div class="panel-footer text-right">
        <?php
        switch ($order['status'])
        {
            case '3':
            case '4':
            case '13':
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#pro4-refund">退款</button> ';
                echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#pro4-hotel-booking">酒店预订成功</button> ';
                echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#pro4-flight-booking">机票预订成功</button>';
                break;

            case '10':
                echo '<label id="order-refund"><button type="button" class="btn btn-danger btn-sm order-refund"  data-status="0" data-type="hotel-flight-prepay" data-id="0">拒绝退款</button></label> ';
                echo '<button type="button" class="btn btn-primary btn-sm" onclick="submit_refund(\'hotel-flight-prepay\', 0)">已退款</button> ';
                break;
        }
        ?>
    </div>
</div>

