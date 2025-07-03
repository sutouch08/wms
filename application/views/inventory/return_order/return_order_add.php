<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center h" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
    <input type="text" class="width-100 text-center edit" id="shipped-date" value=""/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="width-100 text-center h" id="customer-code" value=""  />
	</div>
	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-8 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="width-100 h" id="customer-name" value="" readonly/>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 h" id="warehouse">
			<option value="">เลือก</option>
			<?php echo select_common_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>
	<div class="col-lg-11 col-md-6-harf col-sm-6-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 h" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value=""  />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-add"></i> เพิ่ม</button>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="margin-bottom:0px; min-width:1220px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">
						<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))"/>
						<span class="lbl"></span>
					</th>
					<th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-175">รหัส</th>
					<th class="min-width-200">สินค้า</th>
					<th class="fix-width-120 text-center">อ้างอิง</th>
					<th class="fix-width-120 text-center">ออเดอร์</th>
					<th class="fix-width-80 text-center">ราคา</th>
					<th class="fix-width-100 text-center">ส่วนลด(%)</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-80 text-center">คืน</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">

			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" class="middle text-right">รวม</td>
					<td class="middle text-right" id="total-qty"></td>
					<td class="middle text-right" id="total-amount"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
