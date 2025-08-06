		<form id="ixForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Ix API</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_API" type="checkbox" value="1" <?php echo is_checked($IX_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="IX_API" id="ix-api" value="<?php echo $IX_API; ?>" />
				<span class="help-block">Turn API On/Off</span>
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสคลัง IX</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<select class="fix-width-350" id="ix-warehouse" name="IX_WAREHOUSE" onchange="ixZoneInit()">
					<option value="">เลือกคลัง</option>
					<?php echo select_sell_warehouse($DEFAULT_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสโซน IX</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-zone" name="IX_ZONE" value="<?php echo $IX_ZONE; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสคลังรับคืน IX</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<select class="fix-width-350" id="ix-return-warehouse" name="IX_RETURN_WAREHOUSE" onchange="ixReturnZoneInit()">
					<option value="">เลือกคลัง</option>
					<?php echo select_sell_warehouse($IX_RETURN_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสโซนรับคืน IX</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-return-zone" name="IX_RETURN_ZONE" value="<?php echo $IX_RETURN_ZONE; ?>" />
      </div>
      <div class="divider"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">SYNC API STOCK</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SYNC_IX_STOCK" type="checkbox" value="1" <?php echo is_checked($SYNC_IX_STOCK , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SYNC_IX_STOCK" id="sync-ix-stock" value="<?php echo $SYNC_IX_STOCK; ?>" />
				<span class="help-block">Sync available stock to ix api</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Logs Json</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($IX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="IX_LOG_JSON" id="ix-log-json" value="<?php echo $IX_LOG_JSON; ?>" />
				<span class="help-block">Logs Json text for test</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Test Mode</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_TEST" type="checkbox" value="1" <?php echo is_checked($IX_TEST , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="IX_TEST" id="ix-test" value="<?php echo $IX_TEST; ?>" />
				<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
      </div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
        <button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('ixForm')">SAVE</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
