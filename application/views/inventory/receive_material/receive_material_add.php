<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-white btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="width-100 text-center r" value="" readonly/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center r" id="date-add" value="<?php echo date('d-m-Y'); ?>" readonly/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<input type="text" class="width-100 text-center r" id="posting-date" value="" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code" placeholder="รหัสผู้ขาย" value="" autofocus/>
	</div>
	<div class="col-lg-7 col-md-5 col-sm-5 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" placeholder="ชื่อผู้ขาย" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
		<label>PO No.</label>
		<input type="text" class="width-100 text-center r" id="po-code" placeholder="อ้างอิงใบสั่งซื้อ" autocomplete="off"		value="" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label class="width-100 not-show">&nbsp;</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" style="height:30px;" onclick="clearPo()">Clear</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm r" id="DocCur" disabled>
			<?php echo select_currency(getConfig('DEFAULT_CURRENCY')); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="1.00"  disabled/>
	</div>

	<div class="col-lg-3 col-md-5 col-sm-4 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice-code" value="" />
	</div>

	<div class="col-lg-4-harf col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" onchange="changeZone()">
			<option value="">เลือก</option>
			<?php echo select_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="width-100 r" id="zone-code" value="" />
	</div>

	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label class="not-show">โซนรับสินค้า</label>
		<input type="text" class="width-100 r" id="zone-name" value="" readonly/>
	</div>
	<div class="col-lg-6 col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100" id="remark" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()">Add</button>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
