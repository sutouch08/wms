<div class="incomplete-box" id="incomplete-box">
  <?php  if(!empty($uncomplete_details)) : ?>
    <?php   foreach($uncomplete_details as $rs) : ?>
      <?php   $id = md5($rs->barcode); ?>
      <div class="incomplete pack-item" id="row-<?php echo $id; ?>">
        <div class="item-content width-100">
          <span class="bc padding-right-15"><?php echo $rs->barcode; ?></span>
          <span class="padding-right-15"><?php echo $rs->product_code; ?></span>
          <span class="b-click padding-right-15"><?php echo $rs->product_name; ?></span>
        </div>
        <div class="width-100" style="display: table; font-size:14px;">
          <div class="fix-width-100 pull-left">Order : <span class="width-30 padding-left-10"><?php echo number($rs->order_qty); ?></span></div>
          <div class="fix-width-100 pull-left">Picked : <span class="width-30 padding-left-10" id="prepared-<?php echo $id; ?>"><?php echo number($rs->prepared); ?></span></div>
          <div class="fix-width-100 pull-left">Packed : <span class="width-30 padding-left-10" id="qc-<?php echo $id; ?>"><?php echo number($rs->qc); ?></span></div>
        </div>
        <input type="hidden" class="hidden-qc" id="<?php echo $id; ?>" data-code="<?php echo $rs->product_code; ?>" value="0"/>
        <input type="hidden" id="id-<?php echo $id; ?>" value="<?php echo $id; ?>" />
      </div>
    <?php endforeach; ?>

    <div id="close-bar" class="text-center <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success" onclick="closeOrder()">แพ็คเสร็จแล้ว</button>
    </div>

  <?php else : ?>
    <div class="text-center" id="close-bar">
      <button type="button" class="btn btn-lg btn-success" onclick="closeOrder()">แพ็คเสร็จแล้ว</button>
    </div>
  <?php endif; ?>
</div>
