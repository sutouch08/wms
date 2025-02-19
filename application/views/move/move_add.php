<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 e" value="" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 text-center e" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
  </div>

	<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 padding-5">
		<label>คลังสินค้า</label>
		<select class="width-100 e" id="warehouse">
			<option value="">เลือกคลัง</option>
			<?php echo select_warehouse(); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label>อ้างอิง</label>
		<input type="text" class="width-100 text-center e" id="reference" />
	</div>
  <div class="col-lg-8-harf col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="padding-5 margin-top-15">

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
