<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">SPX API Setting</span>
</div>
<form id="spxForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">SPX API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SPX_API" type="checkbox" value="1" <?php echo is_checked($SPX_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SPX_API" value="<?php echo $SPX_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Name</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="SPX_SENDER_NAME"  value="<?php echo $SPX_SENDER_NAME; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Phone</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="SPX_SENDER_PHONE"  value="<?php echo $SPX_SENDER_PHONE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Address</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="SPX_SENDER_ADDRESS"  value="<?php echo $SPX_SENDER_ADDRESS; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender State</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="SPX_SENDER_STATE"  value="<?php echo $SPX_SENDER_STATE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender City</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="SPX_SENDER_CITY"  value="<?php echo $SPX_SENDER_CITY; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Sender Post Code</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="SPX_SENDER_POST_CODE"  value="<?php echo $SPX_SENDER_POST_CODE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">Package weight</div>
		<div class="col-xs-6">
			<div class="input-group">
				<input type="text" class="width-100" name="SPX_DEFAULT_WEIGHT"  value="<?php echo $SPX_DEFAULT_WEIGHT; ?>" />
				<span class="input-group-addon">KGS</span>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Ship vender</div>
		<div class="col-xs-12 padding-top-5">
			<select class="width-100" name="SPX_ID" id="spx-sender">
				<option value="">Select</option>
				<?php echo select_sender($SPX_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Logs Json</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SPX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($SPX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SPX_LOG_JSON" value="<?php echo $SPX_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Test Mode</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SPX_API_TEST" type="checkbox" value="1" <?php echo is_checked($SPX_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SPX_API_TEST" value="<?php echo $SPX_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('spxForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>
	</div><!--/ row -->
</form>
<script>
	$('#spx-sender').select2();
</script>
