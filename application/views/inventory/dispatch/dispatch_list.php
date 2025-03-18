<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn btn-100" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="order_code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="form-control input-sm filter" name="channels">
				<option value="all">ทั้งหมด</option>
				<?php echo select_dispatch_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Pending</option>
				<option value="S" <?php echo is_selected('S', $status); ?>>Shipped</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>ทะเบียนรถ</label>
			<input type="text" class="form-control input-sm search" name="plate_no"  value="<?php echo $plate_no; ?>" />
		</div>

		<div class="col-lg-2 col-md-3-harf col-sm-4 col-xs-6 padding-5">
			<label>ผู้จัดส่ง</label>
			<select class="form-control input-sm filter" name="sender">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sender($sender); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>

		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered border-1" style="min-width:950px;">
      <tr>
				<th class="fix-width-80 text-center"></th>
        <th class="fix-width-50 text-center">#</th>
        <th class="fix-width-100 text-center">วันที่</th>
        <th class="fix-width-100 text-center">เลขที่เอกสาร</th>
				<th class="fix-width-150 text-center">ช่องทาง</th>
        <th class="fix-width-100 text-center">สถานะ</th>
				<th class="fix-width-100 text-center">ทะเบียนรถ</th>
				<th class="fix-width-150 text-center">ผู้จัดส่ง</th>
    		<th class="min-width-100">User</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment($this->segment) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12" id="row-<?php echo $rs->id; ?>" style="<?php echo textStatusColor($rs->status); ?>">
				<td class="">
					<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code;?>')"><i class="fa fa-eye"></i></button>
					<?php if($this->pm->can_edit && $rs->status == 'P') : ?>
						<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
					<?php endif; ?>
				</td>
        <td class="text-center no"><?php echo $no; ?></td>
        <td class="text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
        <td class="text-center"><?php echo $rs->code; ?></td>
				<td class=""><?php echo $rs->channels_name; ?></td>
    		<td class="text-center"><?php echo dispatch_status($rs->status); ?></td>
				<td class=""> <?php echo $rs->plate_no; ?></td>
				<td class=""> <?php echo $rs->sender_name; ?></td>
        <td class=""> <?php echo $rs->user; ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="6" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
