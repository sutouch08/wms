<div class="row">
  <div class="col-lg-2 col-md-2-harf col-sm-4 col-xs-4 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="width-100" id="barcode-zone" autofocus />
  </div>
  <div class="col-lg-4 col-md-3 col-sm-6 col-xs-5 padding-5">
    <label class="not-show">ชื่อโซน</label>
    <input type="text" class="width-100" id="zone-name" value="" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="not-show">changeZone</label>
    <button type="button" class="btn btn-xs btn-info btn-block" style="height:31px;" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
  </div>
  <div class="divider-hidden visible-sm visible-xs"></div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty" value="1" disabled/>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm" id="barcode-item" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-default btn-block" id="btn-submit" onclick="doPrepare()" disabled>ตกลง</button>
  </div>
</div>
