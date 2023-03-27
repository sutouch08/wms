<div class="row">
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-sm-4 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm edit" name="from_warehouse" id="from_warehouse" value="<?php echo $doc->from_warehouse_name; ?>" required disabled/>
  </div>

	<div class="col-sm-4 padding-5">
    <label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm edit" name="to_warehouse" id="to_warehouse" value="<?php echo $doc->to_warehouse_name; ?>" required disabled/>
  </div>

	<div class="col-sm-1 padding-5">
		<label>WMS</label>
		<select class="form-control input-sm edit" name="api" id="api" disabled>
			<option value="1" <?php echo is_selected('1', $doc->api); ?>>ปกติ</option>
			<option value="0" <?php echo is_selected('0', $doc->api); ?>>ไม่ส่ง</option>
		</select>
	</div>

	<div class="col-sm-1 col-1-harf padding-5">
		<label>เลขที่ WX</label>
		<input type="text" class="form-control input-sm edit" name="wx_code" id="wx_code" value="<?php echo $doc->wx_code; ?>" disabled />
	</div>

	<div class="col-sm-9 col-9-harf padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

  <?php if(($doc->status == -1 OR $doc->status == 0)) : ?>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
  </div>
  <?php else : ?>
    <div class="col-sm-1 padding-5">
      <label>SAP</label>
      <input type="text" class="form-control input-sm" value="<?php echo $doc->inv_code; ?>" disabled>
    </div>
  <?php endif; ?>
</div>
<input type="hidden" id="transfer_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" />
<input type="hidden" id="old_from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="old_to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" />
<input type="hidden" id="is_wms" value="<?php echo $doc->is_wms; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>
