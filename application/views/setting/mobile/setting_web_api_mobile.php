<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Magento API Setting</span>
</div>
<form id="webForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">WEB API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WEB_API" type="checkbox" value="1" <?php echo is_checked($WEB_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WEB_API" id="web-api" value="<?php echo $WEB_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Web API Endpoint</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="WEB_API_HOST"  value="<?php echo $WEB_API_HOST; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">API Credential</div>
		<div class="col-xs-12 padding-top-5">
			<textarea class="width-100" rows="6" name="WEB_API_TOKEN"><?php echo $WEB_API_TOKEN; ?></textarea>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Send Tracking Begin</div>
		<div class="col-xs-4 text-right">
			<input type="text" class="width-100 text-center" id="web-tracking-date" name="WEB_TRACKING_BEGIN" value="<?php echo $WEB_TRACKING_BEGIN; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Send Tracking Per round</div>
		<div class="col-xs-4 text-right">
			<input type="text" class="width-100 text-center" name="WEB_TRACKING_PER_ROUND" value="<?php echo $WEB_TRACKING_PER_ROUND; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('webForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
