<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_receipt/style'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($doc->Status != 'D') : ?>
			<button type="button" class="btn btn-white btn-info top-btn" onclick="printIssue('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i> Print</button>
		<?php endif; ?>
		<?php if($doc->Status != 'D' && $this->pm->can_delete) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="goCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> Cancel</button>
		<?php endif; ?>
		<?php if($doc->Status == 'C') : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send to SAP</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_receipt/production_receipt_view_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_receipt/production_receipt_view_header_right'); ?>
</div>
<hr class="padding-5">
<?php if($doc->Status == 'D') { $this->load->view('cancle_watermark'); } ?>

<?php $this->load->view('productions/production_receipt/production_receipt_view_details'); ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/productions/production_receipt/production_receipt.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_receipt/production_receipt_view.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
