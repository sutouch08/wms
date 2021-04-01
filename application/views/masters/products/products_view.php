<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>รหัส</label>
    <input type="text" class="width-100" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>กลุ่ม</label>
    <select class="form-control" name="group" id="group" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_group($group); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>กลุ่มย่อย</label>
		<select class="form-control" name="sub_group" id="sub_group" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_sub_group($sub_group); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>หมวดหมู่</label>
		<select class="form-control" name="category" id="category" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_category($category); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5 last">
    <label>ประเภท</label>
		<select class="form-control" name="kind" id="kind" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_kind($kind); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5 first">
    <label>ชนิด</label>
		<select class="form-control" name="type" id="type" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_type($type); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>ยี่ห้อ</label>
		<select class="form-control" name="brand" id="brand" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_brand($brand); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label>ปี</label>
		<select class="form-control" name="year" id="year" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-info btn-block" onclick="export_filter()"><i class="fa fa-file-excel-o"></i> Export</button>
  </div>

</div>
<hr class="margin-top-15">
</form>
<form id="export_filter_form" action="<?php echo $this->home; ?>/export_filter" method="post">
	<input type="hidden" name="export_code" id="export_code">
	<input type="hidden" name="export_name" id="export_name">
	<input type="hidden" name="export_group" id="export_group">
	<input type="hidden" name="export_sub_group" id="export_sub_group">
	<input type="hidden" name="export_category" id="export_category">
	<input type="hidden" name="export_kind" id="export_kind">
	<input type="hidden" name="export_type" id="export_type">
	<input type="hidden" name="export_brand" id="export_brand">
	<input type="hidden" name="export_year" id="export_year">
	<input type="hidden" name="token" id="token">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-5 middle text-center">รูปภาพ</th>
					<th class="width-20 middle">รหัส</th>
					<th class="width-10 middle">ราคา</th>
					<th class="width-10 middle">กลุ่ม</th>
					<th class="width-10 middle">หมวดหมู่</th>
					<th class="width-10 middle">ประเภท</th>
					<th class="width-10 middle">ชนิด</th>
					<th class="width-5 middle text-center">ปี</th>
					<th class="width-5 middle text-center">ขาย</th>
					<th class="width-5 middle text-center">ใช้งาน</th>
					<th class="width-5 middle text-center">API</th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center">
							<img src="<?php echo get_cover_image($rs->code, 'mini'); ?>" />
						</td>
						<td class="middle">
							<?php echo $rs->code; ?>
							<?php if(!empty($rs->old_code)) : ?>
							<?php echo '  |  '.$rs->old_code; ?>
							<?php endif; ?>
						</td>
						<td class="middle"><?php echo number($rs->price, 2); ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->category; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle text-center"><?php echo $rs->year; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->sell); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->api); ?></td>
						<td class="middle text-right">
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

<script src="<?php echo base_url(); ?>scripts/masters/products.js"></script>

<?php $this->load->view('include/footer'); ?>
