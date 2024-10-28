$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#due_date').datepicker({
  dateFormat:'dd-mm-yy'
});


//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
  if(validateTransformProducts()){
    load_in();

    $.ajax({
  		url: HOME + 'save/'+ order_code,
  		type:"POST",
      cache:false,
  		success:function(rs) {
        load_out();

  			var rs = $.trim(rs);
  			if( rs == 'success' ){
  				swal({
            title: 'Saved',
            type: 'success',
            timer: 1000
          });

  				setTimeout(function(){
            editOrder(order_code)
          }, 1200);

  			}
        else {
  				swal("Error ! ", rs , "error");
  			}
  		}
  	});
  }
  else {
		swal('warning !', 'กรุณากำหนดสินค้าแปรสภาพให้ครบถ้วน', 'warning');
	}

}


$("#customer-code").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
      $('#customer-code').val(code);
			$("#customer").val(name);
		}else{
      $('#customer-code').val('');
			$("#customerCode").val('');
			$('#customer').val('');
		}
	}
});



$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
      $('#customer-code').val(code);
			$("#customer").val(name);
		}else{
      $('#customer-code').val('');
			$("#customerCode").val('');
			$(this).val('');
		}
	}
});


$('#wq-ref').autocomplete({
  source: BASE_URL + 'auto_complete/get_transform_code/'+true,
  autoFocus: true
});


$('#customer').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customerCode').val('');
  }
});

$('#customerCode').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customer').val('');
  }
});


$("#zone").autocomplete({
	source: BASE_URL + 'auto_complete/get_transform_zone',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#zoneCode").val(code);
			$(this).val(name);
		}else{
			$("#zoneCode").val('');
			$(this).val('');
		}
	}
});

$('#zoneCode').autocomplete({
  source: BASE_URL + 'auto_complete/get_transform_zone',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$(this).val(code);
			$('#zone').val(name);
		}
    else {
			$("#zone").val('');
			$(this).val('');
		}
	}
})


$("#empName").autocomplete({
	source: BASE_URL + 'auto_complete/get_user',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#empName").val(name);
		}else{
			$("#empName").val('');
		}
	}
});



function add(){
  addOrder();
}


function addOrder() {
  let h = {
    'customer_code' : $('#customerCode').val(),
    'customer_name' : $('#customer').val(),
    'date_add' : $('#date').val(),
    'due_date' : $('#due_date').val(),
    'empName' :  $('#empName').val(),
    'role' : $('#role').val(),
    'zone_code' : $('#zoneCode').val(),
    'zone_name' : $('#zone').val(),
    'warehouse_code' : $('#warehouse').val(),
    'reference' : $('#wq-ref').val(),
    'remark' : $('#remark').val()
  }

  let reqRemark = $('#require_remark').val() == 1 ? true : false;

  if(h.customer_code.length == 0 || h.customer_name.length == 0){
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if(!isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    console.log('date error');
    return false;
  }

  if(h.empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_name.length == 0 )
  {
    swal('โซนแปรสภาพไม่ถูกต้อง');
    return false;
  }

  if(h.warehouse_code.length == 0){
    swal('กรุณาเลือกคลัง');
    return false;
  }

  if( ! isDate(h.due_date)) {
    swal('กรุณาระบุวันที่ต้องการสินค้า');
    return false;
  }

  if(reqRemark && h.remark.length < 10) {
    swal({
      title: 'Required',
      text: "กรุณาระบุหมายเหตุอย่างน้อย 10 ตัวอักษร",
      type:'warning'
    });

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
		url: HOME + 'get_detail_table/'+order_code,
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




function validUpdate() {
  updateOrder();
}





function updateOrder() {
  let h = {
    'code' : $('#order_code').val(),
    'customer_code' : $('#customerCode').val(),
    'customer_name' : $('#customer').val(),
    'date_add' : $('#date').val(),
    'due_date' : $('#due_date').val(),
    'empName' :  $('#empName').val(),
    'role' : $('#role').val(),
    'zone_code' : $('#zoneCode').val(),
    'zone_name' : $('#zone').val(),
    'warehouse_code' : $('#warehouse').val(),
    'reference' : $('#wq-ref').val(),
    'remark' : $('#remark').val()
  }

  let reqRemark = $('#require_remark').val() == 1 ? true : false;

  if(h.customer_code.length == 0 || h.customer_name.length == 0){
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if(!isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    console.log('date error');
    return false;
  }

  if(h.empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_name.length == 0 )
  {
    swal('โซนแปรสภาพไม่ถูกต้อง');
    return false;
  }

  if(h.warehouse_code.length == 0){
    swal('กรุณาเลือกคลัง');
    return false;
  }

  if( ! isDate(h.due_date)) {
    swal('กรุณาระบุวันที่ต้องการสินค้า');
    return false;
  }

  if(reqRemark && h.remark.length < 10) {
    swal({
      title: 'Required',
      text: "กรุณาระบุหมายเหตุอย่างน้อย 10 ตัวอักษร",
      type:'warning'
    });

    return false;
  }

	load_in();

	$.ajax({
		url:HOME + 'update_order',
		type:"POST",
		cache:"false",
		data:{
      "data" : JSON.stringify(h)
    },
		success: function(rs){
			load_out();

			if( rs == 'success' ){
				swal({
          title: 'Success !',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);

			}else{
				swal({
          title: "Error!",
          text: rs,
          type: 'error'
        });
			}
		}
	});
}


function changeState(){
  var order_code = $("#order_code").val();
  var state = $("#stateList").val();
  var id_address = $('#address_id').val();
  var id_sender = $('#id_sender').val();
  var trackingNo = $('#trackingNo').val();
  var tracking = $('#tracking').val();
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

  if(state == 9 && (reason_id == '' || cancle_reason.length < 10)) {
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
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          }, function() {
            window.location.reload();
          })
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



function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();
  if(code.length == 0){
    addOrder();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: BASE_URL + 'orders/orders/is_exists_order/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            addOrder();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}
