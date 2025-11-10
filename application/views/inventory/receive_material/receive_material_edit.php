<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
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
				<?php if( ! $this->isSale) : ?>
					<li class="success">
						<a href="javascript:save('C')">บันทึกรับเข้าทันที</a>
					</li>
				<?php endif; ?>
				<li class="purple">
					<a href="javascript:save('O')">บันทึกรอรับ</a>
				</li>
			</ul>
		</div>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Doc Date</label>
		<input type="text" class="width-100 text-center r" id="date-add" data-prev="<?php echo thai_date($doc->date_add); ?>" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Posting Date</label>
		<input type="text" class="width-100 text-center r" id="posting-date" data-prev="<?php echo thai_date($doc->posting_date); ?>" value="<?php echo thai_date($doc->posting_date); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code" placeholder="รหัสผู้ขาย" data-prev="<?php echo $doc->vendor_code; ?>" value="<?php echo $doc->vendor_code; ?>" />
	</div>
	<div class="col-lg-5 col-md-5-harf col-sm-5 col-xs-12 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" placeholder="ชื่อผู้ขาย" data-prev="<?php echo $doc->vendor_code; ?>" value="<?php echo $doc->vendor_name; ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice" placeholder="ใบส่งสินค้า" data-prev="<?php echo $doc->invoice_code; ?>" value="<?php echo $doc->invoice_code; ?>" />
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>PO No.</label>
		<input type="text" class="width-100 text-center r" id="po-no" placeholder="อ้างอิงใบสั่งซื้อ" data-prev="<?php echo $doc->po_code; ?>" value="<?php echo $doc->po_code; ?>" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>Currency</label>
		<select class="width-100 r" id="DocCur" data-prev="<?php echo $doc->Currency; ?>" >
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" data-prev="<?php echo $doc->Rate; ?>" id="DocRate" value="<?php echo $doc->Rate; ?>"  />
	</div>

	<div class="col-lg-4 col-md-3 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" data-prev="<?php echo $doc->warehouse_code; ?>" onchange="changeWhs()">
			<option value="">Select</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-4 col-xs-12 padding-5">
		<label>Bin Location</label>
		<select class="width-100 r" id="zone-code">
			<option value="" data-whs="" data-name="">Select</option>
			<?php echo select_zone($doc->zone_code, $doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-8 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 r" id="remark" data-prev="<?php echo $doc->remark; ?>" value="<?php echo $doc->remark; ?>"/>
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php $this->load->view('receive_po/receive_po_control'); ?>
<?php $this->load->view('receive_po/receive_po_detail'); ?>

<script>
	$('#warehouse').select2();
	$('#zone-code').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
