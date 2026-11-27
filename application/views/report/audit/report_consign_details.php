<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
    <button type="button" class="btn btn-white btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
  </div>
</div><!-- End Row -->
<hr />
<div class="row">
  <div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
    <label>วันที่เอกสาร</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required />
    </div>
  </div>
  
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="display-block">คลังต้นทาง</label>
    <select class="form-control input-sm" name="from_warehouse" id="from-whs">
      <option value="">เลือกคลัง</option>
      <?php echo select_sell_warehouse(); ?>
    </select>
  </div>

  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="display-block">คลังปลายทาง</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>
</div>
<hr>


<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-medium border-1" style="min-width:1200px;">
      <thead>
        <tr>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-80">วันที่</th>
          <th class="fix-width-100">เลขที่เอกสาร</th>
          <th class="fix-width-80">SAP No.</th>
          <th class="min-width-250">ลูกค้า</th>
          <th class="fix-width-150">รหัสสินค้า</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-80 text-center">คลังต้นทาง</th>
          <th class="fix-width-300">โซนปลายทาง</th>
        </tr>
      </thead>
      <tbody id="report-table">
      
      </tbody>
    </table>
  </div>
</div>

<input type="hidden" id="allWarehouse" name="allWarehouse" value="1" />

<form id="export-form" method="post" action="<?php echo $this->home; ?>/do_export">
  <input type="hidden" id="token" name="token" value="" />
  <input type="hidden" id="data" name="data" value="" />
</form>

<div class="modal fade" id="to-whs-modal" tabindex="-1" role="dialog" aria-labelledby="warehouse-modal" aria-hidden="true">
  <div class="modal-dialog" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:solid 1px #ddd;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="title text-center">เลือกคลัง</h4>
      </div>
      <div class="modal-body" style="padding:0px;">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px; max-height: 400px; overflow-y: auto;">
          <?php if (!empty($whsList)) : ?>
            <?php foreach ($whsList as $rs) : ?>
              <label style="display:block;">
                <input type="checkbox" class="ace wh-chk" name="warehouse[]" value="<?php echo $rs->code; ?>" />
                <span class="lbl">&nbsp;&nbsp; <?php echo $rs->code . " | " . $rs->name; ?></span>
              </label>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="divider"></div>
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
        <td colspan="9" class="text-center">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</td>
      </tr>
    {{else}}
      <tr>
        <td class="text-center">{{no}}</td>
        <td>{{date_add}}</td>
        <td>{{code}}</td>
        <td>{{inv_code}}</td>
        <td>{{customer}}</td>
        <td>{{product}}</td>
        <td class="text-right">{{qty}}</td>
        <td class="text-center">{{from_warehouse}}</td>
        <td>{{to_zone}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/audit/consign_details.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>