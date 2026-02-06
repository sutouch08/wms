<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-right-15" style="padding-left:17px;">
  <div class="form-horizontal">
    <div class="form-group">
			<label class="sap-label col-lg-1-harf col-md-2 col-sm-2 col-xs-2 padding-0">Document No.</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
				<input type="text" id="code" class="form-control input-xs" value="<?php echo $doc->code; ?>" disabled/>
			</div>
		</div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2 col-sm-2 col-xs-2 padding-0">IX Ref</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
        <input type="text" id="order-ref" class="form-control input-xs" value="<?php echo $doc->orderRef; ?>" disabled/>
      </div>
      <?php if( ! empty($doc->orderRef)) : ?>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
        <button type="button" class="btn btn-primary btn-minier" style="border-radius:3px !important;" onclick="viewIXProductionOrder()">
          <i class="fa fa-external-link"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2 col-sm-2 col-xs-2 padding-0">External Ref</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
        <input type="text" id="external-ref" class="form-control input-xs" maxlength="11" value="<?php echo $doc->externalRef; ?>" placeholder="Max 11 charecters" />
      </div>
    </div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-0">Production Order</label>
      <div class="col-lg-4 col-md-5 col-sm-5 col-xs-8 padding-5">
        <div class="input-group">
          <span class="input-group-addon" style="padding:0px !important; border:none !important; background-color: transparent;">
            <select class="form-control input-xs" id="p-status" onchange="baseRefInit()" style="width:80px; border:solid 1px #d5d5d5; background:#fff; border-radius:3px; margin-right:5px;">
              <option value="R">Released</option>
              <option value="L">Closed</option>
              <option value="A">All</option>
            </select>
          </span>
          <input type="text" id="base-ref" class="form-control input-xs h" maxlength="50" data-prev="<?php echo $doc->reference; ?>" value="<?php echo $doc->reference; ?>"/>
          <span class="input-group-btn">
            <button type="button" class="btn btn-primary btn-minier" style="border-radius:3px !important; margin-left:5px;" onclick="getOrderData()"><i class="fa fa-check"></i>&nbsp; OK</button>
          </span>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-0">Production Item</label>
      <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 padding-5 hidden-xs">
        <input type="text" id="base-item" class="form-control input-xs h" maxlength="200" value="<?php echo $doc->ItemCode; ?>" disabled/>
      </div>
    </div>
  </div>
</div>
