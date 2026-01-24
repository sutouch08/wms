<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_issue/style'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($doc->Status != 'D') : ?>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				Actions
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<?php if($doc->Status == 'C') : ?>
					<li class="success">
						<a href="javascript:sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send To SAP</a>
					</li>
				<?php endif; ?>

				<?php if($doc->Status == 'P' && $this->pm->can_edit) : ?>
					<li class="warning">
						<a href="javascript:edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> Edit</a>
					</li>
				<?php endif; ?>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->Status == 'P') : ?>
					<li class="success">
						<a href="javascript:close('<?php echo $doc->code; ?>')"><i class="fa fa-check"></i> Close Document</a>
					</li>
				<?php endif; ?>
				<?php if($this->pm->can_edit && ($doc->Status == 'C')) : ?>
					<li class="purple">
						<a href="javascript:rollBack('<?php echo $doc->code; ?>')"><i class="fa fa-history"></i> Rollback Status</a>
					</li>
				<?php endif; ?>				
				<?php if($this->pm->can_delete) : ?>
					<li class="danger">
						<a href="javascript:goCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> Cancel</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_issue/production_issue_view_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_issue/production_issue_view_header_right'); ?>
</div>
<hr class="padding-5">
<?php if($doc->Status == 'D') { $this->load->view('cancle_watermark'); } ?>

<?php $this->load->view('productions/production_issue/production_issue_view_details'); ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/productions/production_issue/production_issue.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_issue/production_issue_view.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
