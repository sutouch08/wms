<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-8 col-xs-8 padding-5">
    <h3 class="title">ออเดอร์ รอส่ง <?php echo $count; ?> จากทั้งหมด <?php echo number($all); ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-4 col-xs-4 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-success top-btn" onclick="startExport()">
        <i class="fa fa-send"></i> Start Export
      </button>
    </p>
  </div>
</div>
<hr/>
<div class="row" id="result">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <table class="table table-striped border-1">
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
                <input type="hidden" class="order" data-id="<?php echo $rs->id; ?>" data-no="<?php echo $no; ?>" id="code-<?php echo $no; ?>"  value="<?php echo $rs->code; ?>" />
              </td>
              <td id="status-<?php echo $rs->id; ?>">รอดำเนินการ</td>
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

function startExport() {
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
        url:BASE_URL + 'auto/auto_delivery_order/send_to_sap/'+code,
        type:'POST',
        cache:false,
        success:function(rs){

          if(rs == 'success') {
            no++;
            $('#status-'+id).text('OK');
            if(no == max) {
              finished = true;
              load_out();
            }
            else {
              do_export(no);
            }
          }
          else {
            no++;
            $('#status-'+id).text('failed');
            $('#msg-'+id).text(rs);
            
            if(no == max) {
              finished = true;
              load_out();
            }
            else {
              do_export(no);
            }
          }
        }
      })
    }
  }
}
</script>
<?php $this->load->view('include/footer'); ?>
