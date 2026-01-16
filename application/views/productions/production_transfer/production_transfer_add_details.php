<div class="row" style="margin-left: -8px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
    <table class="table table-bordered tableFixHead" style="min-width:1220px; margin-bottom:20px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 middle fix-header">&nbsp;</th>
          <th class="fix-width-40 middle text-center fix-header">#</th>
          <th class="fix-width-50 text-center fix-header">Batch</th>
          <th class="fix-width-200 middle fix-header">Item Code</th>
          <th class="fix-width-200 middle fix-header">Item Description.</th>
          <th class="fix-width-80 middle fix-header">From Whs</th>
          <th class="fix-width-150 middle fix-header">From Bin</th>
          <th class="fix-width-80 middle fix-header">To Whs</th>
          <th class="fix-width-120 middle fix-header">To Bin</th>
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
            <tr id="row-<?php echo $uid; ?>" data-uid="<?php echo $uid; ?>" class="font-size-11">
              <td class="middle text-center">
                <a class="pointer" href="javascript:removeRow('<?php echo $uid; ?>')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
              </td>
              <td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
              <td class="middle text-center">
                <?php if($rs->hasBatch) : ?>
                  <a class="pointer add-batch" href="javascript:getPreBatch('<?php echo $uid; ?>')" title="Add Batch Number">
                    <i class="fa fa-plus fa-lg blue"></i>
                  </a>
                <?php endif; ?>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs item-code r" data-uid="<?php echo $uid; ?>" data-hasbatch="<?php echo $rs->hasBatch ? 'Y' : 'N'; ?>" id="item-code-<?php echo $uid; ?>" value="<?php echo $rs->ItemCode; ?>" disabled/>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs item-name r" data-uid="<?php echo $uid; ?>" id="item-name-<?php echo $uid; ?>" value="<?php echo $rs->ItemName; ?>" disabled/>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs from-whs r" data-uid="<?php echo $uid; ?>" id="from-whs-<?php echo $uid; ?>" value="<?php echo $rs->fromWhsCode; ?>" onchange="fromBinInit('<?php echo $uid; ?>')" />
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs from-bin r" data-uid="<?php echo $uid; ?>" id="from-bin-<?php echo $uid; ?>" value="" />
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs to-whs r" data-uid="<?php echo $uid; ?>" id="to-whs-<?php echo $uid; ?>" value="" onchange="toBinInit('<?php echo $uid; ?>')"/>
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs to-bin r" data-uid="<?php echo $uid; ?>" id="to-bin-<?php echo $uid; ?>" value="" />
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs text-right req-qty r" data-uid="<?php echo $uid; ?>" id="instock-<?php echo $uid; ?>" value="<?php echo $rs->InStock; ?>" disabled/>
              </td>
              <td class="middle">
                <input type="text"
                  class="form-control input-xs text-right tran-qty r"
                  data-code="<?php echo $rs->ItemCode; ?>"
                  data-name="<?php echo $rs->ItemName; ?>"
                  data-hasbatch="<?php echo $rs->hasBatch ? 'Y' : 'N'; ?>"
                  data-uomentry="<?php echo $rs->UomEntry; ?>"
                  data-uomcode="<?php echo $rs->UomCode; ?>"
                  data-uom="<?php echo $rs->UomName; ?>"
                  data-uid="<?php echo $uid; ?>"
                  id="tran-qty-<?php echo $uid; ?>"
                  value="<?php echo number($rs->Qty, 2); ?>" />
              </td>
              <td class="middle">
                <input type="text" class="form-control input-xs r"  value="<?php echo $rs->UomName; ?>"  disabled/>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="divider-hidden"></div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-minier btn-primary" onclick="addRow()"><i class="fa fa-plus"></i>&nbsp; New Row</button>
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
          <textarea class="form-control input-xs" id="remark" rows="3"></textarea>
  			</div>
  		</div>
    </div>
  </div>
</div>

<?php $this->load->view('productions/production_transfer/production_modal'); ?>
<?php $this->load->view('productions/production_transfer/batch_modal'); ?>
<?php $this->load->view('productions/production_transfer/bin_modal'); ?>

