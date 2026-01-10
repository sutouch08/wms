<div class="modal fade" id="production-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" style="width:1180px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header" id="production-modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="production-modal-title"></h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:auto;;">
             <table class="table table-bordered tableFixHead" style="min-width:1120px; margin-left:-1px;">
               <thead>
                 <tr class="font-size-11">
									 <th class="fix-width-40 text-center fix-header">#</th>
                   <th class="fix-width-200 fix-header">Item Code</th>
									 <th class="fix-width-300 fix-header">Description</th>
									 <th class="fix-width-80 fix-header">Warehouse</th>
									 <th class="fix-width-80 fix-header">Planned Qty</th>
									 <th class="fix-width-80 fix-header">Issued Qty</th>
									 <th class="fix-width-80 fix-header">Open Qty</th>
									 <th class="fix-width-80 fix-header">In Stock</th>
									 <th class="fix-width-80 fix-header">Qty</th>
									 <th class="fix-width-100 fix-header">Uom</th>
                 </tr>
               </thead>
               <tbody id="production-modal-table">

               </tbody>
             </table>
           </div>
         </div>
       </div>
			 <div class="modal-footer">
				 <button type="button" class="btn btn-xs btn-default top-btn" data-dismiss="modal">Cancel</button>
				 <button type="button" class="btn btn-xs btn-yellow top-btn" onclick="chooseAll()">เลือกทั้งหมด</button>
				 <button type="button" class="btn btn-xs btn-purple top-btn" onclick="clearAll()">เคียร์ทั้งหมด</button>
         <button type="button" class="btn btn-xs btn-primary top-btn" onClick="addToOrder()">เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>


<script id="production-modal-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="10" class="text-center">-- Not found --</td></tr>
		{{else}}
			<tr class="font-size-11 pi-rows" id="pi-row-{{uid}}">
				<td class="middle text-center p-no"></td>
				<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{ItemCode}}" readonly /></td>
				<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{ItemName}}" readonly /></td>
				<td class="middle">{{whsCode}}</td>
				<td class="middle text-right">{{PlannedQty}}</td>
				<td class="middle text-right">{{IssuedQty}}</td>
				<td class="middle text-right"><input type="text" class="form-control input-xs text-label text-right" value="{{OpenQty}}" readonly /></td>
				<td class="middle text-right">{{InStock}}</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-right pi-qty"
						id="pi-qty-{{uid}}"
						data-uid="{{uid}}"
						data-code="{{ItemCode}}"
						data-name="{{ItemName}}"
						data-basetype="{{BaseType}}"
						data-baseentry="{{DocEntry}}"
						data-baseref="{{DocNum}}"
						data-baseline="{{LineNum}}"
						data-hasbatch="{{ManBtchNum}}"
						data-uom="{{UomName}}"
						data-uomentry="{{UomEntry}}"
						data-uomcode="{{UomCode}}"
						data-whscode="{{whsCode}}"
						data-plannedqty="{{PlannedQty}}"
						data-issuedqty="{{IssuedQty}}"
						data-balance="{{OpenQty}}"
						data-instock="{{InStock}}"
						value="" ondblclick="fillOpenQty('{{uid}}')" />
				</td>
				<td class="middle">{{UomName}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>

<script>
	function fillOpenQty(uid) {
		let qty = parseDefaultFloat(removeCommas($('#pi-qty-'+uid).data('balance')), 0);

		if(qty <= 0) {
			return false;
		}

		if(qty > 0) {
			$('#pi-qty-'+uid).val(addCommas(qty));
		}
	}

</script>
