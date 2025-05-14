<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
  <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 padding-5">
    <label>คลัง</label>
    <select class="width-100 rq" id="warehouse">
      <option value="">เลือกคลัง</option>
      <?php echo select_warehouse(); ?>
    </select>
  </div>

  <div class="col-lg-4 col-md-5 col-sm-5 col-xs-8 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm rq" id="name" value="" required />
  </div>

  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>Active</label>
    <select id="active" class="form-control input-sm">
      <option value="1">Active</option>
      <option value="0">Inactive</option>
    </select>
  </div>
  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>Start Date</label>
		  <input type="text" class="form-control input-sm text-center rq" id="start_date" value="" readonly />
  </div>
  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>End Date</label>
    <input type="text" class="form-control input-sm text-center rq" id="end_date" value="" readonly />
  </div>
  <?php if($this->pm->can_add) : ?>
<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()">Add</button>
  </div>
  <?php endif; ?>
</div>
<hr class="margin-top-15">


<script>
  $('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/reserv_stock/reserv_stock.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
