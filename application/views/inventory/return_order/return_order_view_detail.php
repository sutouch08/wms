<?php $this->load->view('include/header'); ?>
<?php
	$status = "Draft";
	$canAccept = NULL;

	if($doc->status == 4)
	{
		$pm = get_permission('APACSM', $this->_user->uid, $this->_user->id_profile);

		if( ! empty($pm))
		{
			$canAccept = ($pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0  OR $this->_SuperAdmin ? TRUE : FALSE;
		}
	}

	if($doc->is_approve == 0)
	{
		$status = ($doc->status == 1 OR $doc->status == 3) ? "รออนุมัติ" : ($doc->status == 2 ? "ยกเลิก" : $status);
	}

	if($doc->is_approve == 1)
	{
		$status = $doc->status == 1 ? "บันทึกแล้ว" : ($doc->status == 3 ? "รอรับเข้า" : ($doc->status == 4 ? "รอยืนยัน" : ($doc->status == 2 ? "ยกเลิก" : $status)));
	}
?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>

		<?php if($doc->is_expire == 1 && $this->_SuperAdmin) : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="rollBackExpired()">ทำให้ไม่หมดอายุ</button>
		<?php endif; ?>
		<?php if($doc->status == 1 && $doc->is_complete == 1 && $doc->is_approve == 1) : ?>
			<button type="button" class="btn btn-white btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
		<?php endif; ?>
		<?php if($doc->status == 4 && ($doc->uname == $this->_user->uname OR $canAccept)) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="accept()">ยืนยันการรับสินค้า</button>
		<?php endif; ?>
		<?php if(($doc->status == 1 OR $doc->status == 3 OR $doc->status == 4) && $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="unsave()">ย้อนสถานะกลับมาแก้ไข</button>
		<?php endif; ?>
		<?php if($doc->status == 3 && $doc->is_approve == 1 && $doc->is_expire == 0 && $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="goProcess('<?php echo $doc->code; ?>')">รับสินค้า</button>
		<?php endif; ?>
		<?php if($doc->status == 0 && $doc->is_expire == 0 && $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')">แก้ไข</button>
		<?php endif; ?>
		<?php if(($doc->status == 1 OR $doc->status == 3) && $doc->is_approve == 0 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn btn-100" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
		<?php endif; ?>
		<?php if(($doc->status == 1 OR $doc->status == 3 OR $doc->status == 4) && $doc->is_approve == 1 && $doc->is_pos_api == 0 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="unapprove()"><i class="fa fa-refresh"></i> ยกเลิกอนุมัติ</button>
		<?php endif; ?>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>
		<?php if($doc->is_wms != 0) : ?>
			<button type="button" class="btn btn-white btn-info top-btn" onclick="printWmsReturn()"><i class="fa fa-print"></i> พิมพ์ใบส่งของ</button>
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
		<input type="text" class="form-control input-sm text-center edit" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
	</div>
	<?php $disabled = $this->pm->can_edit ? "" : 'disabled'; ?>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm text-center" id="ship-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled />
			<span class="input-group-btn">
				<button type="button"
				class="btn btn-xs btn-warning btn-block" style="height:30px;"
				id="btn-edit-ship-date" <?php echo $disabled; ?>
				<?php if($this->pm->can_edit) : ?> onclick="activeShipDate()" <?php endif; ?>>
				<i class="fa fa-pencil" style="min-width:20px;"></i>
			</button>
			<button type="button"
			class="btn btn-xs btn-success btn-block hide" style="height:30px;"
			id="btn-update-ship-date" <?php echo $disabled; ?>
			<?php if($this->pm->can_edit) : ?> onclick="updateShipDate()" <?php endif; ?> >
				<i class="fa fa-save" style="min-width:20px;"></i></button>
			</span>
		</div>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่บิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center edit" name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>คลัง[รับคืน]</label>
		<input type="text" class="form-control input-sm edit" name="warehouse" id="warehouse" value="<?php echo $doc->warehouse_code.' | '.$doc->warehouse_name; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center edit" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-4-harf col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
		<label>ชื่อโซน]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="width-100 text-center" value="<?php echo $status; ?>" disabled />
	</div>
	<div class="col-lg-10-harf col-md-6 col-sm-6 col-xs-8 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>SAP No.</label>
		<div class="input-group width-100">
			<input type="text" class="width-100 text-center" value="<?php echo $doc->inv_code; ?>" disabled />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-info" style="height:30px;" onclick="viewTempReturn('<?php echo $doc->code; ?>')" style="min-width:20px;">
					<i class="fa fa-external-link"></i>
				</button>
			</span>
		</div>
	</div>
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

	if($doc->status == 3 && $doc->is_approve)
	{
		$this->load->view('on_process_watermark');
	}

	if($doc->status == 4 && $doc->is_approve)
	{
		$this->load->view('accept_watermark');
	}
}
?>
<?php if($doc->is_pos_api == 1) : ?>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="red text-center">** เอกสารนี้ถูกสร้างโดยระบบ POS จึงไม่สามารถแก้ไขรายการได้ **</p>
		</div>
	</div>
<?php endif; ?>
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
	<?php $hilight = $rs->qty > $rs->receive_qty ? "color:red;" : ""; ?>
				<tr class="font-size-11" style="<?php echo $hilight; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->barcode; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $doc->is_pos_api ? $rs->bill_code : $rs->invoice_code; ?></td>
					<td class="middle text-center"><?php echo $rs->order_code; ?></td>
					<td class="middle text-center"><?php echo number($rs->price, 2); ?></td>
					<td class="middle text-center"><?php echo $rs->discount_percent; ?> %</td>
					<td class="middle text-center"><?php echo round($rs->qty,2); ?></td>
					<td class="middle text-center"><?php echo round($rs->receive_qty,2); ?></td>
					<td class="middle text-right"><?php echo number($rs->amount,2); ?></td>
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

		<?php if($doc->status == 2) : ?>
			<span class="red display-block">ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancle_date, TRUE); ?></span>
			<span class="red display-block">เหตุผลในการยกเลิก : <?php echo $doc->cancle_reason; ?></span>
		<?php endif; ?>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal'); ?>

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
			url:BASE_URL + 'inventory/return_order/update_shipped_date',
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

	function viewTempReturn(code) {
		var mapForm = document.createElement("form");
    mapForm.target = "Map";
    mapForm.method = "POST"; // or "post" if appropriate
    mapForm.action = BASE_URL + "inventory/temp_return_order?nomenu";

    var mapInput = document.createElement("input");
    mapInput.type = "text";
    mapInput.name = "code";
    mapInput.value = code;
    mapForm.appendChild(mapInput);

    document.body.appendChild(mapForm);

    map = window.open("", "Map", "status=0,title=0,height=600,width=800,scrollbars=1");

		if (map) {
		    mapForm.submit();
		} else {
		    alert('You must allow popups for this map to work.');
		}
	}
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('YmdH');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('YmdH');?>"></script>
<?php $this->load->view('include/footer'); ?>
