<?php $this->load->view('include/header');  ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<table class="table table-bordered">
			<tr><td class="fix-width-100">SKU</td><td><?php echo $item_code; ?></td></tr>
			<tr><td class="fix-width-100">Warehouse</td><td><?php echo $warehouse; ?></td></tr>
			<tr><td class="fix-width-100">Stock</td><td><?php echo number($sell_stock, 2); ?></td></tr>
			<tr><td class="fix-width-100">Ordered</td><td><?php echo number($ordered, 2); ?></td></tr>
			<tr><td class="fix-width-100">Reserved</td><td><?php echo number($reserv_stock, 2); ?></td></tr>
			<tr><td class="fix-width-100">Available</td><td><?php echo number($availableStock, 2); ?></td></tr>
		</table>
	</div>		
</div>
<?php $this->load->view('include/footer'); ?>
