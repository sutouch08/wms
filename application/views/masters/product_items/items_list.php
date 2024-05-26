<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
				<button type="button" class="btn btn-sm btn-primary top-btn" onclick="getUploadFile()"><i class="fa fa-file-excel"></i> เพิ่มใหม่ (Upload)</button>
			<?php endif; ?>
			<button type="button" class="btn btn-sm btn-purple top-btn" onclick="getTemplate()"><i class="fa fa-download"></i>template file</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="width-100" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>บาร์โค้ด</label>
    <input type="text" class="width-100" name="barcode" id="barcode" value="<?php echo $barcode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สี</label>
    <input type="text" class="width-100" name="color" id="color" value="<?php echo $color; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ไซส์</label>
    <input type="text" class="width-100" name="size" id="size" value="<?php echo $size; ?>" />
  </div>


	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>กลุ่ม</label>
    <select class="form-control" name="group" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_group($group); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>กลุ่มย่อย</label>
		<select class="form-control" name="sub_group" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_sub_group($sub_group); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>หมวดหมู่</label>
		<select class="form-control" name="category" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_category($category); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ประเภท</label>
		<select class="form-control" name="kind" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_kind($kind); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ชนิด</label>
		<select class="form-control" name="type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_type($type); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ยี่ห้อ</label>
		<select class="form-control" name="brand" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_product_brand($brand); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>คอลเล็คชั่น</label>
		<select class="form-control" name="collection" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_all_collection($collection); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ปี</label>
		<select class="form-control" name="year" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>Active</label>
		<select class="form-control" name="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>No</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:400px; overflow:auto;">
		<table class="table table-striped table-bordered table-hover" style="min-width:2250px;">
			<thead>
				<tr>
					<th class="fix-width-100 text-center"></th>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-150 middle text-center">บาร์โค้ด</th>
					<th class="fix-width-200 middle text-center">รหัส</th>
					<th class="fix-width-150 middle text-center">รุ่น</th>
					<th class="fix-width-60 middle text-center">สี</th>
					<th class="fix-width-60 middle text-center">ไซส์</th>
					<th class="fix-width-80 middle text-center">ราคา</th>
					<th class="fix-width-150 middle text-center">กลุ่ม</th>
					<th class="fix-width-150 middle text-center">กลุ่มหลัก</th>
					<th class="fix-width-150 middle text-center">กลุ่มย่อย</th>
					<th class="fix-width-150 middle text-center">หมวดหมู่</th>
					<th class="fix-width-150 middle text-center">ประเภท</th>
					<th class="fix-width-150 middle text-center">ชนิด</th>
					<th class="fix-width-150 middle text-center">ยี่ห้อ</th>
					<th class="fix-width-150 middle text-center">คอลเล็คชั่น</th>
					<th class="fix-width-80 middle text-center">ปี</th>
					<th class="fix-width-40 middle text-center">ขาย</th>
					<th class="fix-width-40 middle text-center">Active</th>
					<th class="fix-width-150 middle text-center">รหัสเก่า</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $no; ?>" class="font-size-12">
						<td class="middle text-center">
							<div class="btn-group">
								<button data-toggle="dropdown" class="btn btn-sm btn-primary btn-white dropdown-toggle" aria-expanded="false">
									Action
									<i class="ace-icon fa fa-angle-down icon-on-right"></i>
								</button>

								<ul class="dropdown-menu">
									<?php if($this->wmsApi) : ?>
										<li>
											<a href="javascript:sendToWms('<?php echo $rs->code; ?>')"><i class="fa fa-send green"></i> Send To WMS</a>
										</li>
									<?php endif; ?>
									<?php if($this->sokoApi) : ?>
										<li>
											<a href="javascript:sendToSoko('<?php echo $rs->code; ?>')"><i class="fa fa-send blue"></i> Send To Soko Jung</a>
										</li>
									<?php endif; ?>
									<?php if($this->pm->can_add) : ?>
										<li>
											<a href="javascript:duplicate('<?php echo $rs->id; ?>')"><i class="fa fa-copy purple"></i> Duplicate</a>
										</li>
									<?php endif; ?>

									<?php if($this->pm->can_edit) : ?>
										<li>
											<a href="javascript:getEdit('<?php echo $rs->id; ?>')"><i class="fa fa-pencil orange"></i> Edit</a>
										</li>
									<?php endif; ?>

									<?php if($this->pm->can_delete) : ?>
										<li>
											<a href="javascript:getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>', <?php echo $no; ?>)"><i class="fa fa-trash red"></i> Delete</a>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->barcode; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->style_code; ?></td>
						<td class="middle text-center"><?php echo $rs->color_code; ?></td>
						<td class="middle text-center"><?php echo $rs->size_code; ?></td>
						<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->main_group; ?></td>
						<td class="middle"><?php echo $rs->sub_group; ?></td>
						<td class="middle"><?php echo $rs->category; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle"><?php echo $rs->brand; ?></td>
						<td class="middle"><?php echo $rs->collection; ?></td>
						<td class="middle text-center"><?php echo $rs->year; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->can_sell); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle"><?php echo $rs->old_code; ?></td>

					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<form id="edit-form" method="post" action="<?php echo $this->home; ?>/edit">
	<input type="hidden" id="item-code" name="itemCode" />
</form>
<?php $this->load->view('masters/product_items/import_items'); ?>

<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
