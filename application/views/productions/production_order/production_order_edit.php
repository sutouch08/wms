<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_order/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_order/production_order_edit_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_order/production_order_edit_header_right'); ?>

</div>
<hr class="padding-5">

<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#components" aria-expanded="true">Components</a></li>
    <!-- <li class=""><a data-toggle="tab" href="#summary" aria-expanded="true">Summary</a></li> -->
  </ul>

  <div class="tab-content">
    <?php $this->load->view('productions/production_order/tab_component'); ?>
  </div>
</div>

<div class="divider-hidden"></div>

<div class="row" style="margin-left:0px; margin-right:0px;">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-0">User</label>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
					<input type="text" id="user" class="form-control input-xs" value="<?php echo $this->_user->uname; ?>" disabled/>
				</div>
			</div>
		</div>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-0">Remark</label>
				<div class="col-lg-10 col-md-10 col-sm-9 col-xs-8 padding-5">
					<textarea class="form-control input-xs" id="remark" rows="2"><?php echo $doc->Comments; ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 text-right">
		<button type="button" class="btn btn-xs btn-primary btn-100" onclick="update()">Update</button>
		<button type="button" class="btn btn-xs btn-danger btn-100" onclick="Cancel()">Cancel</button>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
