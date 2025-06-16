<div class="pg-footer visible-xs">
  <div class="pg-footer-inner">
    <div class="pg-footer-content text-right">
      <div class="footer-menu width-15">
        <span class="width-100" onclick="changeZone()">
          <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-11">เปลี่ยนโซน</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="showMoveTable('L')">
          <i class="fa fa-check-square fa-2x white"></i><span class="fon-size-11">รายการโอน</span>
        </span>
      </div>
      <div class="footer-menu width-15">
        <span class="width-100" onclick="showMoveTable('Z')">
          <i class="fa fa-upload fa-2x white"></i><span class="fon-size-11">ย้ายออก</span>
        </span>
      </div>
      <div class="footer-menu width-15">
        <span class="width-100" onclick="showMoveTable('T')">
          <i class="fa fa-download fa-2x white"></i><span class="fon-size-11">ย้ายเข้า</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="showMoveTable('I')">
          <i class="fa fa-search fa-2x white"></i><span class="fon-size-11">Find Item</span>
        </span>
      </div>
      <div class="footer-menu width-15">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleExtraMenu()">
          <i class="fa fa-bars fa-2x white"></i><span class="fon-size-11">เพิ่มเติม</span>
        </button>
      </div>

    </div>
  </div>
</div>

<div class="extra-menu slide-out" id="extra-menu">
  <div class="footer-menu width-20">
    <span class="width-100" onclick="refresh()">
      <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-11">Refresh</span>
    </span>
  </div>
  <div class="footer-menu width-20">
    <span class="width-100" onclick="toggleHeader()">
      <i class="fa fa-file-text-o fa-2x white"></i><span class="fon-size-11">Header</span>
    </span>
  </div>
  <div class="footer-menu width-20">
    <span class="width-100" onclick="save()">
      <i class="fa fa-save fa-2x white"></i><span class="fon-size-11">SAVE</span>
    </span>
  </div>  
</div>
