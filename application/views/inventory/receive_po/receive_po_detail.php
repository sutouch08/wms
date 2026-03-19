<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/receive_po/style'); ?>
<?php
$pm = get_permission('APACWR', $this->_user->uid, $this->_user->id_profile);
$canAccept = FALSE;

if (! empty($pm))
{
	$canAccept = (($pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0  or $this->_SuperAdmin) ? TRUE : FALSE;
}
?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?> </h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<button type="button" class="btn btn-xs btn-info btn-top" onclick="printReceived()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php if ($this->pm->can_edit && $doc->status == 0) : ?>
				<button type="button" class="btn btn-xs btn-warning btn-top" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
			<?php endif; ?>
			<?php if ($this->pm->can_edit && $doc->status != 2 && $doc->status != 0 or ($doc->is_wms == 0 or $this->_SuperAdmin)) : ?>
				<button type="button" class="btn btn-xs btn-primary top-btn" onclick="pullBack('<?php echo $doc->code; ?>')">ดึงสถานะกลับมาแก้ไข</button>
			<?php endif; ?>
			<?php if ($doc->status == 1) : ?>
				<button type="button" class="btn btn-xs btn-success btn-top" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>
			<?php if ($doc->status == 4 && ($doc->user_id = $this->_user->id or $canAccept)) : ?>
				<button type="button" class="btn btn-xs btn-success btn-top" onclick="accept()"><i class="fa fa-check-circle"></i> ยืนยันการรับสินค้า</button>
			<?php endif; ?>
			<?php if ($this->pm->can_delete && $doc->status != 2) : ?>
				<button type="button" class="btn btn-xs btn-danger" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i> ยกเลิก</button>
			<?php endif; ?>
		</p>
	</div>
</div>
<hr class="padding-5" />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Doc date</label>
		<input type="text" class="form-control input-sm text-center e" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Due date</label>
		<input type="text" class="form-control input-sm text-center e" id="due-date" value="<?php echo empty($doc->due_date) ? NULL : thai_date($doc->due_date); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Post date</label>
		<input type="text" class="form-control input-sm text-center e" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>รับที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo is_wms_text($doc->is_wms); ?>" disabled />		
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>รหัสผู้จำหน่าย</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->vendor_code; ?>" disabled />
	</div>
	<div class="col-lg-5 col-md-8 col-sm-8 col-xs-12 padding-5">
		<label>ผู้จำหน่าย</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->vendor_name; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->po_code; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled />
	</div>

	<div class="col-lg-3 col-md-4-harf col-sm-4-harf col-xs-12 padding-5">
		<label>คลัง</label>
		<input type="text" id="" class="form-control input-sm" value="<?php echo $doc->warehouse_name; ?>" disabled />
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-8 padding-5">
		<label>ชื่อโซน</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Currency</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->currency; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Rate</label>
		<input type="text" class="form-control input-sm text-center" id="DocRate" value="<?php echo number($doc->rate, 4); ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo receive_po_status_text($doc->status, $doc->is_expire); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled />
	</div>
	<input type="hidden" id="code" value="<?php echo $doc->code; ?>">
	<input type="hidden" id="vendor" data-code="<?php echo $doc->vendor_code; ?>" data-name="<?php $doc->vendor_name; ?>" value="">
</div>

<?php
if ($doc->is_expire or $doc->status == 2)
{
	if ($doc->status == 2)
	{
		$this->load->view('cancle_watermark');
	}
	else
	{
		$this->load->view('expire_watermark');
	}
}
else
{
	if ($doc->status == 3)
	{
		$this->load->view('on_process_watermark');
	}

	if ($doc->status == 4)
	{
		$this->load->view('accept_watermark');
	}
}
?>
<hr class="margin-top-15 padding-5" />
<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-bordered details-table" style="min-width: 1030px;;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">ราคา</th>
					<th class="fix-width-80 text-center">ส่วนลด(%)</th>
					<th class="fix-width-100 text-center">ราคาหลังส่วนลด</th>
					<th class="fix-width-80 text-center">จำนวนส่ง</th>
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
							<td class="middle text-right"><?php echo number($rs->receive_qty, 2); ?></td>
							</td>
							<td class="middle text-right">
								<input type="text" class="form-control input-xs text-right text-label" id="line-total-<?php echo $uid; ?>" value="<?php echo number($rs->LineTotal, 4); ?>" readonly />
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

		<?php if ($doc->status == 2) : ?>
			<div class="divider-hidden"></div>
			<span class="display-block red">ยกเลิกโดย : <?php echo $doc->cancle_user; ?> &nbsp;&nbsp;&nbsp; วันที่ : <?php echo thai_date($doc->cancel_date, TRUE); ?></span>
			<span class="display-block red">เหตุผลในการยกเลิก : <?php echo $doc->cancle_reason; ?></span>
		<?php endif; ?>

		<?php if (! empty($approve_logs)) : ?>
			<div class="divider-hidden"></div>
			<?php foreach ($approve_logs as $logs) : ?>
				<?php if ($logs->approve == 1) : ?>
					<span class="green display-block">อนุมัติโดย : <?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?></span>
				<?php else : ?>
					<span class="red display-block">ยกเลิกอนุมัติโดย : <?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?> </span>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ($doc->must_accept == 1 && $doc->is_accept == 1) : ?>
			<div class="divider-hidden"></div>
			<span class="green display-block">ยืนยันการรับโดย : <?php echo $doc->accept_by; ?> @ <?php echo thai_date($doc->accept_on, TRUE); ?></span>
			<span class="green display-block">หมายเหตุ : <?php echo $doc->accept_remark; ?></span>
		<?php endif; ?>
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
					<input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($doc->TotalQty, 2); ?>" disabled>					
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number(($doc->DocTotal - $doc->VatSum + $doc->DiscAmount), 2); ?>" disabled>
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
					<input type="text" class="form-control input-sm text-right" id="disc-amount" value="<?php echo number($doc->DiscAmount, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="vat-sum" value="<?php echo number($doc->VatSum, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="<?php echo number($doc->DocTotal, 2); ?>" disabled>
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


<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal', ['op' => TRUE]); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>