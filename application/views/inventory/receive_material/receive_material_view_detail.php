<?php $this->load->view('include/header'); ?>
<style>
	#receive-table > tr > td {
		padding:3px 8px !important;
	}

	.batch-row {
		background-color: #f1fcff;
	}

	.add-batch {
		margin-right: 5px;
	}

	.italic {
		font-style: italic;
	}

	.text-label {
		height: 21px;
		padding: 0 !important;
	}

</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($doc->status == 'O' &&  $this->pm->can_edit) OR ($doc->status == 'C' && $this->_SuperAdmin)) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="rollback('<?php echo $doc->code; ?>')"><i class="fa fa-refresh"></i> ย้อนสถานะ</button>
		<?php endif; ?>
		<?php if($doc->status == 'C') : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send to SAP</button>
		<?php endif; ?>
		<?php if($doc->status == 'O') : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="process('<?php echo $doc->code; ?>')"><i class="fa fa-qrcode"></i> รับสินค้า</button>
		<?php endif; ?>
		<?php if($doc->status == 'P') : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
		<?php endif; ?>
  </div>
</div>
<hr />
<?php if($doc->status == 'D') { $this->load->view('cancle_watermark'); } ?>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->date_add) ?>"  disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->shipped_date); ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->vendor_code; ?>" disabled />
	</div>
	<div class="col-lg-7 col-md-5-harf col-sm-5 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->vendor_name; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>PO No.</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->po_code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm r" id="DocCur" disabled>
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="<?php echo $doc->Rate; ?>"  disabled/>
	</div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-4 col-xs-12 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice-code" value="<?php echo $doc->invoice_code; ?>" disabled/>
	</div>

	<div class="col-lg-4 col-md-5-harf col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
		<input type="text" class="width-100" value="<?php echo empty($doc->warehouse_code) ? "" : $doc->warehouse_code.' | '.warehouse_name($doc->warehouse_code); ?>" disabled/>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="width-100 r" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled/>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label class="not-show">โซนรับสินค้า</label>
		<input type="text" class="width-100 r" id="zone-name" value="<?php echo zone_name($doc->zone_code); ?>" disabled/>
	</div>

	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>สถานะ</label>
		<input type="text" class="width-100 text-center" value="<?php echo receive_material_status_text($doc->status); ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>SAP No</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->inv_code; ?>" disabled/>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<?php $po_ref = $doc->po_code; ?>
<?php if( ! empty($po_refs)) : ?>
	<?php $po_ref = ""; ?>
	<?php $p = 1; ?>
	<?php foreach($po_refs as $ref) : ?>
		<?php $po_ref .= $p == 1 ? $ref->po_code : ", {$ref->po_code}"; ?>
		<?php $p++; ?>
	<?php endforeach; ?>
<?php endif; ?>
<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="height:400px; overflow:auto;">
		<table class="table tableFixHead" style="margin-bottom:0px; min-width:1200px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-centerf fix-header">#</th>
					<th class="fix-width-200 fix-header">รหัสสินค้า</th>
					<th class="min-width-250 fix-header">ชื่อสินค้า</th>
					<th class="fix-width-100 fix-header">Uom</th>
					<th class="fix-width-150 fix-header">Batch No.</th>
					<th class="fix-width-100 text-right fix-header">จำนวน</th>
					<th class="fix-width-100 text-right fix-header">รับแล้ว</th>
					<th class="fix-width-100 text-right fix-header">Unit Price</th>
					<th class="fix-width-120 text-right fix-header">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-table">
  <?php $no = 1; ?>
	<?php if( ! empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
			<tr class="font-size-11">
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->ItemCode; ?></td>
				<td class="middle"><?php echo $rs->ItemName; ?></td>
				<td class="middle"><?php echo $rs->unitMsr; ?></td>
				<td class="middle"></td>
				<td class="middle text-right"><?php echo number($rs->Qty, 2); ?></td>
				<td class="middle text-right"><?php echo number($rs->ReceiveQty, 2); ?></td>
				<td class="middle text-right"><?php echo number($rs->Price, 2); ?></td>
				<td class="middle text-right"><?php echo number($rs->LineTotal, 2); ?></td>
			</tr>
			<?php $no++; ?>

			<?php if( ! empty($rs->batchRows)) : ?>
				<?php foreach($rs->batchRows as $rb) : ?>
					<tr class="font-size-11 batch-row blue italic" >
						<td colspan="4" class="middle text-right">Batch No.</td>
						<td class="middle"><?php echo $rb->BatchNum; ?></td>
						<td class="middle"><?php echo number($rb->Qty, 2); ?></td>
						<td colspan="3" class="middle"></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">User</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled />
        </div>
      </div>
    </div>
  </div>


	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-3 col-sm-2 col-xs-6 control-label no-padding-right">รับแล้ว/จำนวน</label>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($doc->TotalReceived, 2); ?> / <?php echo number($doc->TotalQty, 2); ?>" disabled>
        </div>
        <label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="total-amount" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>
</div> <!-- row -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	<?php if(!empty($logs)) : ?>
		<p class="log-text">
		<?php foreach($logs as $log) : ?>
			<?php echo "* ".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp; {$log->emp_name}  &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
		<?php endforeach; ?>
		</p>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_material/receive_material_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
