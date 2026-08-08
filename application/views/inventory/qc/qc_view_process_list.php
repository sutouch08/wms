<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/qc/style_qc_list'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right hidden-xs">
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="goBack()">รอตรวจ</button>
	</div>
</div><!-- End Row -->
<hr class="" />
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">		
		<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100" name="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="width-100" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
			<label>พนักงาน/ผู้สั่งงาน</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-3-harf col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="width-100" name="channels" id="channels">
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

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label>ประเภท</label>
			<select class="form-control input-sm" name="role">
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

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label>Canceled</label>
			<select class="form-control input-sm" name="is_cancled" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_cancled); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_cancled); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>การจัดส่ง</label>
			<select class="width-100" name="id_sender" id="sender" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sender($id_sender); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
			<label>ช่วงข้อมูล</label>
			<select class="form-control input-sm" name="range">
				<option value="top"><?php echo number(getConfig('FILTER_RESULT_LIMIT')); ?> รายการล่าสุด</option>
				<option value="all" <?php echo is_selected('all', $range); ?>>ทั้งหมด</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
			<label class="not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
			<label class="not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>

<hr class="margin-top-15 hidden-xs">
<?php echo $this->pagination->create_links(); ?>
<?php if ($this->pm->can_add or $this->pm->can_edit) : ?>
	<div class="row">
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-9 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตรวจสินค้า</span>
				<input type="text" class="form-control input-sm text-center" id="order-code" placeholder="scan to pack" autofocus />
			</div>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="goToProcess()">ตรวจสินค้า</button>
		</div>
	</div>
	<hr class="margin-top-15">
<?php endif; ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover table-narrow border-1 table-listing">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-150 middle">เลขที่เอกสาร</th>
					<th class="fix-width-150 middle">เลขที่อ้างอิง</th>
					<th class="fix-width-150 middle">ช่องทาง</th>
					<th class="min-width-200 middle">ลูกค้า/พนักงาน</th>					
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($orders)) : ?>
					<?php $channels = get_channels_array(); ?>					
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach ($orders as $rs) : ?>
						<?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : (empty($rs->customer_name) ? $rs->empName : $rs->customer_name); ?>
						<?php $channels_name = empty($rs->channels_code) ? "" : (empty($channels[$rs->channels_code]) ? "" : $channels[$rs->channels_code]); ?>
						<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>						
						<tr id="row-<?php echo $rs->code; ?>" class="font-size-11">
							<td class="middle">
								<?php if ($this->pm->can_add or $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-white btn-minier btn-info" onClick="goQc('<?php echo $rs->code; ?>')">ตรวจสินค้า</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
							<td class="middle"><a href="javascript:viewOrderDetail('<?php echo $rs->code; ?>', '<?php echo $rs->role; ?>')"><?php echo $rs->code . $cn_text; ?></a></td>
							<td class="middle"><?php echo $rs->reference; ?></td>
							<td class="middle"><?php echo $channels_name; ?></td>
							<td class="middle"><?php echo $customer_name; ?></td>							
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="7" class="text-center">--- No content ---</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$('#user').select2();
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>