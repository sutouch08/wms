<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm edit h f" id="fromWhsCode" value="<?php echo $doc->from_warehouse; ?>" disabled />
  </div>

  <div class="col-lg-3-harf col-md-3 col-sm-6 col-xs-8 padding-5">
    <label class="not-show">คลังต้นทาง</label>
    <input type="text" class="form-control input-sm edit h f" name="from_warehouse" id="from_warehouse" value="<?php echo $doc->from_warehouse_name; ?>" required disabled/>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>คลังปลายทาง</label>
    <input type="text" class="form-control input-sm edit h t" id="toWhsCode" value="<?php echo $doc->to_warehouse; ?>" disabled />
  </div>

	<div class="col-lg-3-harf col-md-3 col-sm-6 col-xs-8 padding-5">
    <label class="not-show">คลังปลายทาง</label>
		<input type="text" class="form-control input-sm edit h t" name="to_warehouse" id="to_warehouse" value="<?php echo $doc->to_warehouse_name; ?>" required disabled/>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>การดำเนินการ</label>
		<select class="form-control input-sm edit h" name="is_wms" id="is_wms" disabled>
			<option value="">เลือก</option>
			<?php if($this->wmsApi OR $doc->is_wms == 1) : ?>
				<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>PIONEER</option>
			<?php endif; ?>
			<?php if($this->sokoApi OR $doc->is_wms == 2) : ?>
				<option value="2" <?php echo is_selected('2', $doc->is_wms); ?>>SOKOCHAN</option>
			<?php endif; ?>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>WARRIX</option>
      <option value="-1" <?php echo is_selected('-1', $doc->is_wms); ?>>ย้ายคลัง</option>
		</select>
	</div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>Interface</label>
		<select class="form-control input-sm edit" name="api" id="api" disabled>
			<option value="1" <?php echo is_selected('1', $doc->api); ?>>ปกติ</option>
			<option value="0" <?php echo is_selected('0', $doc->api); ?>>ไม่ส่ง</option>
		</select>
	</div>

	<div class="col-lg-8 col-md-7-harf col-sm-10-harf col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit h" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

  <?php if(($doc->status == -1 OR $doc->status == 0)) : ?>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
  </div>
  <?php else : ?>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
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
<hr class="margin-top-15 margin-bottom-15"/>
