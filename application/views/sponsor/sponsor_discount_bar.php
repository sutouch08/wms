<div class="row">
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5 margin-top-5 margin-bottom-5">
		<?php if(!$order->is_expired && !$order->is_approved) : ?>
			<?php if($allowEditPrice) : ?>
				<button type="button" class="btn btn-sm btn-default" id="btn-edit-price" onClick="showPriceBox()">แก้ไขราคา</button>
				<button type="button" class="btn btn-sm btn-primary hide" id="btn-update-price" onClick="getApprove('price')">บันทึกราคา</button>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="col-lg-11 col-md-10 col-sm-10 col-xs-9 padding-5">
		<?php if($is_api && $order->is_wms != 0 && $order->is_api == 0 && $order->state >= 3 && $order->state != 9 && $order->wms_export != 1) : ?>
			<?php 	$this->load->view('wms_error_watermark'); ?>
		<?php endif; ?>
		<?php if($is_api && $order->is_wms == 2 && $order->is_api == 0 && $order->state == 3 && $order->wms_export = 1 && $order->is_backorder == 1) : ?>
			<?php 	$this->load->view('backorder_watermark'); ?>
		<?php endif; ?>
	</div>
</div>
<hr/>

<?php $this->load->view('validate_credentials'); ?>

<script src="<?php echo base_url(); ?>scripts/orders/order_discount.js?v=<?php echo date('Ymd'); ?>"></script>
