window.addEventListener('load', () => {
	batchInit();
});

function confirmChangePo() {
	let poCode = $('#po-code').val().trim();
	let prevCode = $('#po-code').data('prev');
	let count = 0;

	$('.receive-qty').each(function() {
		if($(this).data('po') == prevCode) {
			count++;
		}
	});

	if(poCode != "" && poCode != prevCode && count > 0) {
		swal({
			title:'คุณแต่ใจ',
			text:'รายการปัจจุบันจะถูกลบออก ต้องการดำเนินการต่อหรือไม่ ?',
			type:'warning',
			showCancelButton:true,
			confirmButtonText:'Yes',
			cancelButtonText:'No',
			closeOnConfirm:true
		}, function(isConfirm) {
			if(isConfirm) {
				$('#po-code').data('prev', poCode);
				$('.rows').remove();
				recalTotal();
			}
			else {
				$('#po-code').val(prevCode);
			}
		});
	}
	else {
		$('#po-code').attr('disabled', 'disabled');
		$('#confirm-btn').addClass('hide');
		$('#get-po-btn').removeClass('hide');
	}
}


function getPoDetails() {
	let poCode = $('#po-code').val().trim();

	if(poCode.length == 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_details',
		type:'POST',
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
					$('#vendor-code').val(ds.CardCode);
					$('#vendor-name').val(ds.CardName);

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

					let price = parseDefault(parseFloat(el.data('price')), 0.00); //--- price Af discount
					let limit = parseDefault(parseFloat(el.data('limit')), 0.00);
					let backlogs = parseDefault(parseFloat(el.data('backlogs')), 0);
					let amount = roundNumber(qty * price, 2);
					let vatRate = parseDefault(parseFloat(el.data('vatrate')), 7);
					let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

					let item = {
						'uid' : no,
						'pdCode' : el.data('code'),
						'pdName' : el.data('name'),
						'baseCode' : el.data('basecode'),
						'baseEntry' : el.data('baseentry'),
						'baseLine' : el.data('baseline'),
						'vatCode' : el.data('vatcode'),
						'vatRate' : vatRate,
						'price' : price,
						'priceLabel' : addCommas(price.toFixed(2)),
						'PriceBefDi' : el.data('bfprice'),
						'PriceAfVAT' : el.data('afprice'),
						'qty' : qty,
						'qtyLabel' : addCommas(qty.toFixed(2)),
						'backlogs' : backlogs,
						'backLogsLabel' : addCommas(backlogs.toFixed(2)),
						'limit' : limit,
						'amount' : amount,
						'amountLabel' : addCommas(amount.toFixed(2)),
						'uomCode' : el.data('uom'),
						'uomCode2' : el.data('uom2'),
						'uomEntry' : el.data('uomentry'),
						'uomEntry2' : el.data('uomentry2'),
						'unitMsr' : el.data('unitmsr'),
						'unitMsr2' : el.data('unitmsr2'),
						'numPerMsr' : el.data('numpermsr2'),
						'numPerMsr2' : el.data('numpermsr2'),
						"has_batch" : el.data('batch'),
						"hasBatch" : el.data('batch') == 'Y' ? true : false
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


function clearPo() {
	if($('.rows').length) {
		swal({
			title:'คุณแน่ใจ ?',
			text:'รายการทั้งหมดจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
			type:'warning',
			html:true,
			showCancelButton:true,
			confirmButtonText:'Yes',
			cancelButtonText:'No',
			closeOnConfirm:true
		}, function() {
			$('.rows').remove();
			$('.batch-rows').remove();
			$('#po-code').val('').removeAttr('disabled');
			$('#total-receive').val('0.00');
			$('#total-amount').val('0.00');

			setTimeout(() => {
				$('#po-code').focus();
				poInit();
			}, 100)
		})
	}
	else {
		$('#po-code').val('').removeAttr('disabled');
		setTimeout(() => {
			$('#po-code').focus();
			poInit();
		}, 100)
	}
}


function addBatchRow(uid) {
	let el = $('#receive-qty-'+uid);
	let no = parseDefaultInt(el.data('no'), 0);
	let ne = no + 1;
	el.data('no', ne);

	let puid = no + '-' + el.data('uid');
	let cuid = ne + '-' + el.data('uid');

	$('.child-of-'+uid).each(function() {
		puid = $(this).data('id');
	});

	let ds = {
		'cuid' : cuid,
		'uid' : uid,
		'no' : ne,
		'unitMsr' : el.data('unitmsr')
	};


	let source = $('#batch-row-template').html();
	let output = $('#batch-row-'+puid).length ? $('#batch-row-'+puid) : $('#row-'+uid);

	render_after(source, ds, output);
	batchInit(cuid);
	reIndex('ne');
	setTimeout(() => {
		$('#batch-'+cuid).focus();
	}, 100)
}


function batchInit(cid) {
	if(cid == "" || cid == undefined) {
		$('.batch-row').keyup(function(e) {
			if(e.keyCode === 13) {
				if($(this).val().trim() != "") {
					let uid = $(this).data('uid');
					$('#batch-attr1-'+uid).focus();
				}
			}
		});

		$('.batch-attr1').keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-attr2-'+uid).focus();
			}
		});

		$('.batch-attr2').keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-qty-'+uid).focus();
			}
		});

		$('.batch-qty').keyup(function(e) {
			if(e.keyCode === 13) {
				let qty = parseDefaultFloat($(this).val(), 0);

				if(qty > 0) {
					let uid = $(this).data('parent');
					addBatchRow(uid);
				}
			}
		})
	}
	else {
		$('#batch-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				if($(this).val().trim() != "") {
					let uid = $(this).data('uid');
					$('#batch-attr1-'+uid).focus();
				}
			}
		});

		$('#batch-attr1-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-attr2-'+uid).focus();
			}
		});

		$('#batch-attr2-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-qty-'+uid).focus();
			}
		});

		$('#batch-qty-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				let qty = parseDefaultFloat($(this).val(), 0);

				if(qty > 0) {
					let uid = $(this).data('parent');
					addBatchRow(uid);
				}
			}
		})
	}
}


function removeBatchRow(cid) {
	$('#batch-row-'+cid).remove();
	reIndex('ne');
}


function removeRow(uid) {
	$('#row-'+uid).remove();
	$('.child-of-'+uid).remove();

	recalTotal();
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
