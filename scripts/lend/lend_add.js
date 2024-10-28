$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();

  load_in();

	$.ajax({
		url: HOME + 'save/'+ order_code,
		type:"POST",
    cache:false,
		success:function(rs){
			load_out();

			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          editOrder(order_code)
        }, 1200);

			}else{
				swal("Error ! ", rs , "error");
			}
		}
	});
}




$("#empName").autocomplete({
	source: BASE_URL + 'auto_complete/get_employee',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var empName = arr[0];
			var empID = arr[1];
			$("#empName").val(empName);
			$("#empID").val(empID);
		}else{
			$("#empID").val('');
			$(this).val('');
		}
    zoneInit();
	}
});





function zoneInit(){
  let empID = $('#empID').val();
  if(empID == ""){
    $('#zone_code').val('');
    $('#zone').val('');
  }

  $('#zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_lend_zone/'+empID,
    autoFocus: true,
    close:function(){
      var rs = $.trim($(this).val());
      var arr = rs.split(' | ');
      if(arr.length == 2)
      {
        var code = arr[0];
        var name = arr[1];
        $('#zone_code').val(code);
        $('#zone').val(name);
      }else{
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  });
}


$(document).ready(function() {
  zoneInit();
});


function add(){
  addOrder();
}


function addOrder() {
  let h = {
    'date_add' : $('#date').val(),
    'user_ref' : $('#user_ref').val(),
    'empID' : $('#empID').val(),
    'empName' :  $('#empName').val(),
    'role' : $('#role').val(),
    'zone_code' : $('#zone_code').val(),
    'zone_name' : $('#zone').val(),
    'warehouse_code' : $('#warehouse').val(),
    'remark' : $('#remark').val()
  }

  if(!isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }


  if(h.empID == "" || h.empName.length == 0){
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_name.length == 0)
  {
    swal("โซนไม่ถูกต้อง");
    return false;
  }


  if(h.empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }


  if(h.warehouse == ""){
    swal('กรุณาระบุคลัง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          window.location.href = HOME + 'edit_detail/'+ds.code;
        }
        else
        {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          })
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    },
    error:function(xhr) {
      load_out();

      swal({
        title:'Error!',
        text:xhr.responseText,
        type:'error',
        html:true
      })
    }
  })
}

var customer;
var channels;
var payment;
var date;

function getEdit(){
  let approved = $('#is_approved').val();
  if(approved == 1){
    $('#remark').removeAttr('disabled');
  } else {
    $('.edit').removeAttr('disabled');
  }

  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
  customer = $("#customerCode").val();
	date = $("#date").val();
}


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addToOrder(){
  var order_code = $('#order_code').val();
	//var count = countInput();
  var data = [];
  $(".order-grid").each(function(index, element){
    if($(this).val() != ''){
      var code = $(this).attr('id');
      var arr = code.split('qty_');
      data.push({'code' : arr[1], 'qty' : $(this).val()});
    }
  });

	if(data.length > 0 ){
		$("#orderGrid").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/add_detail/'+order_code,
			type:"POST",
      cache:"false",
      data: {
        'data' : data
      },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
            title: 'success',
            type: 'success',
            timer: 1000
          });
					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail
				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}




// JavaScript Document
function updateDetailTable(){
	var order_code = $("#order_code").val();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_detail_table/'+order_code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source = $("#detail-table-template").html();
				var data = $.parseJSON(rs);
				var output = $("#detail-table");
				render(source, data, output);
			}
			else
			{
				var source = $("#nodata-template").html();
				var data = [];
				var output = $("#detail-table");
				render(source, data, output);
			}
		}
	});
}



function removeDetail(id, name){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + name + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: BASE_URL + 'orders/orders/remove_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}




$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}

});


$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	minLength: 4,
	autoFocus:true
});

$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				getItemGrid();
			}, 200);
		}
	}
});


