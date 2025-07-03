<hr/>
<div class="row">
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>ใบกำกับ[SAP]</label>
		<?php if( ! empty($doc->invoice)) : ?>
			<div class="input-group width-100">
				<input type="text" class="width-100 text-center h" id="invoice" value="<?php echo $doc->invoice; ?>" placeholder="ค้นหาใบกำกับ" disabled/>
				<span class="input-group-btn">
					<button type="button" class="btn btn-xs btn-success hide" style="height:30px;" id="btn-confirm-inv" onclick="setInvoice()"><i class="fa fa-check"></i> Set</button>
					<button type="button" class="btn btn-xs btn-warning" style="height:30px;" id="btn-clear-inv" onclick="changeInvoice()"><i class="fa fa-close"></i> Clear</button>
				</span>
			</div>
		<?php else : ?>
			<div class="input-group width-100">
				<input type="text" class="width-100 text-center h" id="invoice" value="" placeholder="ค้นหาใบกำกับ" />
				<span class="input-group-btn">
					<button type="button" class="btn btn-xs btn-success" style="height:30px;" id="btn-confirm-inv" onclick="setInvoice()"><i class="fa fa-check"></i> Set</button>
					<button type="button" class="btn btn-xs btn-warning hide" style="height:30px;" id="btn-clear-inv" onclick="changeInvoice()"><i class="fa fa-close"></i> Clear</button>
				</span>
			</div>
		<?php endif; ?>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>ออเดอร์</label>
		<input type="text" class="width-100 text-center" id="order-code" value="" placeholder="ค้นหาเลขที่ออเดอร์" />
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสสินค้า</label>
		<input type="text" class="width-100 text-center" id="product-code" value="" placeholder="ค้นหารหัสสินค้า" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-info btn-block" id="btn-load-inv" onclick="getInvoice()"><i class="fa fa-download"></i> Load</button>
	</div>
	<div class="divider-hidden hidden-lg"></div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm h" id="zone-code" placeholder="รหัสโซน" value="<?php echo $doc->zone_code; ?>" />
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6-harf col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone h" name="zone" id="zone-name" placeholder="ชื่อโซน" value="<?php echo $doc->zone_name; ?>" readonly/>
	</div>
	<div class="col-md-2 col-sm-1-harf col-xs-8 hidden-lg">
		<label class="not-show">zone</label>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5">
		<label class="display-block not-show">del</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="deleteChecked()"><i class="fa fa-trash"></i> ลบ</button>
	</div>
</div>


<script>
	$('#warehouse').select2();
</script>
