<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="form-horizontal" style="padding-top:30px;">
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 r" value="<?php echo $code; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">สี</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100 r" value="<?php echo $name; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">กลุ่มสี</label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm r" id="color_group" name="color_group">
				<option value="">เลือกกลุ่มสี</option>
				<?php echo select_color_group($id_group); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="group-error"></div>
  </div>

	<div class="divider-hidden"></div>
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
</div>

<script src="<?php echo base_url(); ?>scripts/masters/product_color.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
