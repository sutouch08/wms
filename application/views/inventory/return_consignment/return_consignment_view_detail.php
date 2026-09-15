<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if ($doc->is_approve == 1) : ?>
			<?php if ($doc->status == 1) : ?>
				<button type="button" class="btn btn-white btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>			
		<?php endif; ?>

		<?php if ($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="unsave()">ยกเลิกการบันทึก</button>
		<?php endif; ?>
		<?php if ($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
		<?php endif; ?>

		<?php if($this->pm->can_edit && ($doc->status == 0 OR $doc->status == 3)) : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
			<?php if($doc->status == 3 && $doc->product_arrival == 0) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="setProductArrival('<?php echo $doc->code; ?>', 1)"><i class="fa fa-check"></i> สินค้ามาถึงแล้ว</button>
			<?php endif; ?>
			<?php if($doc->status == 3 && $doc->product_arrival == 1) : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="setProductArrival('<?php echo $doc->code; ?>', 0)"><i class="fa fa-times"></i> สินค้าไม่มาถึง</button>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->pm->can_delete && $doc->status != 2) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
		<?php endif; ?>		

		<?php if ($doc->status != 0 && $this->_SuperAdmin) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="pullBack('<?php echo $doc->code; ?>')">ดึงสถานะกลับมาแก้ไข</button>
		<?php endif; ?>

		<?php if ($doc->status != 0) : ?>
			<button type="button" class="btn btn-white btn-info top-btn" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>			
		<?php endif; ?>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่บิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>GP(%)</label>
		<input type="number" class="form-control input-sm text-center" id="gp" value="<?php echo $doc->gp; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซนฝากขาย</label>
		<input type="text" class="form-control input-sm e" id="from-zone" autocomplete="off" value="<?php echo $doc->from_zone_code; ?>" disabled />
	</div>
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label class="not-show">โซนฝากขาย</label>
		<input type="text" class="form-control input-sm e" id="from-zone-name" value="<?php echo $doc->from_zone_name; ?>" readonly disabled />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm e" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm e" id="zone-name" value="<?php echo $doc->zone_name; ?>" readonly disabled />
	</div>
	<div class="col-lg-8-harf col-md-7-harf col-sm-7-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" id="remark" value="<?php echo $doc->remark; ?>" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" disabled />
	</div>
	<?php $disabled = $this->pm->can_edit ? "" : 'disabled'; ?>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label class="font-size-2 blod">วันที่จัดส่ง</label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm text-center" id="ship-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled />
			<span class="input-group-btn">
				<button type="button"
					class="btn btn-xs btn-warning btn-block"
					id="btn-edit-ship-date" <?php echo $disabled; ?>
					<?php if ($this->pm->can_edit) : ?> onclick="activeShipDate()" <?php endif; ?>>
					<i class="fa fa-pencil" style="min-width:20px;"></i>
				</button>
				<button type="button"
					class="btn btn-xs btn-success btn-block hide"
					id="btn-update-ship-date" <?php echo $disabled; ?>
					<?php if ($this->pm->can_edit) : ?> onclick="updateShipDate()" <?php endif; ?>>
					<i class="fa fa-save" style="min-width:20px;"></i></button>
			</span>
		</div>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>SAP NO.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
		<label>Status</label>
		<input type="text" class="form-control input-sm text-center" 
		value="<?php echo $doc->status == 1 ? 'Closed' : ($doc->status == 2 ? 'Cancelled' : ($doc->status == 3 ? 'On Process' : 'Draft')) ?>" disabled>
	</div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>" />
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" />

<hr class="margin-top-15 margin-bottom-15" />
<?php
if ($doc->status == 2)
{
	$this->load->view('cancle_watermark');
}

if ($doc->status == 3)
{
	$this->load->view('on_process_watermark');
}
?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-narrow border-1" style="min-width:940px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-250">สินค้า</th>
					<th class="fix-width-100">เลขที่บิล</th>
					<th class="fix-width-80 text-right">ราคา</th>
					<th class="fix-width-80 text-right">ส่วนลด</th>
					<th class="fix-width-80 text-right">คืน</th>
					<th class="fix-width-80 text-right">รับ</th>
					<th class="fix-width-80 text-right">มูลค่า(คืน)</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php if (!empty($details)) : ?>
					<?php $no = 1; ?>
					<?php $total_qty = 0; ?>
					<?php $total_receive = 0; ?>
					<?php $total_amount = 0; ?>
					<?php foreach ($details as $rs) : ?>
						<?php $color = $rs->qty == $rs->receive_qty ? "" : "color:red !important"; ?>
						<tr style="<?php echo $color; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-center"><?php echo $rs->invoice_code; ?></td>
							<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
							<td class="middle text-right"><?php echo $rs->discount_percent; ?> %</td>
							<td class="middle text-right"><?php echo round($rs->qty, 2); ?></td>
							<td class="middle text-right"><?php echo round($rs->receive_qty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
						</tr>
						<?php
						$no++;
						$total_qty += $rs->qty;
						$total_receive += $rs->receive_qty;
						$total_amount += $rs->amount;
						?>
					<?php endforeach; ?>
					<tr>
						<td colspan="6" class="middle text-right">รวม</td>
						<td class="middle text-right" id="total-qty"><?php echo number($total_qty); ?></td>
						<td class="middle text-right" id="total-qty"><?php echo number($total_receive); ?></td>
						<td class="middle text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<?php if ($doc->status == 2) : ?>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
			<label>เหตุผลในการยกเลิก</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
			<label>ยกเลิกโดย</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_user; ?>" disabled>
		</div>
	<?php endif; ?>
</div>

<script>
	$('#ship-date').datepicker({
		'dateFormat': 'dd-mm-yy'
	});

	function activeShipDate() {
		$('#ship-date').removeAttr('disabled');
		$('#btn-edit-ship-date').addClass('hide');
		$('#btn-update-ship-date').removeClass('hide');
	}

	function updateShipDate() {
		let shipDate = $('#ship-date').val();
		let code = $('#return_code').val();

		$.ajax({
			url: BASE_URL + 'inventory/return_consignment/update_shipped_date',
			type: 'POST',
			cache: false,
			data: {
				'code': code,
				'shipped_date': shipDate
			},
			success: function(rs) {
				if (rs.trim() === 'success') {
					$('#ship-date').attr('disabled', 'disabled');
					$('#btn-update-ship-date').addClass('hide');
					$('#btn-edit-ship-date').removeClass('hide');
				} else {
					swal({
						title: 'Error!',
						type: 'error',
						text: rs
					});
				}
			}
		})
	}
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>