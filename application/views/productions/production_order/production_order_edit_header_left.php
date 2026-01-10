<?php $disabled = $doc->Status == 'P' ? '' : 'disabled'; ?>
<div class="col-lg-9 col-md-8 col-sm-8 padding-right-15" style="padding-left:17px;">
  <div class="form-horizontal">
    <div class="form-group">
			<label class="sap-label fix-width-150">Ecom No.</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
				<input type="text" id="code" class="form-control input-xs" value="<?php echo $doc->code; ?>" disabled/>
				<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
			</div>
		</div>
    <div class="form-group">
      <label class="sap-label fix-width-150">Type</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
        <select class="form-control input-xs" id="type" <?php echo $disabled; ?>>
          <option value="S" <?php echo is_selected('S', $doc->Type); ?>>Standard</option>
          <option value="P" <?php echo is_selected('P', $doc->Type); ?>>Special</option>
          <option value="D" <?php echo is_selected('D', $doc->Type); ?>>Disassembly</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label fix-width-150">Status</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
        <select class="form-control input-xs" id="status" <?php echo $disabled; ?>>
          <option value="P" <?php echo is_selected('P', $doc->Status); ?>>Planned</option>
          <option value="R" <?php echo is_selected('R', $doc->Status); ?>>Released</option>
        </select>
      </div>
    </div>

		<div class="form-group">
      <label class="sap-label fix-width-150">Product No.</label>
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-8 padding-5">
        <input type="text" id="product-code" class="form-control input-xs r" maxlength="50" value="<?php echo $doc->ItemCode; ?>"
        data-prev="<?php echo $doc->ItemCode; ?>"  <?php echo $disabled; ?>/>
      </div>
    </div>

		<div class="form-group">
      <label class="sap-label fix-width-150">Production Description</label>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 padding-5">
        <input type="text" id="product-name" class="form-control input-xs" maxlength="100" value="<?php echo $doc->ProdName; ?>"
        data-prev="<?php echo $doc->ProdName; ?>" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label fix-width-150">Planned Quantity</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
        <input type="number" id="planned-qty" class="form-control input-xs r" onchange="recalQty()" value="<?php echo round($doc->PlannedQty, 2); ?>" <?php echo $disabled; ?>/>
      </div>
      <label class="sap-label fix-width-60">UoM</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
        <input type="text" id="planned-uom" class="form-control input-xs" value="<?php echo $doc->Uom; ?>" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label fix-width-150">Warehouse</label>
      <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8 padding-5">
        <select class="form-control input-xs" id="warehouse" <?php echo $disabled; ?>>
          <?php echo select_warehouse($doc->Warehouse); ?>
        </select>
      </div>
    </div>
  </div>
</div>

<script>
  $('#warehouse').select2();
</script>
