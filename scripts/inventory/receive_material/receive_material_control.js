//--- เพิ่มรายการจาก PO grid
function addPoItems() {
	let items = [];

	$('#poGrid').modal('hide');

	load_in();

	$('.po-qty').each(function() {
		let el = $(this);

		if(el.val() != "") {
			let qty = parseDefault(parseFloat(removeCommas(el.val())), 0);

			if(qty > 0) {
				let no = el.data('uid');

				if($('#receive-qty-'+no).length) {
					let cqty = parseDefault(parseFloat($('#receive-qty-'+no).val()), 0);
					let nqty = cqty + qty;
					$('#receive-qty-'+no).val(nqty);

					recalAmount(no);
				}
				else {

					let itemCode = el.data('code'); //--- product code;
					let itemName = el.data('name');
					let baseEntry = el.data('baseentry');
					let baseLine = el.data('baseline');
					let price = parseDefault(parseFloat(el.data('price')), 0.00); //--- price Af discount
					let limit = parseDefault(parseFloat(el.data('limit')), 0.00);
					let backlogs = parseDefault(parseFloat(el.data('backlogs')), 0);
					let amount = roundNumber(qty * price, 2);
					let vatCode = el.data('vatcode');
					let vatRate = parseDefault(parseFloat(el.data('vatrate')), 7);
					let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

					let item = {
						'uid' : no,
						'pdCode' : itemCode,
						'pdName' : itemName,
						'baseEntry' : baseEntry,
						'baseLine' : baseLine,
						'vatCode' : vatCode,
						'vatRate' : vatRate,
						'price' : price,
						'priceLabel' : addCommas(price.toFixed(3)),
						'qty' : qty,
						'qtyLabel' : addCommas(qty.toFixed(2)),
						'backlogs' : backlogs,
						'backLogsLabel' : addCommas(backlogs.toFixed(2)),
						'limit' : limit,
						'amount' : amount,
						'amountLabel' : addCommas(amount.toFixed(2))
					}

					items.push(item);
				}
			}
		}
	})

	if(items.length > 0) {
		let source = $('#receive-template').html();
		let output = $('#receive-table');

		render_append(source, items, output);

		$('#btn-confirm-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#poCode').attr('disabled', 'disabled');

		//--- update last no for next gennerate
		$('#no').val(0);

		//--- Calculate Summary
		recalTotal();

		//---- update running no
		reIndex();

		swal({
			title:'Success',
			type:'success',
			timer:1000
		});
	}

	load_out();
}


function recalAmount(id) {
	let price = parseDefault(parseFloat(removeCommas($('#row-price-'+id).val())), 0);
	let qty = parseDefault(parseFloat($('#receive-qty-'+id).val()), 0);
	let amount = price * qty;
	$('#line-total-'+id).val(addCommas(amount.toFixed(2)));

	recalTotal();
}


function recalTotal() {
	let totalAmount = 0;
	let totalQty = 0;

	$('.receive-qty').each(function() {
		let id = $(this).data('uid');
		let qty = parseDefault(parseFloat(removeCommas($('#receive-qty-'+id).val())), 0);
		let price = parseDefault(parseFloat(removeCommas($('#row-price-'+id).val())), 0);
		let amount = qty * price;

		totalQty += qty;
		totalAmount += amount;
	});

	$('#total-receive').val(addCommas(totalQty.toFixed(2)));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
}



function toggleCheckAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	if($('.chk:checked').length) {
		swal({
			title:'คุณแน่ใจ ?',
			text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
			type:'warning',
			showCancelButton:true,
			confirmButtonColor:'#d15b47',
			confirmButtonText:'Yes',
			cancelButtonText:'No',
			closeOnConfirm:true
		}, function() {
			$('.chk:checked').each(function() {
				let no = $(this).val();
				$('#row-'+no).remove();
			});

			recalTotal();
			reIndex();
		})
	}
}


function confirmPo() {
	let poCode = $.trim($('#poCode').val());

	if(poCode.length) {
		if($('.receive-qty').length) {
			swal({
				title:'คุณแน่ใจ ?',
				text:'รายการปัจจุบันจะถูกแทนที่ด้วยรายการจากใบสั่งซื้อเลขที่ ' + poCode,
				type:'warning',
				showCancelButton:true,
				confirmButtonText:'Yes',
				cancelButtonText:'No',
				closeOnConfirm:true
			}, function() {
				setTimeout(() => {
					getPoDetail(poCode);
				}, 100);
			})
		}
		else
		{
			setTimeout(() => {
				getPoDetail(poCode);
			}, 100);
		}
	}
}


function getPoDetail(poCode) {

	if(poCode != "" || poCode == null || poCode == undefined) {
		poCode = $('#poCode').val();
	}

	if(poCode.length == 0) {
		swal({
			title:'Oop!',
			text:'กรุณาระบุเลขที่ใบสั่งซื้อ',
			type:'warning'
		});

		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_detail',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
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
						title:'Error!',
						text:ds.message,
						type:'error'
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
		}
	})
}


function getPoItems() {
	let po = $('#poCode').val();

	if(po.length == 0) {
		swal({
			title:'Oops !',
			text:'กรุณาระบุเลขที่ใบสั่งซื้อ',
			type:'warning'
		});

		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_detail',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
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
						title:'Error!',
						text:ds.message,
						type:'error'
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
		}
	})
}


$('#poGrid').on('shown.bs.modal', function() {
	let id = $('#uid-1').val();

	$('#po-qty-'+id).focus();
})


function receiveAll() {
	$('.po-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).data('qty')), 0);
		if(qty > 0) {
			$(this).val(addCommas(qty));
		}
	});
}


function clearAll() {
	$('.po-qty').each(function() {
		$(this).val('');
	});
}


function clearPo() {
	let poCode = $('#poCode').val();

	if(poCode.length == 0) {
		return false;
	}

	swal({
		title:'เปลียนใบสั่งซื้อ',
		text:'รายการทั้งหมดจะถูกลบ ต้องการเปลียนใบสั่งซื้อหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function() {
		load_in();
		setTimeout(() => {
			load_out();
			$('#receive-table').html('');
			$('#poCode').val('');
			$('#poCode').removeAttr('disabled');
			$('#btn-get-po').addClass('hide');
			$('#btn-confirm-po').removeClass('hide');
			$('#total-receive').val('0.00');
			$('#total-amount').val('0.00');

			setTimeout(() => {
				$('#poCode').focus();
			}, 200);
		}, 200);
	});
}
