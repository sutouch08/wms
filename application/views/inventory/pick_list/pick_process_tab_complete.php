<div id="complete-tab" class="tab-pane fade" style="height:350px; overflow:auto;">
  <table class="table table-striped tableFixHead">
    <thead>
      <tr>
        <th class="fix-width-40 text-center fix-header">
          <button type="button" class="btn btn-minier btn-info" onclick="reloadComplete()"><i class="fa fa-refresh"></i></button>
        </th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="min-width-200 fix-header">สินค้า</th>
        <th class="fix-width-100 text-center fix-header">Release Qty</th>
        <th class="fix-width-100 text-center fix-header">Pick Qty</th>
      </tr>
    </thead>
    <tbody id="complete-table">
  <?php if( ! empty($complete)) : ?>
    <?php $no = 1; ?>
    <?php foreach($complete as $rs) : ?>
      <tr class="font-size-11 complete" id="complete-<?php echo $rs->id; ?>">
        <td class="text-center c-no"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td><?php echo $rs->product_name; ?></td>
        <td class="text-center"><input type="number" class="print-row text-center" value="<?php echo $rs->release_qty; ?>" readonly /></td>
        <td class="text-center"><input type="number" class="print-row text-center picked-qty" value="<?php echo $rs->pick_qty; ?>" readonly /></td>
      </tr>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
    </tbody>
  </table>
</div>

<script id="complete-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      <tr class="font-size-11">
        <td colspan="5" class="text-center">--- no transections ---</td>
      </tr>
    {{else}}
    <tr class="font-size-11 incomplete" id="complete-{{id}}">
      <td class="text-center c-no">{{no}}</td>
      <td>{{product_code}}</td>
      <td class="hide-text">{{product_name}}</td>
      <td class="text-center"><input type="number" class="print-row text-center" value="{{releaseQty}}" readonly /></td>
      <td class="text-center"><input type="number" class="print-row text-center picked-qty" value="{{pickQtty}}" readonly /></td>
    </tr>
    {{/if}}
  {{/each}}
</script>
