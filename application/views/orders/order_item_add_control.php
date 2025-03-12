<?php if($order->is_pre_order) : ?>
  <div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getPreorderItem()">รายการสินค้า</button>
    </div>
  </div>
  <div class="divider-hidden"> </div>


  <div class="modal fade" id="preOrderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog" style="width:500px; max-width:90vw;">
  		<div class="modal-content">
    			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  				<h4 class="modal-title">Pre Order Items</h4>
  			 </div>
  			 <div class="modal-body">
           <div class="row">
             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="max-height:70vh; overflow:auto;">
               <table class="table table-striped border-1">
                 <thead>
                   <tr>
                     <th class="min-width-200">รหัส</th>
                     <th class="fix-width-100 text-center">จำนวน</th>
                   </tr>
                 </thead>
                 <tbody id="preOrderTable">

                 </tbody>
               </table>
             </div>
           </div>
         </div>
  			 <div class="modal-footer">
  				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
  				<button type="button" class="btn btn-primary" onClick="addPreOrderItems()" >เพิ่มในรายการ</button>
  			 </div>
  		</div>
  	</div>
  </div>

<script id="preOrderTemplate" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr>
      <td class="middle">{{product_code}}</td>
      <td class="middle text-center">
        <input type="number" class="form-control input-sm text-center pre-qty" id="pd-{{id}}" data-id="{{id}}" data-pd="{{product_code}}" value="" />
      </td>
    </tr>
  {{/each}}
</script>

<?php else : ?>

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
	<!-- <div class="col-sm-1 col-1-harf col-xs-8 padding-5 margin-bottom-10 not-show">
		<input type="text" class="form-control input-sm text-center" id="qt_no"	name="qty_no" placeholder="Quotation" value="<?php echo $order->quotation_no; ?>"	<?php echo $qt; ?>>
	</div> -->
	<!-- <div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10 not-show">
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-qt-no"	<?php if($asq) : ?>	onclick="get_quotation()" <?php endif; ?>	<?php echo $qt; ?>	>Add</button>
	</div> -->
	<div class="col-sm-2 col-2-harf col-xs-8 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="Model Code" autofocus />
  </div>
  <div class="col-sm-1 col-1-harf col-xs-4 padding-5 margin-bottom-10">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">OK</button>
  </div>

	<div class="divider visible-xs">			</div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf not-show hidden-xs">&nbsp;  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-2-harf col-xs-12 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="SKU Code">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="stock-qty" placeholder="Stock" disabled>
  </div>
  <div class="col-lg-1 col-sm-1 col-xs-3 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="input-qty" placeholder="Qty">
  </div>
  <div class="col-lg-1 col-sm-1 col-xs-3 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">Add</button>
  </div>
  <div class="col-lg-1 col-sm-1 col-xs-3 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="addFreeItemToOrder()">Free</button>
  </div>
</div>
<?php endif;  //--- end if( $order->is_pre_order) ?>
<hr/>
<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:300px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="max-height:60vh; padding:0; overflow:auto;" id="modalBody">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>

<input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>">
