<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pakking-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search-box" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่อ้างอิง</label>
    <input type="text" class="form-control input-sm search-box" name="reference"  value="<?php echo $reference; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search-box" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>พนักงาน/ผู้สั่งงาน</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="width-100 filter" name="channels" id="channels">
      <option value="">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>Shop Name</label>
		<select class="form-control input-sm" name="shop_id" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_shop_name($shop_id); ?>
		</select>
	</div>

	<div class="col-lg-4 col-md-5 col-sm-6 col-xs-6 padding-5">
    <label>คลังสินค้า</label>
		<select class="width-100 filter" id="warehouse" name="warehouse">
      <option value="all">ทั้งหมด</option>
      <?php echo select_sell_warehouse($warehouse); ?>
    </select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>ประเภท</label>
		<select class="form-control input-sm filter" name="role">
			<option value="all">ทั้งหมด</option>
			<option value="S" <?php echo is_selected($role, 'S'); ?>>WO</option>
			<option value="C" <?php echo is_selected($role, 'C'); ?>>WC</option>
			<option value="N" <?php echo is_selected($role, 'N'); ?>>WT</option>
			<option value="P" <?php echo is_selected($role, 'P'); ?>>WS</option>
			<option value="U" <?php echo is_selected($role, 'U'); ?>>WU</option>
			<option value="Q" <?php echo is_selected($role, 'Q'); ?>>WV</option>
			<option value="T" <?php echo is_selected($role, 'T'); ?>>WQ</option>
			<option value="L" <?php echo is_selected($role, 'L'); ?>>WL</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>การยืนยัน</label>
		<select class="form-control input-sm" name="is_valid" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected($is_valid, '1'); ?>>ยืนยันแล้ว</option>
			<option value="0" <?php echo is_selected($is_valid, '0'); ?>>ยังไม่ยืนยัน</option>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Temp</label>
		<select class="form-control input-sm" name="is_exported" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected($is_exported, '1'); ?>>ส่งแล้ว</option>
			<option value="0" <?php echo is_selected($is_exported, '0'); ?>>ยังไม่ส่ง</option>
			<option value="3" <?php echo is_selected($is_exported, '3'); ?>>ส่งออกไม่สำเร็จ</option>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>SAP</label>
		<select class="form-control input-sm" name="sap_status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="Y" <?php echo is_selected($sap_status, 'Y'); ?>>เข้าแล้ว</option>
			<option value="N" <?php echo is_selected($sap_status, 'N'); ?>>ยังไม่เข้า</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>ช่วงข้อมูล</label>
		<select class="form-control input-sm filter" name="range">
			<option value="top"><?php echo number(getConfig('FILTER_RESULT_LIMIT')); ?> รายการล่าสุด</option>
			<option value="all" <?php echo is_selected('all', $range); ?>>ทั้งหมด</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่จัดส่ง</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="ship_from_date" id="shipFromDate" value="<?php echo $ship_from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="ship_to_date" id="shipToDate" value="<?php echo $ship_to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
	</div>
</div>

<hr class="margin-top-15">
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>
<?php $sort_code = $order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:1370px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-150 text-center"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100 text-center">วันที่</th>
					<th class="fix-width-100 text-center">วันที่จัดส่ง</th>
          <th class="fix-width-120">เลขที่เอกสาร</th>
					<th class="fix-width-150">เลขที่อ้างอิง</th>
					<th class="fix-width-100">Tracking</th>
					<th class="fix-width-100">SAP NO</th>
          <th class="min-width-200">ลูกค้า/ผู้รับ/ผู้เบิก</th>
          <th class="fix-width-100 text-right">ยอดเงิน</th>
          <th class="fix-width-100 text-center">รูปแบบ</th>
          <th class="fix-width-150 text-center">พนักงาน</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-11">
					<td class="">
						<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')">
							รายละเอียด
						</button>
            <button type="button" class="btn btn-minier btn-success" onclick="do_export('<?php echo $rs->code; ?>')">
							<i class="fa fa-send"></i> SAP
						</button>
          </td>
          <td class="text-center"><?php echo $no; ?></td>
          <td class="text-center"><?php echo thai_date($rs->date_add); ?></td>
					<td class="text-center"><?php echo thai_date($rs->shipped_date); ?></td>
					<td class=""><a href="javascript:viewOrderDetail('<?php echo $rs->code; ?>', '<?php echo $rs->role; ?>')"><?php echo $rs->code; ?></a></td>
					<td class=""><?php echo $rs->reference; ?></td>
					<td class=""><?php echo $rs->shipping_code; ?></td>
					<td class=""><?php echo $rs->inv_code; ?></td>
          <td class=""><?php echo $rs->customer_name; ?></td>
          <td class="text-right"><?php echo ($rs->doc_total <= 0 ? number($this->invoice_model->get_billed_amount($rs->code), 2) : number($rs->doc_total,2)); ?></td>
          <td class="text-center"><?php echo role_name($rs->role); ?></td>
          <td class="text-center"><?php echo $rs->user; ?></td>

        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
	$('#user').select2();
	$('#channels').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
