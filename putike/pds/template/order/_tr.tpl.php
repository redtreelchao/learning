<?php
if (!defined('BASE_URL')) define('BASE_URL', '/');

function tmpl_buttons($order, $item, $k){
    global $operators;
    $_operator = $item['operator'] ? $operators[$item['operator']].' 锁定了订单' : '';

    if ($item['operator'] && $item['operator'] != $_SESSION['uid']) {
        echo '<a target="" href="', BASE_URL, 'order.php?order=', $order['order'], '&pr=', $k, $item['id'], '&method=view" class="btn btn-sm btn-default"><span class="fa fa-eye hidden-md"></span><span class="hidden-xs hidden-sm"> 查看</span></a><br class="hidden-xs hidden-sm" /> ',
             '<a href="javascript:;" title="'.$_operator.'" onclick="unlock(this, \'', $order['order'], "', '{$k}{$item['id']}'", ');" class="btn btn-sm btn-default btn-unlock"><span class="fa fa-unlock-alt hidden-md"></span><span class="hidden-xs hidden-sm"> 解锁</span></a> ';
    } else {
        if ($item['operator']) { echo '<a href="javascript:;" title="'.$_operator.'" onclick="unlock(this, \'', $order['order'], "', '{$k}{$item['id']}'", ');" class="btn btn-sm btn-default btn-unlock"><span class="fa fa-unlock-alt hidden-md"></span><span class="hidden-xs hidden-sm"> 解锁</span></a> '; }
        else { echo '<a target="" href="', BASE_URL, 'order.php?order=', $order['order'], '&pr=', $k, $item['id'], '&method=view" class="btn btn-sm btn-default"><span class="fa fa-eye hidden-md"></span><span class="hidden-xs hidden-sm"> 查看</span></a> '; }
        echo '<a target="" href="', BASE_URL, 'order.php?order=', $order['order'], '&pr=', $k, $item['id'], '&method=operate" class="btn btn-sm btn-primary"><span class="fa fa-pencil hidden-md"></span><span class="hidden-xs hidden-sm"> 操作</span></a> ';
    }
}

