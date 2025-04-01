<div class="pg-footer visible-xs">
  <div class="pg-footer-inner">
    <div class="pg-footer-content text-right">
      <div class="footer-menu width-20">
        <span class="width-100" onclick="refresh()">
          <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
        </span>
      </div>

      <div class="footer-menu width-20">
        <span class="width-100" onclick="changeZone()">
          <i class="fa fa-repeat fa-2x white"></i><span class="fon-size-12">เปลี่ยนโซน</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="toggleCompleteBox()">
          <i class="fa fa-check-square fa-2x white"></i><span class="fon-size-12">ครบแล้ว</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="toggleTransBox()">
          <i class="fa fa-history fa-2x white"></i><span class="fon-size-12">Transection</span>
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
  <div class="footer-menu width-30">
    <span class="width-100" onclick="clearCache()">
      <i class="fa fa-bolt fa-2x white"></i><span class="fon-size-12">Clear cache</span>
    </span>
  </div>
  <div class="footer-menu width-40">
    <span class="width-100" onclick="confirmClose()">
      <i class="fa fa-exclamation-triangle fa-2x white"></i><span class="fon-size-12">Force Close</span>
    </span>
  </div>
  <div class="footer-menu width-30">
    <span class="width-100" onclick="toggleHeader()">
      <i class="fa fa-file-text-o fa-2x white"></i><span class="fon-size-12">ห้วเอกสาร</span>
    </span>
  </div>
</div>
