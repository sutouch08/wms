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
									 <th class="fix-width-80 fix-header">Planned</th>
									 <th class="fix-width-80 fix-header">Completed</th>
									 <th class="fix-width-80 fix-header">Rejected</th>
									 <th class="fix-width-80 fix-header">Trans. Type</th>
									 <th class="fix-width-80 fix-header">Qty</th>
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
         <button type="button" class="btn btn-xs btn-primary top-btn btn-100" onClick="addToOrder()">Add</button>
			 </div>
		</div>
	</div>
</div>


<script id="production-modal-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="9" class="text-center">-- Not found --</td></tr>
		{{else}}
			<tr class="font-size-11 pi-rows" id="pi-row-{{uid}}">
				<td class="middle text-center p-no"></td>
				<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{ItemCode}}" readonly /></td>
				<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{ItemName}}" readonly /></td>
				<td class="middle">{{WhsCode}}</td>
				<td class="middle text-right">{{Planned}}</td>
				<td class="middle text-right">{{Completed}}</td>
				<td class="middle text-right">{{Rejected}}</td>
				<td class="middle text-right">
					<select class="form-control input-xs" data-uid="{{uid}}" id="pi-type-{{uid}}">
						<option value="C">Complete</option>
						<option value="R">Reject</option>
					</select>
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-right pi-qty"
						id="pi-qty-{{uid}}"
						data-uid="{{uid}}"
						data-code="{{ItemCode}}"
						data-name="{{ItemName}}"
						data-basetype="202"
						data-baseentry="{{DocEntry}}"
						data-baseref="{{DocNum}}"
						data-hasbatch="{{ManBtchNum}}"
						data-uom="{{Uom}}"
						data-uomentry="{{UomEntry}}"
						data-uomcode="{{UomCode}}"
						data-whscode="{{WhsCode}}"
						data-plannedqty="{{PlannedQty}}"
						data-balance="{{Balance}}"
						value="" ondblclick="fillOpenQty('{{uid}}')"/>
				</td>
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
