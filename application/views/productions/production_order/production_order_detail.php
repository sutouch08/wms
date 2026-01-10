<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
  <table class="table table-bordered tableFixHead" style="min-width:1255px; margin-bottom:20px;">
    <thead>
      <tr class="font-size-11">
        <th class="fix-width-25 middle text-center fix-no fix-header">#</th>
        <th class="fix-width-100 middle">Type</th>
        <th class="fix-width-200 middle">Item Code</th>
        <th class="fix-width-250 middle">Item Description.</th>
        <th class="fix-width-80 middle">Base Qty.</th>
        <th class="fix-width-80 middle">Base Ratio</th>
        <th class="fix-width-80 middle">Planned Qty</th>
        <th class="fix-width-80 middle">Issued</th>
        <th class="fix-width-80 middle">Available</th>
        <th class="fix-width-80 middle">Uom</th>
        <th class="fix-width-100 middle">Warehouse</th>
        <th class="fix-width-100 middle">Issue Method</th>
      </tr>
    </thead>
    <tbody id="details-table">
      <?php $no = 1; ?>
      <?php if( ! empty($details)) : ?>
        <?php foreach($details as $rs) : ?>
          <?php $uid = $rs->uid; ?>
          <?php $available = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode); ?>
          <tr id="row-<?php echo $uid; ?>" data-uid="<?php echo $uid; ?>" class="font-size-11">
            <td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
            <td class="middle">
              <select class="form-control input-xs text-label" id="type-<?php echo $uid; ?>">
                <option value="4" <?php echo is_selected('4', strval($rs->ItemType)); ?>>Item</option>
              </select>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label item-code r" data-uid="<?php echo $uid; ?>" id="item-code-<?php echo $uid; ?>" value="<?php echo $rs->ItemCode; ?>" />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label item-name" data-uid="<?php echo $uid; ?>" id="item-name-<?php echo $uid; ?>" value="<?php echo $rs->ItemName; ?>" />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label text-right base-qty r" data-uid="<?php echo $uid; ?>" id="base-qty-<?php echo $uid; ?>" value="<?php echo round($rs->BaseQty, 2); ?>" onchange="recalQty('<?php echo $uid; ?>')" />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="base-ratio-<?php echo $uid; ?>" value="1"  disabled/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label text-right planned-qty r" data-uid="<?php echo $uid; ?>" id="planned-qty-<?php echo $uid; ?>" value="<?php echo round($rs->PlannedQty, 2); ?>"  readonly/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="issued-<?php echo $uid; ?>" value="0" disabled />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="available-<?php echo $uid; ?>" value="<?php echo round($available, 2); ?>" disabled />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-xs text-label"
              data-uid="<?php echo $uid; ?>"
              id="uom-<?php echo $uid; ?>"
              data-uomentry="<?php echo $rs->UomEntry; ?>"
              data-uomcode="<?php echo $rs->UomCode; ?>"
              value="<?php echo $rs->Uom; ?>"  disabled/>
            </td>
            <td class="middle">
              <div class="width-100 wh">
                <input type="text" class="form-control input-xs text-label wh-input r" data-uid="<?php echo $uid; ?>" id="warehouse-<?php echo $uid; ?>" value="<?php echo $rs->WhsCode; ?>" />
              </div>
            </td>
            <td class="middle">
              <select class="form-control input-xs text-label" data-uid="<?php echo $uid; ?>" id="issue-type-<?php echo $uid; ?>">
                <option value="M" <?php echo is_selected('M', $rs->IssueType); ?>>Manual</option>
                <option value="B" <?php echo is_selected('B', $rs->IssueType); ?>>Backflush</option>
              </select>
            </td>
          </tr>
          <?php $no++; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <input type="hidden" id="row-no" value="<?php echo ($no - 1); ?>" />
</div>

<script id="details-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
      <td class="middle text-center fix-no no" scope="row">{{no}}</td>
      <td class="middle">
        <select class="form-control input-xs text-label" id="type-{{uid}}">
          <option value="4" {{type_item}}>Item</option>
        </select>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label item-code r" data-uid="{{uid}}" id="item-code-{{uid}}" value="{{Code}}"  />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label item-name" data-uid="{{uid}}" id="item-name-{{uid}}" value="{{Name}}" />
      </td>

      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right base-qty r" data-uid="{{uid}}" id="base-qty-{{uid}}" value="{{Quantity}}" onchange="recalQty('{{uid}}')" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right" data-uid="{{uid}}" id="base-ratio-{{uid}}" value="{{Ratio}}"  disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right planned-qty r" data-uid="{{uid}}" id="planned-qty-{{uid}}" value="{{PlannedQty}}"  readonly/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right" data-uid="{{uid}}" id="issued-{{uid}}" value="{{Issued}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right" data-uid="{{uid}}" id="available-{{uid}}" value="{{Available}}" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label" id="uom-{{uid}}"
          data-uid="{{uid}}"  data-uomentry="{{UomEntry}}" data-uomcode="{{UomCode}}"
          value="{{Uom}}"  disabled/>
      </td>
      <td class="middle">
      <div class="width-100 wh">
        <input type="text" class="form-control input-xs text-label wh-input r" data-uid="{{uid}}>" id="warehouse-{{uid}}" value="{{Warehouse}}" />
      </div>
      </td>
      <td class="middle">
        <select class="form-control input-xs text-label" data-uid="{{uid}}" id="issue-type-{{uid}}">
          <option value="M" {{issue_m}}>Manual</option>
          <option value="B" {{issue_b}}>Backflush</option>
        </select>
      </td>
    </tr>
  {{/each}}
</script>


<script id="new-row-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
      <td class="middle text-center fix-no no" scope="row">{{no}}</td>
      <td class="middle">
        <select class="form-control input-xs text-label" id="type-{{uid}}">
          <option value="4" {{type_item}}>Item</option>
        </select>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label item-code r" data-uid="{{uid}}" id="item-code-{{uid}}" value=""  />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label item-name" data-uid="{{uid}}" id="item-name-{{uid}}" value="" />
      </td>

      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right base-qty r" data-uid="{{uid}}" id="base-qty-{{uid}}" value=""  onchange="recalQty('{{uid}}')"/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right" data-uid="{{uid}}" id="base-ratio-{{uid}}" value=""  disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right planned-qty r" data-uid="{{uid}}" id="planned-qty-{{uid}}" value=""  />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right" data-uid="{{uid}}" id="issued-{{uid}}" value="" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right" data-uid="{{uid}}" id="available-{{uid}}" value="" disabled />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label" id="uom-{{uid}}"
          data-uid="{{uid}}"  data-uomentry="" data-uomcode=""
          value=""  disabled/>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label wh-input r" data-uid="{{uid}}>" id="warehouse-{{uid}}" value="{{Warehouse}}" />
      </td>
      <td class="middle">
        <select class="form-control input-xs text-label" data-uid="{{uid}}" id="issue-type-{{uid}}">
          <option value="M" {{issue_m}}>Manual</option>
          <option value="B" {{issue_b}}>Backflush</option>
        </select>
      </td>
    </tr>
  {{/each}}
</script>
