function toggleCheckAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} else {
		$('.chk').prop("checked", false);
	}
}


function deleteChecked(){
	load_in();

	setTimeout(function(){
		$('.chk:checked').each(function(){
			var id = $(this).data('id');
			var no = $(this).val();
			removeRow(no, id);
		})

		reIndex();
		recalTotal();
		load_out();
	}, 500)

}



function unsave(){
	var code = $('#return_code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิกการบันทึก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function() {
			load_in();

			$.ajax({
				url:HOME + 'unsave/'+code,
				type:'POST',
				cache:false,
				success:function(rs) {
					load_out();
					if(rs === 'success') {
						setTimeout(function() {
							swal({
								title:'Success',
								text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
								type:'success',
								time:1000
							});

							setTimeout(function(){
								goEdit(code);
							}, 1500);
						}, 200);
					}
					else {
						setTimeout(function() {
							swal({
								title:'Error!',
								text:rs,
								type:'error'
							})
						}, 200);
					}
				}
			});
	});
}


function save()
{
	var error = 0;
	$('.input-price').each(function(){
		let price = parseFloat($(this).val());
		if(isNaN(price)){
			error++;
			swal('กรุณาใสราคาให้ครบถ้วน');
			$(this).addClass('has-error');
			return false;
		}else{
			$(this).removeClass('has-error');
		}
	});

	$('.input-qty').each(function(){
		let qty = parseFloat($(this).val());
		if(isNaN(qty) || qty == 0){
			error++;
			swal('กรุณาใส่จำนวนให้ครบถ้วน');
			$(this).addClass('has-error');
			return false;
		}
	});

	if(error == 0){
		$('#detailsForm').submit();
	}
}



function approve(){
	var code = $('#return_code').val();

	swal({
		title:'Approval',
		text:'ต้องการอนุมัติ '+code+' หรือไม่ ?',
		showCancelButton:true,
		confirmButtonColor:'#8bc34a',
		confirmButtonText:'อนุมัติ',
		cancelButtonText:'ยกเลิก',
		closeOnConfirm:true
	}, () => {
		load_in();

		$.ajax({
			url:HOME + 'approve/'+code,
			type:'GET',
			cache:false,
			success:function(rs) {
				load_out();

				if(rs === 'success') {
					setTimeout(() => {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}, 200);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:rs,
							type:'errr'
						}, () => {
							window.location.reload();
						});
					}, 200);
				}
			}
		});
	});
}



function unapprove() {
	var code = $('#return_code').val();
	swal({
		title:'Warning',
		text:'ต้องการยกเลิกการอนุมัติ '+code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#DD6B55',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, () => {
		load_in();

		$.ajax({
			url: HOME + 'unapprove/'+code,
			type:'GET',
			cache:false,
			success : function(rs) {
				load_out();
				if(rs === 'success') {
					setTimeout(() => {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}, 200);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error',
							text:rs,
							type:'error'
						}, () => {
							window.location.reload();
						});
					}, 200);
				}
			}
		});
	});
}



function doExport(){
	var code = $('#return_code').val();
	$.get(HOME + 'export_return/'+code, function(rs){
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
		}else{
			swal({
				title:'Error!',
				text:rs,
				type:'error'
			});
		}
	});
}


