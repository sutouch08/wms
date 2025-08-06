	<form id="inventoryForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">สต็อกติดลบได้</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_UNDER_ZERO" type="checkbox" value="1" <?php echo is_checked($ALLOW_UNDER_ZERO , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="ALLOW_UNDER_ZERO" id="allow-under-zero" value="<?php echo $ALLOW_UNDER_ZERO; ?>" />
				<span class="help-block">อนุญาติให้สต็อกติดลบได้</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รับสินค้าเกินใบสั่งซื้อ</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_RECEIVE_OVER_PO" type="checkbox" value="1" <?php echo is_checked($ALLOW_RECEIVE_OVER_PO , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="ALLOW_RECEIVE_OVER_PO" id="allow-receive-over-po" value="<?php echo $ALLOW_RECEIVE_OVER_PO; ?>" />
				<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อหรือไม่</span>
      </div>
      <div class="divider"></div>


    	<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รับสินค้าเกินไปสั่งซื้อ(%)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small text-center" name="RECEIVE_OVER_PO"  value="<?php echo $RECEIVE_OVER_PO; ?>" />
      </div>
      <div class="divider"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">การรับสินค้าเกิน Due</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="RECEIVE_OVER_DUE" type="checkbox" value="1" <?php echo is_checked($RECEIVE_OVER_DUE , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
      	<input type="hidden" name="RECEIVE_OVER_DUE" id="receive-over-due" value="<?php echo $RECEIVE_OVER_DUE; ?>" />
				<span class="help-block">รับหรือไม่รับสินค้าจากใบสั่งซื้อที่เกิน Due date ในใบสั่งซื้อ</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">เกินกำหนดรับได้(วัน)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small text-center" name="PO_VALID_DAYS"  value="<?php echo $PO_VALID_DAYS; ?>" />
				<span class="help-block">รับสินค้าเกิน Due date ในใบสั่งซื้อได้ไม่เกินจำนวนวันที่กำหนด เช่น กำหนด 30 วัน กำหนดรับวันที่ 30/09 จะรับสินค้าได้ไม่เกินวันที่ 30/10</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">คลังซื้อ-ขาย เริ่มต้น</span>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-8">
				<select class="width-100" id="default-warehouse" name="DEFAULT_WAREHOUSE" onchange="defaultZoneInit()" required>
					<option value="">เลือกคลัง</option>
					<?php echo select_sell_warehouse($DEFAULT_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสคลังสินค้าระหว่างทำ</span>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-8">
				<select class="width-100" id="transform-warehouse" name="TRANSFORM_WAREHOUSE" required>
					<option value="">เลือกคลัง</option>
					<?php echo select_transform_warehouse($TRANSFORM_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">รหัสคลังยืมสินค้า</span>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-8">
				<select class="width-100" id="lend-warehouse" name="LEND_WAREHOUSE" required>
					<option value="">เลือกคลัง</option>
					<?php echo select_lend_warehouse($LEND_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">คุมสต็อกฝากขายแท้</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="LIMIT_CONSIGN" type="checkbox" value="1" <?php echo is_checked($LIMIT_CONSIGN , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="LIMIT_CONSIGN" id="limit-consign" value="<?php echo $LIMIT_CONSIGN; ?>" />
				<span class="help-block">ควมคุมมูลค่า(ทุน)สินค้าคงเหลือในคลังฝากขายแท้ไม่ให้เกินกว่าที่กำหนด</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">คุมสต็อกฝากขายแท้เทียม</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="LIMIT_CONSIGNMENT" type="checkbox" value="1" <?php echo is_checked($LIMIT_CONSIGNMENT , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="LIMIT_CONSIGNMENT" id="limit-consignment" value="<?php echo $LIMIT_CONSIGNMENT; ?>" />
				<span class="help-block">ควมคุมมูลค่า(ทุน)สินค้าคงเหลือในคลังฝากขายแท้ไม่ให้เกินกว่าที่กำหนด</span>
      </div>
      <div class="divider"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">SYSTEM BIN LOCATION</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="SYSTEM_BIN_LOCATION" type="checkbox" value="1" <?php echo is_checked($SYSTEM_BIN_LOCATION , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="SYSTEM_BIN_LOCATION" id="system-bin-location" value="<?php echo $SYSTEM_BIN_LOCATION; ?>" />
				<span class="help-block">ใช้งาน SYSTEM_BIN_LOCATION หรือไม่</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">ต้องอนุมัติก่อนโอนสินค้าทุกครั้ง</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="STRICT_TRANSFER" type="checkbox" value="1" <?php echo is_checked($STRICT_TRANSFER , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <span class="help-block"></span>
        <input type="hidden" name="STRICT_TRANSFER" id="strict-transfer" value="<?php echo $STRICT_TRANSFER; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">เอกสารกลุ่มโอนคลังหมดอายุทุกสิ้นเดือน</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="TRANSFER_EXPIRE_EOM" type="checkbox" value="1" <?php echo is_checked($TRANSFER_EXPIRE_EOM , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="TRANSFER_EXPIRE_EOM" id="transfer-eom" value="<?php echo $TRANSFER_EXPIRE_EOM; ?>" />
				<span class="help-block">กำหนดให้เอกสารหมดอายุทุกๆ สิ้นเดือนหรือไม่</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">อายุเอกสารกลุ่มโอนคลัง(วัน)</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="number" class="form-control input-sm input-small text-center" name="TRANSFER_EXPIRATION"  value="<?php echo $TRANSFER_EXPIRATION; ?>" />
				<span class="help-block">เอกสารจะหมดอายุภายในจำนวนวันที่กำหนด กำหนดเป็น 0 หากไม่ต้องการใช้งาน</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">สต็อกขั้นต่ำในโซน Fast move</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="number" class="form-control input-sm input-small text-center" name="MIN_STOCK"  value="<?php echo $MIN_STOCK; ?>" />
				<span class="help-block">กำหนดจำนวนขั้นต่ำในโซน fast move หากจำนวนคงเหลือในโซนเหลือด่ำกว่าที่กำหนดจะแสดงผลในรายงาน</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">Default package</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<select class="form-control input-xlarge" name="DEFAULT_PACKAGE" id="default-package">
					<?php echo select_active_package($DEFAULT_PACKAGE); ?>
        </select>
				<span class="help-block">กำหนดขนาด package เริ่มต้นสำหรับการแพ็คสินค้า</span>
      </div>
      <div class="divider-hidden"></div>


			<div class="col-lg-4 col-md-4 col-sm-4">
        <span class="form-control left-label">นำเข้ารายการโอนสินค้า</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_IMPORT_TRANSFER" type="checkbox" value="1" <?php echo is_checked($ALLOW_IMPORT_TRANSFER , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
        <input type="hidden" name="ALLOW_IMPORT_TRANSFER" id="transfer-imp" value="<?php echo $ALLOW_IMPORT_TRANSFER; ?>" />
				<span class="help-block">เปิด/ปิด การอนุญาติให้ นำเข้ารายการโอนสินค้าเข้าเอกสารโอนสินค้าระหว่างคลัง</span>
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">การ Import SM ด้วยไฟล์ Excel</span></div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
  				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_IMPORT_RETURN" type="checkbox" value="1" <?php echo is_checked($ALLOW_IMPORT_RETURN , '1'); ?> onchange="toggleOption($(this))"/>
  				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
  			</label>
				<input type="hidden" name="ALLOW_IMPORT_RETURN" id="allow-import-sm" value="<?php echo $ALLOW_IMPORT_RETURN; ?>" />
				<span class="help-block">กรณีปิดจะไม่สามารถ Import SM ด้วยไฟล์ Excel ได้</span>
			</div>
			<div class="divider"></div>


      <div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
        <button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('inventoryForm')">SAVE</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>
  	</div><!--/ row -->
  </form>
