<?php $disabled = $doc->Status == 'P' ? '' : 'disabled'; ?>
<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-left-15">
	<div class="form-horizontal">
		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">SAP No.</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="doc-num" class="form-control input-xs" value="<?php echo $doc->inv_code; ?>" disabled/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Order Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="posting-date" class="form-control input-xs r" value="<?php echo thai_date($doc->PostDate); ?>" <?php echo $disabled; ?>/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Start Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="release-date" class="form-control input-xs r" value="<?php echo thai_date($doc->ReleaseDate); ?>" <?php echo $disabled; ?>/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Due Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="due-date" class="form-control input-xs r" value="<?php echo thai_date($doc->DueDate); ?>" <?php echo $disabled; ?>/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Origin</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="origin-type" class="form-control input-xs" value="<?php echo originTypeName($doc->OriginType); ?>" data-type="<?php echo $doc->OriginType; ?>" disabled/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Sales Order</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="origin-num" class="form-control input-xs" value="<?php echo $doc->OriginNum; ?>" onchange="validOrigin()" <?php echo $disabled; ?>/>
				<input type="hidden" id="origin-abs" value="<?php echo $doc->OriginAbs; ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Customer</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="customer" class="form-control input-xs" value="<?php echo $doc->CardCode; ?>" <?php echo $disabled; ?>/>
				<input type="hidden" id="customer-code" value="<?php echo $doc->CardCode; ?>" />
			</div>
		</div>
	</div>
</div>
