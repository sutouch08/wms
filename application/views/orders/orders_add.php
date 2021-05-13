<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo getConfig('PREFIX_ORDER'); ?>">
	<input type="hidden" id="runNo" value="<?php echo getConfig('RUN_DIGIT_ORDER'); ?>">
<?php endif; ?>

<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-sm-1 col-1-harf hidden-xs padding-5">
    <label>เลขที่เอกสาร</label>
	<?php if($manual_code == 1) : ?>
    <input type="text" class="form-control input-sm" name="code" id="code" value="" />
	<?php else : ?>
		<input type="text" class="form-control input-sm" value="" disabled />
	<?php endif; ?>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

  <div class="col-sm-4 col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

	<div class="col-sm-2 col-xs-12 padding-5">
    <label>ลูกค้า[ออนไลน์]</label>
		<input type="text" class="form-control input-sm" name="cust_ref" value="" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" id="payment" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method(); ?>
		</select>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>อ้างอิงออเดอร์</label>
		<input type="text" class="form-control input-sm" name="reference" value="" />
  </div>

	<div class="col-sm-2 col-2-harf col-xs-12 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

  <div class="col-sm-7 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-sm-1 padding-5 col-xs-12">
    <label class="display-block not-show">Submit</label>
	<?php if($manual_code == 1) : ?>
    <button type="Button" class="btn btn-xs btn-success btn-block" onclick="validateOrder()"><i class="fa fa-plus"></i> เพิ่ม</button>
		<button type="submit" class="btn btn-xs btn-success btn-block hidden" id="btn-submit">เพิ่ม</button>
	<?php else : ?>
		<button type="submit" class="btn btn-xs btn-success btn-block" id="btn-submit"><i class="fa fa-plus"></i> เพิ่ม</button>
	<?php endif; ?>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js"></script>

<?php $this->load->view('include/footer'); ?>
