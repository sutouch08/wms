function unsave()
{
	var code = $('#return_code').val();
	swal({
		title:'คุณแน่ใจ ?',
		text:'โปรดทราบ คุณต้องลบเอกสารใน SAP ด้วย ต้องการดำเนินการต่อหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'ดำเนินการต่อ',
		confirmButtonColor:'#DD6B55',
		cancelButtonText:'ยกเลิก',
		closeOnConfirm:false
	}, function(){
		$.ajax({
			url:HOME + 'unsave/'+code,
			type:'POST',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					swal({
						title:'Success',
						text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						goEdit(code);
					}, 1500);
				}else{
					swal({
						title:'Error',
						text:rs,
						type:'error'
					});
				}
			}
		})
	});
}


function getEdit(){
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



function update(){
  var code = $('#check_code').val();
  var remark   = $('#remark').val().trim();

  load_in();

  $.ajax({
    url: HOME + 'update_header/'+code,
    type:'POST',
    cache:'false',
    data:{
      'remark' : remark
    },
    success:function(rs) {
      load_out();
      var rs = rs.trim();

      if(rs == 'success'){
        swal({
          title: 'Updated',
          type:'success',
          timer: 1000
        });

				setTimeout(() => {
					window.location.reload();
				}, 1200);
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
		error:function(rs) {
			load_out();
			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
  });
}



$('#date_add').datepicker({
	dateFormat:'dd-mm-yy'
});



$("#customer_code").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function () {
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if (arr.length == 2) {
			var code = arr[0];
			var name = arr[1];
			$("#customer_code").val(code);
			$("#customer_name").val(name);
			zoneInit(code, true);
		}
		else {
			$(this).val('');
			$("#customer_name").val('');
			zoneInit('', true);
		}
	}
});



function zoneInit(customer_code, edit) {
	if (edit) {
		$('#zone_code').val('');
		$('#zone_name').val('');
	}

	$('#zone_code').autocomplete({
		source: BASE_URL + 'auto_complete/get_consign_zone/' + customer_code,
		autoFocus: true,
		close: function () {
			var rs = $.trim($(this).val());
			var arr = rs.split(' | ');
			if (arr.length == 2) {
				var code = arr[0];
				var name = arr[1];
				$('#zone_code').val(code);
				$('#zone_name').val(name);
			} else {
				$('#zone_code').val('');
				$('#zone_name').val('');
			}
		}
	})
}


function add() {
	$('.e').clearError();

	let h = {
		'date_add' : $('#date_add').val(),
		'customer_code' : $('#customer_code').val().trim(),
		'customer_name' : $('#customer_name').val().trim(),
		'zone_code' : $('#zone_code').val().trim(),
		'zone_name' : $('#zone_name').val().trim(),
		'is_wms' : $('#is_wms').val(),
		'remark' : $('#remark').val().trim()
	};


	if(! isDate(h.date_add)){
		$('#date_add').hasError();
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(h.customer_code.length == 0 || h.customer_name.length == 0) {
		$('#customer_code').hasError();
		swal("กรุณาระบุลูกค้า");
		return false;
	}

	if(h.is_wms == "") {
		$('#is_wms').hasError();
		swal("กรุณาเลือกช่องทาง");
		return false;
	}

	if(h.zone_code.length == 0 || h.zone_name.length == 0) {
		$('#zone_code').hasError();
		swal("โซนไม่ถูกต้อง");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					if(ds.ex == 0) {
						swal({
							title:'ข้อผิดพลาด',
							text:ds.message,
							type:'warning'
						}, function() {
							viewDetail(ds.code);
						});
					}
					else {
						if(h.is_wms == '0') {
							goEdit(ds.code);
						}
						else {
							viewDetail(ds.code);
						}
					}
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error',
						html:true
					});
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

		},
		error:function(rs) {
			load_out();
			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
	})
}




function recalTotal(){
	var totalQty = 0;
	$('.qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty),0);
		totalQty += qty;
	});

	$('#totalQty').text(addCommas(totalQty));
}


$(document).ready(function(){
	let customer_code = $('#customer_code').val();
	zoneInit(customer_code, false);
})


function sendToWms() {
	var code = $('#check_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_wms/'+code,
		type:'POST',
		cache:false,
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'succcess',
					timer:1000
				});
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
		error:function(xhr, status, error) {
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
