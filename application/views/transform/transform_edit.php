<?php $this->load->view('include/header'); ?>
<?php $isAdmin = (get_cookie('id_profile') == -987654321 ? TRUE : FALSE); ?>
<div class="row">
	<div class="col-sm-3 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9 col-xs-12 padding">
    	<p class="pull-right top-p">
				<?php if(empty($approve_view)) : ?>
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<button type="button" class="btn btn-sm btn-default" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php endif; ?>

			<?php if(empty($approve_view)) : ?>
				<?php if($order->state < 4 && $isAdmin && $order->never_expire == 0) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="setNotExpire(1)">ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($order->state < 4 && $isAdmin && $order->never_expire == 1) : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="setNotExpire(0)">ไม่ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($isAdmin && $order->is_expired == 1) : ?>
					<button type="button" class="btn btn-sm btn-warning" onclick="unExpired()">ทำให้ไม่หมดอายุ</button>
				<?php endif; ?>
				<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-yellow" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
				<?php endif; ?>
				<?php if($order->status == 0) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
			<?php endif; ?>

				<?php if($order->state == 1 && $order->is_approved == 0 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
						<button type="button" class="btn btn-sm btn-success" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
				<?php endif; ?>
				<?php if($order->state == 1 && $order->is_approved == 1 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
						<button type="button" class="btn btn-sm btn-danger" onclick="unapprove()"><i class="fa fa-refresh"></i> ไม่อนุมัติ</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<input type="hidden" id="id_order" name="id_order" value="<?php echo $order->code; ?>" />
<?php $this->load->view('transform/transform_edit_header'); ?>
<?php if(empty($approve_view)) : ?>
<?php $this->load->view('orders/order_state'); ?>
<?php endif; ?>
<?php $this->load->view('transform/transform_detail'); ?>


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


<script src="<?php echo base_url(); ?>scripts/transform/transform.js"></script>
<script src="<?php echo base_url(); ?>scripts/transform/transform_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/transform/transform_detail.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>

<?php $this->load->view('include/footer'); ?>
