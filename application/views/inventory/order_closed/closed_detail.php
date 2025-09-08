<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <?php if(empty($approve_view)) : ?>
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php endif; ?>

    <?php if($order->role == 'N' && ($order->is_valid == '0' OR $order->is_received === NULL OR $order->is_received === 'N') ) : ?>
      <button type="button" class="btn btn-sm btn-primary" onclick="confirm_receipted()"><i class="fa fa-check"></i> ยืนยันการรับสินค้า</button>
    <?php elseif($order->role == 'N' && ($order->is_valid == '1' OR $order->is_received === 'Y')) : ?>
      <button type="button" class="btn btn-sm btn-default" disabled><i class="fa fa-check"></i> รับสินค้าแล้ว</button>
    <?php endif; ?>

    <?php if(empty($approve_view)) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="doExport()">ส่งข้อมูลไป SAP</button>
    <?php endif; ?>
  </div>
</div>
<hr/>

<?php if( $order->state == 8) : ?>
  <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  <input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
  <input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
  <div class="row">
    <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
      <label>เลขที่เอกสาร</label>
      <div class="input-group width-100">
        <input type="text" class="width-100 text-center" value="<?php echo $order->code; ?>" disabled />
        <span class="input-group-btn">
          <button type="button" class="btn btn-xs btn-info" style="height:30px;" onclick="viewOrderDetail('<?php echo $order->code; ?>', '<?php echo $order->role; ?>')" style="min-width:20px;">
            <i class="fa fa-external-link"></i>
          </button>
        </span>
      </div>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>รหัสลูกค้า</label>
      <input type="text" class="form-control input-sm text-center edit" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 padding-5">
      <label>ลูกค้า[ในระบบ]</label>
      <input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
    <div class="col-lg-3-harf col-md-6 col-sm-6 col-xs-6 padding-5">
      <label>คลัง</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->warehouse_code.' | '.$order->warehouse_name; ?>" disabled />
    </div>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>อ้างอิง</label>
      <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>อ้างอิงลูกค้า</label>
      <input type="text" class="form-control input-sm edit" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->channels_name; ?>" disabled/>
    </div>
    <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>Shop Name</label>
      <input type="text" class="form-control input-sm" value="<?php echo ( ! empty($order->shop_id) ? shop_name($order->shop_id) : NULL); ?>" disabled/>
    </div>
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>การชำระเงิน</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->payment_name; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ผู้จัดส่ง</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->sender_name; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
      <label>Tracking</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->shipping_code; ?>" disabled />
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->remark; ?>" disabled />
    </div>

    <div class="divider"></div>

    <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>ผู้ยืม/ผู้เบิก/ผู้ทำรายการ</label>
      <input type="text" class="form-control input-sm edit" value="<?php echo $order->role == 'L' ? $order->empName : (($order->role == 'T' OR $order->role == 'Q') ? $order->user_ref : NULL); ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>ผู้รับ</label>
      <input type="text" class="form-control input-sm" value="<?php echo ($order->role == 'U' OR $order->role == 'L') ? $order->user_ref : NULL; ?>" disabled />
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5">
      <label>โซนปลายทาง</label>
      <input type="text" class="form-control input-sm" value="<?php echo empty($order->zone_name) ? NULL : $order->zone_name; ?>" disabled />
    </div>

    <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
      <label>สร้างโดย</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
    </div>

    <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
      <label>แก้ไขโดย</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->update_user; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      <label class="font-size-2 blod">วันที่จัดส่ง</label>
      <div class="input-group width-100">
        <input type="text" class="form-control input-sm text-center" id="ship-date" value="<?php echo empty($order->shipped_date) ? NULL : thai_date($order->shipped_date); ?>" disabled />
        <span class="input-group-btn">
          <button type="button" class="btn btn-xs btn-warning btn-block" style="height:30px;" id="btn-edit-ship-date" onclick="activeShipDate()"><i class="fa fa-pencil" style="min-width:20px;"></i></button>
          <button type="button" class="btn btn-xs btn-success btn-block hide" style="height:30px;" id="btn-update-ship-date" onclick="updateShipDate()"><i class="fa fa-save"></i></button>
        </span>
      </div>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label class="font-size-2 blod">SAP No</label>
      <div class="input-group width-100">
        <input type="text" class="width-100 text-center" value="<?php echo $order->inv_code; ?>" disabled />
        <span class="input-group-btn">
          <button type="button" class="btn btn-xs btn-info" style="height:30px;" onclick="viewTemp('<?php echo $order->code; ?>', '<?php echo $order->role; ?>')" style="min-width:20px;">
            <i class="fa fa-external-link"></i>
          </button>
        </span>
      </div>

    </div>
  </div>
  <hr/>

  <div class="row hidden-xs">
    <div class="col-lg-12 col-md-12 col-sm-12 text-right">
      <?php if($order->channels_code == '0009' && ! empty($order->reference)) : ?>
        <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderTiktok('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> TikTok Label</button>
      <?php endif; ?>
      <?php if($order->channels_code == 'SHOPEE' && ! empty($order->reference)) : ?>
        <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderShopee('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> Shopee Label</button>
      <?php endif; ?>
      <?php if($order->channels_code == 'LAZADA' && ! empty($order->reference)) : ?>
        <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderLazada('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> Lazada Label</button>
      <?php endif; ?>
      <?php if(is_true(getConfig('PORLOR_API'))) : ?>
        <?php if($order->id_sender == getConfig('PORLOR_SENDER_ID')) : ?>
          <?php if(empty($order->shipping_code)) : ?>
            <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderPorlor('<?php echo $order->code; ?>')"><i class="fa fa-print"></i> Porlor Label</button>
          <?php else : ?>
            <button type="button" class="btn btn-white btn-info top-btn" onclick="printPorlorLabel('<?php echo $order->code; ?>')"><i class="fa fa-print"></i> Porlor Label</button>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
      <button type="button" class="btn btn-sm btn-info top-btn" onclick="printAddress('<?php echo $order->id_address; ?>', '<?php echo $order->code; ?>', '<?php echo $order->id_sender; ?>')"><i class="fa fa-print"></i> ใบนำส่ง</button>
      <button type="button" class="btn btn-sm btn-primary top-btn" onclick="printOrder()"><i class="fa fa-print"></i> Packing List </button>
      <button type="button" class="btn btn-sm btn-success top-btn" onclick="printOrderBarcode()"><i class="fa fa-print"></i> Packing List (barcode)</button>
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="showBoxList()"><i class="fa fa-print"></i> Packing List (ปะหน้ากล่อง)</button>
    </div>
  </div>
  <hr class="padding-5 hidden-xs"/>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
      <table class="table table-bordered" style="min-width:960px;">
        <thead>
          <tr class="font-size-11">
            <th class="fix-width-40 text-center">ลำดับ</th>
            <th class="min-width-300 text-center">สินค้า</th>
            <th class="fix-width-100 text-center">ราคา</th>
            <th class="fix-width-80 text-center">ออเดอร์</th>
            <th class="fix-width-80 text-center">จัด</th>
            <th class="fix-width-80 text-center">ตรวจ</th>
            <th class="fix-width-80 text-center">เปิดบิล</th>
            <th class="fix-width-100 text-center">ส่วนลด</th>
            <th class="fix-width-100 text-center">มูลค่า</th>
          </tr>
        </thead>
        <tbody>
  <?php if(!empty($details)) : ?>
  <?php   $no = 1;
          $totalQty = 0;
          $totalPrepared = 0;
          $totalQc = 0;
          $totalSold = 0;
          $totalAmount = 0;
          $totalDiscount = 0;
          $totalPrice = 0;
  ?>
  <?php   foreach($details as $rs) :  ?>
		<?php  if($order->is_wms) : ?>
    <?php     $color = ($rs->order_qty == $rs->sold OR $rs->is_count == 0) ? '' : 'red'; ?>
		<?php 	else : ?>
		<?php     $color = ($rs->order_qty == $rs->qc OR $rs->is_count == 0) ? '' : 'red'; ?>
		<?php 	endif; ?>
            <tr class="font-size-11 <?php echo $color; ?>">
              <td class="middle text-center">
                <?php echo $no; ?>
              </td>

              <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
              <td class="moddle">
                <?php echo $rs->product_code.' <br/> '. $rs->product_name; ?>
              </td>

              <!--- ราคาสินค้า  --->
              <td class="middle text-center">
                <?php echo number($rs->price, 2); ?>
              </td>

              <!---   จำนวนที่สั่ง  --->
              <td class="middle text-center">
                <?php echo number($rs->order_qty); ?>
              </td>

              <!--- จำนวนที่จัดได้  --->
              <td class="middle text-center">
                <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->prepared); ?>
              </td>

              <!--- จำนวนที่ตรวจได้ --->
              <td class="middle text-center">
                <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->qc); ?>
              </td>

              <!--- จำนวนที่บันทึกขาย --->
              <td class="middle text-center">
                <?php echo number($rs->sold); ?>
              </td>

              <!--- ส่วนลด  --->
              <td class="middle text-center">
                <?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
              </td>

              <td class="middle text-right">
                <?php echo $rs->is_count == 0 ? number($rs->final_price * $rs->order_qty) : number( $rs->final_price * $rs->sold , 2); ?>
              </td>

            </tr>
    <?php
          $totalQty += $rs->order_qty;
          $totalPrepared += ($rs->is_count == 0 ? $rs->order_qty : $rs->prepared);
          $totalQc += ($rs->is_count == 0 ? $rs->order_qty : $rs->qc);
          $totalSold += $rs->sold;
          $totalDiscount += $rs->discount_amount * $rs->sold;
          $totalAmount += $rs->final_price * $rs->sold;
          $totalPrice += $rs->price * $rs->sold;
          $no++;
    ?>
  <?php   endforeach; ?>
          <tr class="font-size-12">
            <td colspan="3" class="text-right font-size-14">
              รวม
            </td>

            <td class="text-center">
              <?php echo number($totalQty); ?>
            </td>

            <td class="text-center">
              <?php echo number($totalPrepared); ?>
            </td>

            <td class="text-center">
              <?php echo number($totalQc); ?>
            </td>

            <td class="text-center">
              <?php echo number($totalSold); ?>
            </td>

            <td class="text-center">
              ส่วนลดท้ายบิล
            </td>

            <td class="text-right">
              <?php echo number($order->bDiscAmount, 2); ?>
            </td>
          </tr>


          <tr>
            <td colspan="4" rowspan="3" style="white-space:normal;">
              <?php if(!empty($order->remark)) : ?>
              หมายเหตุ : <?php echo $order->remark; ?>
              <?php endif; ?>
            </td>
            <td colspan="3" class="blod">
              ราคารวม
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalPrice, 2); ?>
            </td>
          </tr>

          <tr>
            <td colspan="3">
              ส่วนลดรวม
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalDiscount + $order->bDiscAmount, 2); ?>
            </td>
          </tr>

          <tr>
            <td colspan="3" class="blod">
              ยอดเงินสุทธิ
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalPrice - ($totalDiscount + $order->bDiscAmount), 2); ?>
            </td>
          </tr>

  <?php else : ?>
        <tr><td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td></tr>
  <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


  <!--************** Address Form Modal ************-->
  <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="info_body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
        </div>
      </div>
    </div>
  </div>

  <?php $this->load->view('inventory/order_closed/box_list');  ?>

  <script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
  <script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php else : ?>
  <?php $this->load->view('inventory/delivery_order/invalid_state'); ?>
