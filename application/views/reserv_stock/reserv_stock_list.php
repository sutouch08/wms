<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-8 col-xs-9 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่</label>
			<input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>Description</label>
			<input type="text" class="form-control input-sm" name="name" value="<?php echo $name; ?>" />
		</div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Draft</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Pending</option>
        <option value="A" <?php echo is_selected('A', $status); ?>>Approved</option>
        <option value="R" <?php echo is_selected('R', $status); ?>>Rejected</option>
        <option value="C" <?php echo is_selected('C', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Active</label>
			<select class="form-control input-sm filter" name="active">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
			</select>
		</div>

    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Start Date</label>
			<input type="text" class="form-control input-sm text-center" name="start_date" id="start_date" value="<?php echo $start_date; ?>" readonly/>
		</div>


		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>End Date</label>
			<input type="text" class="form-control input-sm text-center" name="end_date" id="end_date" value="<?php echo $end_date; ?>" readonly/>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
		<input type="hidden" name="search" value="1" />
	</div>
</form>
<hr class="padding-5 margin-top-15">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1060px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100"></th>
					<th class="fix-width-40 middle text-center">#</th>
          <th class="fix-width-100 middle">วันที่</th>
					<th class="fix-width-100 middle">เลขที่</th>
					<th class="fix-width-100 middle text-center">Start Date</th>
					<th class="fix-width-100 middle text-center">End Date</th>
					<th class="fix-width-100 middle text-center">Total SKU</th>
					<th class="fix-width-100 middle text-center">Total Qty</th>
					<th class="fix-width-60 middle text-center">Active</th>
          <th class="fix-width-60 middle text-center">Status</th>
					<th class="min-width-200 middle">Description</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
						<td class="">
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
								<i class="fa fa-eye"></i>
							</button>
							<?php if($this->pm->can_edit && $rs->status != 'C') : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="edit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete && $rs->status != 'C') : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->start_date); ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->end_date); ?></td>
						<td class="middle text-center"><?php echo number($rs->totalSKU); ?></td>
						<td class="middle text-center"><?php echo number($rs->totalQty); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
            <td class="middle text-center"><?php echo reserv_stock_status_text($rs->status); ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="11" class="text-center">--- No content ---</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/reserv_stock/reserv_stock.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
