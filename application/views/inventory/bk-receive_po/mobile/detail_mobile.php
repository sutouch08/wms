<div class="counter text-center">
  <input type="text" class="width-40 counter-input text-right" id="all-qty" value="<?php echo number($totalReceive); ?>" readonly/>
  /
  <input type="text" class="width-40 counter-input text-left" value="<?php echo number($allQty); ?>" readonly/>
</div>

<?php $this->load->view('inventory/receive_po/mobile/incomplete_list_mobile'); ?>
<?php $this->load->view('inventory/receive_po/mobile/complete_list_mobile');?>

<div id="control-box">
  <div class="">
    <div class="width-100 padding-right-5 margin-bottom-10 text-center e-item" id="item-qty">
      <button type="button" class="btn btn-default btn-qty" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="width-30 input-lg focus text-center" id="qty" inputmode="numeric" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default btn-qty" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="width-100 e-item" id="item-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none" autocomplete="off" placeholder="Barcode Item">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;"></i>
      </div>
    </div>
  </div>
</div>
