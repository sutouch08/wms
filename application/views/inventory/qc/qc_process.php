<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-primary top-btn" onclick="goBack()"><i class="fa fa-cubes"></i> รอตรวจ</button>
    <button type="button" class="btn btn-white btn-info top-btn" onclick="viewProcess()"><i class="fa fa-cube"></i> กำลังตรวจ</button>
    <?php if($order->channels_code == '0009' && ! empty($order->reference)) : ?>
      <button type="button" class="btn btn-white btn-info top-btn" onclick="shipOrderTiktok('<?php echo $order->reference; ?>')"><i class="fa fa-print"></i> Print Label</button>
    <?php endif; ?>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="width-100 text-center" value="<?php echo $order->code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 text-center" value="<?php echo thai_date($order->date_add); ?>" disabled />
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5 hidden-sm">
    <label>อ้างอิง</label>
    <input type="text" class="width-100 text-center" value="<?php echo $order->reference; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="width-100 text-center" value="<?php echo $order->customer_code; ?>" disabled />
  </div>
  <div class="col-lg-5-harf col-md-4 col-sm-5-harf col-xs-6 padding-5">
    <label class="not-show">ลูกค้า</label>
    <input type="text" class="width-100" value="<?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>" disabled />
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
    <input type="text" class="width-100" value="<?php echo $order->channels_name; ?>" disabled />
  </div>

  <div class="col-lg-10 col-md-9-harf col-sm-9-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="width-100" value="<?php echo $order->remark; ?>" disabled />
  </div>
</div>

<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
<input type="hidden" id="id_box" value="" />
<hr />

<?php $this->load->view('inventory/qc/qc_box'); ?>
<?php $this->load->view('inventory/qc/qc_control'); ?>
<?php $this->load->view('inventory/qc/qc_incomplete_list'); ?>
<?php $this->load->view('inventory/qc/qc_complete_list'); ?>


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
if(!empty($barcode_list))
{
  foreach($barcode_list as $bc)
  {
    echo '<input type="hidden" id="bc-'.$bc->barcode.'" data-code="'.$bc->product_code.'" value="1" />';
  }
}
 ?>
 <script>
   function shipOrderTiktok(reference) {
     load_in();

     $.ajax({
       url:HOME + 'ship_order_tiktok/'+reference,
       type:'POST',
       cache:false,
       success:function(rs) {
         load_out();

         if(isJson(rs)) {
           let ds = JSON.parse(rs);

           if(ds.status === 'success') {
             window.open(ds.data.fileUrl, "_blank");
           }
           else {
             beep();
             showError(ds.message);
           }
         }
         else {
           beep();
           showError(rs);
         }
       },
       error:function(rs) {
         beep();
         showError(rs);
       }
     })
   }
 </script>

<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_process.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/qc/qc_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
