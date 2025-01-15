<?php $this->load->view('include/header'); ?>
<?php
$add = $this->pm->can_add;
$edit = $this->pm->can_edit;
$delete = $this->pm->can_delete;
$hide = $order->status == 1 ? 'hide' : '';
 ?>
 <style>
 .tableFixHead {
 	table-layout: fixed;
 	min-width: 100%;
 	margin-bottom: 0;
 }

 .tableFixHead thead th {
 	position: sticky;
 	top: -1px;
 	background-color: #f2f2f2;
 	font-size: 12px;
 }

 .fix-header {
   z-index: 50;
   background-color: white;
   outline: solid 1px #dddddd;
 }

 @media (min-width: 768px) {
   .fix-size {
     left: 0;
     position: sticky;
     background-color: #eee !important;
   }


   td[scope=row] {
     background-color: white;
     border: 0 !important;
     outline: solid 1px #dddddd;
   }
 }

 </style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-9 padding-5">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-xs btn-warning" onClick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
					<button type="button" class="btn btn-xs btn-info" onclick="recalDiscount()">คำนวณส่วนลดใหม่</button></button>
      <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
          <button type="button" class="btn btn-xs btn-success <?php echo $hide; ?>" id="btn-save-order" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
      <?php endif; ?>
        </p>
    </div>
</div>
<hr class="margin-bottom-15" />
<?php $this->load->view('orders/order_edit_detail_header'); ?>

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
	<div class="col-sm-1 col-1-harf col-xs-8 padding-5 margin-bottom-10">
		<input type="text" class="form-control input-sm text-center" id="qt_no"	name="qty_no" placeholder="Quotation" value="<?php echo $order->quotation_no; ?>"	<?php echo $qt; ?>>
	</div>
	<div class="col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-qt-no"	<?php if($asq) : ?>	onclick="get_quotation()" <?php endif; ?>	<?php echo $qt; ?>	>Add</button>
	</div>
	<div class="col-sm-2 col-2-harf col-xs-8 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="Model Code" autofocus />
  </div>
  <div class="col-sm-1 col-1-harf col-xs-4 padding-5 margin-bottom-10">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">OK</button>
  </div>

	<div class="divider visible-xs">			</div>
  <div class="col-sm-2 col-2-harf col-xs-6 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="SKU Code">
  </div>
  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="stock-qty" placeholder="Stock" disabled>
  </div>
  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="input-qty" placeholder="Qty">
  </div>
  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">Add</button>
  </div>
</div>
<?php if($order->role == 'S') : ?>
  <?php $disabled = $order->state < 3 ? '' : 'disabled'; ?>
  <div class="divider"> </div>
  <div class="row">
    <div class="col-lg-8-harf col-md-7-harf col-sm-6-harf">&nbsp;</div>
    <div class="col-lg-2-harf col-md-3 col-sm-4 col-xs-8 margin-top-5 padding-5 margin-bottom-5">
      <div class="input-group">
        <span class="input-group-addon">COD Amount</span>
        <input type="number" class="form-control input-sm text-center" id="cod-amount" name="cod-amount" value="<?php echo $order->cod_amount; ?>" <?php echo $disabled; ?>/>
      </div>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5" style="padding-top:5px;">
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="submitCod()" <?php echo $disabled; ?>>บันทึก</button>
    </div>
  </div>
<?php endif; ?>

<hr class="margin-top-15 margin-bottom-0 visible-lg" />
<!--- Category Menu ---------------------------------->
<!-- <div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="widget-box widget-color-blue2 collapsed" onclick="toggleCate()" id="cate-widget">
			<div class="widget-header widget-header-small">
				<h6 class="widget-title">Categories</h6>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<ul class='nav navbar-nav' role='tablist' style="float:none;">
					<?php //echo productTabMenu('order'); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<hr class=""/>
<div class='row'>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class='tab-content' style="min-height:1px; padding:0px; border:0px;">
		<?php //echo getProductTabs(); ?>
		</div>
	</div>
</div> -->
<!-- End Category Menu ------------------------------------>
<?php endif;  //--- end if( $order->is_pre_order) ?>

<?php $this->load->view('orders/order_detail');  ?>

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:60vh; padding:0; overflow:auto;" id="modalBody">

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
<script>
	function toggleCate() {
		if($('#cate-widget').hasClass('collapsed')) {
			$('#cate-widget').removeClass('collapsed');
		}
		else {
			$('#cate-widget').addClass('collapsed');
		}
	}

  function getPreorderItem() {
    load_in();

    $.ajax({
      url:BASE_URL + 'orders/pre_order_policy/get_active_items',
      type:'GET',
      cache:false,
      success:function(rs) {
        load_out();
        if( isJson(rs)) {
          let ds = JSON.parse(rs);
          let source = $('#preOrderTemplate').html();
          let output = $('#preOrderTable');

          render(source, ds, output);

          $('#preOrderModal').modal('show');
        }
        else {
          swal({
            title:'Not found',
            text:'ไม่พบรายการสินค้าที่เปิด Pre Order',
            type:'info'
          });
        }
      }
    })
  }


  function addPreOrderItems() {
    let order_code = $('#order_code').val();
    let items = [];

    $('.pre-qty').each(function() {
      if($(this).val() != '') {
        let qty = parseDefault(parseFloat($(this).val()), 0);
        let code = $(this).data('pd');
        let id = $(this).data('id'); //pre_order_detail_id

        if(qty > 0) {
          items.push({"id" : id, "code" : code, "qty" : qty});
        }
      }
    });

    if(items.length > 0) {
      $('#preOrderModal').modal('hide');

      load_in();

      $.ajax({
        url:BASE_URL + 'orders/orders/add_pre_order_detail',
        type:'POST',
        cache:false,
        data:{
          'order_code' : order_code,
          'data' : JSON.stringify(items)
        },
        success:function(rs) {
          load_out();

          if(rs === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }
          else {
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            })
          }
        }
      })
    }
  }
</script>


<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('YmdH'); ?>"></script>



<?php $this->load->view('include/footer'); ?>
