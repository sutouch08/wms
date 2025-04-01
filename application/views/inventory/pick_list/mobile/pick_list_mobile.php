<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/pick_list/mobile/style'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<?php $this->load->view('inventory/pick_list/mobile/filter'); ?>

<div class="move-list">
	<?php if( ! empty($list)) : ?>
		<?php $no = $this->uri->segment(4) + 1; ?>
		<?php foreach($list as $rs) : ?>
			<?php $co = $rs->status == 'Y' ? 'background-color:#f7d6f6;' : ''; ?>
			<div class="move-list-item" style="<?php echo $co; ?>">
				<div class="col-xs-1-harf text-center bold padding-5" style="height:60px; display:grid; align-items:center;">
					<span class="blue"><?php echo $rs->status;?></span>
				</div>
				<div class="col-xs-8-harf padding-5 font-size-11">
					<p class="move-list-line bold font-size-14"> <?php echo $rs->code; ?></p>
					<p class="move-list-line bold">วันที่:<?php echo thai_date($rs->date_add, FALSE,'/'); ?>&nbsp;&nbsp;&nbsp;ช่องทาง: <?php echo ( ! empty($rs->channels_code) ? $this->channels_model->get_name($rs->channels_code) : ""); ?></p>
					<p class="move-list-line bold">ปลายทาง: <?php echo $this->zone_model->get_name($rs->zone_code); ?>&nbsp;&nbsp;ต้นทาง: <?php echo $rs->warehouse_code; ?></p>
				</div>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<a class="move-list-link font-size-24" style="display:grid; align-items:center;" href="javascript:goProcess('<?php echo $rs->code; ?>')"><i class="fa fa-angle-right"></i></a>
				<?php endif; ?>
			</div>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php $this->load->view('inventory/pick_list/mobile/list_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/mobile/pick_list_mobile.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
