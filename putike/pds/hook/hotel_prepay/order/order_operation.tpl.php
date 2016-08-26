<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">价格日历</h3>
    </div>
    <div class="panel-body panel-sm">
        <ul class="mid-calendar">
            <li class="week w7">日</li>
            <li class="week w1">一</li>
            <li class="week w2">二</li>
            <li class="week w3">三</li>
            <li class="week w4">四</li>
            <li class="week w5">五</li>
            <li class="week w6">六</li>
            <?php
            for($i = $start; $i <= $end; $i = $i + 86400)
            {
                $class = 'w'.date('N', $i).' ';
                $week = array('', '一', '二', '三', '四', '五', '六', '日');

                $price = $floor = $bf = '';
                $title = date('Y年m月d日', $i).' 周'.$week[date('N', $i)];
                if (isset($prices[$i]))
                {
                    $d = $prices[$i];

                    $breakfasts = array('无早', '单早', '双早', '三早');
                    $bf = $d['bf'] > 3 ? "{$d['bf']}早" : $breakfasts[$d['bf']];
                    $price  = $d['p'] + $d['pf'];
                    $floor  = $d['p'];
                }
            ?>
            <li class="<?php echo trim($class); ?>" title="<?php echo $title; ?>"><b><?php echo date('d', $i); ?></b><?php echo $price, '<br />', $floor, '<br />', $bf; ?></li>
            <?php } ?>
        </ul>
    </div>
    <div class="panel-footer text-right">
        <?php
        switch ($item['status'])
        {
            case '3':
            case '4':
            case '12':
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#prepay-refund">退款</button> ';
                echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#prepay-booking">预订成功</button>';
                break;
            case '5':
            case '7':
            case '8':
            case '13':
                echo '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#prepay-refund">退款</button>';
                break;
        }
        ?>
    </div>
</div>

<?php if ($item['status'] == 10 && $mode == 'operate') { ?>
<div id="order-refund" class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">申请退款</h3>
    </div>
    <div class="panel-body panel-sm">
        <b>申请退款金额：</b> ¥<?php echo $item['refund']; ?>　　
    </div>
    <div class="panel-footer text-right">
        <button type="button" class="btn btn-danger btn-sm order-refund" data-status="0" data-type="hotel-prepay" data-id="<?php echo $item['id']; ?>">拒绝退款</button>
        <button type="button" class="btn btn-primary btn-sm order-refund" onclick="submit_refund('hotel-prepay', '<?php echo $item['id']; ?>')">已退款</button>
    </div>
</div>
<?php } ?>

