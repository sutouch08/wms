<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>
<style>
	.text-label {
		border:0px !important;
		background-color: transparent !important;
		color:#333333 !important;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($this->pm->can_edit && $doc->status == 0) : ?>
			<button type="button" class="btn btn-sm btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i> ยกเลิก</button>
		<?php endif; ?>
		<button type="button" class="btn btn-sm btn-purple top-btn hidden-xs" onclick="getSample()"><i class="fa fa-download"></i> ไฟล์ตัวอย่าง</button>
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-primary top-btn hidden-xs" onclick="getUploadFile()"><i class="fa fa-upload"></i> Import</button>

			<div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
          <i class="ace-icon fa fa-save icon-on-left"></i>
          บันทึก
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="primary">
            <a href="javascript:checkLimit(0)">บันทึกเป็นดราฟท์</a>
          </li>
					<li class="success">
            <a href="javascript:checkLimit(1)">บันทึกรับเข้าทันที</a>
          </li>
					<li class="purple">
            <a href="javascript:checkLimit(3)">บันทึกรอรับ</a>
          </li>
        </ul>
      </div>
		<?php	endif; ?>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่สินค้าเข้า</label>
		<input type="text" class="form-control input-sm text-center h" id="due-date" value="<?php echo empty($doc->due_date) ? NULL : thai_date($doc->due_date); ?>" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting date</label>
		<input type="text" class="form-control input-sm text-center h" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ช่องทางการรับ</label>
		<select class="form-control input-sm h" name="is_wms" id="is_wms">
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
	<?php $c_hide = empty($doc->po_code) ? '' : 'hide'; ?>
	<?php $p_hide = empty($doc->po_code) ? 'hide' : '' ; ?>
	<?php $p_disabled = empty($doc->po_code) ? '' : 'disabled'; ?>

	<div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center h" name="poCode" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" <?php echo $p_disabled; ?>/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5">
		<label class="display-block not-show">clear</label>
		<button type="button" class="btn btn-xs btn-info btn-block <?php echo $c_hide; ?> h" id="btn-confirm-po" onclick="confirmPo()">ยืนยัน</button>
		<button type="button" class="btn btn-xs btn-primary btn-block <?php echo $p_hide; ?> h" id="btn-get-po" onclick="getPoDetail()">แสดง</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5">
		<label class="display-block not-show">clear</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear-po" onclick="clearPo()">Clear</button>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ผู้จำหน่าย</label>
		<input type="text" class="form-control input-sm text-center h" name="vendor_code" id="vendor_code" value="<?php echo $doc->vendor_code; ?>" placeholder="รหัสผู้จำหน่าย" />
	</div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-4 col-xs-8 padding-5">
	 	<label class="not-show">vendor</label>
	  <input type="text" class="form-control input-sm h" name="vendorName" id="vendorName" value="<?php echo $doc->vendor_name; ?>" placeholder="ระบุผู้จำหน่าย" />
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-4 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center h" name="invoice" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" />
	</div>
	<div class="col-lg-4 col-md-3 col-sm-3 col-xs-8 padding-5">
		<label>คลัง</label>
		<select class="width-100 h" id="warehouse" onchange="warehouse_init()">
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm h" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo empty($zone) ? NULL : $zone->code; ?>" />
	</div>
	<div class="col-lg-7 col-md-6-harf col-sm-6 col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone h" name="zoneName" id="zoneName" placeholder="ชื่อโซน" value="<?php echo empty($zone) ? NULL : $zone->name; ?>" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm width-100" id="DocCur" onchange="changeRate()" disabled>
			<?php echo select_currency($doc->currency); ?>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Rate</label>
		<input type="number" class="form-control input-sm text-center" id="DocRate" value="<?php echo round($doc->rate, 4); ?>" disabled/>
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm h" name="remark" id="remark" value="<?php echo $doc->remark; ?>"  />
	</div>
</div>
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-danger btn-100" onclick="removeChecked()">ลบรายการ</button>
	</div>
	<input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
	<input type="hidden" id="allow_over_po" value="<?php echo $allow_over_po; ?>">
	<input type="hidden" id="save-type" value="1" /> <!-- 1 = บันทึกรับทันที, 3 = บันทึกรอรับ -->
</div>
<hr class=""/>
<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-bordered" style="margin-bottom:0px; min-width:940px;">
			<thead>
				<tr class="font-size-12">
					<th class="fix-width-50 text-center">
						<label>
							<input type="checkbox" class="ace" id="check-all" onchange="toggleCheckAll($(this))">
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">ราคา (PO)</th>
					<th class="fix-width-100 text-center">ค้างรับ</th>
					<th class="fix-width-100 text-center">จำนวน</th>
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
				<td class="middle text-center">
					<label>
						<input type="checkbox" class="ace chk" value="<?php echo $uid; ?>" />
						<span class="lbl"></span>
					</label>
				</td>
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle"><?php echo $rs->product_name; ?></td>
				<td class="middle">
					<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-<?php echo $uid; ?>" value="<?php echo number($rs->price, 4); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="text" class="form-control input-sm text-right text-label" id="backlogs-<?php echo $uid; ?>" data-backlogs="<?php echo $rs->backlogs; ?>" value="<?php echo number($rs->backlogs); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="number"
						class="form-control input-sm text-center receive-qty"
						id="receive-qty-<?php echo $uid; ?>"
						data-uid="<?php echo $uid; ?>"
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



<?php $this->load->view('inventory/receive_po/receive_modal'); ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>

<script id="receive-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11" id="row-{{uid}}">
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="{{uid}}" />
					<span class="lbl"></span>
				</label>
			</td>
			<td class="middle text-center no">{{no}}</td>
			<td class="middle">{{pdCode}}</td>
			<td class="middle">{{pdName}}</td>
			<td class="middle">
				<input type="text" class="form-control input-sm text-right text-label row-price e" id="row-price-{{uid}}" value="{{priceLabel}}" readonly />
			</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-sm text-right text-label"
					id="backlogs-{{uid}}" data-backlogs="{{backlogs}}" value="{{backLogsLabel}}" readonly />
			</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-center receive-qty"
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
					value="{{qty}}" onchange="recalAmount({{uid}})" />
			</td>
			<td class="middle fix-width-120">
				<input type="text" class="form-contorl input-sm text-right text-label" id="line-total-{{uid}}" data-amount="{{amount}}" value="{{amountLabel}}" readonly/>
			</td>
		</tr>
	{{/each}}
</script>

<script id="po-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}">
		<td class="middle text-center no">{{no}}</td>
		<td class="middle">{{pdCode}}</td>
		<td class="middle">{{pdName}}</td>
		<td class="middle text-right">{{price_label}} <span style="font-size:10px;">{{currency}}</span></td>
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
				value="" />
		</td>
    </tr>
  {{/each}}
</script>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>

<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
