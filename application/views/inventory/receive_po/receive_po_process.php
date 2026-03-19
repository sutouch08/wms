<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>
<?php $this->load->view('inventory/receive_po/style'); ?>

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
	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-3 col-xs-6 padding-5">
		<label>Doc. date</label>
		<input type="text" class="form-control input-sm text-center e" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-3 col-xs-6 padding-5">
		<label>Due date</label>
		<input type="text" class="form-control input-sm text-center e" id="due-date" value="<?php echo empty($doc->due_date) ? NULL : thai_date($doc->due_date); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-3 col-xs-6 padding-5">
		<label>Post date</label>
		<input type="text" class="form-control input-sm text-center e" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="form-control input-sm text-center e" id="vendor-code" value="<?php echo $doc->vendor_code; ?>" onchange="poInit()" disabled />
	</div>
	<div class="col-lg-4-harf col-md-4 col-sm-6 col-xs-8 padding-5">
		<label>ผู้ขาย</label>
		<input type="text" class="form-control input-sm e" id="vendor-name" value="<?php echo $doc->vendor_name; ?>" onchange="poInit()" disabled />
	</div>
	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center e" name="invoice" id="invoice" value="<?php echo $doc->invoice_code; ?>" disabled />
	</div>

	<?php $p_disabled = empty($doc->po_code) ? '' : 'disabled'; ?>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center e" id="po-code" data-vendor="<?php echo $doc->vendor_code; ?>" value="<?php echo $doc->po_code; ?>" disabled />
	</div>

	<div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm" id="warehouse" disabled>
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm e" id="zone-code" placeholder="zone code" value="<?php echo $doc->zone_code; ?>" data-warehouse="<?php echo $doc->warehouse_code; ?>" disabled />
	</div>
	<div class="col-lg-4 col-md-6-harf col-sm-6 col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm e" id="zone-name" placeholder="zone name" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
</div>
<hr class="margin-top-15 margin-bottom-15" />
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 hidden-xs">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 hidden-xs padding-5">
		<input type="number" class="form-control input-sm text-center" id="qty" value="1.00" placeholder="จำนวน" />
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 hidden-xs padding-5">
		<input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 hidden-xs padding-5">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="checkBarcode()"><i class="fa fa-check"></i> ตกลง</button>
	</div>
</div>
<hr class="margin-top-15" />
<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-bordered details-table" style="min-width: 1070px;;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">ราคา</th>
					<th class="fix-width-100 text-center">ส่วนลด(%)</th>
					<th class="fix-width-100 text-center">ราคาหลังส่วนลด</th>
					<th class="fix-width-100 text-center">จำนวนส่ง</th>
					<th class="fix-width-80 text-center">จำนวนรับ</th>
					<th class="fix-width-100 text-center">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-table">
				<?php $no = 1; ?>
				<?php if (! empty($details)) : ?>
					<?php $totalRequest = 0; ?>
					<?php foreach ($details as $rs) : ?>
						<?php $uid = $rs->baseEntry . $rs->baseLine; ?>
						<tr class="font-size-11" id="row-<?php echo $uid; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle hide-text"><?php echo $rs->product_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->PriceBefDi, 4); ?></td>
							<td class="middle text-right"><?php echo number($rs->DiscPrcnt, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->PriceAfDisc, 4); ?></td>
							<td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
							<td class="middle text-center">
								<input type="text"
									class="form-control input-xs text-right receive-qty"
									id="receive-qty-<?php echo $uid; ?>"
									data-id="<?php echo $rs->id; ?>"
									data-uid="<?php echo $uid; ?>"
									data-limit="<?php echo $rs->qty; ?>"
									data-bprice="<?php echo $rs->PriceBefDi; ?>"
									data-aprice="<?php echo $rs->price; ?>"
									data-price="<?php echo $rs->PriceAfDisc; ?>"
									data-discprcnt="<?php echo $rs->DiscPrcnt; ?>"
									data-baseentry="<?php echo $rs->baseEntry; ?>"
									data-baseline="<?php echo $rs->baseLine; ?>"
									data-code="<?php echo $rs->product_code; ?>"
									data-name="<?php echo $rs->product_name; ?>"
									data-vatcode="<?php echo $rs->vatGroup; ?>"
									data-vatrate="<?php echo $rs->vatRate; ?>"
									data-vatamount="0"
									data-uomcode="<?php echo $rs->UomCode; ?>"
									data-unitmsr="<?php echo $rs->unitMsr; ?>"
									value=""
									onchange="recalAmount('<?php echo $uid; ?>')" />
							</td>
							</td>
							<td class="middle text-right">
								<input type="text" class="form-control input-xs text-right text-label" id="line-total-<?php echo $uid; ?>" value="" readonly />
								<input type="hidden" class="<?php echo $rs->barcode; ?>" data-code="<?php echo $rs->product_code; ?>" data-limit="<?php echo $rs->qty; ?>" value="<?php echo $uid; ?>" />
							</td>
						</tr>
						<?php $no++; ?>
						<?php $totalRequest += $rs->qty; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<div class="divider-hidden"></div>
<div class="divider-hidden"></div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-3 control-label no-padding-right">User</label>
				<div class="col-lg-5 col-md-6 col-sm-10 col-xs-9 padding-5">
					<input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-3 control-label no-padding-right">Remark</label>
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 padding-5">
					<textarea class="form-control input-sm" id="remark" rows="3" disabled><?php echo $doc->remark; ?></textarea>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-2 col-xs-6 control-label no-padding-right">จำนวนส่ง</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="req-qty" value="<?php echo number($totalRequest, 2); ?>" disabled>
				</div>
				<div class="divider-hidden visible-xs"></div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label no-padding-right">จำนวนรับ</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="total-qty" value="0" disabled>
					<input type="hidden" id="req-qty" value="<?php echo $totalRequest; ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="0" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-6 col-md-5 col-sm-4 col-xs-3 control-label no-padding-right">ส่วนลด</label>
				<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 padding-5">
					<span class="input-icon input-icon-right">
						<input type="number" class="form-control input-sm text-right" id="disc-percent" value="<?php echo number($doc->DiscPrcnt, 2); ?>" disabled>
						<i class="ace-icon fa fa-percent"></i>
					</span>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="disc-amount" value="0" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="vat-sum" value="0" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="0" disabled>
				</div>
			</div>
		</div>
	</div>
	<div class="divider visible-xs"></div>
	<div class="col-xs-12 visible-xs">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-xs-12 control-label no-padding-right">User</label>
				<div class="col-xs-12 padding-5">
					<input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-xs-12 padding-5">
					<textarea class="form-control input-sm" rows="3" disabled><?php echo $doc->remark; ?></textarea>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="vendor" data-code="<?php echo $doc->vendor_code; ?>" data-name="<?php echo $doc->vendor_name; ?>">
<input type="hidden" id="zone" data-code="<?php echo $doc->zone_code; ?>" data-name="<?php echo $doc->zone_name; ?>" data-warehouse="<?php echo $doc->warehouse_code; ?>">
<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="approver" value="" />
<input type="hidden" id="allow-over-po" value="<?php echo $doc->allow_over_po; ?>">

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>