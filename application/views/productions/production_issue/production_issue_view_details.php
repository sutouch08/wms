<?php
$totalItemsRows = 0;
$totalBatchRows = 0;
$totalQty = 0;
?>
<div class="row" style="margin-left: -8px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
    <table class="table table-bordered tableFixHead" style="min-width:1050px; margin-bottom:20px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 middle text-center fix-header">#</th>
          <th class="fix-width-50 text-center fix-header">Batch</th>
          <th class="fix-width-200 middle fix-header">Item Code</th>
          <th class="min-width-350 middle fix-header">Item Description.</th>
          <th class="fix-width-80 middle fix-header">Warehouse</th>
          <th class="fix-width-150 middle fix-header">Bin Location</th>
          <th class="fix-width-80 middle fix-header">Qty</th>
          <th class="fix-width-100 middle fix-header">Uom</th>
        </tr>
      </thead>
      <tbody id="details-table">
      <?php if( ! empty($details)) : ?>
        <?php $no = 1; ?>
        <?php foreach($details as $rs) : ?>
          <?php $uid = $rs->uid; ?>
          <tr id="row-<?php echo $uid; ?>" data-uid="<?php echo $uid; ?>" class="font-size-11">
            <td class="middle text-center fix-no no"><?php echo $no; ?></td>
            <td class="middle text-center">
              <?php if($rs->hasBatch OR ! empty($rs->batchRows)) : ?>
                <a class="pointer add-batch" href="javascript:toggleBatchRow('<?php echo $uid; ?>')" id="toggle-batch-row-<?php echo $uid; ?>" data-option="show" title="Hide Batch Number">
                  <i class="fa fa-minus fa fa-lg"></i>
                </a>
              <?php endif; ?>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label item-code r" id="item-code-<?php echo $uid; ?>" value="<?php echo $rs->ItemCode; ?>" readonly/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label item-name r" value="<?php echo $rs->ItemName; ?>" readonly/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label r" data-uid="<?php echo $uid; ?>" id="whs-<?php echo $uid; ?>" value="<?php echo $rs->WhsCode; ?>" readonly />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label r" data-uid="<?php echo $uid; ?>" id="bin-<?php echo $uid; ?>" value="<?php echo $rs->BinCode; ?>" readonly/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label text-right issue-qty r" data-uid="<?php echo $uid; ?>" id="issue-qty-<?php echo $uid; ?>" value="<?php echo number($rs->Qty, 2); ?>" readonly/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label" value="<?php echo $rs->unitMsr; ?>"  readonly/>
            </td>
          </tr>
          <?php $totalItemsRows++; ?>
          <?php $totalQty += $rs->Qty; ?>
          <?php $no++; ?>
          <?php if( ! empty($rs->batchRows)) : ?>
            <?php $bno = 1; ?>
            <?php foreach($rs->batchRows as $rb) : ?>
              <?php $uuid = $rb->uid; ?>
              <?php $batchWords = "Batch: {$rb->BatchNum} &nbsp;&nbsp;&nbsp; Attr1: {$rb->BatchAttr1} &nbsp;&nbsp;&nbsp; Attr2: {$rb->BatchAttr2}"; ?>

              <tr id="batch-rows-<?php echo $uuid; ?>" data-uid="<?php echo $uuid; ?>" class="blue font-size-11 child-of-<?php echo $uid; ?>">
                <td class="middle text-center"><?php echo $bno; ?></td>
                <td colspan="3" class="middle italic">
                  <span class="label label-success label-white middle">Batch No : <?php echo $rb->BatchNum; ?></span>
                  <span class="label label-info label-white middle">Attr 1 : <?php echo empty($rb->BatchAttr1) ? '-' : $rb->BatchAttr1; ?></span>
                  <span class="label label-default label-white middle">Attr 2 : <?php echo empty($rb->BatchAttr2) ? '-' : $rb->BatchAttr2; ?></span>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label blue r" value="<?php echo $rb->WhsCode; ?>" readonly />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label blue r" value="<?php echo $rb->BinCode; ?>" readonly />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label blue text-right batch-qty r" value="<?php echo number($rb->Qty, 2); ?>" readonly/>
                </td>
                <td>
                  <input type="text" class="form-control input-xs text-label blue r" value="<?php echo $rs->unitMsr; ?>"  readonly/>
                </td>
              </tr>
              <?php $totalBatchRows++; ?>
              <?php $bno++; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<hr/>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5" style="padding-left:17px;">
    <div class="form-horizontal">
      <div class="form-group">
  			<label class="float-left fix-width-60">User</label>
  			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  				<input type="text" id="user" class="form-control input-xs" value="<?php echo $this->_user->uname; ?>" disabled/>
  			</div>
  		</div>

      <div class="form-group">
  			<label class="float-left fix-width-60">Remark</label>
  			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
          <textarea class="form-control input-xs" id="remark" rows="3" disabled><?php echo $doc->remark; ?></textarea>
  			</div>
  		</div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
  			<label class="col-lg-9 col-md-8 col-sm-7 col-xs-6 padding-5 text-right">Total Items rows</label>
  			<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6 padding-5">
  				<input type="text" id="total-item-row" class="form-control input-xs" value="<?php echo number($totalItemsRows, 2); ?>" disabled/>
  			</div>
  		</div>
      <div class="form-group">
  			<label class="col-lg-9 col-md-8 col-sm-7 col-xs-6 padding-5 text-right">Total Batch rows</label>
  			<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6 padding-5">
  				<input type="text" id="total-batch-row" class="form-control input-xs" value="<?php echo number($totalBatchRows, 2); ?>" disabled/>
  			</div>
  		</div>
      <div class="form-group">
  			<label class="col-lg-9 col-md-8 col-sm-7 col-xs-6 padding-5 text-right">Total Qty</label>
  			<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6 padding-5">
  				<input type="text" id="total-item-qty" class="form-control input-xs" value="<?php echo number($totalQty, 2); ?>" disabled/>
  			</div>
  		</div>
    </div>
  </div>
