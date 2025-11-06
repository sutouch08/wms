<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/prepare/style'); ?>

<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right hidden-xs">
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="genPickList()">พิมพ์ใบจัด(ชั่วคราว)</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="goProcess()"><i class="fa fa-external-link-square"></i> รายการกำลังจัด</button>
	<?php if($this->pm->can_edit) : ?>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="express_order()"><i class="fa fa-qrcode"></i>&nbsp; จัดออเดอร์</button>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-danger btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				<i class="ace-icon fa fa-exclamation-triangle icon-on-left"></i>
				Backorder
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="danger">
					<a href="javascript:updateBackorder(1)">Backorder</a>
				</li>
				<li class="purple">
					<a href="javascript:updateBackorder(0)">Not Backorder</a>
				</li>
			</ul>
		</div>
	<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row filter-pad move-out" id="filter-pad">
		<div class="col-xs-12 padding-5 text-center visible-xs">
			<h4 class="title">ตัวกรอง</h4>
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5 fi">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5 fi">
			<label>อ้างอิง</label>
			<input type="text" class="form-control input-sm search" name="reference"  value="<?php echo $reference; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5 fi">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-2-harf col-md-4-harf col-sm-2-harf col-xs-6 padding-5 fi">
			<label>พนักงาน</label>
			<select class="width-100 filter" id="user" name="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-3 col-md-4 col-sm-3-harf col-xs-6 padding-5 fi">
			<label>คลัง</label>
			<select class="width-100" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sell_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3-harf col-sm-3 col-xs-6 padding-5 fi">
			<label>ช่องทางขาย</label>
			<select class="width-100" name="channels" id="channels">
				<option value="all">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
	    <label>Shop Name</label>
			<select class="form-control input-sm" name="shop_id" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_shop_name($shop_id); ?>
			</select>
	  </div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5 fi">
			<label>ออนไลน์</label>
			<select class="form-control input-sm" name="is_online">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected($is_online, '1'); ?>>ออนไลน์</option>
				<option value="0" <?php echo is_selected($is_online, '0'); ?>>ออฟไลน์</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5 fi">
			<label>ประเภท</label>
			<select class="form-control input-sm" name="role">
				<option value="all">ทั้งหมด</option>
				<option value="S" <?php echo is_selected($role, 'S'); ?>>WO</option>
				<option value="C" <?php echo is_selected($role, 'C'); ?>>WC</option>
				<option value="N" <?php echo is_selected($role, 'N'); ?>>WT</option>
				<option value="P" <?php echo is_selected($role, 'P'); ?>>WS</option>
				<option value="U" <?php echo is_selected($role, 'U'); ?>>WU</option>
				<option value="Q" <?php echo is_selected($role, 'Q'); ?>>WV</option>
				<option value="T" <?php echo is_selected($role, 'T'); ?>>WQ</option>
				<option value="L" <?php echo is_selected($role, 'L'); ?>>WL</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5 fi">
			<label>Backorder</label>
			<select class="form-control input-sm" name="is_backorder">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_backorder);?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_backorder); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label>Canceled</label>
			<select class="form-control input-sm" name="is_cancled">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_cancled); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_cancled); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-2-harf col-xs-6 padding-5 fi">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" readonly value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" readonly value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-2-harf col-xs-6 padding-5 fi">
			<label>Due Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_due_date" id="fromDueDate" readonly value="<?php echo $from_due_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_due_date" id="toDueDate" readonly value="<?php echo $to_due_date; ?>" />
			</div>
		</div>

		<div class="col-lg-2-harf col-md-4-harf col-sm-3 col-xs-12 padding-5">
	    <label>การจัดส่ง</label>
	    <select class="width-100 filter" name="id_sender" id="sender">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sender($id_sender); ?>
			</select>
	  </div>

		<div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5 fi">
			<label>สถานะ</label>
			<select class="form-control input-sm" name="stated">
				<option value="">เลือกสถานะ</option>
				<option value="3" <?php echo is_selected($stated, '3'); ?>>รอจัดสินค้า</option>
				<option value="4" <?php echo is_selected($stated, '4'); ?>>กำลังจัดสินค้า</option>
				<option value="5" <?php echo is_selected($stated, '5'); ?>>รอตรวจ</option>
				<option value="6" <?php echo is_selected($stated, '6'); ?>>กำลังตรวจ</option>
				<option value="7" <?php echo is_selected($stated, '7'); ?>>รอเปิดบิล</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-3 padding-5 fi">
			<label>เริ่มต้น</label>
			<select class="form-control input-sm" name="startTime">
				<?php echo selectTime($startTime); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1 col-xs-3 padding-5 fi">
			<label>สิ้นสุด</label>
			<select class="form-control input-sm" name="endTime">
				<?php echo selectTime($endTime); ?>
			</select>
		</div>

		<div class="col-lg-2-harf col-md-4 col-sm-3 col-xs-12 padding-5 fi">
			<label>รหัสสินค้า</label>
			<input type="text" class="form-control input-sm search" name="item_code" id="item_code" value="<?php echo $item_code; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5 hidden-xs">
			<label class="display-block not-show">&nbsp;</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5 hidden-xs">
			<label class="display-block not-show">&nbsp;</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5 visible-xs fi">
			<label class="display-block not-show">&nbsp;</label>
			<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5 visible-xs fi">
			<label class="display-block not-show">&nbsp;</label>
			<button type="button" class="btn btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
	<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 hidden-xs">
