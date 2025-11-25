<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/receive_material/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($this->pm->can_add) : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					<i class="ace-icon fa fa-save icon-on-left"></i>
					บันทึก
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="primary">
						<a href="javascript:save('P')">บันทึกเป็นดราฟท์</a>
					</li>
					<li class="success">
						<a href="javascript:save('C')">บันทึกรับเข้าทันที</a>
					</li>
				</ul>
			</div>
		<?php	endif; ?>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" readonly disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center r" id="date-add" value="<?php echo thai_date($doc->date_add) ?>"  readonly/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<input type="text" class="width-100 text-center r" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code"
		placeholder="รหัสผู้ขาย"
		value="<?php echo $doc->vendor_code; ?>" data-prev="<?php echo $doc->vendor_code; ?>"
		autofocus onchange="confirmChangeVendor()" />
	</div>
	<div class="col-lg-7 col-md-5 col-sm-5 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" placeholder="ชื่อผู้ขาย" value="<?php echo $doc->vendor_name; ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>PO No.</label>
		<input type="text" class="width-100 text-center r" id="po-code"
		placeholder="อ้างอิงใบสั่งซื้อ" autocomplete="off"
		value="<?php echo $doc->po_code; ?>" data-prev="<?php echo $doc->po_code; ?>" <?php echo empty($doc->po_code) ? '' : 'disabled'; ?> />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="width-100 not-show">&nbsp;</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" style="height:30px;" onclick="getPoDetails()">แสดง</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="width-100 not-show">&nbsp;</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" style="height:30px;" onclick="clearPo()">Clear</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm r" id="DocCur" disabled>
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="<?php echo $doc->Rate; ?>"  disabled/>
	</div>

	<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice-code" value="<?php echo $doc->invoice_code; ?>" />
	</div>

	<div class="col-lg-4 col-md-6 col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" onchange="changeZone()">
			<option value="">เลือก</option>
			<?php echo select_warehouse($warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="width-100 r" id="zone-code" value="<?php echo $doc->zone_code; ?>" />
	</div>

	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label class="not-show">โซนรับสินค้า</label>
		<input type="text" class="width-100 r" id="zone-name" value="<?php echo zone_name($doc->zone_code); ?>" readonly/>
	</div>

	<div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100" id="remark" value="<?php echo $doc->remark; ?>"/>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="height:400px; overflow:scroll;">
		<table class="table tableFixHead" style="margin-bottom:0px; min-width:1270px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center fix-header"></th>
					<th class="fix-width-40 text-center fix-header">#</th>
					<th class="fix-width-50 text-center fix-header">Batch</th>
					<th class="fix-width-250 fix-header">รหัสสินค้า</th>
					<th class="fix-width-250 fix-header">ชื่อสินค้า</th>
					<th class="min-width-200 fix-header"></th>
					<th class="fix-width-100 text-center fix-header">Uom</th>
					<th class="fix-width-100 text-right fix-header">Unit Price</th>
					<th class="fix-width-120 text-right fix-header">จำนวน</th>
					<th class="fix-width-120 text-right fix-header">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-table">
  <?php $no = 1; ?>
	<?php $totalQty = 0; ?>
	<?php $totalAmount = 0; ?>
	<?php if( ! empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
			<?php $uid = $rs->baseEntry.'-'.$rs->baseLine; ?>
			<tr class="font-size-11 rows r" id="row-<?php echo $uid; ?>" data-uid="<?php echo $uid; ?>">
				<td class="middle text-center"><a class="pointer" href="javascript:removeRow('<?php echo $uid; ?>')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a></td>
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle text-center">
					<?php if($rs->hasBatch) : ?>
						<a class="pointer add-batch" href="javascript:addBatchRow('<?php echo $uid; ?>')" title="Add Batch Number">
							<i class="fa fa-plus fa-lg blue"></i>
						</a>
					<?php endif; ?>
				</td>
				<td class="middle"><?php echo $rs->ItemCode; ?></td>
				<td colspan="2" class="middle"><?php echo $rs->ItemName; ?></td>
				<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
				<td class="middle">
					<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-<?php echo $uid; ?>" value="<?php echo number($rs->Price, 2); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="number"
						class="form-control input-sm text-right text-label receive-qty r"
						id="receive-qty-<?php echo $uid; ?>"
						data-no="<?php echo empty($rs->batchRows) ? 0 : count($rs->batchRows); ?>"
						data-uid="<?php echo $uid; ?>"
						data-limit="<?php echo $rs->limit; ?>"
						data-backlogs="<?php echo $rs->backlogs; ?>"
						data-price="<?php echo $rs->Price; ?>"
						data-bfprice="<?php echo $rs->PriceBefDi; ?>"
						data-afprice="<?php echo $rs->PriceAfVAT; ?>"
						data-basecode="<?php echo $rs->baseCode; ?>"
						data-baseentry="<?php echo $rs->baseEntry; ?>"
						data-baseline="<?php echo $rs->baseLine; ?>"
						data-code="<?php echo $rs->ItemCode; ?>"
						data-name="<?php echo $rs->ItemName; ?>"
						data-vatcode="<?php echo $rs->VatGroup; ?>"
						data-vatrate="<?php echo $rs->VatRate; ?>"
						data-currency="<?php echo $rs->Currency; ?>"
						data-rate="<?php echo $rs->Rate; ?>"
						data-uom="<?php echo $rs->UomCode; ?>"
						data-uom2="<?php echo $rs->UomCode2; ?>"
						data-unitmsr="<?php echo $rs->unitMsr; ?>"
						data-unitmsr2="<?php echo $rs->unitMsr2; ?>"
						data-uomentry="<?php echo $rs->UomEntry; ?>"
						data-uomentry2="<?php echo $rs->UomEntry2; ?>"
						data-numpermsr="<?php echo $rs->NumPerMsr; ?>"
						data-numpermsr2="<?php echo $rs->NumPerMsr2; ?>"
						data-batch="<?php echo $rs->hasBatch == 1 ? 'Y' : 'N'; ?>"
						value="<?php echo round($rs->Qty, 2); ?>"
						onchange="recalAmount('<?php echo $uid; ?>')" />
				</td>

				<td class="middle">
					<input type="text" class="form-control input-sm text-right text-label" id="line-total-<?php echo $uid; ?>" data-amount="<?php echo $rs->LineTotal; ?>" value="<?php echo number($rs->LineTotal, 2); ?>" readonly/>
				</td>
			</tr>
			<?php $no++; ?>
			<?php $totalQty += $rs->Qty; ?>
			<?php $totalAmount += $rs->LineTotal; ?>

			<?php if( ! empty($rs->batchRows)) : ?>
				<?php $ne = 1; ?>
				<?php foreach($rs->batchRows as $rb) : ?>
					<?php $cid =  "{$ne}-{$uid}"; ?>
					<tr class="font-size-11 batch-rows child-of-<?php echo $uid; ?> blue" id="batch-row-<?php echo $cid; ?>" data-id="<?php echo $cid; ?>" data-no="<?php echo $ne; ?>" data-uid="<?php echo $cid; ?>" data-parent="<?php echo $uid; ?>">
						<td class="middle text-center"><a class="pointer" href="javascript:removeBatchRow('<?php echo $cid; ?>')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a></td>
						<td class="text-center ne"><?php echo $ne; ?></td>
						<td colspan="2">
							<div class="input-group">
								<span class="input-group-addon batch-label">Batch :</span>
								<input type="text"
								class="form-control input-sm blue batch-row batch-row-<?php echo $uid; ?> r"
								id="batch-<?php echo $cid; ?>"
								data-uid="<?php echo $cid; ?>"
								data-parent="<?php echo $uid; ?>" value="<?php echo $rb->BatchNum; ?>"
								maxlength="32" value="" placeholder="Batch No. (Required)"/>
							</div>
						</td>
						<td class="middle">
							<div class="input-group">
								<span class="input-group-addon batch-label">Attr 1 :</span>
								<input type="text"
								class="form-control input-sm blue batch-attr1"
								id="batch-attr1-<?php echo $cid; ?>"
								data-uid="<?php echo $cid; ?>"
								data-parent="<?php echo $uid; ?>" maxlength="32" value="<?php echo $rb->BatchAttr1; ?>" placeholder="Batch Attribute 1 (Optional)"/>
							</div>
						</td>
						<td colspan="3" class="middle">
							<div class="input-group">
								<span class="input-group-addon batch-label">Attr 2 :</span>
								<input type="text"
								class="form-control input-sm blue batch-attr2"
								style="max-width:250px;"
								id="batch-attr2-<?php echo $cid; ?>"
								data-uid="<?php echo $cid; ?>"
								data-parent="<?php echo $uid; ?>" maxlength="32" value="<?php echo $rb->BatchAttr2; ?>" placeholder="Batch Attribute 2 (Optional)"/>
							</div>
						</td>
						<td class="middle">
							<div class="input-group">
								<span class="input-group-addon batch-label">Qty :</span>
								<input type="number"
								class="form-control input-sm text-right blue batch-qty batch-qty-<?php echo $uid; ?> r"
								id="batch-qty-<?php echo $cid; ?>"
								data-uid="<?php echo $cid; ?>"
								data-parent="<?php echo $uid; ?>" value="<?php echo round($rb->Qty, 2); ?>" />
							</div>
						</td>
						<td class="middle"></td>
					</tr>
					<?php $ne++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-4 col-sm-4 col-xs-6 control-label no-padding-right">User</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm input-large" value="<?php echo $doc->user; ?>" disabled>
        </div>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm text-right" id="total-receive" value="<?php echo number($totalQty, 2); ?>" disabled>
        </div>
      </div>
			<div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($totalAmount, 2); ?>" disabled>
        </div>
      </div>
		</div>
	</div>
</div>

<?php $this->load->view('inventory/receive_material/receive_modal'); ?>

<?php $this->load->view('cancle_modal'); ?>

<script id="receive-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11 rows r" id="row-{{uid}}" data-no="0" data-uid="{{uid}}">
		<td class="middle text-center"><a class="pointer" href="javascript:removeRow('{{uid}}')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a></td>
			<td class="middle text-center no">{{no}}</td>
			<td class="middle text-center">
				{{#if hasBatch}}
					<a class="pointer add-batch" href="javascript:addBatchRow('{{uid}}')" title="Manage Batch Number">
						<i class="fa fa-plus fa-lg blue"></i>
					</a>
				{{/if}}
			</td>
			<td class="middle">{{pdCode}}</td>
			<td colspan="2" class="middle">{{pdName}}</td>
			<td class="middle text-center">{{unitMsr}}</td>
			<td class="middle">
				<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-{{uid}}" value="{{priceLabel}}" readonly />
			</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-right text-label receive-qty r"
					id="receive-qty-{{uid}}"
					data-no="0"
					data-uid="{{uid}}"
					data-limit="{{limit}}"
					data-backlogs="{{backlogs}}"
					data-price="{{price}}"
					data-bfprice="{{PriceBefDi}}"
					data-afprice="{PriceAfVAT}"
					data-basecode="{{baseCode}}"
					data-baseentry="{{baseEntry}}"
					data-baseline="{{baseLine}}"
					data-code="{{pdCode}}"
					data-name="{{pdName}}"
					data-vatcode="{{vatCode}}"
					data-vatrate="{{vatRate}}"
					data-currency="{{currency}}"
					data-uom="{{uomCode}}"
					data-uom2="{{uomCode2}}"
					data-unitmsr="{{unitMsr}}"
					data-unitmsr2="{{unitMsr2}}"
					data-uomentry="{{uomEntry}}"
					data-uomentry2="{{uomEntry2}}"
					data-numpermsr="{{numPerMsr}}"
					data-numpermsr2="{{numPerMsr2}}"
					data-batch="{{has_batch}}"
					value="{{qty}}"
					onchange="recalAmount('{{uid}}')" />
			</td>
			<td class="middle">
				<input type="text" class="form-control input-sm text-right text-label" id="line-total-{{uid}}" data-amount="{{amount}}" value="{{amountLabel}}" readonly/>
			</td>
		</tr>
	{{/each}}
</script>

<script id="batch-row-template" type="text/x-handlebarsTemplate">
	<tr class="font-size-11 batch-rows child-of-{{uid}} blue" id="batch-row-{{cuid}}" data-id="{{cuid}}" data-no="{{no}}" data-uid="{{cuid}}" data-parent="{{uid}}">
		<td class="middle text-center"><a class="pointer" href="javascript:removeBatchRow('{{cuid}}')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a></td>
		<td class="middle text-center ne"></td>
		<td colspan="2" class="middle text-right">
			<div class="input-group">
				<span class="input-group-addon batch-label">Batch :</span>
				<input type="text" class="form-control input-sm blue batch-row batch-row-{{uid}} r"
				id="batch-{{cuid}}" data-uid="{{cuid}}" data-parent="{{uid}}" maxlength="32" value="" placeholder="Batch No. (Required)"/>
			</div>
		</td>
		<td class="middle">
			<div class="input-group">
				<span class="input-group-addon batch-label">Attr 1 :</span>
				<input type="text"
				class="form-control input-sm blue batch-attr1" id="batch-attr1-{{cuid}}"
				data-uid="{{cuid}}" data-parent="{{uid}}" maxlength="32" value="" placeholder="Batch Attribute 1 (Optional)"/>
			</div>
		</td>
		<td colspan="3" class="middle">
			<div class="input-group">
				<span class="input-group-addon batch-label">Attr 2 :</span>
				<input type="text"
				class="form-control input-sm blue batch-attr2" style="max-width:250px;" id="batch-attr2-{{cuid}}"
				data-uid="{{cuid}}" data-parent="{{uid}}" maxlength="32" value="" placeholder="Batch Attribute 2 (Optional)"/>
			</div>
		</td>
		<td class="middle">
			<div class="input-group">
				<span class="input-group-addon batch-label">Qty :</span>
				<input type="number" class="form-control input-sm text-right blue batch-qty batch-qty-{{uid}} r"
				id="batch-qty-{{cuid}}" data-uid="{{cuid}}" data-parent="{{uid}}" />
			</div>
		</td>
		<td class="middle"></td>
	</tr>
</script>


<script id="batch-row-templateX" type="text/x-handlebarsTemplate">
	<tr class="font-size-11 batch-rows child-of-{{uid}} blue italic" id="batch-row-{{cuid}}" data-id="{{cuid}}" data-no="{{no}}" data-uid="{{cuid}}" data-parent="{{uid}}">
		<td></td>
		<td class="text-center ne"></td>
		<td colspan="2" class="middle text-right"><a class="pointer pull-right" href="javascript:removeBatchRow('{{cuid}}')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a></td>
		<td class="middle">
			<input type="text"
			class="form-control input-sm text-label blue batch-row batch-row-{{uid}} r"
			style="height:21px; font-style:italic; color:#478fca !important;"
			id="batch-{{cuid}}"
			data-uid="{{cuid}}"
			data-parent="{{uid}}" value="" />
		</td>
		<td class="middle">
			<input type="number"
			class="form-control input-sm text-center text-label blue batch-qty batch-qty-{{uid}} r"
			style="height:21px; font-style:italic;  color:#478fca !important;"
			id="batch-qty-{{cuid}}"
			data-uid="{{cuid}}"
			data-parent="{{uid}}" />
		</td>
		<td colspan="3" class="middle"></td>
	</tr>
</script>

<script id="po-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}">
		<td class="middle text-center no">{{no}}</td>
		<td class="middle">{{pdCode}}</td>
		<td class="middle">{{pdName}}</td>
		<td class="middle text-right">{{price_label}} <span style="font-size:10px;">{{currency}}</span></td>
		<td class="middle text-center">{{unitMsr}}</td>
		<td class="middle text-center">{{backlog_label}}</td>
		<td class="middle">
			<input type="number"
				class="form-control input-sm text-center po-qty"
				id="po-qty-{{uid}}"
				data-uid="{{uid}}"
				data-code="{{pdCode}}"
				data-name="{{pdName}}"
				data-basecode="{{baseCode}}"
				data-baseentry="{{baseEntry}}"
				data-baseline="{{baseLine}}"
				data-limit="{{limit}}"
				data-backlogs="{{backlog}}"
				data-qty="{{qty}}"
				data-price="{{price}}"
				data-bfprice="{{PriceBefDi}}"
				data-afprice="{{PriceAfVAT}}"
				data-vatcode="{{vatGroup}}"
				data-vatrate="{{vatRate}}"
				data-currency="{{currency}}"
				data-rate="{{Rate}}"
				data-no="{{no}}"
				data-uom="{{uomCode}}"
				data-uom2="{{uomCode2}}"
				data-uomentry="{{uomEntry}}"
				data-uomentry2="{{uomEntry2}}"
				data-unitmsr="{{unitMsr}}"
				data-unitmsr2="{{unitMsr2}}"
				data-numpermsr="{{numPerMsr}}"
				data-numpermsr2="{{numPerMsr2}}"
				data-batch="{{has_batch}}"
				value="" />
		</td>
    </tr>
  {{/each}}
</script>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
