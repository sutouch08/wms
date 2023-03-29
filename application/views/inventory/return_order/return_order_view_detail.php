<?php $this->load->view('include/header'); ?>
<?php
	$pm = get_permission('APACSM', $this->_user->uid, $this->_user->id_profile);
	$canAccept = NULL;

	if( ! empty($pm))
	{
		$canAccept = ($pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0  OR $this->_SuperAdmin ? TRUE : FALSE;
	}
	?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>

  <?php if($doc->status == 1 && $doc->is_complete == 1 && $doc->is_approve == 1) : ?>
				<button type="button" class="btn btn-xs btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
	<?php endif; ?>

	<?php if($this->isAPI && $doc->is_wms == 1 && $doc->api == 1 && $doc->status != 0 && $doc->status !=2 && $doc->is_complete != 1 && $doc->is_approve == 1) : ?>
				<button type="button" class="btn btn-xs btn-success top-btn" onclick="sendToWms()"><i class="fa fa-send"></i> Send to WMS</button>
	<?php endif; ?>
	<?php if($doc->status == 4 && ($doc->uname == $this->_user->uname OR $canAccept)) : ?>
		<button type="button" class="btn btn-xs btn-success top-btn" onclick="accept()">ยืนยันการรับสินค้า</button>
	<?php endif; ?>

	<?php if($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-xs btn-danger top-btn" onclick="unsave()">ยกเลิกการบันทึก</button>
	<?php endif; ?>
	<?php if($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_approve) : ?>
				<button type="button" class="btn btn-xs btn-primary top-btn" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
	<?php endif; ?>
	<?php if($doc->is_wms == 0 && ($doc->status == 1 OR $doc->status == 4) && $doc->is_approve == 1 && $this->pm->can_approve) : ?>
				<button type="button" class="btn btn-xs btn-danger top-btn" onclick="unapprove()"><i class="fa fa-refresh"></i> ไม่อนุมัติ</button>
	<?php endif; ?>
				<button type="button" class="btn btn-xs btn-info top-btn" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>
				<button type="button" class="btn btn-xs btn-info top-btn" onclick="printWmsReturn()"><i class="fa fa-print"></i> พิมพ์ใบส่งของ</button>
    </p>
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
      <input type="text" class="form-control input-sm text-center edit" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center edit" name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center edit" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>" disabled />
		</div>
		<div class="col-lg-6 col-md-5 col-sm-5 col-xs-8 padding-5">
			<label>ชื่อลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled/>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>คลัง[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="warehouse" id="warehouse" value="<?php echo $doc->warehouse_name; ?>" disabled />
		</div>
		<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>รหัสโซน</label>
			<input type="text" class="form-control input-sm text-center edit" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled />
		</div>
		<div class="col-lg-6-harf col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
			<label>ชื่อโซน]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm" name="status" disabled>
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $doc->status); ?>>ยังไม่บันทึก</option>
  			<option value="1" <?php echo is_selected('1', $doc->status); ?>>บันทึกแล้ว</option>
  			<option value="2" <?php echo is_selected('2', $doc->status); ?>>ยกเลิก</option>
				<option value="3" <?php echo is_selected('3', $doc->status); ?>>WMS Process</option>
  		</select>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>รับที่</label>
			<select class="form-control input-sm" disabled>
				<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>WMS</option>
				<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>warrix</option>
			</select>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Interface</label>
			<select class="form-control input-sm" disabled>
				<option value="1" <?php echo is_selected('1', $doc->api); ?>>ปกติ</option>
				<option value="0" <?php echo is_selected('0', $doc->api); ?>>ไม่ส่ง</option>
			</select>
		</div>
		<?php if($doc->status == 1) : ?>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    	<label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>SAP No.</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->inv_code; ?>" disabled/>
		</div>
	<?php elseif($doc->status == 2) : ?>
		<div class="col-lg-4-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
    	<label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
    </div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>เหตุผลในการยกเลิก</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled/>
		</div>
	<?php else : ?>
		<div class="col-lg-7-harf col-7-harf col-sm-7-harf col-xs-12 padding-5">
    	<label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
    </div>
	<?php endif; ?>
	</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" />

<hr class="margin-top-15 margin-bottom-15"/>
<?php
if($doc->is_expire == 1)
{
	$this->load->view('expire_watermark');
}
else
{
	if($doc->status == 2)
	{
		$this->load->view('cancle_watermark');
	}

	if($doc->status == 3)
	{
		$this->load->view('on_process_watermark');
	}

	if($doc->status == 4)
	{
		$this->load->view('accept_watermark');
	}
}
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1180px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-100">บาร์โค้ด</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-150">สินค้า</th>
					<th class="fix-width-100 text-center">เลขที่บิล</th>
					<th class="fix-width-120 text-center">ออเดอร์</th>
					<th class="fix-width-80 text-right">ราคา</th>
					<th class="fix-width-100 text-right">ส่วนลด</th>
					<th class="fix-width-80 text-right">จำนวนคืน</th>
					<th class="fix-width-80 text-right">จำนวนรับ</th>
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
	<?php $hilight = $rs->qty > $rs->receive_qty ? "color:red;" : ""; ?>
				<tr style="<?php echo $hilight; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->barcode; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $rs->invoice_code; ?></td>
					<td class="middle text-center"><?php echo $rs->order_code; ?></td>
					<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
					<td class="middle text-right"><?php echo $rs->discount_percent; ?> %</td>
					<td class="middle text-right"><?php echo round($rs->qty,2); ?></td>
					<td class="middle text-right"><?php echo round($rs->receive_qty,2); ?></td>
					<td class="middle text-right"><?php echo number($rs->amount,2); ?></td>
				</tr>
<?php
				$no++;
				$total_qty += $rs->qty;
				$total_reveice_qty += $rs->receive_qty;
				$total_amount += ($rs->receive_qty * $rs->price);
?>
<?php  endforeach; ?>
				<tr>
					<td colspan="8" class="middle text-right">รวม</td>
					<td class="middle text-right" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="middle text-right" id="total-qty"><?php echo number($total_reveice_qty); ?></td>
					<td class="middle text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if(!empty($approve_list)) :?>
			<?php foreach($approve_list as $appr) : ?>
					<?php if($appr->approve == 1) : ?>
						<span class="green display-block">อนุมัติโดย : <?php echo $appr->approver; ?> @ <?php echo thai_date($appr->date_upd, TRUE); ?></span>
					<?php endif; ?>
					<?php if($appr->approve == 0) : ?>
						<span class="red display-block">ยกเลิกการอนุมัติโดย : <?php echo $appr->approver; ?> @ <?php echo thai_date($appr->date_upd, TRUE); ?></span>
					<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if($doc->must_accept == 1 && $doc->is_accept == 1) : ?>
			<span class="green display-block">ยืนยันการรับโดย : <?php echo $doc->accept_by; ?> @ <?php echo thai_date($doc->accept_on, TRUE); ?></span>
			<span class="green display-block">หมายเหตุ : <?php echo $doc->accept_remark; ?></span>
		<?php endif; ?>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal'); ?>


<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
