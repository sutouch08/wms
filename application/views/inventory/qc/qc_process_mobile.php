<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/qc/style'); ?>
<?php $this->load->view('inventory/qc/process_style'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>

<div class="counter text-center">
  <span id="all-qty"><?php echo number($qc_qty); ?></span><span> / <?php echo number($all_qty); ?></span>
</div>

<?php $this->load->view('inventory/qc/qc_incomplete_list_mobile'); ?>
<?php $this->load->view('inventory/qc/qc_complete_list_mobile');?>
<?php $this->load->view('inventory/qc/hidden_pad'); ?>

<div id="control-box">
  <div class="">
    <div class="width-100 e-box" id="box-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:80px;" id="barcode-box" inputmode="none" placeholder="Barcode box">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:50px; color:grey; z-index:2;"></i>
        <i class="ace-icon fa fa-plus fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;" onclick="addBox()"></i>
      </div>
    </div>
    <div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="item-qty">
      <button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="width-30 input-lg focus text-center" style="padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="width-100 e-item hide" id="item-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none" placeholder="Barcode Item">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;" onclick="qcProduct()"></i>
      </div>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text" id="box-label">กรุณาระบุกล่อง</div>

<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="id_box" value="" />
<input type="hidden" id="extra" value="hide" />
<input type="hidden" id="allow-input-qty" value="<?php echo $allow_input_qty ? 1 : 0; ?>" />
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
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="changeBox()">
          <i class="fa fa-repeat fa-2x white"></i><span class="fon-size-12">เปลี่ยนกล่อง</span>
        </button>
      </div>

      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="openComplete()">
          <i class="fa fa-check-square fa-2x white"></i><span class="fon-size-12">ครบแล้ว</span>
        </button>
      </div>

      <div class="footer-menu width-20">
        <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleBoxList()">
          <i class="fa fa-cubes fa-2x white"></i><span class="fon-size-12">Box list</span>
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
  <div class="footer-menu width-20">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="goBack()">
      <i class="fa fa-tasks fa-2x white"></i><span class="fon-size-12">รายการรอจัด</span>
    </button>
  </div>
  <div class="footer-menu width-20">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="viewProcess()">
      <i class="fa fa-shopping-basket fa-2x white"></i><span class="fon-size-12">รายการกำลังจัด</span>
    </button>
  </div>
  <div class="footer-menu width-20">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="openHeader()">
      <i class="fa fa-file-text-o fa-2x white"></i><span class="fon-size-12">ห้วเอกสาร</span>
    </button>
  </div>
  <div class="footer-menu width-20">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="confirmClose()">
      <i class="fa fa-exclamation-triangle fa-2x white"></i><span class="fon-size-12">Force Close</span>
    </button>
  </div>
  <div class="footer-menu width-20">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="clearCache()">
      <i class="fa fa-bolt fa-2x white"></i><span class="fon-size-12">Clear cache</span>
    </button>
  </div>
</div>


<script id="edit-template" type="text/x-handlebarsTemplate">
  <div class="box-item" style="height:45px;">
    <a class="pull-left margin-left-10" onclick="closeEditBox()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">{{title}}</div>
  </div>
  {{#each items}}
    <div class="item-in-box">
      <table class="table" style="margin-bottom:0px;">
        <tr>
          <td class="width-50" style="border:0px;">
            กล่องที่ : {{box_no}} <br/>
            จำนวน : {{qty}} pcs.
          </td>
          <td class="width-50" style="border:0px;">
            <div class="input-group">
              <input type="number" class="form-control text-center focus e" data-qty="{{qty}}" inputmode="numeric" id="input-{{id_qc}}" />
              <span class="input-group-btn">
              <button class="btn btn-sm btn-danger" onclick="updateQty({{id_qc}})">เอาออก</button>
              </span>
            </div>
          </td>
        </tr>
      </table>
    </div>
  {{/each}}
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
  <div class="col-xs-12 complete-item" id="complete-{{id}}" data-id="{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">Order : <span class="width-30" id="order-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">Picked : <span class="width-30" id="prepared-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">Packed : <span class="width-30" id="qc-{{id}}">{{qc}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{{from_zone}}}</div>
    </div>
    <button type="button" class="btn btn-mini btn-warning"
      style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
      onclick="showEditOption('{{order_code}}', '{{product_code}}', '{{id}}')">
    <i class="fa fa-pencil"></i>
  </button>
  </div>
</script>

<script id="box-template" type="text/x-handlebarsTemplate">
  <div class="box-item" style="height:45px;">
    <a class="pull-left margin-left-10" onclick="closeBoxList()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">Box List</div>
  </div>
  {{#each this}}
    <div class="box-item">
      <div class="row">
        <div class="col-xs-3 text-center" style="padding-right:0;">
          <a href="javascript:getSelectBox('{{code}}')"><i class="fa fa-cube fa-3x"></i></a>
        </div>
        <div class="col-xs-7" style="padding-left:0;">
          <p class="box-line">กล่องที่ {{no}}  &nbsp;&nbsp;&nbsp;&nbsp; รหัส {{code}}</p>
          <p class="box-line">จำนวน : <span id="box-qty-{{id_box}}">{{qty}}</span> pcs.</p>
        </div>
        <a class="box-link font-size-24"
          href="javascript:viewBoxItems({{id_box}})"
          data-barcode="{{barcode}}"
          data-no="{{no}}" data-id="{{id_box}}">
          <i class="fa fa-angle-right"></i></a>
      </div>
    </div>
  {{/each}}
</script>

<script id="no-box-template" type="text/x-handlebarsTemplate">
  <div class="pad-title">
    <a class="margin-left-10" onclick="closeBoxList()"><i class="fa fa-angle-left fa-2x"></i></a>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-12" style="padding-left:0;">
        <h4 class="text-center">ไม่พบกล่อง</h4>
      </div>
    </div>
  </div>
</script>

<script id="box-item-template" type="text/x-handlebarsTemplate">
  <div class="item-in-box" style="height:45px;">
    <a class="pull-left margin-left-10" onclick="closeBoxDetail()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">กล่องที่ {{box_no}}</div>
  </div>
  {{#each items}}
    <div class="item-in-box">
      <p class="font-size-14">{{product_code}}</p>
      <p class="font-size-14">{{product_name}}</p>
      <p class="font-size-18">จำนวน : {{qty}} pcs.</p>
    </div>
  {{/each}}
</script>

<?php
if(!empty($barcode_list))
{
  foreach($barcode_list as $bc)
  {
    echo '<input type="hidden" class="'.$bc->barcode.'" data-code="'.$bc->product_code.'" value="1" />';
  }
}
?>

<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<?php $this->load->view('include/footer'); ?>
