	<form id="bookcodeForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ขายสินค้า</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_ORDER" value="<?php echo $BOOK_CODE_ORDER; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกอภินันท์</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_SUPPORT" value="<?php echo $BOOK_CODE_SUPPORT; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกสปอนเซอร์</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_SPONSOR" value="<?php echo $BOOK_CODE_SPONSOR; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">รับสินค้าจากการซื้อ</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_BI" value="<?php echo $BOOK_CODE_RECEIVE_PO; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">โอนสินค้าระหว่างคลัง</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_TRANSFER" value="<?php echo $BOOK_CODE_TRANSFER; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ยืมสินค้า</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_LEND" value="<?php echo $BOOK_CODE_LEND; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ฝากขาย(โอนคลัง)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGN_TR" value="<?php echo $BOOK_CODE_CONSIGN_TR; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ฝากขาย(ใบกำกับ)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGN_SO" value="<?php echo $BOOK_CODE_CONSIGN_SO; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ตัดยอดฝากขาย(Shop)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGN_SOLD" value="<?php echo $BOOK_CODE_CONSIGN_SOLD; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ตัดยอดฝากขาย(ห้าง)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGNMENT_SOLD" value="<?php echo $BOOK_CODE_CONSIGNMENT_SOLD; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อขาย)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_TRANSFORM" value="<?php echo $BOOK_CODE_TRANSFORM; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อสต็อก)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_TRANSFORM_STOCK" value="<?php echo $BOOK_CODE_TRANSFORM_STOCK; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">รับสินค้าจากการแปรสภาพ</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RECEIVE_TRANSFORM" value="<?php echo $BOOK_CODE_RECEIVE_TRANSFORM; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ตัดยอดแปรสภาพ</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_ADJUST_TRANSFORM" value="<?php echo $BOOK_CODE_ADJUST_TRANSFORM; ?>" />
      </div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ลดหนี้ขาย</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RETURN_ORDER" value="<?php echo $BOOK_CODE_RETURN_ORDER; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ลดหนี้ฝากขายเทียม</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RETURN_CONSIGNMENT" value="<?php echo $BOOK_CODE_RETURN_CONSIGNMENT; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">คืนสินค้า(จากการยืม)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RETURN_LEND" value="<?php echo $BOOK_CODE_RETURN_LEND; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ปรับยอดสต็อก</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_ADJUST" value="<?php echo $BOOK_CODE_ADJUST; ?>" />
      </div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ปรับยอดสต็อก(ฝากขายเทียม)</span></div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_ADJUST_CONSIGNMENT" value="<?php echo $BOOK_CODE_ADJUST_CONSIGNMENT; ?>" />
      </div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

      <div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
      	<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('bookcodeForm')">SAVE</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/ row -->
  </form>
