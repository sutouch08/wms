var click = 0;

//--- use in receive process only
function saveAsDraft() {
	if(click === 0) {
		click = 1;
		clearErrorByClass('input-qty');

		let error = 0;

		let h = {
			'code' : $('#return_code').val(),
			'rows' : []
		};

		$('.input-qty').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);
			let sold = parseDefault(parseFloat(el.data('sold')), 0);
			let price = parseDefault(parseFloat(el.data('price')), 0);
			let discount = parseDefault(parseFloat(el.data('discount')), 0);

			if(qty < 0 || qty > sold) {
				el.hasError();
				error++;
			}
			else {
				h.rows.push({
					'id' : el.data('id'),
					'invoice' : el.data('invoice'),
					'DocEntry' : el.data('docentry'),
					'LineNum' : el.data('linenum'),
					'order_code' : el.data('order'),
					'product_code' : el.data('pdcode'),
					'product_name' : el.data('pdname'),
					'sold_qty' : sold,
					'qty' : qty,
					'price' : price,
					'discount' : discount
				});
			}
		});

		if(error > 0) {
			click = 0;
			swal("จำนวนไม่ถูกต้อง");
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'save_as_draft',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();

				if(rs.trim() === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});
				}
				else {
					beep();
					showError(rs);
				}

				click = 0;
			},
			error:function(rs) {
				beep();
				showError(rs);
				click = 0;
			}
		})
	}
}


//--- use in receive process only
function saveAndClose() {
	if(click === 0) {
		click = 1;

		clearErrorByClass('input-qty');

		let error = 0;

		let h = {
			'code' : $('#return_code').val(),
			'rows' : []
		};

		$('.input-qty').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);
			let sold = parseDefault(parseFloat(el.data('sold')), 0);
			let price = parseDefault(parseFloat(el.data('price')), 0);
			let discount = parseDefault(parseFloat(el.data('discount')), 0);

			if(qty < 0 || qty == 0 || qty > sold) {
				el.hasError();
				error++;
			}
			else {
				h.rows.push({
					'id' : el.data('id'),
					'invoice' : el.data('invoice'),
					'DocEntry' : el.data('docentry'),
					'LineNum' : el.data('linenum'),
					'order_code' : el.data('order'),
					'product_code' : el.data('pdcode'),
					'product_name' : el.data('pdname'),
					'sold_qty' : sold,
					'qty' : qty,
					'price' : price,
					'discount' : discount
				});
			}
		});

		if(error > 0) {
			click = 0;
			beep();
			swal("จำนวนไม่ถูกต้อง");
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'save_and_close',
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
						if(ds.ex == 1) {
							swal({
								title:'Infomation',
								text:ds.message,
								type:'info'
							},
							function() {
								window.location.reload();
							})
						}
						else {
							swal({
								title:'Success',
								type:'success',
								timer:1000
							})

							setTimeout(() => {
								window.location.reload();
							}, 1200);
						}
					}
					else {
						showError(ds.message);
					}
				}
				else {
					beep();
					showError(rs);
				}

				click = 0;
			},
			error:function(rs) {
				beep();
				showError(rs);
				click = 0;
			}
		})
	}
}


function save(saveType) {
	// saveType 0 = Draft, 1 = Save, 3 wms process
	if(click === 0) {
		click = 1;

		clearErrorByClass('h');

		let error = 0;

		let h = {
			'save_type' : saveType,
			'code' : $('#return_code').val(),
			'date_add' : $('#dateAdd').val(),
			'shipped_date' : $('#shipped-date').val(),
			'customer_code' : $('#customer-code').val().trim(),
			'customer_name' : $('#customer-name').val().trim(),
			'invoice' : $('#invoice').val(),
			'warehouse_code' : $('#warehouse').val(),
			'zone_code' : $('#zone-code').val().trim(),
			'remark' : $('#remark').val().trim(),
			'rows' : []
		};

		if( ! isDate(h.date_add)) {
			click = 0;
			$('#dateAdd').hasError();
			swal("วันที่ไม่ถูกต้อง");
			return false;
		}

		if(h.shipped_date.length > 0 && ! isDate(h.shipped_date)) {
			click = 0;
			$('#shipped-date').hasError();
			swal("วันที่ไม่ถูกต้อง");
			return false;
		}

		if(h.warehouse_code == "") {
			click = 0;
			$('#warehouse').hasError();
			swal("กรุณาเลือกคลังรับคืนสินค้า");
			return false;
		}

		if(saveType != 0 || $('.input-qty').length) {
			if(h.invoice == "" || h.invoice.length < 7) {
				click = 0;
				$('#invoice').hasError();
				swal("ใบกำกับไม่ถูกต้อง");
				return false;
			}
		}

		if(saveType != 0 && (h.zone_code == "" || h.zone_code.length < 9)) {
			click = 0;
			$('#zone-code').hasError();
			swal("กรุณาระบุโซนรับเข้า");
			return false;
		}

		let required_remark = $('#required_remark').val();

		if(required_remark == 1 && h.remark.length < 10) {
			click = 0;
			$('#remark').hasError();
			swal("กรุณาระบุหมายเหตุ");
			return false;
		}

		$('.input-qty').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);
			let sold = parseDefault(parseFloat(el.data('sold')), 0);
			let price = parseDefault(parseFloat(el.data('price')), 0);
			let discount = parseDefault(parseFloat(el.data('discount')), 0);

			if(qty != 0) {
				if(qty < 0 || qty > sold) {
					el.hasError();
					error++;
				}
				else {
					h.rows.push({
						'invoice' : el.data('invoice'),
						'DocEntry' : el.data('docentry'),
						'LineNum' : el.data('linenum'),
						'order_code' : el.data('order'),
						'product_code' : el.data('pdcode'),
						'product_name' : el.data('pdname'),
						'sold_qty' : sold,
						'qty' : qty,
						'price' : price,
						'discount' : discount
					});
				}
			}
		});

		if(error > 0) {
			click = 0;
			swal("จำนวนไม่ถูกต้อง");
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'save',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();

				if(rs.trim() === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						refresh();
					}, 1200);
				}
				else {
					beep();
					showError(rs);
				}

				click = 0;
			},
			error:function(rs) {
				beep();
				showError(rs);
				click = 0;
			}
		})
	}
}


