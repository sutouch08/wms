<?php $hide = "not-show"; ?>
<?php if(empty($order->has_payment) && !$order->is_paid && !$order->is_expired && !$order->is_approved) : ?>
	<?php $hide = ""; ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-12 margin-top-5 padding-5 margin-bottom-5 <?php echo $hide; ?>">
		<?php if($allowEditPrice) : ?>
      <button type="button" class="btn btn-xs btn-purple" id="btn-edit-price" onClick="showPriceBox()">แก้ไขราคา</button>
      <button type="button" class="btn btn-xs btn-primary hide" id="btn-update-price" onClick="getApprove('price')">บันทึกราคา</button>
		<?php endif; ?>

		<?php if( $allowEditDisc && ($order->role == 'S' OR $order->role == 'C') OR $order->role == 'N') : ?>
    	<button type="button" class="btn btn-xs btn-yellow" id="btn-edit-discount" onclick="showDiscountBox()">
				<?php if($order->role == 'C' OR $order->role == 'N') : ?>
					แก้ไข GP
				<?php else : ?>
					แก้ไขส่วนลด
				<?php endif; ?>
			</button>
      <button type="button" class="btn btn-xs btn-primary hide" id="btn-update-discount" onClick="getApprove('discount')">
				<?php if( $order->role == 'C' OR $order->role == 'N') : ?>
					บันทึก GP
				<?php else : ?>
					บันทึกส่วนลด
				<?php endif; ?>
			</button>
		<?php endif; ?>

    </div>
		<div class="col-lg-6 col-md-5-harf col-sm-4-harf col-xs-12 text-center padding-5">
			<?php if($is_api && $order->is_wms != 0 && $order->is_api == 0 && $order->state >= 3 && $order->state != 9 && $order->wms_export != 1) : ?>
				<?php 	$this->load->view('wms_error_watermark'); ?>
			<?php endif; ?>			
		</div>

		<?php if($order->role == 'S') : ?>
			<?php $disabled = $order->state < 3 ? '' : 'disabled'; ?>
		<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-8 margin-top-5 padding-5 margin-bottom-5">
			<div class="input-group">
				<span class="input-group-addon" style="font-size:11px; line-height:0;">COD Amount</span>
				<input type="number" class="form-control input-sm text-center" id="cod-amount" name="cod-amount" value="<?php echo $order->cod_amount; ?>" <?php echo $disabled; ?>/>
			</div>
		</div>

		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5" style="padding-top:5px;">
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="submitCod()" <?php echo $disabled; ?>>บันทึก</button>
		</div>
	<?php endif; ?>
</div>
<hr/>

<?php $this->load->view('validate_credentials'); ?>

<script src="<?php echo base_url(); ?>scripts/orders/order_discount.js?v=<?php echo date('Ymd'); ?>"></script>
