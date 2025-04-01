<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr/>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm" value="<?php echo $ds->code; ?>" readonly disabled />
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<label>ชื่อโซน</label>
		<input type="text" class="form-control input-sm" value="<?php echo $ds->name; ?>" readonly disabled />
	</div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label>คลังสินค้า</label>
		<input type="text" class="form-control input-sm" value="<?php echo $ds->warehouse_name; ?>" readonly disabled />
	</div>

	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>เจ้าของโซน</label>
		<select class="width-100" id="user_id" disabled>
			<option value="">ไม่มีเจ้าของ</option>
			<?php echo select_user_id($ds->user_id); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>POS API</label>
		<select class="form-control input-sm" id="pos-api" disabled>
			<option value="0" <?php echo is_selected('0', $ds->is_pos_api); ?>>No</option>
			<option value="1" <?php echo is_selected('1', $ds->is_pos_api); ?>>Yes</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>Pickface</label>
		<select class="form-control input-sm" id="is-pickface" disabled>
			<option value="0" <?php echo is_selected('0', $ds->is_pickface); ?>>No</option>
			<option value="1" <?php echo is_selected('1', $ds->is_pickface); ?>>Yes</option>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-u-update" onclick="saveUpdate()">Save</button>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-u-edit" onclick="editZone()">Edit</button>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-15">
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="row">
			<div class="col-lg-6 col-md-7 col-sm-8 col-xs-9 padding-5">
				<input type="text" class="form-control input-sm" id="search-box" placeholder="ค้นหาลูกค้า" autofocus>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 padding-5">
				<button type="button" class="btn btn-xs btn-primary" onclick="addCustomer()">
					<i class="fa fa-plus"></i> เพิ่มลูกค้า
				</button>
			</div>
			<div class="divider"></div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
				<table class="table table-striped border-1">
					<thead>
						<tr>
							<th class="fix-width-40 text-center">No.</th>
							<th class="fix-width-100">รหัสลูกค้า</th>
							<th class="fix-width-250">ชิ้อลูกค้า</th>
							<th class="fix-width-80"></th>
						</tr>
					</thead>
					<tbody id="cust-table">
		<?php if(!empty($customers)) : ?>
			<?php $no = 1; ?>
			<?php foreach($customers as $rs) : ?>
						<tr id="row-<?php echo $rs->id; ?>">
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->customer_code; ?></td>
							<td class="middle"><?php echo $rs->customer_name; ?></td>
							<td class="middle text-right">
					<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-xs btn-danger" onclick="deleteCustomer(<?php echo $rs->id; ?>, '<?php echo $rs->customer_code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
					<?php endif; ?>
							</td>
						</tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php else : ?>
						<tr>
							<td colspan="4" class="text-center">--- No customer ---</td>
						</tr>
		<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php if(!empty($ds->role == 8)) : ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="row">
				<div class="divider visible-xs"></div>
				<div class="col-lg-6 col-md-7 col-sm-8 col-xs-9 padding-5">
					<input type="text" class="form-control input-sm" id="empName" placeholder="ค้นหาพนักงาน">
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 padding-5">
					<button type="button" class="btn btn-xs btn-purple" onclick="addEmployee()">
						<i class="fa fa-plus"></i> เพิ่มพนักงาน
					</button>
				</div>
				<div class="divider"></div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
					<table class="table table-striped border-1">
						<thead>
							<tr>
								<th class="fix-width-40 text-center">No.</th>
								<th class="fix-width-200">พนักงาน</th>
								<th class="fix-width-80"></th>
							</tr>
						</thead>
						<tbody id="cust-table">
			<?php if(!empty($employees)) : ?>
				<?php $no = 1; ?>
				<?php foreach($employees as $rs) : ?>
							<tr id="emp-<?php echo $rs->id; ?>">
								<td class="middle text-center"><?php echo $no; ?></td>
								<td class="middle"><?php echo $rs->empName; ?></td>
								<td class="middle text-right">
						<?php if($this->pm->can_edit) : ?>
									<button type="button" class="btn btn-xs btn-danger" onclick="deleteEmployee(<?php echo $rs->id; ?>, '<?php echo $rs->empName; ?>')">
										<i class="fa fa-trash"></i>
									</button>
						<?php endif; ?>
								</td>
							</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
							<tr>
								<td colspan="4" class="text-center">--- No customer ---</td>
							</tr>
			<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
	</div>
	<?php endif; ?>
</div>


<input type="hidden" id="empID" value="">
<input type="hidden" id="customer_code" value="" >
<input type="hidden" id="zone_code" value="<?php echo $ds->code; ?>">
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	$('#user_id').select2();
</script>
<?php $this->load->view('include/footer'); ?>
