<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">POS API Setting</span>
</div>
<form id="posForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">POS API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="POS_API" type="checkbox" value="1" <?php echo is_checked($POS_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="POS_API" value="<?php echo $POS_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">POS API WM Create Status</div>
		<div class="col-xs-4">
			<select class="width-100" name="POS_API_WM_CREATE_STATUS">
				<option value="0" <?php echo is_selected('0', $POS_API_WM_CREATE_STATUS); ?>>Pending</option>
				<option value="1" <?php echo is_selected('1', $POS_API_WM_CREATE_STATUS); ?>>Saved</option>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดสถานะเอกสาร WM เมื่อสร้างเอกสารบน IX สำเร็จ</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">POS API WM Create Status</div>
		<div class="col-xs-4">
			<select class="width-100" name="POS_API_CN_CREATE_STATUS">
				<option value="0" <?php echo is_selected('0', $POS_API_CN_CREATE_STATUS); ?>>Pending</option>
				<option value="1" <?php echo is_selected('1', $POS_API_CN_CREATE_STATUS); ?>>Saved</option>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดสถานะเอกสาร SM เมื่อสร้างเอกสารบน IX สำเร็จ</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">POS API WW</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="POS_API_WW" type="checkbox" value="1" <?php echo is_checked($POS_API_WW , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="POS_API_WW" value="<?php echo $POS_API_WW; ?>" />
		</div>
		<div class="divider"></div>

		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('posForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
