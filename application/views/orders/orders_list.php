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
					<button type="button" class="btn btn-xs btn-purple btn-100" onclick="getUploadFile()">นำเข้าออเดอร์</button>
				<?php endif;?>
        <button type="button" class="btn btn-xs btn-success btn-100" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
			<?php if($this->sokoApi OR $this->wmsApi) : ?>
				<button type="button" class="btn btn-xs btn-primary btn-100" onclick="sendOrdersToWms()"><i class="fa fa-send"></i> Send to WMS</button>
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

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Back order</label>
		<select class="form-control input-sm" name="is_backorder" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $is_backorder); ?>>No</option>
			<option value="1" <?php echo is_selected('1', $is_backorder); ?>>Yes</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Pre order</label>
		<select class="form-control input-sm" name="is_pre_order" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $is_pre_order); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $is_pre_order); ?>>No</option>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="width-100" name="warehouse" id="warehouse" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>SAP</label>
		<select class="form-control input-sm" name="sap_status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap_status); ?>>ยังไม่ส่งออก</option>
			<option value="1" <?php echo is_selected('1', $sap_status); ?>>ยังไม่เข้า SAP</option>
			<option value="2" <?php echo is_selected('2', $sap_status); ?>>เข้า SAP แล้ว</option>
			<option value="3" <?php echo is_selected('3', $sap_status); ?>>ส่งออกไม่สำเร็จ</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Add By</label>
		<select class="form-control input-sm" name="method" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $method); ?>>Manual</option>
			<option value="1" <?php echo is_selected('1', $method); ?>>Upload</option>
			<option value="2" <?php echo is_selected('2', $method); ?>>API</option>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">search</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">reset</label>
		<button type="button" class="btn btn-xs btn-warning btn-100" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
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
<input type="hidden" name="search" value="1" />
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by == '' ? "" : ($order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<?php $sort_code = $order_by == '' ? '' : ($order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="order-table" style="overflow:auto;">
		<table class="table table-striped table-hover dataTable tableFixHead" style="min-width:1280px; margin-bottom:20px;">
			<thead>
				<tr>
			<?php if($this->sokoApi OR $this->wmsApi) : ?>
					<th class="fix-width-40 middle text-center fix-header">
						<label>
							<input type="checkbox" class="ace" id="chk-all" />
							<span class="lbl"></span>
						</label>
					</th>
			<?php endif; ?>
					<th class="fix-width-40 middle text-center fix-header">ลำดับ</th>
					<th class="fix-width-100 middle text-center fix-header sorting <?php echo $sort_date; ?>" id="sort_date_add" onclick="sort('date_add')">วันที่</th>
					<th class="fix-width-250 middle fix-header sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
					<th class="fix-width-350 middle fix-header">ลูกค้า</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-150 middle fix-header">ช่องทางขาย</th>
					<th class="fix-width-150 middle fix-header">การชำระเงิน</th>
					<th class="fix-width-150 middle fix-header">สถานะ</th>
					<?php if($this->_SuperAdmin && $instant_export) : ?>
						<th class="fix-width-100 middle fix-header"></th>
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
					<?php if($this->sokoApi OR $this->wmsApi) : ?>
							<td class="middle text-center">
								<?php if($rs->state == 3 && $rs->is_wms != 0 && $rs->wms_export != 1) : ?>
									<label>
										<input type="checkbox" class="ace chk-wms" data-code="<?php echo $rs->code; ?>"/>
										<span class="lbl"></span>
									</label>
								<?php endif; ?>
							</td>
					<?php endif; ?>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code.$ref; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo empty($rs->customer_name) ? $this->customers_model->get_name($rs->customer_code) : $rs->customer_name; ?>
									<?php echo $cus_ref; ?>
								<?php endif; ?>
							</td>
              <td class="middle pointer text-right" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php echo $rs->doc_total <= 0 ? number($this->orders_model->get_order_total_amount($rs->code), 2) : number($rs->doc_total, 2); ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php echo empty($channelsList[$rs->channels_code]) ? "" : $channelsList[$rs->channels_code]; ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php echo empty($paymentList[$rs->payment_code]) ? "" : $paymentList[$rs->payment_code];  ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php if($rs->is_expired) : ?>
									หมดอายุ
								<?php else : ?>
									<?php echo get_state_name($rs->state); ?>
								<?php endif; ?>
							</td>
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
<?php if($this->_SuperAdmin) : ?>
<div >
	<?php
	//echo "query : {$start} - {$end} &nbsp;&nbsp;&nbsp;";
	list($usec, $sec) = explode(' ',$start);
	$querytime_before = ((float)$usec + (float)$sec);
	list($usec, $sec) = explode(' ',$end);
	$querytime_after = ((float)$usec + (float)$sec);
	$querytime = $querytime_after - $querytime_before;
	echo "Query Time : ".round($querytime, 4)." Second";
	?>
</div>
<?php endif; ?>

<?php
if($can_upload == 1) :
	 $this->load->view('orders/import_order');
endif;
?>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
