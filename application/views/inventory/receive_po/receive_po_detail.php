<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?> </h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      <button type="button" class="btn btn-xs btn-info" onclick="printReceived()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php if($doc->status == 1) : ?>
			<button type="button" class="btn btn-xs btn-success" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>
			<?php if($this->isAPI && $doc->is_wms == 1 && ($doc->status == 3 OR $doc->status == 0)) : ?>
			<button type="button" class="btn btn-xs btn-success" onclick="sendToWms()"><i class="fa fa-send"></i> Send to WMS</button>
			<?php endif; ?>
      <?php if($this->pm->can_delete && $doc->status != 2) : ?>
        <button type="button" class="btn btn-xs btn-danger" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i> ยกเลิก</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>รหัสผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vendor_code; ?>" disabled />
  </div>
  <div class="col-lg-6 col-md-5-harf col-sm-6 col-xs-8 padding-5">
  	<label>ผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vendor_name; ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ใบสั่งซื้อ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->po_code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>ใบขออนุมัติ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->request_code; ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
		<label>ช่องทางการรับ</label>
		<select class="form-control input-sm" disabled>
			<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>WMS</option>
			<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>Warrix</option>
		</select>
	</div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-lg-5-harf col-md-5-harf col-sm-7 col-xs-8 padding-5">
  	<label>ชื่อโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled/>
	</div>
	<?php if($doc->status == 2) : ?>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-8 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-5 col-xs-8 padding-5">
			<label>เหตุผลการยกเลิก</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled />
		</div>
	<?php else : ?>
  <div class="col-lg-9 col-md-9 col-sm-10 col-xs-4 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<?php endif; ?>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled />
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
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Currency</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->currency; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Rate</label>
		<input type="number" class="form-control input-sm text-center" id="DocRate" value="<?php echo $doc->rate; ?>" disabled/>
	</div>
	<div class="divider-hidden">	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered" style="min-width:1000px;">
      <thead>
      	<tr class="font-size-12">
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-150 text-center">บาร์โค้ด</th>
          <th class="fix-width-150 text-center">รหัสสินค้า</th>
          <th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-100 text-right">จำนวนรับ</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $total_qty = 0; ?>
					<?php $total_receive = 0; ?>
					<?php $total_amount = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-12">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->barcode; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->qty); ?></td>
							<td class="middle text-right"><?php echo number($rs->receive_qty); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
						<?php $total_receive += $rs->receive_qty; ?>
						<?php $total_amount += $rs->amount; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="5" class="text-right"><strong>รวม</strong></td>
            <td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_receive); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
          </tr>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>
</div>

<?php if(!empty($approve_logs)) : ?>
	<div class="row">
		<?php foreach($approve_logs as $logs) : ?>
		<div class="col-sm-12 text-right padding-5 first last">
			<?php if($logs->approve == 1) : ?>
			  <span class="green">
					อนุมัติโดย :
					<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
				</span>
			<?php else : ?>
				<span class="red">
				ยกเลิกโดย :
				<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
			  </span>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
	</div>
<?php endif; ?>


<?php $this->load->view('inventory/receive_po/cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
