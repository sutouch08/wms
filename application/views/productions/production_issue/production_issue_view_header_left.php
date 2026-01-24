<div class="col-lg-9 col-md-8 col-sm-8-harf padding-right-15">
  <div class="form-horizontal">
    <div class="form-group">
			<label class="sap-label fix-width-150">Ecom No.</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
				<input type="text" id="code" class="form-control input-xs" value="<?php echo $doc->code; ?>" disabled/>
			</div>
		</div>

    <div class="form-group">
			<label class="sap-label fix-width-150">IX Ref</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
        <input type="text" id="order-ref" class="form-control input-xs" value="<?php echo $doc->orderRef; ?>" disabled/>
			</div>
      <?php if( ! empty($doc->orderRef)) : ?>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
        <button type="button" class="btn btn-primary btn-minier" style="border-radius:3px !important;" onclick="viewIXProductionOrder('<?php echo $doc->orderRef; ?>')">
          <i class="fa fa-external-link"></i>
        </button>
      </div>
      <?php endif; ?>
		</div>

    <div class="form-group">
      <label class="sap-label fix-width-150">Production Order</label>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" maxlength="50" value="<?php echo $doc->reference; ?>" disabled/>
      </div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
        <button type="button" class="btn btn-primary btn-minier" style="border-radius:3px !important;" onclick="viewProductionOrder('<?php echo $doc->reference; ?>')">
          <i class="fa fa-external-link"></i>
        </button>
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label fix-width-150">Item Code</label>
      <div class="col-lg-5 col-md-5 col-sm-4-harf col-xs-8 padding-5">
        <input type="text" class="form-control input-xs h" id="base-item" maxlength="200" value="<?php echo $doc->ItemCode; ?>" disabled/>
      </div>
    </div>
  </div>
</div>
