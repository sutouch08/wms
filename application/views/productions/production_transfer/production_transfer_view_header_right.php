<div class="col-lg-3 col-md-4 col-sm-3-harf col-xs-12 padding-5" style="padding-right:12px;">
	<div class="form-horizontal">
		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 padding-0">SAP No.</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
				<?php if($doc->Status == 'C') : ?>
					<div class="input-group">
						<input type="text" id="doc-num" class="form-control input-xs h" value="<?php echo $doc->inv_code; ?>" readonly/>
						<span class="input-group-btn">
							<button type="button" class="btn btn-minier btn-info" title="API Logs" style="border-radius:3px !important; margin-left:5px;" onclick="viewApiLogs('<?php echo $doc->code; ?>')"><i class="fa fa-external-link"></i></button>
						</span>
					</div>
				<?php else : ?>
					<input type="text" id="doc-num" class="form-control input-xs h" value="<?php echo $doc->inv_code; ?>" readonly/>
				<?php endif; ?>
			</div>
		</div>
    <div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 padding-0">Status</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
				<input type="text" class="form-control input-xs h" value="<?php echo production_transfer_status_text($doc->Status); ?>" readonly/>
			</div>
		</div>
    <div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 padding-0">Document Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
				<input type="text" class="form-control input-xs h" value="<?php echo thai_date($doc->date_add); ?>" readonly/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 padding-0">Posting Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
				<input type="text" class="form-control input-xs h" value="<?php echo thai_date($doc->shipped_date); ?>" readonly/>
			</div>
		</div>
	</div>
</div>
