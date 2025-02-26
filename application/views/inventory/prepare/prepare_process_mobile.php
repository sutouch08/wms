<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/prepare/style'); ?>
<?php $this->load->view('inventory/prepare/process_style'); ?>

<?php if($order->state != 4) : ?>
<?php   $this->load->view('inventory/prepare/invalid_state'); ?>
<?php else : ?>
  <?php $ref = empty($order->reference) ? "" : "&nbsp;&nbsp;&nbsp;[{$order->reference}]"; ?>
  <div class="form-horizontal filter-pad move-out" id="header-pad">
    <div class="nav-title">
      <a class="pull-left margin-left-10" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
      <div class="font-size-18 text-center">ข้อมูลเอกสาร</div>
    </div>
    <div class="form-group" style="margin-top:50px;">
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
<?php if($order->allow_prepare) : ?>
  <?php $showKeyboard = get_cookie('showKeyboard'); ?>
  <?php $inputmode = $showKeyboard ? 'text' : 'none'; ?>
  <?php $keyboard = $showKeyboard ? '' : 'hide'; ?>
  <?php $qr = $showKeyboard ? 'hide' : ''; ?>

		<div class="">
			<div class="width-100 e-zone" id="zone-bc">
				<span class="width-100">
					<input type="text" class="form-control input-lg focus"
          style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="<?php echo $inputmode; ?>" placeholder="Barcode Zone" autocomplete="off">
					<i class="ace-icon fa fa-keyboard-o fa-2x <?php echo $keyboard; ?>" style="position:absolute; top:15px; right:22px; color:grey;" id="zone-keyboard" onclick="hideKeyboard('zone')"></i>
          <i class="ace-icon fa fa-qrcode fa-2x <?php echo $qr; ?>" style="position:absolute; top:15px; right:22px; color:grey;" id="zone-qr" onclick="showKeyboard('zone')"></i>
				</span>
			</div>
			<div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="item-qty">
				<button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
				<input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
				<button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
			</div>

			<div class="width-100 e-item hide" id="item-bc">
				<input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="<?php echo $inputmode; ?>"  placeholder="Barcode Item" autocomplete="off">
				<i class="ace-icon fa fa-keyboard-o fa-2x <?php echo $keyboard; ?>" style="position:absolute; top:72px; right:22px; color:grey;" onclick="hideKeyboard('item')"></i>
        <i class="ace-icon fa fa-qr fa-2x <?php echo $qr; ?>" style="position:absolute; top:72px; right:22px; color:grey;" onclick="showKeyboard('item')"></i>
			</div>
		</div>
  <?php else : ?>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
        <h4 class="red">ไม่อนุญาติให้จัดสินค้าในคลังนี้</h4>
      </div>
    </div>
  <?php endif; ?>
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
        <span class="width-100" onclick="refresh()">
          <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="changeZone()">
          <i class="fa fa-repeat fa-2x white"></i><span class="fon-size-12">เปลี่ยนโซน</span>
        </span>
      </div>

      <div class="footer-menu width-20">
        <span class="width-100" onclick="toggleComplete()">
          <i class="fa fa-check-square fa-2x white"></i><span class="fon-size-12">ครบแล้ว</span>
        </span>
      </div>

      <div class="footer-menu width-20">
        <span class="width-100" onclick="goBack()">
          <i class="fa fa-tasks fa-2x white"></i><span class="fon-size-12">รอจัด</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="toggleExtraMenu()">
          <i class="fa fa-bars fa-2x white"></i><span class="fon-size-12">เพิ่มเติม</span>
        </span>
      </div>
    </div>
  </div>
</div>

<div class="extra-menu slide-out" id="extra-menu">
  <div class="footer-menu width-20">
    <span class="width-100" onclick="clearCache()">
      <i class="fa fa-bolt fa-2x white"></i><span class="fon-size-12">Clear cache</span>
    </span>
  </div>
  <div class="footer-menu width-20">
    <span class="width-100" onclick="goProcess()">
      <i class="fa fa-shopping-basket fa-2x white"></i><span class="fon-size-12">กำลังจัด</span>
    </span>
  </div>
  <div class="footer-menu width-20">
    <span class="width-100" onclick="confirmClose()">
      <i class="fa fa-exclamation-triangle fa-2x white"></i><span class="fon-size-12">Force Close</span>
    </span>
  </div>
  <div class="footer-menu width-20">
    <span class="width-100" onclick="goToBuffer()">
      <i class="fa fa-history fa-2x white"></i><span class="fon-size-12">Buffer</span>
    </span>
  </div>
  <div class="footer-menu width-20">
    <span class="width-100" onclick="toggleHeader()">
      <i class="fa fa-file-text-o fa-2x white"></i><span class="fon-size-12">ห้วเอกสาร</span>
    </span>
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

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_mobile.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
