	<?php
		$ix_api_on = $IX_API == 1 ? 'btn-success' : '';
		$ix_api_off = $IX_API == 0 ? 'btn-danger' : '';
		$stock_on = $SYNC_IX_STOCK == 1 ? 'btn-success' : '';
		$stock_off = $SYNC_IX_STOCK == 0 ? 'btn-danger' : '';
		$log_on = $IX_LOG_JSON == 1 ? 'btn-success' : '';
		$log_off = $IX_LOG_JSON == 0 ? 'btn-danger' : '';
		$test_on = $IX_TEST == 1 ? 'btn-success' : '';
		$test_off = $IX_TEST == 0 ? 'btn-primary' : '';
	 ?>
	<form id="ixForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-sm-4">
        <span class="form-control left-label">Ix API</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $ix_api_on; ?>" style="width:50%;" id="btn-ix-api-on" onClick="toggleIxApi(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $ix_api_off; ?>" style="width:50%;" id="btn-ix-api-off" onClick="toggleIxApi(0)">OFF</button>
				</div>
				<input type="hidden" name="IX_API" id="ix-api" value="<?php echo $IX_API; ?>" />
				<span class="help-block">Turn API On/Off</span>
      </div>
      <div class="divider-hidden"></div>

    	<div class="col-sm-4">
        <span class="form-control left-label">Ix api endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="IX_API_HOST"  value="<?php echo $IX_API_HOST; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Ix api user name</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="IX_API_USER_NAME" value="<?php echo $IX_API_USER_NAME; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Ix api password</span>
      </div>
      <div class="col-sm-8">
        <input type="password" class="form-control input-sm input-xxlarge" name="IX_API_PASSWORD" value="<?php echo $IX_API_PASSWORD; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Ix api credential</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="IX_API_CREDENTIAL" value="<?php echo $IX_API_CREDENTIAL; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">รหัสคลัง IX</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-warehouse" name="IX_WAREHOUSE" value="<?php echo $IX_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสโซน IX</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-zone" name="IX_ZONE" value="<?php echo $IX_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสคลังรับคืน IX</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-return-warehouse" name="IX_RETURN_WAREHOUSE" value="<?php echo $IX_RETURN_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสโซนรับคืน IX</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-return-zone" name="IX_RETURN_ZONE" value="<?php echo $IX_RETURN_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>


			<div class="col-sm-4">
        <span class="form-control left-label">SYNC API STOCK</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $stock_on; ?>" style="width:50%;" id="btn-ix-stock-on" onClick="toggleIxSyncStock(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $stock_off; ?>" style="width:50%;" id="btn-ix-stock-off" onClick="toggleIxSyncStock(0)">OFF</button>
				</div>
				<input type="hidden" name="SYNC_IX_STOCK" id="sync-ix-stock" value="<?php echo $SYNC_IX_STOCK; ?>" />
				<span class="help-block">Sync available stock to ix api</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Logs Json</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $log_on; ?>" style="width:50%;" id="btn-ix-log-on" onClick="toggleIxLogJson(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $log_off; ?>" style="width:50%;" id="btn-ix-log-off" onClick="toggleIxLogJson(0)">OFF</button>
				</div>
				<input type="hidden" name="IX_LOG_JSON" id="ix-log-json" value="<?php echo $IX_LOG_JSON; ?>" />
				<span class="help-block">Logs Json text for test</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Test Mode</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm width-50 <?php echo $test_on; ?>" id="btn-ix-test-on" onClick="toggleIxTest(1)">ON</button>
					<button type="button" class="btn btn-sm width-50 <?php echo $test_off; ?>" id="btn-ix-test-off" onClick="toggleIxTest(0)">OFF</button>
				</div>
				<input type="hidden" name="IX_TEST" id="ix-test" value="<?php echo $IX_TEST; ?>" />
				<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('ixForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
