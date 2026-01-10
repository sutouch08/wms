<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="tabbable">
			<ul class="nav nav-tabs" id="myTab">
				<li class="active"><a data-toggle="tab" href="#transfer-table" aria-expanded="true">รายการโอนย้าย</a></li>
				<li class=""><a data-toggle="tab" href="#zone-table" aria-expanded="false">สินค้าในโซน</a></li>
			</ul>

			<div class="tab-content" style="padding:0px;">
				<div id="transfer-table" class="tab-pane fade active in" style="max-height:600px; overflow:auto;">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="padding-top:10px; padding-bottom:10px; border-top:solid 1px #ddd; background-color:#f8f8f8;">
						<button type="button" class="btn btn-xs btn-danger btn-100 pull-right" style="z-index:1; margin-top:-5px;" onclick="removeChecked()">ลบรายการ</button>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive">
						<table class="table table-bordered tableFixHead" style="margin-bottom:0px; min-width:1110px;">
							<thead>
								<tr class="font-size-11">
									<th class="fix-width-40 text-center">
										<label>
											<input type="checkbox" class="ace" onchange="checkAll($(this))" />
											<span class="lbl"></span>
										</label>
									</th>
									<th class="fix-width-40 text-center">#</th>
									<th class="fix-width-200">รหัส</th>
									<th class="min-width-250">สินค้า</th>
									<th class="fix-width-200">ต้นทาง</th>
									<th class="fix-width-200">ปลายทาง</th>
									<th class="fix-width-100 text-center">จำนวน</th>
								<?php if($doc->is_wms == -1) : ?>
									<th class="fix-width-80 text-center">รับแล้ว</th>
								<?php endif; ?>
								</tr>
							</thead>

							<tbody id="transfer-list">
								<?php if(!empty($details)) : ?>
									<?php		$no = 1;						?>
									<?php   $total_qty = 0; ?>
									<?php   $total_wms = 0; ?>
									<?php		foreach($details as $rs) : 	?>
										<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
											<td class="middle text-center">
												<label>
													<input type="checkbox" class="ace chk"
													value="<?php echo $rs->id; ?>"
													data-id="<?php echo $rs->id; ?>"
													data-item="<?php echo $rs->product_code; ?>"
													data-fromzone="<?php echo $rs->from_zone; ?>"
													data-tozone="<?php echo $rs->to_zone; ?>"
													/>
													<span class="lbl"></span>
												</label>
											</td>
											<td class="middle text-center no">
												<?php echo $no; ?>
											</td>
											<!--- บาร์โค้ดสินค้า --->
											<td class="middle">
												<input type="text" class="form-control input-xs text-label" value="<?php echo $rs->product_code; ?>" readonly />
											</td>
											<!--- รหัสสินค้า -->
											<td class="middle">
												<input type="text" class="form-control input-xs text-label" value="<?php echo $rs->product_name; ?>" readonly />
											</td>
											<!--- โซนต้นทาง --->
											<td class="middle">
												<input type="text" class="form-control input-xs text-label" data-fromzone="<?php echo $rs->from_zone; ?>" value="<?php echo $rs->from_zone.' | '.$rs->from_zone_name; ?>" readonly />
												<input type="hidden" class="row-zone-from" id="row-from-<?php echo $rs->id; ?>" value="<?php echo $rs->from_zone; ?>" />
											</td>
											<td class="middle" id="row-label-<?php echo $rs->id; ?>">
												<input type="text" class="form-control input-xs text-label" data-tozone="<?php echo $rs->to_zone; ?>" value="<?php echo $rs->to_zone.' | '.$rs->to_zone_name; ?>" readonly />
											</td>

										<?php if($doc->is_wms == '-1') : ?>
											<td class="middle text-center qty">
													<input type="number" class="width-100 e text-center trans-qty"
													id="trans-qty-<?php echo $rs->id; ?>"
													data-qty="<?php echo $rs->qty; ?>"
													data-wms="<?php echo $rs->wms_qty; ?>"
													onchange="updateTransferQty(<?php echo $rs->id; ?>)"
													value="<?php echo $rs->qty; ?>" />
											</td>
											<td class="middle text-center wms-qty">
												<?php echo number($rs->wms_qty); ?>
											</td>
										<?php else : ?>
											<td class="middle text-center qty">
												<?php echo number($rs->qty); ?>
											</td>
										<?php endif; ?>
										</tr>
										<?php  $no++;			?>
										<?php  $total_qty += $rs->qty; ?>
										<?php $total_wms += $rs->wms_qty; ?>
									<?php		endforeach;			?>
									<tr>
										<td colspan="6" class="middle text-right"><strong>รวม</strong></td>
										<td class="middle text-center" id="total"><?php echo number($total_qty); ?></td>
									<?php if($doc->is_wms == -1) : ?>
										<td class="middle text-center" id="total-wms"><?php echo number($total_wms); ?></td>
									<?php endif; ?>
									</tr>
								<?php	else : ?>
									<tr>
										<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
									</tr>
								<?php	endif; ?>
							</tbody>
						</table>

					</div>
				</div> <!-- Tab-pane -->

				<div id="zone-table" class="tab-pane fade" style="overflow:auto;">
					<div class="divider-hidden"></div>

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
					    <label>โซนต้นทาง</label>
					    <input type="text" class="form-control input-sm" id="from-zone" placeholder="ค้นหาชื่อโซน" autofocus />
					  </div>
					  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
					    <label>รหัสสินค้า</label>
					    <input type="text" class="form-control input-sm" id="item-code" placeholder="กรองด้วยรหัสสินค้า" />
					  </div>

					  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
					    <label class="display-block not-show">ok</label>
					    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductInZone()">แสดงรายการ</button>
					  </div>

						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">&nbsp;</div>

						<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
					    <label>โซนปลายทาง</label>
					    <input type="text" class="form-control input-sm" id="to-zone" placeholder="ค้นหาชื่อโซน" />
					  </div>
					</div>

					<div class="divider-hidden"></div>

					<form id="productForm">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="padding-top:10px; padding-bottom: 10px; border-top:solid 1px #ddd; border-bottom: solid 1px #ddd; background-color:#f8f8f8;">
							<button type="button" class="btn btn-xs btn-info" onclick="selectAll()">เลือกทั้งหมด</button>
							<button type="button" class="btn btn-xs btn-warning" onclick="clearAll()">เคลียร์</button>
							<button type="button" class="btn btn-xs btn-primary pull-right" onclick="addToTransfer()">ย้ายรายการที่เลือก</button>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="min-width:100px; max-height:600px; overflow:auto; padding-left:0; padding-right:0;">
							<table class="table table-striped table-bordered tableFixHead" style="min-width:800px; margin-bottom:0px;">
								<thead>
									<tr class="font-size-11">
										<th class="fix-width-40 text-center fix-header">#</th>
										<th class="fix-width-150 fix-header">โซน</th>
										<th class="fix-width-200 fix-header">รหัส</th>
										<th class="min-width-200 fix-header hidden-xs">สินค้า</th>
										<th class="fix-width-100 text-center fix-header">จำนวน</th>
										<th class="fix-width-100 text-center fix-header">ย้ายออก</th>
									</tr>
								</thead>

								<tbody id="zone-list"> </tbody>

							</table>
						</div>
			      </form>
				</div> <!-- Tab-pane -->
			</div> <!-- Tab content -->
		</div><!--- tabbable -->
  </div><!--- Col-lg-12 -->
