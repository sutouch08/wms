<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
    <label>Reason</label>
    <input type="text" class="width-100 search" name="name" value="<?php echo $name; ?>" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<select class="width-100 filter" name="active">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
	</div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-120"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="min-width-250 middle">Reason</th>
					<th class="fix-width-60 middle text-center">Status</th>
					<th class="fix-width-150 middle">Create By</th>
					<th class="fix-width-150 middle text-center">Create at</th>
					<th class="fix-width-150 middle text-center">Update By</th>
					<th class="fix-width-150 middle text-center">Update at</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->id; ?>">
						<td class="">
							<button type="button" class="btn btn-mini btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
								<i class="fa fa-eye"></i>
							</button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle"><?php echo $rs->user; ?></td>
						<td class="middle"><?php echo thai_date($rs->date_add, TRUE); ?></td>
						<td class="middle"><?php echo $rs->update_user; ?></td>
						<td class="middle"><?php echo ( ! empty($rs->date_upd) ? thai_date($rs->date_upd, TRUE) : ""); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>



<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" style="padding-top:5px;">
              <div class="row">
              	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              		<label>Reason</label>
									<textarea class="form-control input-sm" id="v-name" readonly style="min-height:30px;"></textarea>
              	</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label>Status</label>
									<input type="text" class="form-control input-sm input-small text-center" id="v-status" readonly />
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<label>Create By</label>
									<input type="text" class="form-control input-sm text-center" id="create-by" readonly />
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<label>Create at</label>
									<input type="text" class="form-control input-sm text-center" id="create-at" readonly />
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<label>Update By</label>
									<input type="text" class="form-control input-sm text-center" id="update-by" readonly />
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<label>Update at</label>
									<input type="text" class="form-control input-sm text-center" id="update-at" readonly />
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              		<label>Prev. Reason</label>
									<textarea class="form-control input-sm" id="prev-name" readonly style="min-height:30px;"></textarea>
              	</div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="closeModal('detailModal')" >Close</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/cancel_reason.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
