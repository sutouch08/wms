// JavaScript Document
setInterval(function(){ getSearch(); }, 1000*60);


function confirmPayment(id)
{
	$("#confirmModal").modal('hide');
	load_in();
	$.ajax({
		url:BASE_URL + 'orders/order_payment/confirm_payment',
		type:"POST",
    cache:"false",
    data:{
      "id" : id
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title : 'เรียบร้อย',
          text: 'Paid',
          timer: 1000,
          type: 'success'
        });
				$("#row-"+id).remove();
			}else{
				swal("ข้อผิดพลาด", rs, "error");
			}
		}
	});
}



function unConfirmPayment(id)
{
	$("#confirmModal").modal('hide');
	load_in();
	$.ajax({
		url:BASE_URL + 'orders/order_payment/un_confirm_payment',
		type:"POST",
    cache:"false",
    data:{
      "id" : id
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title : 'เรียบร้อย',
          text: 'ยกเลิกการยืนยันเรียบร้อยแล้ว',
          timer: 1000,
          type: 'success'
        });
				$("#row-"+id).remove();
			}else{
				swal("ข้อผิดพลาด", rs, "error");
			}
		}
	});
}



function viewDetail(id)
{
	load_in();
	$.ajax({
		url:BASE_URL + 'orders/order_payment/get_payment_detail',
		type:"POST",
    cache:"false",
    data:{
      "id" : id
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล หรือ การชำระเงินถูกยืนยันไปแล้ว', 'error');
			}else{
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}




function removePayment(id, name)
{
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบการแจ้งชำระของ '+ name + ' หรือไม่?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: BASE_URL + 'orders/order_payment/remove_payment',
				type:"POST",
        cache:"false",
        data:{
          "id" : id
        },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title : "สำเร็จ",
              text: "ลบรายการเรียบร้อยแล้ว",
              timer: 1000,
              type: "success"
            });

						$("#row-"+id).remove();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ หรือ มีการยืนยันการชำระเงินแล้ว", "error");
					}
				}
			});
		});
}


$("#fromDate").datepicker({
	dateFormat:'dd-mm-yy',
	onClose: function(sd){
		$("#toDate").datepicker("option", "minDate", sd);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#fromDate").datepicker("option", "maxDate", sd);
	}
});


function exportFilter() {
	let code = $('#code').val();
	let customer = $('#customer').val();
	let user = $('#user').val();
	let channels = $('#channels').val();
	let account = $('#account').val();
	let from_date = $('#fromDate').val();
	let to_date = $('#toDate').val();
	let valid = $('#valid').val();
	let is_pre_order = $('#is_pre_order').val();

	if(valid != '0') {
		if( ! isDate(from_date) || ! isDate(to_date)) {
			swal({
				title:'กรุณาระบุวันที่',
				text:'เมื่อตัวกรองระบุสถานะ ยืนยันแล้ว หรือ ทั้งหมด จำเป็นต้องระบุวันที่',
				type:'warning'
			});

			return false;
		}
	}

	let token = generateUID();

	$('#ex-code').val(code);
	$('#ex-customer').val(customer);
	$('#ex-user').val(user);
	$('#ex-channels').val(channels);
	$('#ex-account').val(account);
	$('#ex-from_date').val(from_date);
	$('#ex-to_date').val(to_date);
	$('#ex-valid').val(valid);
	$('#ex-is_pre_order').val(is_pre_order);
	$('#token').val(token);


  get_download(token);
  $('#exportForm').submit();
}
