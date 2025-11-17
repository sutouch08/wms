window.addEventListener('load', () => {
	poInit();
	vendorInit();
	zoneInit();
});

var click = 0;

$("#date-add").datepicker({ dateFormat: 'dd-mm-yy'});
$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy'});

function reGenCode() {
	let date_add = $('#date-add').val();

	$.ajax({
		url:HOME + 'gen_new_code',
		type:'POST',
		cache:false,
		data:{
			'date_add' : date_add
		},
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					$('#code').val(ds.code);
				}
			}
		}
	})
}


function poInit() {
	let vendor_code = $('#vendor-code').val();
	let item = [];

	$('#po-code').autocomplete({
		source:HOME + 'get_po_code/' + vendor_code,
		autoFocus:true,
		// position: {
		// 	my: 'right top',
		// 	at: 'right bottom'
		// },
		select:function(event, ui) {
			item = ui.item;
		},
		close:function() {
			let label = item.label;
			let arr = label.split(' | ');
			let currency = item.currency;
			let rate = item.rate;

			if(arr.length == 3) {
				$(this).val(arr[0]);

				if(vendor_code.length == 0) {
					$('#vendor-code').val(arr[1]);
					$('#vendor-name').val(arr[2]);
				}

				$('#DocCur').val(currency);
				$('#DocRate').val(rate);

				confirmChangePo();
			}
			else {
				$(this).val('');
			}
		}
	});
}


function vendorInit() {
	$('#vendor-code').autocomplete({
		source:HOME + 'get_vendor',
		autoFocus:true,
		close:function(rs) {
			let arr = $(this).val().split(' | ');

			if(arr.length === 2) {
				$('#vendor-code').val(arr[0]);
				$('#vendor-name').val(arr[1]);

				setTimeout(() => {
					poInit();
					$('#po-code').focus();
				}, 100);
			}
			else {
				$('#vendor-code').val('');
				$('#vendor-name').val('');

				setTimeout(() => {
					poInit();
				}, 100);
			}
		}
	})
}


function zoneInit() {
	let whsCode = $('#warehouse').val();
	$('#zone-code').val('');
	$('#zone-name').val('');

	$('#zone-code').autocomplete({
		source:HOME + 'get_zone/' + whsCode,
		autoFocus:true,
		close:function() {
			let label = $(this).val().split(' | ');

			if(label.length == 2) {
				$(this).val(label[0]);
				$('#zone-name').val(label[1]);
			}
			else {
				$(this).val('');
				$('#zone-name').val('');
			}
		}
	})
}


function add() {
	if(click == 0) {
		click = 1;
		clearErrorByClass('r');
		let h = {
			'date_add' : $('#date-add').val(),
			'posting_date' : $('#posting-date').val(),
			'vendor_code' : $('#vendor-code').val().trim(),
			'vendor_name' : $('#vendor-name').val().trim(),
			'remark' : $('#remark').val().trim()
		};

		if( ! isDate(h.date_add)) {
			$('#date-add').hasError();
			click = 0;
			return false;
		}

		if( ! isDate(h.posting_date)) {
			$('#posting-date').hasError();
			click = 0;
			return false;
		}

		if(h.vendor_code.length == 0 || h.vendor_name.length == 0) {
			$('#vendor-code').hasError();
			$('#vendor-name').hasError();
			click = 0;
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
				click = 0;
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {
						goEdit(ds.code);
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
				showError(rs);
				click = 0;
			}
		})
	}
}


function save(saveType) {
	if(click == 0) {
		click = 1;
		clearErrorByClass('r');

		let h = {
			'code' : $('#code').val(),
			'save_type' : saveType,
			'date_add' : $('#date-add').val(),
			'posting_date' : $('#posting-date').val(),
			'vendor_code' : $('#vendor-code').val().trim(),
			'vendor_name' : $('#vendor-name').val().trim(),
			'po_code' : $('#po-code').val().trim(),
			'currency' : $('#DocCur').val(),
			'rate' : $('#DocRate').val(),
			'invoice_code' : $('#invoice-code').val().trim(),
			'warehouse_code' : $('#warehouse').val(),
			'zone_code' : $('#zone-code').val().trim(),
			'remark' : $('#remark').val().trim(),
			'items' : []
		};

		if( ! isDate(h.date_add)) {
			click = 0;
			$('#date-add').hasError();
			return false;
		}

		if( ! isDate(h.posting_date)) {
			click = 0;
			$('#posting-date').hasError();
			return false;
		}

		if(h.vendor_code.length == 0 || h.vendor_name.length == 0) {
			click = 0;
			$('#vendor-code').hasError();
			$('#vendor-name').hasError();
			return false;
		}

		if(saveType == 'C' || saveType == 'O') {
			if(h.po_code.length == 0) {
				click = 0;
				$('#po-code').hasError();
				return false;
			}

			if(h.invoice_code.length == 0) {
				click = 0;
				$('#invoice-code').hasError();
				return false;
			}

			if(h.warehouse_code == '') {
				click = 0;
				$('#warehouse').hasError();
				return false;
			}

			if(saveType == 'c' && h.zone_code.length == 0) {
				click = 0;
				$('#zone-code').hasError();
				return false;
			}

			if($('.receive-qty').length == 0) {
				click = 0;
				showError('ไม่พบรายการรับเข้า');
				return false;
			}

			let err = 0;

			$('.receive-qty').each(function() {
				let el = $(this);
				let qty = parseDefaultFloat(el.val(), 0);

				if(qty > 0) {
					let uid = el.data('uid');

					let row = {
						'uid' : uid,
						'baseEntry' : el.data('baseentry'),
						'baseLine' : el.data('baseline'),
						'product_code' : el.data('code'),
						'product_name' : el.data('name'),
						'qty' : qty,
						'price' : el.data('price'),
						'backlogs' : el.data('backlogs'),
						'currency' : el.data('currency'),
						'rate' : h.rate,
						'vatGroup' : el.data('vatcode'),
						'vatRate' : el.data('vatrate')
					}

					h.items.push(row);
				}
				else {
					err++;
					el.hasError();
				}
			});

			if(error > 0) {
				click = 0;
				showError('พบรายการที่ไม่ถูกต้อง');
				return false;
			}
		}

		console.log(h);
	}
}
