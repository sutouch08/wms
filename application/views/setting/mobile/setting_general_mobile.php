<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">General setting</span>
</div>
<div class="divider-hidden"></div>
<form id="generalForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-12 margin-bottom-10">
			<label>App Title</label>
			<input type="text" class="width-100" name="COMPANY_NAME" id="brand" value="<?php echo $COMPANY_NAME; ?>" />
		</div>
		<div class="col-xs-12 margin-bottom-10">
			<label>Company Name</label>
			<input type="text" class="width-100" name="COMPANY_FULL_NAME" id="cName" value="<?php echo $COMPANY_FULL_NAME; ?>" />
		</div>

		<div class="col-xs-12 margin-bottom-10">
			<label>Address 1</label>
			<input type="text" class="width-100" name="COMPANY_ADDRESS1" id="cAddress1" placeholder="เลขที่ หมู่ ถนน ตำบล" value="<?php echo $COMPANY_ADDRESS1; ?>" />
		</div>

		<div class="col-xs-12 margin-bottom-10">
			<label>Address 2</label>
			<input type="text" class="width-100" name="COMPANY_ADDRESS2" id="cAddress2" placeholder="อำเภอ จังหวัด" value="<?php echo $COMPANY_ADDRESS2; ?>" />
		</div>

		<div class="col-xs-6 margin-bottom-10">
			<label>Zip code</label>
			<input type="text" class="width-100" name="COMPANY_POST_CODE" id="postCode" value="<?php echo $COMPANY_POST_CODE; ?>" />
		</div>
		<div class="col-xs-6 margin-bottom-10">
			<label>Phone</label>
			<input type="text" class="width-100" name="COMPANY_PHONE" id="phone" value="<?php echo $COMPANY_PHONE; ?>" />
		</div>
		<div class="col-xs-6 margin-bottom-10">
			<label>Fax</label>
			<input type="text" class="width-100" name="COMPANY_FAX_NUMBER" id="fax" value="<?php echo $COMPANY_FAX_NUMBER; ?>" />
		</div>
		<div class="col-xs-6 margin-bottom-10">
			<label>Email.</label>
			<input type="text" class="width-100" name="COMPANY_EMAIL" id="email" value="<?php echo $COMPANY_EMAIL; ?>" />
		</div>
		<div class="col-xs-6 margin-bottom-10">
			<label>Tax ID</label>
			<input type="text" class="width-100" name="COMPANY_TAX_ID" id="taxID" value="<?php echo $COMPANY_TAX_ID; ?>" />
		</div>
		<div class="col-xs-6 margin-bottom-10">
			<label>Founded Year</label>
			<input type="number" class="width-100" name="START_YEAR" id="startYear" value="<?php echo $START_YEAR; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-success btn-block" onClick="checkCompanySetting()">SAVE</button>
			<?php endif; ?>
		</div>
	</div><!--/ row -->
</form>
