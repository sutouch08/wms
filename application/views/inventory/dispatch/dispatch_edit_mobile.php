<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/dispatch/mobile/style'); ?>
<?php $this->load->view('inventory/dispatch/mobile/process_style'); ?>
<?php $this->load->view('inventory/dispatch/mobile/edit_header_mobile'); ?>

<?php $this->load->view('inventory/dispatch/mobile/process_menu'); ?>

<div class="counter">
	<div class="row">
		<div class="col-xs-6 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon text-label" style="color:white !important;">Pending</span>
				<input type="text" class="form-control input-lg text-label" style="color:white !important;" id="order-qty"  value="<?php echo number($total_orders); ?>" readonly/>
			</div>
		</div>

		<div class="col-xs-6 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon text-label"  style="color:white !important;">Total</span>
				<input type="text" class="form-control input-lg text-label" style="color:white !important;" id="total-qty"  value="<?php echo number($total_qty); ?>" readonly/>
			</div>
		</div>
	</div>
</div>

<div class="incomplete-box" id="incomplete-box">
  <?php  if( ! empty($details)) : ?>
		<?php $channels = get_channels_array(); ?>
    <?php $no = 1; ?>
		<?php $totalQty = 0; ?>
		<?php $totalShipped = 0; ?>
    <?php   foreach($details as $rs) : ?>
			<?php $channels_name = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?>
			<div class="col-xs-12 incomplete-item dispatch-row" data-id="<?php echo $rs->id; ?>" id="dispatch-<?php echo $rs->id; ?>">
				<div class="row" style="padding: 3px 3px 3px 10px;">
					<div class="col-xs-6"><?php echo $rs->order_code; ?></div>
					<div class="col-xs-6"><?php echo $rs->reference; ?></div>
					<div class="col-xs-4 hide-text"><?php echo $channels_name; ?></div>
					<div class="col-xs-8 hide-text"><?php echo $rs->customer_name; ?></div>
					<div class="col-xs-6">
						<div class="input-group width-100">
							<span class="input-group-addon text-label"  style="padding-left:0px;">จำนวน[กล่อง] :</span>
							<input type="number" class="form-control input-sm text-label" id="carton-qty-<?php echo $rs->id; ?>" value="<?php echo $rs->carton_qty; ?>" readonly/>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="input-group width-100">
							<span class="input-group-addon text-label" style="padding-left:0px;">ยิงแล้ว[กล่อง] :</span>
							<input type="number" class="form-control input-sm text-label" id="carton-shipped-<?php echo $rs->id; ?>" value="<?php echo $rs->carton_shipped; ?>" readonly/>
						</div>
					</div>
				</div><!-- item -->
			</div>
      <?php $no++; ?>
			<?php $totalQty += $rs->carton_qty; ?>
			<?php $totalShipped += $rs->carton_shipped; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="total-box">
	<div class="row">
		<div class="col-xs-6 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon text-label" style="color:white !important;">Total :</span>
				<input type="text" class="form-control input-lg text-label" style="color:white !important;" id="total-carton"  value="<?php echo number($totalQty); ?>" readonly/>
			</div>
		</div>

		<div class="col-xs-6 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon text-label"  style="color:white !important;">Shipped :</span>
				<input type="text" class="form-control input-lg text-label" style="color:white !important;" id="total-shipped"  value="<?php echo number($totalShipped); ?>" readonly/>
			</div>
		</div>
	</div>
</div>

<div class="control-box" id="control-box">
  <div class="">
    <div class="width-100" id="order-add">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg text-center focus" style="padding-left:15px; padding-right:80px;" id="order-no" inputmode="none" autofocus placeholder="สแกนเพื่อเพิ่มออเดอร์">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:50px; color:grey; z-index:2;"></i>
        <i class="ace-icon fa fa-plus fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;"></i>
      </div>
    </div>
		<div class="width-100 hide" id="order-del">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg text-center focus" style="padding-left:15px; padding-right:80px;" id="del-order-no" inputmode="none" placeholder="สแกนเพื่อลบออเดอร์">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:50px; color:grey; z-index:2;"></i>
        <i class="ace-icon fa fa-minus fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;"></i>
      </div>
    </div>
  </div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
	<div class="col-xs-12 incomplete-item dispatch-row" data-id="{{id}}" id="dispatch-{{id}}">
		<div class="row" style="padding: 3px 3px 3px 10px;">
			<div class="col-xs-6">{{order_code}}</div>
			<div class="col-xs-6">{{reference}}</div>
			<div class="col-xs-4 hide-text">{{channels}}</div>
			<div class="col-xs-8 hide-text">{{customer}}</div>
			<div class="col-xs-6">
				<div class="input-group width-100">
					<span class="input-group-addon text-label"  style="padding-left:0px;">จำนวน[กล่อง] :</span>
					<input type="number" class="form-control input-sm text-label" id="carton-qty-{{id}}" value="{{carton_qty}}" readonly/>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="input-group width-100">
					<span class="input-group-addon text-label" style="padding-left:0px;">ยิงแล้ว[กล่อง] :</span>
					<input type="number" class="form-control input-sm text-label" id="carton-shipped-{{id}}" value="{{carton_shipped}}" readonly/>
				</div>
			</div>
		</div>
	</div>
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
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_mobile.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
