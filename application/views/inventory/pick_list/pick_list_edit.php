<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-sm btn-primary top-btn" onclick="releasePickList()">Release Pick List</button>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
    <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 text-center e" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
  </div>

	<div class="col-lg-4-harf col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>คลังสินค้าต้นทาง</label>
		<select class="width-100 e" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_common_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<label>โซนปลายทาง</label>
		<select class="width-100 e" id="zone" disabled>
			<option value="">เลือกโซน</option>
			<?php echo select_pickface_zone($doc->zone_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<label>ช่องทางขาย</label>
		<select class="width-100 e" id="channels" disabled>
			<option value="">เลือกช่องทางขาย</option>
			<?php echo select_channels($doc->channels_code); ?>
		</select>
	</div>
  <div class="col-lg-11 col-md-8 col-sm-8 col-xs-9 padding-5">
    <label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>	
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i>  แก้ไข</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> Update</button>
  </div>
</div>
<hr class="padding-5 margin-top-15">
<?php $this->load->view('inventory/pick_list/pick_list_control'); ?>
<?php $this->load->view('inventory/pick_list/pick_list_details'); ?>

<script>
	$('#warehouse').select2();
	$('#zone').select2();
	$('#channels').select2();
  $('#channels-code').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
