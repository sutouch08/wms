<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_transfer/style'); ?>
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
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>Doc No.</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>Ref No.</label>
			<input type="text" class="form-control input-sm search" name="reference"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
			<label>From Warehouse</label>
			<select class="form-control input-sm filter" name="fromWhsCode" id="fromWhsCode">
				<option value="all">All</option>
				<?php echo select_warehouse($fromWhsCode); ?>
			</select>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
			<label>To Warehouse</label>
			<select class="form-control input-sm filter" name="toWhsCode" id="toWhsCode">
				<option value="all">All</option>
				<?php echo select_warehouse($toWhsCode); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
			<label>User</label>
			<select class="form-control input-sm filter" name="user" id="user">
				<option value="all">All</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">All</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
				<option value="R" <?php echo is_selected('R', $status); ?>>Released</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่เอกสาร</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>SAP</label>
			<select class="form-control input-sm filter" name="is_exported">
				<option value="all">All</option>
				<option value="N" <?php echo is_selected('N', $is_exported); ?>>No</option>
				<option value="Y" <?php echo is_selected('Y', $is_exported); ?>>Yes</option>
				<option value="E" <?php echo is_selected('E', $is_exported); ?>>Failed</option>
			</select>
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
    <table class="table table-striped border-1" id="table-listing" style="min-width:1200px;">
      <tr class="font-size-11">
				<th class="fix-width-50 text-center"></th>
        <th class="fix-width-50 text-center">#</th>
				<th class="fix-width-60 text-center">Status</th>
        <th class="fix-width-80 text-center">Date</th>
				<th class="fix-width-100">Document No</th>
				<th class="fix-width-80">Ref No</th>
				<th class="fix-width-80">SAP No.</th>
				<th class="min-width-250">From Warehouse</th>
				<th class="min-width-250">To Warehouse</th>
    		<th class="min-width-100">User</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment($this->segment) + 1; ?>
		<?php $whsName = []; ?>
    <?php foreach($data as $rs) : ?>
			<?php if(empty($whsName[$rs->fromWhsCode])) { $whsName[$rs->fromWhsCode] = warehouse_name($rs->fromWhsCode); } ?>
			<?php if(empty($whsName[$rs->toWhsCode])) { $whsName[$rs->toWhsCode] = warehouse_name($rs->toWhsCode); } ?>
      <tr class="font-size-11" id="row-<?php echo $rs->id; ?>" style="<?php echo transfer_status_color($rs->Status); ?>">
				<td class="middle">
					<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code;?>')"><i class="fa fa-eye"></i></button>
					<?php if($this->pm->can_edit && $rs->Status == 'P') : ?>
						<button type="button" class="btn btn-minier btn-warning" onclick="edit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
					<?php endif; ?>
				</td>
        <td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle text-center"><?php echo production_transfer_status_text($rs->Status); ?></td>
        <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
        <td class="middle"><?php echo $rs->code; ?></td>
				<td class="middle"><?php echo $rs->reference; ?></td>
				<td class="middle"><?php echo $rs->inv_code; ?></td>
				<td class="middle"><?php echo $rs->fromWhsCode.' | '.$whsName[$rs->fromWhsCode]; ?></td>
				<td class="middle"><?php echo $rs->toWhsCode.' | '.$whsName[$rs->toWhsCode]; ?></td>
        <td class="middle"> <?php echo $rs->user; ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="10" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
	$('#fromWhsCode').select2();
	$('#toWhsCode').select2();
	$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/productions/production_transfer/production_transfer.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