<div class="">
	<?php echo $this->pagination->create_links(); ?>
</div>
<?php $sort_date = $order_by == '' ? "" : ($order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<?php $sort_code = $order_by == '' ? '' : ($order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>

<div class="row">
	<div class="col-lg-3 col-md-4 col-sm-4 padding-5 hidden-xs">
		<div class="input-group width-100">
			<span class="input-group-addon">จัดออเดอร์</span>
			<input type="text" class="form-control input-sm text-center" id="order-code" autofocus />
		</div>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5 hidden-xs">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="goToProcess()">จัดสินค้า</button>
	</div>
</div>
<hr class="margin-top-15 hidden-xs">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-hover border-1 no-border-xs table-listing">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-60 hidden-xs"></th>
					<th class="fix-width-50 hidden-xs">
						<label>
							<input type="checkbox" class="ace" id="pc-all" onchange="toggleAllPc($(this))" />
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 middle text-center hidden-xs">#</th>
					<th class="fix-width-150 middle text-center hidden-xs">วันที่</th>
					<th class="fix-width-100 middle text-center hidden-xs">Due date</th>
					<th class="fix-width-150 middle hidden-xs">เลขที่เอกสาร</th>
					<th class="fix-width-150 middle hidden-xs">เลขที่อ้างอิง</th>
					<th class="fix-width-150 middle hidden-xs">ช่องทาง</th>
					<th class="fix-width-150 middle hidden-xs">การจัดส่ง</th>
					<th class="fix-width-100 middle text-center hidden-xs">จำนวน</th>
					<th class="fix-width-300 middle hidden-xs">ลูกค้า/ผู้เบิก</th>
					<th class="min-width-300 middle hidden-xs">โซน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
					<?php $whName = []; ?>
					<?php $zName = []; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $rs->qty = $this->prepare_model->get_sum_order_qty($rs->code); ?>
						<?php if( empty($whName[$rs->warehouse_code])) : ?>
							<?php $whName[$rs->warehouse_code] = warehouse_name($rs->warehouse_code); ?>
						<?php endif; ?>
						<?php if( ! empty($rs->zone_code) && empty($zName[$rs->zone_code])) : ?>
							<?php $zName[$rs->zone_code] = zone_name($rs->zone_code); ?>
						<?php endif; ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
						<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
						<?php $color = $rs->is_backorder ? 'red' : ''; ?>
            <tr id="row-<?php echo $rs->code; ?>" class="font-size-11 <?php echo $color; ?>">
							<td class="middle hidden-xs">
          <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
                <button type="button" class="btn btn-white btn-xs btn-info" onClick="goPrepare('<?php echo $rs->code; ?>')">จัดสินค้า</button>
          <?php endif; ?>
              </td>
							<td class="middle hidden-xs">
								<label>
									<input type="checkbox" class="ace pc" value="<?php echo $rs->code; ?>" />
									<span class="lbl"><?php echo $this->prepare_model->is_exists_print_logs($rs->code) ? '&nbsp;<i class="fa fa-check green"></i>' : ''; ?>	</span>
								</label>
							</td>
              <td class="middle text-center no hidden-xs"><?php echo $no; ?></td>
							<td class="middle text-center  hidden-xs"><?php echo thai_date($rs->date_add, TRUE,'/'); ?></td>
							<td class="middle text-center  hidden-xs">
								<?php echo empty($rs->due_date) ? "-" : thai_date($rs->due_date, FALSE,'/'); ?>
							</td>
							<td class="middle hidden-xs"><a href="javascript:viewOrderDetail('<?php echo $rs->code; ?>', '<?php echo $rs->role; ?>')"><?php echo $rs->code . $cn_text; ?></a></td>
							<td class="middle hidden-xs"><?php echo $rs->reference; ?></td>
							<td class="middle hidden-xs"><?php echo $rs->channels_name; ?></td>
							<td class="middle hidden-xs"><?php echo $rs->sender_name; ?></td>
							<td class="middle text-center  hidden-xs"><?php echo number($rs->qty); ?></td>
							<td class="middle  hidden-xs">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo $customer_name; ?>
								<?php endif; ?>
							</td>
							<td class="middle hidden-xs"><?php echo empty($rs->zone_code) ? NULL : $zName[$rs->zone_code]; ?></td>

							<td class="visible-xs" style="border:0px; padding:3px; font-size:12px;">
								<div class="col-xs-12" style="border:solid 1px #ccc; border-radius:5px; box-shadow:0px 1px 2px #f3ecec; padding:5px; max-width:100vw !important;">
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
										<?php if($rs->role == 'S') : ?>
										<p class="margin-bottom-3 pre-wrap"><b>ช่องทางขาย : </b> <?php echo $rs->channels_name; ?></p>
										<?php endif; ?>
										<p class="margin-bottom-3 pre-wrap"><b>การจัดส่ง : </b> <?php echo $rs->sender_name; ?></p>
										<?php if( ! empty($rs->zone_code)) : ?>
											<p class="margin-bottom-3 pre-wrap"><b>โซน : </b> <?php echo $zName[$rs->zone_code]; ?></p>
										<?php endif; ?>
										<p class="margin-bottom-3 pre-wrap"><b>คลัง : </b> <?php echo $whName[$rs->warehouse_code]; ?></p>
										<p class="margin-bottom-3 pre-wrap"><b>จำนวน : </b> <?php echo number($rs->qty); ?></p>

									</div>
									<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
										<button type="button" class="btn btn-white btn-info"
										onclick="goPrepare('<?php echo $rs->code; ?>', 'mobile')"
										style="position:absolute; top:5px; right:5px; border-radius:4px !important;">#<?php echo $no; ?> จัดสินค้า</button>
										<?php endif; ?>
								</div>
							</td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="12" class="text-center">--- No content ---</td>
          </tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="pg-footer visible-xs">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu width-20">
				<span class="width-100" onclick="refresh()">
					<i class="fa fa-refresh fa-2x white"></i><span class="fon-size-11">Refresh</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="express_order()">
					<i class="fa fa-qrcode fa-2x white"></i><span class="fon-size-11">จัดด่วน</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="goProcess()">
					<i class="fa fa-server fa-2x white"></i><span class="fon-size-12">กำลังจัด</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="toggleFilter()">
					<i class="fa fa-search fa-2x white"></i><span class="fon-size-12">ตัวกรอง</span>
				</span>
			</div>
			<div class="footer-menu width-20">
        <span class="width-100" onclick="toggleExtraMenu()">
          <i class="fa fa-qrcode fa-2x white"></i><span class="fon-size-12">Order</span>
        </span>
      </div>
		</div>
		<input type="hidden" id="filter" value="hide" />
 </div>
</div>

<div class="extra-menu slide-out visible-xs" id="extra-menu">
	<div class="width-100">
		<span class="width-100">
			<input type="text" class="form-control input-lg focus"
			style="padding-left:15px; padding-right:40px;" id="barcode-order" inputmode="none" placeholder="Barcode Order" autocomplete="off">
			<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:20px; right:22px; color:grey;"></i>
		</span>
	</div>
	<input type="hidden" id="extra" value="hide" />
</div>

<input type="hidden" id="ex" value="0" />
<script>
	$('#user').select2();
	$('#warehouse').select2();
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=1<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=1<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
