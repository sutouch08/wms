<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	<?php if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
	<?php endif; ?>
	<?php if($doc->status == 1 && $this->pm->can_approve) : ?>
				<button type="button" class="btn btn-sm btn-primary" id="btn-approve" onclick="approve()"><i class="fa fa-save"></i> อนุมัติ</button>
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
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
    </div>
		<div class="col-sm-2 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center edit" name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
		</div>
		<div class="col-sm-4 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled/>
		</div>
		<!-- <div class="col-sm-3 padding-5 last">
			<label>คลัง[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="warehouse" id="warehouse" value="<?php echo $doc->warehouse_name; ?>" disabled />
		</div> -->
		<div class="col-sm-3 padding-5 last">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
    <div class="col-sm-11 padding-5 first">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
    </div>
		<div class="col-sm-1 padding-5 last">
			<label class="display-block not-show">save</label>
			<?php 	if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
							<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">แก้ไข</button>
							<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">ปรับปรุง</button>
			<?php	endif; ?>
		</div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" />
<input type="hidden" name="invoice_code" id="invoice_code" value="<?php echo $doc->invoice; ?>" />


<hr class="margin-top-15"/>
<div class="row">
	<!--
	<div class="col-sm-1 padding-5 first">
    	<label>จำนวน</label>
        <input type="number" class="form-control input-sm text-center" id="qty" value="1" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label>บาร์โค้ดสินค้า</label>
        <input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off"  />
    </div>
    <div class="col-sm-1 padding-5">
    	<label class="display-block not-show">ok</label>
        <button type="button" class="btn btn-xs btn-primary" onclick="doReceive()"><i class="fa fa-check"></i> ตกลง</button>
    </div>
	-->

	<div class="col-sm-2 col-sm-offset-10 padding-5 last">
		<button type="button" class="btn btn-sm btn-danger btn-block" onclick="deleteChecked()"><i class="fa fa-trash"></i> ลบรายการที่เลือก</button>
	</div>

	<!--
	<div class="col-sm-2 col-sm-offset-9 padding-5">
		<label>เพิ่มบิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center" id="invoice-box" placeholder="ดึงใบกำกับเพิ่มเติม" />
	</div>
	<div class="col-sm-1 padding-5 last">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-info btn-block" onclick="load_invoice()">ดึงข้อมูล</button>
	</div>
	-->
</div>

<hr class=""/>
<form id="detailsForm" method="post" action="<?php echo $this->home.'/add_details/'.$doc->code; ?>">
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1" style="margin-bottom:0px;">
			<thead>
				<tr>
					<th class="width-5 text-center">ลำดับ</th>
					<th class="width-5 text-center">
					<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))"/>
					<span class="lbl"></span>
					</th>
					<th class="width-10">บาร์โค้ด</th>
					<th class="">สินค้า</th>
					<th class="width-8 text-center">อ้างอิง</th>
					<th class="width-8 text-right">จำนวน</th>
					<th class="width-8 text-right">ราคา</th>
					<th class="width-8 text-right">ส่วนลด</th>
					<th class="width-8 text-right">คืน</th>
					<th class="width-8 text-right">มูลค่า</th>
					<th class="width-5"></th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php  $total_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  foreach($details as $rs) : ?>
		<tr id="row_<?php echo $no; ?>">
			<td class="middle text-center no"><?php echo $no; ?></td>
			<td class="middle text-center">
				<input type="checkbox" class="chk ace" data-id="<?php echo $rs->id; ?>" value="<?php echo $no; ?>">
				<span class="lbl"></span>
			</td>
			<td class="middle <?php echo $no; ?>"><?php echo $rs->barcode; ?></td>
			<td class="middle"><?php echo $rs->product_code .' : '.$rs->product_name; ?></td>
			<td class="middle text-center"><?php echo $rs->invoice_code; ?></td>
			<td class="middle text-right inv_qty"><?php echo round($rs->sold_qty); ?></td>
			<td class="middle text-right"><?php echo $rs->price; ?></td>
			<td class="middle text-right"><?php echo $rs->discount_percent.' %'; ?></td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-right input-qty"
					name="qty[<?php echo $no; ?>]"
					id="qty_<?php echo $no; ?>"
					value="<?php echo $rs->qty; ?>"
					onkeyup="recalRow($(this), <?php echo $no; ?>)"
				/>
			</td>
			<td class="middle text-right amount-label" id="amount_<?php echo $no; ?>">
				<?php echo number($rs->amount, 2); ?>
			</td>
			<td class="middle text-center">
				<button type="button" class="btn btn-minier btn-danger"	onclick="removeRow(<?php echo $no; ?>, <?php echo $rs->id; ?>)">
					<i class="fa fa-trash"></i>
				</button>
			</td>
			<input type="hidden" name="item[<?php echo $no; ?>]" id="item_<?php echo $no; ?>" value="<?php echo $rs->product_code; ?>"/>
			<input type="hidden" name="sold_qty[<?php echo $no; ?>]" id="inv_qty_<?php echo $no; ?>" value="<?php echo round($rs->sold_qty); ?>"/>
			<input type="hidden" class="input-price" name="price[<?php echo $no; ?>]" id="price_<?php echo $no; ?>" value="<?php echo $rs->price; ?>" />
			<input type="hidden" name="discount[<?php echo $no; ?>]" id="discount_<?php echo $no; ?>" value="<?php echo $rs->discount_percent; ?>" />
		</tr>
<?php
		$no++;
		$total_qty += $rs->qty;
		$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<table class="table border-1">
	<tr>
		<td class="middle width-75 text-right">รวม</td>
		<td class="middle widht-10 text-right" id="total-qty"><?php echo number($total_qty); ?></td>
		<td class="middle width-10 text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
		<td class="width-5"></td>
	</tr>
</table>
</form>


<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
