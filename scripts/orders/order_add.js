$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#qt_no').autocomplete({
	source:BASE_URL + 'auto_complete/get_active_quotation',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length === 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
})


function get_quotation() {
	var qt_no = $('#qt_no').val();
	var code = $('#order_code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "การทั้งเก่าหมดจะถูกลบและโหลดใหม่  ยืนยันการดึงรายการหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			load_in();
			$.ajax({
				url: BASE_URL + 'orders/orders/load_quotation',
				type:"GET",
				cache:"false",
				data:{
					'order_code' : code,
					'qt_no' : qt_no
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title:'Success',
							text:'ดึงรายการใหม่เรียบร้อยแล้ว',
							type:'success',
							timer:1000
						});

						window.location.reload();

					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});

}


function validOrder() {
  clearErrorByClass('e');

  let auz = $('#auz').val();
  let error = 0;

  if(auz == 0) {
    let order_code = $('#order_code').val();
    let h = {
      'code' : order_code,
      'rows' : []
    };

    load_in();

    $('.line-qty').each(function() {
      let item_code = $(this).data('code');
      let qty = $(this).val();
      let id = $(this).data('id');
      let is_count = $(this).data('count');

      if(is_count == 1) {
        h.rows.push({
          'product_code' : item_code,
          'qty' : qty,
          'id' : id
        });
      }
    });

    if(h.rows.length > 0) {
      $.ajax({
        url:BASE_URL + 'orders/orders/check_available_stock',
        type:'POST',
        cache:false,
        data:{
          'data' : JSON.stringify(h)
        },
        success:function(rs) {
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status == 'success') {
              if(ds.data.length) {
                ds.data.forEach(function(el) {
                  if(el.status != 'OK') {
                    $('#qty_'+el.id).hasError();
                    error++;

                    if(el.status == 'failed') {
                      $('#qty_'+el.id).attr('title', 'Available : '+el.available);
                    }
                    else if(el.status == 'inactive') {
                      $('#qty_'+el.id).attr('title', 'Inactive');
                    }
                    else if(el.status == 'invalid item') {
                      $('#qty_'+el.status).attr('title', 'Invalid item');
                    }
                  }
                });
              }

              if(error == 0) {
                //saveOrder();
              }
            }
            else {
              showError(ds.message);
            }
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          load_out();
          showError(rs);
        }
      })
    }
  }
  else {
    saveOrder();
  }
}

//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder() {
  let order_code = $('#order_code').val();
	let id_sender = $('#id_sender').val();
	let tracking = $('#tracking').val();
  let payment_role = $('#payment option:selected').data('role');
  let cod_amount = parseDefault(parseFloat($('#cod-amount').val()), 0);

  if(payment_role == '4' && cod_amount <= 0) {
    swal({
      title:"กรุณาระบุยอด COD Amount",
      text:"การชำระเงินแบบ เก็บเงินปลายทางจำเป็นต้องระบุยอดเก็บเงิน",
      type:"warning"
    }, function() {
      setTimeout(() => {
        $('#cod-amount').focus().select();
      }, 200)
    });

    return false;
  }

  load_in();

	$.ajax({
		url: BASE_URL + 'orders/orders/save/'+ order_code,
		type:"POST",
    cache:false,
		data:{
			'id_sender' : id_sender,
			'tracking' : tracking,
      'cod_amount' : cod_amount
		},
		success:function(rs){
      load_out();

			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });
				setTimeout(function(){ editOrder(order_code) }, 1200);
			}
      else {
				swal("Error ! ", rs , "error");
			}
		},
    error:function(xhr) {
      swal({
        title:'Error',
        text:xhr.responseText,
        type:'error',
        html:true
      })
    }
	});
}


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
			$('#customer_code').val(code);
			$("#customer").val(name);
		}else{
			$("#customerCode").val('');
			$('#customer_code').val('');
			$(this).val('');
		}
	}
});


var customer;
var channels;
var payment;
var date;


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');

  customer = $("#customerCode").val();
	channels = $("#channels").val();
	payment  = $("#payment").val();
	date = $("#date").val();
}


function updateRemark() {
  let order_code = $('#order_code').val();
  let remark = $('#remark').val().trim();

  $.ajax({
    url:BASE_URL + 'orders/orders/update_remark',
    type:'POST',
    cache:false,
    data:{
      'code' : order_code,
      'remark' : remark
    },
    success:function(rs) {
      if(rs.trim() != 'success') {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
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

					updateDetailTable();
				}
        else {
					swal("Error", rs, "error");
				}
			}
		});
	}
}


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


