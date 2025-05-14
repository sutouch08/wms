<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  <?php if(($this->pm->can_add OR $this->pm->can_edit) && ($doc->status == 'D' OR $doc->status == 'P')) : ?>
    <button type="button" class="btn btn-white btn-primary top-btn" onclick="getUploadFile()"><i class="fa fa-upload"></i> &nbsp; Import Items</button>
    <button type="button" class="btn btn-white btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> &nbsp; Save</button>
  <?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
  <div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center"  id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 padding-5">
    <label>คลัง</label>
    <select class="width-100 rq" id="warehouse">
      <option value="">เลือกคลัง</option>
      <?php echo select_warehouse($doc->warehouse_code); ?>
    </select>
  </div>

  <div class="col-lg-6 col-md-5 col-sm-5 col-xs-8 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm rq" id="name" value="<?php echo $doc->name; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Active</label>
    <select id="active" class="form-control input-sm">
      <option value="1" <?php echo is_selected('1', $doc->active); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $doc->active); ?>>Inactive</option>
    </select>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Start Date</label>
		  <input type="text" class="form-control input-sm text-center rq" id="start_date" value="<?php echo thai_date($doc->start_date); ?>"  readonly/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>End Date</label>
    <input type="text" class="form-control input-sm text-center rq" id="end_date" value="<?php echo thai_date($doc->end_date); ?>" readonly />
  </div>
  <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="update()">Update</button>
		</div>
  <?php endif; ?>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15">
<?php $this->load->view('reserv_stock/item_control'); ?>
<?php $this->load->view('reserv_stock/details'); ?>
<?php $this->load->view('reserv_stock/import_items'); ?>

<script>
  $('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/reserv_stock/reserv_stock.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
