<?php $min = 3; $max = 6; ?>
	<form id="documentForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ขายสินค้า</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ORDER" required value="<?php echo $PREFIX_ORDER; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_ORDER">
					<?php echo select_running($min, $max, $RUN_DIGIT_ORDER); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ฝากขาย[โอนคลัง]</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIG_TR" required value="<?php echo $PREFIX_CONSIGN_TR; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_CONSIGN_TR">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_TR); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ฝากขาย[ใบกำกับ]</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_SO" required value="<?php echo $PREFIX_CONSIGN_SO; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_CONSIGN_SO">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_SO); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ตัดยอดฝากขาย(Shop)</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_SOLD" required value="<?php echo $PREFIX_CONSIGN_SOLD; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_CONSIGN_SOLD">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_SOLD); ?>
				</select>
			</div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ตัดยอดฝากขาย(ห้าง)</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGNMENT_SOLD" required value="<?php echo $PREFIX_CONSIGNMENT_SOLD; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_CONSIGNMENT_SOLD">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGNMENT_SOLD); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">รับสินคาเข้าจากการซื้อ</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RECEIVE_PO" required value="<?php echo $PREFIX_RECEIVE_PO; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_RECEIVE_PO">
					<?php echo select_running($min, $max, $RUN_DIGIT_RECEIVE_PO); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">รับสินค้าเข้าจากการแปรสภาพ</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2">
      	<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RECEIVE_TRANSFORM" required value="<?php echo $PREFIX_RECEIVE_TRANSFORM; ?>" />
      </div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_RECEIVE_TRANSFORM">
					<?php echo select_running($min, $max, $RUN_DIGIT_RECEIVE_TRANSFORM); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อขาย)</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center" name="PREFIX_TRANSFORM" required value="<?php echo $PREFIX_TRANSFORM; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_TRANSFORM">
					<?php echo select_running($min, $max, $RUN_DIGIT_TRANSFORM); ?>
				</select>
			</div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อสต็อก)</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center" name="PREFIX_TRANSFORM_STOCK" required value="<?php echo $PREFIX_TRANSFORM_STOCK; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_TRANSFORM_STOCK">
					<?php echo select_running($min, $max, $RUN_DIGIT_TRANSFORM_STOCK); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ยืมสินค้า</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_LEND" required value="<?php echo $PREFIX_LEND; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_LEND">
					<?php echo select_running($min, $max, $RUN_DIGIT_LEND); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกสปอนเซอร์</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SPONSOR" required value="<?php echo $PREFIX_SPONSOR; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_SPONSOR">
					<?php echo select_running($min, $max, $RUN_DIGIT_SPONSOR); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เบิกอภินันท์</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SUPPORT" required value="<?php echo $PREFIX_SUPPORT; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_SUPPORT">
					<?php echo select_running($min, $max, $RUN_DIGIT_SUPPORT); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ลดหนี้ขาย</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_ORDER" required value="<?php echo $PREFIX_RETURN_ORDER; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_RETURN_ORDER">
					<?php echo select_running($min, $max, $RUN_DIGIT_RETURN_ORDER); ?>
				</select>
			</div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ลดหนี้ฝากขายเทียม</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_CONSIGNMENT" required value="<?php echo $PREFIX_RETURN_CONSIGNMENT; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_RETURN_CONSIGNMENT">
					<?php echo select_running($min, $max, $RUN_DIGIT_RETURN_CONSIGNMENT); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">คืนสินค้าจากการยืม</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_LEND" required value="<?php echo $PREFIX_RETURN_LEND; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_RETURN_LEND">
					<?php echo select_running($min, $max, $RUN_DIGIT_RETURN_LEND); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">กระทบยอด</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_CHECK" required value="<?php echo $PREFIX_CONSIGN_CHECK; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_CONSIGN_CHECK">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_CHECK); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">โอนสินค้าระหว่างคลัง</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_TRANSFER" required value="<?php echo $PREFIX_TRANSFER; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_TRANSFER">
					<?php echo select_running($min, $max, $RUN_DIGIT_TRANSFER); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ย้ายพื้นที่จัดเก็บ</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_MOVE" required value="<?php echo $PREFIX_MOVE; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_MOVE">
					<?php echo select_running($min, $max, $RUN_DIGIT_MOVE); ?>
				</select>
			</div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ปรับยอดสต็อก</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ADJUST" required value="<?php echo $PREFIX_ADJUST; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_ADJUST">
					<?php echo select_running($min, $max, $RUN_DIGIT_ADJUST); ?>
				</select>
			</div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ปรับยอดสต็อก(ฝากขายเทียม)</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ADJUST_CONSIGNMENT" required value="<?php echo $PREFIX_ADJUST_CONSIGNMENT; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_ADJUST_CONSIGNMENT">
					<?php echo select_running($min, $max, $RUN_DIGIT_ADJUST_CONSIGNMENT); ?>
				</select>
			</div>
      <div class="divider"></div>

			<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">ตัดยอดแปรสภาพ(Goods Issue)</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ADJUST_TRANSFORM" required value="<?php echo $PREFIX_ADJUST_TRANSFORM; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_ADJUST_TRANSFORM">
					<?php echo select_running($min, $max, $RUN_DIGIT_ADJUST_TRANSFORM); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">นโยบายส่วนลด</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_POLICY" required value="<?php echo $PREFIX_POLICY; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_POLICY">
					<?php echo select_running($min, $max, $RUN_DIGIT_POLICY); ?>
				</select>
			</div>
      <div class="divider"></div>

      <div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">เงื่อนไขส่วนลด</span></div>
      <div class="col-lg-1-harf col-md-2 col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RULE" required value="<?php echo $PREFIX_RULE; ?>" /></div>
      <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-lg-1-harf col-md-2 col-sm-2">
				<select class="width-100" name="RUN_DIGIT_RULE">
					<?php echo select_running($min, $max, $RUN_DIGIT_RULE); ?>
				</select>
			</div>
      <div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

      <div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
      	<button type="button" class="btn btn-sm btn-success btn-100" onClick="checkDocumentSetting()">SAVE</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/ row -->
  </form>
