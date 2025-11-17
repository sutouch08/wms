<div class="modal fade" id="batchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px; border-bottom:solid 1px #ddd;">
        <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center><h4 class="modal-title" id="batch-title">Item Code</h4></center>
        <input type="hidden" id="batch-item" value="" data-uid="" data-code="" data-limit=""/>
      </div>
      <div class="modal-body" style="max-width:94vw; height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="font-size:11px; table-layout: fixed; min-width:340px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-200 text-center">Batch Number</th>
            <th class="fix-width-100 text-center">Qty.</th>
          </thead>
          <tbody id="batch-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-xs btn-primary top-btn" onclick="addBatchRows()">OK</button>
       </div>
    </div>
  </div>
</div>

<script id="batch-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11 bt-rows-{{no}}">
    <td class="text-center">{{no}}</td>
    <td><input type="text" class="form-control input-xs text-label batch-no" id="batch-{{no}}" data-no="{{no}}" value="{{batchNo}}" /></td>
    <td><input type="number" class="form-control input-xs text-label batch-qty" id="batch-qty-{{no}}" data-no="{{no}}" value="{{qty}}" /></td>
  </tr>
</script>
