<div id="details-tab" class="tab-pane fade active in" style="height:400px; overflow:auto;">
  <table class="table table-striped tableFixHead" style="min-width:670px;">
    <thead>
      <tr>
        <th class="fix-width-40 text-center fix-header">#</th>
        <th class="fix-width-150 fix-header">เลขที่</th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="min-width-200 fix-header">สินค้า</th>
        <th class="fix-width-80 fix-header">จำนวน</th>
      </tr>
    </thead>
    <tbody>
  <?php if( ! empty($details)) : ?>
    <?php $no = 1; ?>
    <?php $sumDetails = 0; ?>
    <?php foreach($details as $rs) : ?>
      <tr>
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->order_code; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center pick-detail"><?php echo number($rs->qty); ?></td>
      </tr>
      <?php $no++; ?>
      <?php $sumDetails += $rs->qty; ?>
    <?php endforeach; ?>
    <tr>
      <td colspan="4" class="text-right">Total</td>
      <td class="text-center"><?php echo number($sumDetails); ?></td>
    </tr>
  <?php endif; ?>
    </tbody>
  </table>
</div>
