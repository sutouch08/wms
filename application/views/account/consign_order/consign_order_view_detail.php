<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-8 col-md-7 col-sm-8 col-xs-12 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 1) : ?>
			<?php if($this->pm->can_delete && $in_sap === FALSE) : ?>
				<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unSaveConsign()"><i class="fa fa-refresh"></i> ยกเลิกการบันทึก</button>
			<?php endif; ?>
				<button type="button" class="btn btn-sm btn-primary top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
		<?php endif; ?>
			<button type="button" class="btn btn-sm btn-info  top-btn hidden-xs" onclick="printConsignOrder()"><i class="fa fa-print"></i> พิมพ์</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<?php if($doc->status == 2) : ?>
<?php 	$this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/update">
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customerCode" id="customerCode" value="<?php echo $doc->customer_code; ?>" disabled>
	</div>
  <div class="col-lg-6 col-md-5 col-sm-5 col-xs-9 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Tax Status</label>
		<input type="text" class="width-100 text-center" id="tax-status" value="<?php echo $doc->tax_status == 1 ? 'Y' : 'N'; ?>" disabled />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm edit text-center" id="zone_code" name="zone_code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>

	<div class="col-lg-3-harf col-md-8 col-sm-8 col-xs-8 padding-5">
    <label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>อ้างอิง</label>
		<input type="text" class="form-control input-sm"  value="<?php echo $doc->is_api ? $doc->pos_ref : $doc->ref_code; ?>" disabled />
  </div>

	<div class="col-lg-3-harf col-md-10-harf col-sm-10-harf col-xs-5 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>SAP No.</label>
    <input type="text" class="form-control input-sm" id="inv_code" value="<?php echo $doc->inv_code; ?>" disabled>
  </div>
</div>

<?php if($doc->tax_status == 1) : ?>
	<hr/>
	<div class="row">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6 padding-5">
			<label>ชื่อสำหรับออกใบกำกับภาษี</label>
			<input type="text" class="width-100" id="name" value="<?php echo $doc->name; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Tax ID</label>
			<input type="text" class="width-100 text-center" id="tax-id" value="<?php echo $doc->tax_id; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1 col-xs-6 padding-5">
			<label>รหัสสาขา</label>
			<input type="text" class="width-100 text-center" id="branch-code" value="<?php echo $doc->branch_code; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ชื่อสาขา</label>
			<input type="text" class="width-100 text-center" id="branch-name" value="<?php echo $doc->branch_name; ?>" disabled/>
		</div>
		<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เบอร์โทร</label>
			<input type="text" class="width-100 text-center" id="phone" value="<?php echo $doc->phone; ?>" disabled/>
		</div>
		<div class="col-lg-6 col-md-4-harf col-sm-4-harf col-xs-12 padding-5">
			<label>ที่อยุ่</label>
			<input type="text" class="width-100" id="address" value="<?php echo $doc->address; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ตำบล</label>
			<input type="text" class="width-100" id="sub-district" value="<?php echo $doc->sub_district; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>อำเภอ</label>
			<input type="text" class="width-100" id="district" value="<?php echo $doc->district; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>จังหวัด</label>
			<input type="text" class="width-100" id="province" value="<?php echo $doc->province; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>ไปรษณีย์</label>
			<input type="text" class="width-100" id="postcode" value="<?php echo $doc->postcode; ?>" disabled/>
		</div>
	</div>
<?php endif; ?>

<hr class="margin-top-15">
<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" >
</form>
<?php if($doc->is_api == 1) : ?>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="red text-center">** เอกสารนี้ถูกสร้างโดยระบบ POS จึงไม่สามารถแก้ไขรายการได้ **</p>
		</div>
	</div>
<?php endif; ?>
<?php $this->load->view('account/consign_order/consign_order_detail'); ?>


<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