function addFreeItemToOrder(){
	var orderCode = $('#order_code').val();
	var qty = parseDefault(parseInt($('#input-qty').val()), 0);
	var limit = parseDefault(parseInt($('#stock-qty').val()), 0);
	var itemCode = $('#item-code').val();
  var data = [{'code':itemCode, 'qty' : qty}];

	if(qty > 0 && qty <= limit) {
		load_in();
		$.ajax({
			url:BASE_URL + 'orders/orders/add_free_detail/'+orderCode,
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

      recalTotal();
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
					if( rs == 'success' ) {
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}
          else{
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
	autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
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


function validUpdate(){
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $('#customer').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
  var recal = 0;


	//---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( customer_code.length == 0 || customer_name == "" ){
		swal("ชื่อลูกค้าไม่ถูกต้อง");
		return false;
	}

  if(channels_code == ""){
    swal('กรุณาเลือกช่องทางขาย');
    return false;
  }


  if(payment_code == ""){
    swal('กรุณาเลือกช่องทางการชำระเงิน');
    return false;
  }

	//--- ตรวจสอบความเปลี่ยนแปลงที่สำคัญ
	if( (date_add != date) || ( customer_code != customer ) || ( channels_code != channels ) || ( payment_code != payment ) )
  {
		recal = 1; //--- ระบุว่าต้องคำนวณส่วนลดใหม่
	}

  updateOrder(recal);
}


function updateOrder(recal){
	var order_code = $("#order_code").val();
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $("#customer").val();
  var customer_ref = $('#customer_ref').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
	var reference = $('#reference').val();
  var warehouse_code = $('#warehouse').val();
	var transformed = $('#transformed').val();
	var remark = $("#remark").val();

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/orders/update_order',
		type:"POST",
		cache:"false",
		data:{
      "order_code" : order_code,
  		"date_add"	: date_add,
  		"customer_code" : customer_code,
      "customer_ref" : customer_ref,
  		"channels_code" : channels_code,
  		"payment_code" : payment_code,
  		"reference" : reference,
      "warehouse_code" : warehouse_code,
  		"remark" : remark,
			"transformed" : transformed,
      "recal" : recal
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Done !',
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
          type: 'error',
          html:true
        });
			}
		}
	});
}


function recalDiscount(){
	updateOrder(1);
}


// JavaScript Document
function changeState(){
  var order_code = $("#order_code").val();
  var state = $("#stateList").val();
  var is_wms = $('#is_wms').val();
  var trackingNo = $('#trackingNo').val();
  var tracking = $('#tracking').val();
  var id_address = $('#address_id').val();
  var id_sender = $('#id_sender').val();
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
            title:"Error!",
            text:rs,
            type:'error',
            html:true
          }, function() {
            window.location.reload();
          });
        }
      },
      error:function(rs) {
        load_out();
        swal({
          title:'Error!',
          text:rs.responseText,
          type:'error',
          html:true
        }, function() {
          window.location.reload();
        });
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


function add() {
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $("#customer").val();
  var customer_ref = $('#customer_ref').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
	var reference = $('#reference').val();
  var warehouse_code = $('#warehouse').val();
	var transformed = $('#transformed').val();
  var is_pre_order = $('#is_pre_order').val();
	var remark = $("#remark").val();

  //---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( customer_code.length == 0 || customer_name == "" ){
		swal("ชื่อลูกค้าไม่ถูกต้อง");
		return false;
	}

  if(channels_code == ""){
    swal('กรุณาเลือกช่องทางขาย');
    return false;
  }


  if(payment_code == ""){
    swal('กรุณาเลือกช่องทางการชำระเงิน');
    return false;
  }

  if(warehouse_code == "") {
    swal('กรุณาระบุคลังสินค้า');
    return false;
  }

  let data = {
    "date_add"	: date_add,
    "customer_code" : customer_code,
    "customer_ref" : customer_ref,
    "channels_code" : channels_code,
    "payment_code" : payment_code,
    "reference" : reference,
    "warehouse_code" : warehouse_code,
    "remark" : remark,
    "transformed" : transformed,
    "is_pre_order" : is_pre_order
  };

  load_in();

	$.ajax({
		url:BASE_URL + 'orders/orders/add',
		type:"POST",
		cache:false,
		data:{
  		"data" : JSON.stringify(data)
    },
		success: function(rs){
			load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          window.location.href = BASE_URL + 'orders/orders/edit_order/'+ ds.code;
        }
        else {
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
        });
      }
		}
	});
}


function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $.trim($('#code').val());

  if(code.length == 0){
    $('#btn-submit').click();
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
            $('#btn-submit').click();
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


function submitCod() {
  let code = $('#order_code').val();
  let amount = parseDefault(parseFloat($('#cod-amount').val()), 0.00);

  $.ajax({
    url:BASE_URL + 'orders/orders/update_cod_amount',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'amount' : amount
    },
    success:function(rs) {
      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }
      else {
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    }
  })
}


//---- for update price on noncount item
function updateItemPrice(id) {
  let code = $('#order_code').val();
  let price = parseDefault(parseFloat($('#price_'+id).val()), 0.00);
  let currentPrice = parseDefault(parseFloat($('#price_'+id).data('price')), 0);

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/orders/update_item_price',
    type:'POST',
    cache:false,
    data:{
      'order_code' : code,
      'id' : id,
      'price' : price
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        //--- update current
        $('#price_'+id).data('price', price);
        $('#btn-change-state').addClass('hide');
        $('#btn-save-order').removeClass('hide');
      }
      else {
        showError(rs);
        //--- roll back data
        $('#price-'+id).val(currentPrice);
        price = currentPrice;
        let qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
        let disc = $('#disc_'+id).val();
        let discAmount = parseDiscountAmount(disc, price);
        let lineTotal = (price * qty) - (discAmount * qty);
        $('#line_total_'+id).val(addCommas(lineTotal.toFixed(2)));

        recalTotal();
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  })
}


