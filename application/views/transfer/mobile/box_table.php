<div class="move-table hide" id="box-table" style="padding-bottom:350px;">
	<div class="nav-title" style="position: fixed; background-color:#232323; color:#ccc;"><span class="pull-left">Total</span> <span id="box-total">0</span></div>
	<table class="table table-bordered" style="margin-top:45px;">
		<thead>
			<tr class="">
				<th class="fix-width-50 text-center">#</th>
				<th class="min-width-200">Item SKU</th>
				<th class="fix-width-100 text-center">Qty</th>
			</tr>
		</thead>
		<tbody id="box-list">
			
		</tbody>
	</table>

	<div class="control-box">
		<div>
			<div class="width-100" id="box-zone-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus"
					style="padding-left:15px; padding-right:40px;" id="box-barcode-zone" inputmode="" placeholder="Barcode Zone" autocomplete="off">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:15px; right:22px; color:grey;"></i>
				</span>
			</div>
			<div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="box-item-qty">
				<button type="button" class="btn btn-default btn-qty" id="btn-box-decrese"><i class="fa fa-minus"></i></button>
				<input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="box-qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
				<button type="button" class="btn btn-default btn-qty" id="btn-box-increse"><i class="fa fa-plus"></i></button>
			</div>

			<div class="width-100 hide" id="box-item-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus"
					style="padding-left:15px; padding-right:40px;" id="box-barcode-item" inputmode="none"  placeholder="Barcode Item" autocomplete="off">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:72px; right:22px; color:grey;"></i>
				</span>
			</div>
		</div>
	</div>
	<div class="width-100 text-center bottom-info hide-text" id="box-zone-name">กรุณาระบุโซน</div>
  <input type="hidden" id="box-zone-code" />
</div>
<div class="hide" id="barcode-item-list">
	<?php if( ! empty($bcList)) : ?>
		<?php foreach($bcList as $bc) : ?>
			<input type="hidden" id="box-item-<?php echo $bc->barcode; ?>" data-barcode="<?php echo $bc->barcode; ?>" value="<?php echo $bc->product_code; ?>" />
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<script id="boxTemplate" type="text/x-handlebars-template">
	<tr class="box-table-item" id="box-{{barcode}}">
		<td class="text-center box-no"></td>
		<td>{{ product_code }} <a href="javascript:removeBoxItem('{{barcode}}', '{{product_code}}')" class="pull-right"><i class="fa fa-times red"></i></a></td>
		<td  class="middle text-center padding-0">
			<input type="number" class="width-100 text-center box-item focus"
				inputmode="numeric"
			 style="border:0px; background-color:transparent;"
			 id="box-qty-{{barcode}}"
			 data-code="{{product_code}}" value="{{qty}}" onclick="editBoxQty($(this))"/>
		</td>
	</tr>
</script>

<script>
	$('#box-barcode-zone').autocomplete({
		source:BASE_URL + 'auto_complete/get_zone_code_and_name',
		autoFocus:true,
		close:function() {
			let arr = $(this).val().split(' | ');

			if(arr.length == 2) {
				$(this).val(arr[0]);

				setTimeout(() => {
					getBoxZone();
				}, 100);
			}
			else {
				$(this).val('');
			}
		}
	})
</script>
