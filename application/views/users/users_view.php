<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="newUser()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5">
    <label>User name</label>
    <input type="text" class="width-100" name="uname" value="<?php echo $uname; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>Display name</label>
    <input type="text" class="width-100" name="dname" value="<?php echo $dname; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>Profile</label>
    <input type="text" class="width-100" name="profile" value="<?php echo $profile; ?>" />
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 padding-5">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="width-10 middle">User name</th>
					<th class="width-20 middle">Display name</th>
					<th class="width-15 middle">Profile</th>
					<th class="width-10 middle text-center">Create at</th>
					<th class="width-10 middle text-center">Status</th>
					<th class="width-10 middle text-center">Pwd changed</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<?php $active = $rs->active == 1 ? '' : 'hide'; ?>
					<?php $disActive = $rs->active == 0 ? '' : 'hide'; ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle"><?php echo $rs->dname; ?></td>
						<td class="middle"><?php echo $rs->pname; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
						<td class="middle text-center">
								<span class="label label-sm label-success arrowed <?php echo $active; ?>" id="label-active-<?php echo $rs->id; ?>">Actived</span>
								<span class="label labes-sm label-warning arrowed <?php echo $disActive; ?>" id="label-disActive-<?php echo $rs->id; ?>">Suspended</span>
						</td>
						<td class="middle text-center"><?php echo empty($rs->last_pass_change) ? "" : thai_date($rs->last_pass_change, FALSE, '/'); ?></td>
						<td class="text-right">
							<?php if(($this->pm->can_edit && $rs->id_profile >= 0) OR (get_cookie('id_profile') == -987654321)) : ?>
								<button type="button" class="btn btn-minier btn-success <?php echo $disActive; ?>"
									title="Click to activeate this user"
									id="btn-active-<?php echo $rs->id; ?>"
									onclick="setActive(<?php echo $rs->id; ?>)">
									<i class="fa fa-check"></i>
								</button>
								<button type="button" class="btn btn-minier btn-danger <?php echo $active; ?>"
									title="Click here to suspend this user"
									id="btn-disActive-<?php echo $rs->id; ?>"
									onclick="disActive(<?php echo $rs->id; ?>)">
									<i class="fa fa-times"></i>
								</button>
								<button type="button" class="btn btn-minier btn-info" title="Reset password" onclick="getReset(<?php echo $rs->id; ?>)">
									<i class="fa fa-key"></i>
								</button>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if(($this->pm->can_delete && $rs->id_profile > 0) OR (get_cookie('id_profile') == -987654321)) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->uname; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/users/users.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
