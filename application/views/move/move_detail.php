<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="tabbable">
			<ul class="nav nav-tabs" id="myTab">
				<li class="active"><a data-toggle="tab" href="#move-table" onclick="getMoveTable()" aria-expanded="true">รายการโอนย้าย</a></li>
				<li class=""><a data-toggle="tab" href="#zone-table" onclick="getMoveOut()" aria-expanded="false">สินค้าในโซน</a></li>
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
			<?php   $total_qty = 0; ?>
			<?php		foreach($details as $rs) : 	?>
							<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
				      	<td class="middle text-center no"><?php echo $no; ?></td>
				        <td class="middle"><?php echo $rs->barcode; ?></td>
				        <td class="middle"><?php echo $rs->product_code; ?></td>
				        <td class="middle">
				      		<input type="hidden" class="row-zone-from" id="row-from-<?php echo $rs->id; ?>" value="<?php echo $rs->from_zone; ?>" />
									<?php echo $rs->from_zone_name; ?>
				        </td>
				        <td class="middle" id="row-label-<?php echo $rs->id; ?>"><?php 	echo $rs->to_zone_name; 	?></td>
								<td class="middle text-center qty"><?php echo number($rs->qty); ?></td>
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
			<?php     $total_qty += $rs->qty; ?>
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

			<div id="zone-table" class="tab-pane fade">
				<div class="divider-hidden"></div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<label>ต้นทาง</label>
					<input type="text" class="form-control input-sm" id="from-zone" placeholder="ค้นหาชื่อโซน" autofocus />
				</div>

				<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
					<label class="display-block not-show">ok</label>
					<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductInZone()">แสดงสินค้า</button>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
					<label>ปลายทาง</label>
					<input type="text" class="form-control input-sm" id="to-zone" placeholder="ค้นหาชื่อโซน" />
				</div>

				<div class="divider-hidden"></div>
				<div class="divider-hidden"></div>

				<form id="productForm">
					<table class="table table-striped table-bordered" style="margin-top:15px; margin-bottom:0;">
						<thead>
							<tr>
								<th colspan="6" class="text-center">
									<h4 class="title" id="zoneName"></h4>
								</th>
							</tr>
							<tr>
								<th colspan="6">
									<div class="col-sm-6">
										<button type="button" class="btn btn-sm btn-info" onclick="selectAll()">เลือกทั้งหมด</button>
										<button type="button" class="btn btn-sm btn-warning" onclick="clearAll()">เคลียร์</button>
									</div>
									<div class="col-sm-6">
										<p class="pull-right top-p">
											<button type="button" class="btn btn-sm btn-primary" onclick="addToMove()">ย้ายรายการที่เลือก</button>
										</p>
									</div>
								</th>
							</tr>
							<tr>
								<th class="width-10 text-center">ลำดับ</th>
								<th class="width-20 text-center">บาร์โค้ด</th>
								<th class="width-40 text-center">สินค้า</th>
								<th class="width-10 text-center">จำนวน</th>
								<th class="width-10 text-center">ย้ายออก</th>
							</tr>
						</thead>
						<tbody id="zone-list">
							<tr>
								<td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
							</tr>
						</tbody>
					</table>
				</form>
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
		  <td>{{ products }}</td>
		  <td align="center" class="qty-label">{{ qty }}</td>
		  <td align="center">
		  	<input type="number" class="form-control input-sm text-center input-qty" name="items[{{products}}]" data-products="{{products}}" max="{{qty}}" id="item_{{no}}" />
		  </td>
		</tr>
	{{/if}}
{{/each}}
</script>
