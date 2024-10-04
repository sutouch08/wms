<?php $this->load->view('include/header'); ?>
<style>
	#detail-table>tr:first-child {
	    color: blue;
	}
</style>
<div class="row">
	<div class="col-lg-4 col-md-3 col-sm-3 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-8 col-md-9 col-sm-9 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i>
				กลับ</button>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0 && $doc->valid == 0) : ?>
					<button type="button" class="btn btn-xs btn-primary" onclick="reloadStock()">
						<i class="fa fa-refresh"></i> โหลดยอดตั้งต้นใหม่
					</button>
					<?php if($doc->is_wms == 0) : ?>
						<button type="button" class="btn btn-xs btn-success" onclick="closeCheck()">
							<i class="fa fa-bolt"></i> ปิดการตรวจนับ
						</button>
					<?php endif; ?>
				<?php else : ?>
					<!--- consign_check_detail.js --->
					<button type="button" class="btn btn-xs btn-danger" onclick="openCheck()">
						<i class="fa fa-bolt"></i> เปิดการตรวจนับ
					</button>
				<?php endif; ?>

				<?php if($this->pm->can_delete && $doc->status == 0 && $doc->valid == 0) : ?>
					<!--- consign_check_detail.js --->
					<button type="button" class="btn btn-xs btn-danger" onclick="clearDetails()">
						<i class="fa fa-trash"></i> เคลียร์รายการทั้งหมด
					</button>
					<button type="button" class="btn btn-xs btn-danger" onclick="goDelete()">
						<i class="fa fa-trash"></i> ยกเลิกเอกสาร
					</button>
				<?php endif; ?>
			</p>
		</div>
	</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center e" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>" disabled/>
	</div>
	<div class="col-lg-6 col-md-7 col-sm-6-harf col-xs-12 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm e" name="customer_name" id="customer_name" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>ช่องทาง</label>
		<select class="form-control input-sm e" id="is_wms" disabled>
			<?php if($this->wmsApi OR $doc->is_wms == 1) : ?>
				<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>Pioneer</option>
			<?php endif; ?>
			<?php if($this->sokoApi OR $doc->is_wms == 2) : ?>
				<option value="2" <?php echo is_selected('2', $doc->is_wms); ?>>SOKOCHAN</option>
			<?php endif; ?>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>Warrix</option>
		</select>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
		<label>รหัสโซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>
	<div class="col-lg-4-harf col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone_name" id="zone_name" value="<?php echo $doc->zone_name; ?>" disabled/>
	</div>

	<div class="col-lg-4-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">edit</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
  </div>
</div>

<input type="hidden" name="check_code" id="check_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="id_box" id="id_box">
<hr class="margin-top-15" />

<div class="row">
    <div class="col-sm-2 padding-5">
        <label>บาร์โค้ดกล่อง</label>
        <input type="text" class="form-control input-sm text-center box" id="box-code" placeholder="ยิงบาร์โค้ดกล่อง"
            autofocus>
    </div>
    <div class="col-sm-1 padding-5">
        <label>จำนวน</label>
        <input type="number" class="form-control input-sm text-center item" id="qty-box" value="1" disabled>
    </div>
    <div class="col-sm-2 padding-5">
        <label>บาร์โค้ดสินค้า</label>
        <input type="text" class="form-control input-sm text-center item" id="barcode" placeholder="ยิงบาร์โค้ดสินค้า"
            disabled>
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
        <label class="display-block not-show">changebox</label>
        <button type="button" class="btn btn-xs btn-info btn-block item" id="btn-change-box" onclick="changeBox()"
            disabled><i class="fa fa-refresh"></i> เปลี่ยนกล่อง</button>
    </div>
    <div class="col-sm-3 col-3-harf">
        <h4 class="pull-right" style="margin-top:15px;" id="box-label">จำนวนในกล่อง</h4>
    </div>
    <div class="col-sm-2 padding-5">
        <div class="title middle text-center"
            style="height:55px; background-color:black; color:white; padding:10px; margin-top:0px;">
            <h4 class="inline text-center" id="box-qty">0</h4>
        </div>
    </div>
</div>
<hr>
<?php $this->load->view('inventory/consign_check/consign_check_edit_detail'); ?>
<?php $this->load->view('cancle_modal'); ?>


<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
