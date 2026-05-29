<?php $this->load->view('include/header');  ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
</div>
<hr class="margin-bottom-15 padding-5" />
<!--  Search Product -->
<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm" name="warehouse" id="warehouse">
      <option value="">เลือกคลัง</option>
      <?php echo select_sell_warehouse(); ?>
    </select>
  </div>
	<div class="divider-hidden visible-xs"></div>
	<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 padding-5">
		<label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรหัสสินค้า" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12 padding-5">
		<label class="display-block not-show">btn</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-0" />

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="max-width:95%;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="modalBody">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			 </div>
		</div>
	</div>
</div>
</form>


<input type="hidden" name="view" id="view" value="1">

<script>
	$('#warehouse').select2();	

	function availableDetails(code, warehouse)
	{
		const width = 400;
		const height = 500;
		const left = (window.screen.width / 2) - (width / 2);
		const top = (window.screen.height / 2) - (height / 2);		
		const url = `${BASE_URL}view_stock/available_details/${code}/${warehouse}?nomenu`;
		window.open(url, '_blank', `width=${width},height=${height},top=${top},left=${left}`);
	}
</script>

<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
