<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo getConfig('PREFIX_ADJUST'); ?>">
	<input type="hidden" id="runNo" value="<?php echo getConfig('RUN_DIGIT_ADJUST'); ?>">
<?php endif; ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      	<p class="pull-right top-p">
			    <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        </p>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
			<?php if($manual_code == 1) : ?>
				<input type="text" class="form-control input-sm" name="code" id="code" value="" required />
			<?php else : ?>
				<input type="text" class="form-control input-sm" value="" disabled />
			<?php endif; ?>
    </div>
		<div class="col-sm-1 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-2 padding-5">
			<label>อ้างถึง</label>
			<input type="text" class="form-control input-sm" name="reference" id="reference" value="" />
		</div>
		<div class="col-sm-6 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label class="display-block not-show">add</label>
			<?php if($this->pm->can_add) : ?>
				<?php if($manual_code == 1) : ?>
							<button type="button" class="btn btn-xs btn-success btn-block" onclick="validateOrder()"><i class="fa fa-plus"></i> เพิ่ม</button>
				<?php else : ?>
							<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
				<?php endif; ?>
			<?php	endif; ?>
		</div>
</div>
<hr class="margin-top-15"/>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js"></script>
<?php $this->load->view('include/footer'); ?>