</div><!--- row -->

<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="6" class="text-center">
				<h4>ไม่พบสินค้าในโซน</h4>
			</td>
		</tr>
	{{else}}
		<tr class="font-size-11" id="zone-row-{{no}}">
			<td class="middle text-center zone-no">{{ no }}</td>
			<td class="middle">{{ zone_code }}</td>
		  <td class="middle">{{ product_code }}</td>
		  <td class="middle hide-text hidden-xs">{{ product_name }}</td>
		  <td class="middle text-center qty-label" id="qty-label-{{no}}">{{ qty }}</td>
		  <td class="middle text-center">
		  	<input type="number"
				class="form-control input-xs text-center input-qty"
				max="{{qty}}"
				id="{{product_code}}"
				data-no="{{no}}"
				data-fromzone="{{zone_code}}"
				data-limit="{{qty}}"
				data-sku="{{product_code}}"
				data-name="{{product_name}}"
				ondblclick="fillQty($(this))" onkeyup="validQty($(this))" />
		  </td>
		</tr>
	{{/if}}
{{/each}}
</script>



<script id="transferTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
			<tr class="font-size-11">
				<td colspan="6" class="text-right"><strong>รวม</strong></td>
				<td class="middle text-center" id="total">{{ totalQty }}</td>
			<?php if($doc->is_wms == -1) : ?>
				<td class="middle text-center" id="total-wms">{{ totalWms }}</td>
			<?php endif; ?>
			</tr>
		{{else}}
		<tr class="font-size-11" id="row-{{id}}">
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="{{id}}"
						data-id="{{id}}"
						data-item="{{product_code}}"
						data-fromzone="{{from_zone}}"
						data-tozone="{{to_zone}}" />
					<span class="lbl"></span>
				</label>
			</td>
			<td class="middle text-center no">{{ no }}</td>
			<td class="middle">
				<input type="text" class="form-control input-xs text-label" value="{{product_code}}" readonly />
			</td>
			<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{product_name}}" readonly /></td>
			<td class="middle">
				<input type="text" class="form-control input-xs text-label" data-fromzone="{{from_zone}}" value="{{from_zone}} | {{from_zone_name}}" readonly />
				<input type="hidden" class="row-zone-from" id="row-from-{{id}}" value="{{from_zone}}" />
			</td>
			<td class="middle">
				<input type="text" class="form-control input-xs text-label" data-tozone="{{to_zone}}" value="{{to_zone}} | {{to_zone_name}}" readonly />
			</td>
		<?php if($doc->is_wms == -1) : ?>
			<td class="middle text-center qty">
					<input type="number" class="form-control input-xs e text-center trans-qty"
					id="trans-qty-{{id}}"
					data-qty="{{qty}}"
					data-wms="{{wms_qty}}"
					onchange="updateTransferQty({{id}})"
					value="{{qty}}" />
			</td>
			<td class="middle text-center wms-qty">{{wms_qty_label}}</td>
		<?php else : ?>
			<td class="middle text-center qty">{{ qty }}</td>
		<?php endif; ?>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>
