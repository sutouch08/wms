<?php $this->load->view('include/header'); ?>
<!-- รายงาน เอกสารที่ยังไม่ได้กดรับบน SAP -->
<style>
	.fix-width-400 {
		width:400px !important;
	}

</style>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
	</div>
</div><!-- End Row -->
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center r" id="customer-code" />
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm r" id="customer-name" readonly />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm r" id="zone-code" />
	</div>
	<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm r" id="zone-name" readonly />
	</div>
	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 r text-center from-date" id="fromDate" placeholder="เริ่มต้น" value="" />
			<input type="text" class="form-control input-sm width-50 r text-center" id="toDate" placeholder="สิ้นสุด" value=""/>
		</div>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ประเภทวันที่</label>
		<select class="form-control input-sm" id="date-type">
			<option value="D">วันที่เอกสาร</option>
			<option value="S">วันที่จัดส่ง</option>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>การรับ</label>
		<select class="form-control input-sm" id="is-accept">
			<option value="0">ยังไม่รับ</option>
			<option value="1">รับแล้ว</option>
			<option value="all">ทั้งหมด</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>SAP</label>
		<select class="form-control input-sm" id="is-complete">
			<option value="1">เข้าแล้ว</option>
			<option value="0">ยังไม่เข้า</option>
			<option value="all">ทั้งหมด</option>
		</select>
	</div>
</div>

<form id="exportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" id="filter" name="filter" value="" />
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</form>
<hr>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-striped border-1" style="min-width:1970px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-50 text-center">#</th>
					<th class="fix-width-100 text-center">วันที่เอกสาร</th>
					<th class="fix-width-100 text-center">วันที่จัดส่ง</th>
					<th class="fix-width-100">เลขที่</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="fix-width-120">รหัสโซน</th>
					<th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-50 text-center">รับแล้ว</th>
					<th class="fix-width-50 text-center">SAP</th>
					<th class="fix-width-300">สินค้า</th>
					<th class="fix-width-300">โซน</th>
					<th class="fix-width-100">รหัสลูกค้า</th>
					<th class="fix-width-400">ลูกค้า</th>
				</tr>
			</thead>
			<tbody id="result-table">

			</tbody>
		</table>
	</div>
</div>

<script id="template" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="13" class="text-center">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</td></tr>
		{{else}}
		<tr class="font-size-11">
			<td class="text-center">{{no}}</td>
			<td class="text-center">{{date_add}}</td>
			<td class="text-center">{{shipped_date}}</td>
			<td class="">{{reference}}</td>
			<td class="">{{product_code}}</td>
			<td class="">{{zone_code}}</td>
			<td class="text-right">{{qty}}</td>
			<td class="text-center">{{is_accept}}</td>
			<td class="text-center">{{is_complete}}</td>
			<td class="">{{product_name}}</td>
			<td class="">{{zone_name}}</td>
			<td class="">{{customer_code}}</td>
			<td class="">{{customer_name}}</td>
		</tr>
		{{/if}}
	{{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/audit/consign_acception.js?v=<?php date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
