	<?php
		$sokojung_api_on = $SOKOJUNG_API == 1 ? 'btn-success' : '';
		$sokojung_api_off = $SOKOJUNG_API == 0 ? 'btn-danger' : '';
		$stock_on = $SYNC_SOKOJUNG_STOCK == 1 ? 'btn-success' : '';
		$stock_off = $SYNC_SOKOJUNG_STOCK == 0 ? 'btn-danger' : '';
		$log_on = $SOKOJUNG_LOG_JSON == 1 ? 'btn-success' : '';
		$log_off = $SOKOJUNG_LOG_JSON == 0 ? 'btn-danger' : '';
		$test_on = $SOKOJUNG_TEST == 1 ? 'btn-success' : '';
		$test_off = $SOKOJUNG_TEST == 0 ? 'btn-primary' : '';
	 ?>
	<form id="sokojungForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-sm-4">
        <span class="form-control left-label">Sokojung API</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $sokojung_api_on; ?>" style="width:50%;" id="btn-sokojung-api-on" onClick="toggleSokojungApi(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $sokojung_api_off; ?>" style="width:50%;" id="btn-sokojung-api-off" onClick="toggleSokojungApi(0)">OFF</button>
				</div>
				<input type="hidden" name="SOKOJUNG_API" id="sokojung-api" value="<?php echo $SOKOJUNG_API; ?>" />
				<span class="help-block">Turn API On/Off</span>
      </div>
      <div class="divider-hidden"></div>

    	<div class="col-sm-4">
        <span class="form-control left-label">Sokojung api endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_HOST"  value="<?php echo $SOKOJUNG_API_HOST; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Sokojung api user name</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_USER_NAME" value="<?php echo $SOKOJUNG_API_USER_NAME; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Sokojung api password</span>
      </div>
      <div class="col-sm-8">
        <input type="password" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_PASSWORD" value="<?php echo $SOKOJUNG_API_PASSWORD; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Sokojung api credential</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="SOKOJUNG_API_CREDENTIAL" value="<?php echo $SOKOJUNG_API_CREDENTIAL; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">รหัสคลัง SOKOJUNG</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="sokojung-warehouse" name="SOKOJUNG_WAREHOUSE" value="<?php echo $SOKOJUNG_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสโซน SOKOJUNG</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="sokojung-zone" name="SOKOJUNG_ZONE" value="<?php echo $SOKOJUNG_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>


			<div class="col-sm-4">
        <span class="form-control left-label">SYNC API STOCK</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $stock_on; ?>" style="width:50%;" id="btn-soko-stock-on" onClick="toggleSokojungSyncStock(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $stock_off; ?>" style="width:50%;" id="btn-soko-stock-off" onClick="toggleSokojungSyncStock(0)">OFF</button>
				</div>
				<input type="hidden" name="SYNC_SOKOJUNG_STOCK" id="sync-sokojung-stock" value="<?php echo $SYNC_SOKOJUNG_STOCK; ?>" />
				<span class="help-block">Sync available stock to sokojung api</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Logs Json</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $log_on; ?>" style="width:50%;" id="btn-soko-log-on" onClick="toggleSokojungLogJson(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $log_off; ?>" style="width:50%;" id="btn-soko-log-off" onClick="toggleSokojungLogJson(0)">OFF</button>
				</div>
				<input type="hidden" name="SOKOJUNG_LOG_JSON" id="sokojung-log-json" value="<?php echo $SOKOJUNG_LOG_JSON; ?>" />
				<span class="help-block">Logs Json text for test</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Test Mode</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm width-50 <?php echo $test_on; ?>" id="btn-soko-test-on" onClick="toggleSokojungTest(1)">ON</button>
					<button type="button" class="btn btn-sm width-50 <?php echo $test_off; ?>" id="btn-soko-test-off" onClick="toggleSokojungTest(0)">OFF</button>
				</div>
				<input type="hidden" name="SOKOJUNG_TEST" id="sokojung-test" value="<?php echo $SOKOJUNG_TEST; ?>" />
				<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sokojungForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
