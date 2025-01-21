<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm text-center" name="customerCode" id="customerCode" value="" required />
  </div>

  <div class="col-lg-6 col-md-6-harf col-sm-6-harf col-xs-8 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>GP</label>
		<div class="input-group width-100">
			<input type="number" class="form-control input-sm width-50" name="gp" id="gp" value="" />
			<select class="form-control input-sm width-50" style="border-left:0px;" name="unit" id="unit">
				<option value="%">%</option>
				<option value="THB">THB</option>
			</select>
		</div>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center" name="zone_code" id="zone_code" value="" required />
  </div>

	<div class="col-lg-5 col-md-7 col-sm-7 col-xs-8 padding-5">
    <label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm" name="zone" id="zone" value="" />
  </div>

	<div class="col-lg-5 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="width-100" id="warehouse">
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>
  <div class="col-lg-11 col-md-6-harf col-sm-6-harf col-xs-6 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
</form>
<hr class="margin-top-15">

<script>
	$('#warehouse').select2();
</script>
<?php if($this->menu_code == 'SOCCSO') : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_so.js?v=<?php echo date('Ymd'); ?>"></script>
<?php else : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_tr.js?v=<?php echo date('Ymd'); ?>"></script>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
