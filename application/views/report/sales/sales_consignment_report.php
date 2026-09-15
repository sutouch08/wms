<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
  <div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
      <button type="button" class="btn btn-xs btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="hidden-print" />
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5 ">
    <label class="display-block">สินค้า</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 padding-5">
    <label class="">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center r" id="pdFrom" name="pdFrom" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 padding-5">
    <label class="">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center r" id="pdTo" name="pdTo" disabled>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5 ">
    <label class="display-block">ลูกค้า</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-cus-all" onclick="toggleAllCustomer(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-cus-range" onclick="toggleAllCustomer(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 padding-5">
    <label class="">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center r" id="cusFrom" name="cusFrom" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 padding-5">
    <label class="">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center r" id="cusTo" name="cusTo" disabled>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5 ">
    <label class="display-block">คลัง</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5 ">
    <label class="display-block">โซน</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-zone-all" onclick="toggleAllZone(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-zone-range" onclick="toggleAllZone(0)">เลือก</button>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-7 col-xs-6 padding-5 ">
    <label class="display-block not-show">zone</label>
    <input type="text" class="form-control input-sm r" name="zoneName" id="zoneName" disabled>
  </div>

  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center r from-date" id="fromDate" name="fromDate" value="" />
      <input type="text" class="form-control input-sm width-50 text-center r" id="toDate" name="toDate" value="" />
    </div>
  </div>

  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allCustomer" name="allCustomer" value="1">
  <input type="hidden" id="allWarehouse" name="allWhouse" value="1">
  <input type="hidden" id="allZone" name="allZone" value="1">
  <input type="hidden" id="zoneCode" name="zoneCode" value="">
</div>

<div class="divider"></div>
<div class="row" style="margin-left: -7px; margin-right: -7px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive" style="height:400px; overflow:auto;">
    <table class="table table-striped table-narrow" style="min-width:2160px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-70">วันที่</th>
          <th class="fix-width-100">เลขที่</th>
          <th class="fix-width-150">รหัส</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-60 text-right">ต้นทุน</th>
          <th class="fix-width-60 text-right">ราคา</th>
          <th class="fix-width-60 text-right">ส่วนลด</th>
          <th class="fix-width-60 text-right">หลังส่วนลด</th>
          <th class="fix-width-60 text-right">จำนวน</th>
          <th class="fix-width-60 text-right">ส่วนลดรวม</th>
          <th class="fix-width-60 text-right">มูลค่ารวม</th>
          <th class="fix-width-60 text-right">ต้นทุนรวม</th>
          <th class="fix-width-80">รหัสลูกค้า</th>
          <th class="fix-width-200">ลูกค้า</th>
          <th class="fix-width-80">รหัสคลัง</th>
          <th class="fix-width-200">คลัง</th>
          <th class="fix-width-100">รหัสโซน</th>
          <th class="fix-width-200">โซน</th>
        </tr>
      </thead>
      <tbody id="data-row"></tbody>
    </table>
  </div>
  <div class="divider"></div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
    Total Qty : <span class="padding-left-15 font-size-16" id="total-qty">0</span>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
    Total Discount : <span class="padding-left-15 font-size-16" id="total-discount">0</span>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
    Total Cost : <span class="padding-left-15 font-size-16" id="total-cost">0</span>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
    Total Amount : <span class="padding-left-15 font-size-16" id="total-amount">0</span>
  </div>
</div>


<div class="modal fade" id="wh-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: solid 1px #ddd;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="title text-center">
          <label class="pull-left">
            <input type="checkbox" class="ace" id="chk-all" onchange="toggleCheckAll(this)" />
            <span class="lbl">&nbsp;&nbsp;Select All</span>
          </label>

          เลือกคลังฝากขายแท้
        </h4>
      </div>
      <div class="modal-body" style="max-height:400px; overflow-y:auto;">
        <div class="row">
          <?php if (!empty($whList)) : ?>
            <?php foreach ($whList as $rs) : ?>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>
                  <input type="checkbox" class="ace chk" id="<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" />
                  <span class="lbl">&nbsp;&nbsp;<?php echo $rs->code; ?> | <?php echo $rs->name; ?></span>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>

<script id="template" type="text/x-handlebars-template">
  {{#each this}}
    {{#if nodata}}
      <tr>
        <td class="text-center" colspan="19">No data found</td>
      </tr>
    {{else}}
      <tr>
        <td class="middle text-center">{{no}}</td>
        <td class="middle">{{date_add}}</td>
        <td class="middle">{{reference}}</td>
        <td class="middle">{{product_code}}</td>
        <td class="middle">{{product_name}}</td>
        <td class="middle text-right">{{cost}}</td>
        <td class="middle text-right">{{price}}</td>
        <td class="middle text-right">{{discount_label}}</td>
        <td class="middle text-right">{{sell}}</td>
        <td class="middle text-right">{{qty}}</td>
        <td class="middle text-right">{{total_discount}}</td>
        <td class="middle text-right">{{total_amount}}</td>
        <td class="middle text-right">{{total_cost}}</td>
        <td class="middle">{{customer_code}}</td>
        <td class="middle">{{customer_name}}</td>
        <td class="middle">{{warehouse_code}}</td>
        <td class="middle">{{warehouse_name}}</td>
        <td class="middle">{{zone_code}}</td>
        <td class="middle">{{zone_name}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/sales/sales_consignment_report.js?v=<?php date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>