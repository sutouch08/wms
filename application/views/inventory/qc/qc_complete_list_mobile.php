<div class="padding-5 complete-box move-out" id="complete-box">
  <div class="box-item" style="height:45px;">
    <a class="pull-left margin-left-10" onclick="closeComplete()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
  </div>
  <?php  if( ! empty($complete_details)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($complete_details as $rs) : ?>
      <div class="col-xs-12 complete-item" id="complete-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->barcode; ?></div>
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-<?php echo $rs->id; ?>"><?php echo number($rs->order_qty); ?></span></div>
            <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></span></div>
            <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="qc-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qc); ?></span></div>
          </div>
          <div class="margin-bottom-3 pre-wrap">Location : <?php echo $rs->from_zone; ?></div>
        </div>
          <button type="button" class="btn btn-xs btn-warning must-edit"
          style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
          onclick="showEditOption('<?php echo $order->code; ?>', '<?php echo $rs->product_code; ?>')">
            <i class="fa fa-pencil"></i></button>
      <input type="hidden" id="id-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>" />
    </div>
    <?php $no++; ?>
  <?php endforeach; ?>
<?php endif; ?>
</div>
