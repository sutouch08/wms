<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="tabbable">
			<ul class="nav nav-tabs" id="myTab">
				<li class="active"><a data-toggle="tab" href="#move-table" onclick="getMoveTable()" aria-expanded="true">รายการโอนย้าย</a></li>
				<li class=""><a data-toggle="tab" href="#zone-table" onclick="getMoveOut()" aria-expanded="false">ย้ายออก</a></li>
				<li class=""><a data-toggle="tab" href="#temp-table" onclick="getMoveIn()" aria-expanded="false">TEMP</a></li>
			</ul>

			<div class="tab-content" style="padding:0px;">
				<div id="move-table" class="tab-pane fade active in">
					<table class="table table-striped" style="margin-top:15px; margin-bottom:0px;">
						<thead>
							<tr>
								<th class="width-5 text-center">ลำดับ</th>
								<th class="width-15">บาร์โค้ด</th>
								<th class="width-20">สินค้า</th>
								<th class="width-25">ต้นทาง</th>
								<th class="width-25">ปลายทาง</th>
								<th class="width-10 text-center">จำนวน</th>
								<th class="width-5"></th>
							</tr>
						</thead>
						<tbody id="move-list">
							<?php if(!empty($details)) : ?>
								<?php		$no = 1;						?>
								<?php   $total_qty = 0;  ?>
								<?php		foreach($details as $rs) : 	?>
									<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
										<td class="middle text-center no"><?php echo $no; ?></td>
										<td class="middle"><?php echo $rs->barcode; ?></td>
										<td class="middle"><?php echo $rs->product_code; ?></td>
										<td class="middle"><?php echo $rs->from_zone_name; ?></td>
										<td class="middle" id="row-label-<?php echo $rs->id; ?>"><?php echo $rs->to_zone_name; ?></td>
										<td class="middle text-center qty" ><?php echo number($rs->qty); ?></td>
										<td class="middle text-center">
											<?php if($this->pm->can_edit && $rs->valid == 0) : ?>
												<button type="button" class="btn btn-minier btn-danger"
												onclick="deleteMoveItem(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
												<i class="fa fa-trash"></i>
											</button>
										<?php endif; ?>
									</td>
								</tr>
								<?php			$no++;			?>
								<?php 	  $total_qty += $rs->qty; ?>
							<?php		endforeach;			?>
							<tr>
								<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
								<td class="middle text-center" id="total"><?php echo number($total_qty); ?></td>
								<td></td>
							</tr>
						<?php	else : ?>
							<tr>
								<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
							</tr>
						<?php	endif; ?>
					</tbody>
				</table>
			</div> <!-- end tab-pane #move-table -->

			<div id="temp-table" class="tab-pane fade">
				<div class="divider-hidden"></div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="col-lg-3 col-md-3 col-sm-3 padding-5">
						<label>บาร์โค้ดโซน</label>
						<input type="text" class="form-control input-sm" id="toZone-barcode" placeholder="ยิงบาร์โค้ดโซนปลายทาง" />
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 padding-5">
						<label class="display-block not-show">zoneName</label>
						<input type="text" class="form-control input-sm" id="zoneName-label" disabled />
					</div>
					<div class="col-lg-1 col-md-1 col-sm-1 padding-5">
						<label class="display-block not-show">newzone</label>
						<button type="button" class="btn btn-xs btn-info btn-block" id="btn-new-to-zone" onclick="newToZone()" disabled>โซนใหม่</button>
					</div>
					<div class="col-lg-1 col-md-1 col-sm-1 padding-5">
						<label>จำนวน</label>
						<input type="number" class="form-control input-sm text-center" id="qty-to" value="1" disabled />
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-sm-3 padding-5">
						<label>บาร์โค้ดสินค้า</label>
						<input type="text" class="form-control input-sm" id="barcode-item-to" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" disabled />
					</div>
				</div>

				<div class="divider-hidden"></div>
				<div class="divider-hidden"></div>
				<table class="table table-striped table-bordered" style="margin-top:15px; margin-bottom:0;">
					<thead>
						<tr>
							<th colspan="6" class="text-center">รายการใน Temp</th>
						</tr>
						<tr>
							<th class="fix-width-40 text-center">ลำดับ</th>
							<th class="fix-width-150 text-center">บาร์โค้ด</th>
							<th class="min-width-200 text-center">สินค้า</th>
							<th class="fix-width-200 text-center">ต้นทาง</th>
							<th class="fix-width-100 text-center">จำนวน</th>
							<th class="fix-width-60 text-center"></th>
						</tr>
					</thead>
					<tbody id="temp-list"></tbody>
				</table>
			</div><!-- end tab-pane #temp-table -->

				<div id="zone-table" class="tab-pane fade">
					<div class="divider-hidden"></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
	          <label>โซนต้นทาง</label>
	          <input type="text" class="width-100" id="fromZone-barcode" placeholder="ยิงบาร์โค้ดโซน" />
	        </div>

	        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
	          <label class="display-block not-shohw">โซนต้นทาง</label>
	          <input type="text" class="width-100" id="fromZone-name" disabled />
	        </div>

	        <div class="col-sm-1 padding-5">
	          <label class="display-block not-show">newZone</label>
	          <button type="button" class="btn btn-xs btn-info btn-block" id="btn-new-zone" onclick="newFromZone()" disabled >โซนใหม่</button>
	        </div>

	        <div class="col-sm-1 padding-5">
	          <label>จำนวน</label>
	          <input type="number" class="form-control input-sm text-center" id="qty-from" value="1" disabled />
	        </div>

	        <div class="col-sm-3 padding-5">
	          <label>บาร์โค้ดสินค้า</label>
	          <input type="text" class="form-control input-sm" id="barcode-item-from" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" disabled />
	        </div>
					<div class="divider-hidden"></div>
					<div class="divider-hidden"></div>

					<table class="table table-striped table-bordered" style="margin-top:15px; margin-bottom:0px;">
						<thead>
							<tr>
								<th colspan="4" class="text-center"><h4 class="title" id="zoneName"></h4></th>
							</tr>
							<tr>
								<th class="width-10 text-center">ลำดับ</th>
								<th class="width-20 text-center">บาร์โค้ด</th>
								<th class="text-center">สินค้า</th>
								<th class="width-10 text-center">จำนวน</th>
							</tr>
						</thead>
						<tbody id="zone-list">
							<tr>
								<td colspan="4" class="text-center"><h4>ไม่พบรายการ</h4></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><!-- tab-content -->
		</div><!-- tabable -->
	</div><!-- col-lg-12 -->
