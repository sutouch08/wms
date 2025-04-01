<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/pick_list/mobile/style'); ?>
<?php $this->load->view('inventory/pick_list/mobile/process_style'); ?>
<?php $this->load->view('inventory/pick_list/mobile/pick_header_mobile'); ?>
<?php $this->load->view('inventory/pick_list/mobile/pick_control_mobile');?>
<?php $this->load->view('inventory/pick_list/mobile/incomplete_list_mobile'); ?>
<?php $this->load->view('inventory/pick_list/mobile/complete_list_mobile'); ?>
<?php $this->load->view('inventory/pick_list/mobile/transection_list_mobile'); ?>
<?php $this->load->view('inventory/pick_list/mobile/process_menu'); ?>


<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/mobile/pick_list_mobile.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/mobile/pick_process_mobile.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
