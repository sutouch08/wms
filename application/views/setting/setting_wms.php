	<form id="wmsForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS API</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="WMS_API" type="checkbox" value="1" <?php echo is_checked($WMS_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="WMS_API" id="wms-api" value="<?php echo $WMS_API; ?>" />
				<span class="help-block">เปิดใช้งาน WMS API หรือไม่ หากปิด ระบบจะไม่ส่งข้อมูลไป WMS</span>
      </div>
      <div class="divider"></div>

    	<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS CUSTOMER CODE</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-medium" name="WMS_CUST_CODE"  value="<?php echo $WMS_CUST_CODE; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS WAREHOUSE CODE</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-medium" name="WMS_WH_NO" value="<?php echo $WMS_WH_NO; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS Inbound endpoint</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_IB_URL" value="<?php echo $WMS_IB_URL; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS Outbound endpoint</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_OB_URL" value="<?php echo $WMS_OB_URL; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS Product Master endpoint</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_PM_URL" value="<?php echo $WMS_PM_URL; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS Cancelation endpoint</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_CN_URL" value="<?php echo $WMS_CN_URL; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS Compare Stock endpoint</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_STOCK_URL" value="<?php echo $WMS_STOCK_URL; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสคลัง WMS</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="wms-warehouse" name="WMS_WAREHOUSE" value="<?php echo $WMS_WAREHOUSE; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสโซน WMS</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="wms-zone" name="WMS_ZONE" value="<?php echo $WMS_ZONE; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS FULL MODE</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="WMS_FULL_MODE" type="checkbox" value="1" <?php echo is_checked($WMS_FULL_MODE , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="WMS_FULL_MODE" id="wms-full-mode" value="<?php echo $WMS_FULL_MODE; ?>" />
				<span class="help-block">หากใช้งาน FULL MODE จะไม่สามารถย้อนสถานะออเดอร์ที่ปล่อยจัดที่ WMS ได้และระบบจัดสินค้าจะไม่แสดงออเดอร์ที่จัดที่ WMS</span>
      </div>
      <div class="divider"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Auto Export Product Master</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="WMS_EXPORT_ITEMS" type="checkbox" value="1" <?php echo is_checked($WMS_EXPORT_ITEMS , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="WMS_EXPORT_ITEMS" id="wms-export-item" value="<?php echo $WMS_EXPORT_ITEMS; ?>" />
				<span class="help-block">เมื่อมีการ เพิ่ม/แก้ไข รหัสสินค้า จะส่งข้อมูลสินค้าไป WMS หรือไม่</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">WMS Fast Export(For test only)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="WMS_INSTANT_EXPORT" type="checkbox" value="1" <?php echo is_checked($WMS_INSTANT_EXPORT , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="WMS_INSTANT_EXPORT" id="wms-instant-export" value="<?php echo $WMS_INSTANT_EXPORT; ?>" />
				<span class="help-block">เปิดใช้ปุ่ม export to wms บนหน้า order list(สำหรับทดสอบระบบ)</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">TEST MODE</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="WMS_TEST" type="checkbox" value="1" <?php echo is_checked($WMS_TEST , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="WMS_TEST" id="wms-test" value="<?php echo $WMS_TEST; ?>" />
				<span class="help-block">เปิด/ปิด การทดสอบระบบ</span>
			</div>
			<div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
				<span class="form-control left-label">Logs XML</span>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="LOG_XML" type="checkbox" value="1" <?php echo is_checked($LOG_XML , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="LOG_XML" id="log-xml" value="<?php echo $LOG_XML; ?>" />
				<span class="help-block">เก็บ XML logs ไว้ตรวจสอบการส่งข้อมูล</span>
			</div>
			<div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('wmsForm')">SAVE</button>
				<?php endif; ?>
			</div>
			<div class="divider-hidden"></div>      

  	</div><!--/ row -->
  </form>