</div><!-- row -->

<input type="hidden" name="from_zone_code" id="from_zone_code" value="" />
<input type="hidden" name="to_zone_code" id="to_zone_code" value="" />

<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="6" class="text-center">
				<h4>ไม่พบสินค้าในโซน</h4>
			</td>
		</tr>
	{{else}}
		<tr>
			<td align="center">{{ no }}</td>
		  <td align="center">{{ barcode }}</td>
		  <td>
				{{ products }}
				<input type="hidden" id="qty_{{barcode}}" value="{{qty}}" />
			</td>
		  <td align="center" id="qty-label_{{barcode}}">{{ qty }}	</td>
		</tr>
	{{/if}}
{{/each}}
</script>



<script id="tempTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}

		<tr class="font-size-12" id="row-temp-{{ id }}">
			<td class="middle text-center">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle text-center">
				<input type="hidden" id="qty-{{barcode}}" value="{{qty}}" />
				{{ fromZone }}
			</td>

			<td class="middle text-center" id="qty-label-{{barcode}}">
				{{ qty }}
			</td>
			<td class="middle text-center" id="btn-delete-{{id}}">
				{{{btn_delete}}}
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
			<tr>
				<td colspan="5" class="text-right"><strong>รวม</strong></td>
				<td class="middle text-center" id="total">{{ total }}</td>
				<td></td>
			</tr>
		{{else}}
		<tr class="font-size-12" id="row-{{ id }}">
			<td class="middle text-center no">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle">{{ from_zone }}</td>
			<td class="middle">{{{ to_zone }}}</td>
			<td class="middle text-center qty">{{ qty }}</td>
			<td class="middle text-center">{{{ btn_delete }}}</td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>
