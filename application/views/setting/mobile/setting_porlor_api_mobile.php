<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">PORLOR API Setting</span>
</div>
<form id="porlorForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">PORLOR API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="PORLOR_API" type="checkbox" value="1" <?php echo is_checked($PORLOR_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="PORLOR_API" value="<?php echo $PORLOR_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">API Endpoint</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="PORLOR_API_ENDPOINT"  value="<?php echo $PORLOR_API_ENDPOINT; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Code</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="PORLOR_CUSTOMER_CODE"  value="<?php echo $PORLOR_CUSTOMER_CODE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Full Name</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="PORLOR_CUSTOMER_NAME"  value="<?php echo $PORLOR_CUSTOMER_NAME; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Phone</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="PORLOR_CUSTOMER_PHONE"  value="<?php echo $PORLOR_CUSTOMER_PHONE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">Package weight</div>
		<div class="col-xs-6">
			<div class="input-group">
				<input type="text" class="width-100" name="PORLOR_DEFAULT_WEIGHT"  value="<?php echo $PORLOR_DEFAULT_WEIGHT; ?>" />
				<span class="input-group-addon">KGS</span>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Ship vender</div>
		<div class="col-xs-12 padding-top-5">
			<select class="width-100" name="PORLOR_SENDER_ID" id="sender">
				<option value="">Select</option>
				<?php echo select_sender($PORLOR_SENDER_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Logs Json</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="PORLOR_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($PORLOR_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="PORLOR_LOG_JSON" value="<?php echo $PORLOR_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Test Mode</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="PORLOR_API_TEST" type="checkbox" value="1" <?php echo is_checked($PORLOR_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="PORLOR_API_TEST" value="<?php echo $PORLOR_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('porlorForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
