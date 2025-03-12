<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/receive_po/mobile/style'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<?php $this->load->view('inventory/receive_po/mobile/filter'); ?>

<div class="move-list">
	<?php if( ! empty($document)) : ?>
		<?php $no = $this->uri->segment(4) + 1; ?>
		<?php foreach($document as $rs) : ?>
			<div class="move-list-item">
				<div class="col-xs-1-harf text-center bold padding-5 move-list-status">
					<?php if($rs->is_expire OR $rs->status == 2) : ?>
						<?php if($rs->status == 2) : ?>
							<span class="red"><strong>CN</strong></span>
						<?php else : ?>
							<span class="dark">EXP</span>
						<?php endif; ?>
					<?php else : ?>
						<?php if($rs->status == 0 ) : ?>
							<span class="blue"><strong>DF</strong></span>
						<?php endif; ?>
						<?php if($rs->status == 3 ) : ?>
							<span class="purple"><strong>OP</strong></span>
						<?php endif; ?>
						<?php if($rs->status == 4) : ?>
							<span class="orange"><strong>WC</strong></span>
						<?php endif; ?>
						<?php if($rs->status == 1) : ?>
							<span class="green"><strong>OK</strong></span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="col-xs-9 padding-5" style="overflow:auto;">
					<p class="move-list-line bold">
						<?php echo $rs->code; ?>
						<?php echo (empty($rs->po_code) ? "" : "&nbsp;&nbsp;[PO".$rs->po_code."]"); ?>
					</p>
					<p class="move-list-line bold"><?php echo $rs->vendor_name; ?></p>
					<p class="move-list-line bold">วันที่ : <?php echo thai_date($rs->date_add, FALSE,'/'); ?>&nbsp;&nbsp;Invoice : <?php echo $rs->invoice_code; ?></p>
					<p class="move-list-line bold">โซน : <?php echo $rs->zone_code; ?></p>
				</div>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<?php if($rs->status == 3) : ?>
						<a class="move-list-link font-size-24" href="javascript:processMobile('<?php echo $rs->code; ?>')"><i class="fa fa-angle-right"></i></a>
					<?php else : ?>
						<a class="move-list-link font-size-24" href="javascript:viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-angle-right"></i></a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php $this->load->view('inventory/receive_po/mobile/list_menu'); ?>


<script>
	$('#warehouse').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>

<script>
	function toggleFilter() {
		let filter = $('#filter-pad');

		if(filter.hasClass('move-in')) {
			filter.removeClass('move-in');
		}
		else {
			filter.addClass('move-in');
		}
	}


	function closeFilter() {
		$('#filter-pad').removeClass('move-in');
	}
</script>

<?php $this->load->view('include/footer'); ?>
