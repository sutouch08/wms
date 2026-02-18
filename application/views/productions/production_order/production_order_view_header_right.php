<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-left-15">
	<div class="form-horizontal">
		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 padding-0">SAP No.</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
				<?php if( ! empty($doc->inv_code)) : ?>
					<div class="input-group">
						<input type="text" id="doc-num" class="form-control input-xs h" value="<?php echo $doc->inv_code; ?>" disabled/>
						<span class="input-group-btn">
							<button type="button" class="btn btn-minier btn-info" title="View Production Order"
							style="border-radius:3px !important; margin-left:5px;" onclick="viewProductionOrder('<?php echo $doc->inv_code; ?>')">
							<i class="fa fa-external-link"></i></button>
						</span>
					</div>
				<?php else : ?>
					<input type="text" id="doc-num" class="form-control input-xs h" value="<?php echo $doc->inv_code; ?>" disabled/>
				<?php endif; ?>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Type</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" class="form-control input-xs" value="Standard" disabled/>
				<input type="hidden" id="type" value="S" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Status</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" class="form-control input-xs" value="<?php echo production_order_status_text($doc->Status); ?>" disabled/>
				<input type="hidden" id="status" value="<?php echo $doc->Status; ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Order Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="posting-date" class="form-control input-xs r" value="<?php echo thai_date($doc->PostDate); ?>" disabled/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Start Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="release-date" class="form-control input-xs r" value="<?php echo thai_date($doc->ReleaseDate); ?>" disabled/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Due Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="due-date" class="form-control input-xs r" value="<?php echo thai_date($doc->DueDate); ?>" disabled/>
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 padding-0">User</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="user" class="form-control input-xs" value="<?php echo $doc->user; ?>" disabled/>
			</div>
		</div>

		<div class="form-group hide">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Origin</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="origin-type" class="form-control input-xs" value="<?php echo originTypeName($doc->OriginType); ?>" data-type="<?php echo $doc->OriginType; ?>" disabled/>
			</div>
		</div>

		<div class="form-group hide">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Sales Order</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="origin-num" class="form-control input-xs" value="<?php echo $doc->OriginNum; ?>" onchange="validOrigin()" disabled/>
				<input type="hidden" id="origin-abs" value="<?php echo $doc->OriginAbs; ?>" />
			</div>
		</div>

		<div class="form-group hide">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Customer</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="customer" class="form-control input-xs" value="<?php echo $doc->CardCode; ?>" disabled/>
				<input type="hidden" id="customer-code" value="<?php echo $doc->CardCode; ?>" />
			</div>
		</div>
	</div>
</div>
