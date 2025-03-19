<?php
	$wrx_api_on = $WRX_API == 1 ? 'btn-success' : '';
	$wrx_api_off = $WRX_API == 0 ? 'btn-primary' : '';
	$log_on = $WRX_LOG_JSON == 1 ? 'btn-success' : '';
	$log_off = $WRX_LOG_JSON == 0 ? 'btn-primary' : '';
	$test_on = $WRX_API_TEST == 1 ? 'btn-success' : '';
	$test_off = $WRX_API_TEST == 0 ? 'btn-primary' : '';
 ?>
<form id="wrxForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-sm-4">
			<span class="form-control left-label">WRX API</span>
		</div>
		<div class="col-sm-8">
			<div class="btn-group">
				<button type="button" class="btn btn-sm <?php echo $wrx_api_on; ?>" style="width:50%;" id="btn-wrx-api-on" onClick="toggleWrxApi(1)">ON</button>
				<button type="button" class="btn btn-sm <?php echo $wrx_api_off; ?>" style="width:50%;" id="btn-wrx-api-off" onClick="toggleWrxApi(0)">OFF</button>
			</div>
			<input type="hidden" name="WRX_API" id="wrx-api" value="<?php echo $WRX_API; ?>" />
			<span class="help-block">Turn API On/Off</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Wrx api endpoint</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="WRX_API_HOST"  value="<?php echo $WRX_API_HOST; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Wrx api credential</span>
		</div>
		<div class="col-sm-8">
			<textarea class="form-control input-sm" rows="4" name="WRX_API_CREDENTIAL"><?php echo $WRX_API_CREDENTIAL; ?></textarea>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Logs Json</span>
		</div>
		<div class="col-sm-8">
			<div class="btn-group">
				<button type="button" class="btn btn-sm <?php echo $log_on; ?>" style="width:50%;" id="btn-wrx-log-on" onClick="toggleWrxLogJson(1)">ON</button>
				<button type="button" class="btn btn-sm <?php echo $log_off; ?>" style="width:50%;" id="btn-wrx-log-off" onClick="toggleWrxLogJson(0)">OFF</button>
			</div>
			<input type="hidden" name="WRX_LOG_JSON" id="wrx-log-json" value="<?php echo $WRX_LOG_JSON; ?>" />
			<span class="help-block">Logs Json text for test</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Test Mode</span>
		</div>
		<div class="col-sm-8">
			<div class="btn-group">
				<button type="button" class="btn btn-sm width-50 <?php echo $test_on; ?>" id="btn-wrx-test-on" onClick="toggleWrxTest(1)">ON</button>
				<button type="button" class="btn btn-sm width-50 <?php echo $test_off; ?>" id="btn-wrx-test-off" onClick="toggleWrxTest(0)">OFF</button>
			</div>
			<input type="hidden" name="WRX_API_TEST" id="wrx-api-test" value="<?php echo $WRX_API_TEST; ?>" />
			<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-8 col-sm-offset-4">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('wrxForm')">
				<i class="fa fa-save"></i> บันทึก
			</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
