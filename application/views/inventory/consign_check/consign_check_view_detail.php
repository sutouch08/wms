<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h3 class="title" >
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0 && $doc->valid == 0) : ?>
				<button type="button" class="btn btn-xs btn-primary" onclick="reloadStock()">
					<i class="fa fa-refresh"></i> โหลดยอดตั้งต้นใหม่
				</button>
				<?php if($doc->is_wms == 0) : ?>
					<!--- consign_check_detail.js --->
					<button type="button" class="btn btn-xs btn-success" onclick="closeCheck()">
						<i class="fa fa-bolt"></i> ปิดการตรวจนับ
					</button>
				<?php endif; ?>
			<?php endif; ?>

			<?php if(($this->_SuperAdmin && $doc->status != 2) OR (($this->pm->can_edit OR $this->pm->can_delete) && $doc->status != 2 && $doc->valid == 0)) : ?>
				<!--- consign_check_detail.js --->
				<button type="button" class="btn btn-xs btn-purple" onclick="openCheck()">
					<i class="fa fa-bolt"></i> เปิดการตรวจนับ
				</button>
			<?php endif; ?>

			<?php if(($this->_SuperAdmin && $doc->status != 2) OR (($this->pm->can_edit OR $this->pm->can_delete) && $doc->status != 2 && $doc->valid == 0)) : ?>
				<!--- consign_check_detail.js --->
				<button type="button" class="btn btn-xs btn-danger" onclick="goDelete('<?php echo $doc->code; ?>')">
					<i class="fa fa-bolt"></i> ยกเลิก
				</button>
			<?php endif; ?>

			<?php if($doc->status == 3 && (($this->sokoApi && $doc->is_wms == 2) OR ($this->wmsApi && $doc->is_wms == 1))) : ?>
				<button type="button" class="btn btn-xs btn-success btn-top" onclick="sendToWms()"><i class="fa fa-send"></i> Send to WMS</button>
			<?php endif; ?>

			<?php if($this->pm->can_delete && $doc->status == 0 && $doc->valid == 0) : ?>
				<!--- consign_check_detail.js --->
				<button type="button" class="btn btn-xs btn-danger" onclick="clearDetails()">
					<i class="fa fa-trash"></i> ยกเลิกการตรวจนับ
				</button>
			<?php endif; ?>
		</p>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center e" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>" disabled/>
	</div>
	<div class="col-lg-6 col-md-7 col-sm-6-harf col-xs-12 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm e" name="customer_name" id="customer_name" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>ช่องทาง</label>
		<select class="form-control input-sm" disabled>
			<option value="">ไม่ระบุ</option>
			<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>Pioneer</option>
			<option value="2" <?php echo is_selected('2', $doc->is_wms); ?>>SOKOCHAN</option>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>Warrix</option>
		</select>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
		<label>รหัสโซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>
	<div class="col-lg-4-harf col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone_name" id="zone_name" value="<?php echo $doc->zone_name; ?>" disabled/>
	</div>

	<div class="col-lg-5-harf col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>

	<?php if($doc->status == 2) : ?>
		<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
			<label>เหตุผลในการยกเลิก</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
			<label>ยกเลิกโดย</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_user; ?>" disabled>
		</div>
	<?php endif; ?>
</div>

<input type="hidden" name="check_code" id="check_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="id_box" id="id_box">

<hr class="margin-top-15"/>
<?php 	$this->load->view('inventory/consign_check/consign_check_edit_detail'); ?>
<?php if($doc->status != 2) : ?>
	<?php if($doc->status == 3) : ?>
		<?php $this->load->view('on_process_watermark'); ?>
	<?php endif; ?>
<?php else : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
