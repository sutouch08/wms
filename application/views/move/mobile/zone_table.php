<div class="move-table" id="zone-table" style="padding-bottom:250px;">
	<div class="nav-title">ย้ายสินค้าออก</div>
	<table class="table table-bordered">
		<thead>
			<tr class="">
				<th class="fix-width-50 text-center">#</th>
				<th class="min-width-200">สินค้า</th>
				<th class="fix-width-100 text-center">จำนวน</th>
			</tr>
		</thead>
		<tbody id="zone-list">
			<tr>
				<td colspan="3" class="text-center">---กรุณาระบุโซน---</td>
			</tr>
		</tbody>
	</table>

	<div class="control-box">
		<div>
			<div class="width-100" id="from-zone-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus"
					style="padding-left:15px; padding-right:40px;" id="from-barcode-zone" inputmode="none" placeholder="Barcode Zone" autocomplete="off">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:15px; right:22px; color:grey;"></i>
				</span>
			</div>
			<div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="from-item-qty">
				<button type="button" class="btn btn-default btn-qty" id="btn-from-decrese"><i class="fa fa-minus"></i></button>
				<input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="from-qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
				<button type="button" class="btn btn-default btn-qty" id="btn-from-increse"><i class="fa fa-plus"></i></button>
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
	<div class="width-100 text-center bottom-info hide-text" id="from-zone-name">กรุณาระบุโซน</div>
  <input type="hidden" id="from-zone-code" />
</div>


<script id="zoneTemplate" type="text/x-handlebars-template">
  {{#each this}}
    {{#if nodata}}
    <tr><td colspan="3" class="text-center"><h4>ไม่พบสินค้าในโซน</h4></td></tr>
    {{else}}
    <tr id="row-{{barcode}}">
      <td class="text-center no">{{ no }}</td>
      <td>{{ products }}</td>
      <td class="text-center" id="from-qty-{{barcode}}" data-qty="{{qty}}">{{ qty }}</td>
    </tr>
    {{/if}}
  {{/each}}
</script>
