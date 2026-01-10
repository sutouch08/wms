<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-3-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<select class="form-control input-sm edit h" id="from-warehouse" disabled>
			<option value="">เลือกคลังต้นทาง</option>
			<?php echo select_warehouse($doc->from_warehouse); ?>
		</select>
	</div>

	<div class="col-lg-3-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<select class="form-control input-sm edit h" id="to-warehouse" disabled>
			<option value="">เลือกคลังปลายทาง</option>
			<?php echo select_warehouse($doc->to_warehouse); ?>
		</select>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Action</label>
		<select class="form-control input-sm edit h" id="is-wms" disabled>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>ปกติ</option>
      <option value="-1" <?php echo is_selected('-1', $doc->is_wms); ?>>ย้ายคลัง</option>
		</select>
    <input type="hidden" name="api" id="api" value="1" />
	</div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่ WX</label>
		<input type="text" class="form-control input-sm edit h" id="wx-code" value="<?php echo $doc->wx_code; ?>" disabled/>
	</div>

	<div class="col-lg-11 col-md-7 col-sm-7 col-xs-8 padding-5">
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
<input type="hidden" id="prev-from-warehouse" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="prev-to-warehouse" value="<?php echo $doc->to_warehouse; ?>" />
<script>
  $('#from-warehouse').select2();
  $('#to-warehouse').select2();
</script>
<hr class="margin-top-15 margin-bottom-15"/>
