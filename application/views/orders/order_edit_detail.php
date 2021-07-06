<?php $this->load->view('include/header'); ?>
<?php
$add = $this->pm->can_add;
$edit = $this->pm->can_edit;
$delete = $this->pm->can_delete;
$hide = $order->status == 1 ? 'hide' : '';
 ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-warning" onClick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
					<button type="button" class="btn btn-sm btn-info" onclick="recalDiscount()">
			        <i class="fa fa-calculator"></i> คำนวณส่วนลดใหม่</button>
			      </button>
      <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
          <button type="button" class="btn btn-sm btn-success <?php echo $hide; ?>" id="btn-save-order" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
      <?php endif; ?>
        </p>
    </div>
</div>
<hr class="margin-bottom-15" />
<?php $this->load->view('orders/order_edit_detail_header'); ?>

<?php
		$asq = getConfig('ALLOW_LOAD_QUOTATION');
		$qt =  'disabled';
		if($asq && $order->state < 4 && $order->is_expired == 0 && ($this->pm->can_add OR $this->pm->can_edit))
		{
			$qt = '';
		}
?>
<!--  Search Product -->
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-4 padding-5">
		<input type="text"
		class="form-control input-sm text-center"
		id="qt_no"
		name="qty_no"
		placeholder="ใบเสนอราคา"
		value="<?php echo $order->quotation_no; ?>"
		<?php echo $qt; ?>>
	</div>
	<div class="col-sm-1 col-xs-4 padding-5">

		<button type="button"
		class="btn btn-xs btn-primary btn-block"
		id="btn-qt-no"
		<?php if($asq) : ?>
		onclick="get_quotation()"
	<?php endif; ?>
		<?php echo $qt; ?>
		>ดึงรายการ</button>

	</div>
	<div class="col-sm-2 col-2-harf col-xs-12 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรหัสสินค้า" />
  </div>
  <div class="col-sm-1 col-1-harf col-xs-12 padding-5 margin-bottom-10">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>
  <div class="col-sm-2 col-2-harf col-xs-12 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="ค้นหารหัสสินค้า">
  </div>
  <div class="col-sm-1 col-xs-6 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="stock-qty" disabled>
  </div>
  <div class="col-sm-1 col-xs-6 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="input-qty">
  </div>
  <div class="col-sm-1 col-xs-12 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">เพิ่ม</button>
  </div>
</div>

<hr class="margin-top-15 margin-bottom-0" />
<!--- Category Menu ---------------------------------->
<div class="row">
	<div class="col-sm-12 padding-5">
		<ul class='nav navbar-nav' role='tablist' style='background-color:#EEE'>
		<?php echo productTabMenu('order'); ?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr />
<div class='row'>
	<div class='col-sm-12 padding-5'>
		<div class='tab-content' style="min-height:1px; padding:0px; border:0px;">
		<?php echo getProductTabs(); ?>
		</div>
	</div>
</div>
<!-- End Category Menu ------------------------------------>

<?php $this->load->view('orders/order_detail'); //include 'include/order/order_detail.php'; ?>


<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal">
		<div class="modal-content" style="position:relative; min-height: 100px; min-width:250px; max-height:900px; max-width:1200px; overflow-x:scroll; overflow-y:scroll;">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>
<input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>">
<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js"></script>

<?php $this->load->view('include/footer'); ?>
