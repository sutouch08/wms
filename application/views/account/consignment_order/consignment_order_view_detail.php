<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php if ($doc->status == 1) : ?>
      <?php if ($this->pm->can_delete) : ?>
        <button type="button" class="btn btn-xs btn-danger top-btn" onclick="unSaveConsign()"><i class="fa fa-refresh"></i> ยกเลิกการบันทึก</button>
      <?php endif; ?>
      <button type="button" class="btn btn-xs btn-primary top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
    <?php endif; ?>
    <button type="button" class="btn btn-xs btn-info hidden-xs top-btn" onclick="printConsignOrder()"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div><!-- End Row -->
<hr class="" />
<?php if ($doc->status == 2) : ?>
  <?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/update">
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>รหัสลูกค้า</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-5 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>อ้างอิง</label>
      <input type="text" class="form-control input-sm text-center" name="ref_code" id="ref_code" value="<?php echo $doc->ref_code; ?>" disabled>
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>User</label>
      <input type="text" name="" id="" class="form-control input-sm text-center" value="<?php echo $doc->user; ?>" disabled/>
    </div>
    <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
      <label>รหัสโซน</label>
      <input type="text" class="form-control input-sm padding-5" value="<?php echo $doc->zone_code; ?>" disabled />
    </div>
    <div class="col-lg-4 col-md-5-harf col-sm-5-harf col-xs-8 padding-5">
      <label>โซน[ฝากขาย]</label>
      <input type="text" class="form-control input-sm" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
    </div>

    <div class="col-lg-5-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label>SAP No.</label>
      <input type="text" name="" id="" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled/>
    </div>

  </div>
  <hr class="margin-top-15">
  <input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
  <input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
  <input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>">
</form>

<?php $this->load->view('account/consignment_order/consignment_order_detail'); ?>


<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>