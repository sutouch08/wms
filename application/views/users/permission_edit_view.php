<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><i class="fa fa-lock"></i> <?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i>  กลับ</button>
		<?php if($this->permission === TRUE) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="savePermission()"><i class="fa fa-save"></i>  บันทึก</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr />
<form id="permissionForm" method="post" action="<?php echo $this->home; ?>/save_profile_permission">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive" style="height:600px; overflow:auto;">
			<input type="hidden" name="id_profile" value="<?php echo $data->id; ?>" />
			<table class="table table-striped table-bordered table-hover" style="min-width:700px; margin-bottom:0px;">
				<thead>
					<tr class="hide">
						<th class="min-width-100"></th>
						<th class="fix-width-100 text-center">ดู</th>
						<th class="fix-width-100 text-center">เพิ่ม</th>
						<th class="fix-width-100 text-center">แก้ไข</th>
						<th class="fix-width-100 text-center">ลบ</th>
						<th class="fix-width-100 text-center">อนุมัติ</th>
						<th class="fix-width-100 text-center">ทั้งหมด</th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($menus)) : ?>
						<?php foreach($menus as $groups) : ?>
							<?php 	$g_code = $groups['group_code']; ?>
							<tr class="font-size-14" style="background-color:#428bca73;">
								<td class="middle"><?php echo $groups['group_name']; ?></td>
								<td class="middle text-center">
									<input id="view-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupViewCheck($(this), '<?php echo $g_code; ?>')" />
									<span class="lbl">  ดู</span>
								</td>
								<td class="middle text-center">
									<input id="add-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupAddCheck($(this), '<?php echo $g_code; ?>' )">
									<span class="lbl">  เพิ่ม</span>
								</td>
								<td class="middle text-center">
									<input id="edit-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupEditCheck($(this), '<?php echo $g_code; ?>' )">
									<span class="lbl"> แก้ไข</span>
								</td>
								<td class="middle text-center">
									<input id="delete-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupDeleteCheck($(this), '<?php echo $g_code; ?>' )">
									<span class="lbl"> ลบ</span>
								</td>
								<td class="middle text-center">
									<input id="approve-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupApproveCheck($(this), '<?php echo $g_code; ?>' )">
									<span class="lbl"> อนุมัติ</span>
								</td>
								<td class="middle text-center">
									<input id="all-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupAllCheck($(this), '<?php echo $g_code; ?>' )">
									<span class="lbl">  ทั้งหมด</span>
								</td>

							</tr>

							<?php if(!empty($groups['menu'])) : ?>
								<?php foreach($groups['menu'] as $menu) : ?>
									<?php $code = $menu['menu_code']; ?>
									<?php $pm = $menu['permission']; ?>
									<tr>
										<td class="middle" style="padding-left:20px;"> -
											<?php echo $menu['menu_name']; ?>
											<input type="hidden" name="menu[<?php echo $code; ?>]" value="<?php echo $code; ?>"  />
										</td>
										<td class="middle text-center">
											<input id="view-<?php echo $code; ?>" name="view[<?php echo $code; ?>]" type="checkbox" class="ace view-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_view, 1); ?> value="1">
											<span class="lbl"></span>
										</td>
										<td class="middle text-center">
											<input id="add-<?php echo $code; ?>" name="add[<?php echo $code; ?>]" type="checkbox" class="ace add-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_add, 1); ?> value="1">
											<span class="lbl"></span>
										</td>
										<td class="middle text-center">
											<input id="edit-<?php echo $code; ?>" name="edit[<?php echo $code; ?>]" type="checkbox" class="ace edit-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_edit, 1); ?> value="1">
											<span class="lbl"></span>
										</td>
										<td class="middle text-center">
											<input id="delete-<?php echo $code; ?>" name="delete[<?php echo $code; ?>]" type="checkbox" class="ace delete-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_delete, 1); ?> value="1">
											<span class="lbl"></span>
										</td>
										<td class="middle text-center">
											<input id="approve-<?php echo $code; ?>" name="approve[<?php echo $code; ?>]" type="checkbox" class="ace approve-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_approve, 1); ?> value="1">
											<span class="lbl"></span>
										</td>
										<td class="middle text-center">
											<input id="all-<?php echo $code; ?>" type="checkbox" class="ace all all-<?php echo $g_code; ?>" onchange="allCheck($(this), '<?php echo $code; ?>')">
											<span class="lbl"></span>
										</td>

									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</form>

<script src="<?php echo base_url(); ?>scripts/users/permission.js"></script>

<?php $this->load->view('include/footer'); ?>
