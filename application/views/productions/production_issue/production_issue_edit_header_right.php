<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-left-15" style="padding-right:12px;">
	<div class="form-horizontal">
		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">SAP No.</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="doc-num" class="form-control input-xs h" value="<?php echo $doc->inv_code; ?>" disabled/>
			</div>
		</div>
    <div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Status</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" class="form-control input-xs h" value="<?php echo production_issue_status_text($doc->Status); ?>" disabled/>
			</div>
		</div>
    <div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Document Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="date-add" class="form-control input-xs h" value="<?php echo thai_date($doc->date_add); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Posting Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="posting-date" class="form-control input-xs h" value="<?php echo thai_date($doc->shipped_date); ?>" />
			</div>
		</div>
	</div>
</div>
