<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-sm btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
    <button type="button" class="btn btn-sm btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
    <button type="button" class="btn btn-sm btn-info top-btn" onclick="print()"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm search" id="zone-code" value="" />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสสินค้า</label>
		<input type="text" class="form-control input-sm search" id="pd-code" value="" />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>Min stock</label>
		<input type="number" class="width-100 text-center search" id="min-stock" value="<?php echo $min_stock; ?>" />
	</div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>การแสดงผล</label>
		<select class="width-100 filter" id="is-min">
			<option value="1">น้อยกว่าขั้นต่ำ</option>
			<option value="2">มากกว่าขั้นต่ำ</option>
			<option value="all">ทั้งหมด</option>
		</select>
	</div>
</div>

<hr class="margin-top-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered border-1" style="min-width:900px;">
      <tr class="font-size-11">
        <th class="fix-width-50 text-center">#</th>
        <th class="fix-width-150 text-center">รหัสโซน</th>
				<th class="fix-width-150 text-center">ชื่อโซน</th>
        <th class="fix-width-150 text-center">รหัสสินค้า</th>
        <th class="min-width-200 text-center">ชื่อสินค้า</th>
        <th class="fix-width-100 text-center">จำนวน</th>
        <th class="fix-width-100 text-center">สถานะ</th>
      </tr>
      <tbody id="result">
				<tr>
					<td colspan="7" class="text-center">--- ไม่พบรายการ ---</td>
				</tr>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/report/inventory/fast_move_stock.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
