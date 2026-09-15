<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-5 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date-add" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" id="customer-code" value="" autocomplete="off" />
	</div>
	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm" id="customer-name" value="" readonly/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่บิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" value="" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>GP(%)</label>
		<input type="number" class="form-control input-sm text-center" name="gp" id="gp" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซนฝากขาย</label>
		<input type="text" class="form-control input-sm" id="from-zone" autocomplete="off" />
	</div>
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label class="not-show">โซนฝากขาย</label>
		<input type="text" class="form-control input-sm" id="from-zone-name" readonly />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm" id="zone-code" />
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm" id="zone-name" readonly />
	</div>
	<div class="col-lg-11 col-md-11 col-sm-11 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">save</label>
		<?php if ($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-white btn-success btn-block" style="height:30px;" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		<?php endif; ?>
	</div>
</div>

<hr class="margin-top-15" />

<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>