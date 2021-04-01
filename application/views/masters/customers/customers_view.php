<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right">
				<button type="button" class="btn btn-sm btn-info" onclick="syncData()"><i class="fa fa-refresh"></i> Sync</button>
				<?php if(get_cookie('id_profile') == -987654321) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="syncAllData()"><i class="fa fa-refresh"></i> Sync All</button>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>กลุ่มลูกค้า</label>
    <select class="form-control input-sm filter" name="group" id="customer_group">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_group($group); ?>
			<option value="NULL" <?php echo is_selected('NULL', $group); ?>>ไม่มี</option>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>ประเภทลูกค้า</label>
    <select class="form-control input-sm filter" name="kind" id="customer_kind">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_kind($kind); ?>
			<option value="NULL" <?php echo is_selected('NULL', $kind); ?>>ไม่มี</option>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>ชนิดลูกค้า</label>
    <select class="form-control input-sm filter" name="type" id="customer_type">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_type($type); ?>
			<option value="NULL" <?php echo is_selected('NULL', $type); ?>>ไม่มี</option>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>เกรดลูกค้า</label>
    <select class="form-control input-sm filter" name="class" id="customer_class">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_class($class); ?>
			<option value="NULL" <?php echo is_selected('NULL', $class); ?>>ไม่มี</option>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>เขตการขาย</label>
    <select class="form-control input-sm filter" name="area" id="customer_area">
			<option value="">ทั้งหมด</option>
			<?php echo select_customer_area($area); ?>
			<option value="NULL" <?php echo is_selected('NULL', $area); ?>>ไม่มี</option>
		</select>
  </div>

  <div class="col-sm-1 col-1-harf padding-5 last">
    <label class="display-block not-show">buton</label>
		<div class="btn-group width-100">
			<button type="submit" class="btn btn-sm btn-primary width-50">Search</button>
			<button type="button" class="btn btn-sm btn-warning width-50" onclick="clearFilter()">Reset</button>
		</div>
  </div>

</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle">รหัส</th>
					<th class="width-35 middle">ชื่อ</th>
					<th class="width-10 middle">กลุ่ม</th>
					<th class="width-10 middle">ประเภท</th>
					<th class="width-10 middle">ชนิด</th>
					<th class="width-5 middle">เกรด</th>
					<th class="width-5 middle text-center">active</th>
					<th class="width-10"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr style="font-size:11px;">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle">
							<?php echo $rs->code; ?>
							<?php if(!empty($rs->old_code)) : ?>
							<?php echo ' | '.$rs->old_code; ?>
							<?php endif; ?>
						</td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle"><?php echo $rs->class; ?></td>
						<td class="middle text-center">
							<?php echo is_active($rs->active); ?>
						</td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>

<?php $this->load->view('include/footer'); ?>
