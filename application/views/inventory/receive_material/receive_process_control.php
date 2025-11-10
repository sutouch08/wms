<!--  Search Product -->
<div class="row">
	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon">PO No.</span>
			<?php if( ! empty($po_refs)) : ?>
				<select class="width-100" id="po-refs">
					<?php foreach($po_refs as $ref) : ?>
						<option value="<?php echo $ref->po_code; ?>"><?php echo $ref->po_code; ?></option>
					<?php endforeach; ?>
				</select>
			<?php else : ?>
				<input type="text" class="width-100 text-center" id="po-refs" value="<?php echo $doc->po_code; ?>" readonly />
			<?php endif; ?>
		</div>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon">Qty</span>
			<input type="number" class="width-100 text-center" id="input-qty" value="1" />
		</div>
	</div>
	<div class="divider-hidden visible-xs">

	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-8 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon">Barcode</span>
			<input type="text" class="width-100 text-center" id="barcode" value="" autofocus/>
		</div>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="doReceive()">OK</button>
	</div>
</div>

<div class="divider-hidden">	</div>
