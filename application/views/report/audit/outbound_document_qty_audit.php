<?php $this->load->view('include/header'); ?>
<div class="row hidden-print" id="header-row">
	<div class="col-lg-8 col-md-8 col-sm-8 hidden-xs padding-5">
		<h3 class="title"><?php echo $this->title; ?> </h3>
	</div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h4 class="title-xs"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row" id="search-row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm" name="is_wms" id="is_wms">
			<option value="1">PLC</option>
			<option value="2">Sokochan</option>
		</select>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
    </div>
  </div>
  <!-- <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block">เอกสาร</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-doc-all" onclick="toggleAllDocument(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-doc-range" onclick="toggleAllDocument(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="docFrom" name="docFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="docTo" name="docTo" placeholder="สิ้นสุด" disabled>
  </div> -->

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block">ประเภท</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-role-all" onclick="toggleAllRole(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-role-range" onclick="toggleAllRole(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block">สถานะ(IX)</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-state-all" onclick="toggleAllState(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-state-range" onclick="toggleAllState(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
    <select class="form-control input-sm" id="channels" name="channels">
    	<option value="all">ทั้งหมด</option>
			<?php echo select_channels(); ?>
    </select>
  </div>

  <input type="hidden" id="allDoc" name="allDoc" value="1">
	<input type="hidden" id="allRole" name="allRole" value="1">
	<input type="hidden" id="allState" name="allState" value="1">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</div>

<div class="modal fade" id="role-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>เลือกประเภทเอกสาร</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk" id="role-s" name="role[]" value="S" data-prefix="WO" style="margin-right:10px;" />
                WO - ขาย
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-c" name="role[]" value="C" data-prefix="WC" style="margin-right:10px;" />
                WC - ฝากขาย(เทียม)
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-n" name="role[]" value="N" data-prefix="WT" style="margin-right:10px;" />
                WT - ฝากขาย(แท้)
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-p" name="role[]" value="P" data-prefix="WS" style="margin-right:10px;" />
                WS - สปอนเซอร์
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-u" name="role[]" value="U" data-prefix="WU" style="margin-right:10px;" />
                WU - อภินันท์
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-l" name="role[]" value="L" data-prefix="WL" style="margin-right:10px;" />
                WL - ยืมสินค้า
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-t" name="role[]" value="T" data-prefix="WQ" style="margin-right:10px;" />
                WQ - แปรสภาพ(ขาย)
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="chk" id="role-q" name="role[]" value="Q" data-prefix="WV" style="margin-right:10px;" />
                WV - แปรสภาพ(สต็อก)
              </label>
            </div>
        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="state-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>สถานะเอกสาร</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
            <div class="col-sm-12">
              <label><input type="checkbox" class="state" name="state[]" value="1" style="margin-right:10px;" />รอดำเนินการ</label>
						</div>
						<div class="col-sm-12">
              <label><input type="checkbox" class="state" name="state[]" value="2" style="margin-right:10px;" />รอชำระเงิน</label>
						</div>
						<div class="col-sm-12">
              <label><input type="checkbox" class="state" name="state[]" value="3" style="margin-right:10px;" />รอจัดสินค้า</label>
						</div>
						<div class="col-sm-12">
              <label><input type="checkbox" class="state" name="state[]" value="7" style="margin-right:10px;" />รอเปิดบิล</label>
						</div>
						<div class="col-sm-12">
              <label><input type="checkbox" class="state" name="state[]" value="8" style="margin-right:10px;" />เปิดบิลแล้ว</label>
						</div>
						<div class="col-sm-12">
              <label><input type="checkbox" class="state" name="state[]" value="9" style="margin-right:10px;" />ยกเลิก</label>
						</div>
        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>
<hr>
</form>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" id="result" style="overflow:auto;">
		<table class="table table-striped tableFixHead border-1" style="min-width:970px;">
			<thead>
		    <tr class="font-size-12">
		      <th class="fix-width-50 middle text-center fix-header">ลำดับ</th>
		      <th class="fix-width-100 middle text-center fix-header">วันที่(IX)</th>
		      <th class="fix-width-120 middle text-center fix-header">IX</th>
					<th class="fix-width-120 middle text-center fix-header">WMS</th>
					<th class="fix-width-100 middle text-center fix-header">SAP</th>
					<th class="fix-width-80 middle text-right fix-header">Qty(IX)</th>
					<th class="fix-width-80 middle text-right fix-header">Qty(WMS)</th>
					<th class="fix-width-80 middle text-right fix-header">Qty(SAP)</th>
					<th class="fix-width-120 middle text-center fix-header">สถานะ(IX)</th>
					<th class="min-width-120 middle text-center fix-header">ช่องทางขาย</th>
		    </tr>
			</thead>
			<tbody id="rs"></tbody>
		</table>
  </div>
</div>

<script id="template" type="text/x-handlebars-template">
{{#each this}}
  {{#if nodata}}
    <tr>
      <td colspan="10" align="center"><h4>-----  ไม่พบเอกสารตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
		<tr class="font-size-12 {{hilight}}">
			<td class="middle text-center">{{no}}</td>
			<td class="middle text-center">{{ date }}</td>
			<td class="middle text-center">{{ ix_code }}</td>
			<td class="middle text-center">{{ wms_code }}</td>
			<td class="middle text-center">{{ sap_code }}</td>
			<td class="middle text-right">{{ ix_qty }}</td>
			<td class="middle text-right">{{ wms_qty }}</td>
			<td class="middle text-right">{{ sap_qty }}</td>
			<td class="middle text-center">{{ ix_state }}</td>
			<td class="middle text-center">{{ channels }}</td>
		</tr>
  {{/if}}
{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/audit/outbound_document_qty_audit.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
