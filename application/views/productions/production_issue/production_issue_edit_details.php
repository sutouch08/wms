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
          <th class="fix-width-80 middle fix-header">In Stock</th>
          <th class="fix-width-80 middle fix-header">Qty</th>
          <th class="fix-width-100 middle fix-header">Uom</th>
        </tr>
      </thead>
      <tbody id="details-table">
        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <?php $uid = $rs->uid; ?>
              <tr id="row-<?php echo $rs->uid; ?>" data-uid="<?php echo $rs->uid; ?>" class="font-size-11">
                <td class="middle text-center">
                  <a class="pointer" href="javascript:removeRow('<?php echo $rs->uid; ?>')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
                </td>
                <td class="middle text-center fix-no no" scope="row"></td>
                <td class="middle text-center">
                  <?php if( ! empty($rs->hasBatch)) : ?>
                    <a class="pointer add-batch" href="javascript:getPreBatch('<?php echo $rs->uid; ?>')" title="Add Batch Number">
                      <i class="fa fa-plus fa-lg blue"></i>
                    </a>
                  <?php endif; ?>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs item-code r" data-uid="<?php echo $rs->uid; ?>" data-hasbatch="<?php echo $rs->hasBatch ? 'Y' : 'N'; ?>" id="item-code-<?php echo $rs->uid; ?>" value="<?php echo $rs->ItemCode; ?>" disabled/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs item-name r" data-uid="<?php echo $rs->uid; ?>" id="item-name-<?php echo $rs->uid; ?>" value="<?php echo $rs->ItemName; ?>" disabled/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs from-whs r" data-uid="<?php echo $rs->uid; ?>" id="whs-<?php echo $rs->uid; ?>" value="<?php echo $rs->WhsCode; ?>" onchange="binInit('<?php echo $rs->uid; ?>')" />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs from-bin r" data-uid="<?php echo $rs->uid; ?>" id="bin-<?php echo $rs->uid; ?>" value="<?php echo $rs->BinCode; ?>" />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-right r" data-uid="<?php echo $rs->uid; ?>" id="instock-<?php echo $rs->uid; ?>" value="<?php echo number($rs->InStock, 2); ?>" disabled/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-right issue-qty r"
                    data-code="<?php echo $rs->ItemCode; ?>"
                    data-name="<?php echo $rs->ItemName; ?>"
                    data-hasbatch="<?php echo $rs->hasBatch ? 'Y' : 'N'; ?>"
                    data-basetype="<?php echo $rs->BaseType; ?>"
                    data-baseref="<?php echo $rs->BaseRef; ?>"
                    data-baseentry="<?php echo $rs->BaseEntry; ?>"
                    data-baseline="<?php echo $rs->BaseLine; ?>"
                    data-uomentry="<?php echo $rs->UomEntry; ?>"
                    data-uomcode="<?php echo $rs->UomCode; ?>"
                    data-uom="<?php echo $rs->unitMsr; ?>"
                    data-uid="<?php echo $rs->uid; ?>"
                    id="issue-qty-<?php echo $rs->uid; ?>"
                    value="<?php echo number($rs->Qty, 2); ?>" />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs r" id="uom-<?php echo $rs->uid; ?>" value="<?php echo $rs->unitMsr; ?>"  disabled/>
                </td>
              </tr>
              <?php if( ! empty($rs->batchRows)) : ?>
                <?php foreach($rs->batchRows as $br) : ?>
                  <?php $instock = $this->stock_model->get_item_batch_qty($br->ItemCode, $br->BatchNum, $br->WhsCode, $br->BinCode); ?>
                  <tr id="batch-rows-<?php echo $br->uid; ?>" data-uid="<?php echo $br->uid; ?>" class="blue font-size-11 child-of-<?php echo $rs->uid; ?>">
                    <td class="middle text-center">
                      <a class="pointer" href="javascript:removeBatchRow('<?php echo $br->uid; ?>')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a>
                    </td>
                    <td colspan="4" class="middle italic">
                      <span class="label label-success label-white middle italic">Batch No : <?php echo $br->BatchNum; ?></span>
                      <span class="label label-info label-white middle italic">Attr1 : <?php echo $br->BatchAttr1; ?></span>
                      <span class="label label-default label-white middle italic">Attr2 : <?php echo $br->BatchAttr2;?></span>
                    </td>
                    <td class="middle">
                      <input type="text" class="form-control input-xs blue r" value="<?php echo $br->WhsCode; ?>" disabled />
                    </td>
                    <td class="middle">
                      <input type="text" class="form-control input-xs blue r" value="<?php echo $br->BinCode; ?>" disabled />
                    </td>
                    <td class="middle">
                      <input type="text" class="form-control input-xs blue text-right r" id="batch-in-stock-<?php echo $br->uid; ?>" value="<?php echo number($instock, 2); ?>" disabled />
                    </td>
                    <td class="middle">
                      <input type="text" class="form-control input-xs blue text-right batch-qty r"
                      id="batch-qty-<?php echo $br->uid; ?>"
                      data-uid="<?php echo $br->uid; ?>"
                      data-parent="<?php echo $rs->uid; ?>"
                      data-batchnum="<?php echo $br->BatchNum; ?>"
                      data-attr1="<?php echo $br->BatchAttr1; ?>"
                      data-attr2="<?php echo $br->BatchAttr2; ?>"
                      data-fromwhs="<?php echo $br->WhsCode; ?>"
                      data-frombin="<?php echo $br->BinCode; ?>"
                      value="<?php echo number($br->Qty, 2); ?>"
                      onchange="reCalBatchRows('<?php echo $rs->uid; ?>')"/>
                    </td>
                    <td>
                      <input type="text" class="form-control input-xs blue r" value="<?php echo $rs->unitMsr; ?>"  disabled/>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            <?php $no++; ?>
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
</div>