$('#input-qty').keyup(function(e){
	if(e.keyCode == 13){
		addItemToOrder();
	}
});


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addItemToOrder(){
	var orderCode = $('#order_code').val();
	var qty = parseDefault(parseInt($('#input-qty').val()), 0);
	var limit = parseDefault(parseInt($('#stock-qty').val()), 0);
	var itemCode = $('#item-code').val();
  var data = [{'code':itemCode, 'qty' : qty}];

	if(qty > 0 && qty <= limit){
		load_in();
		$.ajax({
			url:BASE_URL + 'orders/orders/add_detail/'+orderCode,
			type:"POST",
			cache:"false",
			data:{
				'data' : data
			},
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title: 'success',
						type: 'success',
						timer: 1000
					});

					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail

					setTimeout(function(){
						$('#item-code').val('');
						$('#stock-qty').val('');
						$('#input-qty').val('');
						$('#item-code').focus();
					},1200);


				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}


//--- ตรวจสอบจำนวนที่คีย์สั่งใน order grid
function countInput(){
	var qty = 0;
	$(".order-grid").each(function(index, element) {
        if( $(this).val() != '' ){
			qty++;
		}
    });
	return qty;
}



function updateOrder() {
  let h = {
    'code' : $('#order_code').val(),
    'date_add' : $('#date').val(),
    'user_ref' : $('#user_ref').val(),
    'empID' : $('#empID').val(),
    'empName' :  $('#empName').val(),
    'role' : $('#role').val(),
    'zone_code' : $('#zone_code').val(),
    'zone_name' : $('#zone').val(),
    'warehouse_code' : $('#warehouse').val(),
    'remark' : $('#remark').val()
  }

  if(!isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }


  if(h.empID == "" || h.empName.length == 0){
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_name.length == 0)
  {
    swal("โซนไม่ถูกต้อง");
    return false;
  }


  if(h.empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }


  if(h.warehouse == ""){
    swal('กรุณาระบุคลัง');
    return false;
  }

	load_in();

  $.ajax({
    url:HOME + 'update_order',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.reload();
        }, 1200);
      }
      else
      {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        });
      }
    },
    error:function(xhr) {
      load_out();

      swal({
        title:'Error!',
        text:xhr.responseText,
        type:'error',
        html:true
      })
    }
  });
}


function changeState(){
  var order_code = $("#order_code").val();
  var state = $("#stateList").val();
  var id_address = $('#address_id').val();
  var id_sender = $('#id_sender').val();
  let trackingNo = $('#trackingNo').val();
  let tracking = $('#tracking').val();
  var is_wms = $('#is_wms').val();
  var reason_id = $('#reason-id').val();
  var cancle_reason = $.trim($('#cancle-reason').val());
  let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

  if(is_wms) {
    if(state == 3 && id_address == "") {
      swal("กรุณาระบุที่อยู่จัดส่ง");
      return false;
    }

    if(state == 3 && id_sender == "") {
      swal("กรุณาระบุผู้จัดส่ง");
      return false;
    }

    if($('#sender option:selected').data('tracking') == 1) {
      if(trackingNo != tracking) {
        swal("กรุณากดบันทึก Tracking No");
        return false;
      }

      if(trackingNo.length === 0) {
        swal("กรุณาระบุ Tracking No");
        return false;
      }
    }
  }

  if(state == 9 && cancle_reason.length < 10) {
    showCancleModal();
    return false;
  }

  if( state != 0){
    load_in();
    $.ajax({
      url:BASE_URL + 'orders/orders/order_state_change',
      type:"POST",
      cache:"false",
      data:{
        "order_code" : order_code,
        "state" : state,
        "id_address" : id_address,
        "id_sender" : id_sender,
        "tracking" : tracking,
        "reason_id" : reason_id,
        "cancle_reason" : cancle_reason,
        "force_cancel" : force_cancel
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title:'success',
            text:'status updated',
            type:'success',
            timer: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500);

        }
        else {
          swal({
            title:"Error !",
            text:rs,
            type:"error",
            html:true
          }, function() {
            window.location.reload();
          });
        }
      },
      error:function(xhr, status, error) {
        load_out();
        swal({
          title:'Error!',
          text:xhr.responseText,
          type:'error',
          html:true
        }, function() {
          window.location.reload();
        })
      }
    });
  }
}


function doCancle() {
  let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return changeState();
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


function showReason() {
	$('#cancle-reason-modal').modal('show');
}


function setNotExpire(option){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/set_never_expire',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code,
      'option' : option
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

function unExpired(){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_expired',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}
