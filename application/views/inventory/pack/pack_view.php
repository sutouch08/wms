<?php $this->load->view('include/header'); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div>
<hr class="" />

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm search" name="order_code" value="<?php echo $order_code; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>รหัสสินค้า</label>
      <input type="text" class="form-control input-sm search" name="pd_code" value="<?php echo $pd_code; ?>" />
    </div>

    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
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
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-narrow border-1" style="min-width:790px;">
      <tr>
        <th class="fix-width-50 text-center">#</th>
        <th class="fix-width-40 text-center"></th>
        <th class="fix-width-120">วันที่</th>
        <th class="fix-width-150">เลขที่เอกสาร</th>
        <th class="fix-width-150">สินค้า</th>
        <th class="fix-width-80">จำนวน</th>
        <th class="fix-width-100">กล่อง</th>
        <th class="min-width-100">สถานะ</th>
      </tr>
      <tbody>
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php $orders = []; ?>

          <?php foreach ($data as $rs) : ?>
            <?php $state = ""; ?>
            <?php if (isset($orders[$rs->order_code])) : ?>
              <?php $state = $orders[$rs->order_code]; ?>
            <?php else : ?>
              <?php $state = $this->pack_model->get_order_state($rs->order_code); ?>
              <?php $orders[$rs->order_code] = $state; ?>
            <?php endif; ?>
            <?php $box_no = empty($rs->box_id) ? "-" : $this->pack_model->get_box_no($rs->box_id); ?>
            <tr id="row-<?php echo $rs->id; ?>">
              <td class="text-center no"><?php echo $no; ?></td>
              <td class="text-center">
                <?php if ($this->pm->can_delete) : ?>
                  <button type="button" class="btn btn-minier btn-danger"
                    onclick="deletePack(<?php echo $rs->id; ?>, '<?php echo $rs->order_code; ?>', '<?php echo $rs->product_code; ?>')">
                    <i class="fa fa-trash"></i>
                  </button>
                <?php endif; ?>
              </td>
              <td><?php echo thai_date($rs->date_upd, TRUE); ?></td>
              <td><?php echo $rs->order_code; ?></td>
              <td><?php echo $rs->product_code; ?></td>
              <td><?php echo number($rs->qty); ?></td>
              <td><?php echo "กล่องที่ {$box_no}"; ?></td>
              <td><?php echo get_state_name($state); ?></td>
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

<script src="<?php echo base_url(); ?>scripts/inventory/pack/pack.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>