<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Inventory setting</span>
</div>

<form id="inventoryForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">สต็อกติดลบได้</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_UNDER_ZERO" type="checkbox" value="1" <?php echo is_checked($ALLOW_UNDER_ZERO , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="ALLOW_UNDER_ZERO" id="allow-under-zero" value="<?php echo $ALLOW_UNDER_ZERO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">อนุญาติให้สต็อกติดลบได้</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รับสินค้าเกินใบสั่งซื้อ</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_RECEIVE_OVER_PO" type="checkbox" value="1" <?php echo is_checked($ALLOW_RECEIVE_OVER_PO , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="ALLOW_RECEIVE_OVER_PO" id="allow-receive-over-po" value="<?php echo $ALLOW_RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อหรือไม่</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รับสินค้าเกินไปสั่งซื้อ(%)</div>
		<div class="col-xs-4 text-right">
			<input type="text" class="width-100 text-center" name="RECEIVE_OVER_PO"  value="<?php echo $RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อได้กี่ %</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การรับสินค้าเกิน Due</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="RECEIVE_OVER_DUE" type="checkbox" value="1" <?php echo is_checked($RECEIVE_OVER_DUE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="RECEIVE_OVER_DUE" id="receive-over-due" value="<?php echo $RECEIVE_OVER_DUE; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">รับสินค้าจากใบสั่งซื้อที่เกิน Due date ในใบสั่งซื้อหรือไม่</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">เกินกำหนดรับได้(วัน)</div>
		<div class="col-xs-4 text-right">
			<input type="text" class="width-100 text-center" name="PO_VALID_DAYS"  value="<?php echo $PO_VALID_DAYS; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">รับสินค้าเกิน Due date ในใบสั่งซื้อได้ไม่เกินจำนวนวันที่กำหนด เช่น กำหนด 30 วัน กำหนดรับวันที่ 30/09 จะรับสินค้าได้ไม่เกินวันที่ 30/10</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">คลังซื้อ-ขาย เริ่มต้น</div>
		<div class="col-xs-12">
			<select class="width-100" id="default-warehouse" name="DEFAULT_WAREHOUSE" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($DEFAULT_WAREHOUSE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดคลังซื้อ - ขาย เริ่มต้น กรณีไม่ระบุคลังจะใช้คลังนี้เป็นคลังหลัก</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">รหัสคลังสินค้าระหว่างทำ</div>
		<div class="col-xs-12">
			<select class="width-100" id="transform-warehouse" name="TRANSFORM_WAREHOUSE" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_transform_warehouse($TRANSFORM_WAREHOUSE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดคลังระหว่างทำเริ่มต้น กรณีไม่ระบุคลังจะใช้คลังนี้เป็นคลังระหว่างทำ</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">รหัสคลังยืมสินค้า</div>
		<div class="col-xs-12">
			<select class="width-100" id="lend-warehouse" name="LEND_WAREHOUSE" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_lend_warehouse($LEND_WAREHOUSE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดคลังยิมสินค้าเริ่มต้น กรณีไม่ระบุคลังจะใช้คลังนี้เป็นคลังยืม</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">คุมสต็อกฝากขายแท้</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="LIMIT_CONSIGN" type="checkbox" value="1" <?php echo is_checked($LIMIT_CONSIGN , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="LIMIT_CONSIGN" id="limit-consign" value="<?php echo $LIMIT_CONSIGN; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ควมคุมมูลค่า(ทุน)สินค้าคงเหลือในคลังฝากขายแท้ไม่ให้เกินกว่าที่กำหนด</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">คุมสต็อกฝากขายแท้เทียม</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="LIMIT_CONSIGNMENT" type="checkbox" value="1" <?php echo is_checked($LIMIT_CONSIGNMENT , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="LIMIT_CONSIGNMENT" id="limit-consignment" value="<?php echo $LIMIT_CONSIGNMENT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ควมคุมมูลค่า(ทุน)สินค้าคงเหลือในคลังฝากขายแท้ไม่ให้เกินกว่าที่กำหนด</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">SYSTEM BIN LOCATION</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SYSTEM_BIN_LOCATION" type="checkbox" value="1" <?php echo is_checked($SYSTEM_BIN_LOCATION , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="SYSTEM_BIN_LOCATION" id="system-bin-location" value="<?php echo $SYSTEM_BIN_LOCATION; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ใช้งาน SYSTEM_BIN_LOCATION หรือไม่</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ต้องอนุมัติก่อนโอนสินค้าทุกครั้ง</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="STRICT_TRANSFER" type="checkbox" value="1" <?php echo is_checked($STRICT_TRANSFER , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="STRICT_TRANSFER" id="strict-transfer" value="<?php echo $STRICT_TRANSFER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ใช้งาน SYSTEM_BIN_LOCATION หรือไม่</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">เอกสารกลุ่มโอนคลังหมดอายุทุกสิ้นเดือน</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="TRANSFER_EXPIRE_EOM" type="checkbox" value="1" <?php echo is_checked($TRANSFER_EXPIRE_EOM , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="TRANSFER_EXPIRE_EOM" id="transfer-eom" value="<?php echo $TRANSFER_EXPIRE_EOM; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดให้เอกสารหมดอายุทุกๆ สิ้นเดือนหรือไม่</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">อายุเอกสารกลุ่มโอนคลัง(วัน)</div>
		<div class="col-xs-4 text-right">
			<input type="number" class="width-100 text-center" name="TRANSFER_EXPIRATION"  value="<?php echo $TRANSFER_EXPIRATION; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">เอกสารจะหมดอายุภายในจำนวนวันที่กำหนด กำหนดเป็น 0 หากไม่ต้องการใช้งาน</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">
			<span class="form-control left-label">สต็อกขั้นต่ำในโซน Fast move</span>
		</div>
		<div class="col-xs-4 text-right">
			<input type="number" class="width-100 text-center" name="MIN_STOCK"  value="<?php echo $MIN_STOCK; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดจำนวนขั้นต่ำในโซน fast move หากจำนวนคงเหลือในโซนเหลือด่ำกว่าที่กำหนดจะแสดงผลในรายงาน</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Default package</div>
		<div class="col-xs-12">
			<select class="width-100" name="DEFAULT_PACKAGE" id="default-package">
				<?php echo select_active_package($DEFAULT_PACKAGE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดขนาด package เริ่มต้นสำหรับการแพ็คสินค้า</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การ Import WW ด้วยไฟล์ Excel</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_IMPORT_TRANSFER" type="checkbox" value="1" <?php echo is_checked($ALLOW_IMPORT_TRANSFER , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_IMPORT_TRANSFER" id="transfer-imp" value="<?php echo $ALLOW_IMPORT_TRANSFER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">เปิด/ปิด การอนุญาติให้ นำเข้ารายการโอนสินค้าเข้าเอกสารโอนสินค้าระหว่างคลัง</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การ Import SM ด้วยไฟล์ Excel</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_IMPORT_RETURN" type="checkbox" value="1" <?php echo is_checked($ALLOW_IMPORT_RETURN , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_IMPORT_RETURN" id="allow-import-sm" value="<?php echo $ALLOW_IMPORT_RETURN; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กรณีปิดจะไม่สามารถ Import SM ด้วยไฟล์ Excel ได้</span>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('inventoryForm')">SAVE</button>
			<?php endif; ?>
		</div>
	</div><!--/ row -->
</form>
