<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-left-15">
	<div class="form-horizontal">
		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">SAP No.</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" class="form-control input-xs" value="" disabled/>
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
				<input type="text" class="form-control input-xs" value="Planned" disabled/>
				<input type="hidden" id="status" value="P" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Order Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="posting-date" class="form-control input-xs r" value="<?php echo date('d-m-Y'); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Due Date</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="due-date" class="form-control input-xs r" value="<?php echo date('d-m-Y'); ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">User</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="user" class="form-control input-xs" value="<?php echo $this->_user->uname; ?>" disabled/>
			</div>
		</div>

		<!-- not use -->
		<div class="form-group hide">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Origin</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="origin-type" class="form-control input-xs" value="Manual" data-type="M" disabled/>
			</div>
		</div>

		<div class="form-group hide">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Sales Order</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="origin-num" class="form-control input-xs" value="" onchange="validOrigin()"/>
				<input type="hidden" id="origin-abs" value="" />
			</div>
		</div>

		<div class="form-group hide">
			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Customer</label>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
				<input type="text" id="customer" class="form-control input-xs" value="" />
				<input type="hidden" id="customer-code" value="" />
			</div>
		</div>

		<!--- not use -->
	</div>
</div>
