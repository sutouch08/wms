<form id="spxForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">SPX API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SPX_API" type="checkbox" value="1" <?php echo is_checked($SPX_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SPX_API" id="spx-api" value="<?php echo $SPX_API; ?>" />
			<span class="help-block">Turn API On/Off</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender Name</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_NAME"  value="<?php echo $SPX_SENDER_NAME; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender Phone</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_PHONE"  value="<?php echo $SPX_SENDER_PHONE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender Address</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_ADDRESS"  value="<?php echo $SPX_SENDER_ADDRESS; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender State</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_STATE"  value="<?php echo $SPX_SENDER_STATE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender City</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_CITY"  value="<?php echo $SPX_SENDER_CITY; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender District</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_DISTRICT"  value="<?php echo $SPX_SENDER_DISTRICT; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender Post Code</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="SPX_SENDER_POST_CODE"  value="<?php echo $SPX_SENDER_POST_CODE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Ship vender</span>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<select class="width-100" name="SPX_ID" id="spx-sender">
				<option value="">Select</option>
				<?php echo select_sender($SPX_ID); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Package weight</span>
		</div>
		<div class="col-lg-2 col-md-3 col-sm-3">
			<div class="input-group">
				<input type="text" class="form-control input-sm input-small" name="SPX_DEFAULT_WEIGHT"  value="<?php echo $SPX_DEFAULT_WEIGHT; ?>" />
				<span class="input-group-addon">KGS</span>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Logs Json</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SPX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($SPX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SPX_LOG_JSON" id="spx-log-json" value="<?php echo $SPX_LOG_JSON; ?>" />
			<span class="help-block">Logs Json text for test</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Test Mode</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SPX_API_TEST" type="checkbox" value="1" <?php echo is_checked($SPX_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SPX_API_TEST" id="spx-api-test" value="<?php echo $WRX_API_TEST; ?>" />
			<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('spxForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
	<script>
		$('#spx-sender').select2();
	</script>
</form>
