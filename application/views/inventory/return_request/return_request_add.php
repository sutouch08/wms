<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6 padding-5">
      	<p class="pull-right top-p">
					<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        </p>
    </div>
</div>
<hr class="padding-5" />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
<div class="row">
    <div class="col-sm-1 col-1-harf padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
    <div class="col-sm-8 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">save</label>
			<?php 	if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่ม</button>
			<?php	endif; ?>
		</div>
</div>
</form>
<hr class="margin-top-15 padding-5"/>

<script src="<?php echo base_url(); ?>scripts/inventory/return_request/return_request.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
