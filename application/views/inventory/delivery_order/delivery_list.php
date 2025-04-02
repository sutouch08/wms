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
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า/ผู้เบิก</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>พนักงาน/ผู้สั่งงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รูปแบบ</label>
		<select class="form-control input-sm" name="role" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_order_role($role); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คลังสินค้า</label>
		<select class="form-control input-sm" name="warehouse" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_sell_warehouse($warehouse); ?>
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
    <table class="table table-striped border-1 dataTable" style="min-width:1300px;">
      <thead>
        <tr>
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
					<th class="fix-width-100 text-right"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>
			<?php $bg = $rs->is_hold ? 'background-color:#fde4e4;' : ''; ?>
			<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
        <tr class="font-size-12" id="row-<?php echo $rs->code; ?>" style="<?php echo $bg; ?>">

          <td class="text-center pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $no; ?>
          </td>

          <td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo thai_date($rs->date_add); ?>
          </td>

					<td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo (empty($rs->shipped_date) ? "" : thai_date($rs->shipped_date, FALSE)); ?>
          </td>

          <td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->code . $cn_text; ?>            
          </td>
					<td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->reference; ?>
          </td>

					<td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
						<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
							<?php echo $rs->empID; ?>
						<?php else: ?>
							<?php echo $rs->customer_code; ?>
						<?php endif; ?>
          </td>

          <td class="pointer" onclick="goDetail('<?php echo $rs->code; ?>')">
						<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
							<?php echo $rs->empName; ?>
						<?php else: ?>
            	<?php echo $rs->customer_name; ?>
						<?php endif; ?>
          </td>

          <td class="pointer text-center" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo ($rs->doc_total <= 0 ? number($this->orders_model->get_order_total_amount($rs->code), 2) : number($rs->doc_total,2)); ?>
          </td>

          <td class="pointer text-center" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo role_name($rs->role); ?>
          </td>

          <td class="pointer text-center hide-text" onclick="goDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->user; ?>
          </td>
					<td class="text-right">
            <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
							<?php if($rs->is_hold ==0 OR $this->_SuperAdmin) : ?>
								<button type="button" class="btn btn-xs btn-primary" onclick="confirmBill('<?php echo $rs->code; ?>')">เปิดบิล</button>
							<?php endif; ?>
						<?php endif; ?>
          </td>

        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function confirmBill(order_code){

	load_in();

	$.ajax({
		url: HOME + 'confirm_order',
		type:'POST',
		cache:'false',
		data:{
			'order_code' : order_code
		},
		success:function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success'){
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				$('#row-'+order_code).remove();

			}else {
				swal('Error!', rs, 'error');
			}
		}
	});
}
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
