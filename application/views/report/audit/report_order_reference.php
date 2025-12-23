<?php $this->load->view('include/header'); ?>
<style>
	.dispatch-row > td {
		padding:3px !important;
		vertical-align: middle !important;
		color: #333333;
	}

	.font-size-11 {
		font-size: 11px !important;
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
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
	</div>
</div><!-- End Row -->
<hr />
<div class="row">
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
		<label>เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm text-center focus" placeholder="สแกนเพื่อเพิ่มออเดอร์" id="order-no" autofocus />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getOrder()">Submit</button>
  </div>
</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered">
      <thead>
				<tr>
					<th class="fix-width-50 text-center fix-header"></th>
					<th class="fix-width-50 text-center fix-header">#</th>
					<th class="fix-width-150 fix-header">เลขที่</th>
					<th class="fix-width-150 fix-header">อ้างอิง</th>
					<th class="fix-width-150 fix-header">Tracking</th>
					<th class="min-width-200 fix-header">ลูกค้า</th>
					<th class="fix-width-150 fix-header">ช่องทางขาย</th>
					<th class="fix-width-100 text-center fix-header">กล่อง(ทั้งหมด)</th>
				</tr>
      </thead>
      <tbody id="order-table">

      </tbody>
			<tfoot>
				<tr>
					<td colspan="7" class="text-right">รวม</td>
					<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="total-carton" value="0" readonly/></td>
				</tr>
			</tfoot>
    </table>
  </div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
	<tr id="row-{{id}}" class="font-size-11 dispatch-row" data-id="{{id}}">
		<td class="middle text-center">
			<a href="javascript:deleteRow('{{id}}')"><i class="fa fa-trash fa-lg red"></i></a>
		</td>
		<td class="text-center no"></td>
		<td>
			<input type="text"
			class="form-control input-xs text-label font-size-11 order-code"
			id="{{id}}"
			data-order="{{order_code}}"
			data-ref="{{reference}}"
			data-tracking="{{tracking_no}}"
			data-customer="{{customer_name}}"
			data-channels="{{channels_name}}"
			data-carton="{{carton_qty}}"
			value="{{order_code}}" readonly />
		</td>
		<td>{{reference}}</td>
		<td>{{tracking_no}}</td>
		<td>{{customer_name}}</td>
		<td>{{channels_name}}</td>
		<td style="padding:0px;">
			<input type="number" class="form-control input-xs text-label font-size-11 text-center carton-qty" id="carton-qty-{{id}}" value="{{carton_qty}}" readonly/>
		</td>
	</tr>
</script>


<form id="exportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" id="data" name="data" value="" />
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</form>

<script src="<?php echo base_url(); ?>scripts/report/audit/order_reference.js?v=<?php date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
