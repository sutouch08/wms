<div class="move-table hide" id="find-table" style="padding-bottom:250px;">
	<div class="nav-title" id="item-sku">Find Items</div>
	<table class="table table-striped">
		<thead>
			<tr class="font-size-11">
				<td class="min-width-200">โซน</td>
				<td class="fix-width-100 text-center">จำนวน</td>
			</tr>
		</thead>
		<tbody id="location-list">
			<tr class="font-size-11">
				<td colspan="2" class="text-center">---กรุณาสแกนสินค้า---</td>
			</tr>
		</tbody>
	</table>

	<div class="control-box">
		<div>
			<div class="width-100" id="find-item-bc">
        <span class="width-100">
  				<input type="text" class="form-control input-lg text-center focus"
          style="padding-left:15px; padding-right:40px;" id="find-barcode-item" inputmode="none"  placeholder="Scan Barcode To Find Item" autocomplete="off">
  				<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:15px; right:22px; color:grey;"></i>
        </span>
			</div>
		</div>
	</div>
</div>


<script id="locationTemplate" type="text/x-handlebars-template">
  {{#each this}}
    {{#if nodata}}
    	<tr class="font-size-11">
				<td colspan="2" class="text-center">--ไม่พบสินค้าในโซนใดๆ--</td>
			</tr>
    {{else}}
    <tr class="font-size-11">
      <td>{{ zone_name }}</td>
      <td class="text-center">{{ qty }}</td>
    </tr>
    {{/if}}
  {{/each}}
</script>
