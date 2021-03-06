<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
<?php  $prefix = getConfig('PREFIX_TRANSFER'); ?>
<?php  $runNo = getConfig('RUN_DIGIT_TRANSFER'); ?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo $prefix; ?>">
	<input type="hidden" id="runNo" value="<?php echo $runNo; ?>">
<?php endif; ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
	<div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
		<?php if($manual_code == 1) : ?>
	    <input type="text" class="form-control input-sm" name="code" id="code" value="" />
		<?php else : ?>
			<input type="text" class="form-control input-sm" value="" disabled />
		<?php endif; ?>
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>

  <div class="col-sm-4 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm" name="from_warehouse" id="from_warehouse" value="" required />
  </div>

	<div class="col-sm-4 padding-5">
    <label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm" name="to_warehouse" id="to_warehouse" value="" required />
  </div>

	<div class="col-sm-1 padding-5">
		<label>WMS</label>
		<select class="form-control input-sm" name="api" id="api">
			<option value="1">ปกติ</option>
			<option value="0">ไม่ส่ง</option>
		</select>
	</div>
	<div class="col-sm-1 col-1-harf padding-5">
		<label>เลขที่ WX</label>
		<input type="text" class="form-control input-sm" name="wx_code" id="wx_code" />
	</div>
  <div class="col-sm-9 col-9-harf padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="from_warehouse_code" id="from_warehouse_code" value="" />
<input type="hidden" name="to_warehouse_code" id="to_warehouse_code" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
