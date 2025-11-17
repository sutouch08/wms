<?php $this->load->view('include/header'); ?>
<style>
	#receive-table > tr > td {
		padding:3px !important;
	}

	#batch-body > tr > td {
		padding:3px !important;
	}

	.add-batch {
		margin-right: 5px;
	}

	.italic {
		font-style: italic;
	}

	.text-label {
		height: 21px;
	}

</style>
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
					<li class="purple">
						<a href="javascript:save('O')">บันทึกรอรับ</a>
					</li>
				</ul>
			</div>
		<?php	endif; ?>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" readonly disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center r" id="date-add" value="<?php echo thai_date($doc->date_add) ?>"  readonly/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<input type="text" class="width-100 text-center r" id="posting-date" value="<?php echo thai_date($doc->shipped_date); ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code"
		placeholder="รหัสผู้ขาย"
		value="<?php echo $doc->vendor_code; ?>" data-prev="<?php echo $doc->vendor_code; ?>"
		autofocus onchange="confirmChangeVendor()" />
	</div>
	<div class="col-lg-7 col-md-5-harf col-sm-5-harf col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" placeholder="ชื่อผู้ขาย" value="<?php echo $doc->vendor_name; ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>PO No.</label>
		<input type="text" class="width-100 text-center r" id="po-code"
		placeholder="อ้างอิงใบสั่งซื้อ" autocomplete="off"
		value="<?php echo $doc->po_code; ?>" data-prev="<?php echo $doc->po_code; ?>" <?php echo empty($doc->po_code) ? '' : 'disabled'; ?> />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label class="width-100 not-show">&nbsp;</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" style="height:30px;" onclick="getPoDetails()">แสดง</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label class="width-100 not-show">&nbsp;</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" style="height:30px;" onclick="clearPo()">Clear</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm r" id="DocCur" disabled>
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="<?php echo $doc->Rate; ?>"  disabled/>
	</div>

	<div class="col-lg-2-harf col-md-2 col-sm-4 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice-code" value="<?php echo $doc->invoice_code; ?>" />
	</div>

	<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" onchange="zoneInit()">
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

	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100" id="remark" value="<?php echo $doc->remark; ?>"/>
	</div>

	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-danger btn-100" onclick="removeChecked()">ลบรายการ</button>
	</div>
	<input type="hidden" name="approver" id="approver" value="" />
	<input type="hidden" id="allow_over_po" value="<?php echo $allow_over_po; ?>">
	<input type="hidden" id="save-type" value="1" /> <!-- 1 = บันทึกรับทันที, 3 = บันทึกรอรับ -->
</div>
<hr class=""/>

