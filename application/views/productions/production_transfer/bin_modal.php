<div class="modal fade" id="bin-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" style="width:550px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header" id="bin-modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title pointer" id="bin-modal-title"></h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
					 <input type="hidden" id="from-bin-stock-uid" value="" />
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:scroll;;">
             <table class="table table-bordered tableFixHead" style="min-width:500px; margin-left:-1px;">
               <thead>
                 <tr class="font-size-11">
                   <th class="fix-width-40 text-center fix-header">#</th>
                   <th class="fix-width-100 fix-header">Warehouse</th>
									 <th class="fix-width-150 fix-header">Bin Location</th>
									 <th class="fix-width-100 fix-header">In Stock</th>
									 <th class="fix-width-100 fix-header">Qty</th>
                 </tr>
               </thead>
               <tbody id="bin-modal-table">

               </tbody>
             </table>
           </div>
         </div>
       </div>
			 <div class="modal-footer">
				 <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-xs btn-primary" onClick="addToRow()" >OK</button>
			 </div>
		</div>
	</div>
</div>


<script id="bin-modal-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="5" class="text-center">-- Not found --</td></tr>
		{{else}}
			<tr class="font-size-11 bi-rows" id="bin-row-{{uid}}">
				<td class="middle text-center bin-no">{{no}}</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-label" value="{{WhsCode}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-label" value="{{BinCode}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-right text-label" id="bin-stock-{{uid}}" value="{{Qty}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-right text-label bin-qty"
					id="bin-qty-{{uid}}"
					data-uid="{{uid}}"
					data-bin="{{BinCode}}"
					data-whs="{{WhsCode}}"
					data-instock="{{Qty}}"
					value="" ondblclick="fillStockQty('{{uid}}')" onchange="updateBinSelected('{{uid}}')"/>
				</td>
			</tr>
		{{/if}}
	{{/each}}
</script>

<script>
	function fillStockQty(uid) {
		let qty = parseDefaultFloat(removeCommas($('#bin-qty-'+uid).data('instock')), 0);

		if(qty <= 0) {
			return false;
		}

		if(qty > 0) {
			$('#bin-qty-'+uid).val(addCommas(qty));

			updateBinSelected(uid);
		}
	}


	function updateBinSelected(uid) {
		let qty = parseDefaultFloat(removeCommas($('#bin-qty-'+uid).val()), 0);

		$('.bin-qty').val('');

		$('#bin-qty-'+uid).val(addCommas(qty));
	}
</script>
