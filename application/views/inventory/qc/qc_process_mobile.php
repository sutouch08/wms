<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/qc/style'); ?>
<?php $this->load->view('inventory/qc/process_style'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
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

<!-- แสดงผลกล่อง  -->
<div class="box-list move-out" id="box-list">
  <div class="box-item">
    <div class="row">
      <div class="col-xs-12" style="padding-left:0;">
        <h4 class="text-center">ไม่พบกล่อง</h4>
      </div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>

  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <div class="box-item">
    <div class="row">
      <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
      <div class="col-xs-7" style="padding-left:0;">
        <p class="box-line">กล่องที่ 1</p>
        <p class="box-line">จำนวน : 100 pcs.</p>
      </div>
      <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
    </div>
  </div>
  <?php if(!empty($box_list)) : ?>
    <?php   foreach($box_list as $rs) : ?>
      <button type="button" class="btn btn-sm btn-default" id="btn-box-<?php echo $rs->id; ?>" onclick="printBox(<?php echo $rs->id; ?>)">
        <i class="fa fa-print"></i>&nbsp;กล่องที่ <?php echo $rs->box_no; ?>&nbsp; : &nbsp;
        <span id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span>&nbsp; Pcs.
      </button>
    <?php   endforeach; ?>
  <?php endif; ?>
</div>

<div class="counter text-center">
  <span id="all-qty"><?php echo number($qc_qty); ?><span><span> / <?php echo number($all_qty); ?><span>
</div>

<?php $this->load->view('inventory/qc/qc_incomplete_list_mobile'); ?>
<?php $this->load->view('inventory/qc/qc_complete_list_mobile');?>

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
      <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none" placeholder="Barcode Item">
      <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:72px; right:22px; color:grey;"></i>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text" id="box-label">กรุณาระบุกล่อง</div>

<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="id_box" value="" />
<input type="hidden" id="header" value="hide" />
<input type="hidden" id="filter" value="hide" />
<input type="hidden" id="extra" value="hide" />
<input type="hidden" id="box-pad" value="hide" />
<input type="hidden" id="complete" value="hide" />
<input type="hidden" id="allow-input-qty" value="<?php echo $allow_input_qty ? 1 : 0; ?>" />


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
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="toggleHeader()">
      <i class="fa fa-file-text-o fa-2x white"></i><span class="fon-size-12">ห้วเอกสาร</span>
    </button>
  </div>
  <div class="footer-menu width-25">
    <button class="btn btn-block" style="border:none; padding:0; background-color:transparent !important;" onclick="confirmClose()">
      <i class="fa fa-exclamation-triangle fa-2x white"></i><span class="fon-size-12">Force Close</span>
    </button>
  </div>
</div>



  <!--************** Address Form Modal ************-->
  <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="colse" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="info_body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="optionModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="edit-title"></h4>
        </div>
        <div class="modal-body" id="edit-body">

        </div>
      </div>

    </div>
  </div>

<script id="edit-template" type="text/x-handlebarsTemplate">
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="width-20">รหัส</th>
            <th class="width-40">กล่อง</th>
            <th class="width-15 text-center">ในกล่อง</th>
            <th class="width-15 text-center">เอาออก</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
      {{#each this}}
        <tr>
          <td>{{barcode}}</td>
          <td>กล่องที่ {{box_no}}</td>
          <td class="text-center"><span id="label-{{id_qc}}">{{qty}}</span></td>
          <td class="text-center">
            <input type="number" class="form-control input-sm text-center" id="input-{{id_qc}}" />
          </td>
          <td class="text-right">
          <?php if($this->pm->can_delete) : ?>
            <button type="button" class="btn btn-sm btn-danger" onclick="updateQty({{id_qc}})">Update</button>
          <?php endif; ?>
          </td>
        </tr>
      {{/each}}
        </tbody>
      </table>
    </div>
  </div>
  </script>

  <script id="box-template" type="text/x-handlebarsTemplate">
    <div class="box-item">
      <div class="row">
        <div class="col-xs-3 text-center" style="padding-right:0;"><i class="fa fa-cube fa-3x"></i></div>
        <div class="col-xs-7" style="padding-left:0;">
          <p class="box-line">กล่องที่ {{no}}</p>
          <p class="box-line">จำนวน : {{qty}} pcs.</p>
        </div>
        <div class="box-link font-size-24"><i class="fa fa-angle-right"></i></div>
      </div>
    </div>
  </script>

  <script id="no-box-template" type="text/x-handlebarsTemplate">
    <div class="box-item">
      <div class="row">
        <div class="col-xs-12" style="padding-left:0;">
          <h4 class="text-center">ไม่พบกล่อง</h4>
        </div>
      </div>
    </div>
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
