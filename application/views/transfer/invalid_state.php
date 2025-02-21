<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center" style="margin-top:50px;">
		<h1><i class="fa fa-frown-o"></i></h1>
		<h3>Oops.. Something went wrong.</h3>
		<h4>สถานะเอกสาร ไม่อยู่ในสถานะที่สามารถดำเนินการได้ โปรดตรวจสอบสถานะก่อนดำเนินการต่อไป</h4>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="text-center">
			<button class="btn btn-white btn-default btn-100" onclick="goBack()"><i class="fa fa-arrow-left"></i> Go Back</button>
		</div>
	</div>
</div>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
