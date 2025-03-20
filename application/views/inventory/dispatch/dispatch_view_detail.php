<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($this->pm->can_delete) && ($doc->status != 'D' && $doc->status != 'C')) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="cancelDispatch('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
		<?php endif; ?>
    <?php if(($this->pm->can_add OR $this->pm->can_edit) && ($doc->status == 'P' OR $doc->status =='S')) : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
    <?php endif; ?>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status =='S') : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="closeDispatch('<?php echo $doc->code; ?>')"><i class="fa fa-check"></i> ปิดการจัดส่ง</button>
		<?php endif; ?>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="printDispatch('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i>  พิมพ์</button>
	</div>
</div><!-- End Row -->
<?php if($doc->status == 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
		<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled/>
  </div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ช่องทางขาย</label>
		<select class="form-control input-sm e" id="channels" disabled>
			<option value="">เลือก</option>
			<?php echo select_channels($doc->channels_code); ?>
		</select>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ผู้จัดส่ง</label>
		<select class="form-control input-sm e" id="sender" disabled>
			<option value="">เลือก</option>
			<?php echo select_sender($doc->sender_code); ?>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ทะเบียนรถ</label>
    <input type="text" class="form-control input-sm e" id="plate-no" value="<?php echo $doc->plate_no; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>จังหวัด</label>
    <input type="text" class="form-control input-sm e" id="province" value="<?php echo $doc->plate_province; ?>" disabled/>
	</div>

	<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>ชื่อคนขับ</label>
    <input type="text" class="form-control input-sm e" id="driver-name" value="<?php echo $doc->driver_name; ?>" disabled/>
	</div>
  <div class="col-lg-8-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm e" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" id="status" value="<?php echo dispatch_status($doc->status); ?>" disabled />
  </div>
</div>
<div class="divider"></div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th class="fix-width-50 text-center fix-header">#</th>
          <th class="fix-width-150 fix-header">เลขที่</th>
          <th class="fix-width-150 fix-header">อ้างอิง</th>
          <th class="min-width-200 fix-header">ลูกค้า</th>
          <th class="fix-width-150 fix-header">ช่องทางขาย</th>
					<th class="fix-width-100 fix-header">จำนวน(กล่อง)</th>
        </tr>
      </thead>
      <tbody id="dispatch-table">
        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php $channels = get_channels_array(); ?>
          <?php foreach($details as $rs) : ?>
            <tr id="dispatch-<?php echo $rs->id; ?>" class="font-size-11">
              <td class="text-center dp-no"><?php echo $no; ?></td>
              <td><?php echo $rs->order_code; ?></td>
              <td><?php echo $rs->reference; ?></td>
              <td><?php echo $rs->customer_code." : ".$rs->customer_name; ?></td>
              <td><?php echo empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?></td>
							<td class="text-center"><?php echo $rs->carton_shipped; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="5" class="text-center">---- ไม่พบรายการ ----</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<?php $this->load->view('cancle_modal'); ?>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
