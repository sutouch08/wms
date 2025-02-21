<?php $this->load->view('include/header'); ?>

<input type="hidden" id="require_remark" value="<?php echo $this->require_remark; ?>" />
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
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm h" value="" id="code" disabled />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-sm-6 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm text-center h f" id="from_warehouse_code" autofocus />
	</div>
  <div class="col-lg-3-harf col-md-3 col-sm-3 col-xs-8 padding-5">
    <label class="not-show">&nbsp;</label>
    <input type="text" class="form-control input-sm h f" id="from_warehouse" value="" readonly />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center h t" id="to_warehouse_code" autofocus />
	</div>
	<div class="col-lg-3-harf col-md-3 col-sm-3 col-xs-8 padding-5">
    <label class="not-show">&nbsp;</label>
		<input type="text" class="form-control input-sm h t" id="to_warehouse" value="" readonly />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>การดำเนินการ</label>
		<select class="form-control input-sm h" name="is_wms" id="is_wms">
			<option value="">เลือก</option>
			<?php if($this->wmsApi) : ?>
				<option value="1">PIONEER</option>
			<?php endif; ?>
			<?php if($this->sokoApi) : ?>
				<option value="2">SOKOCHAN</option>
			<?php endif; ?>
			<option value="0">WARRIX</option>
			<option value="-1">ย้ายคลัง</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Interface</label>
		<select class="form-control input-sm h" name="api" id="api">
			<option value="1">ปกติ</option>
			<option value="0">ไม่ส่ง</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่ WX</label>
		<input type="text" class="form-control input-sm h" name="wx_code" id="wx_code" />
	</div>

  <div class="col-lg-6-harf col-md-6 col-sm-5-harf col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm h" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="addTransfer()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">


<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
