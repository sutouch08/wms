	<form id="posForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">POS API</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="POS_API" type="checkbox" value="1" <?php echo is_checked($POS_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="POS_API" id="pos-api" value="<?php echo $POS_API; ?>" />
				<span class="help-block">Turn POS Api On/Off</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">POS API WM Create Status</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<select class="form-control input-sm input-medium" name="POS_API_WM_CREATE_STATUS">
					<option value="0" <?php echo is_selected('0', $POS_API_WM_CREATE_STATUS); ?>>Pending</option>
					<option value="1" <?php echo is_selected('1', $POS_API_WM_CREATE_STATUS); ?>>Saved</option>
				</select>
				<span class="help-block">กำหนดสถานะเอกสาร WM เมื่อสร้างเอกสารบน IX สำเร็จ</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">POS API SM Create Status</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<select class="form-control input-sm input-medium" name="POS_API_CN_CREATE_STATUS">
					<option value="0" <?php echo is_selected('0', $POS_API_CN_CREATE_STATUS); ?>>Pending</option>
					<option value="1" <?php echo is_selected('1', $POS_API_CN_CREATE_STATUS); ?>>Saved</option>
				</select>
				<span class="help-block">กำหนดสถานะเอกสาร SM เมื่อสร้างเอกสารบน IX สำเร็จ</span>
      </div>
      <div class="divider"></div>		

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">POS API WW</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="POS_API_WW" type="checkbox" value="1" <?php echo is_checked($POS_API_WW , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="POS_API_WW" id="pos-api-ww" value="<?php echo $POS_API_WW; ?>" />
				<span class="help-block">Turn POS Api WW On/Off</span>
      </div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('posForm')">SAVE</button>
				<?php endif; ?>
			</div>
			<div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
