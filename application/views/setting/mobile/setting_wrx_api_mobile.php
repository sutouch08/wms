<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">WRX API Setting</span>
</div>
<form id="wrxForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">WRX API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API" type="checkbox" value="1" <?php echo is_checked($WRX_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_API" id="wrx-api" value="<?php echo $WRX_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">API Endpoint</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="WRX_API_HOST"  value="<?php echo $WRX_API_HOST; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">API Credential</div>
		<div class="col-xs-12 padding-top-5">
			<textarea class="width-100" rows="6" name="WRX_API_CREDENTIAL"><?php echo $WRX_API_CREDENTIAL; ?></textarea>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Shopee API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_SHOPEE_API" type="checkbox" value="1" <?php echo is_checked($WRX_SHOPEE_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_SHOPEE_API" value="<?php echo $WRX_SHOPEE_API; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">Shopee Shop ID</div>
		<div class="col-xs-12 padding-top-5">
			<select class="width-100" name="WRX_SHOPEE_SHOP_ID">
				<option value="">กรุณาเลือก</option>
				<?php echo select_shop_name($WRX_SHOPEE_SHOP_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Tiktok API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_TIKTOK_API" type="checkbox" value="1" <?php echo is_checked($WRX_TIKTOK_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_TIKTOK_API" value="<?php echo $WRX_TIKTOK_API; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">Tiktok Shop ID</div>
		<div class="col-xs-12 padding-top-5">
			<select class="width-100" name="WRX_TIKTOK_SHOP_ID">
				<option value="">กรุณาเลือก</option>
				<?php echo select_shop_name($WRX_TIKTOK_SHOP_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Lazada API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LAZADA_API" type="checkbox" value="1" <?php echo is_checked($WRX_LAZADA_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_LAZADA_API" value="<?php echo $WRX_LAZADA_API; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">Lazada Shop ID</div>
		<div class="col-xs-12 padding-top-5">
			<select class="width-100" name="WRX_LAZADA_SHOP_ID">
				<option value="">กรุณาเลือก</option>
				<?php echo select_shop_name($WRX_LAZADA_SHOP_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Logs Json</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($WRX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_LOG_JSON" id="wrx-log-json" value="<?php echo $WRX_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Test Mode</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API_TEST" type="checkbox" value="1" <?php echo is_checked($WRX_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_API_TEST" id="wrx-api-test" value="<?php echo $WRX_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('wrxForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
