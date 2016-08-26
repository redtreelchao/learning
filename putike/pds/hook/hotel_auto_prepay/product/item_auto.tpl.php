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
            <label class="col-sm-2 control-label">车辆</label>
            <div class="col-sm-7 col-xs-9">
                <select name="auto" class="form-control ui-select">
                    <option value="">请选择..</option>
                    <?php foreach($autos as $r) { ?>
                    <option value="<?php echo $r['id']; ?>"<?php if($r['id'] == $data['objpid']) echo " selected" ?>><?php echo $r['code'], "（{$r['company']}）"; ?></option>
                    <?php } ?>
                </select>
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
                <textarea class="form-control" name="childstd" rows="1"><?php echo $data['childstd']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">婴儿说明</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="babystd" rows="1"><?php echo $data['babystd']; ?></textarea>
            </div>
        </div>

    </div>

    <input type="hidden" name="type" value="auto" />
    <input type="hidden" id="item-pid" name="pid" value="<?php echo $pid; ?>" />
    <input type="hidden" id="item-id" name="id" value="<?php echo $data['id']; ?>" />
</form>

<script>
$(function(){
    $(".ui-select").chosen({disable_search_threshold:10, width:"100%", no_results_text:"未找到..", placeholder_text_single:"请选择.."});
});
</script>