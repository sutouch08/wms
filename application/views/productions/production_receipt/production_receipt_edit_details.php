<?php
$totalItemsRows = 0;
$totalBatchRows = 0;
$totalQty = 0;
?>
<div class="row" style="margin-left: -8px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
    <table class="table tableFixHead" style="min-width:1220px; margin-bottom:20px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 middle fix-header">&nbsp;</th>
          <th class="fix-width-40 middle text-center fix-header">#</th>
          <th class="fix-width-50 text-center fix-header">Batch</th>
          <th class="fix-width-200 middle fix-header">Item Code</th>
          <th class="min-width-200 middle fix-header">Item Description.</th>
          <th class="fix-width-80 middle fix-header">Warehouse</th>
          <th class="fix-width-150 middle fix-header">Bin Location</th>
          <th class="fix-width-80 middle fix-header">Trans. Type</th>
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
              <td class="middle text-center">
                <a class="pointer" href="javascript:removeRow('<?php echo $uid; ?>')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
              </td>
              <td class="middle text-center fix-no no" scope="row"></td>
              <td class="middle text-center">
                <?php if($rs->hasBatch) : ?>
                  <a class="pointer add-batch" href="javascript:addBatchRow('<?php echo $uid; ?>')" title="Add Batch Number">
                    <i class="fa fa-plus fa-lg blue"></i>
                  </a>
                <?php endif; ?>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs item-code r"
                data-uid="<?php echo $uid; ?>" data-hasbatch="<?php echo $rs->hasBatch ? 'Y' : 'N'; ?>"
                id="item-code-<?php echo $uid; ?>" value="<?php echo $rs->ItemCode; ?>" disabled/>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs item-name r"
                data-uid="<?php echo $uid; ?>" id="item-name-<?php echo $uid; ?>" value="<?php echo $rs->ItemName; ?>" disabled/>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs r" data-uid="<?php echo $uid; ?>" id="whs-<?php echo $uid; ?>" value="<?php echo $rs->WhsCode; ?>" onchange="binInit('<?php echo $uid; ?>')" />
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs r" data-uid="<?php echo $uid; ?>" id="bin-<?php echo $uid; ?>" value="<?php echo $rs->BinCode; ?>" data-whs="<?php echo $rs->WhsCode; ?>" />
              </td>
              <td class="middle">
                <select class="form-control input-xs" data-uid="<?php echo $uid; ?>" id="tran-type-<?php echo $uid; ?>">
                  <option value="C" <?php echo is_selected('C', $rs->TranType); ?>>Complete</option>
                  <option value="R" <?php echo is_selected('R', $rs->TranType); ?>>Reject</option>
                </select>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs text-right receipt-qty r"
                  data-no="0"
                  data-code="<?php echo $rs->ItemCode; ?>"
                  data-name="<?php echo $rs->ItemName; ?>"
                  data-hasbatch="<?php echo $rs->hasBatch ? 'Y' : 'N'; ?>"
                  data-basetype="<?php echo $rs->BaseType; ?>"
                  data-baseref="<?php echo $rs->BaseRef; ?>"
                  data-baseentry="<?php echo $rs->BaseEntry; ?>"
                  data-uomentry="<?php echo $rs->UomEntry; ?>"
                  data-uomcode="<?php echo $rs->UomCode; ?>"
                  data-uom="<?php echo $rs->unitMsr; ?>"
                  data-uid="<?php echo $uid; ?>"
                  id="receipt-qty-<?php echo $uid; ?>"
                  value="<?php echo number($rs->Qty, 2);?>" />
              </td>
              <td class="middle"><input type="text" class="form-control input-xs r"  value="<?php echo $rs->unitMsr; ?>"  disabled/></td>
            </tr>
            <?php $totalItemsRows++; ?>
            <?php $totalQty += $rs->Qty; ?>
            <?php $no++; ?>
            <?php if( ! empty($rs->batchRows)) : ?>
              <?php $bno = 1; ?>
              <?php foreach($rs->batchRows as $br) : ?>
                <tr id="batch-row-<?php echo $br->uid; ?>" data-uid="<?php echo $br->uid; ?>" class="blue font-size-11 child-of-<?php echo $uid; ?>">
                  <td class="middle text-center">
                    <a class="pointer" href="javascript:removeBatchRow('<?php echo $br->uid; ?>')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a>
                  </td>
                  <td class="middle text-center italic b-<?php echo $uid; ?>"><?php echo $bno; ?></td>
                  <td colspan="6" class="middle italic">
                    <div class="input-group width-30 float-left">
                      <span class="input-group-addon batch-label">Batch No. :</span>
                      <input type="text" class="form-control input-xs batch-row r"
                      id="batch-<?php echo $br->uid; ?>" data-uid="<?php echo $br->uid; ?>" data-parent="<?php echo $uid; ?>" value="<?php echo $br->BatchNum; ?>" onpaste="handlePaste(event, $(this))" />
                    </div>
                    <div class="input-group width-30 float-left">
                      <span class="input-group-addon batch-label">Attr1 :</span>
                      <input type="text" class="form-control input-xs batch-attr1 r"
                      id="batch-attr1-<?php echo $br->uid; ?>" data-uid="<?php echo $br->uid; ?>" data-parent="<?php echo $uid; ?>" value="<?php echo $br->BatchAttr1; ?>" />
                    </div>
                    <div class="input-group width-30 float-left">
                      <span class="input-group-addon batch-label">Attr2 :</span>
                      <input type="text" class="form-control input-xs batch-attr2 r"
                      id="batch-attr2-<?php echo $br->uid; ?>" data-uid="<?php echo $br->uid; ?>" data-parent="<?php echo $uid; ?>" value="<?php echo $br->BatchAttr2; ?>" />
                    </div>
                  </td>
                  <td class="middle">
                    <input type="text" class="form-control input-xs blue text-right batch-qty r"
                    id="batch-qty-<?php echo $br->uid; ?>"
                    data-uid="<?php echo $br->uid; ?>"
                    data-parent="<?php echo $uid; ?>"
                    value="<?php echo number($br->Qty, 2); ?>" />
                  </td>
                  <td>
                    <input type="text" class="form-control input-xs blue r" value="<?php echo $rs->unitMsr; ?>"  disabled/>
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
          <textarea class="form-control input-xs" id="remark" rows="3"><?php echo $doc->remark; ?></textarea>
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

