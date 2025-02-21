<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('transfer/mobile/style'); ?>
<?php $this->load->view('transfer/mobile/process_style'); ?>
<?php $this->load->view('transfer/mobile/header_mobile'); ?>
<?php $this->load->view('transfer/mobile/detail_mobile'); ?>
<?php $this->load->view('transfer/mobile/process_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/transfer/mobile/transfer_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/mobile/transfer_control_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
