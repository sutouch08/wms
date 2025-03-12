<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ใบสั่งซื้อ</label>
			<input type="text" class="form-control input-sm search" name="po" value="<?php echo $po; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ใบส่งสินค้า</label>
			<input type="text" class="form-control input-sm search" name="invoice" value="<?php echo $invoice; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ผู้จำหน่าย</label>
			<input type="text" class="form-control input-sm search" name="vendor" value="<?php echo $vendor; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>พนักงาน</label>
			<input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
		</div>

		<div class="col-lg-4-harf col-md-4 col-sm-6 col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>การรับ</label>
			<select name="is_wms" class="form-control input-sm" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $is_wms); ?>>Warrix</option>
				<option value="1" <?php echo is_selected('1', $is_wms); ?>>Pioneer</option>
				<option value="2" <?php echo is_selected('2', $is_wms); ?>>SOKOCHAN</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select name="status" class="form-control input-sm" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
				<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
				<option value="3" <?php echo is_selected('3', $status); ?>>WMS Process</option>
				<option value="4" <?php echo is_selected('4', $status); ?>>รอการยืนยัน</option>
				<option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>WMS</label>
			<select name="wms_export" class="form-control input-sm" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $wms_export); ?>>ยังไม่ส่ง</option>
				<option value="1" <?php echo is_selected('1', $wms_export); ?>>ส่งแล้ว</option>
				<option value="3" <?php echo is_selected('3', $wms_export); ?>>ส่งไม่ผ่าน</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>การยืนยัน</label>
			<select name="must_accept" class="form-control input-sm" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $must_accept); ?>>ต้องยืนยัน</option>
				<option value="0" <?php echo is_selected('0', $must_accept); ?>>ไม่ต้องยืนยัน</option>
			</select>
		</div>


		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>SAP</label>
			<select name="sap" class="form-control input-sm" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
				<option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12 padding-5">
    <p class="pull-right">
      สถานะ : ว่างๆ = ปกติ, &nbsp;
      <span class="red">CN</span> = ยกเลิก, &nbsp;
      <span class="blue">DF</span> = ยังไม่บันทึก, &nbsp;
			<span class="purple">OP</span> = รอรับที่ WMS, &nbsp;
			<span class="orange">WC</span> = รอการยืนยัน, &nbsp;
			<span class="dark">EXP</span> = หมดอายุ &nbsp;
    </p>
  </div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1480px;">
			<thead>
				<tr>
					<th class="fix-width-120"></th>
					<th class="fix-width-40 middle text-center">สถานะ</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle text-center">กำหนดรับ</th>
					<th class="fix-width-150 middle">เลขที่เอกสาร</th>
					<th class="fix-width-80 middle">การรับ</th>
					<th class="fix-width-100 middle">คลัง</th>
					<th class="fix-width-150 middle">ใบส่งสินค้า</th>
					<th class="fix-width-100 middle">ใบสั่งซื้อ</th>
					<th class="fix-width-250 middle">ผู้จำหน่าย</th>
					<th class="fix-width-100 middle text-center">จำนวน</th>
					<th class="min-width-150 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($document)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($document as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>" style="font-size:12px; <?php echo statusBackgroundColor($rs->is_expire, $rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
						<?php if($this->pm->can_delete) : ?>
							<?php if($rs->status == 0 OR ($rs->is_wms == 0 && $rs->status == 3)) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($this->pm->can_edit) : ?>
								<?php if($rs->status == 3 && $rs->is_wms == 0) : ?>
									<button type="button" class="btn btn-minier btn-purple" onclick="goProcess('<?php echo $rs->code; ?>')">รับเข้า</button>
								<?php endif; ?>
								<?php if($rs->status == 0) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
							<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if($rs->is_expire OR $rs->status == 2) : ?>
									<?php if($rs->status == 2) : ?>
										<span class="red"><strong>CN</strong></span>
									<?php else : ?>
										<span class="dark">EXP</span>
									<?php endif; ?>
								<?php else : ?>
									<?php if($rs->status == 0 ) : ?>
										<span class="blue"><strong>DF</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 3 ) : ?>
										<span class="purple"><strong>OP</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 4) : ?>
										<span class="orange"><strong>WC</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 1) : ?>
										<span class="green"><strong>OK</strong></span>
									<?php endif; ?>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->due_date, FALSE, '/'); ?></td>
              <td class="middle">
								<?php echo $rs->code; ?> <?php echo ( ! empty($rs->soko_code) ? "[{$rs->soko_code}]" : ""); ?>
								<?php if($rs->wms_export == 3) : ?>
									<span class="font-size-10 red">Failed</span>
								<?php endif; ?>
							</td>
							<td class="middle">
								<?php echo $rs->is_wms == 2 ? "Soko" : ($rs->is_wms == 1 ? "PLC" : "Warrix"); ?>
							</td>
							<td class="middle"><?php echo $rs->warehouse_code; ?></td>
              <td class="middle"><?php echo $rs->invoice_code; ?></td>
              <td class="middle"><?php echo $rs->po_code; ?></td>
              <td class="middle"><?php echo $rs->vendor_name; ?></td>
              <td class="middle text-center"><?php echo number($rs->qty); ?></td>
							<td class="middle">
                <?php echo $rs->display_name; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
