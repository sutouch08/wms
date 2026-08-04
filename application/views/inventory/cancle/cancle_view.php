<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div>
<hr />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm search" name="order_code" value="<?php echo $order_code; ?>" placeholder="เลขที่เอกสาร" />
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>รหัสสินค้า</label>
      <input type="text" class="form-control input-sm search" name="pd_code" value="<?php echo $pd_code; ?>" placeholder="รหัสสินค้า" />
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>รหัสโซน</label>
      <input type="text" class="form-control input-sm search" name="zone_code" value="<?php echo $zone_code; ?>" placeholder="รหัสโซน" />
    </div>

    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5 last">
      <label class="display-block not-show">buton</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
        <th class="width-15 text-center">วันที่</th>
        <th class="width-10 text-center">เลขที่เอกสาร</th>
        <th class="width-15 text-center">สินค้า</th>
        <th class="width-10 text-center">จำนวน</th>
        <th class="width-10 text-center">สถานะ</th>
        <th class="width-20">โซน</th>
        <th class="width-15 text-center">Action</th>
      </tr>
      <tbody>
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php $orders = []; ?>
          <?php $zones = []; ?>          
          <?php foreach ($data as $rs) : ?>
          <?php $zoneName = ""; ?>
          <?php if(isset($zones[$rs->zone_code])) : ?>
            <?php $zoneName = $zones[$rs->zone_code]; ?>
          <?php else : ?>
            <?php $zoneName = zone_name($rs->zone_code); ?>
            <?php $zones[$rs->zone_code] = $zoneName; ?>
          <?php endif; ?>
          
          <?php $state = ""; ?>
          <?php if(isset($orders[$rs->order_code])) : ?>
            <?php $state = $orders[$rs->order_code]; ?>
          <?php else : ?>
            <?php $state = $this->cancle_model->get_order_state($rs->order_code); ?>
            <?php $orders[$rs->order_code] = $state; ?>
          <?php endif; ?>
            <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
              <td class="text-center no"><?php echo $no; ?></td>
              <td class="text-center"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
              <td class="text-center"><?php echo $rs->order_code; ?></td>
              <td><?php echo $rs->product_code; ?></td>
              <td class="text-center"><?php echo number($rs->qty); ?></td>
              <td class="text-center"><?php echo get_state_name($state); ?></td>
              <td> <?php echo $zoneName; ?></td>
              <td class="text-right">

              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="8" class="text-center">--- ไม่พบข้อมูล ---</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/cancle/cancle.js"></script>

<?php $this->load->view('include/footer'); ?>