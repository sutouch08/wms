<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('move/mobile/style'); ?>
<?php $this->load->view('move/mobile/process_style'); ?>
<?php $this->load->view('move/mobile/edit_header_mobile'); ?>
<?php $this->load->view('move/mobile/move_detail_mobile'); ?>
<?php $this->load->view('move/mobile/process_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/move/mobile/move_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/mobile/move_control_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