<?php $this->load->view('productions/production_receipt/production_modal'); ?>
<?php $this->load->view('productions/production_receipt/batch_modal'); ?>

<script id="batch-row-template" type="text/x-handlebarsTemplate">
  <tr id="batch-row-{{uid}}" data-uid="{{uid}}" class="blue font-size-11 child-of-{{parentUid}}">
    <td class="middle text-center">
      <a class="pointer" href="javascript:removeBatchRow('{{uid}}')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a>
    </td>
    <td class="middle text-center italic b-{{parentUid}}"></td>
    <td colspan="6" class="middle italic">
      <div class="input-group width-30 float-left">
        <span class="input-group-addon batch-label">Batch No. :</span>
        <input type="text" class="form-control input-xs batch-row r" id="batch-{{uid}}" data-uid="{{uid}}" data-parent="{{parentUid}}" value="" onpaste="handlePaste(event, $(this))"/>
      </div>
      <div class="input-group width-30 float-left">
        <span class="input-group-addon batch-label">Attr1 :</span>
        <input type="text" class="form-control input-xs batch-attr1 r" id="batch-attr1-{{uid}}" data-uid="{{uid}}" data-parent="{{parentUid}}" value="" />
      </div>
      <div class="input-group width-30 float-left">
        <span class="input-group-addon batch-label">Attr2 :</span>
        <input type="text" class="form-control input-xs batch-attr2 r" id="batch-attr2-{{uid}}" data-uid="{{uid}}" data-parent="{{parentUid}}" value="" />
      </div>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs blue text-right batch-qty r"
      id="batch-qty-{{uid}}"
      data-uid="{{uid}}"
      data-parent="{{parentUid}}"
      value="" />
    </td>
    <td>
      <input type="text" class="form-control input-xs blue r" value="{{UomName}}"  disabled/>
    </td>
  </tr>
