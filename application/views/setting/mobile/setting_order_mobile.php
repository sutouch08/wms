<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Orders setting</span>
</div>
<form id="orderForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">อายุของออเดอร์ ( วัน )</div>
    <div class="col-xs-4">
      <input type="text" class="width-100 text-center" name="ORDER_EXPIRATION" required value="<?php echo $ORDER_EXPIRATION; ?>" />
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดวันหมดอายุของออเดอร์ หากออเดอร์อยู่ในสถานะ รอการชำระเงิน, รอจัดสินค้า หรือ ไม่บันทึก เกินกว่าจำนวนวันที่กำหนด</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-7 padding-top-5">ประเภทของออเดอร์ที่หมดอายุ</div>
    <div class="col-xs-5">
      <input type="text" class="width-100" name="ORDER_EXPIRATION_ROLE" required value="<?php echo $ORDER_EXPIRATION_ROLE; ?>" />
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">
				กำหนดประเภทของออเดอร์ที่ทำให้หมดอายุ <br>
				S = ขาย, C = ฝากขายเทียม, N = ฝากขายแท้, P = สปอนเซอร์, U = อภินันท์, L = ยืม, T = แปรสภาพ(ขาย), Q = แปรสภาพ(สต็อก)
			</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การจำกัดการแสดงผลสต็อก</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="STOCK_FILTER" required value="<?php echo $STOCK_FILTER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดจำนวนสินค้าคงเหลือสูงสุดที่จะแสดงใหเห็น ถ้าไม่ต้องการใช้กำหนดเป็น 0 </span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">แสดงยอดรวมสต็อก</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="SHOW_SUM_STOCK" type="checkbox" value="1" <?php echo is_checked($SHOW_SUM_STOCK , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="SHOW_SUM_STOCK" id="show-sum-stock" value="<?php echo $SHOW_SUM_STOCK; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">แสดงยอดรวมสินค้าคงเหลือในหน้าออเดอร์หรือไม่ (หากเปิดไว้ระบบจะทำงานช้าลง)</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">วันที่ในการบันทึกขายตัดสต็อก</div>
    <div class="col-xs-4">
      <div class="btn-group width-100">
        <select class="width-100" name="ORDER_SOLD_DATE">
					<option value="B" <?php echo is_selected("B", $ORDER_SOLD_DATE); ?>>วันที่เปิดบิล</option>
					<option value="D" <?php echo is_selected("D", $ORDER_SOLD_DATE); ?>>วันที่เอกสาร</option>
				</select>
      </div>
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดประเภทวันที่ที่ใช้ในการบันทึกขายและตัดสต็อกในระบบ SAP</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รหัสลูกค้าเริ่มต้น</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="DEFAULT_CUSTOMER" required value="<?php echo $DEFAULT_CUSTOMER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ลูกค้าเริ่มต้นหากไม่มีการกำหนดรหัสลูกค้า</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ควบคุมเครดิต</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="CONTROL_CREDIT" type="checkbox" value="1" <?php echo is_checked($CONTROL_CREDIT , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="CONTROL_CREDIT" id="control-credit" value="<?php echo $CONTROL_CREDIT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ใช้การควบคุมเครดิตหรือไม่ หากควบคุมจะไม่สามารถเปิดออเดอร์ได้ถ้าเครดิตคงเหลือไม่เพียงพอ</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">วันเพิ่มในการคุมเครดิต</div>
		<div class="col-xs-4">
			<input type="number" class="width-100 text-center" name="OVER_DUE_DATE" required value="<?php echo $OVER_DUE_DATE; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">จำนวนวันเพิ่มจากวันครบกำหนดชำระ เช่น เครดติ 30 วัน เพิ่มอีก 30 วัน</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Strict Overdue</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="STRICT_OVER_DUE" type="checkbox" value="1" <?php echo is_checked($STRICT_OVER_DUE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="STRICT_OVER_DUE" id="strict-over-due" value="<?php echo $STRICT_OVER_DUE; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">ไม่อนุญาติให้ขายสินค้าให้ลูกค้าที่มียอดค้างชำระเกินวันที่กำหนดในการคุมเครดิต</span>
		</div>
    <div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การแก้ไขส่วนลดในออเดอร์</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_EDIT_DISCOUNT" type="checkbox" value="1" <?php echo is_checked($ALLOW_EDIT_DISCOUNT , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_EDIT_DISCOUNT" id="allow-edit-discount" value="<?php echo $ALLOW_EDIT_DISCOUNT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขส่วนลดในออเดอร์ได้ ส่วนลดจะถูกคำนวณโดยระบบเท่านั้น</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การแก้ไขราคาในออเดอร์</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_EDIT_PRICE" type="checkbox" value="1" <?php echo is_checked($ALLOW_EDIT_PRICE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_EDIT_PRICE" id="allow-edit-price" value="<?php echo $ALLOW_EDIT_PRICE; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขราคาขายสินค้าในออเดอร์ได้ จะใช้ราคาขายในระบบเท่านั้น</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การ Import Order ด้วยไฟล์ Excel</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_UPLOAD_ORDER" type="checkbox" value="1" <?php echo is_checked($ALLOW_UPLOAD_ORDER , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_UPLOAD_ORDER" id="allow-upload-order" value="<?php echo $ALLOW_UPLOAD_ORDER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กรณีปิดจะไม่สามารถ Import Order ด้วยไฟล์ Excel ได้</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การ Import WC ด้วยไฟล์ Excel</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_IMPORT_WC" type="checkbox" value="1" <?php echo is_checked($ALLOW_IMPORT_WC , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_IMPORT_WC" id="allow-import-wc" value="<?php echo $ALLOW_IMPORT_WC; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กรณีปิดจะไม่สามารถ Import WC ด้วยไฟล์ Excel ได้</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">การ Import WT ด้วยไฟล์ Excel</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_IMPORT_WT" type="checkbox" value="1" <?php echo is_checked($ALLOW_IMPORT_WT , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_IMPORT_WT" id="allow-import-wt" value="<?php echo $ALLOW_IMPORT_WT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กรณีปิดจะไม่สามารถ Import WC ด้วยไฟล์ Excel ได้</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">ช่องทางขายเว็บไซต์</div>
		<div class="col-xs-6 text-right">
			<select class="width-100" name="WEB_SITE_CHANNELS_CODE" id="web-site-channels-code" >
				<?php echo select_channels($WEB_SITE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">เลือกรหัสสำหรับการขายบนเว็บไซต์(ใช้ในการ import order)</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-4 padding-top-5">คลังเว็บไซต์</div>
		<div class="col-xs-8 text-right">
			<select class="width-100" name="WEB_SITE_WAREHOUSE_CODE" >
				<option value="">ทั้งหมด</option>
				<?php echo select_sell_warehouse($WEB_SITE_WAREHOUSE_CODE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">เลือกคลังสำหรับการขายบนเว็บไซต์(ใช้ในการ import order)</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">รหัสลูกค้าเว็บไซต์ COD</div>
		<div class="col-xs-6 text-right">
			<input type="text" class="width-100 text-center" name="CUSTOMER_CODE_COD" required value="<?php echo $CUSTOMER_CODE_COD; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสลูกค้า สำหรับช่องทางการชำระเงินแบบ COD บนเว็บไซต์ (ใช้ในการ import order)</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">รหัสลูกค้าเว็บไซต์ 2C2P</div>
		<div class="col-xs-6 text-right">
			<input type="text" class="width-100 text-center" name="CUSTOMER_CODE_2C2P" required value="<?php echo $CUSTOMER_CODE_2C2P; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสลูกค้า สำหรับช่องทางการชำระเงินแบบ 2C2P บนเว็บไซต์ (ใช้ในการ import order)</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รหัสนำหน้าเลขที่จัดส่ง</div>
		<div class="col-xs-4 text-right">
      <input type="text" class="width-100 text-center" name="PREFIX_SHIPPING_NUMBER" value="<?php echo $PREFIX_SHIPPING_NUMBER; ?>" />
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">รหัสนำหน้าเลขที่จัดส่ง โดยใช้เลขที่ออเดอร์ของ Warrix12 แล้วเติมรหัสนี้นำหน้าและบันทึกเป็นเลขที่จัดส่งทันที ใช้ในการ import ออเดอร์จากเว็บไซต์</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">รหัสรายได้ค่าจัดส่ง</div>
		<div class="col-xs-6 text-right">
      <input type="text" class="width-100 text-center" name="SHIPPING_ITEM_CODE" value="<?php echo $SHIPPING_ITEM_CODE; ?>" />
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">รหัสสินค้ารายได้ค่าจัดส่ง ที่จะเพิ่มเป็นรายการสินค้าให้ในออเดอร์ที่มีการคิดค่าจัดส่ง</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">น้ำหนักเหมารวม(กรัม)</div>
		<div class="col-xs-4 text-right">
      <input type="text" class="width-100 text-center" name="DHL_DEFAULT_WEIGHT" value="<?php echo $DHL_DEFAULT_WEIGHT; ?>" />
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">น้ำหนักเหมารวมในการจัดส่ง 1 แพ็คเกจ(กรัม)</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">อัพโหลดออเดอร์(รายการ)/ครั้ง</div>
		<div class="col-xs-4 text-right">
      <input type="text" class="form-control input-sm input-small text-center" name="IMPORT_ROWS_LIMIT" value="<?php echo $IMPORT_ROWS_LIMIT; ?>" />
    </div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">จำกัดจำนวนรายการที่ออเดอร์ที่สามารถนำเข้าระบบได้ครั้งละไม่เกินรายการที่กำหนด เพื่อไม่ให้ระบบเกิดข้อผิดพลาด</span>
		</div>
		<div class="divider"></div>

		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
    <div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('orderForm')">SAVE</button>
			<?php endif; ?>
		</div>
  </div>
</form>
