<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <button type="button" class="btn btn-white btn-success top-btn" onclick="closeReceive()"><i class="fa fa-save"></i> Save and Close</button>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Doc Date</label>
		<input type="text" class="width-100 text-center r" id="date-add" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<input type="text" class="width-100 text-center r" id="posting-date" value="<?php echo thai_date($doc->posting_date); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code" value="<?php echo $doc->vendor_code; ?>" disabled/>
	</div>
	<div class="col-lg-5 col-md-5-harf col-sm-5 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" value="<?php echo $doc->vendor_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice" value="<?php echo $doc->invoice_code; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>PO No.</label>
		<input type="text" class="width-100 text-center r" id="po-no" value="<?php echo $doc->po_code; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
		<label>Currency</label>
		<select class="width-100 r" id="DocCur" disabled >
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="<?php echo $doc->Rate; ?>"  disabled/>
	</div>

	<div class="col-lg-3 col-md-2-harf col-sm-4 col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" disabled>
			<option value="">Select</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>Bin Location</label>
		<input type="text" class="width-100 text-center r" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>
	<?php $zoneName = $this->zone_model->getName($doc->zone_code); ?>
	<div class="col-lg-3-harf col-md-1-harf col-sm-2 col-xs-8 padding-5">
		<label class="not-show">bin name</label>
		<input type="text" class="width-100 r" id="zone-name" value="<?php echo $zoneName; ?>" disabled />
	</div>
	<div class="col-lg-12 col-md-12 col-sm-8 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 r" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php $this->load->view('receive_po/receive_process_control'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1 table-responsive" id="receiveTable" style="min-height:200px; padding-left:0; padding-right:0;">
		<table class="table table-bordered" style="font-size:11px; min-width:1130px; margin-bottom:0;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-100 text-center">Barcode</th>
					<th class="fix-width-100 text-center">รหัสสินค้า</th>
					<th class="min-width-250 text-center">ชื่อสินค้า</th>
					<th class="fix-width-80 text-center">หน่วยนับ</th>
					<th class="fix-width-80 text-center">PO No.</th>
					<th class="fix-width-80 text-center">ราคา</th>
					<th class="fix-width-80 text-center">ส่วนลด</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-100 text-center">จำนวนรับ</th>
					<th class="fix-width-100 text-center">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-list">
<?php $totalQty = 0; ?>
<?php $totalAmount = 0; ?>
<?php if( ! empty($details)) : ?>
	<?php $no = 1; ?>
	<?php foreach($details as $rs) : ?>
		<?php $uid = $rs->baseEntry."-".$rs->baseLine; ?>
				<tr id="row-<?php echo $uid; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo (! empty($rs->barcode) ? $rs->barcode : ""); ?></td>
					<td class="middle"><a style="color:#0032e7;" href="javascript:viewItemData('<?php echo $rs->ItemName; ?>', '<?php echo $uid; ?>')"><?php echo $rs->ItemCode; ?></a></td>
					<td class="middle"><?php echo $rs->ItemName; ?></td>
					<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
					<td class="middle text-center"><?php echo $rs->baseCode; ?></td>
					<td class="middle text-center"><?php echo number($rs->PriceBefDi, 2); ?></td>
					<td class="middle text-center"><?php echo number($rs->DiscPrcnt, 2); ?></td>
					<td class="middle text-center"><?php echo number($rs->Qty, 2); ?></td>
					<td class="middle text-center">
						<input type="hidden" class="<?php echo $rs->baseCode."-".md5($rs->ItemCode); ?>" value="<?php echo $uid; ?>" />
						<input type="hidden" id="item-data-<?php echo $uid; ?>" value="<?php echo $rs->item_data; ?>" />
						<input type="text" class="form-control input-sm text-right row-qty"
							id="row-qty-<?php echo $uid; ?>"
							onchange="recalAmount('<?php echo $uid; ?>')"
							data-uid="<?php echo $uid; ?>"
							data-id="<?php echo $rs->id; ?>"
							data-code="<?php echo $rs->ItemCode; ?>"
							data-name="<?php echo $rs->ItemName; ?>"
							data-vatcode="<?php echo $rs->VatGroup; ?>"
							data-vatrate="<?php echo $rs->VatRate; ?>"
							data-vatperqty="<?php echo $rs->VatPerQty; ?>"
							data-limit="<?php echo $rs->Qty; ?>"
							data-price="<?php echo $rs->Price; ?>"
							data-bfprice="<?php echo $rs->PriceBefDi; ?>"
							data-afprice="<?php echo $rs->PriceAfVAT; ?>"
							data-discprcnt="<?php echo $rs->DiscPrcnt; ?>"
							data-basecode="<?php echo $rs->baseCode; ?>"
							data-baseline="<?php echo $rs->baseLine; ?>"
							data-baseentry="<?php echo $rs->baseEntry; ?>"
							data-unitmsr="<?php echo $rs->unitMsr; ?>"
							data-numpermsr="<?php echo $rs->NumPerMsr; ?>"
							data-unitmsr2="<?php echo $rs->unitMsr2; ?>"
							data-numpermsr2="<?php echo $rs->NumPerMsr2; ?>"
							data-uomentry="<?php echo $rs->UomEntry; ?>"
							data-uomentry2="<?php echo $rs->UomEntry2; ?>"
							data-uomcode="<?php echo $rs->UomCode; ?>"
							data-uomcode2="<?php echo $rs->UomCode2; ?>"
							value="<?php echo number($rs->ReceiveQty, 2); ?>" />
					</td>
					<td class="middle text-right">
						<input type="text" class="form-control input-sm text-right row-total" id="row-total-<?php echo $uid; ?>" value="<?php echo number($rs->LineTotal, 2); ?>" disabled />
						<input type="hidden" id="row-vat-amount-<?php echo $uid; ?>" value="<?php echo $rs->VatAmount; ?>" />
					</td>
				</tr>
				<?php $no++; ?>
				<?php $totalQty += $rs->ReceiveQty; ?>
				<?php $totalAmount += $rs->LineTotal; ?>
			<?php endforeach; ?>
		<?php endif; ?>
			</tbody>
		</table>
  </div>

	<?php if( ! empty($bcList)) : ?>
		<?php foreach($bcList as $bc) : ?>
			<input type="hidden" id="bc-<?php echo $bc->Barcode; ?>"
				data-item="<?php echo $bc->ItemCode; ?>"
				data-baseqty="<?php echo $bc->BaseQty; ?>"
				data-uomentry="<?php echo $bc->UomEntry; ?>"
				data-uomcode="<?php echo $bc->UomCode; ?>"
				data-uomname="<?php echo $bc->UomName; ?>"
				value="<?php echo $bc->BaseQty; ?>"/>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user->emp_name; ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $this->user->uname; ?>" />
        </div>
      </div>
    </div>
  </div>


	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-3 col-sm-2 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm text-right" id="total-qty" value="0.00" disabled>
        </div>
        <label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="total-amount" class="form-control input-sm text-right" value="0.00" disabled/>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="vat-sum" class="form-control input-sm text-right" value="0.00" disabled />
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="doc-total" class="form-control input-sm text-right" value="0.00" disabled/>
        </div>
      </div>
    </div>
  </div>
</div> <!-- row -->

<div class="modal fade" id="item-info-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:800px; max-width:95vw; max-height:95vh;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center" id="item-info-name">Item Info</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="position:relative; min-width:250px; max-height:60vh; overflow:auto;">
						<table class="table table-bordered">
							<thead>
								<tr class="font-size-11">
									<th class="fix-width-100 text-center">Barcode</th>
									<th class="fix-width-100 text-center">SKU</th>
									<th class="min-width-300 text-center">Description</th>
									<th class="fix-width-100 text-center">Uom</th>
									<th class="fix-width-100 text-center">BaseQty</th>
								</tr>
							</thead>
							<tbody id="item-info">

							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			</div>
		</div>
	</div>
</div>

<script id="item-info-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11">
			<td class="text-center">{{barcode}}</td>
			<td class="text-center">{{ItemCode}}</td>
			<td>{{ItemName}}</td>
			<td class="text-center">{{UomName}}</td>
			<td class="text-center">{{BaseQty}}</td>
		</tr>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_process_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
