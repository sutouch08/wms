<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <?php if($allow_import_return) : ?>
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-primary top-btn" onclick="getUploadFile()"><i class="fa fa-file-excel-o"></i> &nbsp; Import Excel</button>
      <?php endif; ?>
      <button type="button" class="btn btn-sm btn-purple top-btn" onclick="getTemplate()"><i class="fa fa-download"></i> &nbsp; Template file</button>
    <?php endif; ?>
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
    <?php endif; ?>
  </div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center search" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>เลขที่บิล</label>
      <input type="text" class="form-control input-sm text-center search" name="invoice" value="<?php echo $invoice; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm text-center search" name="customer_code" value="<?php echo $customer_code; ?>" />
    </div>
		<div class="col-lg-3 col-md-4-harf col-sm-4-harf col-xs-8 padding-5">
			<label>โซน</label>
			<input type="text" class="form-control input-sm padding-5" name="zone" value="<?php echo $zone; ?>" />
		</div>

    <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
      <label>การยืนยัน</label>
  		<select name="must_accept" class="form-control input-sm" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $must_accept); ?>>ไม่ต้องยืนยัน</option>
  			<option value="1" <?php echo is_selected('1', $must_accept); ?>>ต้องยืนยัน</option>
  		</select>
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label>สถานะ</label>
      <select class="form-control input-sm" name="status" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
  			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
  			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
				<option value="3" <?php echo is_selected('3', $status); ?>>WMS Process</option>
        <option value="4" <?php echo is_selected('4', $status); ?>>รอการยืนยัน</option>
        <option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
  		</select>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>การอนุมัติ</label>
      <select class="form-control input-sm" name="approve" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected($approve, '0'); ?>>รออนุมัติ</option>
  			<option value="1" <?php echo is_selected($approve, '1'); ?>>อนุมัติแล้ว</option>
  		</select>
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>WMS</label>
      <select class="form-control input-sm" name="api" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected($api, '0'); ?>>ไม่ส่ง</option>
  			<option value="1" <?php echo is_selected($api, '1'); ?>>ปกติ</option>
  		</select>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Interface</label>
      <select class="form-control input-sm" name="wms_export" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected($wms_export, '0'); ?>>ยังไม่ส่ง</option>
  			<option value="1" <?php echo is_selected($wms_export, '1'); ?>>ส่งแล้ว</option>
        <option value="3" <?php echo is_selected($wms_export, '3'); ?>>ส่งไม่สำเร็จ</option>
  		</select>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>POS</label>
      <select class="form-control input-sm" name="is_pos_api" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected($is_pos_api, '0'); ?>>ไม่ใช่</option>
  			<option value="1" <?php echo is_selected($is_pos_api, '1'); ?>>ใช่</option>
  		</select>
    </div>
    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>SAP</label>
  		<select name="sap" class="form-control input-sm" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
  			<option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
  		</select>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <p class="pull-right top-p">
      สถานะ : ว่างๆ = ปกติ,&nbsp;
      <span class="grey blod">NC</span> = ยังไม่บันทึก,&nbsp;
      <span class="blue blod">AP</span> = รออนุมัติ,&nbsp;
      <span class="purple blod">OP</span> = รอรับที่ WMS,&nbsp;
      <span class="red blod">CN</span> = ยกเลิก, &nbsp;
      <span class="orange blod">WC</span> = รอการยืนยันม &nbsp;
      <span class="dark blod">EXP</span> = หมดอายุ
    </p>
  </div>
  <?php
    $ex = $this->_SuperAdmin && is_true(getConfig('WMS_FAST_EXPORT')) ? TRUE : FALSE;
    $width = 1320;
    $width = $ex ? $width + 80 : $width;

  ?>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:<?php echo $width; ?>px;">
      <thead>
        <tr>
          <?php if($ex) : ?>
            <th class="fix-width-80 text-center">WMS</th>
          <?php endif; ?>
          <th class="fix-width-100"></th>
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-150">เลขที่เอกสาร</th>
          <th class="fix-width-120">เลขที่บิล</th>
          <th class="min-width-200">ลูกค้า</th>
					<th class="fix-width-150">โซน</th>
          <th class="fix-width-80 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">มลูค่า</th>
          <th class="fix-width-60 text-center">สถานะ</th>
          <th class="fix-width-60 text-center">อนุมัติ</th>
					<th class="fix-width-60 text-center">WMS</th>
          <th class="fix-width-150">พนักงาน</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment($this->segment) + 1; ?>
