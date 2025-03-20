<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn btn-100" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" id="date-add" value="<?php echo date('d-m-Y'); ?>" disabled/>
  </div>
	<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>ช่องทางขาย</label>
		<select class="width-100 e" id="channels">
			<option value="" data-name="">เลือก</option>			
			<?php echo select_dispatch_channels(); ?>
		</select>
	</div>
	<div class="col-lg-3 col-md-3-harf col-sm-3 col-xs-6 padding-5">
		<label>ผู้จัดส่ง</label>
		<select class="width-100 e" id="sender">
			<option value="">เลือก</option>
			<?php echo select_sender(); ?>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ทะเบียนรถ</label>
    <input type="text" class="form-control input-sm text-center e" id="plate-no" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>จังหวัด</label>
    <input type="text" class="form-control input-sm text-center e" id="province" value="" />
	</div>

	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ชื่อคนขับ</label>
    <input type="text" class="form-control input-sm text-center e" id="driver-name" value="" />
	</div>
  <div class="col-lg-8-harf col-md-7-harf col-sm-7-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" id="remark" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">x</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> Add</button>
  </div>
</div>
<hr class=""/>

<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