function unsave(){
	var code = $('#return_code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการย้อนสถานะ '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
	}, function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'unsave',
				type:'POST',
				cache:false,
				data:{
					'code' : code
				},
				success:function(rs) {
					load_out();
					if(rs === 'success') {
						swal({
							title:'Success',
							type:'success',
							time:1000
						});

						setTimeout(function(){
							goEdit(code);
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
		}, 100);
	});
}


function approve(){
	let code = $('#return_code').val();

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
		setTimeout(() => {
			$.ajax({
				url:HOME + 'approve',
				type:'POST',
				cache:false,
				data:{
					'code' : code
				},
				success:function(rs) {
					load_out();

					if(isJson(rs)) {
						let ds = JSON.parse(rs);

						if(ds.status == 'success') {
							if(ds.ex == 1) {
								swal({
									title:'Infomation',
									text:ds.message,
									type:'info'
								},
								function() {
									window.location.reload();
								})
							}
							else {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								})

								setTimeout(() => {
									window.location.reload();
								}, 1200);
							}
						}
						else {
							showError(ds.message);
						}
					}
					else {
						beep();
						showError(rs);
					}
				},
				error:function(rs) {
					beep();
					showError(rs);
				}
			});
		}, 100);
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


function editHeader(){
	$('.e').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
	clearErrorByClass('e');

	let code = $('#return_code').val();
	let date_add = $('#dateAdd').val();
	let shipped_date = $('#shipped-date').val();
	let invoice = $('#invoice').val();
	let customer_code = $('#customer-code').val();
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
		$('#customer-code').addClass('has-error');
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


function add() {
	clearErrorByClass('h');

  let date_add = $('#dateAdd').val();
	let shipped_date = $('#shipped-date').val();
	let customer_code = $('#customer-code').val();
	let warehouse_code = $('#warehouse').val();
	let remark = $.trim($('#remark').val());
	let reqRemark = $('#required-remark').val();


  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
		$('#dataAdd').hasError();
    return false;
  }

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		$('#customer-code').hasError();
		return false;
	}

	if(warehouse_code == "") {
		swal("กรุณาเลือกคลังรับเข้า");
		$('#warehouse').hasError();
		return false;
	}

	if(reqRemark == 1 && remark.length < 10) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาใส่หมายเหตุ (ความยาวอย่างน้อย 10 ตัวอักษร)',
			type:'warning'
		});

		$('#remark').hasError();
		return false;
	}

	let data = {
		'date_add' : date_add,
		'shipped_date' : shipped_date,
		'customer_code' : customer_code,
		'warehouse_code' : warehouse_code,
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


$('#customer-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer-code').val(arr[0]);
			$('#customer-name').val(arr[1]);
		}
		else {
			$('#customer-code').val('');
			$('#customer-name').val('');
		}
	}
});


function checkCustomer() {
	let customerCode = $('#customer-code').val().trim();
	let prevCustCode = $('#prev-customer-code').val();
	let prevCustName = $('#prev-customer-name').val();
	let invoice = $('#invoice').val().trim();

	if(customerCode.length < 3) {
		$('#customer-code').val('');
		$('#customer-name').val('');

		if(invoice.length > 7) {
			confirmChangeCustomer();
		}
		else {
			invoice_init();
		}
	}
	else {
		if(customerCode != prevCustCode) {
			if(invoice.length > 7) {
				confirmChangeCustomer();
			}
			else {
				invoice_init();
			}
		}
	}
}


function confirmChangeCustomer() {
	let prevCustCode = $('#prev-customer-code').val();
	let prevCustName = $('#prev-customer-name').val();
	swal({
		title:'Clear data',
		text:'รายการทั้งหมดจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function(isConfirm) {
		if(isConfirm) {
			clearInvoice();

			setTimeout(() => {
				invoice_init();
			}, 1000);
		}
		else {
			$('#customer-code').val(prevCustCode);
			$('#customer-name').val(prevCustName);
		}
	})
}

function clearInvoice() {
	$('#detail-table').html('');

	recalTotal();

	$('#btn-clear-inv').addClass('hide');
	$('#btn-confirm-inv').removeClass('hide');
	$('#invoice').removeAttr('disabled');
	$('#invoice').val('').focus();
	$('.item-control').removeAttr('disabled');
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
