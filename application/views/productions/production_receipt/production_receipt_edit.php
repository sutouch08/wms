<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_receipt/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div><!-- End Row -->
<hr class=""/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>Doc No.</label>
		<input type="text" class="form-control input-sm h" value="<?php echo $doc->code; ?>" id="code" disabled />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-sm-6 col-xs-6 padding-5">
    <label>Date</label>
    <input type="text" class="form-control input-sm text-center h" id="date-add" value="<?php echo thai_date($doc->date_add) ?>" readonly required />
  </div>

	<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>From Warehouse</label>
		<select class="form-control input-sm h" id="fromWhsCode">
      <option value="">Please Select</option>
      <?php echo select_warehouse($doc->fromWhsCode); ?>
    </select>
	</div>

  <div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>To Warehouse</label>
		<select class="form-control input-sm h" id="toWhsCode">
      <option value="">Please Select</option>
      <?php echo select_warehouse($doc->toWhsCode); ?>
    </select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Ref No.</label>
    <input type="text" class="form-control input-sm h" id="base-ref" value="<?php echo $doc->reference; ?>" data-prev="<?php echo $doc->reference; ?>" />
	</div>

  <div class="col-lg-2 col-md-2 col-sm-2 cl-xs-6 padding-5">
    <label>Production Item</label>
    <input type="text" class="form-control input-sm" id="pdo-item" value="<?php echo $doc->ItemCode; ?>" readonly />
  </div>

  <div class="col-lg-10-harf col-md-6 col-sm-5-harf col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm h" id="remark" value="<?php echo $doc->remark; ?>">
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> Add</button>
  </div>
</div>
<hr class="margin-top-15">

<script>
  $('#fromWhsCode').select2();
  $('#toWhsCode').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/productions/production_receipt/production_receipt.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_receipt/production_receipt_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
