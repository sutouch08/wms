
<form id="porlorForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">PORLOR API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="PORLOR_API" type="checkbox" value="1" <?php echo is_checked($PORLOR_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="PORLOR_API" id="porlor-api" value="<?php echo $PORLOR_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="PORLOR_API_ENDPOINT"  value="<?php echo $PORLOR_API_ENDPOINT; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Api Sender code</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="PORLOR_CUSTOMER_CODE"  value="<?php echo $PORLOR_CUSTOMER_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Sender full name</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="PORLOR_CUSTOMER_NAME"  value="<?php echo $PORLOR_CUSTOMER_NAME; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Ship vender</span>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<select class="width-100" name="PORLOR_SENDER_ID" id="sender">
				<option value="">Select</option>
				<?php echo select_sender($PORLOR_SENDER_ID); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Logs Json</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="PORLOR_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($PORLOR_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="PORLOR_LOG_JSON" value="<?php echo $PORLOR_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Test Mode</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="PORLOR_API_TEST" type="checkbox" value="1" <?php echo is_checked($PORLOR_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="PORLOR_API_TEST" value="<?php echo $PORLOR_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('porlorForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>
	</div><!--/ row -->
</form>