<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="height:400px; overflow:auto;">
		<table class="table table-bordered" style="margin-bottom:0px; min-width:990px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-50 text-center"></th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">จำนวน</th>
					<th class="fix-width-100 text-center">Uom</th>
					<th class="fix-width-100 text-center">Unit Price</th>
					<th class="fix-width-100 text-center">ค้างรับ</th>
					<th calss="fix-width-100 text-center">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-table">
  <?php $no = 1; ?>
	<?php $totalQty = 0; ?>
	<?php $totalAmount = 0; ?>
	<?php if( ! empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
			<?php $uid = $rs->baseEntry.'-'.$rs->baseLine; ?>
			<tr class="font-size-11 rows" id="row-<?php echo $uid; ?>">
				<td class="middle text-center">
					<label>
						<input type="checkbox" class="ace chk" value="<?php echo $uid; ?>" />
						<span class="lbl"></span>
					</label>
				</td>
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle"><?php echo $rs->product_name; ?></td>
				<td class="middle text-center">
					<input type="number"
						class="form-control input-sm text-center receive-qty"
						id="receive-qty-<?php echo $uid; ?>"
						data-uid="<?php echo $uid; ?>"
						data-id="<?php echo $rs->id; ?>"
						data-limit="<?php echo $rs->limit; ?>"
						data-backlogs="<?php echo $rs->backlogs; ?>"
						data-price="<?php echo $rs->price; ?>"
						data-baseentry="<?php echo $rs->baseEntry; ?>"
						data-baseline="<?php echo $rs->baseLine; ?>"
						data-code="<?php echo $rs->product_code; ?>"
						data-name="<?php echo $rs->product_name; ?>"
						data-vatcode="<?php echo $rs->vatGroup; ?>"
						data-vatrate="<?php echo $rs->vatRate; ?>"
						data-currency="<?php echo $rs->currency; ?>"
						data-rate="<?php echo $rs->rate; ?>"
						value="<?php echo round($rs->qty, 2); ?>"
						onchange="recalAmount(<?php echo $uid; ?>)" />
				</td>
				<td class="middle text-center">
					<input type="text" class="form-control input-sm text-center"
						id="uom-<?php echo $uid; ?>" value="<?php echo $rs->unitMsr; ?>"
						data-uom="<?php echo $rs->UomCode; ?>" data-uom2="<?php echo $rs->UomCode2; ?>"
						data-unitmsr="<?php echo $rs->unitMsr; ?>" data-unitmsr2="<?php echo $rs->unitMsr2; ?>"
						data-uomentry="<?php echo $rs->UomEntry; ?>" data-uomentry2="<?php echo $rs->UomEntry2; ?>"
						dtaa-numpermsr="<?php echo $rs->NumPerMsr; ?>" data-numpermsr2="<?php echo $rs->NumPerMsr2; ?>"
						/>
				</td>
				<td class="middle">
					<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-<?php echo $uid; ?>" value="<?php echo number($rs->price, 4); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="text" class="form-control input-sm text-right text-label" id="backlogs-<?php echo $uid; ?>" data-backlogs="<?php echo $rs->backlogs; ?>" value="<?php echo number($rs->backlogs); ?>" readonly />
				</td>

				<td class="fix-width-120 middle">
					<input type="text" class="form-control input-sm text-right text-label" id="line-total-<?php echo $uid; ?>" value="<?php echo number($rs->amount, 4); ?>" readonly/>
				</td>
			</tr>
			<?php $no++; ?>
			<?php $totalQty += $rs->qty; ?>
			<?php $totalAmount += $rs->amount; ?>
		<?php endforeach; ?>
	<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>
<div class="hide" id="batch-rows-table">
	<?php if( ! empty($batch_details)) : ?>
		<?php foreach($batch_details as $rs) : ?>
			<?php $uid = $rs->baseEntry.'-'.$rs->baseLine; ?>
			<input type="hidden" class="batch-rows <?php echo $uid; ?>"
			id="<?php echo $rs->LineNum.'-'.$uid; ?>"
			data-uid="<?php echo $uid; ?>"
			data-qty="<?php echo $rs->Qty; ?>"
			data-batch="<?php echo $rs->BatchNum; ?>"
			value="" />
		<?php endforeach; ?>
	<?php endif; ?>
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
          <input type="text" class="form-control input-sm text-right" id="total-receive" value="<?php echo number($totalQty); ?>" disabled>
        </div>
      </div>
			<div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($totalAmount, 4); ?>" disabled>
        </div>
      </div>
		</div>
	</div>
</div>

<?php $this->load->view('inventory/receive_material/receive_modal'); ?>

<?php $this->load->view('cancle_modal'); ?>

<script id="receive-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11 rows" id="row-{{uid}}" data-no="0" data-uid="{{uid}}">
			<td class="middle text-center no">{{no}}</td>
			<td class="middle text-center"><a class="pointer" href="javascript:removeRow({{uid}})" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a></td>
			<td class="middle">
				{{pdCode}}
				<a class="pointer pull-right add-batch" href="javascript:addBatchRow('{{uid}}')" title="Manage Batch Number">
					<i class="fa fa-plus fa-lg blue"></i>
				</a>
			</td>
			<td class="middle">{{pdName}}</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-center text-label receive-qty"
					id="receive-qty-{{uid}}"
					data-uid="{{uid}}"
					data-limit="{{limit}}"
					data-backlogs="{{backlogs}}"
					data-price="{{price}}"
					data-baseentry="{{baseEntry}}"
					data-baseline="{{baseLine}}"
					data-code="{{pdCode}}"
					data-name="{{pdName}}"
					data-vatcode="{{vatCode}}"
					data-vatrate="{{vatRate}}"
					data-currency="{{currency}}"
					data-uom="uomCode"
					data-uom2="{{uomCode2}}"
					data-unitmsr="{{unitMsr}}"
					data-unitmsr2="{{unitMsr2}}"
					data-uomentry="{{uomEntry}}"
					data-uomentry2="{{uomEntry2}}"
					dtaa-numpermsr="{{numPerMsr}}"
					data-numpermsr2="{{numPerMsr2}}"
					value="{{qty}}"
					onchange="recalAmount({{uid}})" />
			</td>
			<td class="middle text-center">{{unitMsr}}</td>
			<td class="middle">
				<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-{{uid}}" value="{{priceLabel}}" readonly />
			</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-sm text-right text-label"
					id="backlogs-{{uid}}" data-backlogs="{{backlogs}}" value="{{backLogsLabel}}" readonly />
			</td>
			<td class="middle fix-width-120">
				<input type="text" class="form-contorl input-sm text-right text-label" id="line-total-{{uid}}" data-amount="{{amount}}" value="{{amountLabel}}" readonly/>
			</td>
		</tr>
	{{/each}}
</script>


<script id="child-row-template" type="text/x-handlebarsTemplate">
	<tr class="font-size-11 child-row blue italic" id="child-row-{{cuid}}" data-no="{{no}}" data-uid="{{cuid}}" data-parent="{{uid}}">
		<td class="middle text-center no">{{no}}</td>
		<td class="middle text-center">
			<a class="pointer pull-right add-batch" href="javascript:removeChildRow({{cuid}})" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
		</td>
		<td class="middle text-right">Batch No.</td>
		<td class="middle">
			<input type="text"
			class="form-control input-sm text-label blue batch-row"
			style="height:21px; font-style:italic; color:#478fca !important;"
			id="batch-{{cuid}}"
			data-uid="{{cuid}}"
			data-parent="{{uid}}" value="" />
			</td>
		<td class="middle">
			<input type="number"
			class="form-control input-sm text-center text-label blue batch-qty"
			style="height:21px; font-style:italic;  color:#478fca !important;"
			id="batch-qty-{{cuid}}"
			data-uid="{{cuid}}"
			data-parent="{{uid}}" />
		</td>
		<td class="middle text-center">{{unitMsr}}</td>
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
