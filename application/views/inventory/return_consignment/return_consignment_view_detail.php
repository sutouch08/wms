<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->is_approve == 1) : ?>
				<?php if($doc->status == 1) : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
				<?php endif; ?>
				<?php if($doc->status == 3) : ?>
					<?php if(($doc->is_wms == 1 && $this->wmsApi && $doc->is_api) OR ($doc->is_wms == 2 && $this->sokoApi && $doc->is_api)) : ?>
						<button type="button" class="btn btn-sm btn-success" onclick="sendToWms()"><i class="fa fa-send"></i> Send to WMS</button>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unsave()">ยกเลิกการบันทึก</button>
			<?php endif; ?>
			<?php if($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_approve) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
			<?php endif; ?>
			<?php if($this->pm->can_delete && $doc->status != 2) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
			<?php endif; ?>

			<?php if($doc->status != 0 && $this->_SuperAdmin) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="pullBack('<?php echo $doc->code; ?>')">ดึงสถานะกลับมาแก้ไข</button>
			<?php endif; ?>

			<?php if($doc->status != 0) : ?>
				<button type="button" class="btn btn-sm btn-info" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php if($doc->status != 2) : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="printWmsReturn()"><i class="fa fa-print"></i> พิมพ์ใบส่งของ</button>
				<?php endif; ?>
			<?php endif; ?>
		</p>
	</div>
</div>
<hr />

<div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled/>
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->customer_code; ?>" disabled />
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-9 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->customer_name; ?>" disabled/>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice; ?>" disabled />
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>GP(%)</label>
			<input type="number" class="form-control input-sm text-center" value="<?php echo $doc->gp; ?>" disabled />
		</div>

		<div class="col-lg-6 col-md-4-harf col-sm-4-harf col-xs-12 padding-5">
			<label>โซนฝากขาย</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->from_zone_name; ?>" disabled />
		</div>

		<div class="col-lg-6 col-md-4-harf col-sm-4-harf col-xs-12 padding-5">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>รับที่</label>
			<select class="form-control input-sm" disabled>
				<option value="">เลือก</option>
				<?php if($this->wmsApi OR $doc->is_wms == 1) : ?>
					<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>PIONEER</option>
				<?php endif; ?>
				<?php if($this->sokoApi OR $doc->is_wms == 2) : ?>
					<option value="2" <?php echo is_selected('2', $doc->is_wms); ?>>SOKOCHAN</option>
				<?php endif; ?>
				<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>WARRIX</option>
			</select>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>Interface</label>
			<select class="form-control input-sm" disabled>
				<option value="1" <?php echo is_selected("1", $doc->is_api); ?>>ส่ง</option>
				<option value="0" <?php echo is_selected("0", $doc->is_api); ?>>ไม่ส่ง</option>
			</select>
		</div>
    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
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
					<?php if($this->pm->can_edit) : ?> onclick="activeShipDate()" <?php endif; ?>>
						<i class="fa fa-pencil" style="min-width:20px;"></i>
					</button>
					<button type="button"
					class="btn btn-xs btn-success btn-block hide"
					id="btn-update-ship-date" <?php echo $disabled; ?>
					<?php if($this->pm->can_edit) : ?> onclick="updateShipDate()" <?php endif; ?> >
					<i class="fa fa-save" style="min-width:20px;"></i></button>
				</span>
			</div>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>SAP NO.</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled>
		</div>


		<?php if($doc->status == 2) : ?>
			<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
	    	<label>เหตุผลในการยกเลิก</label>
	        <input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled>
	    </div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
				<label>ยกเลิกโดย</label>
	      <input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_user; ?>" disabled>
			</div>
		<?php endif; ?>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" />

<hr class="margin-top-15 margin-bottom-15"/>
<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}

if($doc->status == 3)
{
  $this->load->view('on_process_watermark');
}
?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:900px;">
			<thead>
				<tr>
					<th class="width-5 text-center">ลำดับ</th>
					<th class="15">รหัส</th>
					<th class="">สินค้า</th>
					<th class="width-10 text-center">เลขที่บิล</th>
					<th class="width-10 text-right">ราคา</th>
					<th class="width-10 text-right">ส่วนลด</th>
					<th class="width-10 text-right">คืน</th>
					<th class="width-10 text-right">รับ</th>
					<th class="width-10 text-right">มูลค่า(คืน)</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  $total_qty = 0; ?>
<?php  $total_receive = 0; ?>
<?php  $total_amount = 0; ?>
<?php  foreach($details as $rs) : ?>
	<?php $color = $rs->qty == $rs->receive_qty ? "" : "color:red !important"; ?>
				<tr style="<?php echo $color; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $rs->invoice_code; ?></td>
					<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
					<td class="middle text-right"><?php echo $rs->discount_percent; ?> %</td>
					<td class="middle text-right"><?php echo round($rs->qty,2); ?></td>
					<td class="middle text-right"><?php echo round($rs->receive_qty,2); ?></td>
					<td class="middle text-right"><?php echo number($rs->amount,2); ?></td>
				</tr>
<?php
				$no++;
				$total_qty += $rs->qty;
				$total_receive += $rs->receive_qty;
				$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
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

<script>
	$('#ship-date').datepicker({
		'dateFormat' : 'dd-mm-yy'
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
			url:BASE_URL + 'inventory/return_consignment/update_shipped_date',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'shipped_date' : shipDate
			},
			success:function(rs) {
				if(rs.trim() === 'success') {
					$('#ship-date').attr('disabled', 'disabled');
					$('#btn-update-ship-date').addClass('hide');
					$('#btn-edit-ship-date').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						type:'error',
						text:rs
					});
				}
			}
		})
	}
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
