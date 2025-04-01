<div id="rows-tab" class="tab-pane fade" style="height:400px; overflow:auto;">
  <table class="table table-striped tableFixHead">
    <thead>
      <tr>
        <th class="fix-width-40 text-center fix-header">#</th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="min-width-200 fix-header">สินค้า</th>
        <th class="fix-width-100 text-center fix-header">Release Qty</th>
        <th class="fix-width-100 text-center fix-header">Pick Qty</th>
      </tr>
    </thead>
    <tbody>
  <?php if( ! empty($rows)) : ?>
    <?php $no = 1; ?>
    <?php $sumReleaseQty = 0; ?>
    <?php $sumPickQty = 0; ?>
    <?php foreach($rows as $rs) : ?>
      <tr>
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->release_qty); ?></td>
        <td class="text-center"><?php echo number($rs->pick_qty); ?></td>
      </tr>
      <?php $no++; ?>
      <?php $sumReleaseQty += $rs->release_qty; ?>
      <?php $sumPickQty += $rs->pick_qty; ?>
    <?php endforeach; ?>
    <tr>
      <td colspan="3" class="text-right">Total</td>
      <td class="text-center"><?php echo number($sumReleaseQty); ?></td>
      <td class="text-center"><?php echo number($sumPickQty); ?></td>
    </tr>    
  <?php endif; ?>
    </tbody>
  </table>
</div>
