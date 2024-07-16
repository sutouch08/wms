<?php $this->load->view('include/header'); ?>
<input type="hidden" id="required_remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
<div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm" value="" disabled />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>วันที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center e" id="doc-date" value="<?php echo date('d-m-Y'); ?>" readonly/>
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>วันที่สินค้าเข้า</label>
      <input type="text" class="form-control input-sm text-center e" id="due-date" value="" />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>Posting date</label>
      <input type="text" class="form-control input-sm text-center e" id="posting-date" value="" />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ช่องทางการรับ</label>
			<select class="form-control input-sm e" name="is_wms" id="is_wms">
				<option value="">เลือก</option>
				<?php if($this->wmsApi) : ?>
				<option value="1">Pioneer</option>
				<?php endif; ?>
				<?php if($this->sokoApi) : ?>
				<option value="2">SOKOCHAN</option>
				<?php endif; ?>
				<option value="0">Warrix</option>
			</select>
		</div>
    <div class="col-lg-6-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm e" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">save</label>
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่ม</button>
			<?php	endif; ?>
		</div>
</div>
</form>
<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
