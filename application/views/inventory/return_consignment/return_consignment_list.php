<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
  </div>
</div>
<hr />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center search" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่บิล</label>
      <input type="text" class="form-control input-sm text-center search" name="invoice" value="<?php echo $invoice; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-3 col-sm-2 col-xs-6 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm text-center search" name="customer_code" value="<?php echo $customer_code; ?>" />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>สถานะ</label>
      <select class="form-control input-sm" name="status" onchange="getSearch()">
        <option value="all">ทั้งหมด</option>
        <option value="0" <?php echo is_selected('0', $status); ?>>Draft</option>
        <option value="1" <?php echo is_selected('1', $status); ?>>Closed</option>
        <option value="3" <?php echo is_selected('3', $status); ?>>On Process</option>
        <option value="2" <?php echo is_selected('2', $status); ?>>Cancelled</option>
      </select>
    </div>
    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>การอนุมัติ</label>
      <select class="form-control input-sm" name="approve" onchange="getSearch()">
        <option value="all">ทั้งหมด</option>
        <option value="0" <?php echo is_selected($approve, '0'); ?>>รออนุมัติ</option>
        <option value="1" <?php echo is_selected($approve, '1'); ?>>อนุมัติแล้ว</option>
      </select>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>Arrival</label>
      <select class="form-control input-sm" name="is_arrival" onchange="getSearch()">
        <option value="all">ทั้งหมด</option>
        <option value="0" <?php echo is_selected('0', $is_arrival); ?>>ยังไม่มาถึง</option>
        <option value="1" <?php echo is_selected('1', $is_arrival); ?>>มาถึงแล้ว</option>
      </select>
    </div>    
    <div class="col-lg-4-harf col-md-3 col-sm-2-harf col-xs-6 padding-5">
      <label>คลังฝากขาย</label>
      <select class="form-control input-sm filter" name="from_warehouse" id="from-whs">
        <option value="all">ทั้งหมด</option>
        <?php echo select_consignment_warehouse($from_warehouse); ?>
      </select>
    </div>
    <div class="col-lg-4-harf col-md-3 col-sm-2-harf col-xs-6 padding-5">
      <label>คลังรับเข้า</label>
      <select class="form-control input-sm filter" name="to_warehouse" id="to-whs">
        <option value="all">ทั้งหมด</option>
        <?php echo select_common_warehouse($to_warehouse); ?>
      </select>
    </div>

    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>SAP</label>
      <select name="sap" class="form-control input-sm" onchange="getSearch()">
        <option value="all">ทั้งหมด</option>
        <option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
        <option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
      </select>
    </div>

    <div class="divider-hidden visible-xs"></div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
      <label class="display-block not-show hidden-xs">btn</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
      <label class="display-block not-show hidden-xs">btn</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15 padding-5" />
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <p class="pull-right top-p padding-5">สถานะ : <span class="green">OK</span> = ปกติ,&nbsp; <span class="orange">DF</span> = ดราฟท์,&nbsp; <span class="purple">OP</span> = รอรับที่คลัง,&nbsp; <span class="red">CN</span> = ยกเลิก</p>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-narrow border-1" style="min-width:1170px;">
      <thead>
        <tr>
          <th class="fix-width-100"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-80 text-center">วันที่</th>
          <th class="fix-width-100">เลขที่เอกสาร</th>
          <th class="fix-width-80">เลขที่บิล</th>
          <th class="fix-width-60 text-center">สถานะ</th>
          <th class="fix-width-50 text-center">Arrival</th>
          <th class="min-width-300">ลูกค้า</th>
          <th class="fix-width-120">โซน(รับ)</th>
          <th class="fix-width-80 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">มลูค่า</th>
          <th class="fix-width-60 text-center">อนุมัติ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($docs)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach ($docs as $rs) : ?>
            <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
              <td class="middle">
                <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
                <?php if ($this->pm->can_edit && $rs->status == 0) : ?>
                  <button type="button" class="btn btn-minier btn-warning" onclick="edit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>
                <?php if ($this->pm->can_delete && $rs->status != 2) : ?>
                  <button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
              <td class="middle hide-text text-center no"><?php echo $no; ?></td>
              <td class="middle hide-text text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
              <td class="middle hide-text"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->invoice; ?></td>
              <td class="middle text-center"><?php echo $rs->status == 1 ? '<span class="green">OK</span>' : ($rs->status == 0 ? '<span class="orange">DF</span>' : ($rs->status == 3 ? '<span class="purple">OP</span>' : ($rs->status == 2 ? '<span class="red">CN</span>' : ''))); ?></td>              
              <td class="middle text-center"><?php echo is_active($rs->product_arrival, FALSE); ?></td>
              <td class="middle hide-text" style="max-width:400px;"><?php echo $rs->customer_name; ?></td>
              <td class="middle hide-text"><?php echo $rs->zone_code; ?></td>
              <td class="middle text-right"><?php echo number($rs->qty); ?></td>
              <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
              <td class="middle text-center"><?php echo is_active($rs->is_approve); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="12" class="text-center">
              --- ไม่พบรายการ ---
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('cancle_modal'); ?>

<script>
  $('#from-whs').select2();
  $('#to-whs').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>