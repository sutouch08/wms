<div class="move-table" id="zone-table" style="padding-bottom:250px;">
	<div class="nav-title">สินค้าในกล่อง</div>
	<table class="table table-bordered">
		<thead>
			<tr class="">
				<th class="fix-width-50 text-center">#</th>
				<th class="min-width-200">สินค้า</th>
				<th class="fix-width-100 text-center">Qty</th>
			</tr>
		</thead>
		<tbody id="box-list">

		</tbody>
	</table>

	<?php if($doc->status == 0) : ?>
	<div class="control-box">
		<div>
			<div class="width-100" id="box-zone-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus"
					style="padding-left:15px; padding-right:40px;" id="box-barcode-zone" inputmode="none" placeholder="Barcode Zone" autocomplete="off">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:15px; right:22px; color:grey;"></i>
				</span>
			</div>
			<div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="box-item-qty">
				<button type="button" class="btn btn-default btn-qty" id="btn-box-decrese"><i class="fa fa-minus"></i></button>
				<input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="box-qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
				<button type="button" class="btn btn-default btn-qty" id="btn-box-increse"><i class="fa fa-plus"></i></button>
			</div>

			<div class="width-100 hide" id="from-item-bc">
        <span class="width-100">
  				<input type="text" class="form-control input-lg focus"
          style="padding-left:15px; padding-right:40px;" id="from-barcode-item" inputmode="none"  placeholder="Barcode Item" autocomplete="off">
  				<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:72px; right:22px; color:grey;"></i>
        </span>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="width-100 text-center bottom-info hide-text" id="from-zone-name">กรุณาระบุโซน</div>
  <input type="hidden" id="from-zone-code" />
</div>


<script id="zoneTemplate" type="text/x-handlebars-template">
	<tr class="zone-table-item" id="row-{{barcode}}">
		<td class="text-center no"></td>
		<td>{{ product_code }}</td>
		<td class="text-center" id="stock-{{barcode}}">{{ stock_qty }}</td>
		<td class="text-center" id="temp-qty-{{barcode}}">{{ temp_qty }}</td>
	</tr>
</script>
