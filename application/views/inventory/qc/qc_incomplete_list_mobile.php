<div class="incomplete-box" id="incomplete-box">
  <?php  if(!empty($uncomplete_details)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($uncomplete_details as $rs) : ?>
      <div class="col-xs-12 incomplete-item" id="incomplete-<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap b-click"><?php echo $rs->barcode; ?></div>
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <div class="width-33 float-left">Order : <span class="width-30"><?php echo number($rs->order_qty); ?></span></div>
            <div class="width-33 float-left">Picked : <span class="width-30" id="prepared-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></span></div>
            <div class="width-33 float-left">Packed : <span class="width-30" id="qc-<?php echo $rs->id; ?>"><?php echo number($rs->qc); ?></span></div>
          </div>
          <div class="margin-bottom-3 pre-wrap">Location : <?php echo $rs->from_zone; ?></div>
        </div>
        <span class="badge-qty" id="badge-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared - $rs->qc); ?></span>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>

    <div id="close-bar" class="text-center <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success" onclick="closeOrder()">แพ็คเสร็จแล้ว</button>
    </div>

  <?php else : ?>
    <div class="text-center" id="close-bar">
      <button type="button" class="btn btn-lg btn-success" onclick="finishPrepare()">แพ็คเสร็จแล้ว</button>
    </div>
  <?php endif; ?>
</div>
