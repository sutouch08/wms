$(document).ready(function() {
	invoice_init();
});


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

var click = 0;

function save()
{
	if(click > 0) {
		return false;
	}

	click = 1;

	$('#btn-save').attr('disabled', 'disabled');

	$('.input-qty').removeClass('has-error');

	var error = 0;
	let code = $('#return_code').val();
	let rows = [];

	$('.input-qty').each(function() {
		let el = $(this);

		let qty = parseDefault(parseFloat(el.val()), 0);

		if(qty > 0) {
			let sold = parseDefault(parseFloat(el.data('sold')), 0);

			if(qty <= sold) {
				let row = {
					'no' : el.data('no'),
					'product_code' : el.data('pdcode'),
					'product_name' : el.data('pdname'),
					'order_code' : el.data('order'),
					'sold_qty' : el.data('sold'),
					'qty' : el.val(),
					'price' : el.data('price'),
					'discount_percent' : el.data('discount')
				}

				rows.push(row);
			}
			else {
				el.addClass('has-error');
				error++;
			}
		}
	});

	if(error > 0) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาแก้ไขข้อผิดพลาด',
			type:'warning'
		});

		click = 0;
		$('#btn-save').removeAttr('disabled');

		return false;
	}

	if(rows.length == 0) {
		swal({
			title:'ข้อผิดพลาด',
			text:'ไม่พบจำนวนในการรับคืน',
			type:'warning'
		});

		click = 0;
		$('#btn-save').removeAttr('disabled');

		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add_details/' + code,
		type:'POST',
		cache:false,
		data: {
			'data' : JSON.stringify(rows)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						viewDetail(code);
					}, 1200);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error',
						html:true
					})

					click = 0;
					$('#btn-save').removeAttr('disabled');
				}
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				});

				click = 0;
				$('#btn-save').removeAttr('disabled');
			}
		},
		error:function(xhr) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			});

			click = 0;
			$('#btn-save').removeAttr('disabled');
		}
	})

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

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						if(ds.ex == 1) {
							setTimeout(() => {
								swal({
									title:'Infomation',
									text:ds.message,
									type:'info'
								},
								function() {
									window.location.reload();
								})
							}, 100);
						}
						else {
							setTimeout(() => {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								})

								setTimeout(() => {
									window.location.reload();
								}, 1200);
							}, 100);
						}
					}
					else {
						setTimeout(() => {
							swal({
								title:'Error!',
								text:ds.message,
								type:'error',
								html:true
							})
						}, 100);
					}
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:rs,
							type:'error'
						});
					}, 100);
				}
			},
			error:function(xhr) {
				load_out();

				setTimeout(() => {
					swal({
						title:'Error!',
						text:xhr.responseText,
						type:'errr',
						html:true
					});
				}, 200);
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


function sendToSoko() {
	var code = $('#return_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_soko',
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
	$('.edit').removeClass('has-error');

	let code = $('#return_code').val();
	let date_add = $('#dateAdd').val();
	let shipped_date = $('#shipped-date').val();
	let invoice = $('#invoice').val();
	let customer_code = $('#customer_code').val();
	let zone_code = $('#zone_code').val();
	let is_wms = $('#is_wms').val();
	let api = $('#api').val();
	let reqRemark = $('#required_remark').val();
  let remark = $.trim($('#remark').val());

	if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
		$('#dateAdd').addClass('has-error');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		$('#invoice').addClass('has-error');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		$('#customer_code').addClass('has-error');
		return false;
	}

	if(is_wms == "") {
		swal("กรุณาระบุการรับ");
		$('#is_wms').addClass('has-error');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		$('#zone_code').addClass('has-error');
		return false;
	}

	if(reqRemark == 1 && remark.length < 10) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาใส่หมายเหตุ (ความยาวอย่างน้อย 10 ตัวอักษร)',
			type:'warning'
		});

		$('#remark').addClass('has-error');
		return false;
	}

	let data = {
		'code' : code,
		'date_add' : date_add,
		'shipped_date' : shipped_date,
		'invoice' : invoice,
		'customer_code' : customer_code,
		'zone_code' : zone_code,
		'is_wms' : is_wms,
		'api' : api,
		'remark' : remark
	}

  load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(data)
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

$('#shipped-date').datepicker({
	dateFormat:'dd-mm-yy'
});


function updateZone() {
	let el = $('#is_wms option:selected');
	$('#zone_code').val(el.data('zonecode'));
	$('#zone').val(el.data('zonename'));
}


function addNew()
{
	$('.h').removeClass('has-error');

  let date_add = $('#dateAdd').val();
	let shipped_date = $('#shipped-date').val();
	let invoice = $('#invoice').val();
	let customer_code = $('#customer_code').val();
	let zone_code = $('#zone_code').val();
	let is_wms = $('#is_wms').val();
	let api = $('#api').val();
	let remark = $.trim($('#remark').val());
	let reqRemark = $('#required-remark').val();


  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
		$('#dataAdd').addClass('has-error');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		$('#invoice').addClass('has-error');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		$('#customer_code').addClass('has-error');
		return false;
	}

	if(is_wms == "") {
		swal("กรุณาระบุการรับ");
		$('#is_wms').addClass('has-error');
		return false;
	}

	if(zone_code.length == 0) {
		swal('กรุณาระบุโซนรับสินค้า');
		$('#zone_code').addClass('has-error');
		return false;
	}


	if(reqRemark == 1 && remark.length < 10) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาใส่หมายเหตุ (ความยาวอย่างน้อย 10 ตัวอักษร)',
			type:'warning'
		});

		$('#remark').addClass('has-error');
		return false;
	}

	let data = {
		'date_add' : date_add,
		'shipped_date' : shipped_date,
		'invoice' : invoice,
		'customer_code' : customer_code,
		'zone_code' : zone_code,
		'is_wms' : is_wms,
		'api' : api,
		'remark' : remark
	}

  load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					goEdit(ds.code);
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
					title:'Error',
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
			});
		}
	})
}


function invoice_init() {
	let customer_code = $('#customer_code').val();

	$('#invoice').autocomplete({
		source:HOME + 'get_sap_invoice_code/' + customer_code,
		autoFocus:true,
		open:function(event) {
			let ul = $(this).autocomplete('widget');
			ul.css('width', 'auto');
		},
		select:function(event, ui) {
			let code = ui.item.code;
			let customerCode = ui.item.customer_code;
			let customerName = ui.item.customer_name;

			if(customerCode.length && customerName.length) {
				$('#customer_code').val(customerCode);
				$('#customer').val(customerName);
			}
		},
		close:function(){
			var arr = $(this).val().split(' | ');

			if(arr.length > 2){
				$(this).val(arr[0]);
			}
			else {
				$(this).val('');
			}
		}
	});
}


$('#customer_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer').val(arr[1]);
			invoice_init();
		}
		else {
			$('#customer_code').val('');
			$('#customer').val('');
			invoice_init();
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
			invoice_init();
		}else{
			$('#customer_code').val('');
			$('#customer').val('');
			invoice_init();
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


function rollBackExpired() {
	let code = $('#return_code').val();

	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการทำให้เอกสารนี้ยังไม่หมดอายุหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		closeOnConfirm:true
	},
	function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'roll_back_expired',
				type:'POST',
				cache:false,
				data:{
					'code' : code
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
					});
				}
			})
		}, 200);
	});
}


$(document).ready(function(){
	load_out();
});
