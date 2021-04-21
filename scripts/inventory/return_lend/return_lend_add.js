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


function getValidate(){
	var isManual = $('#manualCode').length;
	if(isManual === 1){
		var prefix = $('#prefix').val();
	  var runNo = parseInt($('#runNo').val());
	}

	var code = $('#code').val();

	if(code.length == 0){
	   save();
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
	    addOrder();
		}

	}else{
	  swal('เลขที่เอกสารไม่ถูกต้อง');
	  return false;
	}
}


function addOrder(){
	var code = $('#code').val();
	$.ajax({
		url: HOME + 'is_exists/'+code,
		type:'GET',
		cache:false,
		success:function(rs){
			if(rs == 'not_exists'){
				save();
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


function save()
{
	var count = 0;
	var error = 0;
	let zone = $('#zone_code').val();
	let zoneName = $('#zone').val();
	let empName = $('#empName').val();
	let empID = $('#empID').val();
	let code = $('#lendCode').val();
	let date = $('#dateAdd').val();

	if(!isDate(date)){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(zone.length == 0 || zoneName.length == 0){
		swal("กรุณาระบุโซนรับเข้า");
		return false;
	}

	if(empName.length == 0 || empID == ''){
		swal("กรุณาระบุผู้ยืม");
		return false;
	}

	if(code.length == 0){
		swal("กรุณาระบุใบยืมสินค้า");
		return false;
	}


	$('.qty').each(function(){
		let arr = $(this).attr('id').split('qty_');
		let itemCode = arr[1];
		let limit = parseDefault(parseInt($('#backlogs_'+itemCode).val()), 0);
		let qty = parseDefault(parseInt($(this).val()), 0);

		if(qty > 0){
			count++;
			$(this).removeClass('has-error');
		}

		if(qty < 0 || qty > limit){
			error++;
			$(this).addClass('has-error');
		}

	});

	if(error > 0){
		swal("จำนวนที่คืนต้องไม่มากกว่ายอดค้างรับ และ ต้องไม่น้อยกว่า 0");
		return false;
	}

	if(count == 0){
		swal("ต้องคืนอย่างน้อย 1 ตัว");
		return false
	}

	$('#addForm').submit();
}





function doExport(){
	var code = $('#return_code').val();
	$.get(HOME + '/do_export/'+code, function(rs){
		if(rs === 'success'){
			swal({
				title:'Success',
				text:'ส่งข้อมูลไป SAP สำเร็จ',
				type:'success',
				timer:1000
			});
			setTimeout(function(){
				viewDetail(code);
			}, 1500);
		}
	});
}




$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});


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
	}
});




$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$(this).val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$(this).val('');
			$('#zone_code').val('');
		}
	}
})



function recalTotal(){
	var totalQty = 0;
	$('.qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty),0);
		totalQty += qty;
	});

	$('#totalQty').text(addCommas(totalQty));
}


function sendToWms() {
	var code = $('#return_code').val();

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
