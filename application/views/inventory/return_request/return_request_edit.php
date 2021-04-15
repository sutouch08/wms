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
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
        </p>
    </div>
</div>
<hr class="padding-5" />

<form id="addForm" action="<?php echo $this->home.'/update'; ?>" method="post">
<div class="row">
    <div class="col-sm-1 col-1-harf padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
				<input type="hidden" id="code" value="<?php echo $doc->code; ?>">
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
    </div>
    <div class="col-sm-8 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
    </div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">save</label>
			<?php 	if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> อัพเดต</button>
			<?php	endif; ?>
		</div>
</div>
</form>
<hr class="margin-top-15 padding-5"/>

<?php $this->load->view('inventory/return_request/return_request_control'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/return_request/return_request.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
