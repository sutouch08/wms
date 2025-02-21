<div class="move-table hide" id="move-table">
	<div class="nav-title">รายการโอนย้าย</div>
	<div class="col-xs-12 padding-0 table-responsive" style="margin-bottom:80px; border-bottom:solid 1px #ccc;">
		<table class="table table-striped table-bordered" style="min-width:630px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-40"></th>
					<th class="min-width-150">สินค้า</th>
					<th class="fix-width-100 text-center">ยอดตั้ง</th>
					<th class="fix-width-100 text-center">ยอดรับ</th>
					<th class="fix-width-100">ต้นทาง</th>
					<th class="fix-width-100">ปลายทาง</th>
				</tr>
			</thead>
			<tbody id="move-list">

			</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center total-move">
		<span class="pull-left">Total</span><span id="wms-total">0</span> / <span id="move-total">0</span>
	</div>
</div>

<script id="moveTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
		<tr style="font-size:18px;">
			<td colspan="3" class="text-right">Total</td>
			<td class="middle text-center" id="move-table-total-qty">0</td>
			<td class="middle text-center" id="move-table-total-wms">0</td>
			<td colspan="2"></td>
		</tr>
		{{else}}
		<tr class="font-size-12" id="row-{{ id }}">
			<td class="middle text-center mo">{{ no }}</td>
			<td class="middle text-center">{{{ btn_delete }}}</td>
			<td class="middle">{{ product_code }}</td>
			<td class="middle text-center qty">{{ qty_label }}</td>
			<td class="middle text-center wms" id="wms-{{id}}">{{ wms_qty_label }}</td>
			<td class="middle hide-text">{{ from_zone }}</td>
			<td class="middle hide-text">{{{ to_zone }}}</td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>
