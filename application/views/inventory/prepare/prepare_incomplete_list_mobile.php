<div class="col-xs-12 padding-5 incomplete-box" id="incomplete-box">
  <?php  if(!empty($uncomplete_details)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($uncomplete_details as $rs) : ?>
      <div class="col-xs-12 incomplete-item" id="incomplete-<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap b-click " id="b-click-<?php echo $rs->id; ?>"><?php echo $rs->barcode; ?></div>
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span></div>
            <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></span></div>
            <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty - $rs->prepared); ?></span></div>
          </div>
          <div class="divider margin-top-10 margin-bottom-10"></div>
          <span class="stock-reload"  onclick="reloadStockInZone(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>','<?php echo $order->warehouse_code; ?>')"><i class="fa fa-refresh"></i></span>
          <div class="margin-bottom-3 pre-wrap" id="stock-<?php echo $rs->id; ?>">Location : <?php echo $rs->stock_in_zone; ?></div>
        </div>
        <span class="badge-qty" id="badge-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty - $rs->prepared); ?></span>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>

    <div id="close-bar" class="text-center <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success" onclick="finishPrepare()">จัดเสร็จแล้ว</button>
    </div>

  <?php else : ?>
    <div class="text-center" id="close-bar">
      <button type="button" class="btn btn-lg btn-success" onclick="finishPrepare()">จัดเสร็จแล้ว</button>
    </div>
  <?php endif; ?>
</div>
