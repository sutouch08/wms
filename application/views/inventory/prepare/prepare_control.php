<div class="row">
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="width-100 focus" id="barcode-zone" autocomplete="off" 
    value="<?php echo empty($order->zone) ? '' : $order->zone->code; ?>" <?php echo empty($order->zone) ? 'autofocus' : ''; ?> />
  </div>
  <div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-5 padding-5">
    <label class="not-show">ชื่อโซน</label>
    <input type="text" class="width-100" id="zone-name" value="<?php echo empty($order->zone) ? '' : $order->zone->name; ?>" readonly />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-3 padding-5">
    <label class="not-show">change</label>
    <button type="button" class="btn btn-xs btn-info btn-block" style="height:31px;" id="btn-change-zone" onclick="changeZone()"><i class="fa fa-refresh"></i></i></button>
  </div>
  <div class="divider-hidden visible-xs"></div>
  <div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center focus" id="qty" value="1" />
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm focus" id="barcode-item" autocomplete="off" <?php echo empty($order->zone) ? '' : 'autofocus'; ?> />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-default btn-block" id="btn-submit" onclick="doPrepare()">ตกลง</button>
  </div>
</div>
