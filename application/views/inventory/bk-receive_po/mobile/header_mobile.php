<div class="goback">
  <a class="goback-icon pull-left" onclick="leave()"><i class="fa fa-angle-left fa-2x"></i></a>
</div>
<div class="form-horizontal filter-pad move-out" id="header-pad">
  <div class="nav-title">
    <a class="pull-left" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">ข้อมูลเอกสาร</div>
  </div>
  <div class="form-group margin-top-20">
    <div class="col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?> " readonly/>
    </div>
    <div class="col-xs-6 padding-5">
      <label>วันที่เอกสาร</label>
      <input type="text" class="width-100 text-center h" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" readonly/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-6 padding-5">
      <label>Posting date</label>
  		<input type="text" class="width-100 text-center h" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" disabled/>
    </div>
    <div class="col-xs-6 padding-5">
      <label>ช่องทางการรับ</label>
      <input type="text" class="width-100" value="WARRIX" readonly/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-4 padding-5">
      <label>ผู้จำหน่าย</label>
  		<input type="text" class="width-100 text-center h" name="vendor_code" id="vendor_code" value="<?php echo $doc->vendor_code; ?>" placeholder="รหัสผู้จำหน่าย" disabled/>
    </div>
    <div class="col-xs-8 padding-5">
      <label class="not-show">vendor</label>
  		<input type="text" class="form-control input-sm h" name="vendorName" id="vendorName" value="<?php echo $doc->vendor_name; ?>" placeholder="ระบุผู้จำหน่าย" disabled/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-6 padding-5">
      <label>ใบสั่งซื้อ</label>
  		<input type="text" class="form-control input-sm text-center h" name="poCode" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" disabled/>
    </div>
    <div class="col-xs-6 padding-5">
      <label>ใบส่งสินค้า</label>
  		<input type="text" class="form-control input-sm text-center h" name="invoice" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" disabled/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>คลัง</label>
      <select class="form-control input-sm h" disabled>
        <option value="">เลือก</option>
        <?php echo select_warehouse($doc->warehouse_code); ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-4 padding-5">
      <label>โซนรับสินค้า</label>
  		<input type="text" class="form-control input-sm h" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo empty($zone) ? NULL : $zone->code; ?>" disabled/>
    </div>
    <div class="col-xs-8 padding-5">
      <label class="not-show">zone</label>
  		<input type="text" class="form-control input-sm zone h" name="zoneName" id="zoneName" placeholder="ชื่อโซน" value="<?php echo empty($zone) ? NULL : $zone->name; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <textarea class="width-100" readonly><?php echo $doc->remark; ?></textarea>
    </div>
  </div>
</div><!-- end from-horizontal -->
<hr class="margin-top-15 margin-bottom-15"/>
