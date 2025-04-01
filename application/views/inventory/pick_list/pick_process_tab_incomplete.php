<div id="incomplete-tab" class="tab-pane fade active in" style="height:350px; overflow:auto;">
  <table class="table table-striped tableFixHead" style="min-width:1130px;">
    <thead>
      <tr>
        <th class="fix-width-40 text-center fix-header">
          <button type="button" class="btn btn-minier btn-info" onclick="reloadInComplete()"><i class="fa fa-refresh"></i></button>
        </th>
        <th class="fix-width-100 fix-header">บาร์โค้ด</th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="fix-width-250 fix-header">สินค้า</th>
        <th class="fix-width-100 text-center fix-header">Rel Qty</th>
        <th class="fix-width-100 text-center fix-header">จัดแล้ว</th>
        <th class="fix-width-100 text-center fix-header">คงเหลือ</th>
        <th class="fix-width-40 text-center fix-header">&nbsp;</th>
        <th class="min-width-200 fix-header">Stock Zone</th>
      </tr>
    </thead>
    <tbody id="incomplete-table">
  <?php if( ! empty($incomplete)) : ?>
    <?php $no = 1; ?>
    <?php foreach($incomplete as $rs) : ?>
      <tr class="font-size-11 incomplete" id="incomplete-<?php echo $rs->id; ?>">
        <td class="text-center i-no"><?php echo $no; ?></td>
        <td>
          <?php echo $rs->barcode; ?>
          <?php if( ! empty($rs->barcode)) : ?>
            <input type="hidden" id="<?php echo $rs->barcode; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-code="<?php echo $rs->product_code; ?>"
              data-name="<?php echo $rs->product_name; ?>"
              data-release="<?php echo $rs->release_qty; ?>" />
          <?php endif; ?>
        </td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center"><input type="number" class="print-row text-center" id="release-qty-<?php echo $rs->id; ?>" value="<?php echo $rs->release_qty; ?>" readonly /></td>
        <td class="text-center"><input type="number" class="print-row text-center picked-qty" id="pick-qty-<?php echo $rs->id; ?>" value="<?php echo $rs->pick_qty; ?>" readonly /></td>
        <td class="text-center"><input type="number" class="print-row text-center" id="balance-qty-<?php echo $rs->id; ?>" value="<?php echo $rs->balance; ?>" readonly /></td>
        <td class="text-center"><button type="button" class="btn btn-minier btn-info" onclick="reloadPickRow(<?php echo $rs->id; ?>)"><i class="fa fa-refresh"></i></td>
        <td class="scroll" id="stock-in-zone-<?php echo $rs->id; ?>"><?php echo $rs->stock_in_zone; ?></td>
      </tr>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
    </tbody>
  </table>
</div>

<script id="incomplete-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      <tr class="font-size-11">
        <td colspan="9" class="text-center">--- no items ---</td>
      </tr>
    {{else}}
      <tr class="font-size-11 incomplete" id="incomplete-{{id}}">
        <td class="text-center i-no">{{no}}</td>
        <td>
          {{barcode}}
          {{#if barcode}}
            <input type="hidden" id="{{barcode}}"
            data-id="{{id}}"
            data-code="{{product_code}}"
            data-name="{{product_name}}"
            data-release="{{release_qty}}" />
          {{/if}}
        </td>
        <td>{{product_code}}</td>
        <td class="hide-text">{{product_name}}</td>
        <td class="text-center"><input type="number" class="print-row text-center" id="release-qty-{{id}}" value="{{release_qty}}" readonly /></td>
        <td class="text-center"><input type="number" class="print-row text-center picked-qty" id="pick-qty-{{id}}" value="{{pick_qty}}" readonly /></td>
        <td class="text-center"><input type="number" class="print-row text-center" id="balance-qty-{{id}}" value="{{balance}}" readonly /></td>
        <td class="text-center"><button type="button" class="btn btn-minier btn-info" onclick="reloadPickRow({{id}})"><i class="fa fa-refresh"></i></td>
        <td class="scroll" id="stock-in-zone-{{id}}">{{{stock_in_zone}}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>

<script id="complete-row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11 incomplete" id="complete-{{id}}">
    <td class="text-center c-no"></td>
    <td>{{product_code}}</td>
    <td class="hide-text">{{product_name}}</td>
    <td class="text-center"><input type="number" class="print-row text-center" value="{{releaseQty}}" readonly /></td>
    <td class="text-center"><input type="number" class="print-row text-center picked-qty" value="{{pickQtty}}" readonly /></td>
  </tr>
</script>
