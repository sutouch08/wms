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


	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ผู้ยืม/ผู้เบิก/ผู้ทำรายการ</label>
		<input type="text" class="form-control input-sm edit" value="<?php echo $order->role == 'L' ? $order->empName : (($order->role == 'T' OR $order->role == 'Q') ? $order->user_ref : NULL); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ผู้รับ</label>
		<input type="text" class="form-control input-sm" value="<?php echo ($order->role == 'U' OR $order->role == 'L') ? $order->user_ref : NULL; ?>" disabled />
	</div>
	<div class="col-lg-3-harf col-md-6 col-sm-6 col-xs-12 padding-5">
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
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>วันที่จัดส่ง</label>
		<input type="text" class="form-control input-sm text-center" id="ship-date" value="<?php echo thai_date($order->shipped_date, FALSE); ?>" disabled />
	</div>
</div>
<hr/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
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
    <button type="button" class="btn btn-sm btn-info top-btn" onclick="printAddress(<?php echo $order->id_address; ?>, '<?php echo $order->code; ?>', <?php echo $order->id_sender; ?>)"><i class="fa fa-print"></i> ใบนำส่ง</button>
    <button type="button" class="btn btn-sm btn-primary top-btn" onclick="printOrder()"><i class="fa fa-print"></i> Packing List </button>
    <button type="button" class="btn btn-sm btn-success top-btn" onclick="printOrderBarcode()"><i class="fa fa-print"></i> Packing List (barcode)</button>
    <button type="button" class="btn btn-sm btn-warning top-btn" onclick="showBoxList()"><i class="fa fa-print"></i> Packing List (ปะหน้ากล่อง)</button>
  </div>
</div>
<hr/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:940px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-50 text-center">#</th>
          <th class="min-width-350 text-center">สินค้า</th>
          <th class="fix-width-100 text-center">ราคา</th>
          <th class="fix-width-80 text-center">ออเดอร์</th>
          <th class="fix-width-80 text-center">จัด</th>
          <th class="fix-width-80 text-center">ตรวจ</th>
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
        $totalAmount = 0;
        $totalDiscount = 0;
        $totalPrice = 0;
?>
<?php   foreach($details as $rs) :  ?>
  <?php     $color = ($rs->order_qty == $rs->qc OR $rs->is_count == 0) ? '' : 'red'; ?>
          <tr class="font-size-11 <?php echo $color; ?>">
            <td class="text-center">
              <?php echo $no; ?>
            </td>

            <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
            <td>
              <?php echo $rs->product_code.' <br/> '. $rs->product_name; ?>
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
        $totalDiscount += ($rs->is_count == 0 ? $rs->discount_amount * $rs->order_qty : $rs->discount_amount * $rs->qc);
        $totalAmount += ($rs->is_count == 0 ? $rs->final_price * $rs->order_qty : $rs->final_price * $rs->qc);
        $totalPrice += ($rs->is_count == 0 ? $rs->price * $rs->order_qty : $rs->price * $rs->qc);
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
            ส่วนลดท้ายบิล
          </td>

          <td class="text-right">
            <?php echo number($order->bDiscAmount, 2); ?>
          </td>
        </tr>


        <tr>
          <td colspan="3" rowspan="3">
            หมายเหตุ : <?php echo $order->remark; ?>
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
