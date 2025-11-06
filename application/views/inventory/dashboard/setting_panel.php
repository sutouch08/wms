<div class="setting-panel move-out" id="setting-panel">
  <div class="nav-title">
    <a class="pull-left" onclick="closeSetting()"><i class="fa fa-times font-size-24"></i></a>
    <div class="font-size-24 text-center">ตั้งค่าการแสดงผล</div>
  </div>
  <div class="row">
    <div class="divider-hidden"></div>
    <div class="col-lg-8 col-md-8 col-sm-8">Refresh Rate</div>
    <div class="col-lg-4 col-md-4 col-sm-4">
      <select class="width-100" id="refresh-time-ms">
        <option value="60000"> 1 นาที</option>
        <option value="120000"> 2 นาที</option>
        <option value="300000"> 5 นาที</option>
        <option value="600000"> 10 นาที</option>
      </select>
    </div>
    <div class="divider-hidden"></div>
    <div class="col-lg-12 col-md-12 col-sm-12 font-size-24 text-center">Channels</div>
    <div class="divider" style="margin-top:5px;"></div>
    <div class="col-lg-8 col-md-8 col-sm-8">OFFLINE</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="channels-offline" value="1" id="channels-offline" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-8">ONLINE</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="channels-online" value="1" id="channels-online" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-8">TIKTOK</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="channels-tiktok" value="1" id="channels-tiktok" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-8">SHOPEE</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="channels-shopee" value="1" id="channels-shopee" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-8">LAZADA</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="channels-lazada" value="1" id="channels-lazada" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>

    <div class="divider-hidden"></div>
    <div class="col-lg-12 col-md-12 col-sm-12 font-size-24 text-center">State</div>
    <div class="divider" style="margin-top:5px;"></div>
    <div class="col-lg-8 col-md-8 col-sm-8">Back order</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-0" value="1" id="state-0" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">รอจัด</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-3" value="1" id="state-3" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">กำลังจัด</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-4" value="1" id="state-4" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">รอตรวจ</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-5" value="1" id="state-5" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">กำลังตรวจ</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-6" value="1" id="state-6" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">รอส่ง</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-7" value="1" id="state-7" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">จัดส่งแล้ว</div>
    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
      <label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" data-name="state-8" value="1" id="state-8" onchange="toggleOption($(this))" checked/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
    </div>

    <div class="divider-hidden"></div>
    <div class="divider-hidden"></div>
    <div class="divider-hidden"></div>
    <div class="col-lg-12 col-md-12 col-sm-12 font-size-24 text-center">
      <button type="button" class="btn btn-success btn-block" onclick="saveSetting()">SAVE</button>
    </div>
  </div>

  <input type="hidden" name="channels-offline" id="setting-offline" value="1" />
  <input type="hidden" name="channels-online" id="setting-online" value="1" />
  <input type="hidden" name="channels-tiktok" id="setting-tiktok" value="1" />
  <input type="hidden" name="channels-shopee" id="setting-shopee" value="1" />
  <input type="hidden" name="channels-lazada" id="setting-lazada" value="1" />
  <input type="hidden" name="state-0" id="setting-state-0" value="1" />
  <input type="hidden" name="state-3" id="setting-state-3" value="1" />
  <input type="hidden" name="state-4" id="setting-state-4" value="1" />
  <input type="hidden" name="state-5" id="setting-state-5" value="1" />
  <input type="hidden" name="state-6" id="setting-state-6" value="1" />
  <input type="hidden" name="state-7" id="setting-state-7" value="1" />
  <input type="hidden" name="state-8" id="setting-state-8" value="1" />

</div>
