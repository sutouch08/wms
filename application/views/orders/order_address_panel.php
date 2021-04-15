
<div class="row">
  <div class="col-sm-12">
  <?php echo paymentLabel($order->code, paymentExists($order->code), $order->is_paid); ?>
  </div>
</div>
<hr />
<div class="row">
  <div class="col-sm-12">
    <div class="tabable">
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
              <table class='table table-bordered' style="margin-bottom:0px;">
                <thead>
                  <tr>
                    <td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
                      <p class="pull-right top-p">
                        <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                      </p>
                    </td>
                  </tr>
                  <tr style="font-size:12px;">
                    <td align="center" width="10%">ชื่อเรียก</td>
                    <td width="12%">ผู้รับ</td>
                    <td width="35%">ที่อยู่</td>
                    <td width="15%">อีเมล์</td>
                    <td width="15%">โทรศัพท์</td>
                    <td ></td>
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
              <?php if( $rs->id == $order->id_address ) : ?>
                      <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php else : ?>
                      <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>" onClick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php endif; ?>
											<button type="button" class="btn btn-minier btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>, '<?php echo $order->code; ?>')"><i class="fa fa-print"></i></button>
                      <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                      <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
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
					<div class="col-sm-6">
						<table class="table" style="margin-bottom:0px;">
							<tr>
								<td class="width-20 middle text-right" style="border:none;">เลือกผู้จัดส่ง : </td>
								<td style="border:none;">
									<select class="form-control input-sm" id="id_sender">
										<option value="">เลือก</option>
										<?php echo select_common_sender($order->customer_code, $order->id_sender); //--- sender helper?>
									</select>
								</td>
								<td class="width-20 middle" style="border:none;">
									<button type="button" class="btn btn-xs btn-success btn-block" onclick="setSender()">บันทึก</button>
								</td>
							</tr>
						</table>

					</div>
				</div>
			</div>
    </div>
      </div>
	</div>
</div>
