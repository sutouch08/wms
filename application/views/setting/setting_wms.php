
<div class="tab-pane fade" id="WMS">
	<?php
		$full_on = $WMS_FULL_MODE == 1 ? 'btn-success' : '';
		$full_off = $WMS_FULL_MODE == 0 ? 'btn-danger' : '';

		$item_on = $WMS_EXPORT_ITEMS == 1 ? 'btn-success' : '';
		$item_off = $WMS_EXPORT_ITEMS == 0 ? 'btn-danger' : '';

		$xml_on = $LOG_XML == 1 ? 'btn-success' : '';
		$xml_off = $LOG_XML == 0 ? 'btn-danger' : '';

		$api_on = $WMS_API == 1 ? 'btn-success' : '';
		$api_off = $WMS_API == 0 ? 'btn-danger' : '';

		$ex_on = $WMS_INSTANT_EXPORT == 1 ? 'btn-success' : '';
		$ex_off = $WMS_INSTANT_EXPORT == 0 ? 'btn-danger' : '';
	 ?>
	<form id="wmsForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
    	<div class="col-sm-4">
        <span class="form-control left-label">WMS CUSTOMER CODE</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-medium" name="WMS_CUST_CODE"  value="<?php echo $WMS_CUST_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">WMS WAREHOUSE CODE</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-medium" name="WMS_WH_NO" value="<?php echo $WMS_WH_NO; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">WMS Inbound endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_IB_URL" value="<?php echo $WMS_IB_URL; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">WMS Outbound endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_OB_URL" value="<?php echo $WMS_OB_URL; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">WMS Product Master endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_PM_URL" value="<?php echo $WMS_PM_URL; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">WMS Cancelation endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_CN_URL" value="<?php echo $WMS_CN_URL; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">WMS Compare Stock endpoint</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-xxlarge" name="WMS_STOCK_URL" value="<?php echo $WMS_STOCK_URL; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">รหัสคลัง WMS</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="wms-warehouse" name="WMS_WAREHOUSE" value="<?php echo $WMS_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสโซน WMS</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="wms-zone" name="WMS_ZONE" value="<?php echo $WMS_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">WMS API</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $api_on; ?>" style="width:50%;" id="btn-api-on" onClick="toggleWmsApi(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $api_off; ?>" style="width:50%;" id="btn-api-off" onClick="toggleWmsApi(0)">OFF</button>
				</div>
				<input type="hidden" name="WMS_API" id="wms-api" value="<?php echo $WMS_API; ?>" />
				<span class="help-block">เปิดใช้งาน WMS API หรือไม่ หากปิด ระบบจะไม่ส่งข้อมูลไป WMS</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">WMS FULL MODE</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $full_on; ?>" style="width:50%;" id="btn-full-on" onClick="toggleFullMode(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $full_off; ?>" style="width:50%;" id="btn-full-off" onClick="toggleFullMode(0)">OFF</button>
				</div>
				<input type="hidden" name="WMS_FULL_MODE" id="wms-full-mode" value="<?php echo $WMS_FULL_MODE; ?>" />
				<span class="help-block">หากใช้งาน FULL MODE จะไม่สามารถย้อนสถานะออเดอร์ที่ปล่อยจัดที่ WMS ได้และระบบจัดสินค้าจะไม่แสดงออเดอร์ที่จัดที่ WMS</span>
      </div>
      <div class="divider-hidden"></div>


			<div class="col-sm-4">
        <span class="form-control left-label">Auto Export Product Master</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $item_on; ?>" style="width:50%;" id="btn-item-on" onClick="toggleExportItem(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $item_off; ?>" style="width:50%;" id="btn-item-off" onClick="toggleExportItem(0)">OFF</button>
				</div>
				<input type="hidden" name="WMS_EXPORT_ITEMS" id="wms-export-item" value="<?php echo $WMS_EXPORT_ITEMS; ?>" />
				<span class="help-block">เมื่อมีการ เพิ่ม/แก้ไข รหัสสินค้า จะส่งข้อมูลสินค้าไป WMS หรือไม่</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">WMS Fast Export(For test only)</span>
      </div>
      <div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $ex_on; ?>" style="width:50%;" id="btn-ex-on" onClick="toggleFastExport(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $ex_off; ?>" style="width:50%;" id="btn-ex-off" onClick="toggleFastExport(0)">OFF</button>
				</div>
				<input type="hidden" name="WMS_INSTANT_EXPORT" id="wms-instant-export" value="<?php echo $WMS_INSTANT_EXPORT; ?>" />
				<span class="help-block">เปิดใช้ปุ่ม export to wms บนหน้า order list(สำหรับทดสอบระบบ)</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
				<span class="form-control left-label">Logs XML</span>
			</div>
			<div class="col-sm-8">
				<div class="btn-group">
					<button type="button" class="btn btn-sm <?php echo $xml_on; ?>" style="width:50%;" id="btn-xml-on" onClick="toggleLogXml(1)">ON</button>
					<button type="button" class="btn btn-sm <?php echo $xml_off; ?>" style="width:50%;" id="btn-xml-off" onClick="toggleLogXml(0)">OFF</button>
				</div>
				<input type="hidden" name="LOG_XML" id="log-xml" value="<?php echo $LOG_XML; ?>" />
				<span class="help-block">เก็บ XML logs ไว้ตรวจสอบการส่งข้อมูล</span>
			</div>
			<div class="divider-hidden"></div>

      <div class="col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('wmsForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>