<?php   foreach($docs as $rs) : ?>
          <tr class="font-size-12" id="row-<?php echo $rs->code; ?>" style="<?php echo statusBackgroundColor($rs->is_expire, $rs->status, $rs->is_approve); ?>">
            <?php if($ex) : ?>
              <td class="fix-width-40 text-center">
                <?php if($this->wmsApi && $rs->is_wms == 1 && $rs->api == 1 && $rs->status != 0 && $rs->status !=2 && $rs->is_approve == 1) : ?>
              				<button type="button" class="btn btn-minier btn-success top-btn" onclick="sendToFulfillment('<?php echo $rs->code; ?>')"><i class="fa fa-send"></i> PLC</button>
              	<?php endif; ?>
              	<?php if($this->sokoApi && $rs->is_wms == 2 && $rs->api == 1 && $rs->status != 0 && $rs->status !=2 && $rs->is_approve == 1) : ?>
              				<button type="button" class="btn btn-minier btn-success top-btn" onclick="sendToFulfillment('<?php echo $rs->code; ?>')"><i class="fa fa-send"></i> SOKO</button>
              	<?php endif; ?>
              </td>
            <?php endif; ?>
            <td class="middle">
              <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
          <?php if($this->pm->can_edit && $rs->status == 0 && $rs->is_expire == 0) : ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
          <?php endif; ?>
          <?php if(($this->pm->can_delete && $rs->status != 2 && $rs->is_pos_api == 0) OR ($rs->status != 2 && $this->_SuperAdmin)) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
          <?php endif; ?>
            </td>
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
            <td class="middle">
              <?php echo $rs->code; ?>
              <?php if($rs->wms_export == 3) : ?>
                <span class="red">failed</span>
              <?php endif; ?>
              <?php if($rs->wms_export == 1 && ! empty($rs->soko_code)) : ?>
                [<?php echo $rs->soko_code; ?>]
              <?php endif; ?>
            </td>
            <td class="middle"><?php echo ($rs->is_pos_api == 1 ? $rs->bill_code : $rs->invoice); ?></td>
            <td class="middle"><?php echo inputRow($rs->customer_name); ?></td>
						<td class="middle"><?php echo $rs->zone_code; ?></td>
            <td class="middle text-right"><?php echo number($rs->qty); ?></td>
            <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            <td class="middle text-center">
              <?php if($rs->is_expire == 1) : ?>
                <span class="dark">EXP</span>
              <?php else : ?>
                <?php if($rs->status == 2) : ?>
                  <span class="red">CN</span>
                <?php endif;?>
                <?php if($rs->status == 0) : ?>
                  <span class="blue">NC</span>
                <?php endif; ?>
                <?php if($rs->status == 1 && $rs->is_approve == 0) : ?>
                  <span class="blue">AP</span>
                <?php endif; ?>
  							<?php if($rs->status == 3) : ?>
                  <span class="purple">OP</span>
                <?php endif; ?>
                <?php if($rs->status == 4) : ?>
                  <span class="orange">WC</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td class="middle text-center">
              <?php echo is_active($rs->is_approve); ?>
            </td>
						<td class="middle text-center">
              <?php echo $rs->api == 1 ? 'Y' : 'N'; ?>
            </td>
            <td class="middle"><?php echo $rs->display_name; ?></td>
          </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="13" class="text-center">
            --- ไม่พบรายการ ---
          </td>
        </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if($allow_import_return) : ?>
  <?php $this->load->view('inventory/return_order/import_file'); ?>
<?php endif; ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