</div>


<div class="modal fade" id="production-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" style="width:1000px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header" id="production-modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="production-modal-title"></h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:auto;;">
             <table class="table table-bordered tableFixHead" style="min-width:940px; margin-left:-1px;">
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
                 </tr>
               </thead>
               <tbody id="production-modal-table">

               </tbody>
             </table>
           </div>
         </div>
       </div>
       <div class="modal-footer">
				<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
			 </div>
		</div>
	</div>
</div>


<div class="modal fade" id="pre-batch-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog" style="width:900px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header" id="pre-batch-modal-header">
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
         <button type="button" class="btn btn-xs btn-primary" onClick="viewItemBatch()" >Choose</button>
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
				<h4 class="modal-title" id="batch-modal-title"></h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:scroll;;" id="batch-list-table">
             <table class="table table-bordered tableFixHead" id="batch-table" style="min-width:850px; margin-left:-1px;">
               <thead>
                 <tr class="font-size-11">
                   <th class="fix-width-40 text-center fix-header">#</th>
                   <th class="fix-width-150 fix-header">Batch No</th>
									 <th class="fix-width-150 fix-header">Batch Attr1</th>
									 <th class="fix-width-150 fix-header">Batch Attr2</th>
									 <th class="min-width-200 fix-header">Bin Location</th>
									 <th class="fix-width-100 fix-header">In Stock</th>
                 </tr>
               </thead>
               <tbody id="batch-modal-table">

               </tbody>
             </table>
           </div>
         </div>
       </div>
			 <div class="modal-footer">
         <button type="button" class="btn btn-xs btn-info" onclick="showFilterBatch()">Filter</button>
				<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
			 </div>
		</div>
	</div>
</div>

<script>
  $('#whs-filter').select2();
</script>


<script id="batch-modal-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="6" class="text-center">-- Not found --</td></tr>
		{{else}}
			<tr class="font-size-11 bi-rows">
				<td class="middle text-center b-no"></td>
				<td class="middle">{{BatchNum}}</td>
				<td class="middle">{{BatchAttr1}}</td>
				<td class="middle">{{BatchAttr2}}</td>
				<td class="middle">{{BinCode}}</td>
				<td class="middle">{{Qty}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>

<script id="production-modal-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="8" class="text-center">-- Not found --</td></tr>
		{{else}}
			<tr class="font-size-11 pi-rows">
				<td class="middle text-center p-no"></td>
				<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{ItemCode}}" readonly /></td>
				<td class="middle"><input type="text" class="form-control input-xs text-label" value="{{ItemName}}" readonly /></td>
				<td class="middle">{{fromWhsCode}}</td>
				<td class="middle text-right">{{PlannedQty}}</td>
				<td class="middle text-right">{{IssuedQty}}</td>
				<td class="middle text-right">{{OpenQty}}</td>
				<td class="middle text-right">{{InStock}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>
