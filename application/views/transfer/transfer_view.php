<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-3">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		    <?php if($doc->status == 1) : ?>
					<button type="button" class="btn btn-sm btn-danger" onclick="unSave()"><i class="fa fa-exclamation-triangle"></i> ยกเลิกการบันทึก</button>
		      <button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
		    <?php endif; ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="printTransfer()"><i class="fa fa-print"></i> พิมพ์</button>
      </p>
    </div>
</div><!-- End Row -->
<input type="hidden" id="transfer_code" name="transfer_code" value="<?php echo $doc->code; ?>" />
<hr/>
<?php
	$this->load->view('transfer/transfer_view_header');
	$this->load->view('transfer/transfer_view_detail');
?>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_control.js"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js"></script>

<?php $this->load->view('include/footer'); ?>
