<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if (($this->pm->can_add OR $this->pm->can_edit) && ($doc->status == 0 OR $doc->status == 3)) : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					<i class="ace-icon fa fa-save icon-on-left"></i>
					บันทึก
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="primary">
						<a href="javascript:save(0)">บันทึกเป็นดราฟท์</a>
					</li>
					<li class="success">
						<a href="javascript:save(1)">บันทึกรับเข้าทันที</a>
					</li>
					<li class="purple">
						<a href="javascript:save(3)">บันทึกรอรับ</a>
					</li>
				</ul>
			</div>
		<?php endif; ?>
		<?php if ($doc->status == 1 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-primary" id="btn-approve" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
		<?php endif; ?>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" id="date-add" name="date_add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center e" id="customer-code" value="<?php echo $doc->customer_code; ?>" autocomplete="off" disabled />
	</div>
	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm e" id="customer-name" value="<?php echo $doc->customer_name; ?>" readonly disabled />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่บิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center e" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>GP(%)</label>
		<input type="number" class="form-control input-sm text-center e" id="gp" value="<?php echo $doc->gp; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซนฝากขาย</label>
		<input type="text" class="form-control input-sm e" id="from-zone" autocomplete="off" value="<?php echo $doc->from_zone_code; ?>" disabled />
	</div>
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label class="not-show">โซนฝากขาย</label>
		<input type="text" class="form-control input-sm e" id="from-zone-name" value="<?php echo $doc->from_zone_name; ?>" readonly disabled />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm e" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm e" id="zone-name" value="<?php echo $doc->zone_name; ?>" readonly disabled />
	</div>
	<div class="col-lg-11 col-md-11 col-sm-11 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" id="remark" value="<?php echo $doc->remark; ?>" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" disabled />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">save</label>
		<?php if ($doc->status == 0 && ($this->pm->can_add or $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-sm btn-white btn-warning btn-block" id="btn-edit" style="height:30px;" onclick="editHeader()">Edit</button>
			<button type="button" class="btn btn-sm btn-white btn-success btn-block hide" id="btn-update" style="height:30px;" onclick="update()">Update</button>
		<?php endif; ?>
	</div>
</div>

<hr class="margin-top-15" />

<div class="row">
	<div class="col-sm-4 col-xs-12 padding-5">
		<label>เลขที่บิล</label>
		<span id="invoice_list" class="form-control input-sm" disabled><?php echo $doc->invoice_list; ?></span>
	</div>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>มูลค่าบิล</label>
		<input type="number" class="form-control input-sm text-center" name="bill_amount" id="bill_amount" value="<?php echo $doc->invoice_amount; ?>" disabled />
	</div>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>เพิ่มบิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center" id="invoice-box" placeholder="ดึงใบกำกับเพิ่มเติม" />
	</div>
	<div class="col-sm-1 col-xs-12 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-info btn-block" onclick="add_invoice()">เพิ่มบิล</button>
	</div>
	<div class="col-md-1 col-sm-1 hidden-xs"></div>

	<div class="divider visible-xs"></div>

	<div class="col-md-2 col-sm-3 col-xs-12 padding-5">
		<?php if (($this->pm->can_add or $this->pm->can_edit) && $doc->status == 0) : ?>
			<label class="display-block not-show">btn</label>
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="load_stock_in_zone()">
				โหลดสินค้าในโซน
			</button>
		<?php endif; ?>
	</div>
</div>
<div class="row">


</div>
<hr class="margin-top-10 margin-bottom-10" />
<?php $this->load->view('inventory/return_consignment/return_consignment_control'); ?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-narrow border-1" style="margin-bottom:0px; min-width:970px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-40 text-center">
						<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))" />
						<span class="lbl"></span>
					</th>
					<th class="fix-width-100">บาร์โค้ด</th>
					<th class="min-width-250">สินค้า</th>
					<th class="fix-width-100 text-center">อ้างอิง</th>
					<th class="fix-width-80 text-right">ราคา</th>
					<th class="fix-width-80 text-right">ส่วนลด(%)</th>
					<th class="fix-width-80 text-right">คืน</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
					<th class="fix-width-40"></th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php $total_qty = 0; ?>
				<?php $total_amount = 0; ?>
				<?php $no = 0; ?>
				<?php if (!empty($details)) : ?>
					<?php foreach ($details as $rs) : ?>
						<?php $no++; ?>
						<tr id="row_<?php echo $no; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center">
								<input type="checkbox" class="chk ace" data-id="<?php echo $rs->id; ?>" value="<?php echo $no; ?>">
								<span class="lbl"></span>
							</td>
							<td class="middle <?php echo $no; ?>"><?php echo $rs->barcode; ?></td>
							<td class="middle"><?php echo $rs->product_code . ' : ' . $rs->product_name; ?></td>
							<td class="middle text-center"><?php echo $rs->invoice_code; ?> </td>
							<td class="middle text-right">
								<input type="number"
									class="form-control input-sm text-right"
									name="price[<?php echo $no; ?>]"
									id="price_<?php echo $no; ?>"
									value="<?php echo $rs->price; ?>"
									onkeyup="recalRow(<?php echo $no; ?>)" />
							</td>
							<td class="middle text-right">
								<input type="number"
									class="form-control input-sm text-right"
									name="discount[<?php echo $no; ?>]"
									id="discount_<?php echo $no; ?>"
									value="<?php echo round($rs->discount_percent, 2); ?>"
									onkeyup="recalRow(<?php echo $no; ?>)" />
							</td>
							<td class="middle">
								<input type="number"
									class="form-control input-sm text-right input-qty"
									name="qty[<?php echo $no; ?>]"
									id="qty_<?php echo $no; ?>"
									value="<?php echo $rs->qty; ?>"
									data-no="<?php echo $no; ?>"
									onkeyup="recalRow(<?php echo $no; ?>)" />
							</td>
							<td class="middle text-right amount-label" id="amount_<?php echo $no; ?>">
								<?php echo number($rs->amount, 2); ?>
							</td>
							<td class="middle text-center">
								<a href="javascript:void(0)" onclick="removeRow(<?php echo $no; ?>, <?php echo $rs->id; ?>)"><i class="fa fa-times fa-lg red"></i></a>
							</td>
							<input type="hidden" id="barcode_<?php echo (empty($rs->barcode) ? $rs->product_code : $rs->barcode); ?>" value="<?php echo $no; ?>" />
							<input type="hidden" name="item[<?php echo $no; ?>]" id="item_<?php echo $no; ?>" value="<?php echo $rs->product_code; ?>" />
							<input type="hidden" name="item_name[<?php echo $no; ?>]" id="item_name_<?php echo $no; ?>" value="<?php echo $rs->product_name; ?>" />
						</tr>
						<?php
						$total_qty += $rs->qty;
						$total_amount += $rs->amount;
						?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>			
		</table>
	</div>
</div>
<div class="divider-hidden"></div>
<div class="divider-hidden"></div>
<div class="row">
	<div class="col-lg-3 col-lg-offset-7 col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-7 col-xs-6 text-right"><label class="padding-top-5 text-right">Total Qty</label></div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($total_qty); ?>" readonly />
	</div>
	<div class="divider-hidden"></div>
	<div class="col-lg-3 col-lg-offset-7 col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-7 col-xs-6 text-right"><label class="padding-top-5 text-right">Total Amount</label></div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($total_amount, 2); ?>" readonly />
	</div>
</div>
<input type="hidden" id="no" value="<?php echo $no; ?>" />


<script type="text/x-handlebarsTemplate" id="row-template">
	<tr id="row_{{no}}">
		<td class="middle text-center no"></td>
		<td class="middle text-center">
			<input type="checkbox" class="chk ace" data-id="" value="{{no}}">
			<span class="lbl"></span>
		</td>
		<td class="middle {{no}}">{{barcode}}</td>
		<td class="middle">{{code}} : {{name}}</td>
		<td class="middle text-center invoice">{{invoice}}</td>
		<td class="middle text-right">
			<input type="number"
				class="form-control input-sm text-right"
				name="price[{{no}}]"
				id="price_{{no}}"
				value="{{price}}"
				onkeyup="recalRow({{no}})" />
		</td>
		<td class="middle text-right">
			<input type="number"
			class="form-control input-sm text-right"
			name="discount[{{no}}]"
			id="discount_{{no}}"
			value="{{discount}}"
			onkeyup="recalRow({{no}})"
			/>
		</td>
		<td class="middle">
			<input type="number"
			class="form-control input-sm text-right input-qty"
			name="qty[{{no}}]"
			id="qty_{{no}}"
			value="{{qty}}"
			data-no = "{{no}}"
			onkeyup="recalRow({{no}})"
			/>
		</td>
		<td class="middle text-right amount-label" id="amount_{{no}}">{{amount}}</td>
		<td class="middle text-center">
			<button type="button" class="btn btn-minier btn-danger" onclick="removeRow({{no}}, 0)">
			<i class="fa fa-trash"></i>
			</button>
		</td>
		<input type="hidden" id="barcode_{{barcode}}" value="{{no}}"/>
		<input type="hidden" name="item[{{no}}]" id="item_{{no}}" value="{{code}}"/>
		<input type="hidden" name="item_name[{{no}}]" id="item_name_{{no}}" value="{{name}}"/>
	</tr>
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>