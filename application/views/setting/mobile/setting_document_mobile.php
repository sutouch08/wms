<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Document setting</span>
</div>
<?php $min = 3; $max = 6; ?>
<form id="documentForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ขายสินค้า</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_ORDER" required value="<?php echo $PREFIX_ORDER; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_ORDER">
					<?php echo select_running($min, $max, $RUN_DIGIT_ORDER); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ฝากขายแท้</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_CONSIG_TR" required value="<?php echo $PREFIX_CONSIGN_TR; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_CONSIGN_TR">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_TR); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ฝากขายเทียม</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_CONSIGN_SO" required value="<?php echo $PREFIX_CONSIGN_SO; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_CONSIGN_SO">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_SO); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ตัดยอดฝากขายแท้</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_CONSIGN_SOLD" required value="<?php echo $PREFIX_CONSIGN_SOLD; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_CONSIGN_SOLD">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_SOLD); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ตัดยอดฝากขายเทียม</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_CONSIGNMENT_SOLD" required value="<?php echo $PREFIX_CONSIGNMENT_SOLD; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_CONSIGNMENT_SOLD">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGNMENT_SOLD); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">รับสินค้าจากการซื้อ</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_RECEIVE_PO" required value="<?php echo $PREFIX_RECEIVE_PO; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_RECEIVE_PO">
					<?php echo select_running($min, $max, $RUN_DIGIT_RECEIVE_PO); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">รับจากการแปรสภาพ</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_RECEIVE_TRANSFORM" required value="<?php echo $PREFIX_RECEIVE_TRANSFORM; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_RECEIVE_TRANSFORM">
					<?php echo select_running($min, $max, $RUN_DIGIT_RECEIVE_TRANSFORM); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">เบิกแปรสภาพ(ขาย)</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_TRANSFORM" required value="<?php echo $PREFIX_TRANSFORM; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_TRANSFORM">
					<?php echo select_running($min, $max, $RUN_DIGIT_TRANSFORM); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">เบิกแปรสภาพ(สต็อก)</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_TRANSFORM_STOCK" required value="<?php echo $PREFIX_TRANSFORM_STOCK; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_TRANSFORM_STOCK">
					<?php echo select_running($min, $max, $RUN_DIGIT_TRANSFORM_STOCK); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ยืมสินค้า</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_LEND" required value="<?php echo $PREFIX_LEND; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_LEND">
					<?php echo select_running($min, $max, $RUN_DIGIT_LEND); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">เบิกสปอนเซอร์</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_SPONSOR" required value="<?php echo $PREFIX_SPONSOR; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_SPONSOR">
					<?php echo select_running($min, $max, $RUN_DIGIT_SPONSOR); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">เบิกอภินันท์</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_SUPPORT" required value="<?php echo $PREFIX_SUPPORT; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_SUPPORT">
					<?php echo select_running($min, $max, $RUN_DIGIT_SUPPORT); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ลดหนี้ขาย</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_RETURN_ORDER" required value="<?php echo $PREFIX_RETURN_ORDER; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_RETURN_ORDER">
					<?php echo select_running($min, $max, $RUN_DIGIT_RETURN_ORDER); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ลดหนี้ฝากขายเทียม</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_RETURN_CONSIGNMENT" required value="<?php echo $PREFIX_RETURN_CONSIGNMENT; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_RETURN_CONSIGNMENT">
					<?php echo select_running($min, $max, $RUN_DIGIT_RETURN_CONSIGNMENT); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">คืนสินค้าจากการยืม</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_RETURN_LEND" required value="<?php echo $PREFIX_RETURN_LEND; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_RETURN_LEND">
					<?php echo select_running($min, $max, $RUN_DIGIT_RETURN_LEND); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">กระทบยอด</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_CONSIGN_CHECK" required value="<?php echo $PREFIX_CONSIGN_CHECK; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_CONSIGN_CHECK">
					<?php echo select_running($min, $max, $RUN_DIGIT_CONSIGN_CHECK); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">โอนสินค้าระหว่างคลัง</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_TRANSFER" required value="<?php echo $PREFIX_TRANSFER; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_TRANSFER">
					<?php echo select_running($min, $max, $RUN_DIGIT_TRANSFER); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ย้ายพื้นที่จัดเก็บ</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_MOVE" required value="<?php echo $PREFIX_MOVE; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_MOVE">
					<?php echo select_running($min, $max, $RUN_DIGIT_MOVE); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ปรับยอดสต็อก</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_ADJUST" required value="<?php echo $PREFIX_ADJUST; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_ADJUST">
					<?php echo select_running($min, $max, $RUN_DIGIT_ADJUST); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ปรับสต็อกฝากขายเทียม</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_ADJUST_CONSIGNMENT" required value="<?php echo $PREFIX_ADJUST_CONSIGNMENT; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_ADJUST_CONSIGNMENT">
					<?php echo select_running($min, $max, $RUN_DIGIT_ADJUST_CONSIGNMENT); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">ตัดยอดแปรสภาพ</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_ADJUST_TRANSFORM" required value="<?php echo $PREFIX_ADJUST_TRANSFORM; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_ADJUST_TRANSFORM">
					<?php echo select_running($min, $max, $RUN_DIGIT_ADJUST_TRANSFORM); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">นโยบายส่วนลด</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_POLICY" required value="<?php echo $PREFIX_POLICY; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_POLICY">
					<?php echo select_running($min, $max, $RUN_DIGIT_POLICY); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>

		<div class="col-xs-7 last">
			<div class="input-group width-100">
				<span class="input-group-addon fix-width-120 font-size-11" style="text-align:left;">เงื่อนไขส่วนลด</span>
				<input type="text" class="width-100 text-center prefix" name="PREFIX_RULE" required value="<?php echo $PREFIX_RULE; ?>" />
			</div>
		</div>
		<div class="col-xs-5 first">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-11">Running</span>
				<select class="width-100" name="RUN_DIGIT_RULE">
					<?php echo select_running($min, $max, $RUN_DIGIT_RULE); ?>
				</select>
			</div>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

			<div class="col-xs-12">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<button type="button" class="btn btn-sm btn-success btn-block" onClick="checkDocumentSetting()">SAVE</button>
				<?php endif; ?>
			</div>
			<div class="divider-hidden"></div>

		</div><!--/ row -->
	</form>
