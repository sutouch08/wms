<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>

<div class="row">
	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-white btn-success top-btn" onclick="validateReceive()"><i class="fa fa-save"></i> บันทึก</button>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" disabled/>
  </div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>วันที่สินค้าเข้า</label>
		<input type="text" class="form-control input-sm text-center h" id="due-date" value="<?php echo empty($doc->due_date) ? NULL : thai_date($doc->due_date); ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>Posting date</label>
		<input type="text" class="form-control input-sm text-center h" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>ช่องทางการรับ</label>
		<select class="form-control input-sm h" name="is_wms" id="is_wms" disabled>
			<option value="">เลือก</option>
			<?php if($this->wmsApi OR $doc->is_wms == 1) : ?>
			<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>Pioneer</option>
			<?php endif; ?>
			<?php if($this->sokoApi OR $doc->is_wms == 2) : ?>
			<option value="2" <?php echo is_selected('2', $doc->is_wms); ?>>SOKOCHAN</option>
			<?php endif; ?>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>Warrix</option>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ผู้จำหน่าย</label>
		<input type="text" class="form-control input-sm text-center h" name="vendor_code" id="vendor_code" value="<?php echo $doc->vendor_code; ?>" placeholder="รหัสผู้จำหน่าย" disabled/>
	</div>

	<div class="col-lg-4-harf col-md-5 col-sm-5 col-xs-8 padding-5">
		<label class="not-show">vendor</label>
		<input type="text" class="form-control input-sm h" name="vendorName" id="vendorName" value="<?php echo $doc->vendor_name; ?>" placeholder="ระบุผู้จำหน่าย" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center h" name="poCode" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center h" name="invoice" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" disabled/>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm h" disabled>
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm h" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo empty($zone) ? NULL : $zone->code; ?>" disabled/>
	</div>
	<div class="col-lg-4-harf col-md-5-harf col-sm-5-harf col-xs-8 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone h" name="zoneName" id="zoneName" placeholder="ชื่อโซน" value="<?php echo empty($zone) ? NULL : $zone->name; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm width-100" id="DocCur" onchange="changeRate()" disabled>
			<?php echo select_currency($doc->currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Rate</label>
		<input type="number" class="form-control input-sm text-center" id="DocRate" value="<?php echo round($doc->rate, 4); ?>" disabled/>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-9 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm h" name="remark" id="remark" value="<?php echo $doc->remark; ?>"  disabled/>
	</div>
</div>
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 hidden-xs">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 hidden-xs padding-5">
		<input type="number" class="form-control input-sm text-center" id="qty" value="1.00" placeholder="จำนวน"/>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 hidden-xs padding-5">
		<input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off"  />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 hidden-xs padding-5">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="checkBarcode()"><i class="fa fa-check"></i> ตกลง</button>
	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3 hidden-xs">&nbsp;</div>
	<input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
	<input type="hidden" id="allow_over_po" value="<?php echo $allow_over_po; ?>">
</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered border-1" style="margin-bottom:0px; min-width:940px;">
			<thead>
				<tr class="font-size-12">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">ราคา (PO)</th>
					<th class="fix-width-100 text-center">จำนวน[ส่ง]</th>
					<th class="fix-width-100 text-center">จำนวน[รับ]</th>
					<th calss="fix-width-120 text-center">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-table">
  <?php $no = 1; ?>
	<?php $totalQty = 0; ?>
	<?php $totalAmount = 0; ?>
	<?php if( ! empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
			<?php $uid = $rs->baseEntry.$rs->baseLine; ?>
			<tr class="font-size-11" id="row-<?php echo $uid; ?>">
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle"><?php echo $rs->product_name; ?></td>
				<td class="middle">
					<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-<?php echo $uid; ?>" value="<?php echo number($rs->price, 4); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="text" class="form-control input-sm text-center text-label" id="qty-<?php echo $uid; ?>" value="<?php echo number($rs->qty); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="number"
						class="form-control input-sm text-center text-label receive-qty"
						id="receive-qty-<?php echo $uid; ?>"
						data-id="<?php echo $rs->id; ?>"
						data-uid="<?php echo $uid; ?>"
						data-limit="<?php echo $rs->qty; ?>"
						data-price="<?php echo $rs->price; ?>"
						data-baseentry="<?php echo $rs->baseEntry; ?>"
						data-baseline="<?php echo $rs->baseLine; ?>"
						data-code="<?php echo $rs->product_code; ?>"
						data-name="<?php echo $rs->product_name; ?>"
						data-vatcode="<?php echo $rs->vatGroup; ?>"
						data-vatrate="<?php echo $rs->vatRate; ?>"
						data-currency="<?php echo $rs->currency; ?>"
						data-rate="<?php echo $rs->rate; ?>"
						value="" onchange="sumReceive()"/>
				</td>
				<td class="fix-width-120 middle">
					<input type="text" class="form-control input-sm text-right text-label" id="line-total-<?php echo $uid; ?>" value="0" readonly/>
					<input type="hidden"
						class="<?php echo $rs->barcode; ?>"
						data-code="<?php echo $rs->product_code; ?>"
						data-limit="<?php echo $rs->qty; ?>"
						value="<?php echo $uid; ?>"
						/>
				</td>
			</tr>
			<?php $no++; ?>
			<?php $totalQty += $rs->qty; ?>
			<?php $totalAmount += $rs->amount; ?>
		<?php endforeach; ?>
			<tr>
				<td colspan="4" class="text-right">รวม</td>
				<td class=""><input type="text" class="form-control input-sm text-center text-label" id="total-qty" value="<?php echo number($totalQty); ?>" readonly/></td>
				<td class=""><input type="text" class="form-control input-sm text-center text-label" id="total-receive" value="0" readonly/></td>
				<td class=""><input type="text" class="form-control input-sm text-right text-label" id="total-amount" value="0.0000" readonly/></td>
			</tr>
	<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>

<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
