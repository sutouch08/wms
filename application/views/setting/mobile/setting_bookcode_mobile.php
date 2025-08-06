<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Book code setting</span>
</div>
<form id="bookcodeForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">ขายสินค้า</div>
		<div class="col-xs-4 text-right">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_ORDER" value="<?php echo $BOOK_CODE_ORDER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบสั่งขาย"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">เบิกอภินันท์</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_SUPPORT" value="<?php echo $BOOK_CODE_SUPPORT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบเบิกอภินันท์"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">เบิกสปอนเซอร์</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_SPONSOR" value="<?php echo $BOOK_CODE_SPONSOR; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบเบิกสปอนเซอร์"</span>
		</div>
		<div class="divider"></div>


		<div class="col-xs-8 padding-top-5">รับสินค้าจากการซื้อ</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_BI" value="<?php echo $BOOK_CODE_RECEIVE_PO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบรับสินค้า"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">โอนสินค้าระหว่างคลัง</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_TRANSFER" value="<?php echo $BOOK_CODE_TRANSFER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้าคลัง"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ยืมสินค้า</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_LEND" value="<?php echo $BOOK_CODE_LEND; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบยืมสินค้า"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ฝากขาย(แท้)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_CONSIGN_TR" value="<?php echo $BOOK_CODE_CONSIGN_TR; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้าฝากขายแท้"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ฝากขาย(เทียม)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_CONSIGN_SO" value="<?php echo $BOOK_CODE_CONSIGN_SO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้าฝากขายเทียม"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ตัดยอดฝากขาย(Shop)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_CONSIGN_SOLD" value="<?php echo $BOOK_CODE_CONSIGN_SOLD; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ตัดยอดฝากขาย(Shop)"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ตัดยอดฝากขาย(ห้าง)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_CONSIGNMENT_SOLD" value="<?php echo $BOOK_CODE_CONSIGNMENT_SOLD; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ตัดยอดฝากขาย(ห้าง)"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">เบิกแปรสภาพ(เพื่อขาย)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_TRANSFORM" value="<?php echo $BOOK_CODE_TRANSFORM; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบเบิกแปรสภาพ"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">เบิกแปรสภาพ(เพื่อสต็อก)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_TRANSFORM_STOCK" value="<?php echo $BOOK_CODE_TRANSFORM_STOCK; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบเบิกแปรสภาพ"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รับสินค้าจากการแปรสภาพ</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_RECEIVE_TRANSFORM" value="<?php echo $BOOK_CODE_RECEIVE_TRANSFORM; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบรับสินค้าจากการแปรสภาพ"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ตัดยอดแปรสภาพ</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_ADJUST_TRANSFORM" value="<?php echo $BOOK_CODE_ADJUST_TRANSFORM; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบตัดยอดสินค้าแปรสภาพ"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ลดหนี้ขาย</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_RETURN_ORDER" value="<?php echo $BOOK_CODE_RETURN_ORDER; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบลดหนี้ขาย"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ลดหนี้ฝากขายเทียม</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_RETURN_CONSIGNMENT" value="<?php echo $BOOK_CODE_RETURN_CONSIGNMENT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบลดหนี้ฝากขายเทียม"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">คืนสินค้า(จากการยืม)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_RETURN_LEND" value="<?php echo $BOOK_CODE_RETURN_LEND; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบคืนสินค้าจากการยืม"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ปรับยอดสต็อก</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_ADJUST" value="<?php echo $BOOK_CODE_ADJUST; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ปรับยอดสต็อก"</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">ปรับยอดสต็อก(ฝากขายเทียม)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 bookcode text-center" name="BOOK_CODE_ADJUST_CONSIGNMENT" value="<?php echo $BOOK_CODE_ADJUST_CONSIGNMENT; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดรหัสเล่มเอกสาร "ปรับยอดสต็อกฝากขายเทียม"</span>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>


		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('bookcodeForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>
	</div><!--/ row -->
</form>
