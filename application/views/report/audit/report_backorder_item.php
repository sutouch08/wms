<?php $this->load->view('include/header'); ?>
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
	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 r text-center from-date" id="fromDate" placeholder="เริ่มต้น" value="" />
			<input type="text" class="form-control input-sm width-50 r text-center" id="toDate" placeholder="สิ้นสุด" value=""/>
		</div>
	</div>
</div>

<form id="exportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" id="filter" name="filter" value="" />
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</form>
<hr>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-striped table-bordered tableFixHead border-1" style="min-width:650px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-50 text-center fix-header">#</th>
					<th class="fix-width-100 text-center fix-header">วันที่</th>
					<th class="fix-width-100 text-center fix-header">ออเดอร์</th>
					<th class="min-width-200 fix-header">รหัสสินค้า</th>
					<th class="fix-width-100 text-right fix-header">Order Qty</th>
					<th class="fix-width-100 text-center fix-header">Available Qty</th>
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
			<tr><td colspan="6" class="text-center">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</td></tr>
		{{else}}
		<tr class="font-size-11">
			<td class="text-center">{{no}}</td>
			<td class="text-center">{{date_upd}}</td>
			<td class="">{{order_code}}</td>
			<td class="">{{product_code}}</td>
			<td class="text-right">{{order_qty}}</td>
			<td class="text-right">{{available_qty}}</td>
		</tr>
		{{/if}}
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/audit/backorder_item.js?v=<?php date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
