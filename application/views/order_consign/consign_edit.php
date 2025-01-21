<?php $this->load->view('include/header'); ?>
<?php $allow_upload = getConfig('ALLOW_IMPORT_WT'); ?>
<?php $cim = get_permission('SOIMWT', $this->_user->uid, $this->_user->id_profile); ?>
<?php $can_upload = (is_true($allow_upload) && can_do($cim)) ? TRUE : FALSE; ?>

<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5 padding-top-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-5 text-right">
<?php if(empty($approve_view)) : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($order->state < 4 && $order->is_approved == 0 && $order->is_expired == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-white btn-yellow top-btn" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
			<?php endif; ?>

			<?php if($order->status == 0) : ?>
				<button type="button" class="btn btn-white btn-success top-btn" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
			<?php endif; ?>
		<?php if($order->state == 1 && $order->is_approved == 0 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
		<?php endif; ?>
		<?php if($order->state == 1 && $order->is_approved == 1 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="unapprove()"><i class="fa fa-refresh"></i> ยกเลิกอนุมัติ</button>
		<?php endif; ?>
		<?php if($is_api && $order->is_wms != 0 && $order->status == 1 && $order->is_expired == 0 && $order->state == 3) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToWMS()">Send to WMS</button>
		<?php endif; ?>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				<i class="ace-icon fa fa-list icon-on-left"></i>ตัวเลือก
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="primary">
					<a href="javascript:printOrderSheet()"><i class="fa fa-print"></i> &nbsp; พิมพ์ใบส่งของ</a>
				</li>

				<?php if($order->state < 3 && $order->status != 2 && ($this->pm->can_add OR $this->pm->can_edit) && $can_upload) : ?>
					<li class="success">
						<a href="javascript:getUploadFile()"><i class="fa fa-upload"></i> &nbsp; Import Excel</a>
					</li>					
				<?php endif; ?>
				<li class="purple">
					<a href="javascript:getTemplate()"><i class="fa fa-download"></i> &nbsp; ไฟล์ Template</a>
				</li>

				<?php if($order->state < 4 && $this->_SuperAdmin && $order->is_expired == 0) : ?>
					<?php if($order->never_expire == 0) : ?>
						<li class="primary">
							<a href="javascript:setNotExpire(1)"><i class="fa fa-print"></i> &nbsp; ยกเว้นการหมดอายุ</a>
						</li>
					<?php else : ?>
						<li class="primary">
							<a href="javascript:setNotExpire(0)"><i class="fa fa-print"></i> &nbsp; ไม่ยกเว้นการหมดอายุ</a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if($this->_SuperAdmin && $order->is_expired == 1) : ?>
					<li class="warning">
						<a href="javascript:unExpired()"><i class="fa fa-print"></i> &nbsp; ทำให้ไม่หมดอายุ</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
<?php endif; ?>
    </div>
</div><!-- End Row -->
<hr/>
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="customerCode" value="<?php echo $order->customer_code; ?>" />

<?php $this->load->view('order_consign/consign_edit_header'); ?>

<?php if(empty($approve_view)) : ?>
<?php $this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_discount_bar'); ?>
<?php $this->load->view('orders/order_online_modal'); ?>
<?php else : ?>
	<input type="hidden" id="id_sender" value="<?php echo $order->id_sender; ?>"/>
	<input type="hidden" id="id_address" value="<?php echo $order->id_address; ?>"/>
<?php endif; ?>
<?php $this->load->view('order_consign/consign_detail'); ?>

<?php if(!empty($approve_logs)) : ?>
	<div class="row">
		<?php foreach($approve_logs as $logs) : ?>
		<div class="col-sm-12 text-right padding-5 first last">
			<?php if($logs->approve == 1) : ?>
			  <span class="green">
					อนุมัติโดย :
					<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
				</span>
			<?php else : ?>
				<span class="red">
				ยกเลิกโดย :
				<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
			  </span>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php $this->load->view('order_consign/import_order'); ?>


<?php if($this->menu_code == 'SOCCSO') : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_so.js?v=<?php echo date('Ymd'); ?>"></script>
<?php else : ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_tr.js?v=<?php echo date('Ymd'); ?>"></script>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_consign/consign_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/cancel_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php if($order->is_wms && $order->status == 1 && $order->is_expired == 0 && $order->state == 3) : ?>
	<script src="<?php echo base_url(); ?>scripts/wms/wms_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php endif; ?>

<?php $this->load->view('include/footer'); ?>
