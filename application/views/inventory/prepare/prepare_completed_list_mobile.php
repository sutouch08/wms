<div class="padding-5 complete-box move-out" id="complete-box">
  <div class="nav-title">
    <a class="pull-left margin-left-10" onclick="closeCompleteBox()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
  </div>
  <?php  if( ! empty($complete_details)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($complete_details as $rs) : ?>
      <div class="col-xs-12 complete-item c-<?php echo $no; ?>" id="complete-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->barcode; ?></div>
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span></div>
            <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></span></div>
            <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty - $rs->prepared); ?></span></div>
          </div>
          <div class="margin-bottom-3 pre-wrap">Location : <?php echo $rs->from_zone; ?></div>
        </div>
        <button type="button" class="btn btn-white btn-minier btn-danger"
          style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
          onclick="removeBuffer('<?php echo $order->code; ?>', '<?php echo $rs->product_code; ?>', '<?php echo $rs->id; ?>')">
        <i class="fa fa-times"></i>
      </button>
    </div>
    <?php $no++; ?>
  <?php endforeach; ?>
<?php endif; ?>
</div>
