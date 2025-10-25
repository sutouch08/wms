<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/prepare/style'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right hidden-xs">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-external-link-square"></i> รายการรอจัด</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="goProcess()"><i class="fa fa-external-link-square"></i> รายการกำลังจัด</button>
	</div>
</div><!-- End Row -->
<hr class=""/>


<div class="row">
	<div class="col-lg-3 col-md-4 col-sm-4 padding-5 hidden-xs">
		<div class="input-group width-100">
			<span class="input-group-addon">จัดออเดอร์</span>
			<input type="text" class="form-control input-sm text-center" id="order-code" autofocus />
		</div>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5 hidden-xs">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="goToProcess('ex')">จัดสินค้า</button>
	</div>
</div>
<hr class="margin-top-15 hidden-xs">

<div class="pg-footer visible-xs">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu width-20">
				<span class="width-100" onclick="refresh()">
					<i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="goToBuffer()">
					<i class="fa fa-history fa-2x white"></i><span class="fon-size-12">Buffer</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="goBack()">
					<i class="fa fa-server fa-2x white"></i><span class="fon-size-12">รอจัด</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="goProcess()">
					<i class="fa fa-server fa-2x white"></i><span class="fon-size-12">กำลังจัด</span>
				</span>
			</div>
		</div>
		<input type="hidden" id="filter" value="hide" />
 </div>
</div>

<div class="extra-menu slide-out slide-in visible-xs" id="extra-menu">
	<div class="width-100">
		<span class="width-100">
			<input type="text" class="form-control input-lg focus"
			style="padding-left:15px; padding-right:40px;" id="barcode-order" inputmode="none" placeholder="Barcode Order" autocomplete="off" autofocus>
			<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:20px; right:22px; color:grey;"></i>
		</span>
	</div>
</div>

<input type="hidden" id="ex" value="1" />

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<?php $this->load->view('include/footer'); ?>