<script id="batch-rows-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="batch-rows-{{uid}}" data-uid="{{uid}}" class="blue font-size-11 child-of-{{parentUid}}">
      <td class="middle text-center">
        <a class="pointer" href="javascript:removeBatchRow('{{uid}}')" title="Remove this row"><i class="fa fa-times fa-lg grey"></i></a>
      </td>
      <td colspan="4" class="middle italic">
        <span class="label label-success label-white middle">Batch No : {{batchNum}}</span>
        <span class="label label-info label-white middle">Attr1 : {{batchAttr1}}</span>
        <span class="label label-default label-white middle">Attr2 : {{batchAttr2}}</span>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue r" value="{{whsCode}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue r" value="{{binCode}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue r" id="batch-toWhs-{{uid}}" value="{{toWhsCode}}"  onchange="batchToBinInit('{{uid}}', true)"/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs blue r" id="batch-toBin-{{uid}}" value="{{toBinCode}}" />
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
        <input type="text" class="form-control input-xs from-whs r" data-uid="{{uid}}" id="from-whs-{{uid}}" value="{{fromWhsCode}}" onchange="fromBinInit('{{uid}}', true)" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs from-bin r" data-uid="{{uid}}" id="from-bin-{{uid}}" value="" onchange="getAvailableStock('{{uid}}')"/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs to-whs r" data-uid="{{uid}}" id="to-whs-{{uid}}" value="{{toWhsCode}}" onchange="toBinInit('{{uid}}', true)"/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs to-bin r" data-uid="{{uid}}" id="to-bin-{{uid}}" value="{{toBinCode}}" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-right req-qty r" data-uid="{{uid}}" id="instock-{{uid}}" value="{{InStock}}" disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-right tran-qty r"
          data-code="{{ItemCode}}"
          data-name="{{ItemName}}"
          data-hasbatch="{{ManBtchNum}}"
          data-uomentry="{{UomEntry}}"
          data-uomcode="{{UomCode}}"
          data-uom="{{UomName}}"
          data-method="Load"
          data-uid="{{uid}}"
          id="tran-qty-{{uid}}"
          value="{{Qty}}" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs r"
          data-uid="{{uid}}"
          id="uom-{{uid}}"
          data-uomentry="{{UomEntry}}"
          data-uomcode="{{UomCode}}"
          value="{{UomName}}"  disabled/>
      </td>
    </tr>
  {{/each}}
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
    <td class="middle text-center">
      <a class="pointer" href="javascript:removeRow('{{uid}}')" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
    </td>
    <td class="middle text-center fix-no no" scope="row"></td>
    <td class="middle text-center" id="batch-td-{{uid}}">
      {{#if hasBatch}}
        <a class="pointer add-batch" href="javascript:getPreBatch('{{uid}}')" title="Add Batch Number">
          <i class="fa fa-plus fa-lg blue"></i>
        </a>
      {{/if}}
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs item-code r" data-uid="{{uid}}" data-hasbatch="{{ManBtchNum}}" id="item-code-{{uid}}" value="{{ItemCode}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs item-name r" data-uid="{{uid}}" id="item-name-{{uid}}" value="{{ItemName}}" disabled/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs from-whs r" data-uid="{{uid}}" id="from-whs-{{uid}}" value="{{fromWhsCode}}" onchange="fromBinInit('{{uid}}', true)" />
    </td>
    <td class="middle" style="position:relative;">
      <input type="text" class="form-control input-xs from-bin r" style="padding-right:22px;" data-uid="{{uid}}" id="from-bin-{{uid}}" value="" onchange="getAvailableStock('{{uid}}')"/>
      <a class="bin-link" href="javascript:getBinStock('{{uid}}')"><i class="fa fa-arrow-left fa-lg blue"></i></a>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs to-whs r" data-uid="{{uid}}" id="to-whs-{{uid}}" value="{{toWhsCode}}" onchange="toBinInit('{{uid}}', true)"/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs to-bin r" data-uid="{{uid}}" id="to-bin-{{uid}}" value="{{toBinCode}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right req-qty r" data-uid="{{uid}}" id="instock-{{uid}}" value="{{InStock}}" disabled/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right tran-qty r"
        data-code="{{ItemCode}}"
        data-name="{{ItemName}}"
        data-hasbatch="{{ManBtchNum}}"
        data-uomentry="{{UomEntry}}"
        data-uomcode="{{UomCode}}"
        data-uom="{{UomName}}"
        data-method="Load"
        data-uid="{{uid}}"
        id="tran-qty-{{uid}}"
        value="{{Qty}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs r"
        data-uid="{{uid}}"
        id="uom-{{uid}}"
        data-uomentry="{{UomEntry}}"
        data-uomcode="{{UomCode}}"
        value="{{UomName}}"  disabled/>
    </td>
  </tr>
</script>
