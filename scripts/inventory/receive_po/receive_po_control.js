//--- เพิ่มรายการจาก PO grid
function addPoItems() {
	let items = [];

	$('#poGrid').modal('hide');

	load_in();

	$('.po-qty').each(function () {
		let el = $(this);

		if (el.val() != "") {
			let qty = parseDefault(parseFloat(removeCommas(el.val())), 0);

			if (qty > 0) {
				let no = el.data('uid');

				if ($('#receive-qty-' + no).length) {
					let cqty = parseDefault(parseFloat($('#receive-qty-' + no).val()), 0);
					let nqty = cqty + qty;
					$('#receive-qty-' + no).val(nqty);

					recalAmount(no);
				}
				else {

					let itemCode = el.data('code'); //--- product code;
					let itemName = el.data('name');
					let baseEntry = el.data('baseentry');
					let baseLine = el.data('baseline');
					let bPrice = parseDefaultFloat(el.data('bprice'), 0.00);
					let aPrice = parseDefaultFloat(el.data('aprice'), 0.00);
					let discprcnt = parseDefaultFloat(el.data('discprcnt'), 0.00);
					let price = parseDefault(parseFloat(el.data('price')), 0.00); //--- price Af discount
					let limit = parseDefault(parseFloat(el.data('limit')), 0.00);
					let backlogs = parseDefault(parseFloat(el.data('backlogs')), 0);
					let amount = roundNumber(qty * price, 2);
					let vatCode = el.data('vatcode');
					let vatRate = parseDefault(parseFloat(el.data('vatrate')), 7);
					let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

					let item = {
						'uid': no,
						'pdCode': itemCode,
						'pdName': itemName,
						'baseEntry': baseEntry,
						'baseLine': baseLine,
						'vatCode': vatCode,
						'vatRate': vatRate,
						'PriceBefDi' : bPrice,
						'PriceBefDiLabel' : addCommas(bPrice.toFixed(4)),
						'PriceAfVAT' : aPrice,
						'DiscPrcnt' : discprcnt.toFixed(2),
						'Price': price,
						'PriceAfDiscLabel': addCommas(price.toFixed(4)),
						'qty': qty,
						'qtyLabel': addCommas(qty.toFixed(2)),
						'backlogs': backlogs,
						'backlogsLabel': addCommas(backlogs.toFixed(2)),
						'limit': limit,
						'amount': amount,
						'amountLabel': addCommas(amount.toFixed(2)),
						'vatAmount' : vatAmount,
						'UomEntry' : el.data('uomentry'),
						'UomCode' : el.data('uomcode'),
						'unitMsr' : el.data('unitmsr')						
					}

					items.push(item);
				}
			}
		}
	})

	if (items.length > 0) {
		let source = $('#receive-template').html();
		let output = $('#receive-table');

		render_append(source, items, output);

		$('#btn-confirm-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#po-code').attr('disabled', 'disabled');

		//--- update last no for next gennerate
		$('#no').val(0);

		//--- Calculate Summary
		recalTotal();

		//---- update running no
		reIndex();		
	}

	load_out();
}


function recalAmount(id) {
	let el = $('#receive-qty-'+id);
	el.clearError();
	let qty = parseDefaultFloat(removeCommas(el.val()), 0);
	let limit = parseDefaultFloat(el.data('limit'), 0);
	let price = parseDefaultFloat(el.data('price'), 0);	
	let vatRate = parseDefaultFloat(el.data('vatrate'), 0) * 0.01;
	let amount = price * qty;
	let vatAmount = amount * vatRate
	el.data('vatamount', vatAmount);

	$('#line-total-' + id).val(addCommas(amount.toFixed(4)));	

	if(qty > limit) {
		el.hasError();
	}

	recalTotal();
}


function recalTotal() {
	let totalAmount = 0;
	let totalQty = 0;
	let totalVat = 0;	
	let billDisc = parseDefaultFloat($('#disc-percent').val(), 0) * 0.01;	

	$('.receive-qty').each(function () {
		let el = $(this);
		let qty = parseDefaultFloat(removeCommas(el.val()), 0);		
		let price = parseDefaultFloat(el.data('price'), 0); // price after discount before vat		
		let vatRate = parseDefaultFloat(el.data('vatrate'), 0);
		let amount = qty * price;
		let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

		totalQty += qty;
		totalAmount += amount;
		totalVat += vatAmount;
	});

	let discAmount = totalAmount * billDisc;
	let totalAfDisc = totalAmount - discAmount;
	let vatSum = totalVat - (totalVat * billDisc);
	let docTotal = totalAfDisc + vatSum;

	$('#total-qty').val(addCommas(totalQty.toFixed(2)));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
	$('#disc-amount').val(addCommas(discAmount.toFixed(2)));
	$('#vat-sum').val(addCommas(vatSum.toFixed(2)));
	$('#doc-total').val(addCommas(docTotal.toFixed(2)));
}


