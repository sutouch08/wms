<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/receive_po/style'); ?>
<input type="hidden" id="req-remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		</p>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Doc date</label>
		<input type="text" class="form-control input-sm text-center e" id="doc-date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Due date</label>
		<input type="text" class="form-control input-sm text-center e" id="due-date" value="" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Post date</label>
		<input type="text" class="form-control input-sm text-center e" id="posting-date" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" id="vendor-code" class="form-control input-sm text-center e">
	</div>
	<div class="col-lg-4 col-md-4-harf col-sm-4 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" id="vendor-name" class="form-control input-sm e">
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center e" id="invoice" value="" placeholder="อ้างอิงใบส่งสินค้า" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-8 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center e" id="po-code" data-vendor="" value="" placeholder="ค้นหาใบสั่งซื้อ" />
	</div>
	<div class="col-lg-3 col-md-7 col-sm-7 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 e" id="warehouse">
			<option value="">เลือก</option>
			<?php echo select_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>
	<div class="col-lg-6-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">save</label>
		<?php if ($this->pm->can_add) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		<?php endif; ?>
	</div>
	<input type="hidden" id="vendor" data-code="" data-name="" />
</div>
<hr class="margin-top-15" />

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>