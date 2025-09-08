<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่อ้างอิง</label>
    <input type="text" class="form-control input-sm search" name="reference"  value="<?php echo $reference; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า/ผู้เบิก</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
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

	<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คลังสินค้า</label>
		<select class="width-100 filter" name="warehouse" id="warehouse">
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
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Flag</label>
		<select class="form-control input-sm" name="is_hold" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $is_hold); ?>>On Hold</option>
			<option value="0" <?php echo is_selected('0', $is_hold); ?>>Ready</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Canceled</label>
		<select class="form-control input-sm" name="is_cancled" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $is_cancled); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $is_cancled); ?>>No</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Dispatch</label>
		<select class="form-control input-sm" name="dispatch" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $dispatch); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $dispatch); ?>>No</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
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

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">X</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">X</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
	</div>
</div>
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
<input type="hidden" name="search" value="1" />

<hr class="margin-top-15">
</form>


<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by == '' ? "" : ($order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<?php $sort_code = $order_by == '' ? '' : ($order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:1350px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-150"></th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-100">วันที่</th>
					<th class="fix-width-100">วันที่จัดส่ง</th>
          <th class="fix-width-150">เลขที่เอกสาร</th>
					<th class="fix-width-150">เลขที่อ้างอิง</th>
					<th class="fix-width-100">รหัสลูกค้า</th>
          <th class="min-width-250">ลูกค้า</th>
          <th class="fix-width-100 text-center">ยอดเงิน</th>
          <th class="fix-width-100 text-center">รูปแบบ</th>
          <th class="fix-width-100 text-center">พนักงาน</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>
			<?php $bg = $rs->is_hold ? 'background-color:#fde4e4;' : ''; ?>
			<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
        <tr class="font-size-11" id="row-<?php echo $rs->code; ?>" style="<?php echo $bg; ?>">
					<td class="text-right">
						<button type="button" class="btn btn-mini btn-info" onclick="goDetail('<?php echo $rs->code; ?>')">รายละเอียด</button>

            <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
							<button type="button" class="btn btn-mini btn-primary" onclick="confirmBill('<?php echo $rs->code; ?>')">เปิดบิล</button>
						<?php endif; ?>
          </td>
          <td class="text-center"><?php echo $no; ?></td>
          <td class=""><?php echo thai_date($rs->date_add); ?></td>
					<td class=""><?php echo (empty($rs->shipped_date) ? "" : thai_date($rs->shipped_date, FALSE)); ?></td>
          <td class=""><a href="javascript:viewOrderDetail('<?php echo $rs->code; ?>', '<?php echo $rs->role; ?>')"><?php echo $rs->code . $cn_text; ?></a></td>
					<td class=""><?php echo $rs->reference; ?></td>
					<td class="">
						<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
							<?php echo $rs->empID; ?>
						<?php else: ?>
							<?php echo $rs->customer_code; ?>
						<?php endif; ?>
          </td>
          <td class="">
						<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
							<?php echo $rs->empName; ?>
						<?php else: ?>
            	<?php echo $rs->customer_name; ?>
						<?php endif; ?>
          </td>
          <td class="text-center"><?php echo ($rs->doc_total <= 0 ? number($this->orders_model->get_order_total_amount($rs->code), 2) : number($rs->doc_total,2)); ?></td>
          <td class="text-center"><?php echo role_name($rs->role); ?></td>
          <td class="text-center hide-text"><?php echo $rs->user; ?></td>

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

function confirmBill(order_code){
	load_in();

	$.ajax({
		url: HOME + 'confirm_order',
		type:'POST',
		cache:'false',
		data:{
			'order_code' : order_code
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success'){
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				$('#row-'+order_code).remove();
			}
			else {
				beep();
				showError(rs);
			}
		},
		error:function(rs) {
			beep();
			showError(rs);
		}
	});
}
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
