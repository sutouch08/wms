<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h4 class="title">รอดำเนินการ <?php echo $count; ?> ออเดอร์ จากทั้งหมด <?php echo number($all); ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-info top-btn" onclick="refresh()"><i class="fa fa-refresh"></i> Refresh</button>
    <?php if($this->pm->can_edit) : ?>
    <button type="button" class="btn btn-white btn-primary top-btn" onclick="getUploadFile()"><i class="fa fa-upload"></i> &nbsp; Import Order</button>
    <button type="button" class="btn btn-white btn-warning top-btn" onclick="clearAllData()"><i class="fa fa-times"></i> &nbsp; Clear All Data</button>
    <?php endif; ?>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-6-harf col-md-4 col-sm-3 hidden-xs">&nbsp; </div>
  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
    <div class="input-group">
      <span class="input-group-addon">จำนวน/รอบ</span>
      <input type="number" class="form-control input-sm text-center" id="order-limit" onchange="changeOrderLimit()" value="<?php echo $limit; ?>" />
    </div>
  </div>
  <div class="divider-hidden visible-xs"></div>

  <div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-9 padding-5">
    <div class="input-group">
      <span class="input-group-addon">สถานะ</span>
      <select class="form-control input-sm" id="state">
        <option value="">Select State</option>
        <option value="1">รอดำเนินการ</option>
        <option value="3">รอจัดสินค้า</option>
        <option value="7">รอเปิดบิล</option>
        <option value="9">ยกเลิก</option>
      </select>
    </div>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <button type="button" class="btn btn-xs btn-success btn-block" style="height:30px;" onclick="startExport()">GO</button>
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


<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:600px; max-width:95vw;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 <h4 class="modal-title">Import File</h4>
			</div>
			<div class="modal-body">
				<form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="input-group width-100">
              <input type="text" class="form-control" id="show-file-name" placeholder="กรุณาเลือกไฟล์ Excel" readonly />
              <span class="input-group-btn">
                <button type="button" class="btn btn-white btn-default"  onclick="getFile()">เลือกไฟล์</button>
              </span>
            </div>
					</div>
				</div>
				<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
				</form>
			 </div>
			<div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default btn-100" onclick="closeModal('upload-modal')">ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-primary btn-100" onclick="uploadfile()">นำเข้า</button>
			</div>
	 </div>
 </div>
</div>

<script>
var HOME = BASE_URL + 'auto/auto_change_state/';

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


function getUploadFile(){
  $('#upload-modal').modal('show');
}


function getFile(){
  $('#uploadFile').click();
}


$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		//readURL(this);
    $('#show-file-name').val(name);
	}
});


function uploadfile()	{
  $('#upload-modal').modal('hide');

  var file	= $("#uploadFile")[0].files[0];
  var fd = new FormData();
  fd.append('uploadFile', $('input[type=file]')[0].files[0]);
  if( file !== '')
  {
    load_in();

    $.ajax({
      url:BASE_URL + 'auto/auto_change_state/import_order',
      type:"POST",
      cache:false,
      data: fd,
      processData:false,
      contentType: false,
      success: function(rs) {
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'นำเข้าเรียบร้อยแล้ว',
            type:'success',
            html:true,
            timer:1000
          });

          setTimeout(() => {
            refresh();
          }, 1200);
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        showError(rs);
      }
    });
  }
}


function clearAllData() {
  swal({
    title:'Clear Data',
    text:'Clear All Data, Do you want to process this operation ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    load_in();

    $.ajax({
      url:HOME + 'clear_data',
      type:'GET',
      cache:false,
      success:function(rs) {
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'success',
            type:'success',
            timer:1000,
          });

          setTimeout(() => {
            refresh();
          }, 1200);
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        showError(rs);
      }
    })
  })
}


function changeOrderLimit() {
  let limit = parseDefault(parseInt($('#order-limit').val()), 0);

  if(limit <= 0) {
    swal("จำนวนต้องมากกว่า 0");
    return false;
  }

  $.ajax({
    url:HOME + 'change_order_limit',
    type:'POST',
    cache:false,
    data:{
      'limit' : limit
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        refresh();
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}

</script>


<?php $this->load->view('include/footer'); ?>
