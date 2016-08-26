<img class="logo" src="http://pds.putike.cn/template/imgs/<?php echo strtolower($data['from']) ?>.png" />

<h1>行程确认单</h1>

<h2>订单信息</h2>
<div class="row">
    <div class="col-md-2"><b>订单号</b></div><div class="col-md-10"><?php echo $data['order']; ?> </div>
    <div class="col-md-2"><b>姓名</b></div><div class="col-md-10" data-editor="input" data-name="contact"><?php echo $data['contact']; ?> </div>
    <div class="col-md-2"><b>手机号</b></div><div class="col-md-10" data-editor="input" data-name="tel"><?php echo $data['tel']; ?> </div>
    <div class="col-md-2"><b>酒店</b></div><div class="col-md-10" data-editor="input" data-name="hotelname"><?php echo $data['hotelname']; ?> </div>
    <div class="col-md-2"><b>房型</b></div><div class="col-md-10" data-editor="input" data-name="roomname"><?php echo $data['roomname']; ?> </div>
    <div class="col-md-2"><b>套餐</b></div><div class="col-md-10" data-editor="textarea" data-name="intro"><?php echo nl2br($data['intro']); ?> </div>
</div>

<h2>入住信息<?php if ($mode=='edit') { ?><a href="javascript:;" data-target="rooms">增加房间信息</a><?php } ?></h2>
<div class="row" id="rooms">
    <?php foreach ($data['rooms'] as $k => $v) { ?>
    <div class="col-md-2"><b>Room <?php echo $k+1; ?></b></div>
    <div class="col-md-4" data-editor="textarea" data-name="rooms[<?php echo $k; ?>][people]"><?php echo nl2br($v['people']); ?></div>
    <?php } ?>
</div>

<h2>订房明细<?php if ($mode=='edit') { ?><a href="javascript:;" data-target="hotels">增加订房明细</a><?php } ?></h2>
<table class="table" id="hotels">
    <thead>
        <tr>
            <td>酒店</td>
            <td>入住日</td>
            <td>离店日</td>
            <td>订房信息</td>
            <td>备注</td>
            <td>确认号</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data['hotel'] as $k => $v) { ?>
        <tr>
            <td data-editor="input" data-name="hotel[<?php echo $k ?>][hotelname]"><?php echo $v['hotelname']; ?> </td>
            <td data-editor="input" data-name="hotel[<?php echo $k ?>][checkin]"><?php echo $v['checkin']; ?> </td>
            <td data-editor="input" data-name="hotel[<?php echo $k ?>][checkout]"><?php echo $v['checkout']; ?> </td>
            <td data-editor="input" data-name="hotel[<?php echo $k ?>][roomname]"><?php echo $v['roomname']; ?> </td>
            <td data-editor="input" data-name="hotel[<?php echo $k ?>][require]"><?php echo $v['require']; ?> </td>
            <td data-editor="input" data-name="hotel[<?php echo $k ?>][confirmno]"><?php echo $v['confirmno']; ?> </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<hr />
<div class="row">
    <div class="col-md-2"><b>酒店地址</b></div><div class="col-md-10" data-editor="input" data-name="hoteladdress"><?php echo $data['hoteladdress']; ?> </div>
    <div class="col-md-2"><b>酒店电话</b></div><div class="col-md-10" data-editor="input" data-name="hoteltel"><?php echo $data['hoteltel']; ?> </div>
</div>

<?php if (!empty($data['flight'])) { ?>
<h2>航班信息<?php if ($mode=='edit') { ?><a href="javascript:;" data-target="flights">增加航班信息</a><?php } ?></h2>
<table class="table" id="flights">
    <thead>
        <tr>
            <td>始发地</td>
            <td>目的地</td>
            <td>航班</td>
            <td>日期</td>
            <td>起飞时间</td>
            <td>到达时间</td>
            <td>航站楼</td>
            <td>客票状态</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['flight'] as $k => $flight) { ?>
        <tr>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][depart_city]"><?php echo $flight['depart_city']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][arrive_city]"><?php echo $flight['arrive_city']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][flight_code]"><?php echo $flight['flight_code']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][date]"><?php echo $flight['date']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][takeoff]"><?php echo $flight['takeoff']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][landing]"><?php echo $flight['landing']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][terminal]"><?php echo $flight['terminal']; ?> </td>
            <td data-editor="input" data-name="flight[<?php echo $k ?>][status]"><?php echo $flight['status']; ?> </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>

<div>
    <strong>Thanks for choosing <?php echo ucfirst(strtolower($data['from'])); ?>！<br />
    Wish you have a nice stay ！</strong>
</div>