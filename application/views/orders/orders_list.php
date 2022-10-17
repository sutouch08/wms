<?php $this->load->view('include/header'); ?>
<?php $can_upload = getConfig('ALLOW_UPLOAD_ORDER'); ?>
<?php $instant_export = getConfig('WMS_INSTANT_EXPORT'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
				<?php if($can_upload == 1) : ?>
					<button type="button" class="btn btn-xs btn-purple" onclick="getUploadFile()">นำเข้าออเดอร์</button>
				<?php endif;?>
        <button type="button" class="btn btn-xs btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ใบเสนอราคา</label>
    <input type="text" class="form-control input-sm search" name="qt_no"  value="<?php echo $qt_no; ?>" />
	</div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่จัดส่ง</label>
		<input type="text" class="form-control input-sm search" name="shipCode" value="<?php echo $ship_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_channels($channels); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method($payment); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>WMS</label>
		<select class="form-control input-sm" name="wms_export" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $wms_export); ?>>ยังไม่ส่ง</option>
			<option value="1" <?php echo is_selected('1', $wms_export); ?>>ส่งแล้ว</option>
			<option value="3" <?php echo is_selected('3', $wms_export); ?>>Error</option>
		</select>
	</div>

	<!--
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label class="display-block">สถานะ</label>
		<select class="form-control input-sm" name="stated">
			<option value="">เลือกสถานะ</option>
			<option value="3" <?php echo is_selected($stated, '3'); ?>>รอจัดสินค้า</option>
			<option value="4" <?php echo is_selected($stated, '4'); ?>>กำลังจัดสินค้า</option>
			<option value="5" <?php echo is_selected($stated, '5'); ?>>รอตรวจ</option>
			<option value="6" <?php echo is_selected($stated, '6'); ?>>กำลังตรวจ</option>
			<option value="7" <?php echo is_selected($stated, '7'); ?>>รอเปิดบิล</option>
		</select>
	</div>

	<div class="col-sm-1 col-1-harf  col-xs-6 padding-5">
		<label class="display-block">เริ่มต้น</label>
		<select class="form-control input-sm" name="startTime">
			<?php echo selectTime($startTime); ?>
		</select>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label class="display-block">สิ้นสุด</label>
		<select class="form-control input-sm" name="endTime">
			<?php echo selectTime($endTime); ?>
		</select>
	</div>
-->
	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm" name="warehouse" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>SAP Status</label>
		<select class="form-control input-sm" name="sap_status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap_status); ?>>ยังไม่ส่งออก</option>
			<option value="1" <?php echo is_selected('1', $sap_status); ?>>ยังไม่เข้า SAP</option>
			<option value="2" <?php echo is_selected('2', $sap_status); ?>>เข้า SAP แล้ว</option>
			<option value="3" <?php echo is_selected('3', $sap_status); ?>>ส่งออกไม่สำเร็จ</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>DO No.</label>
    <input type="text" class="form-control input-sm search" name="DoNo" value="<?php echo $DoNo; ?>" />
  </div>


	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Create By</label>
		<select class="form-control input-sm" name="method" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $method); ?>>Manual</option>
			<option value="1" <?php echo is_selected('1', $method); ?>>Upload</option>
			<option value="2" <?php echo is_selected('2', $method); ?>>API</option>
		</select>
	</div>


	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>


