<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
    <h3 class="title">ออเดอร์ รอย้อนสถานะ <?php echo $count; ?> จากทั้งหมด <?php echo number($all); ?></h3>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-9 col-md-7 col-sm-7 hidden-xs">&nbsp; </div>
  <div class="col-lg-2 col-md-3-harf col-sm-3-harf col-xs-9 padding-5">
    <div class="input-group">
      <span class="input-group-addon">สถานะ</span>
      <select class="form-control" id="state">
        <option value="">Select State</option>
        <option value="1">รอดำเนินการ</option>
        <option value="3">รอจัดสินค้า</option>
        <option value="7">รอเปิดบิล</option>
        <option value="9">ยกเลิก</option>
      </select>
    </div>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <button type="button" class="btn btn-sm btn-success btn-block" onclick="startExport()">Start</button>
  </div>
</div>
<hr/>

<?php

  $stateName = array(
    '1' => 'รอดำเนินการ',
    '2' => 'รอชำระเงิน',
    '3' => 'รอจัดสินค้า',
    '4' => 'กำลังจัด',
    '5' => 'รอตรวจ',
    '6' => 'กำลังตรวจ',
    '7' => 'รอเปิดบิล',
    '8' => 'เปิดบิลแล้ว',
    '9' => 'ยกเลิก'
  );

 ?>
<div class="row" id="result">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:500px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-150">Order</th>
          <th class="fix-width-100">Status</th>
          <th class="min-width-100">message</th>
        </tr>
      </thead>
      <tbody>
        <?php if( ! empty($data)) : ?>
          <?php $no = 1; ?>
          <?php foreach($data as $rs) : ?>
            <tr>
              <td class="text-center"><?php echo $no; ?></td>
              <td>
                <?php echo $rs->code; ?>
                <input type="hidden" class="order" data-id="<?php echo $rs->id; ?>" data-no="<?php echo $no; ?>" id="code-<?php echo $rs->id; ?>"  value="<?php echo $rs->code; ?>" />
              </td>
              <td id="status-<?php echo $rs->id; ?>"><?php echo empty($stateName[$rs->state]) ? "Unknow" : $stateName[$rs->state]; ?></td>
              <td id="msg-<?php echo $rs->id; ?>"></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="4" class="text-center">---- No Order ----</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <input type="hidden" id="count" value="<?php echo $count; ?>" />
  </div>
</div>

<script>

var finished = false;
var max = 0;
var orders = [];
var state = 7;

function startExport() {
  let stateSelected = $('#state').val();

  if(stateSelected == "") {
    swal("กรุณาเลือกสถานะ");
    return false;
  }
  else {
    state = stateSelected;
  }

  load_in();

  max = parseDefault(parseInt($('#count').val()), 0);

  $('.order').each(function() {
    let code = $(this).val();
    let id = $(this).data('id');
    orders.push({'code' : code, 'id' : id});
  });

  if(orders.length > 0 && max > 0) {
    do_export(0);
  }

}


function do_export(no){
  let order = orders[no];
  let code = order.code;
  let id = order.id;

  if(finished == false) {
    if(code != null && code != "" && code != undefined) {
      $.ajax({
        url:BASE_URL + 'orders/orders/order_state_change',
        type:'POST',
        cache:false,
        data:{
          'order_code' : code,
          'state' : state
        },
        success:function(rs){

          if(rs == 'success') {
            $('#status-'+id).text('OK');
            no++;
            if(no == max) {
              update_status(code, 1, rs);
              finished = true;
              load_out();
            }
            else {
              update_status(code, 1, rs);

              do_export(no);
            }
          }
          else {
            $('#status-'+id).text('failed');
            $('#msg-'+id).text(rs);
            no++;
            if(no == max) {
              update_status(code, 3, rs);
              finished = true;
              load_out();
            }
            else {
              update_status(code, 3, rs);
              do_export(no);
            }
          }
        }
      })
    }
  }
}

function update_status(code, status, message) {
  $.ajax({
    url:BASE_URL + 'auto/auto_change_state/update_status',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'status' : status,
      'message' : message
    },
    success:function(rs) {
      console.log(rs);
    }
  })
}
</script>


<?php $this->load->view('include/footer'); ?>
