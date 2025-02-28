<div class="move-table hide" id="temp-table">
	<div class="nav-title">รายการใน Temp</div>
	<div class="col-xs-12 padding-0 table-responsive" style="margin-bottom:220px; border-bottom:solid 1px #ccc;">
		<table class="table table-bordered" style="min-width:430px;">
			<thead>
				<tr class="">
					<th class="fix-width-40 text-center">#</th>
				<?php if($doc->status == 0) : ?>
					<th class="fix-width-40"></th>
				<?php endif; ?>
					<th class="min-width-150">สินค้า</th>
					<th class="fix-width-100 text-center">จำนวน</th>
					<th class="fix-width-100 text-center">ต้นทาง</th>
				</tr>
			</thead>
			<tbody id="temp-list">
				<tr>
					<td colspan="5" class="text-center">---กรุณาระบุโซน---</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="control-box">
		<div>
			<div class="width-100" id="to-zone-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus"
					style="padding-left:15px; padding-right:40px;" id="to-barcode-zone" placeholder="Barcode Zone" autocomplete="off">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:15px; right:22px; color:grey;"></i>
				</span>
			</div>
			<div class="width-100 padding-right-5 margin-bottom-10 text-center hide" id="to-item-qty">
				<button type="button" class="btn btn-default btn-qty" id="btn-to-decrese"><i class="fa fa-minus"></i></button>
				<input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="to-qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
				<button type="button" class="btn btn-default btn-qty" id="btn-to-increse"><i class="fa fa-plus"></i></button>
			</div>

			<div class="width-100 e-item hide" id="to-item-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus e"
					style="padding-left:15px; padding-right:40px;" id="to-barcode-item" inputmode="none"  placeholder="Barcode Item" autocomplete="off">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:72px; right:22px; color:grey;"></i>
				</span>
			</div>
		</div>
	</div>
	<div class="width-100 text-center bottom-info hide-text" id="to-zone-name">กรุณาระบุโซน</div>
	<input type="hidden" id="to-zone-code" />
</div>

<script id="tempTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="5" class="text-center"><h4>ไม่พบรายการ</h4></td>
		</tr>
	{{else}}
		{{#if @last}}
			<tr style="font-size:18px;">
				<?php if($doc->status == 0) : ?>
					<td colspan="3" class="text-right">Total</td>
				<?php else : ?>
					<td colspan="2" class="text-right">Total</td>
				<?php endif; ?>
				<td class="middle text-center" id="temp-total">0</td>
				<td>
					{{#each bcList}}
						<input type="hidden" id="bc-{{barcode}}" data-code="{{product_code}}" value="{{product_code}}" />
					{{/each}}
				</td>
			</tr>
		{{else}}
			<tr class="font-size-12 row-temp" id="row-temp-{{ id }}">
				<td class="middle text-center tmp">{{ no }}</td>
			<?php if($doc->status == 0) : ?>
				<td class="middle text-center" id="btn-delete-{{id}}">{{{btn_delete}}}</td>
			<?php endif; ?>
				<td class="middle">{{ products }}</td>
				<td class="middle text-center temp-qty" data-id="{{id}}" id="qty-temp-{{id}}">{{ qty }}</td>
				<td class="middle text-center">{{ from_zone }}	</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>
<script>
	$('#to-barcode-zone').autocomplete({
		source:BASE_URL + 'auto_complete/get_zone_code_and_name',
		autoFocus:true,
		close:function() {
			let arr = $(this).val().split(' | ');

			if(arr.length == 2) {
				$(this).val(arr[0]);

				setTimeout(() => {
					getToZone();
				}, 100);
			}
			else {
				$(this).val('');
			}
		}
	})
</script>
