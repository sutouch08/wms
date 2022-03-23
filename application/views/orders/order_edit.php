<?php $this->load->view('include/header'); ?>
<?php $isAdmin = (get_cookie('id_profile') == -987654321 ? TRUE : FALSE); ?>

<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 padding-5 hidden-xs">
    <h3 class="title" style="margin-top:6px;"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 text-center visible-xs" style="background-color:#eee;">
		<h3 class="margin-top-0"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
    	<p class="pull-right top-p text-right" >
				<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($order->is_term == 0 && $order->status == 1 && $order->state < 3 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-xs btn-info top-btn" onclick="payOrder()">แจ้งชำระเงิน</button>
				<?php endif; ?>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<!--<button type="button" class="btn btn-xs btn-grey top-btn" onClick="inputDeliveryNo()">บันทึกการจัดส่ง</button>-->
				<?php endif; ?>
				<button type="button" class="btn btn-xs btn-purple top-btn" onclick="getSummary()">สรุปข้อมูล</button>
				<button type="button" class="btn btn-xs btn-default top-btn hidden-xs" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php if($isAdmin && $order->state < 4 && $order->never_expire == 0 && $order->is_expired == 0) : ?>
				<button type="button" class="btn btn-xs btn-primary top-btn" onclick="setNotExpire(1)">ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($isAdmin && $order->never_expire == 1 && $order->is_expired == 0) : ?>
					<button type="button" class="btn btn-xs btn-info top-btn" onclick="setNotExpire(0)">ไม่ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($isAdmin && $order->is_expired == 1) : ?>
								<button type="button" class="btn btn-xs btn-warning top-btn" onclick="unExpired()">ทำให้ไม่หมดอายุ</button>
				<?php endif; ?>

				<?php if((($order->is_wms == 0 && $order->state < 4) OR ($order->is_wms == 1 && $order->state < 3))) : ?>
				 	<?php if( $order->is_expired == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
						<button type="button" class="btn btn-xs btn-yellow top-btn" onclick="editDetail()">แก้ไขรายการ</button>
					<?php endif; ?>
				<?php endif; ?>

				<?php if($order->status == 0 && $order->is_expired == 0) : ?>
					<button type="button" class="btn btn-xs btn-success top-btn" onclick="saveOrder()">บันทึก</button>
				<?php endif; ?>
				<?php if($this->isAPI && $order->is_wms && $order->status == 1 && $order->is_expired == 0 && $order->state == 3) : ?>
					<button type="button" class="btn btn-xs btn-success top-btn" onclick="sendToWMS()">Send to WMS</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<?php if($this->isAPI && $order->is_wms && $order->state >= 3 && $order->wms_export != 1) : ?>
<?php 	$this->load->view('wms_error_watermark'); ?>
<?php endif; ?>
<?php $this->load->view('orders/order_edit_header'); ?>
<?php $this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_discount_bar'); ?>
<?php $this->load->view('orders/order_detail'); ?>
<?php $this->load->view('orders/order_online_modal'); ?>
<script src="<?php echo base_url(); ?>assets/js/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<?php if($order->is_wms && $order->status == 1 && $order->is_expired == 0 && $order->state == 3) : ?>
	<script src="<?php echo base_url(); ?>scripts/wms/wms_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php endif; ?>

<?php $this->load->view('include/footer'); ?>
