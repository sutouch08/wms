<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/qc/webcam_style'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-primary top-btn" onclick="goBack()"><i class="fa fa-chevron-left"></i>&nbsp; รอตรวจ (ESC)</button>
    <button type="button" class="btn btn-white btn-info top-btn" onclick="viewProcess()"><i class="fa fa-cube"></i> กำลังตรวจ</button>
    <?php if ($order->channels_code == '0009' && ! empty($order->reference) && is_true(getConfig('WRX_TIKTOK_API'))) : ?>
      <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderTiktok('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> Print Label</button>
    <?php elseif ($order->channels_code == 'SHOPEE' && ! empty($order->reference) && is_true(getConfig('WRX_SHOPEE_API'))) : ?>
      <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderShopee('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> Print Label</button>
    <?php elseif ($order->channels_code == 'LAZADA' && ! empty($order->reference) && is_true(getConfig('WRX_LAZADA_API'))) : ?>
      <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderLazada('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> Print Label</button>
    <?php endif; ?>
    <?php if (is_true(getConfig('PORLOR_API'))) : ?>
      <?php if ($order->id_sender == getConfig('PORLOR_SENDER_ID')) : ?>
        <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderPorlor('<?php echo $order->code; ?>')"><i class="fa fa-print"></i> Print Porlor Label</button>
      <?php endif; ?>
    <?php endif; ?>
    <?php if (is_true(getConfig('SPX_API'))) : ?>
      <?php if ($order->id_sender == getConfig('SPX_ID')) : ?>
        <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderSPX('<?php echo $order->code; ?>')"><i class="fa fa-print"></i> Print SPX Label</button>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<hr />
<div class="row">
  <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 ">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
        <div class="webcam">
          <video id="video" autoplay muted></video>
          <div id="stop-watch">00:00:00</div>
        </div>
      </div>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 margin-top-10">
        <div class="btn-group width-100">
          <button class="btn btn-xs btn-purple width-25" onclick="selectDevices()" title="Select Camera"><i class="fa fa-video-camera"></i></button>
          <button class="btn btn-xs btn-info width-25" id="start-camera" onclick="startCamera()" title="Preview (F9)"><i class="fa fa-play"></i></button>
          <button class="btn btn-xs btn-info width-25 hide" id="stop-camera" onclick="stopCamera()" title="Stop Preview (F9)"><i class="fa fa-stop"></i> </button>
          <button class="btn btn-xs btn-primary width-25" id="start-record" onclick="startRecord()" title="Start Record (F8)"><i class="fa fa-circle"></i></button>
          <button class="btn btn-xs btn-primary width-25 hide" id="pause-record" onclick="pauseRecord()" title="Pause Recording (F8)"><i class="fa fa-pause"></i></button>
          <button class="btn btn-xs btn-primary width-25 hide" id="resume-record" onclick="resumeRecord()" title="Resume Record (F8)"><i class="fa fa-circle"></i></button>
          <button class="btn btn-xs btn-danger width-25" id="stop-record" onclick="stopRecord()" title="Stop Record (F10)"><i class="fa fa-stop"></i></button>
        </div>
        <a target="_blank" class="recorded-preview"></a>
      </div>
    </div>
  </div>

  <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
    <?php $this->load->view('inventory/qc/tab/qc_box'); ?>
    <?php $this->load->view('inventory/qc/tab/qc_control'); ?>
  </div>
  <div class="divider"></div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="tabable">
      <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#incomplete-tab" aria-expanded="true">รายการยังไม่ครบ</a></li>
        <li class=""><a data-toggle="tab" href="#complete-tab" aria-expanded="true">รายการครบแล้ว</a></li>
        <li class=""><a data-toggle="tab" href="#header-tab" aria-expanded="false">ข้อมูลเอกสาร</a></li>
      </ul>
      <div class="tab-content">
        <?php $this->load->view('inventory/qc/tab/header_tab'); ?>
        <?php $this->load->view('inventory/qc/tab/incomplete_tab'); ?>
        <?php $this->load->view('inventory/qc/tab/complete_tab'); ?>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
<input type="hidden" id="id_box" value="<?php echo $active_box_id; ?>" />
<input type="hidden" id="state" value="<?php echo $order->state; ?>">
<input type="hidden" id="order-code"
  value="<?php echo $order->code; ?>"
  data-role="<?php echo $order->role; ?>"
  data-user="<?php echo $this->_user->uname; ?>"
  data-endpoint="<?php echo getConfig('VIDEO_ON_PACK_ENDPOINT'); ?>" />

<input type="hidden" id="video-config"
  data-audio-required="<?php echo getConfig('AUDIO_ON_PACK'); ?>"
  data-auto-record="<?php echo getConfig('VIDEO_ON_PACK_AUTO_RECORD'); ?>" />

<div class="modal fade" id="devices-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:300px; max-width:95%; margin-left:auto; margin-right:auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Choose Camera</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label><i class="fa fa-video-camera"></i>&nbsp; Choose Camera</label>
            <select class="form-control input-sm" id="video-devices">
              <option value="">Select Video Device</option>
            </select>
          </div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide" id="audio-option">
            <label><i class="fa fa-microphone"></i>&nbsp; Choose Microphone</label>
            <select class="form-control input-sm" id="audio-devices">
              <option value="">Select Audio Device</option>
            </select>
          </div>

        </div>
        <div class="err-label" id="devices-error"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success btn-100" onclick="saveDevicesId()">Save</button>
      </div>
    </div>
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
        <h4 class="modal-title" style="font-size:16px; text-align:center" id="edit-title"></h4>
      </div>
      <div class="modal-body" id="edit-body">

      </div>
    </div>

  </div>
</div>

<script id="edit-template" type="text/x-handlebarsTemplate">
  <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
        <table class="table border-1">
          <thead>
            <tr class="font-size-12">
              <th class="min-width-150">BOX CODE</th>
              <th class="fix-width-100">BOX No.</th>
              <th class="fix-width-100 text-center">QTY.</th>
              <th class="fix-width-100 text-center">Remove</th>
              <th class="fix-width-80"></th>
            </tr>
          </thead>
          <tbody>
        {{#each this}}
          <tr class="font-size-12">
            <td>{{barcode}}</td>
            <td>กล่องที่ {{box_no}}</td>
            <td class="text-center"><span id="label-{{id_qc}}">{{qty}}</span></td>
            <td class="text-center">
              <input type="number" class="form-control input-xs text-center" data-product="{{product_code}}" data-id="{{id_qc}}" data-limit="{{qty}}" id="input-{{id_qc}}" />
            </td>
            <td class="text-right">
            <?php if ($this->pm->can_delete) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="updateQty({{id_qc}})">Update</button>
            <?php endif; ?>
            </td>
          </tr>
        {{/each}}
          </tbody>
        </table>
      </div>
    </div>
    </script>

<div class="modal fade" id="edit-box-modal" tabindex="-1" role="dialog" aria-labelledby="optionModal" aria-hidden="true">
  <div class="modal-dialog" style="width:600px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="edit-box-title"></h4>
      </div>
      <div class="modal-body" id="edit-box-table">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-xs btn-100" onclick="updateEditQty()"> Update</button>
      </div>
    </div>
  </div>
</div>

<script id="edit-box-template" type="text/x-handlebarsTemplate">
  <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="fix-width-50 text-center">#</th>
              <th class="min-width-200">รหัสสินค้า</th>
              <th class="fix-width-100 text-center">จำนวน</th>
              <th class="fix-width-100 text-center">เอาออก</th>
            </tr>
          </thead>
          <tbody>
        {{#each this}}
          <tr id="edit-row-{{id}}">
            <td class="middle text-center">{{no}}</td>
            <td class="middle">{{product_code}}</td>
            <td class="middle text-center"><span id="label-{{id}}">{{qty}}</span></td>
            <td class="middle text-center">
              <input type="number" class="width-100 text-center edit-input-qty e" data-item="{{product_code}}" data-qty="{{qty}}" data-id="{{id}}" id="edit-input-{{id}}" onkeyup="checkEditQty({{id}})"/>
            </td>
          </tr>
        {{/each}}
          </tbody>
        </table>
      </div>
    </div>
  </script>



<?php
if (!empty($barcode_list))
{
  foreach ($barcode_list as $bc)
  {
    echo '<input type="hidden" id="bc-' . $bc->barcode . '" data-code="' . $bc->product_code . '" value="1" />';
  }
}
?>


<script>
  function shipOrderTiktok(reference) {
    load_in();

    $.ajax({
      url: HOME + 'ship_order_tiktok/' + reference,
      type: 'POST',
      cache: false,
      success: function(rs) {
        load_out();

        if (isJson(rs)) {
          let ds = JSON.parse(rs);

          if (ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          } else {
            beep();
            showError(ds.message);
          }
        } else {
          beep();
          showError(rs);
        }
      },
      error: function(rs) {
        beep();
        showError(rs);
      }
    })
  }


  function shipOrderShopee(reference) {
    load_in();

    $.ajax({
      url: HOME + 'ship_order_shopee/' + reference,
      type: 'POST',
      cache: false,
      success: function(rs) {
        load_out();

        if (isJson(rs)) {
          let ds = JSON.parse(rs);

          if (ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          } else {
            beep();
            showError(ds.message);
          }
        } else {
          beep();
          showError(rs);
        }
      },
      error: function(rs) {
        beep();
        showError(rs);
      }
    })
  }


  function shipOrderLazada(reference) {
    load_in();

    $.ajax({
      url: HOME + 'ship_order_lazada/' + reference,
      type: 'POST',
      cache: false,
      success: function(rs) {
        load_out();

        if (isJson(rs)) {
          let ds = JSON.parse(rs);

          if (ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          } else {
            beep();
            showError(ds.message);
          }
        } else {
          beep();
          showError(rs);
        }
      },
      error: function(rs) {
        beep();
        showError(rs);
      }
    })
  }


  function shipOrderPorlor(code) {
    load_in();

    $.ajax({
      url: HOME + 'ship_order_porlor/' + code,
      type: 'POST',
      cache: false,
      success: function(rs) {
        load_out();

        if (rs.trim() === 'success') {
          target = HOME + 'print_porlor_label/' + code;
          window.open(target, "_blank");
        } else {
          beep();
          showError(rs);
        }
      },
      error: function(rs) {
        beep();
        showError(rs);
      }
    })
  }


  function shipOrderSPX(code) {
    load_in();

    $.ajax({
      url: HOME + 'ship_order_spx/' + code,
      type: 'POST',
      cache: false,
      success: function(rs) {
        load_out();

        if (isJson(rs)) {
          let ds = JSON.parse(rs);

          if (ds.status == 'success') {
            window.open(ds.data.awb_link, "_blank");
          } else {
            beep();
            showError(ds.message);
          }
        } else {
          beep();
          showError(rs);
        }
      },
      error: function(rs) {
        beep();
        showError(rs);
      }
    })
  }
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_process.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_video_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>