function toggleCheckAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} 
	else {
		$('.chk').prop("checked", false);
	}
}

function deleteChecked(){
	let count = $('.chk:checked').length;
	if(count > 0){
		load_in();
		$('.chk:checked').each(function(){
			let id = $(this).data('id');
			let no = $(this).val();
			count--;
			removeRow(no, id, count);
		})
	}
}

function clearReturnQty() {
	let count = $('.input-qty').length;
	if(count > 0) {
		$('.input-qty').each(function() {
			$(this).val('');
			recalRow($(this).data('no'));
		});
	}
}

function unsave(){
	let code = $('#code').val();
	$.ajax({
		url:`${HOME}unsave/${code}`,
		type:'POST',
		cache:false,
		success:function(rs){
			if(rs === 'success'){
				swal({
					title:'Success',
					text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
					type:'success',
					time:1000
				});

				setTimeout(function(){
					goEdit(code);
				}, 1500);
			}
		}
	})
}

function save(option = 1) {	
	let count = 0;
	let items = [];

	$('.input-qty').each(function(){
		let no = $(this).data("no");
		let qty = parseDefault(parseFloat($(this).val()), 0);

		if(qty > 0) {
			let item_code = $(`#item_${no}`).val();
			let item_name = $(`#item_name_${no}`).val();
			let price = parseDefault(parseFloat($(`#price_${no}`).val()), 0);
			let discount = parseDefault(parseFloat($(`#discount_${no}`).val()), 0);

			let ds = {
				"item_code" : item_code,
				"item_name" : item_name,
				"price" : price.toFixed(2),
				"discount" : discount.toFixed(2),
				"qty" : qty
			};

			items.push(ds);
			count++;
		}
	});

	if(count == 0) {
		swal("ไม่พบรายการคืนสินค้า", "", "error");
		return false;
	}

	let code = $('#code').val();
	let data = {
		"code" : code, 
		"save_type" : option, // 0 = darft, 1 = save immediately, 3 = save as request
		"items" : items
	};

	load_in();

	$.ajax({
		url:`${HOME}save`,
		type:'POST',
		cache:false,
		data:JSON.stringify(data),
		contentType: 'application/json',
		complete:function(rs) {
			load_out();
			if(rs.responseText === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					viewDetail(code);
				}, 1200);
			}
			else {
				if(rs.status == 200) {
					swal({
						title:"Error!",
						text:rs.responseText,
						type:'error',
						html:true
					});
				}
				else {
					swal({
						title:"Error!",
						text:"Error-" + rs.status + " : Internal server error",
						type:'error',
						html:true
					});
				}
			}
		}
	});
}

function approve() {
	let code = $('#code').val();
	let url = `${HOME}approve/${code}`;
	load_in();
	$.get(url, function(rs) {
		if(rs === 'success') {
			load_out();

			swal({
				title:'Success',
				type:'success',
				timer: 1000
			});

			setTimeout(function(){
				window.location.reload();
			}, 1000);

		}
		else {
			showError(rs);			
		}
	});
}

