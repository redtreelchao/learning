<?php
if (!defined("PT_PATH")) exit;

ignore_user_abort(true);
set_time_limit(0);
ini_set('memory_limit', '512M');

/**
 * Array insert other items
 *
 * @access public
 * @param mixed $arr
 * @param mixed $offset
 * @param array $items
 * @return void
 */
function array_insert_items(&$arr, $offset, $items=array())
{
    $_tmp1 = array_slice($arr, 0, $offset+1);
    $_tmp2 = array_slice($arr, $offset+1);
    $arr = array_merge($_tmp1, $items, $_tmp2);
}

// 订单导出
// payment
$payment = (string)$_POST['payment'];
template::assign('payment', $payment);

// type
$type = empty($_POST['product']) ? 0 : (int)$_POST['product'];
template::assign('product', $type);

// search condition
$join = array();
$group = '';

$join_hotel  = "LEFT JOIN `ptc_order_hotel` AS h ON o.`id`=h.`orderid`";
$join_flight = "LEFT JOIN `ptc_order_flight` AS f ON o.`id`=f.`orderid`";
$join_view   = "LEFT JOIN `ptc_order_view` AS v ON o.`id`=v.`orderid`";
$join_goods  = "LEFT JOIN `ptc_order_goods` AS g ON o.`id`=g.`orderid`";

switch ($type)
{
    case 1:
        $join["order_hotel"] = $join_hotel; $group .= ', h.`id`'; break;
    case 2:
        $join["order_hotel"] = $join_hotel; $group .= ', h.`id`'; break;
    case 3:
        $join["order_flight"] = $join_flight; $group .= ', f.`id`'; break;
    case 5:
        $join["order_view"] = $join_view; $group .= ', v.`id`'; break;
    case 4:
        $join["order_hotel"] = $join_hotel;
        $join["order_flight"] = $join_flight;
        $group .= ', h.`id`, f.`id`';
        break;
    case 6:
        $join["order_hotel"] = $join_hotel;
        $join["order_view"] = $join_view;
        $group .= ', h.`id`, v.`id`';
        break;
    case 7:
        $join["order_goods"] = $join_goods;
        $group .= ', g.`id`';
        break;
    case 8:
        $join["order_flight"] = $join_flight;
        $join["order_view"] = $join_view;
        $group .= ', f.`id`, v.`id`';
        break;
    case 9:
        $join["order_hotel"] = $join_hotel;
        $join["order_flight"] = $join_flight;
        $join["order_view"] = $join_view;
        $group .= ', h.`id`, f.`id`, v.`id`';
        break;
    default:
        json_return(null, 1, '请选择产品类型');
}

$where =  '';
$condition = array(':type' => !in_array($type, array(1,3,5,7)) ? $type : 0);
$keywords = array('time'=>'','start'=>'','end'=>'','name'=>'','from'=>'','supply'=>'','status'=>'','clear'=>'');

if (!empty($_POST['time']))
{
    $start = strtotime($_POST['start']);
    $end = strtotime($_POST['end']);
    if (!$start || !$end || $start >= $end)
        json_return(null, 1, '请选择一个日期范围');

    if ($end - $start >= 366 * 86400)
        json_return(null, 1, '日期不得超过一年');

    switch ($_POST['time'])
    {
        case 'booking':
            $where = 'o.`create` >= :start AND o.`create` < :end';
            $condition[':start'] = $start;
            $keywords['start'] = $start;

            $condition[':end'] = $end;
            $keywords['end'] = $end;

            $keywords['time'] = 'booking';
            break;

        case 'checkin':
            if (!in_array($type, array(1,2,4,6,9))) break;

            $where = $payment == 'ticket' ? 'r.`checkin` >= :start' : 'h.`checkin` >= :start';
            $condition[':start'] = $start;
            $keywords['start'] = $start;

            $where .= $payment == 'ticket' ? ' AND (r.`checkout` > 0 AND r.`checkout` < :end)' : ' AND h.`checkout` < :end';
            $condition[':end'] = $end;
            $keywords['end'] = $end;

            $keywords['time'] = 'checkin';
            break;

        case 'appointment':
            if (!in_array($type, array(1,2,4,6,9))) break;

            $where = 'o.`appointmentime` >= :start AND o.`appointmentime` < :end';
            $condition[':start'] = $start;
            $keywords['start'] = $start;

            $condition[':end'] = $end;
            $keywords['end'] = $end;

            $keywords['time'] = 'appointment';
            break;

        case 'refund':
            $where = 'o.`refundtime` >= :start AND o.`refundtime` < :end';
            $condition[':start'] = $start;
            $keywords['start'] = $start;

            $condition[':end'] = $end;
            $keywords['end'] = $end;

            $keywords['time'] = 'refund';
            break;

        case 'refunded':
            $where = 'o.`refundedtime` >= :start AND o.`refundedtime` < :end';
            $condition[':start'] = $start;
            $keywords['start'] = $start;

            $condition[':end'] = $end;
            $keywords['end'] = $end;

            $keywords['time'] = 'refunded';
            break;
    }
}

