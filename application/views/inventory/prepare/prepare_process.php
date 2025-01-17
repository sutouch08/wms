<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-warning" onclick="goBack()"><i class="fa fa-server"></i> ออเดอร์รอจัด</button>
    <button type="button" class="btn btn-white btn-info" onclick="goProcess()"><i class="fa fa-shopping-basket"></i> ออเดอร์กำลังจัด</button>
  </div>
</div>

<hr class="padding-5" />
<?php if($order->state != 4) : ?>
<?php   $this->load->view('inventory/prepare/invalid_state'); ?>
<?php else : ?>

  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่</label>
      <input type="text" class="width-100 text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="width-100 text-center" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>อ้างอิง</label>
      <input type="text" class="width-100 text-center" value="<?php echo $order->reference; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="width-100 text-center" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-5 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
      <label class="not-show">ลูกค้า</label>
      <input type="text" class="width-100" value="<?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
      <input type="text" class="width-100" value="<?php echo $order->channels_name; ?>" disabled />
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
      <label>คลัง</label>
      <input type="text" class="width-100" id="whs-name" value="<?php echo $order->warehouse_name; ?>" disabled />
    </div>

    <div class="col-lg-9 col-md-7 col-sm-7 col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="width-100" value="<?php echo $order->remark; ?>" disabled />
    </div>
  </div>

  <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  <input type="hidden" name="zone_code" id="zone_code" />
  <input type="hidden" id="warehouse_code" value="<?php echo $order->warehouse_code; ?>" />
  <input type="hidden" id="allow-prepare" value="<?php echo $order->allow_prepare; ?>" />

  <hr class="margin-top-10 margin-bottom-10"/>

  <?php if($order->allow_prepare) : ?>
    <?php $this->load->view('inventory/prepare/prepare_control'); ?>
  <?php else : ?>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
        <h4 class="red">ไม่อนุญาติให้จัดสินค้าในคลังนี้</h4>
      </div>
    </div>
  <?php endif; ?>

  <hr class="margin-top-10 margin-bottom-10"/>

  <?php $this->load->view('inventory/prepare/prepare_incomplete_list');  ?>

  <?php $this->load->view('inventory/prepare/prepare_completed_list'); ?>

<?php endif; //--- endif order->state ?>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_process.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
