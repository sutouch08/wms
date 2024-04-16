<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>
<div class="row">
	<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">
	<div class="row">
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รหัส</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="code" id="code" class="width-100 r" value="" onkeyup="validCode(this)" autofocus required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline grey e" id="code-error">Allow only [a-z, A-Z, 0-9, "-", "_" ]</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รหัสเก่า</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="old_code" id="old_code" class="width-100 r" value="" placeholder="รหัสเก่า (ไม่บังคับ)" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="name" id="name" class="width-100 r" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="name-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รุ่น</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="style" id="style" class="width-100 r" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="style-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รุ่นเก่า</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="old_style" id="old_style" class="width-100" value="" placeholder="รหัสรุ่นเก่า (ไม่บังคับ)"/>
			</div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">สี</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="color" id="color" class="width-100 r" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="color-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ไซส์</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="size" id="size" class="width-100 r" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="size-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">บาร์โค้ด</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="barcode" id="barcode" class="width-100 r" value="" />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="barcode-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ราคาทุน</label>
			<div class="col-xs-12 col-sm-3">
				<input type="number" step="any" name="cost" id="cost" class="width-100 r" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="cost-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ราคาขาย</label>
			<div class="col-xs-12 col-sm-3">
				<input type="number" step="any" name="price" id="price" class="width-100 r" value="" required />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="price-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">หน่วยนับ</label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm r" name="unit_code" id="unit_code" required>
					<option value="">โปรดเลือก</option>
					<?php echo select_unit(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="unit-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ยี่ห้อ</label>
			<div class="col-xs-12 col-sm-3">
				<select name="brand_code" id="brand" class="form-control r">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_brand(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="brand-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="group_code" id="group" class="form-control input-sm r" required>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_group(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="group-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มหลัก</label>
			<div class="col-xs-12 col-sm-3">
				<select name="main_group_code" id="mainGroup" class="form-control r" required>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_main_group(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="mainGroup-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มย่อย</label>
			<div class="col-xs-12 col-sm-3">
				<select name="sub_group_code" id="subGroup" class="form-control r">
					<option value="">โปรดเลือก</option>
				<?php echo select_product_sub_group(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="subGroup-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">หมวดหมู่สินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="category_code" id="category" class="form-control r" required>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_category(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="category-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ประเภทสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="kind_code" id="kind" class="form-control r" required>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_kind(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="kind-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ชนิดสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="type_code" id="type" class="form-control r" required>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_type(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="type-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">คอเล็คชั่น</label>
			<div class="col-xs-12 col-sm-3">
				<select name="collection_code" id="collection" class="form-control r">
					<option value="">กรุณาเลือก</option>
				<?php echo select_active_collection(); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="collection-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ปี</label>
			<div class="col-xs-12 col-sm-3">
				<select name="year" id="year" class="form-control r" required>
				<?php echo select_years(date('Y')); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red e" id="year-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="count_stock" id="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="can_sell" id="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">API</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="is_api" id="is_api" class="ace ace-switch ace-switch-7" type="checkbox" value="1" />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ใช้งาน</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="active" id="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label not-show">บันทึก</label>
			<div class="col-xs-12 col-sm-3">
				<button type="button" class="btn btn-sm btn-success btn-block" onclick="add()"><i class="fa fa-save"></i> บันทึก</button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<input type="hidden" name="valid" id="valid" value=""/>
	</div>
	</form>
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