<?php endif; ?>


<script>
	$('#ship-date').datepicker({
		'dateFormat' : 'dd-mm-yy'
	});

	function activeShipDate() {
		$('#ship-date').removeAttr('disabled');
		$('#btn-edit-ship-date').addClass('hide');
		$('#btn-update-ship-date').removeClass('hide');
	}

	function updateShipDate() {
		let shipDate = $('#ship-date').val();
		let order = $('#order_code').val();

		$.ajax({
			url:BASE_URL + 'inventory/delivery_order/update_shipped_date',
			type:'POST',
			cache:false,
			data:{
				'order_code' : order,
				'shipped_date' : shipDate
			},
			success:function(rs) {
				rs = $.trim(rs);
				if(rs === 'success') {
					$('#ship-date').attr('disabled', 'disabled');
					$('#btn-update-ship-date').addClass('hide');
					$('#btn-edit-ship-date').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						type:'error',
						text:rs
					});
				}
			}
		})
	}

  function viewOrderDetail(code, role) {
    let width = $(document).width() * 0.9;
    var center = ($(document).width() - width)/2;
    var prop = "width="+width+", height=900. left="+center+", scrollbars=yes";

    var target = BASE_URL + 'orders/orders/edit_order/'+code+'?nomenu';

    switch (role) {
      case 'S' :
        target = BASE_URL + 'orders/orders/edit_order/'+code+'?nomenu';
      break;
      case 'P' :
        target = BASE_URL + 'orders/sponsor/edit_order/'+code+'?nomenu';
      break;
      case 'C' :
        target = BASE_URL + 'orders/consign_so/edit_order/'+code+'?nomenu';
      break;
      case 'N' :
        target = BASE_URL + 'orders/consign_tr/edit_order/'+code+'?nomenu';
      break;
      case 'T' :
        target = BASE_URL + 'inventory/transform/edit_order/'+code+'?nomenu';
      break;
      case 'Q' :
        target = BASE_URL + 'inventory/transform_stock/edit_order/'+code+'?nomenu';
      break;
      case 'U' :
        target = BASE_URL + 'inventory/support/edit_order/'+code+'?nomenu';
      break;
      case 'L' :
        target = BASE_URL + 'inventory/lend/edit_order/'+code+'?nomenu';
      break;
      default:
        target = BASE_URL + 'orders/orders/edit_order/'+code+'?nomenu';
    }

    window.open(target, '_blank', prop);

  }

  function viewTemp(code, role) {
    let width = $(document).width() * 0.9;
    var center = ($(document).width() - width)/2;
    var prop = "width="+width+", height=900. left="+center+", scrollbars=yes";

    var target = BASE_URL + 'inventory/temp_delivery_order';

    switch (role) {
      case 'S' :
        target = BASE_URL + 'inventory/temp_delivery_order';
      break;
      case 'P' :
        target = BASE_URL + 'inventory/temp_delivery_order';
      break;
      case 'C' :
        target = BASE_URL + 'inventory/temp_delivery_order';
      break;
      case 'N' :
        target = BASE_URL + 'inventory/temp_transfer_draft';
      break;
      case 'T' :
        target = BASE_URL + 'inventory/temp_transfer';
      break;
      case 'Q' :
        target = BASE_URL + 'inventory/temp_transfer';
      break;
      case 'U' :
        target = BASE_URL + 'inventory/temp_delivery_order';
      break;
      case 'L' :
        target = BASE_URL + 'inventory/temp_transfer';
      break;
      default:
        target = BASE_URL + 'inventory/temp_delivery_order';
      break;
    }

    let url = target;
    let mapForm = document.createElement("form");
    mapForm.target = "Temp";
    mapForm.method = "POST";
    mapForm.action = url;

    let mapInput = document.createElement("input");
    mapInput.type = "text";
    mapInput.name = "code";
    mapInput.value = code;
    mapForm.appendChild(mapInput);

    document.body.appendChild(mapForm);
    map = window.open(url, "Temp", prop);
    mapForm.submit();
    document.body.removeChild(mapForm);
  }
