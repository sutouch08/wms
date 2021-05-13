<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6"></div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>ประเภทเอกสาร</label>
    <select class="form-control input-sm" name="type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="RT">รับเข้า(RT)</option>
			<option value="RN">รับคืน(RN)</option>
			<option value="SM">รับคืน(SM)</option>
			<option value="WR">รับเข้า(WR)</option>
			<option value="WW">โอนคลัง(WW)</option>
		</select>
  </div>

  <div class="col-sm-2 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $status); ?>>เข้าแล้ว</option>
      <option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่เข้า</option>
      <option value="3" <?php echo is_selected('3', $status); ?>>Error</option>
    </select>
  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-sm-12">
    <p class="pull-right">
      สถานะ : ว่างๆ = ปกติ, &nbsp;
      <span class="red">ERROR</span> = เกิดข้อผิดพลาด, &nbsp;
      <span class="blue">NC</span> = ยังไม่เข้า IX
    </p>
  </div>
  <div class="col-sm-12">
    <table class="table table-striped border-1 dataTable">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-15">เลขที่เอกสาร </th>
          <th class="width-15">เข้า Temp</th>
          <th class="width-15">เข้า IX</th>
          <th class="width-5 text-center">สถานะ</th>
					<th class="">หมายเหตุ</th>
					<th class="width-5"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(5) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12">
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
          <td class="middle" ><?php echo thai_date($rs->temp_date, TRUE); ?></td>

          <td class="middle">
						<?php
							if($rs->status != 0 && !empty($rs->ix_date))
							{
								echo thai_date($rs->ix_date, TRUE);
							}
					 	?>
				 	</td>
					<td class="middle text-center">
            <?php if($rs->status == 0) : ?>
              <span class="blue">NC</span>
            <?php elseif($rs->status == 3) : ?>
              <span class="red">ERROR</span>
						<?php elseif($rs->status == 1) : ?>
							<span class="green">สำเร็จ</span>
            <?php endif; ?>
          </td>
          <td class="middle">
            <?php
            if($rs->status == 3)
            {
              echo $rs->message;
            }
            ?>
          </td>
					<td class="middle text-right">
						<button type="button" class="btn btn-minier btn-info" onclick="getDetails(<?php echo $rs->id; ?>)">
							<i class="fa fa-eye"></i>
						</button>
					</td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/wms/wms_temp_receive.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