function sendToWms() {
	var code = $('#return_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_wms',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
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



function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
	var code = $('#return_code').val();
	var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var customer_code = $('#customer_code').val();
	var warehouse_code = $('#warehouse_code').val();
	var zone_code = $('#zone_code').val();
	var is_wms = $('#is_wms').val();
	var api = $('#api').val();
	var reqRemark = $('#required_remark').val();
  var remark = $.trim($('#remark').val());

	if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(warehouse_code.length == 0){
		swal('กรุณาระบุคลังสินค้า');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	if(reqRemark == 1 && remark.length < 10) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาใส่หมายเหตุ (ความยาวอย่างน้อย 10 ตัวอักษร)',
			type:'warning'
		});

		return false;
	}

  load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'return_code' : code,
			'date_add' : date_add,
			'invoice' : invoice,
			'customer_code' : customer_code,
			'warehouse_code' : warehouse_code,
			'zone_code' : zone_code,
			'is_wms' : is_wms,
			'api' : api,
			'remark' : remark
		},
		success:function(rs){
			load_out();

			if(rs == 'success') {
				$('.edit').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					text:'ต้องการโหลดข้อมูลรายการสินค้าใหม่หรือไม่ ?',
					type: 'success',
					showCancelButton: true,
					cancelButtonText: 'No',
					confirmButtonText: 'Yes',
					closeOnConfirm: true
				}, function() {
					load_in();
					window.location.reload();
				});
			}
			else
			{
				swal({
					title:'Error!!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}



$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});



function addNew()
{
  var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var customer_code = $('#customer_code').val();
	var zone_code = $('#zone_code').val();
	var is_wms = $('#is_wms').val();
	let remark = $.trim($('#remark').val());
	let reqRemark = $('#required-remark').val();


  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(is_wms == 0) {
		if(zone_code.length == 0){
			swal('กรุณาระบุโซนรับสินค้า');
			return false;
		}
	}

	if(reqRemark == 1 && remark.length < 10) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาใส่หมายเหตุ (ความยาวอย่างน้อย 10 ตัวอักษร)',
			type:'warning'
		});

		return false;
	}


  $('#addForm').submit();
}



$('#warehouse').autocomplete({
	source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#warehouse_code').val(arr[0]);
			$('#warehouse').val(arr[1]);
			zoneInit();
		}else{
			$('#warehouse_code').val('');
			$('#warehouse').val('');
		}
	}
});


$('#customer_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customer').val('');
		}
	}
});

$('#customer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customer').val('');
		}
	}
});


$('#zone_code').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#zone').val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$('#zone').val('');
			$('#zone_code').val('');
		}
	}
});


$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#zone').val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$('#zone').val('');
			$('#zone_code').val('');
		}
	}
});


function recalRow(el, no) {
	var price = parseFloat($('#price_' + no).val());
	var qty = parseFloat(el.val());
	var discount = parseFloat($('#discount_' + no).val()) * 0.01;
	price = isNaN(price) ? 0 : price;
	qty = isNaN(qty) ? 0 : qty;
	discount = qty * (price * discount);
	var amount = (qty * price) - discount;
	amount = amount.toFixed(2);
	$('#amount_' + no).text(addCommas(amount));
	recalTotal();
}



function recalTotal(){
	var totalAmount = 0;
	var totalQty = 0;
	$('.amount-label').each(function(){
		let amount = removeCommas($(this).text());
		amount = parseDefault(parseFloat(amount), 0);
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty), 0);
		totalQty += qty;
	});

	totalQty = totalQty.toFixed(2);
	totalAmount = totalAmount.toFixed(2);

	$('#total-qty').text(addCommas(totalQty));
	$('#total-amount').text(addCommas(totalAmount));
}



function removeRow(no, id){
	if(id != '' && id != '0' && id != 0){
		$.ajax({
			url:HOME + 'delete_detail/'+id,
			type:'GET',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					$('#row_' + no).remove();
					//reIndex();
					//recalTotal();
				}
				else
				{
					swal(rs);
					return false;
				}
			}
		});
	}
	else
	{
		$('#row_'+no).remove();
		// reIndex();
		// recalTotal();
	}
}


function accept() {
	$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
	$('#accept-modal').modal('show');
}

function acceptConfirm() {
	let code = $('#return_code').val();
	let note = $.trim($('#accept-note').val());

	if(note.length < 10) {
		$('#accept-error').text('กรุณาระบุหมายเหตุอย่างนี้อย 10 ตัวอักษร');
		return false;
	}
	else {
		$('#accept-error').text('');
	}

	load_in();

	$.ajax({
		url:HOME + 'accept_confirm',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"accept_remark" : note
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
					text: rs,
					type:'error'
				});
			}
		}
	});

}


$(document).ready(function(){
	load_out();
});
