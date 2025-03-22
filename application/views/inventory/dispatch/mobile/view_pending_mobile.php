<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/dispatch/mobile/style'); ?>
<?php $this->load->view('inventory/dispatch/mobile/process_style'); ?>
<div class="goback">
  <a class="goback-icon pull-left" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-angle-left fa-2x"></i></a>
</div>
<div class="pending-box">
  <?php  if( ! empty($orders)) : ?>
		<?php $channels = get_channels_array(); ?>
    <?php $no = 1; ?>
		<?php $totalQty = 0; ?>
    <?php   foreach($orders as $rs) : ?>
			<?php $channels_name = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?>
			<?php $qty = $this->dispatch_model->count_order_box($rs->code); ?>
			<div class="width-100 incomplete-item">
				<div class="row" style="padding: 3px 3px 3px 10px;">
					<div class="col-xs-4"><?php echo $rs->code; ?></div>
					<div class="col-xs-5"><?php echo $rs->reference; ?></div>
					<div class="col-xs-3 text-right"><?php echo $qty; ?> กล่อง</div>
					<div class="col-xs-4 hide-text"><?php echo $channels_name; ?></div>
					<div class="col-xs-8 hide-text"><?php echo $rs->customer_name; ?></div>
				</div><!-- item -->
			</div>
      <?php $no++; ?>
			<?php $totalQty += $qty; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="width-100 text-center" style="position: fixed; left:0; bottom:0; height:55px; padding:10px; background-color:black; color:white; font-size:20px; z-index:8;">
	Total &nbsp;&nbsp;<?php echo number($totalQty); ?>&nbsp;&nbsp; กล่อง
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_mobile.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
