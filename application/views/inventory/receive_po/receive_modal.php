<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" data-backdrop="false" aria-labelledby="myModalLabel">
  <div class="modal-dialog" style="width:1000px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header" id="po-grid-header" style="border-bottom:solid 1px  #e5e5e5">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title text-center" id="po-title">ใบสั่งซื้อ</h4>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered tableFixHead" style="font-size:11px; table-layout: fixed; min-width:740px;">
          <thead>
            <tr class="font-size-11">
              <th class="fix-width-40 text-center">#</th>
              <th class="fix-width-200 text-center">รหัส</th>
              <th class="min-width-250 text-center">สินค้า</th>
              <th class="fix-width-100 text-center">ราคาหลังส่วนลด</th>
              <th class="fix-width-100 text-center">ค้างรับ</th>
              <th class="fix-width-100 text-center">จำนวน</th>
            </tr>
          </thead>
          <tbody id="po-body">
            <tr>
              <td colspan="6" class="text-center">--- ไม่พบข้อมูล ---</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-yellow top-btn" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
        <button type="button" class="btn btn-xs btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-xs btn-primary top-btn" onclick="addPoItems()">เพิ่มในรายการ</button>
        <button type="button" class="btn btn-xs btn-default top-btn" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
        <h4 class='modal-title-site text-center'> ผู้มีอำนาจอนุมัติรับสินค้าเกิน </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()">อนุมัติ</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:600px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Import File</h4>
      </div>
      <div class="modal-body">
        <form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="input-group width-100">
                <input type="text" class="form-control" id="show-file-name" placeholder="กรุณาเลือกไฟล์ Excel" readonly />
                <span class="input-group-btn">
                  <button type="button" class="btn btn-white btn-default" onclick="getFile()">เลือกไฟล์</button>
                </span>
              </div>
            </div>
          </div>
          <input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default btn-100" onclick="closeModal('upload-modal')">ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-primary btn-100" onclick="uploadfile()">นำเข้า</button>
      </div>
    </div>
  </div>
</div>


<script id="receive-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}">
      <td class="middle text-center">
        <label>
          <input type="checkbox" class="ace chk" value="{{uid}}" />
          <span class="lbl"></span>
        </label>
      </td>
      <td class="middle text-center no">{{no}}</td>
      <td class="middle">{{pdCode}}</td>
      <td class="middle">{{pdName}}</td>
      <td class="middle text-center">{{vatCode}}</td>
      <td class="middle text-right">{{PriceBefDiLabel}}</td>
      <td class="middle text-right">{{DiscPrcnt}}</td>
      <td class="middle text-right">{{PriceAfDiscLabel}}</td>
      <td class="middle text-right">{{backlogsLabel}}</td>
      <td class="middle">
        <input type="text"
          class="form-control input-xs text-right receive-qty"
          id="receive-qty-{{uid}}"
          data-uid="{{uid}}"
          data-limit="{{limit}}"
          data-backlogs="{{backlogs}}"
          data-bprice="{{PriceBefDi}}"
          data-aprice="{{PriceAfVAT}}"
          data-discprcnt="{{DiscPrcnt}}"
          data-price="{{Price}}"
          data-baseentry="{{baseEntry}}"
          data-baseline="{{baseLine}}"
          data-code="{{pdCode}}"
          data-name="{{pdName}}"
          data-vatcode="{{vatCode}}"
          data-vatrate="{{vatRate}}"
          data-vatamount="{{vatAmount}}"
          data-uomcode="{{UomCode}}"
          data-unitmsr="{{unitMsr}}"
          value="{{qty}}"
          onchange="recalAmount({{uid}})" />
      </td>
      <td class="middle">
        <input type="text" class="form-contorl input-xs text-right text-label"
          id="line-total-{{uid}}" data-amount="{{amount}}" value="{{amountLabel}}" readonly />
      </td>
    </tr>
  {{/each}}
</script>

<script id="po-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}">
      <td class="middle text-center no">{{no}}</td>
      <td class="middle">{{pdCode}}</td>
      <td class="middle">{{pdName}}</td>
      <td class="middle text-right">{{PriceAfDiscLabel}} <span style="font-size:10px;">{{Currency}}</span></td>
      <td class="middle text-center">{{backlogsLabel}}</td>
      <td class="middle">
        <input type="text"
          class="form-control input-xs text-center po-qty"
          id="po-qty-{{uid}}"
          data-uid="{{uid}}"
          data-code="{{pdCode}}"
          data-name="{{pdName}}"
          data-basecode="{{baseCode}}"
          data-baseentry="{{baseEntry}}"
          data-baseline="{{baseLine}}"
          data-limit="{{limit}}"
          data-backlogs="{{backlogs}}"
          data-qty="{{qty}}"
          data-bprice="{{PriceBefDi}}"
          data-aprice="{{PriceAfVAT}}"
          data-price="{{Price}}"
          data-discprcnt="{{DiscPrcnt}}"
          data-vatcode="{{vatGroup}}"
          data-vatrate="{{vatRate}}"
          data-uomcode="{{UomCode}}"
          data-unitmsr="{{unitMsr}}"
          data-no="{{no}}"
          value="" ondblclick="fillPoQty('{{uid}}')" />
      </td>
    </tr>
  {{/each}}
</script>