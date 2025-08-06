	<form id="webForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WEB API</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="WEB_API" type="checkbox" value="1" <?php echo is_checked($WEB_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="WEB_API" id="web-api" value="<?php echo $WEB_API; ?>" />
				<span class="help-block">Turn Web API On/Off</span>
      </div>
      <div class="divider"></div>

    	<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Web Api Host</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WEB_API_HOST"  value="<?php echo $WEB_API_HOST; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Web Api Token</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WEB_API_TOKEN" value="<?php echo $WEB_API_TOKEN; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Send Tracking Begin</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small text-center" id="web-tracking-date" name="WEB_TRACKING_BEGIN" value="<?php echo $WEB_TRACKING_BEGIN; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Send tracking per round</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small text-center" name="WEB_TRACKING_PER_ROUND" value="<?php echo $WEB_TRACKING_PER_ROUND; ?>" />
				<span class="help-block">ส่ง Tracking รอบละไม่เกิน จำนวนออเดอร์ที่กำหนด</span>
      </div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('webForm')">SAVE</button>
				<?php endif; ?>
			</div>
			<div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
