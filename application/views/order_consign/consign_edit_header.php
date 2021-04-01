<div class="row">
	<div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-sm-1 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
    <div class="col-sm-4 col-4-harf padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
		<div class="col-sm-4 padding-5">
	    <label>โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $order->zone_name; ?>" required disabled/>
	  </div>

	  <div class="col-sm-1 padding-5 last">
	    <label>GP[%]</label>
			<input type="text" class="form-control input-sm text-center edit" name="gp" id="gp" value="<?php echo $order->gp; ?>" disabled />
	  </div>
		<div class="col-sm-2 col-2-harf col-xs-12 padding-5 first">
			<label>คลัง</label>
	    <select class="form-control input-sm edit" name="warehouse" id="warehouse" required disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
	  </div>

		<?php if(empty($approve_view) && ($this->pm->can_add OR $this->pm->can_edit)): ?>
		<div class="col-sm-8 padding-5">
		 	<label>หมายเหตุ</label>
		  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label class="display-block not-show">แก้ไข</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
		</div>
		<?php else : ?>
			<div class="col-sm-9 col-9-harf padding-5 last">
			 	<label>หมายเหตุ</label>
			  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
		<?php endif; ?>

    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $order->zone_code; ?>" />
		<input type="hidden" id="is_approved" value="<?php echo $order->is_approved; ?>" />
</div>
<hr class="margin-bottom-15"/>
