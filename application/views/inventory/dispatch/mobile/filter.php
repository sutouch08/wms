<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title">
	    <a class="pull-left margin-left-10" onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>
		<div class="divider-hidden"></div>
		<div class="col-xs-6 padding-5 fi">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>สถานะ</label>
			<select class="form-control input-sm" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
				<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
				<option value="4" <?php echo is_selected('4', $status); ?>>รอยืนยัน</option>
				<option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
			</select>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>วันที่</label>
			<div class="input-group width-100">
				<input type="text" class="form-control input-sm text-center width-50 from-date" inputmode="none" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm text-center width-50" inputmode="none" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>คลัง</label>
			<select class="width-100" name="warehouse" id="warehouse">
        <option value="all">ทั้งหมด</option>
        <?php echo select_common_warehouse($warehouse); ?>
      </select>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>พนักงาน</label>
			<select class="width-100" name="user" id="user">
        <option value="all">ทั้งหมด</option>
        <?php echo select_user($user); ?>
      </select>
		</div>

		<div class="col-xs-9 padding-5 fi">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-xs-3 padding-5 fi">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>

<?php echo $this->pagination->create_links(); ?>
