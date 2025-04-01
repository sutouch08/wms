<div class="width-100 header-info hide-text">
  <div class="col-xs-12 font-size-24 text-center" style="padding:4px;">
    <span id="total-picked"><?php echo number($totalPickQty); ?></span>
    &nbsp;/&nbsp;
    <span id="total-release"><?php echo number($totalReleaseQty); ?></span>
  </div>
</div>

<div class="control-box" id="control-box">
  <?php $showKeyboard = get_cookie('showKeyboard'); ?>
  <?php $inputmode = $showKeyboard ? 'text' : 'none'; ?>
  <?php $keyboard = $showKeyboard ? '' : 'hide'; ?>
  <?php $qr = $showKeyboard ? 'hide' : ''; ?>

  <div class="">
    <div class="width-100 e-zone" id="zone-bc">
      <span class="width-100">
        <input type="text" class="form-control input-lg focus"
        style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="<?php echo $inputmode; ?>" placeholder="Barcode Zone" autocomplete="off">
        <i class="ace-icon fa fa-keyboard-o fa-2x <?php echo $keyboard; ?>" style="position:absolute; top:15px; right:22px; color:grey;" id="zone-keyboard" onclick="hideKeyboard('zone')"></i>
        <i class="ace-icon fa fa-qrcode fa-2x <?php echo $qr; ?>" style="position:absolute; top:15px; right:22px; color:grey;" id="zone-qr" onclick="showKeyboard('zone')"></i>
      </span>
    </div>
    <div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="item-qty">
      <button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="width-100 e-item hide" id="item-bc">
      <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="<?php echo $inputmode; ?>"  placeholder="Barcode Item" autocomplete="off">
      <i class="ace-icon fa fa-keyboard-o fa-2x <?php echo $keyboard; ?>" style="position:absolute; top:72px; right:22px; color:grey;" onclick="hideKeyboard('item')"></i>
      <i class="ace-icon fa fa-qr fa-2x <?php echo $qr; ?>" style="position:absolute; top:72px; right:22px; color:grey;" onclick="showKeyboard('item')"></i>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text" id="zone-name">กรุณาระบุโซน</div>
