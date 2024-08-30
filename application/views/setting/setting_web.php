	<?php
		$web_api_on = $WEB_API == 1 ? 'btn-primary' : '';
		$web_api_off = $WEB_API == 0 ? 'btn-primary' : '';
		$pos_on = $POS_API == 1 ? 'btn-primary' : '';
		$pos_off = $POS_API == 0 ? 'btn-primary' : '';
		$pos_ww_on = $POS_API_WW == 1 ? 'btn-primary' : '';
		$pos_ww_off = $POS_API_WW == 0 ? 'btn-primary' : '';
	 ?>
	<form id="webForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-sm-4">
        <span class="form-control left-label">WEB API</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $web_api_on; ?>" style="width:50%;" id="btn-web-api-on" onClick="toggleWebApi(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $web_api_off; ?>" style="width:50%;" id="btn-web-api-off" onClick="toggleWebApi(0)">OFF</button>
				</div>
				<input type="hidden" name="WEB_API" id="web-api" value="<?php echo $WEB_API; ?>" />
				<span class="help-block">Turn Web API On/Off</span>
      </div>
      <div class="divider-hidden"></div>

    	<div class="col-sm-4">
        <span class="form-control left-label">Web Api Host</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WEB_API_HOST"  value="<?php echo $WEB_API_HOST; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Web Api Token</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WEB_API_TOKEN" value="<?php echo $WEB_API_TOKEN; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Send Tracking Begin</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small text-center" id="web-tracking-date" name="WEB_TRACKING_BEGIN" value="<?php echo $WEB_TRACKING_BEGIN; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Send tracking per round</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small text-center" name="WEB_TRACKING_PER_ROUND" value="<?php echo $WEB_TRACKING_PER_ROUND; ?>" />
				<span class="help-block">ส่ง Tracking รอบละไม่เกิน จำนวนออเดอร์ที่กำหนด</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="divider"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">POS API</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $pos_on; ?>" style="width:50%;" id="btn-pos-api-on" onClick="togglePosApi(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $pos_off; ?>" style="width:50%;" id="btn-pos-api-off" onClick="togglePosApi(0)">OFF</button>
				</div>
				<input type="hidden" name="POS_API" id="pos-api" value="<?php echo $POS_API; ?>" />
				<span class="help-block">Turn POS Api On/Off</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">POS API WM Create Status</span>
      </div>
      <div class="col-sm-8">
				<select class="form-control input-sm input-medium" name="POS_API_WM_CREATE_STATUS">
					<option value="0" <?php echo is_selected('0', $POS_API_WM_CREATE_STATUS); ?>>Pending</option>
					<option value="1" <?php echo is_selected('1', $POS_API_WM_CREATE_STATUS); ?>>Saved</option>
				</select>
				<span class="help-block">กำหนดสถานะเอกสาร WM เมื่อสร้างเอกสารบน IX สำเร็จ</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">POS API SM Create Status</span>
      </div>
      <div class="col-sm-8">
				<select class="form-control input-sm input-medium" name="POS_API_CN_CREATE_STATUS">
					<option value="0" <?php echo is_selected('0', $POS_API_CN_CREATE_STATUS); ?>>Pending</option>
					<option value="1" <?php echo is_selected('1', $POS_API_CN_CREATE_STATUS); ?>>Saved</option>
				</select>
				<span class="help-block">กำหนดสถานะเอกสาร SM เมื่อสร้างเอกสารบน IX สำเร็จ</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="divider"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">POS API WW</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $pos_ww_on; ?>" style="width:50%;" id="btn-pos-api-ww-on" onClick="togglePosApiWW(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $pos_ww_off; ?>" style="width:50%;" id="btn-pos-api-ww-off" onClick="togglePosApiWW(0)">OFF</button>
				</div>
				<input type="hidden" name="POS_API_WW" id="pos-api-ww" value="<?php echo $POS_API_WW; ?>" />
				<span class="help-block">Turn POS Api WW On/Off</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('webForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
