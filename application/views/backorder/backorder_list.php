<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn btn-100" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
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
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_channels($channels); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="from-date" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="to-date" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ประเภท</label>
		<select class="form-control input-sm" name="role" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
		<?php echo select_order_role($role); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="width-100" name="warehouse" id="warehouse" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">search</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">reset</label>
		<button type="button" class="btn btn-xs btn-warning btn-100" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
	</div>
</div>
<input type="hidden" name="search" value="1" />
</form>
<hr/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="order-table" style="overflow:auto;">
		<table class="table table-striped table-hover dataTable tableFixHead" style="min-width:1040px; margin-bottom:20px;">
			<thead>
				<tr>
					<th class="fix-width-50 middle text-center"></th>
					<th class="fix-width-40 middle text-center fix-header">ลำดับ</th>
					<th class="fix-width-100 middle text-center fix-header">วันที่</th>
					<th class="fix-width-250 middle fix-header">เลขที่เอกสาร</th>
					<th class="min-width-200 middle fix-header">ลูกค้า</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-150 middle fix-header">ช่องทางขาย</th>
					<th class="fix-width-150 middle fix-header">สถานะ</th>
				</tr>
			</thead>
			<tbody>
        <?php if( ! empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $ref = empty($rs->reference) ? '' :' ['.$rs->reference.']'; ?>
						<?php $cus_ref = empty($rs->customer_ref) ? '' : ' ['.$rs->customer_ref.']'; ?>
            <tr id="row-<?php echo $rs->id; ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewBackorder('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code.$ref; ?></td>
              <td class="middle">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo empty($rs->customer_name) ? $this->customers_model->get_name($rs->customer_code) : $rs->customer_name; ?>
									<?php echo $cus_ref; ?>
								<?php endif; ?>
							</td>
              <td class="middle text-right">
								<?php echo $rs->doc_total <= 0 ? number($this->orders_model->get_order_total_amount($rs->code), 2) : number($rs->doc_total, 2); ?>
							</td>
              <td class="middle" >
								<?php echo empty($channelsList[$rs->channels_code]) ? "" : $channelsList[$rs->channels_code]; ?>
							</td>
              <td class="middle">
								<?php if($rs->is_expired) : ?>
									หมดอายุ
								<?php else : ?>
									<?php echo get_state_name($rs->state); ?>
								<?php endif; ?>
							</td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/backorder/backorder.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
