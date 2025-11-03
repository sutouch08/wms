<form id="wrxForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">WRX API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API" type="checkbox" value="1" <?php echo is_checked($WRX_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_API" id="wrx-api" value="<?php echo $WRX_API; ?>" />
			<span class="help-block">Turn API On/Off</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Wrx api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="WRX_API_HOST"  value="<?php echo $WRX_API_HOST; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Wrx api credential</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<textarea class="form-control input-sm" rows="4" name="WRX_API_CREDENTIAL"><?php echo $WRX_API_CREDENTIAL; ?></textarea>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Shopee API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_SHOPEE_API" type="checkbox" value="1" <?php echo is_checked($WRX_SHOPEE_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_SHOPEE_API" value="<?php echo $WRX_SHOPEE_API; ?>"/>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Shopee Shop ID</span>
		</div>
		<div class="col-lg-5 col-md-6 col-sm-8">
			<select class="width-100" name="WRX_SHOPEE_SHOP_ID">
				<option value="">กรุณาเลือก</option>
				<?php echo select_shop_name($WRX_SHOPEE_SHOP_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Titok API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_TIKTOK_API" type="checkbox" value="1" <?php echo is_checked($WRX_TIKTOK_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_TIKTOK_API" value="<?php echo $WRX_TIKTOK_API; ?>"/>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Tiktok Shop ID</span>
		</div>
		<div class="col-lg-5 col-md-6 col-sm-8">
			<select class="width-100" name="WRX_TIKTOK_SHOP_ID">
				<option value="">กรุณาเลือก</option>
				<?php echo select_shop_name($WRX_TIKTOK_SHOP_ID); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Lazada API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LAZADA_API" type="checkbox" value="1" <?php echo is_checked($WRX_LAZADA_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_LAZADA_API" value="<?php echo $WRX_LAZADA_API; ?>"/>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Lazada Shop ID</span>
		</div>
		<div class="col-lg-5 col-md-6 col-sm-8">
			<select class="width-100" name="WRX_LAZADA_SHOP_ID">
				<option value="">กรุณาเลือก</option>
				<?php echo select_shop_name($WRX_LAZADA_SHOP_ID); ?>
			</select>
		</div>
		<div class="divider"></div>


		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Web tracking API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_WEB_TRACKING_API" type="checkbox" value="1" <?php echo is_checked($WRX_WEB_TRACKING_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_WEB_TRACKING_API" value="<?php echo $WRX_WEB_TRACKING_API; ?>" />
			<span class="help-block">Turn Web API On/Off</span>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Send Tracking Begin</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-small text-center" id="web-tracking-date" name="WEB_TRACKING_BEGIN" value="<?php echo $WEB_TRACKING_BEGIN; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Send tracking per round</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-small text-center" name="WEB_TRACKING_PER_ROUND" value="<?php echo $WEB_TRACKING_PER_ROUND; ?>" />
			<span class="help-block">ส่ง Tracking รอบละไม่เกิน จำนวนออเดอร์ที่กำหนด</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Logs Json</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($WRX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_LOG_JSON" id="wrx-log-json" value="<?php echo $WRX_LOG_JSON; ?>" />
			<span class="help-block">Logs Json text for test</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Test Mode</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API_TEST" type="checkbox" value="1" <?php echo is_checked($WRX_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_API_TEST" id="wrx-api-test" value="<?php echo $WRX_API_TEST; ?>" />
			<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('wrxForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