function doExport(){
	let code = $('#code').val();
	load_in();
	$.ajax({
		url:`${HOME}export_return/${code}`,
		type:'GET',
		cache:false,
		success:function(rs) {
			load_out();
			if(rs == 'success') {
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
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
}

function editHeader(){
	$('.e').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}

function update() {
	let h = {
		'code' : $('#code').val(),
		'date_add' : $('#date-add').val(),
		'invoice' : $('#invoice').val(),
		'customer_code' : $('#customer-code').val(),
		'is_wms' : $('#is_wms').val(),
		'is_api' : $('#is_api').val(),
		'warehouse_code' : $('#warehouse-code').val(),
		'zone_code' : $('#zone-code').val(),		
		'from_zone' : $('#from-zone').val(),
		'remark' : $('#remark').val(),
		'gp' : $('#gp').val()
	};
	
	if(!isDate(h.date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(h.invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(h.customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(h.zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	if(h.from_zone.length == 0){
		swal('กรุณาระบุโซนฝากขาย');
		return false;
	}

  load_in();

	$.ajax({
		url:`${HOME}update`,
		type:'POST',
		cache:false,
		data:h,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				$('.e').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					type: 'success',
					timer:1000
				});
			}
			else
			{
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
}

$('#date-add').datepicker({
	dateFormat:'dd-mm-yy'
});

function invoice_init() {	
	$('#invoice').autocomplete({
		source:`${HOME}search_invoice_code`,
		autoFocus:true,
		close:function(){
			let rs = $(this).val().trim();
			let arr = rs.split(' | ');
			if(arr.length == 2)
			{
				$(this).val(arr[0]);
			}
		}
	})
}

function add() {  
	let h = {
		'date_add' : $('#date-add').val(),
		'invoice' : $('#invoice').val(),		
		'gp' : parseDefaultFloat($('#gp').val(), 0),
		'customer_code' : $('#customer-code').val(),
		'from_zone' : $('#from-zone').val(),
		'zone_code' : $('#zone-code').val(),
		'remark' : $('#remark').val().trim()
	};

  if(!isDate(h.date_add)) {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(h.customer_code.length == 0) {
		swal('กรุณาระบุลูกค้า');
		return false;
	}

	if(h.invoice.length == 0) {
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(h.gp > 100 || h.gp < 0) {
		swal('GP ต้องอยู่ระหว่าง 0-100');
		return false;
	}

	if(h.from_zone.length == 0) {
		swal('กรุณาระบุโซนฝากขาย');
		return false;
	}

	if(h.zone_code.length == 0) {
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	load_in();

	$.ajax({
		url: `${HOME}add`,
		type:'POST',
		cache:false,
		data:h,
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);
				
				if(ds.status === 'success') {
					edit(ds.code);
				}
				else{
					showError(ds.message);
				}
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

$('#customer-code').autocomplete({
	source:`${BASE_URL}auto_complete/get_customer_code_and_name`,
	autoFocus:true,
	close:function(){
		let arr = $(this).val().split(' | ');
		if(arr.length == 2) {
			$('#customer-code').val(arr[0]);
			$('#customer-name').val(arr[1]);
		}
		else {
			$('#customer-code').val('');
			$('#customer-name').val('');
		}
		
		fromZoneInit();
		invoice_box_init();
		invoiceInit();
	}
});

function fromZoneInit() {
	let customer = $('#customer-code').val();

	$('#from-zone').autocomplete({
		source : `${BASE_URL}auto_complete/get_consignment_zone/${customer}`,
		autoFocus:true,
		close:function(){
			let arr = $(this).val().split(' | ');
			if(arr.length == 2) {
				$('#from-zone').val(arr[0]);
				$('#from-zone-name').val(arr[1]);				
			}
			else {
				$('#from-zone').val('');
				$('#from-zone-name').val('');
			}
		}
	});
}

$('#zone-code').autocomplete({
	source : `${BASE_URL}auto_complete/get_common_zone_code_and_name`,
	autoFocus:true,
	close:function(){
		let arr = $(this).val().split(' | ');
		if(arr.length == 2) {
			$('#zone-code').val(arr[0]);			
			$('#zone-name').val(arr[1]);
		}
		else {
			$('#zone-code').val('');
			$('#zone-name').val('');
		}
	}
});

function recalRow(no) {
	let price = parseDefaultFloat($('#price_' + no).val(), 0);
	let qty = parseDefaultFloat($('#qty_'+no).val(), 0);
	let discount = parseDefaultFloat($('#discount_' + no).val(), 0) * 0.01;
	discount = qty * (price * discount);
	let amount = (qty * price) - discount;
	amount = amount.toFixed(2);
	$('#amount_' + no).text(addCommas(amount));
	recalTotal();
}

function recalTotal() {
	let totalQty = 0;
	let totalAmount = 0;

	$('.amount-label').each(function(){
		let amount = parseDefaultFloat(removeCommas($(this).text()), 0);		
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		let qty = parseDefaultFloat($(this).val(), 0);
		totalQty += qty;
	});

	$('#total-qty').val(addCommas(totalQty));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
}

function removeRow(no, id, count){
	if(id != '' && id != '0' && id != 0){
		$('#row_' + no).remove();
		if(count == 1) {
			reIndex();
			recalTotal();
			load_out();
		}
	}
	else
	{
		$('#row_'+no).remove();
		reIndex();
		recalTotal();
	}
}

function load_stock_in_zone() {
	swal({
    title: "นำเข้าสินค้าในโซน",
		text: "รายการที่มีอยู่ในเอกสารจะถูกลบแล้วแทนที่ด้วยสินค้าในโซน <br/> ต้องการดำเนินการหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'ดำเนินการ',
		cancelButtonText: 'ยกเลิก',
		html:true,
		closeOnConfirm: true
  },function(){
    let code = $('#code').val();
    load_in();

		setTimeout(() => {
			$.ajax({
				url: `${HOME}load_stock_in_zone`,
				type: 'POST',
				cache: false,
				data: {
					'code': code
				},
				success: function (rs) {
					load_out();
					if (rs.trim() == 'success') {
						swal({
							title: 'Success',
							type: 'success',
							timer: 1000
						});

						setTimeout(function () {
							window.location.reload();
						}, 1500);
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

function invoice_box_init() {
	let customer_code = $('#customer-code').val().trim();

	$('#invoice-box').autocomplete({
		source: `${BASE_URL}auto_complete/get_sap_invoice_code/${customer_code}`,
		autoFocus:true,
		close:function() {
			let arr = $(this).val().split(' | ');
			if(arr.length === 2) {
				$(this).val(arr[0]);
			}
			else {
				$(this).val('');
			}
		}
	})
}

function get_invoice_gp(invoice) {
	$.ajax({
		url:`${HOME}get_invoice_gp`,
		type:'GET',
		cache:false,
		data:{
			'invoice' : invoice
		},
		success:function(rs) {
			let arr = rs.split(' | ');
			if(arr.length == 2) {
				$('#gp').val(arr[1]);
			}
			else {
				$('#gp').val("");
			}
		}
	})
}

function invoiceInit() {
	let customer_code = $('#customer-code').val();
	$('#invoice').autocomplete({
		source: `${BASE_URL}auto_complete/get_sap_invoice_code/${customer_code}`,
		autoFocus:true,
		close:function() {
			let arr = $(this).val().split(' | ');
			if(arr.length === 2) {
				$(this).val(arr[0]);
				get_invoice_gp(arr[0]);
			}
			else {
				$(this).val('');
			}
		}
	});
}

function setProductArrival(code, status) {
	load_in();
	$.ajax({
		url: `${HOME}set_product_arrival`,
		type: 'POST',
		cache: false,
		data: {
			'code': code,
			'arrival': status
		},
		success: function (rs) {
			load_out();
			if (rs.trim() == 'success') {
				swal({
					title: 'Success',
					type: 'success',
					timer: 1000
				});
				setTimeout(function () {
					window.location.reload();
				}, 1500);
			} 
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			load_out();
			showError(rs);
		}
	});
}	

window.addEventListener('load', function() {
	fromZoneInit();
	invoice_box_init();
	invoiceInit();
});
