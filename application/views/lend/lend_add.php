<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
<?php  $prefix = getConfig('PREFIX_LEND'); ?>
<?php  $runNo = getConfig('RUN_DIGIT_LEND'); ?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo $prefix; ?>">
	<input type="hidden" id="runNo" value="<?php echo $runNo; ?>">
<?php endif; ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>เลขที่เอกสาร</label>
		<?php if($manual_code == 1) : ?>
	    <input type="text" class="form-control input-sm" name="code" id="code" value="" />
		<?php else : ?>
			<input type="text" class="form-control input-sm" value="" disabled />
		<?php endif; ?>
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
  </div>

  <div class="col-sm-3 padding-5">
    <label>ผู้ยืม</label>
    <input type="text" class="form-control input-sm" name="empName" id="empName" value="" required />
  </div>

	<div class="col-sm-4 padding-5">
    <label>พื้นที่จัดเก็บ[คลังยืม]</label>
		<input type="text" class="form-control input-sm" name="zone" id="zone" value="" />
  </div>

	<div class="col-sm-2 padding-5 last">
		<label>ผู้รับ[คนสั่งงาน]</label>
		<input type="text" class="form-control input-sm" name="user_ref" id="user_ref" value="" />
	</div>

	<div class="col-sm-2 col-xs-12 padding-5 first">
		<label>คลัง</label>
    <select class="form-control input-sm" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

  <div class="col-sm-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="empID" id="empID" value="" />
<input type="hidden" name="zone_code" id="zone_code" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/lend/lend.js"></script>
<script src="<?php echo base_url(); ?>scripts/lend/lend_add.js"></script>

<?php $this->load->view('include/footer'); ?>