function updateItem(id) {
  let code = $('#order_code').val();
	let qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
	let price = parseDefault(parseFloat($('#price_'+id).val()), 0.00);
	let disc = $('#disc_'+id).val();
  let currentQty = parseDefault(parseFloat($('#qty_'+id).data('qty')), 0);
	let currentPrice = parseDefault(parseFloat($('#price_'+id).data('price')), 0);
	let currentDisc = $('#disc_'+id).data('disc');

	disc = disc == '' ? 0 : disc;
	currentDisc = currentDisc == '' ? 0 : currentDisc;

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/orders/update_item',
    type:'POST',
    cache:false,
    data:{
      'order_code' : code,
      'id' : id,
      'price' : price,
      'qty' : qty
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          //--- update current
          $('#price_'+id).data('price', price);
          $('#disc_'+id).data('disc', ds.discLabel);
          $('#disc_'+id).val(ds.discLabel);
          $('#disc_label_'+id).text(ds.discLabel);

          recalItem(id);
          $('#btn-change-state').addClass('hide');
          $('#btn-save-order').removeClass('hide');
        }
        else {
          showError(ds.message);

          //--- roll back data
          $('#price-'+id).val(currentPrice);
          $('#disc_'+id).val(currentDisc);
          $('#qty_'+id).val(currentQty);
          recalItem(id);
        }

      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      load_out();
      showError(rs);
    }
  })
}


function recalItem(id, updatePrice) {
	let price = parseDefault(parseFloat($('#price_'+id).val()), 0);
  let isCount = $('#price_'+id).data('count');

  if(price < 0) {
    price = price * (-1);
    $('#price-'+id).val(price.toFixed(2));
  }

	let qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
	let disc = $('#disc_'+id).val();
	let discAmount = parseDiscountAmount(disc, price);
	let lineTotal = (price * qty) - (discAmount * qty);
	$('#line_total_'+id).val(addCommas(lineTotal.toFixed(2)));
  console.log(disc);
	recalTotal();

  if(isCount == 0 && updatePrice == 'Y') {
    updateItemPrice(id);
  }
}


function recalTotal() {
	var total_order = 0;
	var totalAfDisc = 0;
	var total_qty = 0;
	var total_disc = 0;

	var net_amount = 0;

	$('.line-total').each(function() {
		let id = $(this).data('id');
		let price = parseDefault(parseFloat($('#price_'+id).val()), 0);
		let qty = parseDefault(parseFloat($('#qty_'+id).val()), 0);
		let amount = parseDefault(parseFloat(removeCommas($('#line_total_'+id).val())), 0);
		let order_amount = qty * price;
		let disc_amount = order_amount - amount;

		total_order += order_amount;
		total_qty += qty;
		total_disc += disc_amount;

	});

	net_amount = total_order - total_disc;

	$('#total-qty').val(addCommas(total_qty.toFixed(2)));
	$('#total-order').val(addCommas(total_order.toFixed(2)));
	$('#total-disc').val(addCommas(total_disc.toFixed(2)));
	$('#net-amount').val(addCommas(net_amount.toFixed(2)));
}


function toggleCate() {
  if($('#cate-widget').hasClass('collapsed')) {
    $('#cate-widget').removeClass('collapsed');
  }
  else {
    $('#cate-widget').addClass('collapsed');
  }
}

function getPreorderItem() {
  load_in();

  $.ajax({
    url:BASE_URL + 'orders/pre_order_policy/get_active_items',
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();
      if( isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#preOrderTemplate').html();
        let output = $('#preOrderTable');

        render(source, ds, output);

        $('#preOrderModal').modal('show');
      }
      else {
        swal({
          title:'Not found',
          text:'ไม่พบรายการสินค้าที่เปิด Pre Order',
          type:'info'
        });
      }
    }
  })
}


function addPreOrderItems() {
  let order_code = $('#order_code').val();
  let items = [];

  $('.pre-qty').each(function() {
    if($(this).val() != '') {
      let qty = parseDefault(parseFloat($(this).val()), 0);
      let code = $(this).data('pd');
      let id = $(this).data('id'); //pre_order_detail_id

      if(qty > 0) {
        items.push({"id" : id, "code" : code, "qty" : qty});
      }
    }
  });

  if(items.length > 0) {
    $('#preOrderModal').modal('hide');

    load_in();

    $.ajax({
      url:BASE_URL + 'orders/orders/add_pre_order_detail',
      type:'POST',
      cache:false,
      data:{
        'order_code' : order_code,
        'data' : JSON.stringify(items)
      },
      success:function(rs) {
        load_out();

        if(rs === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          })
        }
      }
    })
  }
}