<?php $this->load->view('productions/production_issue/production_modal'); ?>
<?php $this->load->view('productions/production_issue/batch_modal'); ?>

<script id="batch-rows-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="batch-rows-{{uid}}" data-uid="{{uid}}" class="blue font-size-11 child-of-{{parentUid}}">
      <td class="middle text-center">
        <a class="pointer" href="javascript:removeBatchRow('{{uid}}')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a>
      </td>
      <td colspan="4" class="middle italic">
        <span class="label label-success label-white middle italic">Batch No : {{batchNum}}</span>
        <span class="label label-info label-white middle italic">Attr1 : {{batchAttr1}}</span>
        <span class="label label-default label-white middle italic">Attr2 : {{batchAttr2}}</span>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue r" value="{{whsCode}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue r" value="{{binCode}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue text-right r" id="batch-in-stock-{{uid}}" value="{{InStock}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue text-right batch-qty r"
        id="batch-qty-{{uid}}"
        data-uid="{{uid}}"
        data-parent="{{parentUid}}"
        data-batchnum="{{batchNum}}"
        data-attr1="{{batchAttr1}}"
        data-attr2="{{batchAttr2}}"
        data-fromwhs="{{whsCode}}"
        data-frombin="{{binCode}}"
        value="{{qty}}"
        onchange="reCalBatchRows('{{parentUid}}')"/>
      </td>
      <td>
        <input type="text" class="form-control input-xs blue r" value="{{UomName}}"  disabled/>
      </td>      
    </tr>
  {{/each}}
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
          <a class="pointer add-batch" href="javascript:getPreBatch('{{uid}}')" title="Add Batch Number">
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
        <input type="text" class="form-control input-xs from-whs r" data-uid="{{uid}}" id="whs-{{uid}}" value="{{whsCode}}" onchange="binInit('{{uid}}')" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs from-bin r" data-uid="{{uid}}" id="bin-{{uid}}" value="" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-right r" data-uid="{{uid}}" id="instock-{{uid}}" value="{{InStock}}" disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-right issue-qty r"
          data-code="{{ItemCode}}"
          data-name="{{ItemName}}"
          data-hasbatch="{{ManBtchNum}}"
          data-basetype="{{BaseType}}"
          data-baseref="{{BaseRef}}"
          data-baseentry="{{BaseEntry}}"
          data-baseline="{{BaseLine}}"
          data-uomentry="{{UomEntry}}"
          data-uomcode="{{UomCode}}"
          data-uom="{{UomName}}"
          data-method="Load"
          data-uid="{{uid}}"
          id="issue-qty-{{uid}}"
          value="{{Qty}}" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs r" value="{{UomName}}"  disabled/>
      </td>
    </tr>
  {{/each}}
</script>
