<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($doc->status == 'O' && ! $this->isSale) OR (($doc->status == 'C' OR $doc->status == 'D') && ($this->isAdmin OR $this->isSuperAdmin))) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="rollback('<?php echo $doc->code; ?>')"><i class="fa fa-refresh"></i> ย้อนสถานะ</button>
		<?php endif; ?>
		<?php if($doc->status == 'C') : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send to SAP</button>
		<?php endif; ?>
		<?php if($doc->status == 'O') : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="process('<?php echo $doc->code; ?>')"><i class="fa fa-qrcode"></i> รับสินค้า</button>
		<?php endif; ?>
		<?php if($doc->status == 'P') : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
		<?php endif; ?>
  </div>
</div>
<hr />
<?php if($doc->status == 'D') { $this->load->view('cancle_watermark'); } ?>
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
		<select class="width-100 r" id="DocCur" disabled>
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="<?php echo $doc->Rate; ?>"  disabled/>
	</div>

	<div class="col-lg-3 col-md-2-harf col-sm-4 col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" data-prev="<?php echo $doc->warehouse_code; ?>" disabled>
			<option value="">Select</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label class="hidden-xs">Bin Location</label><label class="visible-xs">Bin.</label>
		<input type="text" class="width-100 text-center r" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>
	<?php $zoneName = $this->zone_model->getName($doc->zone_code); ?>
	<div class="col-lg-3-harf col-md-1-harf col-sm-2 col-xs-8 padding-5">
		<label class="not-show">bin name</label>
		<input type="text" class="width-100 r" id="zone-name" data-prev="<?php echo $zoneName; ?>" value="<?php echo $zoneName; ?>" disabled />
	</div>
	<div class="col-lg-11 col-md-10-harf col-sm-6-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 r" id="remark" data-prev="<?php echo $doc->remark; ?>" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>SAP No.</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->DocNum; ?>" disabled>
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<?php $po_ref = $doc->po_code; ?>
<?php if( ! empty($po_refs)) : ?>
	<?php $po_ref = ""; ?>
	<?php $p = 1; ?>
	<?php foreach($po_refs as $ref) : ?>
		<?php $po_ref .= $p == 1 ? $ref->po_code : ", {$ref->po_code}"; ?>
		<?php $p++; ?>
	<?php endforeach; ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<b>Po reference : </b><?php echo $po_ref; ?>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1 table-responsive" id="receiveTable" style="padding-left:0; padding-right:0; max-height:400px; overflow:auto;">
		<table class="table table-bordered" style="font-size:11px; min-width:1010px; margin-bottom:0;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-100 text-center">รหัสสินค้า</th>
					<th class="min-width-250 text-center">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">หน่วยนับ</th>
					<th class="fix-width-80 text-center">PO No.</th>
					<th class="fix-width-100 text-center">ราคา</th>
					<th class="fix-width-80 text-center">ส่วนลด</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-80 text-center">จำนวนรับ</th>
					<th class="fix-width-100 text-center">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-list">
<?php $totalQty = 0; ?>
<?php $totalAmount = 0; ?>
<?php $minRows = 10; ?>
<?php $no = 1; ?>
<?php if( ! empty($details)) : ?>
	<?php foreach($details as $rs) : ?>
		<?php $red = $rs->ReceiveQty < $rs->Qty ? 'red' : ''; ?>
				<tr class="<?php echo $red; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->ItemCode; ?></td>
					<td class="middle"><?php echo $rs->ItemName; ?></td>
					<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
					<td class="middle text-center"><?php echo $rs->baseCode; ?></td>
					<td class="middle text-center"><?php echo number($rs->PriceBefDi, 4); ?></td>
					<td class="middle text-center"><?php echo number($rs->DiscPrcnt, 2); ?></td>
					<td class="middle text-center"><?php echo number($rs->Qty, 2); ?></td>
					<td class="middle text-center"><?php echo number($rs->ReceiveQty, 2); ?></td>
					<td class="middle text-right"><?php echo number($rs->LineTotal, 2); ?></td>
				</tr>
				<?php $no++; ?>
				<?php $totalQty += $rs->ReceiveQty; ?>
				<?php $totalAmount += $rs->LineTotal; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php while($no <= $minRows) : ?>
			<tr>
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"></td>
				<td class="middle"></td>
				<td class="middle text-center"></td>
				<td class="middle text-center"></td>
				<td class="middle text-center"></td>
				<td class="middle text-center"></td>
				<td class="middle text-center"></td>
				<td class="middle text-center"></td>
				<td class="middle text-right"></td>
			</tr>
			<?php $no++; ?>
		<?php endwhile; ?>
			</tbody>
		</table>
  </div>

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
          <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($doc->TotalReceived, 2); ?>" disabled>
        </div>
        <label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="total-amount" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="vat-sum" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="doc-total" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal + $doc->VatSum, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>
</div> <!-- row -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	<?php if(!empty($logs)) : ?>
		<p class="log-text">
		<?php foreach($logs as $log) : ?>
			<?php echo "* ".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp; {$log->emp_name}  &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
		<?php endforeach; ?>
		</p>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
