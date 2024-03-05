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
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Doc No.</label>
    <input type="text" class="form-control input-sm text-center"  id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm rq" id="name" value="<?php echo $doc->name; ?>" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Status</label>
    <select id="status" class="form-control input-sm">
      <option value="1" <?php echo is_selected('1', $doc->status); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $doc->status); ?>>Inactive</option>
    </select>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Start Date</label>
		  <input type="text" class="form-control input-sm text-center rq" id="start_date" value="<?php echo thai_date($doc->start_date); ?>"  readonly/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
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
<?php $this->load->view('preorder/item_control'); ?>
<?php $this->load->view('preorder/details'); ?>

<script src="<?php echo base_url(); ?>scripts/preorder/preorder.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
