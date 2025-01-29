<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="getSample()">
			<i class="fa fa-download"></i> Template file
		</button>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
			<?php if($doc->is_api == 0) : ?>
				<button type="button" class="btn btn-sm btn-primary top-btn" onclick="getUploadFile()">
					Import Excel
				</button>
				<?php if(empty($doc->ref_code)) : ?>
					<button type="button" class="btn btn-sm btn-info top-btn" onclick="getActiveCheckList()">
						เอกสารกระทบยอด
					</button>
				<?php endif; ?>
			<?php endif; ?>
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success top-btn" onclick="saveConsign()">
					<i class="fa fa-save"></i> บันทึก
				</button>
			<?php endif; ?>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/update">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customerCode" id="customerCode" value="<?php echo $doc->customer_code; ?>" disabled>
	</div>
  <div class="col-lg-7 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Tax Status</label>
		<input type="text" class="form-control input-sm text-center" id="tax-status" value="<?php echo $doc->tax_status == 1 ? 'Y' : 'N'; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>E-TAX</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->is_etax ? 'Y' : 'N'; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center edit" id="zone_code" name="zone_code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>

	<div class="col-lg-8 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
    <label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
  </div>

	<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled />
	</div>

<?php if($doc->is_api == 0) : ?>
	<?php if($this->pm->can_edit) : ?>
	  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
	    <label class="display-block not-show">Submit</label>
	    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"></i class="fa fa-pencil"></i> แก้ไข</button>
	    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
	  </div>
	<?php endif; ?>
<?php else : ?>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>POS Ref.</label>
		<input type="text" class="form-control input-sm" id="pos-ref" value="<?php echo $doc->pos_ref; ?>" disabled />
	</div>
<?php endif; ?>
</div>
<?php if($doc->tax_status == 1) : ?>
	<hr/>
	<div class="row">
		<div class="divider"></div>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-8 padding-5">
			<label>ชื่อสำหรับออกใบกำกับภาษี</label>
			<input type="text" class="width-100" id="name" value="<?php echo $doc->name; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
			<label>Tax ID</label>
			<input type="text" class="width-100 text-center" id="tax-id" value="<?php echo $doc->tax_id; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1 col-xs-4 padding-5">
			<label>รหัสสาขา</label>
			<input type="text" class="width-100 text-center" id="branch-code" value="<?php echo $doc->branch_code; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>ชื่อสาขา</label>
			<input type="text" class="width-100 text-center" id="branch-name" value="<?php echo $doc->branch_name; ?>" disabled/>
		</div>
		<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>เบอร์โทร</label>
			<input type="text" class="width-100 text-center" id="phone" value="<?php echo $doc->phone; ?>" disabled/>
		</div>
		<div class="col-lg-4 col-md-7 col-sm-4-harf col-xs-12 padding-5">
			<label>ที่อยุ่</label>
			<input type="text" class="width-100" id="address" value="<?php echo $doc->address; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
			<label>ตำบล</label>
			<input type="text" class="width-100" id="sub-district" value="<?php echo $doc->sub_district; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
			<label>อำเภอ</label>
			<input type="text" class="width-100" id="district" value="<?php echo $doc->district; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
			<label>จังหวัด</label>
			<input type="text" class="width-100" id="province" value="<?php echo $doc->province; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>ไปรษณีย์</label>
			<input type="text" class="width-100" id="postcode" value="<?php echo $doc->postcode; ?>" disabled/>
		</div>
		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>Email</label>
			<input type="text" class="width-100 text-center" value="<?php echo $doc->email; ?>" disabled />
		</div>
	</div>
<?php endif; ?>
<hr class="margin-top-15">
<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="auz" id="auz" value="<?php echo $auz; ?>">
</form>
<?php if($doc->is_api == 0) : ?>
	<?php $this->load->view('account/consign_order/consign_order_control'); ?>
<?php endif; ?>
<?php if($doc->is_api == 1) : ?>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="red text-center">** เอกสารนี้ถูกสร้างโดยระบบ POS จึงไม่สามารถแก้ไขรายการได้ **</p>
		</div>
	</div>
<?php endif; ?>
<?php $this->load->view('account/consign_order/consign_order_detail'); ?>


<div class="modal fade" id="check-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:400px;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="check-list-body">

			 </div>
			<div class="modal-footer">
			 <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			</div>
	 </div>
 </div>
</div>

<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:350px;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 <h4 class="modal-title">นำเข้าไฟล์ Excel</h4>
			</div>
			<div class="modal-body">
				<form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-sm-9 col-xs-9 padding-5">
						<button type="button" class="btn btn-sm btn-primary btn-block" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
					</div>

					<div class="col-sm-3 col-xs-3 padding-5">
						<button type="button" class="btn btn-sm btn-info btn-block" onclick="uploadfile()"><i class="fa fa-cloud-upload"></i> นำเข้า</button>
					</div>
				</div>
				<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
				<input type="hidden" name="555" />
				</form>
			 </div>
			<div class="modal-footer">

			</div>
	 </div>
 </div>
</div>


<script id="check-list-template" type="text/x-handlebarsTemplate">
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="width-30 text-center">วันที่</th>
					<th class="width-40 text-center">เอกสาร</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="check-list-table">
		 {{#each this}}
			 {{#if nodata}}
				 <tr>
					 <td colspan="3" class="text-center"><h4>ไม่พบรายการ</h4></td>
				 </tr>
			 {{else}}
					<tr>
						<td class="middle text-center">{{date_add}}</td>
						<td class="middle text-center">{{code}}</td>
						<td class="middle text-center">
							<button type="button" class="btn btn-xs btn-info btn-block" onclick="loadCheckDiff('{{code}}')">นำเข้ายอดต่าง</button>
						</td>
					</tr>
				{{/if}}
		 {{/each}}
			</tbody>
		</table>
	</div>
</div>
</script>

<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
