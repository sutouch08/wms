<div id="trans-tab" class="tab-pane fade" style="height:400px; overflow:auto;">
  <table class="table table-striped tableFixHead" style="min-width:1040px;">
    <thead>
      <tr>
        <th class="fix-width-40 text-center fix-header">#</th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="fix-width-250 fix-header">สินค้า</th>
        <th class="fix-width-100 fix-header">จำนวน</th>
        <th class="fix-width-150 fix-header">โซน</th>
        <th class="fix-width-150 fix-header">User</th>
        <th class="min-width-150 fix-header">เวลา</th>
      </tr>
    </thead>
    <tbody>
  <?php if( ! empty($trans)) : ?>
    <?php $no = 1; ?>
    <?php $totalTransQty = 0; ?>
    <?php foreach($trans as $rs) : ?>
      <tr>
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
        <td><?php echo $rs->zone_code; ?></td>
        <td><?php echo $rs->user; ?></td>
        <td><?php echo thai_date($rs->date_upd, TRUE); ?></td>
      </tr>
      <?php $no++; ?>
      <?php $totalTransQty += $rs->qty; ?>
    <?php endforeach; ?>
      <tr>
        <td colspan="3" class="text-right">Total</td>
        <td class="text-center"><?php echo number($totalTransQty); ?></td>
        <td colspan="3"></td>
      </tr>
  <?php endif; ?>
    </tbody>
  </table>
</div>
