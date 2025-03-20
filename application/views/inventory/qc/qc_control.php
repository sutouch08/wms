<!--  Control -->
<div class="row">
  <div class="col-lg-10 col-md-9-harf col-sm-9 col-xs-12 padding-0">
    <div class="col-lg-1-harf col-md-3 col-sm-1-harf col-xs-4 padding-5">
      <label class="display-block not-show">box</label>
      <button type="button" class="btn btn-xs btn-info btn-block" id="btn-add-box" onclick="confirmSaveBeforeAddBox()" <?php echo $disActive; ?>><i class="fa fa-plus"></i> เพิ่มกล่อง</button>
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
      <label>จำนวน</label>
      <input type="number" class="form-control input-sm text-center" id="qc-qty" value="1" <?php echo $disActive; ?> <?php echo $allow_input_qty ? "" : "disabled"; ?>/>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
      <label>บาร์โค้ดสินค้า</label>
      <input type="text" class="form-control input-sm text-center item" id="barcode-item" disabled <?php echo $disActive; ?>/>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">submit</label>
      <button type="button" class="btn btn-xs btn-default btn-block item" id="btn-submit" onclick="qcProduct()" <?php echo $disActive; ?>>ตกลง</button>
    </div>

    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
      <label class="display-block not-show">submit</label>
      <button type="button" class="btn btn-xs btn-success btn-block item" onclick="saveQc(0)" <?php echo $disActive; ?>>
        <i class="fa fa-save"></i> บันทึก
      </button>
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-3 padding-5">
      <label class="display-block not-show">print</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-print-address" onclick="printAddress(<?php echo $order->id_address; ?>, '<?php echo $order->code; ?>', <?php echo $order->id_sender; ?>)">พิมพ์ใบปะหน้า</button>
    </div>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-12 padding-5">
    <div class="title middle text-center" style="height:55px; background-color:black; color:white; padding-top:20px; margin-top:0px;">
      <h4 id="all_qty" style="display:inline;">
        <?php echo number($qc_qty); ?>
      </h4>
      <h4 style="display:inline;"> / <?php echo number($all_qty); ?></h4>
    </div>
  </div>
</div>

<input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />

<hr/>
<!--  Control -->
