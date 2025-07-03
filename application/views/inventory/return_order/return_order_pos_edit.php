<?php $this->load->view('include/header'); ?>
<input type="hidden" id="required_remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="savePosDoc()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
		<?php if($doc->status == 1 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" id="btn-approve" onclick="approve()"><i class="fa fa-save"></i> อนุมัติ</button>
		<?php endif; ?>
  </div>
</div>
<hr />
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center h" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting Date</label>
		<input type="text" class="form-control input-sm text-center h" id="shipped-date" value="<?php echo empty($doc->shipped_date) ? "" : thai_date($doc->shipped_date, FALSE); ?>" readonly disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" id="customer-code" onchange="checkCustomer()" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-5-harf col-md-5 col-sm-5 col-xs-8 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<input type="text" class="form-control input-sm h" id="waerhouse-name" value="<?php echo $doc->warehouse_code." | ".$doc->warehouse_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-4 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm h" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-3 col-md-4 col-sm-5-harf col-xs-8 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm h" id="zone-name" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>POS ref.</label>
		<input type="text" class="form-control input-sm h"  value="<?php echo $doc->pos_ref; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>Bill No.</label>
		<input type="text" class="form-control input-sm h"  value="<?php echo $doc->bill_code; ?>" disabled />
	</div>
	<div class="col-lg-12 col-md-9 col-sm-6-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm h" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
</div>

<?php if($doc->is_pos_api == 1) : ?>
	<hr/>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="red text-center">** เอกสารนี้ถูกสร้างโดยระบบ POS จึงไม่สามารถแก้ไขรายการได้ **</p>
		</div>
	</div>
<?php else : ?>
<?php 	$this->load->view('inventory/return_order/return_order_control'); ?>
<?php endif; ?>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="prev-customer-code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" id="prev-customer-name" value="<?php echo $doc->customer_name; ?>" />
<input type="hidden" id="prev-warehouse-code" value="<?php echo $doc->warehouse_code; ?>" />
<input type="hidden" id="prev-zone-code" value="<?php echo $doc->zone_code; ?>" />
<input type="hidden" id="zone-warehouse" value="<?php echo $doc->warehouse_code; ?>" />
<input type="hidden" id="is-pos-api" value="<?php echo $doc->is_pos_api; ?>" />

<hr/>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="margin-bottom:0px; min-width:1200px;">
			<thead>
				<tr class="fon-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-175">รหัส</th>
					<th class="min-width-200">สินค้า</th>
					<th class="fix-width-100 text-center">อ้างอิง</th>
					<th class="fix-width-120 text-center">ออเดอร์</th>
					<th class="fix-width-80 text-center">ราคา</th>
					<th class="fix-width-100 text-center">ส่วนลด</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-80 text-center">คืน</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php  $total_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  foreach($details as $rs) : ?>
		<tr class="font-size-11" id="row-<?php echo $rs->uid; ?>">
			<td class="middle text-center no"><?php echo $no; ?></td>
			<td class="middle <?php echo $no; ?>"><?php echo $rs->product_code; ?></td>
			<td class="middle"><?php echo $rs->product_name; ?></td>
			<td class="middle text-center"><?php echo $rs->pos_ref; ?></td>
			<td class="middle text-center"><?php echo $rs->bill_code; ?></td>
			<td class="middle text-center"><?php echo $rs->price; ?></td>
			<td class="middle text-center"><?php echo $rs->discount_percent.' %'; ?></td>
			<td class="middle text-center"><?php echo round($rs->sold_qty); ?></td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-center text-label input-qty"
					id="qty-<?php echo $rs->uid; ?>"
					data-id="<?php echo $rs->id; ?>"
					data-uid="<?php echo $rs->uid; ?>"
					data-pdcode="<?php echo $rs->product_code; ?>"
					data-pdname="<?php echo $rs->product_name; ?>"
					data-invoice="<?php echo $rs->invoice_code; ?>"
					data-order="<?php echo $rs->order_code; ?>"
					data-sold="<?php echo round($rs->sold_qty); ?>"
					data-price="<?php echo $rs->price; ?>"
					data-discount="<?php echo $rs->discount_percent; ?>"
					data-docentry="<?php echo $rs->DocEntry; ?>"
					data-linenum="<?php echo $rs->LineNum; ?>"
					value="<?php echo $rs->qty; ?>" readonly/>
			</td>
			<td class="middle text-right amount-label" id="amount-<?php echo $rs->uid; ?>">
				<?php echo number($rs->amount, 2); ?>
			</td>
		</tr>
<?php
		$no++;
		$total_qty += $rs->qty;
		$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8" class="middle text-right">รวม</td>
					<td class="middle text-center" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="middle text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>


<div class="modal fade" id="invoice-grid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:1200px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="invoice-title">ใบกำกับ</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="table-layout: fixed; min-width:910px;">
          <thead>
						<tr class="font-size-11">
	            <th class="fix-width-40 text-center">#</th>
	            <th class="fix-width-200 text-center">รหัส</th>
	            <th class="min-width-200 text-center">สินค้า</th>
							<th class="fix-width-100 text-center">ออเดอร์</th>
							<th class="fix-width-80 text-center">ราคา</th>
							<th class="fix-width-80 text-center">ส่วนลด</th>
	            <th class="fix-width-80 text-center">จำนวนขาย</th>
	            <th class="fix-width-80 text-center">จำนวนคืน</th>
						</tr>
          </thead>
          <tbody id="invoice-table">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="addToOrder()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>

<script id="invoice-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11" id="inv-{{uid}}">
			<td class="middle text-center">{{no}}</td>
			<td class="middle">{{product_code}}</td>
			<td class="middle">{{product_name}}</td>
			<td class="middle text-center">{{order_code}}</td>
			<td class="middle text-center">{{price_label}}</td>
			<td class="middle text-center">{{discount}} %</td>
			<td class="middle text-center">{{sold_label}}</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-center inv-qty"
					id="inv-{{uid}}"
					data-uid="{{uid}}"
					data-pdcode="{{product_code}}"
					data-pdname="{{product_name}}"
					data-invoice="{{invoice}}"
					data-docentry="{{DocEntry}}"
					data-linenum="{{LineNum}}"
					data-order="{{order_code}}"
					data-sold="{{sold_qty}}"
					data-price="{{price}}"
					data-discount="{{discount}}"
					value=""	/>
			</td>
		</tr>
	{{/each}}
</script>

<script id="row-update-template" type="text/x-handlebarsTemplate">
	<td class="middle text-center no"></td>
	<td class="middle text-center">
		<input type="checkbox" class="chk ace" data-uid="{{uid}}" value="{{uid}}">
		<span class="lbl"></span>
	</td>
	<td class="middle">{{product_code}}</td>
	<td class="middle">{{product_name}}</td>
	<td class="middle text-center">{{invoice}}</td>
	<td class="middle text-center">{{order_code}}</td>
	<td class="middle text-center">{{price}}</td>
	<td class="middle text-center">{{discount}} %</td>
	<td class="middle text-center">{{sold_qty}}</td>
	<td class="middle">
		<input type="number"
			class="form-control input-sm text-center input-qty"
			id="qty-{{uid}}"
			data-uid="{{uid}}"
			data-pdcode="{{product_code}}"
			data-pdname="{{product_name}}"
			data-invoice="{{invoice}}"
			data-docentry="{{DocEntry}}"
			data-linenum="{{LineNum}}"
			data-order="{{order_code}}"
			data-sold="{{sold_qty}}"
			data-price="{{price}}"
			data-discount="{{discount}}"
			value="{{qty}}"
			onkeyup="recalRow($(this))"	/>
	</td>
	<td class="middle text-right amount-label" id="amount-{{uid}}">{{amount}}</td>
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
	<tr class="font-size-11" id="row-{{uid}}">
		<td class="middle text-center no"></td>
		<td class="middle text-center">
			<input type="checkbox" class="chk ace" data-uid="{{uid}}" value="{{uid}}">
			<span class="lbl"></span>
		</td>
		<td class="middle">{{product_code}}</td>
		<td class="middle">{{product_name}}</td>
		<td class="middle text-center">{{invoice}}</td>
		<td class="middle text-center">{{order_code}}</td>
		<td class="middle text-center">{{price}}</td>
		<td class="middle text-center">{{discount}} %</td>
		<td class="middle text-center">{{sold_qty}}</td>
		<td class="middle">
			<input type="number"
				class="form-control input-sm text-center input-qty"
				id="qty-{{uid}}"
				data-uid="{{uid}}"
				data-pdcode="{{product_code}}"
				data-pdname="{{product_name}}"
				data-invoice="{{invoice}}"
				data-docentry="{{DocEntry}}"
				data-linenum="{{LineNum}}"
				data-order="{{order_code}}"
				data-sold="{{sold_qty}}"
				data-price="{{price}}"
				data-discount="{{discount}}"
				value="{{qty}}"
				onkeyup="recalRow($(this), '{{uid}}')"	/>
		</td>
		<td class="middle text-right amount-label" id="amount-{{uid}}">{{amount}}</td>
	</tr>
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
