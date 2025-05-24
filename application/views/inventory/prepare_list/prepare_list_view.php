<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn" onclick="exportFilter()"><i class="fa fa-file-excel-o"></i> Export CSV</button>
	</div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-3 col-sm-2-harf col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100 search" name="order_code" id="order-code"  value="<?php echo $order_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-2-harf col-xs-6 padding-5">
			<label>รหัสสินค้า</label>
			<input type="text" class="form-control input-sm search" name="pd_code" id="pd-code" value="<?php echo $pd_code; ?>" />
		</div>

		<div class="col-lg-3 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>รหัสโซน</label>
			<input type="text" class="form-control input-sm search" name="zone_code" id="zone-code" value="<?php echo $zone_code; ?>" />
		</div>

		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse_code" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse_code); ?>
			</select>
		</div>

		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>User</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>

<form id="export-form" action="<?php echo $this->home; ?>/export_filter" method="post">
	<input type="hidden" name="order_code" id="ex-order-code" />
	<input type="hidden" name="product_code" id="ex-pd-code" />
	<input type="hidden" name="warehouse_code" id="ex-whs-code" />
	<input type="hidden" name="zone_code" id="ex-zone-code" />
	<input type="hidden" name="user" id="ex-user" />
	<input type="hidden" name="from_date" id="ex-from-date" />
	<input type="hidden" name="to_date" id="ex-to-date" />
	<input type="hidden" name="token" id="token" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1100px;">
      <tr class="font-size-11">
        <th class="fix-width-50 text-center">ลำดับ</th>
        <th class="fix-width-150">วันที่</th>
        <th class="fix-width-120">เลขที่เอกสาร</th>
        <th class="fix-width-150">สินค้า</th>
        <th class="fix-width-80 text-center">จำนวน</th>
        <th class="fix-width-100 text-center">สถานะ</th>
				<th class="fix-width-100">คลัง</th>
    		<th class="min-width-200">โซน</th>
        <th class="fix-width-150">User</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
		<?php $stateName = $this->prepare_list_model->state_name_array(); ?>
		<?php $state = []; ?>
		<?php $zoneList = []; ?>
    <?php foreach($data as $rs) : ?>
			<?php if(empty($state[$rs->order_code])) : ?>
				<?php $state[$rs->order_code] =  $this->prepare_list_model->get_order_state($rs->order_code); ?>
			<?php endif; ?>
			<?php if(empty($zoneList[$rs->zone_code])) : ?>
				<?php $zoneList[$rs->zone_code] = $this->zone_model->get_name($rs->zone_code); ?>
			<?php endif; ?>
      <tr class="font-size-11">
        <td class="text-center no"><?php echo $no; ?></td>
        <td><?php echo thai_date($rs->date_upd, TRUE); ?></td>
        <td><?php echo $rs->order_code; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
    		<td class="text-center"><?php echo $stateName[$state[$rs->order_code]]; ?></td>
				<td><?php echo $rs->warehouse_code; ?></td>
        <td><?php echo $zoneList[$rs->zone_code]; ?></td>
    		<td><?php echo $rs->user; ?></td>
      </tr>
    <?php  $no++; ?>
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
<script>
	$('#warehouse').select2();
	$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare_list/prepare_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
