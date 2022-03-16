<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-xs btn-success" onclick="export_diff()">Export ยอดต่าง</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า/ผู้เบิก</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="Y" <?php echo is_selected('Y', $status); ?>>เข้าแล้ว</option>
      <option value="N" <?php echo is_selected('N', $status); ?>>ยังไม่เข้า</option>
      <option value="E" <?php echo is_selected('E', $status); ?>>Error</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
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
      <span class="red">ERROR</span> = เกิดข้อผิดพลาด, &nbsp;
      <span class="blue">NC</span> = ยังไม่เข้า SAP
    </p>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:1340px;">
      <thead>
        <tr>
          <th class="text-center" style="width:40px;">#</th>
          <th class="text-center" style="width:100px;">วันที่</th>
          <th class="" style="width:120px;">เลขที่เอกสาร </th>
          <th class="" style="width:100px;">รหัสลูกค้า</th>
          <th class="" style="width:350px;">ชื่อลูกค้า</th>
          <th class="" style="width:140px;">เข้าถังกลาง</th>
          <th class="" style="width:140px;">SAP Update</th>
          <th class="text-center" style="width:70px;">สถานะ</th>
					<th class="" style="width:200px;">หมายเหตุ</th>
					<th class="" style="width:100px;"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>
        <tr class="font-size-12" id="row-<?php echo $no; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>

          <td class="middle text-center"><?php echo thai_date($rs->DocDate); ?></td>

          <td class="middle">
						<?php if($rs->U_BOOKCODE === 'WM') : ?>
							<a href="javascript:void(0)" onclick="getConsign('<?php echo $rs->U_ECOMNO; ?>')">
							<?php echo $rs->U_ECOMNO; ?>
							</a>
						<?php else : ?>
						<a href="javascript:void(0)" onclick="getInvoice('<?php echo $rs->U_ECOMNO; ?>')">
						<?php echo $rs->U_ECOMNO; ?>
						</a>
						<?php endif; ?>
					</td>

          <td class="middle"><?php echo $rs->CardCode; ?></td>

          <td class="middle hide-text"><?php echo $rs->CardName; ?></td>

          <td class="middle" ><?php echo thai_date($rs->F_E_CommerceDate, TRUE); ?></td>

          <td class="middle">
						<?php
							if(!empty($rs->F_SapDate))
							{
								echo thai_date($rs->F_SapDate, TRUE);
							}
					 	?>
				 	</td>
					<td class="middle text-center">
            <?php if($rs->F_Sap === NULL) : ?>
              <span class="blue">NC</span>
            <?php elseif($rs->F_Sap === 'N') : ?>
              <span class="red">ERROR</span>
						<?php elseif($rs->F_Sap === 'Y') : ?>
							<span class="green">สำเร็จ</span>
            <?php endif; ?>
          </td>
          <td class="middle">
            <?php
            if($rs->F_Sap === 'N')
            {
              echo $rs->Message;
            }
            ?>
          </td>
					<td class="middle text-right">

						<button type="button" class="btn btn-minier btn-info" onclick="get_detail(<?php echo $rs->DocEntry; ?>)">
							<i class="fa fa-eye"></i>
						</button>
						<?php if(($rs->F_Sap === 'N' OR $rs->F_Sap == NULL)) : ?>
							<button type="button" class="btn btn-minier btn-danger" onclick="deleteTemp(<?php echo $rs->DocEntry; ?>, '<?php echo $rs->U_ECOMNO; ?>', <?php echo $no; ?>)">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
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
<form id="reportForm" method="post" action="<?php echo $this->home; ?>/export_diff">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>
<script src="<?php echo base_url(); ?>scripts/inventory/temp/temp_list.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
