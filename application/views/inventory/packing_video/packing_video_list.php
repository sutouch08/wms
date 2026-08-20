<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div>
<hr/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm search" name="order_code" value="<?php echo $order_code; ?>" />
    </div>    

    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ประเภท</label>
      <select class="form-control input-sm filter" name="role" id="role">
        <option value="all">ทั้งหมด</option>
        <option value="S" <?php echo is_selected('S', $role); ?>>WO</option>
        <option value="C" <?php echo is_selected('C', $role); ?>>WC</option>
        <option value="N" <?php echo is_selected('N', $role); ?>>WT</option>
        <option value="P" <?php echo is_selected('P', $role); ?>>WS</option>
        <option value="U" <?php echo is_selected('U', $role); ?>>WU</option>
        <option value="T" <?php echo is_selected('T', $role); ?>>WQ</option>
        <option value="Q" <?php echo is_selected('Q', $role); ?>>WV</option>
        <option value="L" <?php echo is_selected('L', $role); ?>>WL</option>
      </select>
    </div>

    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">buton</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<?php $owner = getConfig('VIDEO_SOURCE_OWNER'); ?>
<?php $secret = getConfig('VIDEO_ENDPOINT_SECRET'); ?>
<?php $endpoint = getConfig('VIDEO_SOURCE_ENDPOINT'); ?>
<?php $lastChar = substr($endpoint, -1); ?>
<?php $url = $lastChar === '/' ? $endpoint."view?secret={$secret}&owner={$owner}" : $endpoint . '/view?secret='.$secret.'&owner='.$owner; ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-narrow border-1">
      <tr>
        <th class="fix-width-40 text-center">#</th>        
        <th class="fix-width-120">Order No.</th>
        <th class="fix-width-120">Create at</th>
        <th class="fix-width-120">User</th>
        <th class="min-width-200">Link</th>        
      </tr>
      <tbody>
        <?php if (!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach ($data as $rs) : ?>
          <?php $link = $url . '&order=' . $rs->order_code; ?>
            <tr>
              <td class="text-center no"><?php echo $no; ?></td>
              <td><?php echo $rs->order_code; ?></td>
              <td><?php echo thai_date($rs->create_date, TRUE); ?></td>
              <td><?php echo $rs->user; ?></td>
              <td>
                <a href="javascript:void(0)" onclick="goToVideo('<?php echo $link; ?>')"><?php echo $rs->file_name; ?></a>                
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="5" class="text-center">--- ไม่พบข้อมูล ---</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/packing_video/packing_video.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>