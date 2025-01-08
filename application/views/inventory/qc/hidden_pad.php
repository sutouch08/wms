
<?php $ref = empty($order->reference) ? "" : "&nbsp;&nbsp;&nbsp;[{$order->reference}]"; ?>
<div class="form-horizontal filter-pad move-out" id="header-pad">
  <div class="box-item" style="height:45px;">
    <a class="pull-left margin-left-10" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">หัวเอกสาร</div>
  </div>
  <div class="form-group margin-top-30">
    <div class="col-xs-12 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="width-100" value="<?php echo $order->code . $ref; ?> " readonly/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>ลูกค้า/ผู้เบิก/ผู้ยืม</label>
      <input type="text" class="width-100" value="<?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>" readonly/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>คลัง</label>
      <input type="text" class="width-100" value="<?php echo $order->warehouse_name; ?>" readonly/>
    </div>
  </div>

  <?php if($order->role == 'S') : ?>
    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>ช่องทาง</label>
        <input type="text" class="width-100" value="<?php echo $order->channels_name; ?>" readonly/>
      </div>
    </div>
  <?php endif; ?>

  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>วันที่</label>
      <input type="text" class="width-100" value="<?php echo thai_date($order->date_add); ?>" readonly/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <textarea class="form-control" rows="5" readonly><?php echo $order->remark; ?></textarea>
    </div>
  </div>
</div>


<!-- แสดงผลกล่อง  -->
<div class="box-list move-out" id="box-list">
  <div class="box-item" style="height:45px;">
    <a class="pull-left margin-left-10" onclick="closeBoxList()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">Box List</div>
  </div>
  <?php if( ! empty($box_list)) : ?>
    <?php foreach($box_list as $rs) : ?>
      <div class="box-item">
        <div class="row">
          <div class="col-xs-3 text-center" style="padding-right:0;">
            <a href="javascript:getSelectBox('<?php echo $rs->code; ?>')"><i class="fa fa-cube fa-3x"></i></a>
          </div>
          <div class="col-xs-7" style="padding-left:0;">
            <p class="box-line">กล่องที่ <?php echo $rs->box_no; ?>&nbsp;&nbsp;&nbsp;&nbsp; รหัส <?php echo $rs->code; ?></p>
            <p class="box-line">จำนวน : <span id="box-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span> pcs.</p>
          </div>
          <a class="box-link font-size-24"
            href="javascript:viewBoxItems(<?php echo $rs->id; ?>)"
            data-barcode="<?php echo $rs->code; ?>"
            data-no="<?php echo $rs->box_no; ?>" data-id="<?php echo $rs->id; ?>">
            <i class="fa fa-angle-right"></i>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="box-item">
      <div class="row">
        <div class="col-xs-12" style="padding-left:0;">
          <h4 class="text-center">ไม่พบกล่อง</h4>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>


<!-- แสดงผลรายการในกล่อง  -->
<div class="box-details move-out" id="box-details">

</div>


<div class="edit-box move-out" id="edit-box">
  <!-- จะมีการดึงรายการที่ qc ไปแล้วมาใส่ แล้วค่อยแสดงผล -->
</div>
