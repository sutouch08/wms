<?php $this->load->view('include/header'); ?>
<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5 margin-top-5">
			<h3 class="title"><?php echo $this->title; ?></h3>
		</div>
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-5 text-right">
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status == 1) : ?>
				<button type="button" class="btn btn-sm btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
				<?php if($this->pm->can_edit) : ?>
					<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unSave()"><i class="fa fa-exclamation-triangle"></i> ยกเลิกการบันทึก</button>
				<?php endif; ?>
			<?php endif; ?>
			<?php if(($doc->status == -1 OR $doc->status == 0) && $this->pm->can_add OR $this->pm->can_edit) : ?>

				<?php if(($doc->status == -1 OR $doc->status == 0) && $barcode === TRUE) : ?>
					<button type="button" class="btn btn-sm btn-primary top-btn" onclick="goUseKeyboard()">คีย์มือ</button>
				<?php endif; ?>


				<?php if(($doc->status == -1 OR $doc->status == 0) && $barcode === FALSE) : ?>
					<!-- <button type="button" class="btn btn-sm btn-primary" onclick="goUseBarcode()">ใช้บาร์โค้ด</button> -->
				<?php endif; ?>

				<?php if(($doc->status == -1 OR $doc->status == 0) && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
					<?php if(getConfig('ALLOW_IMPORT_TRANSFER')) : ?>
						<button type="button" class="btn btn-sm btn-primary top-btn" onclick="getUploadFile()"><i class="fa fa-file-excel-o"></i> Import Excel</button>
						<button type="button" class="btn btn-sm btn-purple top-btn" onclick="getTemplate()"><i class="fa fa-download"></i> ไฟล์ Template</button>
					<?php endif; ?>
					<?php if($doc->is_wms == '-1') : ?>
						<button type="button" class="btn btn-sm btn-primary top-btn" onclick="saveAsRequest()">บันทึกรอรับ</button>
					<?php endif; ?>
					<?php if($doc->is_wms == 0) : ?>
						<!-- <button type="button" class="btn btn-sm btn-primary top-btn" onclick="saveAsRequest()">บันทึกรอรับ</button> -->
						<button type="button" class="btn btn-sm btn-success top-btn" onclick="save()">บันทึกรับทันที</button>
					<?php endif; ?>
					<?php if($doc->is_wms >= 1) : ?>
						<button type="button" class="btn btn-sm btn-success top-btn" onclick="save()">บันทึก</button>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div><!-- End Row -->
<hr/>
<?php
	$this->load->view('transfer/transfer_edit_header');

	if($barcode === TRUE)
	{
		$this->load->view('transfer/transfer_detail_barcode');
	}
	else
	{
		$this->load->view('transfer/transfer_detail');
	}
?>

<input type="hidden" name="from_zone_code" id="from_zone_code" value="" />
<input type="hidden" name="to_zone_code" id="to_zone_code" value="" />

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
