<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-right-15" style="padding-left:17px;">
  <div class="form-horizontal">
    <div class="form-group">
			<label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Document No.</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
        <div class="input-group">
          <input type="text" id="code" class="form-control input-xs h" value="<?php echo $doc->code; ?>" disabled/>
          <span class="input-group-btn">
            <button type="button" class="btn btn-minier btn-info" title="API Logs"
            style="border-radius:3px !important; margin-left:5px;"
            onclick="viewApiLogs('<?php echo $doc->code; ?>')"><i class="fa fa-external-link"></i></button>
          </span>
        </div>
        <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
			</div>
		</div>

		<div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Product No.</label>
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-8 padding-5">
        <input type="text" id="product-code" class="form-control input-xs r" maxlength="50" value="<?php echo $doc->ItemCode; ?>"
        data-prev="<?php echo $doc->ItemCode; ?>"  disabled/>
      </div>
    </div>

		<div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Description</label>
      <div class="col-lg-8 col-md-10 col-sm-10 col-xs-8 padding-5">
        <input type="text" id="product-name" class="form-control input-xs" maxlength="100" value="<?php echo $doc->ProdName; ?>"
        data-prev="<?php echo $doc->ProdName; ?>" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Planned Qty</label>
      <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-left-5 padding-right-15">
        <input type="number" id="planned-qty" class="form-control input-xs r" onchange="recalQty()" value="<?php echo round($doc->PlannedQty, 2); ?>" disabled/>
      </div>
      <label class="sap-label col-lg-1 col-md-1 col-sm-1 col-xs-1-harf padding-0">UoM</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3-harf padding-5">
        <input type="text" id="planned-uom" class="form-control input-xs" value="<?php echo $doc->Uom; ?>" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Warehouse</label>
      <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8 padding-5">
        <select class="form-control input-xs" id="warehouse" disabled>
          <?php echo select_warehouse($doc->Warehouse); ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <div class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0" style="height:48px; display:flex; align-items:end;">
        <span>Remark</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 padding-5">
        <textarea class="form-control input-xs" id="remark" rows="2" disabled><?php echo $doc->Comments; ?></textarea>
      </div>
    </div>

  </div>
</div>

<script>
  $('#warehouse').select2();
</script>
