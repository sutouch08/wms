<div id="header-tab" class="tab-pane fade" style="height:350px;">
  <div class="row" style="margin:0px;">
    <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>เลขที่</label>
      <input type="text" class="width-100 text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="width-100 text-center" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
    <div class="col-lg-3 col-md-2 col-sm-2 col-xs-6 padding-5 hidden-sm">
      <label>อ้างอิง</label>
      <input type="text" class="width-100 text-center" value="<?php echo $order->reference; ?>" disabled />
    </div>
    <div class="col-lg-2-harf col-md-2 col-sm-3 col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
      <input type="text" class="width-100" value="<?php echo $order->channels_name; ?>" disabled />
    </div>
    <div class="col-lg-3 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>Shop Name</label>
      <input type="text" class="width-100" value="<?php echo shop_name($order->shop_id); ?>" disabled />
    </div>
    <div class="col-lg-2 col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="width-100 text-center" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-10 col-md-5 col-sm-5-harf col-xs-6 padding-5">
      <label class="not-show">ลูกค้า</label>
      <input type="text" class="width-100" value="<?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>" disabled />
    </div>

    <div class="col-lg-6 col-md-5 col-sm-6 col-xs-6 padding-5">
      <label>คลัง</label>
      <input type="text" class="width-100" value="<?php echo $order->warehouse_code.' | '.warehouse_name($order->warehouse_code); ?>" disabled />
    </div>
    <div class="col-lg-6 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>ขนส่ง</label>
      <input type="text" class="width-100" value="<?php echo sender_name($order->id_sender); ?>" disabled />
    </div>
    <div class="col-lg-12 col-md-12 col-sm-9 col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="width-100" value="<?php echo $order->remark; ?>" disabled />
    </div>
  </div>
</div>
