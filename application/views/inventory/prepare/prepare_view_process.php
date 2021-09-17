<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-xs btn-primary" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
		    <button type="button" class="btn btn-xs btn-warning" onclick="clearProcessFilter()"><i class="fa fa-retweet"></i> Reset</button>
				<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> รอจัด</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
	  <div class="col-sm-2 padding-5 first">
	    <label>เลขที่เอกสาร</label>
	    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
	  </div>

	  <div class="col-sm-2 padding-5">
	    <label>ลูกค้า</label>
	    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
	  </div>

		<div class="col-sm-2 padding-5">
	    <label>พนักงาน[จัดออเดอร์]</label>
	    <input type="text" class="form-control input-sm search" name="display_name" value="<?php echo $display_name; ?>" />
	  </div>

		<div class="col-sm-2 padding-5">
	    <label>ช่องทางขาย</label>
			<select class="form-control input-sm" name="channels" onchange="getSearch()">
	      <option value="">ทั้งหมด</option>
	      <?php echo select_channels($channels); ?>
	    </select>
	  </div>

		<div class="col-sm-2 padding-5">
	    <label>ออนไลน์</label>
			<select class="form-control input-sm" name="is_online" onchange="getSearch()">
	      <option value="2">ทั้งหมด</option>
	      <option value="1" <?php echo is_selected($is_online, '1'); ?>>ออนไลน์</option>
				<option value="0" <?php echo is_selected($is_online, '0'); ?>>ออฟไลน์</option>
	    </select>
	  </div>

		<div class="col-sm-2 padding-5 last">
	    <label>ประเภท</label>
			<select class="form-control input-sm" name="role" onchange="getSearch()">
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

		<div class="col-sm-2 padding-5 first">
	    <label>ช่องทางการชำระเงิน</label>
			<select class="form-control input-sm" name="payment" onchange="getSearch()">
				<option value="">ทั้งหมด</option>
				<?php echo select_payment_method($payment); ?>
			</select>
	  </div>

		<div class="col-sm-2 padding-5">
	    <label>วันที่</label>
	    <div class="input-daterange input-group">
	      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
	      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
	    </div>

	  </div>

		<div class="col-sm-2 padding-5">
			<label class="display-block">สถานะ</label>
			<select class="form-control input-sm" name="stated">
				<option value="">เลือกสถานะ</option>
				<option value="3" <?php echo is_selected($stated, '3'); ?>>รอจัดสินค้า</option>
				<option value="4" <?php echo is_selected($stated, '4'); ?>>กำลังจัดสินค้า</option>
				<option value="5" <?php echo is_selected($stated, '5'); ?>>รอตรวจ</option>
				<option value="6" <?php echo is_selected($stated, '6'); ?>>กำลังตรวจ</option>
				<option value="7" <?php echo is_selected($stated, '7'); ?>>รอเปิดบิล</option>
			</select>
		</div>

		<div class="col-sm-1 padding-5">
			<label class="display-block">เริ่มต้น</label>
			<select class="form-control input-sm" name="startTime">
				<?php echo selectTime($startTime); ?>
			</select>
		</div>

		<div class="col-sm-1 padding-5">
			<label class="display-block">สิ้นสุด</label>
			<select class="form-control input-sm" name="endTime">
				<?php echo selectTime($endTime); ?>
			</select>
		</div>

		<div class="col-sm-2 padding-5">
	    <label>รหัสสินค้า</label>
	    <input type="text" class="form-control input-sm search" name="item_code" id="item_code" value="<?php echo $item_code; ?>" />
	  </div>

		<div class="col-sm-2 padding-5 last">
			<label>คลัง</label>
			<select class="form-control input-sm" name="warehouse" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sell_warehouse($warehouse); ?>
			</select>
		</div>

	</div>
<hr class="margin-top-15">
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by == '' ? "" : ($order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<?php $sort_code = $order_by == '' ? '' : ($order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''); ?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-8 middle text-center sorting <?php echo $sort_date; ?>" id="sort_date_add" onclick="sort('date_add')">วันที่</th>
					<th class="width-20 middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
					<th class="middle">ลูกค้า/ผู้เบิก</th>
					<th class="width-10 middle text-center">จำนวน</th>
          <th class="width-10 middle">ช่องทาง</th>
					<th class="width-15 middle">พนักงาน</th>
					<th class="width-10 middle"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
            <tr id="row-<?php echo $rs->code; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
              <td class="middle">
								<?php echo $rs->code; ?>
								<?php echo (empty($rs->reference) ? "" : "[".$rs->reference."]"); ?>
							</td>
              <td class="middle">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo $customer_name; ?>
								<?php endif; ?>
              </td>
							<td class="middle text-center"><?php echo number($rs->qty); ?></td>
              <td class="middle"><?php echo $rs->channels_name; ?></td>

							<td class="middle"><?php echo $rs->display_name;; ?></td>
              <td class="middle text-right no-wrap">
          <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
                <button type="button" class="btn btn-minier btn-info" onClick="goPrepare('<?php echo $rs->code; ?>')">จัดสินค้า</button>
					<?php endif; ?>
					<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-warning" onClick="pullBack('<?php echo $rs->code; ?>')">ดึงกลับ</button>
          <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="8" class="text-center">--- No content ---</td>
          </tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('YmdHis'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=<?php echo date('YmdHis'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
