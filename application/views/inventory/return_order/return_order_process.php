<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 3 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					<i class="ace-icon fa fa-save icon-on-left"></i>
					บันทึก
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="primary">
						<a href="javascript:saveAsDraft()">บันทึกเป็นดราฟท์</a>
					</li>
					<li class="success">
						<a href="javascript:saveAndClose()">บันทึกรับเข้าทันที</a>
					</li>
				</ul>
			</div>
		<?php endif; ?>
  </div>
</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
	</div>
	<?php $disabled = $this->pm->can_edit ? "" : 'disabled'; ?>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่บิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label>คลัง[รับคืน]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->warehouse_code.' | '.$doc->warehouse_name; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-4-harf col-md-6-harf col-sm-6-harf col-xs-6 padding-5">
		<label>ชื่อโซน]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>สถานะ</label>
		<input type="text" class="width-100 text-center" value="รอรับเข้า" disabled />
	</div>
	<div class="col-lg-10-harf col-md-9 col-sm-9 col-xs-8 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->inv_code; ?>" disabled/>
	</div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer-code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" id="warehouse" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" id="zone-code" value="<?php echo $doc->zone_code; ?>" />

<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-4 padding-5">
    <div class="input-group width-100">
      <span class="input-group-addon">QTY.</span>
      <input type="number" class="width-100 text-center r" id="qty" value="1" autocomplete="off"/>
    </div>
  </div>

  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-8 padding-5">
    <div class="input-group width-100">
      <span class="input-group-addon">Barcode</span>
      <input type="text" class="width-100 text-center r" id="barcode" value="" placeholder="Scan barcode item" autocomplete="off" autofocus />
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs padding-5">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="doReceive()">OK</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1200px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-100">บาร์โค้ด</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-150">สินค้า</th>
					<th class="fix-width-120 text-center">เลขที่บิล</th>
					<th class="fix-width-120 text-center">ออเดอร์</th>
					<th class="fix-width-80 text-center">ราคา</th>
					<th class="fix-width-100 text-center">ส่วนลด</th>
					<th class="fix-width-80 text-center">จำนวนคืน</th>
					<th class="fix-width-80 text-center">จำนวนรับ</th>
					<th class="fix-width-120 text-right">มูลค่า(รับ)</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  $total_qty = 0; ?>
<?php  $total_reveice_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php  foreach($details as $rs) : ?>
  <?php $uid = empty($rs->DocEntry) ? $rs->id : $rs->DocEntry."-".$rs->LineNum; ?>
				<tr class="font-size-11" id="row-<?php echo $rs->uid; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->barcode; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $doc->is_pos_api ? $rs->bill_code : $rs->invoice_code; ?></td>
					<td class="middle text-center"><?php echo $rs->order_code; ?></td>
					<td class="middle text-center"><?php echo number($rs->price, 2); ?></td>
					<td class="middle text-center"><?php echo $rs->discount_percent; ?> %</td>
					<td class="middle text-center"><?php echo round($rs->qty,2); ?></td>
					<td class="middle text-center">
            <input type="number"
    					class="form-control input-sm text-center input-qty <?php echo $rs->bc; ?>"
    					id="qty-<?php echo $rs->uid; ?>"
    					data-id="<?php echo $rs->id; ?>"
    					data-uid="<?php echo $rs->uid; ?>"
    					data-pdcode="<?php echo $rs->product_code; ?>"
    					data-pdname="<?php echo $rs->product_name; ?>"
    					data-invoice="<?php echo $rs->invoice_code; ?>"
    					data-order="<?php echo $rs->order_code; ?>"
    					data-sold="<?php echo round($rs->qty); ?>"
              data-limit="<?php echo round($rs->qty); ?>"
    					data-price="<?php echo $rs->price; ?>"
    					data-discount="<?php echo $rs->discount_percent; ?>"
    					data-docentry="<?php echo $rs->DocEntry; ?>"
    					data-linenum="<?php echo $rs->LineNum; ?>"
    					value="<?php echo $rs->receive_qty; ?>"
    					onkeyup="recalRow($(this))" />
          </td>
					<td class="middle text-right amount-label" id="amount-<?php echo $rs->uid; ?>"><?php echo number($rs->amount,2); ?></td>
				</tr>
<?php
				$no++;
				$total_qty += $rs->qty;
				$total_reveice_qty += $rs->receive_qty;
				$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
				<tr>
					<td colspan="8" class="middle text-right">รวม</td>
					<td class="middle text-center"><?php echo number($total_qty); ?></td>
					<td class="middle text-center"><?php echo number($total_reveice_qty); ?></td>
					<td class="middle text-right"><?php echo number($total_amount, 2); ?></td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('YmdH');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('YmdH');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_control.js?v=<?php echo date('YmdH');?>"></script>
<?php $this->load->view('include/footer'); ?>
