<div class="col-lg-9 col-md-8 col-sm-8-harf padding-right-15" style="padding-left:17px;">
  <div class="form-horizontal">
    <div class="form-group">
			<label class="sap-label fix-width-150">Ecom No.</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
				<input type="text" id="code" class="form-control input-xs" value="<?php echo $doc->code; ?>" readonly/>
			</div>
      <label class="sap-label fix-width-50">IX Ref</label>
      <div class="col-lg-2 col-md-3 col-sm-2-harf col-xs-8 padding-5">
        <input type="text" id="order-ref" class="form-control input-xs" value="<?php echo $doc->orderRef; ?>" disabled/>
      </div>
      <?php if( ! empty($doc->orderRef)) : ?>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
        <button type="button" class="btn btn-primary btn-minier" style="border-radius:3px !important;" onclick="viewIXProductionOrder('<?php echo $doc->reference; ?>')">
          <i class="fa fa-external-link"></i>
          &nbsp; View
        </button>
      </div>
      <?php endif; ?>
		</div>

    <div class="form-group">
      <label class="sap-label fix-width-150">From Warehouse</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" value="<?php echo $doc->fromWhsCode; ?> | <?php echo warehouse_name($doc->fromWhsCode); ?>" readonly />
      </div>
    </div>
    <div class="form-group">
      <label class="sap-label fix-width-150">To Warehouse</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" value="<?php echo $doc->toWhsCode; ?> | <?php echo warehouse_name($doc->toWhsCode); ?>" readonly />
      </div>
    </div>
		<div class="form-group">
      <label class="sap-label fix-width-150">To Bin Location</label>
      <div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" maxlength="200" value="<?php echo $doc->toBinCode; ?>" readonly/>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-4-harf col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" maxlength="200" value="<?php echo zone_name($doc->toBinCode); ?>" readonly/>
      </div>
    </div>
    <div class="form-group">
      <label class="sap-label fix-width-150">Production Order</label>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" maxlength="50" value="<?php echo $doc->reference; ?>" readonly/>
      </div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
        <button type="button" class="btn btn-primary btn-minier" style="border-radius:3px !important;" onclick="viewProductionOrder('<?php echo $doc->reference; ?>')">
          <i class="fa fa-external-link"></i>
          &nbsp; View
        </button>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-4-harf col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" id="base-item" maxlength="200" value="<?php echo $doc->ItemCode; ?>" readonly/>
      </div>
    </div>
  </div>
</div>