function toggleCheckAll(el) {
	if (el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	if ($('.chk:checked').length) {
		swal({
			title: 'คุณแน่ใจ ?',
			text: 'ต้องการลบรายการที่เลือกหรือไม่ ?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d15b47',
			confirmButtonText: 'Yes',
			cancelButtonText: 'No',
			closeOnConfirm: true
		}, function () {
			$('.chk:checked').each(function () {
				let no = $(this).val();
				$('#row-' + no).remove();
			});

			recalTotal();
			reIndex();
		})
	}
}


function confirmPo() {
	let poCode = $.trim($('#po-code').val());

	if (poCode.length) {
		if ($('.receive-qty').length) {
			swal({
				title: 'คุณแน่ใจ ?',
				text: 'รายการปัจจุบันจะถูกแทนที่ด้วยรายการจากใบสั่งซื้อเลขที่ ' + poCode,
				type: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No',
				closeOnConfirm: true
			}, function () {
				setTimeout(() => {
					getPoDetail(poCode);
				}, 100);
			})
		}
		else {
			setTimeout(() => {
				getPoDetail(poCode);
			}, 100);
		}
	}
}


function getPoDetail(poCode) {

	if ( ! poCode ) {
		poCode = $('#po-code').val();
	}

	if (poCode.length == 0) {

		swal('กรุณาระบุเลขที่ใบสั่งซื้อ');		
		return false;
	}

	load_in();

	$.ajax({
		url: `${HOME}get_po_detail`,
		type: 'GET',
		cache: false,
		data: {
			'po_code': poCode
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status === 'success') {
					$('#po-code').val(ds.DocNum);
					$('#DocCur').val(ds.DocCur);
					$('#DocRate').val(ds.DocRate);
					$('#vendor-code').val(ds.CardCode);
					$('#vendor-name').val(ds.CardName);
					$('#vendor').data('code', ds.CardCode).data('name', ds.CardName);
					$('#disc-percent').val(roundNumber(ds.DiscPrcnt, 2));
					$('#disc-percent').data('discPrcnt', ds.DiscPrcnt);

					let source = $('#po-template').html();
					let data = ds.details;
					let output = $('#po-body');

					render(source, data, output);

					$('#poGrid').modal('show');
					dragElement('poGrid', 'po-grid-header');
				}
				else {
					swal({
						title: 'Error!',
						text: ds.message,
						type: 'error'
					});
				}
			}
			else {
				swal({
					title: 'Error!',
					text: rs,
					type: 'error',
					html: true
				});
			}
		}
	})
}


function getPoItems() {
	let po = $('#po-code').val();

	if (po.length == 0) {
		swal({
			title: 'Oops !',
			text: 'กรุณาระบุเลขที่ใบสั่งซื้อ',
			type: 'warning'
		});

		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'get_po_detail',
		type: 'GET',
		cache: false,
		data: {
			'po_code': poCode
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status === 'success') {
					$('#po-code').val(ds.DocNum);
					$('#DocCur').val(ds.DocCur);
					$('#DocRate').val(ds.DocRate);
					$('#vendor_code').val(ds.CardCode);
					$('#vendorName').val(ds.CardName);

					let source = $('#po-template').html();
					let data = ds.details;
					let output = $('#po-body');

					render(source, data, output);

					$('#poGrid').modal('show');

				}
				else {
					swal({
						title: 'Error!',
						text: ds.message,
						type: 'error'
					});
				}
			}
			else {
				swal({
					title: 'Error!',
					text: rs,
					type: 'error',
					html: true
				});
			}
		}
	})
}


function fillPoQty(uid) {
	let el = $(`#po-qty-${uid}`);
	let qty = parseDefaultFloat(el.data('qty'), 0);

	el.val(addCommas(qty.toFixed(2)));
}


function receiveAll() {
	$('.po-qty').each(function () {
		let el = $(this);
		let qty = parseDefaultFloat(el.data('qty'), 0);

		if (qty > 0) {
			el.val(addCommas(qty.toFixed(2)));
		}
	});
}


function clearAll() {
	$('.po-qty').val('');
}


function clearPo() {
	let poCode = $('#po-code').val();

	if ( ! poCode.length) {
		return false;
	}

	if ($('.receive-qty').length) {
		swal({
			title: 'เปลียนใบสั่งซื้อ',
			text: 'รายการทั้งหมดจะถูกลบ ต้องการเปลียนใบสั่งซื้อหรือไม่ ?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes',
			cancelButtonText: 'No',
			closeOnConfirm: true
		}, function () {
			load_in();
			setTimeout(() => {
				load_out();
				$('#receive-table').html('');
				recalTotal();				
				$('#po-code').val('').removeAttr('disabled').focus();				
			}, 200);
		});
	}
	else {
		$('#po-code').val('').removeAttr('disabled').focus();
	}
}
