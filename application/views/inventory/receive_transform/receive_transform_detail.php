<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
    <div class="col-sm-6">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      <button type="button" class="btn btn-sm btn-info" onclick="printReceived()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php if($doc->status == 1) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-sm-1 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>ใบเบิกแปรสภาพ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->order_code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>
  <div class="col-sm-2 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-sm-4 col-4-harf padding-5 last">
  	<label>ชื่อโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
	<div class="col-sm-1 col-1-harf padding-5 first">
		<label>ช่องทางการรับ</label>
		<select class="form-control input-sm header-box" name="is_wms" id="is_wms" disabled>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>Warrix</option>
			<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>WMS</option>
		</select>
	</div>

	<div class="col-sm-1 col-1-harf padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm header-box" name="status" id="status" disabled>
			<option value="0" <?php echo is_selected('0', $doc->status); ?>>ยังไม่บันทึก</option>
			<option value="1" <?php echo is_selected('1', $doc->status); ?>>บันทึกแล้ว</option>
			<option value="3" <?php echo is_selected('3', $doc->status); ?>>WMS Process</option>
			<option value="2" <?php echo is_selected('2', $doc->status); ?>>ยกเลิก</option>
		</select>
	</div>

  <div class="col-sm-9 padding-5 last">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
	</div>
  <input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
</div>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}

if($doc->status == 3)
{
	$this->load->view('on_process_watermark');
}
?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-sm-12">
    <table class="table table-striped table-bordered">
      <thead>
      	<tr class="font-size-12">
        	<th class="width-5 text-center">ลำดับ	</th>
          <th class="width-15 text-center">บาร์โค้ด</th>
          <th class="width-20 text-center">รหัสสินค้า</th>
          <th class="">ชื่อสินค้า</th>
          <th class="width-10 text-right">จำนวน</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $total_qty = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-12">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->barcode; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
              <td class="middle text-right"><?php echo number($rs->qty); ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="4" class="text-right"><strong>รวม</strong></td>
            <td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
          </tr>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform_add.js"></script>

<?php $this->load->view('include/footer'); ?>
