<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Code</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 r" value="<?php echo $code; ?>" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Name</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100 r" value="<?php echo $name; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="name-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ตำแหน่ง</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="position" id="position" class="width-50 r" value="<?php echo $position; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="pos-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="update(<?php echo $id; ?>)"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" id="id" value="<?php echo $id; ?>" />
</div>

<script src="<?php echo base_url(); ?>scripts/masters/product_size.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
