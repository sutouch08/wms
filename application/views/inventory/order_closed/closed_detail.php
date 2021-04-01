<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
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
    </p>
  </div>
</div>
<hr/>


<?php if( $order->state == 8) : ?>
  <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  <input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
  <input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<?php $reference = empty($order->reference) ? $order->code : $order->code . " [{$order->reference}]"; ?>
<?php $cust_name = empty($order->customer_ref) ? $order->customer_name : $order->customer_ref; ?>
  <div class="row">
    <div class="col-sm-2 padding-5 first">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $reference; ?>" disabled />
    </div>

    <?php if($order->role == 'C' OR $order->role == 'N') : ?>
    <div class="col-sm-4 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm" value="<?php echo $cust_name; ?>" disabled />
    </div>
    <div class="col-sm-4 padding-5">
      <label>โซน</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->zone_name; ?>" disabled />
    </div>
    <div class="col-sm-2 padding-5 last">
      <label>พนักงาน</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
    </div>
    <div class="col-sm-10 padding-5 first">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->remark; ?>" disabled />
    </div>
    <div class="col-sm-2 padding-5 last">
      <label class="font-size-2 blod">SAP No</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
    </div>
    <?php else : ?>
      <div class="col-sm-6 padding-5">
        <label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" value="<?php echo $cust_name; ?>" disabled />
      </div>
      <div class="col-sm-4 padding-5 last">
        <label>พนักงาน</label>
        <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
      </div>
      <div class="col-sm-10 padding-5 first">
        <label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" value="<?php echo $order->remark; ?>" disabled />
      </div>
      <div class="col-sm-2 padding-5 last">
        <label class="font-size-2 blod">SAP No</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
      </div>
    <?php endif; ?>
  </div>
  <hr/>

  <div class="row">
    <div class="col-sm-12 text-right">
      <button type="button" class="btn btn-sm btn-info" onclick="printAddress()"><i class="fa fa-print"></i> ใบนำส่ง</button>
      <button type="button" class="btn btn-sm btn-primary" onclick="printOrder()"><i class="fa fa-print"></i> Packing List </button>
      <button type="button" class="btn btn-sm btn-success" onclick="printOrderBarcode()"><i class="fa fa-print"></i> Packing List (barcode)</button>
      <button type="button" class="btn btn-sm btn-warning" onclick="showBoxList()"><i class="fa fa-print"></i> Packing List (ปะหน้ากล่อง)</button>
    </div>
  </div>
  <hr/>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered">
        <thead>
          <tr class="font-size-12">
            <th class="width-5 text-center">ลำดับ</th>
            <th class="width-35 text-center">สินค้า</th>
            <th class="width-8 text-center">ราคา</th>
            <th class="width-8 text-center">ออเดอร์</th>
            <th class="width-8 text-center">จัด</th>
            <th class="width-8 text-center">ตรวจ</th>
            <th class="width-8 text-center">เปิดบิล</th>
            <th class="width-10 text-center">ส่วนลด</th>
            <th class="width-10 text-center">มูลค่า</th>
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
    <?php     $color = ($rs->order_qty == $rs->qc OR $rs->is_count == 0) ? '' : 'red'; ?>
            <tr class="font-size-12 <?php echo $color; ?>">
              <td class="text-center">
                <?php echo $no; ?>
              </td>

              <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
              <td>
                <?php echo limitText($rs->product_code.' : '. $rs->product_name, 100); ?>
              </td>

              <!--- ราคาสินค้า  --->
              <td class="text-center">
                <?php echo number($rs->price, 2); ?>
              </td>

              <!---   จำนวนที่สั่ง  --->
              <td class="text-center">
                <?php echo number($rs->order_qty); ?>
              </td>

              <!--- จำนวนที่จัดได้  --->
              <td class="text-center">
                <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->prepared); ?>
              </td>

              <!--- จำนวนที่ตรวจได้ --->
              <td class="text-center">
                <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->qc); ?>
              </td>

              <!--- จำนวนที่บันทึกขาย --->
              <td class="text-center">
                <?php echo number($rs->sold); ?>
              </td>

              <!--- ส่วนลด  --->
              <td class="text-center">
                <?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
              </td>

              <td class="text-right">
                <?php echo $rs->is_count == 0 ? number($rs->final_price * $rs->order_qty) : number( $rs->final_price * $rs->qc , 2); ?>
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

  <script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>
  <script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>
  <script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>

<?php else : ?>
  <?php $this->load->view('inventory/delivery_order/invalid_state'); ?>
<?php endif; ?>


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
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js"></script>

<?php $this->load->view('include/footer'); ?>
