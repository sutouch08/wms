	<form id="sokojungForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sokojung API</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SOKOJUNG_API" type="checkbox" value="1" <?php echo is_checked($SOKOJUNG_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SOKOJUNG_API" id="sokojung-api" value="<?php echo $SOKOJUNG_API; ?>" />
				<span class="help-block">Turn API On/Off</span>
      </div>
      <div class="divider"></div>

    	<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sokojung api endpoint</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_HOST"  value="<?php echo $SOKOJUNG_API_HOST; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sokojung api user name</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_USER_NAME" value="<?php echo $SOKOJUNG_API_USER_NAME; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sokojung api password</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="password" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_PASSWORD" value="<?php echo $SOKOJUNG_API_PASSWORD; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sokojung api credential</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_CREDENTIAL" value="<?php echo $SOKOJUNG_API_CREDENTIAL; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสคลัง SOKOJUNG</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="sokojung-warehouse" name="SOKOJUNG_WAREHOUSE" value="<?php echo $SOKOJUNG_WAREHOUSE; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสโซน SOKOJUNG</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="sokojung-zone" name="SOKOJUNG_ZONE" value="<?php echo $SOKOJUNG_ZONE; ?>" />
      </div>
      <div class="divider"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">SYNC API STOCK</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SYNC_SOKOJUNG_STOCK" type="checkbox" value="1" <?php echo is_checked($SYNC_SOKOJUNG_STOCK , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SYNC_SOKOJUNG_STOCK" id="sync-sokojung-stock" value="<?php echo $SYNC_SOKOJUNG_STOCK; ?>" />
				<span class="help-block">Sync available stock to sokojung api</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Logs Json</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SOKOJUNG_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($SOKOJUNG_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SOKOJUNG_LOG_JSON" id="sokojung-log-json" value="<?php echo $SOKOJUNG_LOG_JSON; ?>" />
				<span class="help-block">Logs Json text for test</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Test Mode</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SOKOJUNG_TEST" type="checkbox" value="1" <?php echo is_checked($SOKOJUNG_TEST , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SOKOJUNG_TEST" id="sokojung-test" value="<?php echo $SOKOJUNG_TEST; ?>" />
				<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
      </div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('sokojungForm')">SAVE</button>
				<?php endif; ?>
			</div>
			<div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