</script>

<script id="details-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
      <td class="middle text-center">
        <a class="pointer" href="javascript:removeRow('{{uid}}')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
      </td>
      <td class="middle text-center fix-no no" scope="row"></td>
      <td class="middle text-center">
        {{#if hasBatch}}
          <a class="pointer add-batch" href="javascript:addBatchRow('{{uid}}')" title="Add Batch Number">
            <i class="fa fa-plus fa-lg blue"></i>
          </a>
        {{/if}}
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs item-code r" data-uid="{{uid}}" data-hasbatch="{{ManBtchNum}}" id="item-code-{{uid}}" value="{{ItemCode}}" disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs item-name r" data-uid="{{uid}}" id="item-name-{{uid}}" value="{{ItemName}}" disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs r" data-uid="{{uid}}" id="whs-{{uid}}" value="{{whsCode}}" onchange="binInit('{{uid}}')" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs r" data-uid="{{uid}}" id="bin-{{uid}}" value="" />
      </td>
      <td class="middle">
        <select class="form-control input-xs" data-uid="{{uid}}" id="tran-type-{{uid}}">
          <option value="C" {{tranComplete}}>Complete</option>
          <option value="R" {{tranReject}}>Reject</option>
        </select>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-right receipt-qty r"
          data-no="0"
          data-code="{{ItemCode}}"
          data-name="{{ItemName}}"
          data-hasbatch="{{ManBtchNum}}"
          data-basetype="{{BaseType}}"
          data-baseref="{{BaseRef}}"
          data-baseentry="{{BaseEntry}}"
          data-uomentry="{{UomEntry}}"
          data-uomcode="{{UomCode}}"
          data-uom="{{UomName}}"
          data-uid="{{uid}}"
          id="receipt-qty-{{uid}}"
          value="{{Qty}}" />
      </td>
      <td class="middle"><input type="text" class="form-control input-xs r"  value="{{UomName}}"  disabled/></td>
    </tr>
  {{/each}}
</script>

<!-- not use -->
<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
    <td class="middle text-center">
      <a class="pointer" href="javascript:removeRow('{{uid}}')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
    </td>
    <td class="middle text-center fix-no no" scope="row"></td>
    <td class="middle text-center" id="batch-btn-{{uid}}">
      {{#if hasBatch}}
        <a class="pointer add-batch" href="javascript:getBatch('{{uid}}')" title="Add Batch Number">
          <i class="fa fa-plus fa-lg blue"></i>
        </a>
      {{/if}}
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs item-code r" data-uid="{{uid}}" data-hasbatch="N" id="item-code-{{uid}}" value="" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs item-name" data-uid="{{uid}}" id="item-name-{{uid}}" value="" disabled/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs from-whs r" data-uid="{{uid}}" id="from-whs-{{uid}}" value="{{fromWhsCode}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs from-bin r" data-uid="{{uid}}" id="from-bin-{{uid}}" value="" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs to-whs r" data-uid="{{uid}}" id="to-whs-{{uid}}" value="{{toWhsCode}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs to-bin r" data-uid="{{uid}}" id="to-bin-{{uid}}" value="{{toBinCode}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-label text-right req-qty r" data-uid="{{uid}}" id="req-qty-{{uid}}" value="0" disabled/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right tran-qty r" data-uid="{{uid}}" data-method="Manual" id="tran-qty-{{uid}}" value="" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs"
        data-uid="{{uid}}"
        id="uom-{{uid}}"
        data-uomentry=""
        data-uomcode=""
        value=""  disabled/>
    </td>
  </tr>
</script>
