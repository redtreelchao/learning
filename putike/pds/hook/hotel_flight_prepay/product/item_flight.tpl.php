<?php define('BASE_URL', '/'); ?>
<form role="form" class="form-horizontal">

    <div id="page1" class="page">

        <div class="form-group">
            <label class="col-sm-2 control-label">始发地</label>
            <div class="col-sm-9">
                <input type="text" name="name" class="form-control" value="<?php echo $data['name']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">航班</label>
            <div class="col-sm-7 col-xs-9">
                <select name="flight" class="form-control ui-select">
                    <option value="">请选择..</option>
                    <?php foreach($flights as $r) { ?>
                    <option value="<?php echo $r['id']; ?>"<?php if($r['id'] == $data['objpid']) echo " selected" ?>><?php echo $r['code'], " （{$r['depart_airport']} - {$r['arrive_airport']}）"; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-2 col-xs-3">
                <input type="text" name="class" class="form-control" placeholder="舱位" value="<?php echo $data['ext2']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">包含返程</label>
            <div class="col-sm-7">
                <select name="back" class="form-control ui-select">
                    <option value="0">无</option>
                    <?php foreach($flights as $r) { ?>
                    <option value="<?php echo $r['id']; ?>"<?php if($r['id'] == $data['objid']) echo " selected" ?>><?php echo $r['code'], " （{$r['depart_airport']} - {$r['arrive_airport']}）"; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-2 col-xs-3">
                <input type="text" name="back_class" class="form-control" placeholder="舱位" value="<?php echo $data['ext2']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">返程日期</label>
            <div class="col-sm-7">
                <div class="input-group">
                    <input type="number" name="ext" class="form-control" value="<?php echo $data['ext']; ?>" />
                    <span class="input-group-addon">天后</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">产品说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="intro" rows="4"><?php echo $data['intro']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">儿童说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="childstd" rows="2"><?php echo $data['childstd']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">婴儿说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="babystd" rows="2"><?php echo $data['babystd']; ?></textarea>
            </div>
        </div>

    </div>



    <div id="page2" class="page hidden">

        <div class="form-group">
            <label class="col-sm-2 control-label">提前预订</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" name="advance" class="form-control" value="<?php echo $extend['advance']; ?>" />
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">餐饮类型</label>
            <div class="col-sm-9">
                <input type="text" name="meal" class="form-control" readonly value="<?php echo 'B'; ?>" />
            </div>
        </div>

    </div>


    <ul class="pager">
        <li class="previous hidden"><a href="#page1">&laquo; 基本资料</a></li>
        <li class="next"><a href="#page2">更多资料 &raquo;</a></li>
    </ul>

    <input type="hidden" name="type" value="flight" />
    <input type="hidden" id="item-pid" name="pid" value="<?php echo $pid; ?>" />
    <input type="hidden" id="item-id" name="id" value="<?php echo $data['id']; ?>" />
</form>

<script>
$(function(){
    $(".ui-select").chosen({disable_search_threshold:10, width:"100%", no_results_text:"未找到..", placeholder_text_single:"请选择.."});
});
</script>