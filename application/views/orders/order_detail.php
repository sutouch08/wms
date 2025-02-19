<?php
	$add = $this->pm->can_add;
	$edit = $this->pm->can_edit;
	$delete = $this->pm->can_delete;
	?>
<form id="discount-form">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
			<table class="table table-striped border-1" style="border-collapse:inherit; margin-bottom:0px; min-width:1000px;">
				<thead>
					<tr class="font-size-12">
						<th class="fix-width-40 text-center">#</th>
						<th class="fix-width-50 text-center"></th>
						<th class="fix-width-60 text-center">Img</th>
						<th class="fix-width-150">รหัสสินค้า</th>
						<th class="min-width-250">ชื่อสินค้า</th>
						<th class="fix-width-100 text-right">ราคา</th>
						<th class="fix-width-100 text-center">จำนวน</th>
						<th class="fix-width-100 text-center"><?php echo $order->role == 'C' ? 'GP' : 'ส่วนลด'; ?></th>
						<th class="fix-width-150 text-right">มูลค่า</th>

					</tr>
				</thead>
        <tbody id="detail-table">
          <?php   $no = 1;              ?>
          <?php   $total_qty = 0;       ?>
          <?php   $total_discount = 0;  ?>
          <?php   $total_amount = 0;    ?>
          <?php   $order_amount = 0;    ?>
          <?php if(!empty($details)) : ?>
          <?php   foreach($details as $rs) : ?>
            <?php 	$discount = $order->role == 'C' ? $rs->gp : discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
            <?php 	$discLabel = $order->role == 'C' ? $rs->gp .' %' : discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
            <tr class="font-size-10" id="row_<?php echo $rs->id; ?>">
							<input type="hidden" id="currentQty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>">
              <input type="hidden" id="currentPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
              <input type="hidden" id="currentDisc-<?php echo $rs->id; ?>" value="<?php echo $discLabel; ?>">
            	<td class="middle text-center">
      					<?php echo $no; ?>
      				</td>
							<td class="middle text-center">
              <?php if( ( $order->is_paid == 0 && $order->state != 2 && $order->is_expired == 0 ) && ($edit OR $add)) : ?>
								<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state != 8)) : ?>
										<button type="button" class="btn btn-minier btn-danger" onclick="removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
											<i class="fa fa-trash"></i>
										</button>
								<?php endif; ?>
              <?php endif; ?>
              </td>

      				<td class="middle text-center padding-0">
              	<img src="<?php echo get_product_image($rs->product_code, 'mini'); ?>" width="40px" height="40px"  />
              </td>

      				<td class="middle">
      					<?php echo $rs->product_code; ?>
      				</td>

              <td class="middle">
      					<?php echo $rs->product_name; ?>
      				</td>

							<td class="middle text-right">
								<?php if( ($allowEditPrice && $order->state < 4) OR ($rs->is_count == 0 && $order->state < 8)  ) : ?>
									<input type="number"
										class="form-control input-sm text-right price-box e <?php echo $rs->is_count ? "hide" : ""; ?>"
										id="price_<?php echo $rs->id; ?>"
										name="price[<?php echo $rs->id; ?>]"
										value="<?php echo round($rs->price, 2); ?>"
										data-price="<?php echo round($rs->price, 2); ?>"
										data-count="<?php echo $rs->is_count; ?>"
										onchange="recalItem(<?php echo $rs->id; ?>, '<?php echo $rs->is_count ? 'N' : 'Y'; ?>')"/>
								<?php endif; ?>
								<?php if($rs->is_count == 1 OR $order->state >= 8) : ?>
								<span class="price-label" id="price-label-<?php echo $rs->id; ?>">	<?php echo number($rs->price, 2); ?></span>
								<?php endif; ?>								
							</td>

              <td class="middle text-center">
								<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
									<input type="number" class="form-control input-sm text-center line-qty e"
										id="qty_<?php echo $rs->id; ?>"
										data-code="<?php echo $rs->product_code; ?>"
										data-id="<?php echo $rs->id; ?>"
										value="<?php echo round($rs->qty, 2); ?>"
										data-qty="<?php echo round($rs->qty, 2); ?>"
										data-count="<?php echo $rs->is_count; ?>"
										onchange="updateItem(<?php echo $rs->id; ?>)"
									/>
								<?php else : ?>
									<input type="number" class="form-control input-sm text-center line-qty hide"
										id="qty_<?php echo $rs->id; ?>"
										data-code="<?php echo $rs->product_code; ?>"
										data-id="<?php echo $rs->id; ?>"
										value="<?php echo round($rs->qty, 2); ?>"
										data-qty="<?php echo round($rs->qty, 2); ?>"
										data-count="<?php echo $rs->is_count; ?>"
									/>
      						<?php echo number($rs->qty); ?>
								<?php endif; ?>
      				</td>

              <td class="middle text-center">
              <?php if( $order->state < 4 ) : ?>
                <input type="text"
									class="form-control input-sm text-center discount-box hide e"
									id="disc_<?php echo $rs->id; ?>"
									name="disc[<?php echo $rs->id; ?>]"
									value="<?php echo $discount; ?>"
									data-disc="<?php echo $discount; ?>"
									onchange="recalItem(<?php echo $rs->id; ?>, '<?php echo $rs->is_count ? 'N' : 'Y'; ?>')"
									/>
							<?php endif; ?>
							<span class="discount-label" id="disc_label_<?php echo $rs->id; ?>"><?php echo $discLabel; ?></span>
              </td>

              <td class="middle text-right">
							<?php if($order->state < 4 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
								<input type="text"
									class="form-control input-sm line-total text-right e"
									id="line_total_<?php echo $rs->id; ?>"
									data-id="<?php echo $rs->id; ?>"
									onkeyup="recalDiscount(<?php echo $rs->id; ?>)"
									value="<?php echo number($rs->total_amount,2); ?>"
									data-total="<?php echo round($rs->total_amount,2); ?>"
									readonly
									 />
							<?php else : ?>
      					<?php echo number($rs->total_amount, 2); ?>
							<?php endif; ?>
      				</td>
          </tr>

      <?php			$total_qty += $rs->qty;	?>
      <?php 		$total_discount += $rs->discount_amount; ?>
      <?php 		$order_amount += $rs->qty * $rs->price; ?>
      <?php			$total_amount += $rs->total_amount; ?>
      <?php			$no++; ?>
          <?php   endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
            </tr>
          <?php endif; ?>

        	</tbody>
        </table>
    </div>
</div>
<?php 	$netAmount = $total_amount - $order->bDiscAmount;	?>
<?php $disabled = ($order->state == 1 && ($this->pm->can_add OR $this->pm->can_edit)) ? '' : 'disabled'; ?>
<div class="divider-hidden"></div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-2 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-2 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
      <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
        <textarea id="remark" maxlength="254" rows="3" class="form-control" onchange="updateRemark()" <?php echo $disabled; ?>><?php echo str_replace('"', '&quot;',$order->remark); ?></textarea>
      </div>
    </div>

  </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">จำนวน</label>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($total_qty); ?>" disabled>
      </div>
      <label class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-order" value="<?php echo number($order_amount, 2); ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ส่วนลดรวม</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-disc" value="<?php echo number($total_discount, 2); ?>" disabled />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="net-amount" value="<?php echo number($netAmount, 2); ?>" disabled/>
      </div>
    </div>
  </div> <!-- form horizontal -->
</div>
<!--  End Order Detail ----------------->
</form>
<!-- order detail template ------>
<script id="detail-table-template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}

	{{else}}
				<tr class="font-size-10" id="row_{{id}}">
					<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
					<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
					<input type="hidden" id="currentDisc-{{id}}" value="{{discount}}">
					<td class="middle text-center">{{no}}</td>
					<td class="middle text-center">
					<?php if( ( $order->is_paid == 0 && $order->state != 2 && $order->is_expired == 0 ) && ($edit OR $add)) : ?>
						<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state != 8)) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="removeDetail({{id}}, '{{productCode}}')">
									<i class="fa fa-trash"></i>
								</button>
						<?php endif; ?>
					<?php endif; ?>
					</td>

					<td class="middle text-center padding-0">
						<img src="{{imageLink}}" width="40px" height="40px"  />
					</td>

					<td class="middle">{{productCode}}</td>

					<td class="middle">{{productName}}</td>

					<td class="middle text-center">
						<?php if( ($allowEditPrice && $order->state < 4) OR ($rs->is_count == 0 && $order->state < 8)  ) : ?>
							<input type="number"
								class="form-control input-sm text-right price-box e {{#if is_count}} hide {{/if}}"
								id="price_{{id}}"
								name="price[{{id}}]"
								value="{{price}}"
								data-price="{{price}}"
								data-count="{{is_count}}"
								onchange="recalItem({{id}}, '{{#if is_count}}N{{else}}Y{{/if}}')"/>
						<?php endif; ?>
						{{#if is_count}}
						<span class="price-label" id="price-label-{{id}}">	{{price}}</span>
						{{/if}}
					</td>

					<td class="middle text-center">
						<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
							<input type="number" class="form-control input-sm text-center line-qty e"
								id="qty_{{id}}"
								data-code="{{productCode}}"
								data-id="{{id}}"
								value="{{qty}}"
								data-qty="{{qty}}"
								data-count="{{is_count}}"
								onchange="updateItem({{id}})"
							/>
						<?php else : ?>
							{{qty}}
						<?php endif; ?>
					</td>

					<td class="middle text-center">
					<?php if( $order->state < 4 ) : ?>
						<input type="text"
							class="form-control input-sm text-center discount-box hide e"
							id="disc_{{id}}"
							name="disc[{{id}}]"
							value="{{discount}}"
							data-disc="{{discount}}"
							onchange="recalItem({{id}})"
							/>
					<?php endif; ?>
					<span class="discount-label" id="disc_label_{{id}}">{{discount}}</span>
					</td>

					<td class="middle text-right">
					<?php if($order->state < 4 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
						<input type="text"
							class="form-control input-sm line-total text-right e"
							id="line_total_{{id}}"
							data-id="{{id}}"
							onkeyup="recalDiscount({{id}})"
							value="{{amount}}"
							data-total="{{amount}}"
							readonly
							 />
					<?php else : ?>
						{{amount}}
					<?php endif; ?>
					</td>
			</tr>
	{{/if}}
{{/each}}
</script>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
      <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
  </tr>
</script>
