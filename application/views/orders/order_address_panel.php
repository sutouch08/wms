<?php if($order->role == 'S') : ?>
	<?php 	$paymentLabel = paymentLabel($order->code, paymentExists($order->code), $order->is_paid);	?>
	<?php if(!empty($paymentLabel)) : ?>
		<div class="row">
		  <div class="col-sm-12 col-xs-12 padding-5">
		  	<?php echo $paymentLabel; ?>
		  </div>
		</div>
		<hr class="padding-5"/>
	<?php endif; ?>
<?php endif; ?>
<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5">
    <div class="tabable">
			<?php if($order->is_wms && $order->wms_export == 1) : ?>
			<button type="button" class="btn btn-sm btn-primary pull-right" style="z-index:100;" onclick="update_wms_status()">Update WMS Status</button>
			<?php endif; ?>
    	<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สถานะ</a>
        </li>
      	<li>
          <a href="#address" aria-expanded="false" aria-controls="address" role="tab" data-toggle="tab">ที่อยู่</a>
        </li>
				<li>
          <a href="#sender" aria-expanded="false" aria-controls="sender" role="tab" data-toggle="tab">ผู้จัดส่ง</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">

				<div role="tabpanel" class="tab-pane fade" id="address">
          <div class='row'>
            <div class="col-sm-12">
            <div class="table-responsive">
              <table class='table table-bordered' style="margin-bottom:0px; border-collapse:collapse; border:0;">
                <thead>
                  <tr style="background-color:white;">
                    <th colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
                      <p class="pull-right top-p">
                        <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                      </p>
                    </th>
                  </tr>
                  <tr style="font-size:12px; background-color:white;">
                    <th align="center" width="10%">ชื่อเรียก</th>
                    <th width="12%">ผู้รับ</th>
                    <th width="35%">ที่อยู่</th>
                    <th width="15%">อีเมล์</th>
                    <th width="15%">โทรศัพท์</th>
                    <th ></td>
                  </tr>
                </thead>
                <tbody id="adrs">
          <?php if(!empty($addr)) : ?>
          <?php 	foreach($addr as $rs) : ?>
                  <tr style="font-size:12px;" id="<?php echo $rs->id; ?>">
                    <td align="center"><?php echo $rs->alias; ?></td>
                    <td><?php echo $rs->name; ?></td>
                    <td><?php echo $rs->address.' '. $rs->sub_district.' '.$rs->district.' '.$rs->province.' '. $rs->postcode; ?></td>
                    <td><?php echo $rs->email; ?></td>
                    <td><?php echo $rs->phone; ?></td>
                    <td align="right">
									<?php if(($order->is_wms == 0) OR ($order->is_wms == 1 && $order->state < 3)) : ?>
										<?php $func = "onClick='setAddress({$rs->id})'"; ?>
									<?php else : ?>
										<?php $func = ""; ?>
									<?php endif; ?>

              <?php if( $rs->id == $order->id_address ) : ?>
                      <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" <?php echo $func; ?>>
                        <i class="fa fa-check"></i>
                      </button>
              <?php else : ?>
                      <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>" <?php echo $func; ?>>
                        <i class="fa fa-check"></i>
                      </button>
              <?php endif; ?>
											<button type="button" class="btn btn-minier btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>, '<?php echo $order->code; ?>')">
												<i class="fa fa-print"></i>
											</button>
										<?php if(($order->is_wms == 0) OR ($order->is_wms == 1 && $order->state < 3)) : ?>
                      <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                      <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
										<?php endif; ?>
                    </td>
                  </tr>

          <?php 	endforeach; ?>
          <?php else : ?>
                  <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
          <?php endif; ?>
                </tbody>
              </table>
            </div>
            </div>
          </div><!-- /row-->
      </div>

      <div role="tabpanel" class="tab-pane active" id="state">
				<?php $this->load->view('orders/order_state'); ?>
      </div>
			<div role="tabpanel" class="tab-pane fade" id="sender">
				<div class="row" style="padding:15px;">
					<div class="col-sm-12 col-xs-12 padding-5">
						<table class="table" style="margin-bottom:0px;">
							<tr>
								<td class="width-10 middle text-right" style="border:none;">เลือกผู้จัดส่ง : </td>
								<td class="width-20"style="border:none;">
									<select class="form-control input-sm" id="id_sender">
										<option value="">เลือก</option>
										<?php echo select_common_sender($order->customer_code, $order->id_sender); //--- sender helper?>
									</select>
								</td>
								<td class="width-10 middle" style="border:none;">
									<?php if(($order->is_wms == 0) OR ($order->is_wms == 1 && $order->state < 3) OR $order->id_sender == NULL) : ?>
									<button type="button" class="btn btn-xs btn-success btn-block" onclick="setSender()">บันทึก</button>
									<?php endif; ?>
								</td>
								<td class="width-15 middle text-right" style="border:none;">Tracking No: </td>
								<td class="width-20 middle" style="border:none;">
									<input type="text" class="form-control input-sm" id="tracking" value="<?php echo $order->shipping_code; ?>">
									<input type="hidden" id="trackingNo" value="<?php echo $order->shipping_code; ?>">
								</td>
								<td class="width-10 middle" style="border:none;">
									<?php if(($order->is_wms == 0) OR ($order->is_wms == 1 && $order->state < 3) OR $order->shipping_code == NULL) : ?>
									<button type="button" class="btn btn-xs btn-success btn-block" onclick="update_tracking()">บันทึก</button>
									<?php endif; ?>
								</td>
								<td style="border:none;"></td>
							</tr>
						</table>

					</div>
				</div>
			</div>
    </div>
      </div>
	</div>
</div>
<hr class="padding-5"/>
<script>
function update_wms_status() {
	const order_code = $('#order_code').val();
	if(order_code !== "" && order_code !== undefined) {
		load_in();
		$.ajax({
			url:BASE_URL + 'rest/V1/wms_order_status/update_wms_status',
			type:'GET',
			cache:false,
			data:{
				"order_code" : order_code
			},
			success:function(rs) {
				load_out();
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal(rs);
				}
			}
		})
	}
}
</script>
