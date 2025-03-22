<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn btn-100" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php if(($this->pm->can_add OR $this->pm->can_edit) && ($doc->status == 'P' OR $doc->status == 'S')) : ?>
			<button type="button" class="btn btn-white btn-success top-btn btn-100" onclick="save()"><i class="fa fa-save"></i> Save</button>
    <?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
		<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled/>
  </div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ช่องทางขาย</label>
		<select class="form-control input-sm e" id="channels" disabled>
			<option value="" data-name="">เลือก</option>
			<?php echo select_dispatch_channels($doc->channels_code); ?>
		</select>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ผู้จัดส่ง</label>
		<select class="form-control input-sm e" id="sender" disabled>
			<option value="">เลือก</option>
			<?php echo select_sender($doc->sender_code); ?>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>ทะเบียนรถ</label>
    <input type="text" class="form-control input-sm e" id="plate-no" value="<?php echo $doc->plate_no; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>จังหวัด</label>
    <input type="text" class="form-control input-sm e" id="province" value="<?php echo $doc->plate_province; ?>" disabled/>
	</div>

	<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>ชื่อคนขับ</label>
    <input type="text" class="form-control input-sm e" id="driver-name" value="<?php echo $doc->driver_name; ?>" disabled/>
	</div>
  <div class="col-lg-8-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm e" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i>  แก้ไข</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> Update</button>
  </div>
</div>
<hr class=""/>
<div class="row">
	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon">Pending</span>
			<input type="text" class="form-control input-lg text-center" id="order-qty"  value="<?php echo number($total_orders); ?>" readonly/>
			<span class="input-group-addon"><a href="javascript:viewPending()"><i class="fa fa-external-link"></i></a></span>
		</div>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon">Total</span>
			<input type="text" class="form-control input-lg text-center" id="total-qty"  value="<?php echo number($total_qty); ?>" readonly/>
		</div>
	</div>

	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<input type="text" class="form-control input-lg text-center focus" placeholder="สแกนเพื่อเพิ่มออเดอร์" id="order-no" autofocus />
	</div>
	<div class="col-lg-4-harf col-md-2 col-sm-2 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-lg btn-danger" onclick="removeChecked()">ลบรายการ</button>
	</div>
</div>
<hr class=""/>
<div class="row">
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
		<input type="text" class="form-control text-center focus" id="del-order-no" placeholder="สแกนเพื่อลบออเดอร์" />
	</div>
</div>
<hr class=""/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered">
      <thead>
				<tr>
					<th class="fix-width-50 text-center fix-header">
						<label>
							<input type="checkbox" class="ace" onchange="checkDispatchrAll($(this))">
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-50 text-center fix-header">#</th>
					<th class="fix-width-150 fix-header">เลขที่</th>
					<th class="fix-width-150 fix-header">อ้างอิง</th>
					<th class="min-width-200 fix-header">ลูกค้า</th>
					<th class="fix-width-150 fix-header">ช่องทางขาย</th>
					<th class="fix-width-100 text-center fix-header">กล่อง(ทั้งหมด)</th>
					<th class="fix-width-100 text-center fix-header">กล่อง(ยิงแล้ว)</th>
				</tr>
      </thead>
      <tbody id="dispatch-table">
				<?php $totalQty = 0; ?>
				<?php $totalShipped = 0; ?>
        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php $channels = get_channels_array(); ?>
          <?php foreach($details as $rs) : ?>
            <tr id="dispatch-<?php echo $rs->id; ?>" class="font-size-11 dispatch-row" data-id="<?php echo $rs->id; ?>">
							<td class="text-center">
                <label>
                  <input type="checkbox" class="ace dp"
                    value="<?php echo $rs->id; ?>"
                    data-code="<?php echo $rs->order_code; ?>"
                    data-ref="<?php echo $rs->reference; ?>" />
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="text-center dp-no"><?php echo $no; ?></td>
              <td><?php echo $rs->order_code; ?></td>
              <td><?php echo $rs->reference; ?></td>
              <td><?php echo $rs->customer_code." : ".$rs->customer_name; ?></td>
              <td><?php echo empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?></td>
							<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="carton-qty-<?php echo $rs->id; ?>" value="<?php echo $rs->carton_qty; ?>" readonly/></td>
							<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="carton-shipped-<?php echo $rs->id; ?>" value="<?php echo $rs->carton_shipped; ?>" readonly/></td>
            </tr>
            <?php $no++; ?>
						<?php $totalQty += $rs->carton_qty; ?>
						<?php $totalShipped += $rs->carton_shipped; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
			<tfoot>
				<tr>
					<td colspan="6" class="text-right">รวม</td>
					<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="total-carton" value="<?php echo $totalQty; ?>" readonly/></td>
					<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="total-shipped" value="<?php echo $totalShipped; ?>" readonly/></td>
				</tr>
			</tfoot>
    </table>
  </div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
	<tr id="dispatch-{{id}}" class="font-size-11 dispatch-row" data-id="{{id}}">
		<td class="text-center">
			<label>
				<input type="checkbox" class="ace dp" value="{{id}}" data-code="{{order_code}}" data-ref="{{reference}}" />
				<span class="lbl"></span>
			</label>
		</td>
		<td class="text-center dp-no"></td>
		<td>{{order_code}}</td>
		<td>{{reference}}</td>
		<td>{{customer}}</td>
		<td>{{channels}}</td>
		<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="carton-qty-{{id}}" value="{{carton_qty}}" readonly/></td>
		<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="carton-shipped-{{id}}" value="{{carton_shipped}}" readonly/></td>
	</tr>
</script>

<script id="dispatch-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      <tr>
        <td colspan="6" class="text-center">---- ไม่พบรายการ ----</td>
      </tr>
    {{else}}
      <tr id="dispatch-{{id}}" class="font-size-11 dispatch-row" data-id="{{id}}">
        <td class="text-center">
          <label>
            <input type="checkbox" class="ace dp" value="{{id}}" data-code="{{order_code}}" data-ref="{{reference}}" />
            <span class="lbl"></span>
          </label>
        </td>
        <td class="text-center dp-no">{{no}}</td>
        <td>{{order_code}}</td>
        <td>{{reference}}</td>
        <td>{{customer}}</td>
        <td>{{channels}}</td>
				<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="carton-qty-{{id}}" value="{{carton_qty}}" readonly/></td>
				<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="carton-shipped-{{id}}" value="{{carton_shipped}}" readonly/></td>
      </tr>
    {{/if}}
  {{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<script>
	var autoFocus = 1;
	window.addEventListener('load', () => {
		focus_init();
	});

	function focus_init() {
		$('.focus').focusout(function() {
			autoFocus = 1
			setTimeout(() => {
				if(autoFocus == 1) {
					setFocus();
				}
			}, 5000)
		})

		$('.focus').focusin(function() {
			autoFocus = 0;
		});
	}


	function setFocus() {		
	  $('#order-no').focus();
	}

</script>

<?php $this->load->view('include/footer'); ?>
