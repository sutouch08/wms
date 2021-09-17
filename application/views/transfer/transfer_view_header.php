<div class="row">
  <div class="col-sm-1 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-sm-1 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>

  <div class="col-sm-2 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm edit" name="from_warehouse" id="from_warehouse" value="<?php echo $doc->from_warehouse_name; ?>" disabled/>
  </div>

	<div class="col-sm-2 padding-5">
    <label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm edit" name="to_warehouse" id="to_warehouse" value="<?php echo $doc->to_warehouse_name; ?>" disabled/>
  </div>

	<div class="col-sm-1 padding-5">
		<label>WMS</label>
		<select class="form-control input-sm edit" name="api" id="api" disabled>
			<option value="1" <?php echo is_selected('1', $doc->api); ?>>ปกติ</option>
			<option value="0" <?php echo is_selected('0', $doc->api); ?>>ไม่ส่ง</option>
		</select>
	</div>
  <div class="col-sm-4 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>
	<div class="col-sm-1 padding-5">
		<label>SAP</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled >
	</div>
</div>
<input type="hidden" id="transfer_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>
