<div class="setting-panel move-out" id="setting-panel">
  <div class="form-horizontal">
    <div class="nav-title">
      <a class="pull-left" onclick="closeSetting()"><i class="fa fa-times font-size-24"></i></a>
      <div class="font-size-24 text-center">ตั้งค่าการแสดงผล</div>
    </div>
    <div class="form-group margin-top-20">
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <span class="form-control left-label">Refresh Rate</span>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 padding-5">
        <select class="width-100" id="refresh-rate-setting" onchange="updateRefreshRate()">
          <option value="30000">30 วินาที</option>
          <option value="60000"> 1 นาที</option>
          <option value="120000"> 2 นาที</option>
          <option value="300000"> 5 นาที</option>
        </select>
      </div>    
    </div>
    <div class="form-group">
      <div class="col-xs-6 padding-5">
        <label>วันที่</label>
        <input type="text" class="width-100 text-center" value="" readonly/>
      </div>
      <div class="col-xs-6 padding-5">
        <label>Owner</label>
        <input type="text" class="width-100" value="" readonly/>
      </div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>คลังสินค้า</label>
        <input type="text" class="width-100" value="" readonly/>
      </div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>หมายเหตุ</label>
        <textarea class="width-100" readonly></textarea>
      </div>
    </div>
  </div><!-- end from-horizontal -->

</div>