<div class="row margin-top-10">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<button type="button" id="btn-state-1" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_1']; ?>" onclick="toggleState(1)">รอดำเนินการ</button>
		<button type="button" id="btn-state-2" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_2']; ?>" onclick="toggleState(2)">รอชำระเงิน</button>
		<button type="button" id="btn-state-3" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_3']; ?>" onclick="toggleState(3)">รอจัด</button>
		<button type="button" id="btn-state-4" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_4']; ?>" onclick="toggleState(4)">กำลังจัด</button>
		<button type="button" id="btn-state-5" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_5']; ?>" onclick="toggleState(5)">รอตรวจ</button>
		<button type="button" id="btn-state-6" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_6']; ?>" onclick="toggleState(6)">กำลังตรวจ</button>
		<button type="button" id="btn-state-7" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_7']; ?>" onclick="toggleState(7)">รอเปิดบิล</button>
		<button type="button" id="btn-state-8" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_8']; ?>" onclick="toggleState(8)">เปิดบิลแล้ว</button>
		<button type="button" id="btn-state-9" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_9']; ?>" onclick="toggleState(9)">ยกเลิก</button>
		<button type="button" id="btn-not-save" class="btn btn-sm margin-bottom-5 <?php echo $btn['not_save']; ?>" onclick="toggleNotSave()">ไม่บันทึก</button>
		<button type="button" id="btn-expire" class="btn btn-sm margin-bottom-5 <?php echo $btn['is_expire']; ?>" onclick="toggleIsExpire()">หมดอายุ</button>
		<button type="button" id="btn-only-me" class="btn btn-sm margin-bottom-5 <?php echo $btn['only_me']; ?>" onclick="toggleOnlyMe()">เฉพาะฉัน</button>
	</div>
</div>

<input type="hidden" name="state_1" id="state_1" value="<?php echo $state[1]; ?>" />
<input type="hidden" name="state_2" id="state_2" value="<?php echo $state[2]; ?>" />
<input type="hidden" name="state_3" id="state_3" value="<?php echo $state[3]; ?>" />
<input type="hidden" name="state_4" id="state_4" value="<?php echo $state[4]; ?>" />
<input type="hidden" name="state_5" id="state_5" value="<?php echo $state[5]; ?>" />
<input type="hidden" name="state_6" id="state_6" value="<?php echo $state[6]; ?>" />
<input type="hidden" name="state_7" id="state_7" value="<?php echo $state[7]; ?>" />
<input type="hidden" name="state_8" id="state_8" value="<?php echo $state[8]; ?>" />
<input type="hidden" name="state_9" id="state_9" value="<?php echo $state[9]; ?>" />
<input type="hidden" name="notSave" id="notSave" value="<?php echo $notSave; ?>" />
<input type="hidden" name="onlyMe" id="onlyMe" value="<?php echo $onlyMe; ?>" />
<input type="hidden" name="isExpire" id="isExpire" value="<?php echo $isExpire; ?>" />
<hr class="margin-top-15 padding-5">
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by == '' ? "" : ($order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<?php $sort_code = $order_by == '' ? '' : ($order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="double-scroll">
		<table class="table table-striped table-hover dataTable" style="min-width:1000px; border-collapse:inherit;">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle text-center sorting <?php echo $sort_date; ?>" id="sort_date_add" onclick="sort('date_add')">วันที่</th>
					<th class="width-15 middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
					<th class="middle">ลูกค้า</th>
					<th class="width-10 middle">ยอดเงิน</th>
					<th class="width-10 middle">ช่องทางขาย</th>
					<th class="width-10 middle">การชำระเงิน</th>
					<th class="width-10 middle">สถานะ</th>
					<?php if($this->_SuperAdmin && $instant_export) : ?>
						<th class="width-5 middle"></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $ref = empty($rs->reference) ? '' :' ['.$rs->reference.']'; ?>
						<?php $cus_ref = empty($rs->customer_ref) ? '' : ' ['.$rs->customer_ref.']'; ?>
            <tr id="row-<?php echo $rs->code; ?>" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code.$ref; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo $rs->customer_name.$cus_ref; ?>
								<?php endif; ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo number($rs->total_amount, 2); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->channels_name; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->payment_name; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->state_name; ?></td>
              <?php if($this->_SuperAdmin && $instant_export) : ?>
							<td class="middle text-right"><button type="button" class="btn btn-minier btn-primary" onclick="sendToWms('<?php echo $rs->code; ?>')">Wms</button></td>
							<?php endif; ?>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php
if($can_upload == 1) :
	 $this->load->view('orders/import_order');
endif;
?>


<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
