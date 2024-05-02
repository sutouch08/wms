<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<div class="form-horizontal" id="addForm">
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Reason</label>
    <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
			<input type="text" name="name" id="name" class="width-100" maxlength="250" value="<?php echo $name; ?>" placeholder="เหตุผลในการยกเลิก (จำเป็น)" autofocus/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Status</label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-small" name="active" id="active">
				<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="active-error"></div>
  </div>

	<div class="divider-hidden"></div>

	<?php if($this->pm->can_edit) : ?>
	  <div class="form-group">
	    <label class="col-sm-3 control-label no-padding-right"></label>
	    <div class="col-xs-12 col-sm-3">
	      <p class="pull-right">
	        <button type="button" class="btn btn-sm btn-success btn-100" onclick="update(<?php echo $id; ?>)">Update</button>
	      </p>
	    </div>
	    <div class="help-block col-xs-12 col-sm-reset inline">
	      &nbsp;
	    </div>
	  </div>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/cancel_reason.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
