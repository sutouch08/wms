<div class="pg-footer visible-xs">
  <div class="pg-footer-inner">
    <div class="pg-footer-content text-right">
      <div class="footer-menu width-20">
        <span class="width-100" onclick="changeZone()">
          <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">เปลี่ยนโซน</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="showMoveTable('L')">
          <i class="fa fa-check-square fa-2x white"></i><span class="fon-size-12">รายการโอน</span>
        </span>
      </div>

      <div class="footer-menu width-20">
        <span class="width-100" onclick="showMoveTable('B')">
          <i class="fa fa-cube fa-2x white"></i><span class="fon-size-12">เปิดกล่อง</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="showMoveTable('T')">
          <i class="fa fa-download fa-2x white"></i><span class="fon-size-12">ย้ายเข้า</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="toggleExtraMenu()">
          <i class="fa fa-bars fa-2x white"></i><span class="fon-size-12">เพิ่มเติม</span>
        </span>
      </div>

    </div>
  </div>
</div>

<div class="extra-menu slide-out" id="extra-menu">
  <div class="footer-menu width-25">
    <span class="width-100" onclick="refresh()">
      <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Reload</span>
    </span>
  </div>
  <div class="footer-menu width-25">
    <span class="width-100" onclick="toggleHeader()">
      <i class="fa fa-info fa-2x white"></i><span class="fon-size-12">ข้อมูลเอกสาร</span>
    </span>
  </div>
  <div class="footer-menu width-25">
    <span class="width-100" onclick="showMoveTable('Z')">
      <i class="fa fa-upload fa-2x white"></i><span class="fon-size-12">ย้ายออก</span>
    </span>
  </div>
  <div class="footer-menu width-25">
    <span class="width-100" onclick="saveBox()">
      <i class="fa fa-cube fa-2x white"></i><span class="fon-size-12">บันทึกกล่อง</span>
    </span>
  </div>
  <input type="hidden" id="extra" value="hide" />
</div>