</script>
<script>

  function confirm_receipted(){
    var code = $('#order_code').val();
    swal({
      title: "ยืนยันการรับสินค้า",
      text: "คุณได้รับสินค้าครบเอกสารเลขที่ "+code+" แล้วใช่หรือไม่ ?",
      type:"warning",
      showCancelButton:true,
      confirmButtonColor:"#428bca",
      confirmButtonText:"ยืนยัน ได้รับครบแล้ว",
      cancelButtonText:"ยกเลิก",
      closeOnConfirm: false
    }, function(){
      $.ajax({
        url:BASE_URL + 'inventory/transfer/confirm_receipted',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs){
          var rs = $.trim(rs);
          if(rs === 'success'){
            swal({
              title:'Confirmed',
              type:'success',
              timer:1000
            });
            setTimeout(function(){
              window.location.reload();
            }, 1200);
          }else{
            swal({
              title:'Error!!',
              text:rs,
              type:'error'
            });
          }
        }
      })
    })
  }

  function shipOrderTiktok(reference) {
    load_in();

    $.ajax({
      url:BASE_URL + 'inventory/qc/ship_order_tiktok/'+reference,
      type:'POST',
      cache:false,
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }


  function shipOrderShopee(reference) {
    load_in();

    $.ajax({
      url:BASE_URL + 'inventory/qc/ship_order_shopee/'+reference,
      type:'POST',
      cache:false,
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }

  function shipOrderLazada(reference) {
    load_in();

    $.ajax({
      url:BASE_URL + 'inventory/qc/ship_order_lazada/'+reference,
      type:'POST',
      cache:false,
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }

  function shipOrderPorlor(code) {
    load_in();

    $.ajax({
      url:BASE_URL + 'inventory/qc/ship_order_porlor/'+code,
      type:'POST',
      cache:false,
      success:function(rs) {
        load_out();

        if(rs.trim() === 'success') {
          printPorlorLabel(code);
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }

  function printPorlorLabel(code) {
    let center = ($(document).width() - 800)/2;
    let prop = "width=800, height=1200. left="+center+", scrollbars=yes";
    let target = BASE_URL + 'inventory/qc/print_porlor_label/'+code;
    window.open(target, "_blank", prop);
  }
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