foreach($list as $k => $v) {
?>
    <tr id="order-<?php echo $v['order']; ?>" class="<?php if(isset($v['important'])) echo 'important'; if(!isset($page)) echo ' new'; if($v['status'] < 3) echo ' unpay'; ?>">
        <td class="bgf9"><input type="checkbox" class="checkbox" value="<?php echo $v['order']; ?>" /></td>
        <td class="bgf9" colspan="5">
            <span class="label label-default" style="background:#<?php echo $v['color']; ?>; font-size:12px;"><?php echo $v['org']; ?></span>&nbsp;
            <?php
            if($v['warn']){
                switch($v['warn']){
                    case '1': echo '<span class="label label-warning">旧订单</span>'; break;
                }
            }
            ?>
            <?php echo $v['order']; ?> &nbsp;&nbsp;&nbsp;
            <span class="info"><?php echo $v['contact'],' / ',$v['tel']; ?></span>
        </td>
        <td class="bgf9" colspan="2">
            <abbr style="<?php if(in_array($v['status'], array(4,10,13))) echo "color:#c9302c;"; ?>" title="<?php echo '下单时间: ', date('Y-m-d H:i:s', $v['create']), ($v['paytime'] ? "\n支付时间: ".date('Y-m-d H:i:s', $v['paytime']) : ''), ($v['refundtime'] ? "\n退款申请：".date('Y-m-d H:i:s', $v['refundtime']) : ''); ?>"><?php echo $v['status_str']; ?></abbr>
        </td>
    </tr>


    <?php
    // ----------------------------------- HOTEL -------------------------------------
    if (!empty($v['hotels'])) {
        foreach($v['hotels'] as $hotel) {
    ?>
    <tr class="f12<?php if($v['status'] < 3) echo ' unpay'; ?>">
        <td>&nbsp;</td>
        <td>
            <?php
                echo $hotel['supply'] == 'TICKET' ? '<span class="tag">券</span> ' : '<span class="tag red">预</span> ';
                echo $hotel['hotelname'];
                if (!empty($hotel['productname']) && $hotel['supply'] == 'TICKET') {
                    echo "<br /><span class=\"info\">{$hotel['productname']}</span>";
                }
            ?>
        </td>
        <td>
            <?php
                echo $hotel['roomname'];
                if (!empty($hotel['itemname']) && $hotel['supply'] == 'TICKET') {
                    echo "<br /><span class=\"info\">{$hotel['itemname']}</span>";
                }
            ?>
        </td>
        <td>
            <?php
                if ($hotel['supply'] == 'TICKET') {
                    if ($hotel['start'] || $hotel['end']) {
                        echo '有效期<br />'.date('m-d', $hotel['start']).'<br />'.date('m-d', $hotel['end']);
                    } else {
                        echo '长期有效';
                    }
                } else {
                    echo '共',$hotel['nights'].'晚<br />',date('m-d', $hotel['checkin']).'<br />'.date('m-d', $hotel['checkout']);
                }
            ?>
        </td>
        <td>
            <?php echo $hotel['rooms'],($hotel['supply'] == 'TICKET' ? '张' : '间'); ?>
        </td>
        <td>
            <?php echo '￥',$hotel['total'],' (',($hotel['rooms'] * $hotel['nights']),')'; ?>
        </td>
        <td>
            <?php echo $status[$hotel['status']]; ?>
        </td>
        <td class="md-nowrap">
            <?php tmpl_buttons($v, $hotel, 'h'); ?>
        </td>
    </tr>
    <?php
        }
    }
    ?>


    <?php
    // ----------------------------------- FLIGHT -------------------------------------
    if (!empty($v['flights'])) {
        foreach($v['flights'] as $flight) {
        }
    }
    ?>


    <?php
    // ----------------------------------- GOODS -------------------------------------
    if (!empty($v['goods'])) {
        foreach($v['goods'] as $goods) {
    ?>
    <tr class="f12<?php if($v['status'] < 3) echo ' unpay'; ?>">
        <td>&nbsp;</td>
        <td>
            <?php
                echo $goods['supply'] == 'TICKET' ? '<span class="tag">券</span> ' : '<span class="tag red">预</span> ';
                if (!empty($goods['productname']) && $goods['supply'] == 'TICKET') {
                    echo $goods['productname'];
                }
            ?>
        </td>
        <td>
            <?php
                echo $goods['goodsname'];
            ?>
        </td>
        <td colspan="2">
            数量：<?php echo $goods['num']; ?>
        </td>
        <td>
            <?php echo '￥',$goods['total']; ?>
        </td>
        <td>
            <?php echo $status[$goods['status']]; ?>
        </td>
        <td class="md-nowrap">
            <?php tmpl_buttons($v, $goods, 'g'); ?>
        </td>
    </tr>
    <?php
        }
    }
    ?>



    <?php
    // ----------------------------------- PRODUCT -------------------------------------
    if (!empty($v['products'])) {
        foreach($v['products'] as $product) {
            $_status = array();
            foreach ($product['items'] as $_item) {
                $_status[] = $_item['status'];
            }

            $_status = array_unique($_status);
            $_rows = count($product['items']);
    ?>
    <tr class="f12<?php if($v['status'] < 3) echo ' unpay'; ?>">
        <td rowspan="<?php echo $_rows; ?>">&nbsp;</td>
        <td rowspan="<?php echo $_rows; ?>">
            <?php
                switch ($product['type']) {
                    case 2:
                        echo '<span class="tag orange">车</span> ';
                        break;
                    case 4:
                        echo '<span class="tag blue">机</span> ';
                        break;
                }
                echo $product['name'];
                foreach ($product['items'] as $_item)
                {
                    echo '<br />';
                    switch ($_item['type'])
                    {
                        case 'hotel':
                            echo '<span class="info">', $_item['hotelname'], '</span>';
                        break;
                        case 'flight':
                            echo '<span class="info">', $_item['flightname'], '</span>';
                        break;
                    }
                }
            ?>
        </td>
        <td>
            <?php
                $item = $product['items'][0];
                switch ($item['type'])
                {
                    case 'hotel':
                        echo $item['item'], '<br />';
                        echo '<span class="info">', $item['roomname'], '</span>';
                        break;
                    case 'flight':
                        echo $item['item'], '出发<br />';
                        echo '<span class="info">', $item['flightcode'], '</span>';
                        break;
                    case 'view':
                        break;
                }
            ?>
        </td>
        <td>
            <?php
                switch ($item['type'])
                {
                    case 'hotel':
                        if ($item['supply'] == 'TICKET') {
                            if ($item['start'] || $item['end']) {
                                echo '有效期<br />'.date('m-d', $item['start']).'<br />'.date('m-d', $item['end']);
                            } else {
                                echo '长期有效';
                            }
                        } else {
                            echo '共',$item['nights'].'晚<br />',date('m-d', $item['checkin']).'<br />'.date('m-d', $item['checkout']);
                        }
                    break;
                    case 'flight':
                        if ($item['supply'] == 'TICKET') {
                            if ($item['start'] || $item['end']) {
                                echo '有效期<br />'.date('m-d', $item['start']).'<br />'.date('m-d', $item['end']);
                            } else {
                                echo '长期有效';
                            }
                        } else {
                            echo '登机<br />',date('m-d', $item['date']);
                        }
                    break;
                }
            ?>
        </td>
        <td>
            <?php
                switch ($item['type'])
                {
                    case 'hotel':
                        echo $item['rooms'],'间';
                    break;
                    case 'flight':
                        echo $item['passengers'],'人';
                    break;
                }
            ?>
        </td>
        <td rowspan="<?php echo $_rows; ?>">
            <?php
                $_total = 0;
                foreach ($product['items'] as $k => $item)
                {
                    $_total += $item['total'];
                }
                echo '￥',$_total;
            ?>
        </td>
        <td <?php if(count($_status) == 1){ echo 'rowspan="'.$_rows.'"'; } ?>>
            <?php echo $status[$item['status']]; ?>
        </td>
        <td class="md-nowrap" rowspan="<?php echo $_rows; ?>">
            <?php tmpl_buttons($v, $product, 'p'); ?>
        </td>
    </tr>
    <?php
            foreach($product['items'] as $k => $item)
            {
                if ($k <= 0) continue;
    ?>
    <tr class="f12<?php if($v['status'] < 3) echo ' unpay'; ?>">
        <td>
            <?php
                switch ($item['type'])
                {
                    case 'hotel':
                        echo $item['item'], '<br />';
                        echo '<span class="info">', $item['roomname'], '</span>';
                        break;
                    case 'flight':
                        echo $item['item'], '出发<br />';
                        echo '<span class="info">', $item['flightcode'], '</span>';
                        break;
                    case 'view':
                        break;
                    case 'auto':
                        echo $item['item'], '出发<br />';
                        echo '<span class="info">', $_item['autoname'], '</span>';
                    break;
                }
            ?>
        </td>
        <td>
            <?php
                switch ($item['type'])
                {
                    case 'hotel':
                        if ($item['supply'] == 'TICKET') {
                            if ($item['start'] || $item['end']) {
                                echo '有效期<br />'.date('m-d', $item['start']).'<br />'.date('m-d', $item['end']);
                            } else {
                                echo '长期有效';
                            }
                        } else {
                            echo '共',$item['nights'].'晚<br />',date('m-d', $item['checkin']).'<br />'.date('m-d', $item['checkout']);
                        }
                    break;
                    case 'flight':
                        if ($item['supply'] == 'TICKET') {
                            if ($item['start'] || $item['end']) {
                                echo '有效期<br />'.date('m-d', $item['start']).'<br />'.date('m-d', $item['end']);
                            } else {
                                echo '长期有效';
                            }
                        } else {
                            echo '登机<br />',date('m-d', $item['date']);
                        }
                    case 'auto':
                        if ($item['supply'] == 'TICKET') {
                            if ($item['start'] || $item['end']) {
                                echo '有效期<br />'.date('m-d', $item['start']).'<br />'.date('m-d', $item['end']);
                            } else {
                                echo '长期有效';
                            }
                        } else {
                            echo '提车<br />',date('m-d', $item['date']);
                        }
                    break;
                }
            ?>
        </td>
        <td>
            <?php
                switch ($item['type'])
                {
                    case 'hotel':
                        echo $item['rooms'],'间';
                    break;
                    case 'flight':
                        echo $item['passengers'],'人';
                    case 'auto':
                    break;
                }
            ?>
        </td>
        <?php if(count($_status) > 1){ ?>
        <td>
            <?php
            echo $status[$item['status']];
            ?>
        </td>
        <?php } ?>
    </tr>
        <?php
            }
        }
    }
}

