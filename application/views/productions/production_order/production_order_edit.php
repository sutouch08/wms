<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_order/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				&nbsp;Save&nbsp;
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="primary">
					<a href="javascript:update('P')">Save As Draft</a>
				</li>
				<li class="purple">
					<a href="javascript:update('R')">Save And Release</a>
				</li>
			</ul>
		</div>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_order/production_order_edit_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_order/production_order_edit_header_right'); ?>
</div>
<hr class="margin-top-10 margin-bottom-10" style="margin-left:-12px; margin-right:-12px;">
<div class="row">
	<?php $this->load->view('productions/production_order/production_order_edit_detail'); ?>
</div>

<div class="divider-hidden"></div>

<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
