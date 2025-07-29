<?php $this->load->view('include/header'); ?>
<style>
	input[type=checkbox].ace.ace-switch.ace-switch-7 + .lbl::before {
		width:100px;
		height: 30px;
		line-height: 25px;
	}

	input[type=checkbox].ace.ace-switch.ace-switch-7 + .lbl::after {
		left:65px;
		height: 24px;
		line-height: 21px;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Description</label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Type</label>
		<select class="width-100 filter" name="type">
			<option value="all">ทั้งหมด</option>
			<option value="box" <?php echo is_selected('box', $type); ?>>กล่อง</option>
			<option value="bag" <?php echo is_selected('bag', $type); ?>>ซอง</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Active</label>
		<select class="width-100 filter" name="active">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>No</option>
		</select>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-80"></th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-250">Description</th>
					<th class="fix-width-80 text-center">ประเภท</th>
					<th class="fix-width-250">Dimention  W x L x H (cm)</th>
					<th class="fix-width-80 text-center">Active</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')"><i class="fa fa-trash"></i></button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center">
							<?php echo $rs->type == 'bag' ? 'ซอง' : ($rs->type == 'box' ? 'กล่อง' : 'Unknow'); ?>
						</td>
						<td class="middle"><?php echo "{$rs->width} X {$rs->length} X {$rs->height}"; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #ddd;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center">Create New Package</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
						<label>ขื่อ</label>
						<input type="text" class="width-100 add" id="package-name" value="" placeholder="Define package name" autocomplete="off" />
					</div>
					<div class="divider-hidden"></div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>ประเภท</label>
						<select class="width-100 add" id="package-type">
							<option value="box">กล่อง</option>
							<option value="bag">ซอง</option>
						</select>
					</div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>กว้าง (cm)</label>
						<input type="number" class="width-100 add text-center" id="package-width" value="" placeholder="Define package width" autocomplete="off" />
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>ยาว (cm)</label>
						<input type="number" class="width-100 add text-center" id="package-length" value="" placeholder="Define package length" autocomplete="off" />
					</div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>สูง (cm)</label>
						<input type="number" class="width-100 add text-center" id="package-height" value="" placeholder="Define package height" autocomplete="off" />
					</div>
					<div class="divider-hidden"></div>
					<div class="divider-hidden"></div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-default" data-dismiss="modal">Close</button>
				<?php if($this->pm->can_add) : ?>
					<button type="button" class="btn btn-white btn-primary btn-100" onclick="add()">Add</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #ddd;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center">Change Package</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<input type="hidden" id="edit-id" />
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
						<label>ขื่อ</label>
						<input type="text" class="width-100 edit" id="edit-package-name" value="" placeholder="Define package name" autocomplete="off" />
					</div>
					<div class="divider-hidden"></div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>ประเภท</label>
						<select class="width-100 edit" id="edit-package-type">
							<option value="box">กล่อง</option>
							<option value="bag">ซอง</option>
						</select>
					</div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>กว้าง (cm)</label>
						<input type="number" class="width-100 edit text-center" id="edit-package-width" value="" placeholder="Define package width" autocomplete="off" />
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>ยาว (cm)</label>
						<input type="number" class="width-100 edit text-center" id="edit-package-length" value="" placeholder="Define package length" autocomplete="off" />
					</div>

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label>สูง (cm)</label>
						<input type="number" class="width-100 edit text-center" id="edit-package-height" value="" placeholder="Define package height" autocomplete="off" />
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
						<label style="padding-top:5px; margin-bottom:0px;">
							<input class="ace ace-switch ace-switch-7" type="checkbox" value="1" id="edit-active"/>
							<span class="lbl margin-left-0" data-lbl="Inactive &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Active"></span>
						</label>
					</div>
					<div class="divider-hidden"></div>
					<div class="divider-hidden"></div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-default" data-dismiss="modal">Close</button>
				<?php if($this->pm->can_edit) : ?>
					<button type="button" class="btn btn-white btn-primary btn-100" onclick="update()">Update</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/package.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
