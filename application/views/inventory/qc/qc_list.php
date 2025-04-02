<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/qc/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right hidden-xs">
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="viewProcess()">กำลังตรวจ</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row filter-pad move-out" id="filter-pad">
		<div class="col-xs-12 padding-5 text-center visible-xs">
			<h4 class="title">ตัวกรอง</h4>
		</div>
		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="width-100" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>พนักงาน</label>
			<input type="text" class="width-100" name="user" value="<?php echo $user; ?>" />
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="width-100" style="height:31px;" name="channels">
				<option value="">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ประเภท</label>
			<select class="width-100" style="height:31px;" name="role">
				<option value="all">ทั้งหมด</option>
				<option value="S" <?php echo is_selected($role, 'S'); ?>>ขาย</option>
				<option value="C" <?php echo is_selected($role, 'C'); ?>>ฝากขาย(SO)</option>
				<option value="N" <?php echo is_selected($role, 'N'); ?>>ฝากขาย(TR)</option>
				<option value="P" <?php echo is_selected($role, 'P'); ?>>สปอนเซอร์</option>
				<option value="U" <?php echo is_selected($role, 'U'); ?>>อภินันท์</option>
				<option value="Q" <?php echo is_selected($role, 'Q'); ?>>แปรสภาพ(สต็อก)</option>
				<option value="T" <?php echo is_selected($role, 'T'); ?>>แปรสภาพ(ขาย)</option>
				<option value="L" <?php echo is_selected($role, 'L'); ?>>ยืม</option>
			</select>
		</div>
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>

<hr class="margin-top-15 hidden-xs">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-hover border-1 no-border-xs table-listing">
			<thead>
				<tr>
					<th class="fix-width-100 middle hidden-xs"></th>
					<th class="fix-width-50 middle text-center hidden-xs">#</th>
					<th class="fix-width-100 middle text-center hidden-xs">วันที่</th>
					<th class="fix-width-150 middle hidden-xs">เลขที่เอกสาร</th>
					<th class="fix-width-150 middle hidden-xs">เลขที่อ้างอิง</th>
					<th class="fix-width-150 middle hidden-xs">ช่องทาง</th>
					<th class="min-width-200 middle hidden-xs">ลูกค้า/พนักงาน</th>
					<th class="width-100 text-center hide">รายการรอจัด</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($orders)) : ?>
					<?php $channels = get_channels_array(); ?>
					<?php $whName = []; ?>
					<?php $no = $this->uri->segment(4) + 1; ?>
					<?php foreach($orders as $rs) : ?>
						<?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : (empty($rs->customer_name) ? $rs->empName : $rs->customer_name); ?>
						<?php $channels_name = empty($rs->channels_code) ? "" : (empty($channels[$rs->channels_code]) ? "" : $channels[$rs->channels_code]); ?>
						<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
						<?php if( empty($whName[$rs->warehouse_code])) : ?>
							<?php $whName[$rs->warehouse_code] = warehouse_name($rs->warehouse_code); ?>
						<?php endif; ?>
						<tr id="row-<?php echo $rs->code; ?>" class="font-size-12">
							<td class="middle hidden-xs">
								<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-white btn-xs btn-info" onClick="goQc('<?php echo $rs->code; ?>')">ตรวจสินค้า</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center no hidden-xs"><?php echo $no; ?></td>
							<td class="middle text-center hidden-xs"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
							<td class="middle hidden-xs"><?php echo $rs->code . $cn_text; ?></td>
							<td class="middle hidden-xs"><?php echo $rs->reference; ?></td>
							<td class="middle hidden-xs"><?php echo $channels_name; ?></td>
							<td class="middle hidden-xs"><?php echo $customer_name; ?></td>

							<td class="visible-xs" style="border:0px; padding:3px; font-size:14px;">
								<div class="col-xs-12" style="border:solid 1px #ccc; border-radius:5px; box-shadow:0px 1px 2px #f3ecec; padding:5px;">
									<div class="width-100" style="padding: 3px 3px 3px 10px;">
										<p class="margin-bottom-3 pre-wrap"><b>วันที่ : </b><?php echo thai_date($rs->date_add, FALSE,'/'); ?></p>
										<p class="margin-bottom-3 pre-wrap"><b>เลขที่ : </b>
											<?php echo $rs->code; ?>
											<?php echo (empty($rs->reference) ? "" : "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[".$rs->reference."]"); ?>
											<?php echo $cn_text; ?>
										</p>
										<p class="margin-bottom-3 pre-wrap"><b>ลูกค้า : </b>
											<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
												<?php echo $rs->empName; ?>
											<?php else : ?>
												<?php echo $customer_name; ?>
											<?php endif; ?>
										</p>
										<p class="margin-bottom-3 pre-wrap"><b>ช่องทางขาย : </b> <?php echo $channels_name; ; ?></p>
										<p class="margin-bottom-3 pre-wrap"><b>คลัง : </b> <?php echo $whName[$rs->warehouse_code]; ?></p>
									</div>
									<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
										<button type="button" class="btn btn-white btn-info"
										onclick="goQc('<?php echo $rs->code; ?>', 'mobile')"
										style="position:absolute; top:5px; right:5px; border-radius:4px !important;">#<?php echo $no; ?> ตรวจสินค้า</button>
										<?php endif; ?>
								</div>
							</td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="7" class="text-center">--- No content ---</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="pg-footer visible-xs">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu width-25">
				<button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="refresh()">
					<i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
				</button>
			</div>
			<div class="footer-menu width-25">
				<button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="goToBuffer()">
					<i class="fa fa-history fa-2x white"></i><span class="fon-size-12">Buffer</span>
				</button>
			</div>
			<div class="footer-menu width-25">
				<button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="viewProcess()">
					<i class="fa fa-cube fa-2x white"></i><span class="fon-size-12">กำลังตรวจ</span>
				</button>
			</div>
			<div class="footer-menu width-25">
				<button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleFilter()">
					<i class="fa fa-search fa-2x white"></i><span class="fon-size-12">ตัวกรอง</span>
				</button>
			</div>
		</div>
		<input type="hidden" id="filter" value="hide" />
 </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
