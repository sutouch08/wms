<?php $this->load->view('include/header'); ?>
<?php $isAdmin = (get_cookie('id_profile') == -987654321 ? TRUE : FALSE); ?>
<div class="row">
	<div class="col-sm-3">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9">
    	<p class="pull-right" style="margin-bottom:1px;">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($order->is_term == 0 && $order->status == 1 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-info" onclick="payOrder()"><i class="fa fa-credit-card"></i> แจ้งชำระเงิน</button>
				<?php endif; ?>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-grey" onClick="inputDeliveryNo()"><i class="fa fa-truck"></i> บันทึกการจัดส่ง</button>
				<?php endif; ?>
				<button type="button" class="btn btn-sm btn-purple" onclick="getSummary()"><i class="fa fa-bolt"></i> สรุปข้อมูล</button>
				<button type="button" class="btn btn-sm btn-default" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php if($isAdmin && $order->state < 4 && $order->never_expire == 0 && $order->is_expired == 0) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="setNotExpire(1)">ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($isAdmin && $order->never_expire == 1 && $order->is_expired == 0) : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="setNotExpire(0)">ไม่ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($isAdmin && $order->is_expired == 1) : ?>
								<button type="button" class="btn btn-sm btn-warning" onclick="unExpired()">ทำให้ไม่หมดอายุ</button>
				<?php endif; ?>
				<?php if($order->state < 4 && $order->is_expired == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-yellow" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
				<?php endif; ?>
				<?php if($order->status == 0 && $order->is_expired == 0) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
				<?php if($is_wms && $order->status == 1 && $order->is_expired == 0 && $order->state == 3) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="sendToWMS()">Send to WMS</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>

<?php $this->load->view('orders/order_edit_header'); ?>
<?php $this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_discount_bar'); ?>
<?php $this->load->view('orders/order_detail'); ?>
<?php $this->load->view('orders/order_online_modal'); ?>
<script src="<?php echo base_url(); ?>assets/js/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>
<?php if($is_wms && $order->status == 1 && $order->is_expired == 0 && $order->state == 3) : ?>
	<script src="<?php echo base_url(); ?>scripts/wms/wms_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php endif; ?>

<?php $this->load->view('include/footer'); ?>