if (!empty($_POST['name']))
{
    if (in_array($type, array(1,2,4,6,9)))
    {
        $where .= " AND (h.`productname` LIKE :name OR h.`hotelname` LIKE :name)";
    }
    else if ($type == 7)
    {
        $where .= " AND (g.`productname` LIKE :name OR g.`goodsname` LIKE :name)";
    }
    $condition[':name'] = '%'.trim($_POST['name']).'%';
    $keywords['name'] = trim($_POST['name']);
}

if (!empty($_POST['type']) && in_array($type, array(1,2,4,6,9)))
{
    $join["hotel"] = "LEFT JOIN `ptc_hotel` AS ho ON h.`hotel`=ho.`id`";
    $where .= " AND ho.`type` = :hoteltype";
    $condition[':hoteltype'] = (int)$_POST['type'];
    $keywords['hoteltype'] = (int)$_POST['type'];
}

if (!empty($_POST['from']))
{
    $where .= " AND o.`from` = :from";
    $condition[':from'] = (int)$_POST['from'];
    $keywords['from'] = (int)$_POST['from'];
}

if (!empty($_POST['supply']) && in_array($type, array(1,2,4,6,9)))
{
    if (is_numeric($_POST['supply']))
    {
        $where .= $payment == 'ticket' ? ' AND r.`supply` = :supply' : ' AND h.`supplyid` = :supply';
        $condition[':supply'] = $_POST['supply'];
    }
    else
    {
        $where .= $payment == 'ticket' ? ' AND (h.`supply` = :supply AND r.`supply` = :supply)' : ' AND h.`supply` = :supply';
        $condition[':supply'] = $_POST['supply'];
    }
    $keywords['supply'] = $_POST['supply'];
}

if (!empty($_POST['clear']))
{
    if ($_POST['clear'] == 1)
    {
        $where .= " AND o.`clear` = 2";
        $keywords['clear'] = 1;
    }
    else
    {
        $where .= " AND o.`clear` = 1";
        $keywords['clear'] = -1;
    }
}

template::assign('keywords', $keywords);

// status
$status = array();
if (!empty($_POST['status']))
{
    if (in_array('unpay', $_POST['status']))
        $status = array_merge($status, array(1,2));

    if (in_array('paid', $_POST['status']))
        $status = array_merge($status, array(3,4,5,7,13));

    if (in_array('used', $_POST['status']))
        $status = array_merge($status, array(8));

    if (in_array('over', $_POST['status']))
        $status = array_merge($status, array(9));

    if (in_array('refund', $_POST['status']))
        $status = array_merge($status, array(10,12,16));

    if (in_array('refunded', $_POST['status']))
        $status = array_merge($status, array(11,14));
}

if (!$status)
    json_return(null, 1, '请选择导出的订单状态');

