<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6 col-xs-6">
    <p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center search" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่บิล</label>
      <input type="text" class="form-control input-sm text-center search" name="invoice" value="<?php echo $invoice; ?>" />
    </div>
    <div class="col-md-2 col-sm-4 col-xs-12 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm text-center search" name="customer_code" value="<?php echo $customer_code; ?>" />
    </div>
		<div class="col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>สถานะ</label>
      <select class="form-control input-sm" name="status" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
  			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
  			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
  		</select>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>การอนุมัติ</label>
      <select class="form-control input-sm" name="approve" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected($approve, '0'); ?>>รออนุมัติ</option>
  			<option value="1" <?php echo is_selected($approve, '1'); ?>>อนุมัติแล้ว</option>
  		</select>
    </div>
		<div class="col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>WMS</label>
			<select class="form-control input-sm" name="api" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected("0", $api); ?>>ไม่ส่ง</option>
				<option value="1" <?php echo is_selected("1", $api); ?>>ส่ง</option>
			</select>
		</div>
		<div class="col-md-4 col-sm-3 col-xs-12 padding-5">
			<label>คลังฝากขาย</label>
			<select class="form-control input-sm" name="from_warehouse" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_consignment_warehouse($from_warehouse); ?>
			</select>
		</div>
		<div class="col-md-4 col-sm-3 col-xs-12 padding-5">
			<label>คลังรับเข้า</label>
			<select class="form-control input-sm" name="to_warehouse" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_common_warehouse($to_warehouse); ?>
			</select>
		</div>

    <div class="col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group">
        <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>
		<div class="divider-hidden visible-xs"></div>
    <div class="col-md-1 col-sm-1 col-xs-10 padding-5">
      <label class="display-block not-show hidden-xs">btn</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
    </div>
    <div class="col-md-1 col-sm-1 col-xs-2 padding-5">
      <label class="display-block not-show hidden-xs">btn</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<p class="pull-right top-p padding-5">สถานะ : ว่างๆ = ปกติ,&nbsp;  <span class="blue">NC</span> = ยังไม่บันทึก,&nbsp;  <span class="purple">OP</span> = รอรับที่ WMS,&nbsp;  <span class="red">CN</span> = ยกเลิก</p>
  <div class="col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1200px;">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-8 text-center">วันที่</th>
          <th class="width-10">เลขที่เอกสาร</th>
          <th class="width-8">เลขที่บิล</th>
          <th class="width-25">ลูกค้า</th>
					<th class="width-10">โซน(รับ)</th>
          <th class="width-8 text-right">จำนวน</th>
          <th class="width-10 text-right">มลูค่า</th>
          <th class="text-center">สถานะ</th>
          <th class="text-center">อนุมัติ</th>
					<th class="text-center">WMS</th>
          <th class="width-10"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($docs as $rs) : ?>
          <tr class="font-size-12" id="row-<?php $rs->code; ?>">
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
            <td class="middle"><?php echo $rs->code; ?></td>
            <td class="middle"><?php echo $rs->invoice; ?></td>
            <td class="middle"><?php echo inputRow($rs->customer_name); ?></td>
						<td class="middle"><?php echo $rs->zone_code; ?></td>
            <td class="middle text-right"><?php echo number($rs->qty); ?></td>
            <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            <td class="middle text-center">
							<?php if($rs->status == 3) : ?>
								<span class="purple">OP</span>
							<?php endif; ?>
              <?php if($rs->status == 2) : ?>
                <span class="red">CN</span>
              <?php endif;?>
              <?php if($rs->status == 0) : ?>
                <span class="blue">NC</span>
              <?php endif; ?>
            </td>
            <td class="middle text-center">
              <?php echo is_active($rs->is_approve); ?>
            </td>
						<td class="middle text-center">
              <?php echo ($rs->is_wms && $rs->is_api ? 'Y' : 'N'); ?>
            </td>
            <td class="middle text-right">
              <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
          <?php if($this->pm->can_edit && $rs->status == 0) : ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
          <?php endif; ?>
          <?php if($this->pm->can_delete && $rs->status != 2) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
          <?php endif; ?>
            </td>
          </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="10" class="text-center">
            --- ไม่พบรายการ ---
          </td>
        </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js"></script>
<?php $this->load->view('include/footer'); ?>
