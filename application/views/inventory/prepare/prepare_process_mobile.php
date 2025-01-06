<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/prepare/style'); ?>
<?php $this->load->view('inventory/prepare/process_style'); ?>

<?php if($order->state != 4) : ?>
<?php   $this->load->view('inventory/prepare/invalid_state'); ?>
<?php else : ?>
  <?php $ref = empty($order->reference) ? "" : "&nbsp;&nbsp;&nbsp;[{$order->reference}]"; ?>
  <div class="form-horizontal filter-pad move-out" id="header-pad">
    <div class="form-group margin-top-30">
      <div class="col-xs-12 padding-5">
        <label>เลขที่เอกสาร</label>
        <input type="text" class="width-100" value="<?php echo $order->code . $ref; ?> " readonly/>
      </div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>ลูกค้า/ผู้เบิก/ผู้ยืม</label>
        <input type="text" class="width-100" value="<?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>" readonly/>
      </div>
    </div>

    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>คลัง</label>
        <input type="text" class="width-100" value="<?php echo $order->warehouse_name; ?>" readonly/>
      </div>
    </div>

		<?php if($order->role == 'S') : ?>
	    <div class="form-group">
	      <div class="col-xs-12 padding-5">
	        <label>ช่องทาง</label>
	        <input type="text" class="width-100" value="<?php echo $order->channels_name; ?>" readonly/>
	      </div>
	    </div>
		<?php endif; ?>

    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>วันที่</label>
        <input type="text" class="width-100" value="<?php echo thai_date($order->date_add); ?>" readonly/>
      </div>
    </div>

    <div class="form-group">
      <div class="col-xs-12 padding-5">
        <label>หมายเหตุ</label>
        <textarea class="form-control" rows="5" readonly><?php echo $order->remark; ?></textarea>
      </div>
    </div>
  </div>


	<div class="width-100 header-info hide-text">
    <div class="col-xs-12 font-size-24 text-center" style="padding:4px;">
      <span id="pick-qty"><?php echo $pickedQty; ?></span>
      &nbsp;/&nbsp;
      <span id="order-qty"><?php echo $orderQty; ?></span>
    </div>
	</div>
	<div id="control-box">
		<div class="">
			<div class="width-100 e-zone" id="zone-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="none" placeholder="Barcode Zone">
					<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:15px; right:22px; color:grey;" onclick="showItem()"></i>
				</span>
			</div>
			<div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="item-qty">
				<button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
				<input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" placeholder="QTY" value="1">
				<button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
			</div>

			<div class="width-100 e-item hide" id="item-bc">
				<input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none" placeholder="Barcode Item">
				<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:72px; right:22px; color:grey;"></i>
			</div>
		</div>
	</div>

  <div class="width-100 text-center bottom-info hide-text" id="zone-name">กรุณาระบุโซน</div>

  <hr class="margin-top-10 margin-bottom-10"/>
  <div class="row">
    <?php $this->load->view('inventory/prepare/prepare_incomplete_list_mobile');  ?>
    <?php $this->load->view('inventory/prepare/prepare_completed_list_mobile'); ?>
  </div><!--rox-->

<?php endif; //--- endif order->state ?>
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="warehouse_code" value="<?php echo $order->warehouse_code; ?>" />
<input type="hidden" id="zone_code" />
<input type="hidden" id="header" value="hide" />
<input type="hidden" id="filter" value="hide" />
<input type="hidden" id="extra" value="hide" />
<input type="hidden" id="complete" value="hide" />
<input type="hidden" id="finished" value="<?php echo $finished ? 1 : 0; ?>" />

<div class="pg-footer visible-xs">
  <div class="pg-footer-inner">
    <div class="pg-footer-content text-right">
      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="refresh()">
          <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
        </button>
      </div>
      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="changeZone()">
          <i class="fa fa-repeat fa-2x white"></i><span class="fon-size-12">เปลี่ยนโซน</span>
        </button>
      </div>

      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleComplete()">
          <i class="fa fa-check-square fa-2x white"></i><span class="fon-size-12">ครบแล้ว</span>
        </button>
      </div>

      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleHeader()">
          <i class="fa fa-file-text-o fa-2x white"></i><span class="fon-size-12">ห้วเอกสาร</span>
        </button>
      </div>
      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleExtraMenu()">
          <i class="fa fa-bars fa-2x white"></i><span class="fon-size-12">เพิ่มเติม</span>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="extra-menu slide-out" id="extra-menu">
  <div class="footer-menu width-25">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="goBack()">
      <i class="fa fa-tasks fa-2x white"></i><span class="fon-size-12">รายการรอจัด</span>
    </button>
  </div>
  <div class="footer-menu width-25">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="goProcess()">
      <i class="fa fa-shopping-basket fa-2x white"></i><span class="fon-size-12">รายการกำลังจัด</span>
    </button>
  </div>
  <div class="footer-menu width-25">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="goToBuffer()">
      <i class="fa fa-history fa-2x white"></i><span class="fon-size-12">Buffer</span>
    </button>
  </div>
  <div class="footer-menu width-25">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="confirmClose()">
      <i class="fa fa-exclamation-triangle fa-2x white"></i><span class="fon-size-12">Force Close</span>
    </button>
  </div>
</div>

<script id="incomplete-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 incomplete-item" id="incomplete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap b-click " id="b-click-{{id}}">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-{{id}}">{{balance}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{stock_in_zone}}</div>
    </div>
    <span class="badge-qty" id="badge-qty-{{id}}">{{balance}}</span>
  </div>
</script>

<script id="incomplete-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 incomplete-item" id="incomplete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap b-click " id="b-click-{{id}}">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-{{id}}">{{balance}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{stock_in_zone}}</div>
    </div>
    <span class="badge-qty" id="badge-qty-{{id}}">{{balance}}</span>
  </div>
</script>

<script id="complete-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 complete-item" id="complete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-{{id}}">{{balance}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{{from_zone}}}</div>
    </div>
    <button type="button" class="btn btn-mini btn-danger"
      style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
      onclick="removeBuffer('{{order_code}}', '{{product_code}}', '{{id}}')">
    <i class="fa fa-trash"></i>
  </button>
  </div>
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
