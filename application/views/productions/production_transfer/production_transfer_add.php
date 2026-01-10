<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_transfer/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> Back</button>		
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_transfer/production_transfer_add_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_transfer/production_transfer_add_header_right'); ?>
</div>
<hr class="padding-5">

<?php $this->load->view('productions/production_transfer/production_transfer_details'); ?>

<script>
  $('#fromWhsCode').select2();
  $('#toWhsCode').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/productions/production_transfer/production_transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_transfer/production_transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
