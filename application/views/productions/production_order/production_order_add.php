<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_order/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_order/production_order_add_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_order/production_order_add_header_right'); ?>

</div>
<hr class="margin-top-10 margin-bottom-10" style="margin-left:-12px; margin-right:-12px;">
<div class="row">
	<?php $this->load->view('productions/production_order/production_order_detail'); ?>
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
					<textarea class="form-control input-xs" id="remark" rows="2"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 text-right">
		<button type="button" class="btn btn-white btn-success btn-100" onclick="add()">Add</button>
		<button type="button" class="btn btn-white btn-default btn-100" onclick="leave()">Cancel</button>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
