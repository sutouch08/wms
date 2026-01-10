<?php $this->load->view('include/header'); ?>

<input type="hidden" id="require-remark" value="<?php echo $this->require_remark; ?>" />

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm h" value="" id="code" disabled />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-sm-6 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>

	<div class="col-lg-3-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<select class="form-control input-sm h" id="from-warehouse">
			<option value="">เลือกคลังต้นทาง</option>
			<?php echo select_warehouse(); ?>
		</select>
	</div>

	<div class="col-lg-3-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<select class="form-control input-sm h" id="to-warehouse">
			<option value="">เลือกคลังปลายทาง</option>
			<?php echo select_warehouse(); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Action</label>
		<select class="form-control input-sm h" id="is-wms">
			<option value="0">ปกติ</option>
			<option value="-1">ย้ายคลัง</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่ WX</label>
		<input type="text" class="form-control input-sm h" id="wx-code" />
	</div>

  <div class="col-lg-11 col-md-7 col-sm-7 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm h" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="addTransfer()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">

<script>
	$('#from-warehouse').select2();
	$('#to-warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
