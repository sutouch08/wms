<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>อ้างถึง</label>
    <input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

  <div class="col-lg-2 col-md-4 col-sm-5 col-xs-12 padding-5">
    <label>พนักงาน</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
    </select>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>การอนุมัติ</label>
		<select class="form-control input-sm" name="isApprove" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected($isApprove, "0"); ?>>รออนุมัติ</option>
			<option value="1" <?php echo is_selected($isApprove, "1"); ?>>อนุมัติแล้ว</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all" <?php echo is_selected($status, 'all'); ?>>ทั้งหมด</option>
			<option value="0" <?php echo is_selected($status, '0'); ?>>ยังไม่บันทึก</option>
      <option value="1" <?php echo is_selected($status, '1'); ?>>บันทึกแล้ว</option>
      <option value="2" <?php echo is_selected($status, '2'); ?>>ยกเลิก</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>SAP</label>
		<select name="sap" class="form-control input-sm" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
			<option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
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
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p class="pull-right">
      สถานะ : ว่างๆ = ปกติ, &nbsp;
      <span class="red">CN</span> = ยกเลิก, &nbsp;
      <span class="blue">NC</span> = ยังไม่บันทึก
    </p>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1250px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-100"></th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-80 text-center">วันที่</th>
          <th class="fix-width-100">เลขที่เอกสาร</th>
					<th class="fix-width-50 text-center">สถานะ</th>
					<th class="fix-width-50 text-center">อนุมัติ</th>
					<th class="fix-width-80 text-center">Receipt Qty</th>
					<th class="fix-width-80 text-center">Issue Qty.</th>
					<th class="fix-width-80 text-center">Receipt No.</th>
					<th class="fix-width-80 text-center">Issue No.</th>
          <th class="fix-width-150">อ้างถึง</th>
          <th class="fix-width-150">พนักงาน</th>
					<th class="min-width-200">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($list))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($list as $rs)  : ?>
        <tr class="font-size-11">
					<td class="middle">
						<button type="button" class="btn btn-minier btn-info" onclick="goDetail('<?php echo $rs->code; ?>')">
							<i class="fa fa-eye"></i>
						</button>
						<?php if($rs->status == 0 && $this->pm->can_edit) : ?>
							<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
								<i class="fa fa-pencil"></i>
							</button>
						<?php endif; ?>

						<?php if($rs->status != 2 && $this->pm->can_delete && (empty($rs->issue_code) && empty($rs->receive_code))) : ?>
							<button type="button" class="btn btn-minier btn-danger" onclick="goCancle('<?php echo $rs->code; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle"><?php echo thai_date($rs->date_add); ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
					<td class="middle text-center">
						<?php if($rs->status == 1) : ?>
							<span class="green">OK</span>
						<?php endif; ?>
						<?php if($rs->status == 0) : ?>
							<span class="blue">NC</span>
						<?php endif; ?>
						<?php if($rs->status == 2) : ?>
							<span class="red">CN</span>
						<?php endif; ?>
					</td>
					<td class="middle text-center"><?php echo $rs->is_approved ? is_active($rs->is_approved) : ""; ?></td>
					<td class="middle text-center"><?php echo ac_format($rs->total_receive); ?></td>
					<td class="middle text-center"><?php echo ac_format($rs->total_issue); ?></td>
					<td class="middle text-center"><?php echo $rs->receive_code; ?></td>
					<td class="middle text-center"><?php echo $rs->issue_code; ?></td>
          <td class="middle"><?php echo $rs->reference; ?></td>
          <td class="middle"><?php echo $rs->user; ?></td>
					<td class="middle"><?php echo $rs->remark; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="13" class="text-center">-- ไม่พบรายการ --</td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script>
	$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
