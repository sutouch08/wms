<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-right-15">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Type</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
        <select class="form-control input-xs" id="type">
          <option value="S">Standard</option>
          <!-- <option value="P">Special</option>
          <option value="D">Disassembly</option> -->
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Status</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
        <select class="form-control input-xs" id="status">
          <option value="P">Planned</option>
          <!-- <option value="R">Released</option>
          <option value="C">Closed</option> -->
        </select>
      </div>
    </div>

		<div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Product No.</label>
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-8 padding-5">
        <input type="text" id="product-code" class="form-control input-xs r" maxlength="50" value=""/>
      </div>
    </div>

		<div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Description</label>
      <div class="col-lg-8 col-md-9 col-sm-9 col-xs-8 padding-5">
        <input type="text" id="product-name" class="form-control input-xs" maxlength="100" value="" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Planned Qty</label>
      <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-left-5 padding-right-15">
        <input type="number" id="planned-qty" class="form-control input-xs r" onchange="recalQty()" value="1"/>
      </div>
      <label class="sap-label col-lg-1 col-md-1 col-sm-1 col-xs-1-harf padding-0">UoM</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3-harf padding-5">
        <input type="text" id="planned-uom" class="form-control input-xs" value="" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Warehouse</label>
      <div class="col-lg-6 col-md-9 col-sm-9 col-xs-8 padding-5">
        <select class="form-control input-xs" id="warehouse">
          <?php echo select_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
        </select>
      </div>
    </div>

  </div>
</div>

<script>
  $('#warehouse').select2();
</script>
