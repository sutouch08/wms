<div class="col-lg-9-harf col-md-9 col-sm-9 padding-right-15" style="padding-left:17px;">
  <div class="form-horizontal">
    <div class="form-group hidden-xs">
			<label class="sap-label col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-0">Doc No.</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
				<input type="text" id="code" class="form-control input-xs" value="<?php echo $doc->code; ?>" disabled/>
			</div>
		</div>

    <div class="form-group hidden-xs">
			<label class="sap-label col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-0">Business Partner</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
				<input type="text" id="vender-code" class="form-control input-xs" maxlength="15" value="<?php echo $doc->CardCode; ?>"/>
			</div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5 hidden-xs">
        <input type="text" id="vender-name" class="form-control input-xs h" maxlength="100" value="<?php echo $doc->CardName; ?>" disabled/>
      </div>
		</div>

    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-0">From Warehouse</label>
      <div class="col-lg-5 col-md-6 col-sm-7-harf col-xs-8 padding-5">
        <select class="form-control input-xs h" id="fromWhsCode">
          <?php echo select_warehouse($doc->fromWhsCode); ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-0">To Warehouse</label>
      <div class="col-lg-5 col-md-6 col-sm-7-harf col-xs-8 padding-5">
        <select class="form-control input-xs h" id="toWhsCode" onchange="binCodeInit('Y')">
          <?php echo select_warehouse($doc->toWhsCode); ?>
        </select>
      </div>
    </div>
		<div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-0">To Bin Location</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
        <input type="text" id="bin-code" class="form-control input-xs h" maxlength="200" value="<?php echo $doc->toBinCode; ?>"/>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5 hidden-xs">
        <input type="text" id="bin-name" class="form-control input-xs h" maxlength="200" value="<?php echo $doc->toBinName; ?>" disabled/>
      </div>
    </div>
    <div class="form-group">
      <label class="sap-label col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-0">Production Order</label>
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
      <div class="col-lg-5 col-md-4 col-sm-4 col-xs-8 padding-5 hidden-xs">
        <input type="text" id="base-item" class="form-control input-xs h" maxlength="200" value="<?php echo $doc->ItemCode; ?>" disabled/>
      </div>
    </div>
  </div>
</div>

<script>
  $('#fromWhsCode').select2();
  $('#toWhsCode').select2();
</script>
