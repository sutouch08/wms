
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
		<?php if($order->role == 'S') : ?>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>อ้างอิง</label>
		  <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
		</div>
		<?php endif; ?>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>

		<?php if($order->role == 'S' OR $order->role == 'C' OR $order->role == 'N') : ?>
			<?php if($order->role == 'S') : ?>
				<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
					<label>รหัสลูกค้า</label>
					<input type="text" class="form-control input-sm text-center edit" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
				</div>

		    <div class="col-lg-4 col-md-5 col-sm-4-harf col-xs-12 padding-5">
		    	<label>ลูกค้า[ในระบบ]</label>
					<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
		    </div>
		    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		    	<label>ลูกค้า[ออนไลน์]</label>
		      <input type="text" class="form-control input-sm edit" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" disabled />
		    </div>

		    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		    	<label>ช่องทางขาย</label>
					<input type="text" class="form-control input-sm" value="<?php echo $order->channels_name; ?>" disabled/>
		    </div>
		    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		    	<label>การชำระเงิน</label>
					<input type="text" class="form-control input-sm" value="<?php echo $order->payment_name; ?>" disabled />
		    </div>
			<?php endif; ?>

			<?php if($order->role == 'C' OR $order->role == 'N') : ?>
				<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
					<label>รหัสลูกค้า</label>
					<input type="text" class="form-control input-sm text-center edit" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
				</div>

		    <div class="col-lg-4 col-md-6-harf col-sm-6-harf col-xs-8 padding-5">
		    	<label>ลูกค้า[ในระบบ]</label>
					<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
		    </div>
				<div class="col-lg-4-harf col-md-9 col-sm-9 col-xs-12 padding-5">
					<label>โซนฝากขาย</label>
					<input type="text" class="form-control input-sm" value="<?php echo $order->zone_name; ?>" disabled />
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if($order->role == 'L' OR $order->role == 'U' OR $order->role == 'P' OR $order->role == 'Q' OR $order->role == 'T') : ?>
				<?php if($order->role != 'L') : ?>
				<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
					<label>รหัสลูกค้า</label>
					<input type="text" class="form-control input-sm text-center edit" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
				</div>
		    <div class="col-lg-4 col-md-5 col-sm-4-harf col-xs-8 padding-5">
		    	<label>ลูกค้า[ในระบบ]</label>
					<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
		    </div>
				<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
				 	<label>ผู้เบิก</label>
					<input type="text" class="form-control input-sm edit" value="<?php echo $order->user_ref; ?>" disabled />
				</div>
			<?php else : ?>
				<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
				 	<label>ผู้เบิก</label>
					<input type="text" class="form-control input-sm edit" value="<?php echo $order->empName; ?>" disabled />
				</div>
				<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
				 	<label>ผู้รับ</label>
					<input type="text" class="form-control input-sm" value="<?php echo $order->user_ref; ?>" disabled />
				</div>
				<div class="col-lg-4-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
					<label>โซนยืมสินค้า</label>
					<input type="text" class="form-control input-sm" value="<?php echo $order->zone_name; ?>" disabled />
				</div>

			<?php endif; ?>
		<?php endif; ?>



		<div class="col-lg-2 col-md-4 col-sm-3 col-xs-6 padding-5">
			<label>คลัง</label>
	    <input type="text" class="form-control input-sm" value="<?php echo $order->warehouse_name; ?>" disabled />
	  </div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		 	<label>สร้างโดย</label>
		  <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		 	<label>แก้ไขโดย</label>
		  <input type="text" class="form-control input-sm" value="<?php echo $order->update_user; ?>" disabled />
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		 	<label>วันที่จัดส่ง</label>
		  <input type="text" class="form-control input-sm text-center" id="ship-date" value="<?php echo thai_date($order->shipped_date, FALSE); ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="display-block not-show">x</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit-ship-date" onclick="activeShipDate()">เปลี่ยนวันที่จัดส่ง</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update-ship-date" onclick="updateShipDate()">Update</button>
		</div>
</div>
<hr class="margin-top-15"/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
    <?php if( $this->pm->can_edit || $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-sm btn-primary" id="btn-confirm-order" onclick="confirmOrder()">เปิดบิลและตัดสต็อก</button>
    <?php endif; ?>
  </div>
</div>
<hr/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:800px;">
      <thead>
        <tr class="font-size-12">
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-35 text-center">สินค้า</th>
          <th class="width-10 text-center">ราคา</th>
          <th class="width-10 text-center">ออเดอร์</th>
          <th class="width-10 text-center">จัด</th>
          <th class="width-10 text-center">ตรวจ</th>
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
			url:HOME + 'update_shipped_date',
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
</script>
