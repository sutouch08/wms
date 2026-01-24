<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
  <table class="table table-bordered tableFixHead" style="min-width:1205px; margin-bottom:20px;">
    <thead>
      <tr class="font-size-11">
        <th class="fix-width-25 middle text-center fix-no fix-header">#</th>
        <th class="fix-width-50 middle">Type</th>
        <th class="fix-width-200 middle">Item Code</th>
        <th class="min-width-250 middle">Item Description.</th>
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

    </tbody>
  </table>
</div>
<div class="divider-hidden"></div>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <button type="button" class="btn btn-white btn-xs btn-info" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-white btn-xs btn-danger" onclick="removeRows()">Delete Row</button>
  </div>  
</div>

<script id="details-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
      <td class="middle text-center pointer fix-no no" onclick="toggleChecked('{{uid}}')" scope="row"></td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-center" value="Item" readonly />
        <input type="hidden" id="type-{{uid}}" value="4" data-uid="{{uid}}" />
        <input type="checkbox" class="chk hide" id="row-chk-{{uid}}" data-uid="{{uid}}" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label item-code r" data-uid="{{uid}}" id="item-code-{{uid}}" value="{{Code}}" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label item-name" data-uid="{{uid}}" id="item-name-{{uid}}" value="{{Name}}" />
      </td>

      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-right base-qty r" data-uid="{{uid}}" id="base-qty-{{uid}}" value="{{Quantity}}" onchange="recalQty('{{uid}}')" readonly/>
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
        <input type="text" class="form-control input-xs text-label wh-input r" data-uid="{{uid}}" id="warehouse-{{uid}}" value="{{Warehouse}}" />
      </div>
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-center" value="Manual" readonly />
        <input type="hidden" data-uid="{{uid}}" id="issue-type-{{uid}}" value="M" />
      </td>
    </tr>
  {{/each}}
</script>


<script id="new-row-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}" data-uid="{{uid}}" class="font-size-11">
      <td class="middle text-center pointer fix-no no" onclick="toggleChecked('{{uid}}')" scope="row"></td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-center" value="Item" readonly />
        <input type="hidden" id="type-{{uid}}" value="4" data-uid="{{uid}}" />
        <input type="checkbox" class="chk hide" id="row-chk-{{uid}}" data-uid="{{uid}}" />
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
        <input type="text" class="form-control input-xs text-label wh-input r" data-uid="{{uid}}" id="warehouse-{{uid}}" value="{{Warehouse}}" />
      </td>
      <td class="middle">
        <input type="text" class="form-control input-xs text-label text-center" value="Manual" readonly />
        <input type="hidden" data-uid="{{uid}}" id="issue-type-{{uid}}" value="M" />
      </td>
    </tr>
  {{/each}}
</script>
