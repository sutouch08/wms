<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">SAP setting</span>
</div>
<form id="sapForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">Currency (สกุลเงิน)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="CURRENCY"  value="<?php echo $CURRENCY; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Item group code (รหัสกลุ่มสินค้า)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="ITEM_GROUP_CODE" value="<?php echo $ITEM_GROUP_CODE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Purchase VAT code (รหัสภาษีซื้อ)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="PURCHASE_VAT_CODE" value="<?php echo $PURCHASE_VAT_CODE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Purchase VAT rate (อัตราภาษีซื้อ)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Sell VAT code (รหัสภาษีขาย)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="SALE_VAT_CODE" value="<?php echo $SALE_VAT_CODE; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Sell VAT rate (อัตราภาษีขาย)</div>
		<div class="col-xs-4">
			<input type="text" class="width-100 text-center" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>


		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('sapForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
