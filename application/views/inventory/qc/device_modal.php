<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:90vw;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: 1px solid #e5e5e5;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เชื่อมต่อเครื่องชั่งใหม่</h4>
        <input type="hidden" id="device-id" value="" />
        <input type="hidden" id="device-uid" value="" />
        <input type="hidden" id="action" value="" />
      </div>
      <div class="modal-body">
        <div class="row" style="margin-left:0; margin-right:0;">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
            <label class="label-top">รหัสเครื่องชั่ง</label>
            <input type="text" class="form-control input-sm r" maxlength="20" id="device-code" value="" />
          </div>
          <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
            <label class="label-top">ชื่อเครื่องชั่ง</label>
            <input type="text" class="form-control input-sm r" maxlength="100" id="device-name" value="" />
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <label class="fix-width-100">
              <input type="radio" class="ace" name="active" value="1" checked>
              <span class="lbl"> Active</span>
            </label>
            <label class="fix-width-100">
              <input type="radio" class="ace" name="active" value="0">
              <span class="lbl"> Inactive</span>
            </label>
          </div>

          <div class="divider"></div>

          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5 hide">
            <label class="label-top">Port</label>
            <select class="form-control input-sm" id="device-port">
              <option value="RS-232" data-name="Serial">RS-232</option>
              <option value="USB" data-name="USB">USB</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Unit</label>
            <select class="form-control input-sm" id="device-unit">
              <option value="kg" data-name="กิโลกรัม">KG</option>
              <option value="g" data-name="กรัม">G</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Baud Rate</label>
            <select class="form-control input-sm" id="device-baud-rate">
              <option value="110">110</option>
              <option value="200">200</option>
              <option value="300">300</option>
              <option value="600">600</option>
              <option value="1200">1200</option>
              <option value="1800">1800</option>
              <option value="2400">2400</option>
              <option value="4800">4800</option>
              <option value="7200">7200</option>
              <option value="9600" selected>9600</option>
              <option value="1440">1440</option>
              <option value="1920">1920</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Data Bits</label>
            <select class="form-control input-sm" id="device-data-bits">
              <option value="7">7</option>
              <option value="8" selected>8</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Stop Bits</label>
            <select class="form-control input-sm" id="device-stop-bits">
              <option value="1" selected>1</option>
              <option value="2">2</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top">Parity</label>
            <select class="form-control input-sm" id="device-parity">
              <option value="none" selected>None</option>
              <option value="even">Even</option>
              <option value="odd">Odd</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
            <label class="label-top display-block not-show">submit</label>
            <button type="button" class="btn btn-xs btn-primary btn-block" id="open-close-btn" onclick="connect()">Connect</button>            
          </div>


          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <label class="label-top">Result Message</label>
            <textarea class="form-control text-center" id="result-message" rows="5" disabled></textarea>
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <label class="label-top">Weight</label>
            <input type="text" class="form-control text-center" id="weight" disabled />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-default btn-100" onclick="disconectPort('add-modal')">Close</button>
        <button type="button" class="btn btn-xs btn-danger btn-50 hide" id="del-btn" onclick="removeDevice()">Delete</button>
        <button type="button" class="btn btn-xs btn-success btn-100" id="save-btn" onclick="saveAndClose()">Save and Close</button>
      </div>
    </div>
  </div>
</div>

<style>
  .device-list {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;
    border: solid 1px #cccccc;
    /* background-color: #f5f5f5; */
    border-radius: 5px;
    padding: 10px;
  }

  .desc {
    float: left;
  }

  .btn-device {
    height: 39px;
    border-radius: 5px;
  }
</style>

<div class="modal fade" id="devices-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:600px; max-width:90vw;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: 1px solid #e5e5e5;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center">เชื่อมต่อเครื่องชั่ง</h4>
      </div>
      <div class="modal-body">
        <div class="row" id="device-table" style="margin-left:0; margin-right:0; max-height:400px; overflow:auto;">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 device-list">
            <div class="width-90">
              <span class="width-30 desc">Code : WE-001</span>
              <span class="width-70 desc">Name : เครื่องชั่ง 1</span>
              <span class="width-30 desc">Port : RS-232</span>
              <span class="width-30 desc">Baud Rate : 9600</span>
              <span class="width-30 desc">Unit : กิโลกรัม</span>
            </div>
            <div class="width-10" style="display:flex; justify-content:center; align-items:center;">
              <button type="button" class="btn btn-white btn-primary btn-block btn-device">เลือก</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script id="device-template" type="text/x-handlebars-template">
  {{#each this}}
    {{#if nodata}}
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
        <h4 class="red">ไม่พบข้อมูลเครื่องชั่ง</h4>
      </div>
    {{else}}
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 device-list">
        <div class="width-90">
          <span class="width-30 desc">Code : {{deviceCode}}</span>
          <span class="width-70 desc">Name : {{deviceName}}</span>
          <span class="width-30 desc">Port : {{devicePort}}</span>
          <span class="width-30 desc">Baud Rate : {{deviceBaudRate}}</span>
          <span class="width-30 desc">Unit : {{deviceUnit}}</span>
        </div>
        <div class="width-10" style="display:flex; justify-content:center; align-items:center;">
          <button type="button" class="btn btn-white btn-primary btn-block btn-device" onclick="selectDevice('{{id}}')">เลือก</button>
        </div>
        <input type="hidden"
          id="device-data-{{id}}" 
          value="{{deviceId}}"
          data-id="{{id}}"
          data-uid="{{deviceId}}"
          data-code="{{deviceCode}}"
          data-name="{{deviceName}}"
          data-unit="{{deviceUnit}}"
          data-baudrate="{{deviceBaudRate}}"
          data-databits="{{deviceDataBits}}"
          data-stopbits="{{deviceStopBits}}"
          data-parity="{{deviceParity}}"
          data-port="{{devicePort}}">
      </div>
    {{/if}}
  {{/each}}
</script>