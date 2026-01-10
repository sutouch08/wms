<?php $this->load->view('include/header'); ?>
<?php $this->load->view('transfer/style'); ?>
<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 margin-top-5">
			<h3 class="title"><?php echo $this->title; ?></h3>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
			<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status == 1) : ?>
				<button type="button" class="btn btn-white btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
				<?php if($this->pm->can_edit) : ?>
					<button type="button" class="btn btn-white btn-danger top-btn" onclick="unSave()"><i class="fa fa-exclamation-triangle"></i> ยกเลิกการบันทึก</button>
				<?php endif; ?>
			<?php endif; ?>
			<?php if(($doc->status == -1 OR $doc->status == 0) && $this->pm->can_add OR $this->pm->can_edit) : ?>
				<?php if(($doc->status == -1 OR $doc->status == 0) && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
					<?php if(getConfig('ALLOW_IMPORT_TRANSFER')) : ?>
						<div class="btn-group">
							<button type="button" data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle margin-top-5" aria-expanded="false">
								<i class="ace-icon fa fa-cloud icon-on-left"></i> Import <i class="ace-icon fa fa-angle-down icon-on-right"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li class="primary">
									<a href="javascript:getUploadFile()"><i class="fa fa-cloud-upload"></i> &nbsp; Import Excel</a>
								</li>
								<li class="purple">
									<a href="javascript:getTemplate()"><i class="fa fa-cloud-download"></i> &nbsp; Template file</a>
								</li>
							</ul>
						</div>
					<?php endif; ?>

					<div class="btn-group">
		        <button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
		          <i class="ace-icon fa fa-save icon-on-left"></i>
		          บันทึก
		          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
		        </button>
		        <ul class="dropdown-menu dropdown-menu-right">
		          <li class="primary">
		            <a href="javascript:save()">บันทึกรับเข้าทันที</a>
		          </li>
							<li class="purple">
		            <a href="javascript:saveAsRequest()">บันทึกรอรับ</a>
		          </li>
		        </ul>
		      </div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div><!-- End Row -->
<hr/>
<?php
	$this->load->view('transfer/transfer_edit_header');
	$this->load->view('transfer/transfer_detail');
?>

<input type="hidden" id="from-zone-code" value="" />
<input type="hidden" id="to-zone-code" value="" />

<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 <h4 class="modal-title">Import File</h4>
			</div>
			<div class="modal-body">
				<form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-sm-9">
						<button type="button" class="btn btn-sm btn-primary btn-block" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
					</div>

					<div class="col-sm-3">
						<button type="button" class="btn btn-sm btn-info" onclick="uploadfile()"><i class="fa fa-cloud-upload"></i> นำเข้า</button>
					</div>
				</div>
				<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
				</form>
			 </div>
			<div class="modal-footer">

			</div>
	 </div>
 </div>
</div>

<?php else : ?>
<?php $this->load->view('deny_page'); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_edit.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