if ($payment == 'ticket')
{
    if ($type == 1)
    {
        if (in_array('paid', $_POST['status'])) $status[] = 0;
        $join["order_room"] = "LEFT JOIN `ptc_order_room` AS r ON h.`id`=r.`pid`";
        if (in_array(1, $status))
            $where .= ' AND h.`producttype`=:type AND h.`supply`="TICKET" AND h.`status` > 0';
        else
            $where .= ' AND h.`producttype`=:type AND h.`supply`="TICKET" AND h.`status` >= 3 AND r.`ticket` IN ('.implode(',', $status).')';
    }
    else if ($type == 7)
    {
        $where .= ' AND g.`producttype`=:type AND g.`supply`="TICKET" AND g.`status` >= 3';
    }
}
else
{
    $where .= ' AND h.`producttype`=:type AND h.`supply`!="TICKET" AND h.`status` IN ('.implode(',', $status).')';
}

if (isset($_POST['count']))
{
    $join = implode(" ", $join);
    $sql = "SELECT __FIELD__ FROM `ptc_order` AS o
                {$join}
            WHERE {$where}
            GROUP BY o.`id`{$group}
            ORDER BY o.`id` ASC";
    switch ($type)
    {
        case 1:
        case 2:
        case 4:
        case 6:
        case 9:
            $count_sql = str_replace('__FIELD__', 'h.id', $sql);
            break;
        case 7:
            $count_sql = str_replace('__FIELD__', 'g.id', $sql);
            break;
    }
    $count = $db -> prepare("SELECT COUNT(*) AS `c` FROM ($count_sql) AS s") -> execute($condition);
    //debug :
    //echo "SELECT COUNT(*) AS `c` FROM ($count_sql) AS s;\n /* "; var_dump($condition); echo '*/'; exit;

    if ($count)
        json_return($count[0]['c']);
    else
        json_return(null, 1, '错误，请重试');
}

// Fields
$fields = $_POST['fields'];

include_once CLASS_PATH.'PHPExcel.php';

define('D_STR2', PHPExcel_Cell_DataType::TYPE_STRING2);
define('D_STR',  PHPExcel_Cell_DataType::TYPE_STRING);
define('D_FML',  PHPExcel_Cell_DataType::TYPE_FORMULA);
define('D_NUM',  PHPExcel_Cell_DataType::TYPE_NUMERIC);

