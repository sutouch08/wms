	<form id="sapForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">SAP API</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SAP_API" type="checkbox" value="1" <?php echo is_checked($SAP_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SAP_API" id="sap-api" value="<?php echo $SAP_API; ?>" />
				<span class="help-block">Turn API On/Off</span>
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">SAP API endpoint</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<input type="text" class="form-control input-sm input-xxlarge" name="SAP_API_HOST"  value="<?php echo $SAP_API_HOST; ?>" />
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">SAP API Username</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<input type="text" class="form-control input-sm input-large" name="SAP_API_USERNAME"  value="<?php echo $SAP_API_USERNAME; ?>" />
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">SAP API Password</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<input type="password" class="form-control input-sm input-large" name="SAP_API_PWD"  value="<?php echo $SAP_API_PWD; ?>" />
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">SAP API credential</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<textarea class="form-control input-sm" id="sap-api-token" rows="4" name="SAP_API_CREDENTIAL"><?php echo $SAP_API_CREDENTIAL; ?></textarea>
				<button type="button" class="btn btn-sm btn-primary btn-100 margin-top-15" id="renewtoken-btn" onclick="renewToken()">Renew</button>
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">Logs Json</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SAP_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($SAP_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SAP_LOG_JSON" id="sap-log-json" value="<?php echo $SAP_LOG_JSON; ?>" />
				<span class="help-block">Logs Json text for test</span>
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">Test Mode</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SAP_API_TEST" type="checkbox" value="1" <?php echo is_checked($SAP_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SAP_API_TEST" id="sap-api-test" value="<?php echo $WRX_API_TEST; ?>" />
				<span class="help-block">เปิดระบบทดสอบหรือไม่ เมื่อเปิดทดสอบจะไม่ทำการ interface จริง</span>
			</div>
			<div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

    	<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Currency (สกุลเงิน)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="CURRENCY"  value="<?php echo $CURRENCY; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Item group code (รหัสกลุ่มสินค้า)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="ITEM_GROUP_CODE" value="<?php echo $ITEM_GROUP_CODE; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Purchase VAT code (รหัสภาษีซื้อ)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_CODE" value="<?php echo $PURCHASE_VAT_CODE; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Purchase VAT rate (อัตราภาษีซื้อ)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sell VAT code (รหัสภาษีขาย)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_CODE" value="<?php echo $SALE_VAT_CODE; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Sell VAT rate (อัตราภาษีขาย)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
      </div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

      <div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sapForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>
  	</div><!--/ row -->
  </form>
