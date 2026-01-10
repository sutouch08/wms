<div class="modal fade" id="pre-batch-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" style="width:900px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header" id="pre-batch-modal-header" style="border-bottom:solid 1px #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Batch Filter</h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
					 <input type="hidden" id="pre-target-uid" value="" />
					 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
						 <label>Warehouse</label>
						 <select class="form-control input-xs" id="whs-filter">
							 <option value="all">All | ทั้งหมด</option>
							 <?php echo select_warehouse(); ?>
						 </select>
					 </div>
					 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
						 <label>Batch Num</label>
						 <input type="text" class="form-control input-xs" id="batch-num-filter" value="" />
					 </div>
					 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
						 <label>Attr1</label>
						 <input type="text" class="form-control input-xs" id="attr1-filter" />
					 </div>
					 <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
						 <label>Attr2</label>
						 <input type="text" class="form-control input-xs" id="attr2-filter" />
					 </div>
         </div>
       </div>
			 <div class="modal-footer">
         <button type="button" class="btn btn-xs btn-primary" onClick="getBatch()" >Choose</button>
				 <button type="button" class="btn btn-xs btn-warning" onClick="clearBatchFilter()" >Clear</button>
				<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
			 </div>
		</div>
	</div>
</div>


<div class="modal fade" id="batch-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" style="width:900px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header" id="batch-modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">
					<span id="batch-modal-title"></span>
					<span class="pull-right" id="tr-qty" style="margin-right:25px;">0</span>
					<span class="pull-right">&nbsp;/&nbsp;</span>
					<span class="pull-right" id="total-batch-qty">0</span>
				</h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
					 <input type="hidden" id="target-uid" value="" />
					 <input type="hidden" id="target-qty" value="0" />
					 <input type="hidden" id="target-qtty" value="0" />

           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:scroll;;" id="batch-list-table">
             <table class="table table-bordered tableFixHead" id="batch-table" style="min-width:850px; margin-left:-1px;">
               <thead>
                 <tr class="font-size-11">
                   <th class="fix-width-40 text-center fix-header">#</th>
                   <th class="fix-width-150 fix-header">Batch No</th>
									 <th class="fix-width-150 fix-header">Batch Attr1</th>
									 <th class="fix-width-100 fix-header">Batch Attr2</th>
									 <th class="min-width-200 fix-header">Bin Location</th>
									 <th class="fix-width-100 fix-header">In Stock</th>
									 <th class="fix-width-100 fix-header">Qty</th>
                 </tr>
               </thead>
               <tbody id="batch-modal-table">

               </tbody>
             </table>
           </div>
         </div>
       </div>
			 <div class="modal-footer">
				 <button type="button" class="btn btn-xs btn-info" onClick="showFilterBatch()" >Filter</button>
        <button type="button" class="btn btn-xs btn-primary" onClick="addBatchRows()" >Choose</button>
				<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
			 </div>
		</div>
	</div>
</div>


<script id="batch-modal-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="7" class="text-center">-- Not found --</td></tr>
		{{else}}
			<tr class="font-size-11 bi-rows" id="bi-row-{{uid}}">
				<td class="middle text-center b-no"></td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-label" value="{{BatchNum}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-label" value="{{BatchAttr1}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-label" value="{{BatchAttr2}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-label" value="{{BinCode}}" readonly />
				</td>
				<td class="middle">
					<input type="text" class="form-control input-xs text-right text-label" id="bi-onhand-{{uid}}" value="{{Qty}}" readonly />
				</td>
				<td class="middle">
					<input type="number" class="form-control input-xs text-right text-label bi-qty"
					id="bi-qty-{{uid}}"
					data-uid="{{uid}}"
					data-batch="{{BatchNum}}"
					data-attr1="{{BatchAttr1}}"
					data-attr2="{{BatchAttr2}}"
					data-bin="{{BinCode}}"
					data-whs="{{WhsCode}}"
					data-instock="{{Qty}}"
					value="" ondblclick="fillBatchQty('{{uid}}')" onchange="updateTotalBatchSelected()"/>
				</td>
			</tr>
		{{/if}}
	{{/each}}
</script>

<script>
	function fillBatchQty(uid) {
		let qty = parseDefaultFloat(removeCommas($('#bi-qty-'+uid).data('instock')), 0);

		if(qty <= 0) {
			return false;
		}

		if(qty > 0) {
			$('#bi-qty-'+uid).val(addCommas(qty));
		}

		updateTotalBatchSelected();
	}


	function updateTotalBatchSelected() {
		let total = 0;
		$('.bi-qty').each(function() {
			let el = $(this);
			let qty =  parseDefaultFloat(el.val(), 0);

			if(qty >= 0) {
				total += qty;
			}
		});

		$('#target-qtty').val(total);
		$('#total-batch-qty').text(addCommas(total.toFixed(2)));
	}

	$('#whs-filter').select2();

</script>