define('A_CENTER',  PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
define('A_LEFT',    PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
define('A_RIGHT',   PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$_fields = array(
                        // field                                            width  bgcolor  type      name
    'order'     => array('CONCAT(o.`order`, " ") AS `order`',              20,    null,    D_STR2,   '订单号'),
    'product'   => array('h.`productname` AS `product`',                    30,    null,    D_STR2,   '产品名'),
    'item'      => array('h.`itemname` AS `item`',                          20,    null,    D_STR2,   '明细'),
    'country'   => array('co.`name` AS `country`',                          11,    null,    D_STR2,   '国家'),
    'province'  => array('ci.`province` AS `province`',                     8,     null,    D_STR2,   '省份'),
    'city'      => array('ci.`name` AS `city`',                             8,     null,    D_STR2,   '城市'),
    'hotel'     => array('h.`hotelname` AS `hotel`',                        20,    null,    D_STR2,   '酒店'),
    'hoteltype' => array('ho.`type` AS `hoteltype`',                        11,    null,    D_STR2,   '类型'),
    'room'      => array('h.`roomname` AS `room`',                          17,    null,    D_STR2,   '房型'),
    'checkin'   => array('FROM_UNIXTIME(h.`checkin`,"%Y/%m/%d") AS `checkin`', 12,    null,    D_STR2,   '入住日期'),
    'checkout'  => array('FROM_UNIXTIME(h.`checkout`,"%Y/%m/%d") AS `checkout`',12,   null,    D_STR2,   '离店日期'),
    'flight'    => array('f.`flightcode` AS `flight`',                      12,    null,    D_STR2,   '航班'),
    'goods'     => array('g.`goodsname` AS `goods`',                        30,    null,    D_STR2,   '商品'),
    'view'      => array('v.`viewname` AS `view`',                          26,    null,    D_STR2,   '景/体验'),
    'from'      => array('org.`name` AS `from`',                            11,    null,    D_STR2,   '分销渠道'),
    'contact'   => array('o.`contact` AS `contact`',                        10,    null,    D_STR2,   '联系人'),
    'tel'       => array('o.`tel` AS `tel`',                                14,    null,    D_STR2,   '联系电话'),
    'floor'     => array('o.`floor` AS `floor`',                            8,     null,    D_NUM,    '底价'),
    'total'     => array('o.`total` AS `total`',                            8,    'FFFF99', D_NUM,    '售价'),
    'invoice'   => array('IF(o.`invoice`=2,"已开",IF(o.`invoice`=1,"未开","")) AS `invoice`',   10,    null,    D_STR2,   '发票'),
    'paytype'   => array('e.`paytype` AS `paytype`',                        10,    null,    D_STR2,   '支付方式'),
    'refund'    => array('o.`refund` AS `refund`',                          8,     null,    D_NUM,    '退款'),
    'time'      => array('FROM_UNIXTIME(o.`create`, "%Y/%m/%d %H:%i:%s") AS `time`',    20,    null,    D_STR2,   '下单日期'),
);

switch ($type)
{
    case 7:
        $_fields['product'][0]  = 'g.`productname` AS `product`';
        $_fields['item'][0]     = 'g.`itemname` AS `item`';
}


// Ticket
if (isset($join['order_room']))
{
    $_fields['floor'][0] = 'h.`floor` AS `floor`';
    $_fields['total'][0] = 'h.`total` AS `total`';
    $_fields['checkin'][0]  = 'IF(r.`checkin`=0, "", FROM_UNIXTIME(r.`checkin`, "%Y/%m/%d")) AS `checkin`';
    $_fields['checkout'][0] = 'IF(r.`checkout`=0, "", FROM_UNIXTIME(r.`checkout`,"%Y/%m/%d")) AS `checkout`';
    unset($_fields['refund'], $fields['refund']);
}

// Other Join
if (in_array('from', $fields))
{
    $join['org'] = 'LEFT JOIN `ptc_org` AS org ON o.`from`=org.`id`';
}

// Order Extend
if (in_array('paytype', $fields))
{
    $join['ext'] = 'LEFT JOIN `ptc_order_ext` AS e ON o.`id`=e.`orderid`';
}

// District
if (in_array('district', $fields))
{
    $fields[] = 'country';
    $fields[] = 'province';
    $fields[] = 'city';
    $join['district'] = 'LEFT JOIN `ptc_district` AS co ON h.`country`=co.`id`
                         LEFT JOIN `ptc_district` AS ci ON h.`city`=ci.`id`';
}

// Hotel Type
if (in_array('hoteltype', $fields))
{
    $join["hotel"] = "LEFT JOIN `ptc_hotel` AS ho ON h.`hotel`=ho.`id`";
    $_hoteltypes = hotel::types();
    $hoteltypes = array();
    foreach($_hoteltypes as $v)
        $hoteltypes[$v['id']] = $v['name'];
}

// Checkout
if (in_array('checkin', $fields))
{
    $fields[] = 'checkout';
}

$supplies = supplies();


// 格外的逐项运算 hook ?
switch ($type.'-'.$payment)
{
    case '1-ticket':

        $_fields['order'][0] = 'CONCAT(o.`order`, "-", h.`id`) AS `order`';
        $_fields['order'][1] = 26;

        $f = array(
            'num'           => array('h.`rooms` AS `num`',                                          9, 'FFCC99',    D_NUM,  '购买数量'),
            'unpay_num'     => array('SUM(IF(r.`ticket` IN (0) AND h.`status` < 3, 1, 0)) AS `unpay_num`',              7,  null,       D_NUM,  '未支付券'),
            'paid_num'      => array('SUM(IF(r.`ticket` IN (0,3,4,5,7,13) AND h.`status` >= 2, 1, 0)) AS `paid_num`',   7,  null,       D_NUM,  '可用券'),
            'used_num'      => array('SUM(IF(r.`ticket` IN (8,9), 1, 0)) AS `used_num`',            7,  null,       D_NUM,  '已用券'),
            'refund_num'    => array('SUM(IF(r.`ticket` IN (10,12,16), 1, 0)) AS `refund_num`',     9,  null,       D_NUM,  '申退券数'),
            'refunded_num'  => array('SUM(IF(r.`ticket` IN (11,14), 1, 0)) AS `refunded_num`',      7,  null,       D_NUM,  '已退券'),
            '_rooms'        => array('1 AS `_rooms`',                                               5,  null,       D_NUM,  '间'),
            '_nights'       => array('h.`nights` AS `_nights`',                                     5,  null,       D_NUM,  '夜'),
            'nights'        => array('SUM(IF(r.`ticket` IN (8,9), 1, 0)) * h.`nights` AS `nights`', 7,  null,       D_NUM,  '间夜数'),
            'status'        => array('IF(h.`status`>=3, "Y", "") AS `status`',                      7,  null,       D_STR2, '已付款'),
            'settle'        => array('IF(r.`settle`!=0, FROM_UNIXTIME(r.`settletime`, "%Y/%m/%d"), "") AS `settle`',         10,  null,      D_STR2, '结算时间'),
        );

        if (in_array('checkin', $fields) || in_array('settle', $fields))
        {
            $_fields['order'][0] = 'CONCAT(o.`order`, "-", h.`id`, "-", r.`group`) AS `order`';
            $_fields['order'][1] = 28;
            $group .= ', r.`group`';

            $_fields['floor'][0] = 'h.`floor`/h.`rooms`*COUNT(r.id) AS `floor`';
            $_fields['total'][0] = 'h.`total`/h.`rooms`*COUNT(r.id) AS `total`';

            $f['num'][0]         = 'COUNT(r.id) AS `num`';
        }

        if (in_array('paid', $_POST['status'])) $fields[] = 'paid_num';
        if (in_array('used', $_POST['status'])) $fields[] = 'used_num';
        if (in_array('refund', $_POST['status'])) $fields[] = 'refund_num';
        if (in_array('refunded', $_POST['status'])) $fields[] = 'refunded_num';
        if (in_array('nights', $fields))
        {
            $fields[] = '_rooms';
            $fields[] = '_nights';
        }

        $pos = array_search('tel', array_keys($_fields));
        array_insert_items($_fields, $pos, $f);

        $f = array();
        if (in_array('supply', $fields))
        {
            /*
            foreach ($supplies as $k => $sup)
            {
                $fields[] = 'sup_'.$k;
                $f['sup_'.$k] = array("SUM(IF(r.`supply`='{$k}', 1, 0)) AS `sup_{$k}`",    12,  'CCFFFF',  D_NUM,  $sup.'供');
            }*/

            $join["supply"] = "LEFT JOIN `ptc_supply` AS s ON r.`supply`=s.`id`";

            $f['supply']  = array('s.`name` AS `supply`',                                                    15, null, D_STR2, '供应商');
            $f['s_mode']  = array('CASE s.`mode` WHEN 1 THEN "直采" WHEN 2 THEN "分销" END AS `s_mode`',     10,  null, D_STR2, '合作方式');
            $f['s_payby'] = array('CASE s.`payby`
                                    WHEN 1 THEN CONCAT("预付", s.`period`)
                                    WHEN 2 THEN "周结"
                                    WHEN 3 THEN "一单一结"
                                    WHEN 4 THEN "月结"
                                  END AS `s_mode`',     10,  null, D_STR2, '结算方式');
            $f['s_bank']    = array('s.`bank` AS `s_bank`',                 15,  null, D_STR2, '开户行');
            $f['s_account'] = array('s.`bankaccount` AS `s_account`',       15,  null, D_STR2, '户名');
            $f['s_code']    = array('s.`bankcode` AS `s_code`',             15,  null, D_STR2, '银行账号');

            array_push($fields, 's_mode', 's_payby', 's_bank', 's_account', 's_code');
        }

        $pos = array_search('supply', array_keys($_fields));
        array_insert_items($_fields, $pos, $f);
        break;

    case '1-prepay':
    case '2-prepay':
        $_fields['status'] = array('s.`name` AS `status`', 16,  null,   D_STR2, '订单状态');

        if (in_array('status', $fields))
            $join['status'] = 'LEFT JOIN `ptc_order_status` AS s ON s.`id`=o.`status`';
        break;

    case '4-prepay':
        $_fields['num'] = array('g.`num` AS `num`', 8,  null,   D_NUM, '数量');
        break;
}


$columns = array();
$column_names = array();

foreach ($_fields as $k => $v)
{
    if (in_array($k, $fields))
    {
        $columns[$k] = $v[0];
        $column_names[$k] = array('name'=>$v[4], 'width'=>$v[1], 'bg'=>$v[2], 'type'=>$v[3]);
    }
}
// var_dump($column_names); exit;

// Query
$columns = implode(',', $columns);
$join = implode(' ', $join);
$sql = "SELECT {$columns}
        FROM `ptc_order` AS o
            {$join}
        WHERE {$where}
        GROUP BY o.`id`{$group}
        ORDER BY o.`id` ASC";
// echo str_replace(array_keys($condition), array_values($condition), $sql); exit;
$data = $db -> prepare($sql) -> execute($condition);


$objExcel = new PHPExcel();

$objProps = $objExcel -> getProperties();
$objProps -> setCreator("PUTIKE.CN");
$objProps -> setTitle("璞缇客订单数据导出，仅供内部使用");
$objExcel -> setActiveSheetIndex(0);

$objActSheet = $objExcel -> getActiveSheet();
$objActSheet -> setTitle(date('Y-m-d', $start).' - '.date('Y-m-d', $end).($keywords['time'] == 'booking' ? '下单' : '入住'));

$defaultCss = $objActSheet -> getDefaultStyle();
$defaultCss -> getFont() -> setSize(10);

// Default Style
$objActSheet -> getDefaultRowDimension() -> setRowHeight(18);


$field = array();
array_unshift($column_names, array('name'=>'序号', 'width'=>5, 'bg'=>null, 'type'=>D_NUM));

// Columns
$i = 0;
foreach ($column_names as $k => $v)
{
    $code = (floor($i / 26) ? chr(64 + floor($i / 26)) : '') . chr(65 + $i % 26);

    // Width
    $objActSheet -> getColumnDimension($code) -> setWidth($v['width']);
    $objActSheet -> getRowDimension(1) -> setRowHeight(18);
    $objActSheet -> getStyle("{$code}1") -> getAlignment() -> setHorizontal(A_CENTER) -> setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setWrapText(true);
    $objActSheet -> getStyle("{$code}1") -> getFont() -> setBold(true);

    // Style
    $style = $objActSheet -> getStyle("{$code}1");
    $style -> getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('FFCCCCCC');

    $objActSheet -> setCellValue("{$code}1", $v['name']);
    $v['code']  = $code;
    $field[$k]  = $v;
    $i ++;
}


// Freeze Title Row
$objActSheet -> freezePane('A1');
$objActSheet -> freezePane('A2');

// Rows
foreach ($data as $key => $values)
{
    $row = $key + 2;
    $objActSheet -> setCellValue("A{$row}", $key+1, PHPExcel_Cell_DataType::TYPE_NUMERIC);

    foreach ($values as $k => $v)
    {
        $code = $field[$k]['code'];

        if ($k == 'hoteltype')
        {
            $v = $hoteltypes[$v];
        }

        // Style
        if ($field[$k]['bg'])
        {
            $style = $objActSheet -> getStyle("{$code}{$row}");
            $style -> getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('FF'.$field[$k]['bg']);
        }

        $objActSheet -> setCellValue("{$code}{$row}", $v, $field[$k]['type']);
    }
}

header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition:inline;filename=\"Order_type{$type}{$payment}_".date('YmdHis').".xlsx\"");
header("Content-Transfer-Encoding: binary");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
$objWriter = new PHPExcel_Writer_Excel2007($objExcel);
$objWriter -> save('php://output');
exit;