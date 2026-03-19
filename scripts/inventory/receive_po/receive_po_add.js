window.addEventListener('load', () => {
	poInit();
	zoneInit();
});

var click = 0;
var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;

$("#doc-date").datepicker({ dateFormat: 'dd-mm-yy' });
$("#due-date").datepicker({ dateFormat: 'dd-mm-yy' });
$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy' });

function getSample() {
	window.location.href = `${HOME}get_sample_file`;
}


$('#vendor-code').autocomplete({
	source: `${BASE_URL}auto_complete/get_vendor_code_and_name`,
	autoFocus: true,
	close: function () {
		let arr = $(this).val().split(' | ');

		if (arr.length === 2) {
			let code = arr[0];
			let name = arr[1];

			$('#vendor-code').val(code);
			$('#vendor-name').val(name);
			$('#vendor').data('code', code);
			$('#vendor').data('name', name);
		}
		else {
			$('#vendor').data('code', '');
			$('#vendor').data('name', '');
		}
	}
});


$('#vendor-name').autocomplete({
	source: `${BASE_URL}auto_complete/get_vendor_code_and_name`,
	autoFocus: true,
	close: function () {
		let arr = $(this).val().split(' | ');

		if (arr.length === 2) {
			let code = arr[0];
			let name = arr[1];

			$('#vendor-code').val(code);
			$('#vendor-name').val(name);
			$('#vendor').data('code', code);
			$('#vendor').data('name', name);
		}
		else {
			$('#vendor').data('code', '');
			$('#vendor').data('name', '');
		}
	}
});


function poInit() {
	let vendor = $('#vendor').data('code');
	let vendor_code = vendor.length ? vendor : 'no_vendor';

	$('#po-code').autocomplete({
		source: `${HOME}get_po_code_and_vendor/${vendor_code}`,
		autoFocus: true,
		close: function () {
			let arr = $(this).val().split(' | ');

			if (arr.length === 3) {
				let code = arr[0];
				let cardCode = arr[1];
				let cardName = arr[2];

				$(this).val(code);

				if (code != vendor) {
					$('#vendor-code').val(cardCode);
					$('#vendor-name').val(cardName);
					$('#vendor').data('code', cardCode).data('name', cardName);
				}
			}
			else {
				$(this).val('');
			}
		}
	})
}


$('#vendor-name').focusout(function (event) {
	let vendor = $(this).val().trim();

	if (vendor.length === 0) {
		$('#vendor-code').val('');
		$('#vendor-name').val('');
		$('#vendor').data('code', '').data('name', '');
	}

	poInit();
});


$('#vendor-code').focusout(function (event) {
	let vendor = $(this).val().trim();

	if (vendor.length === 0) {
		$('#vendor-code').val('');
		$('#vendor-name').val('');
		$('#vendor').data('code', '').data('name', '');
	}

	poInit();
});


function updateVendor(po) {
	let vendor_code = $('#vendor-code').val();
	if (po.length) {
		$.ajax({
			url: `${HOME}get_vendor_by_po/${po_code}`,
			type: 'GET',
			cache: false,
			success: function (rs) {
				if (isJson(rs)) {
					let ds = JSON.parse(rs);

					if (ds.code != vendor_code) {
						$('#vendor-code').val(ds.code);
						$('#vendor-name').val(ds.name);
						$('#vendor').data('code', ds.code).data('name', ds.name);
					}
				}
			},
			error: function (rs) {
				console.error(rs);
			}
		})
	}
}


function add() {
	if (click != 0) { return false; }

	click = 1;
	clearErrorByClass('e');

	let reqRemark = $('#req-remark').val();

	let fm = {
		'date_add': $('#doc-date').val(),
		'due_date': $('#due-date').val(),
		'posting_date': $('#posting-date').val(),
		'vendor_code': $('#vendor').data('code'),
		'vendor_name': $('#vendor').data('name'),
		'po_code': $('#po-code').val().trim(),
		'invoice': $('#invoice').val().trim(),
		'warehouse_code': $('#warehouse').val(),
		'remark': $('#remark').val().trim()
	};


	if (!isDate(fm.date_add)) {
		$('#doc-date').hasError();
		swal('กรุณาระบุวันที่');
		click = 0;
		return false;
	}

	if (fm.vendor_code.length == "" || fm.vendor_name.length == "") {
		$('#vendor-code').hasError();
		$('#vendor-name').hasError();
		swal('กรุณาระบุผู้ขาย');
		click = 0;
		return false;
	}

	if (fm.warehouse_code == '') {
		$('#warehouse').hasError();
		swal('กรุณาระบุคลังสินค้า');
		click = 0;
		return false;
	}

	if (reqRemark == 1 && fm.remark.length < 5) {
		$('#remark').hasError();
		swal('กรุณาใส่หมายเหตุ');
		click = 0;
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'add',
		type: 'POST',
		cache: false,
		data: {
			'data': JSON.stringify(fm)
		},
		success: function (rs) {
			click = 0;
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status == 'success') {
					goEdit(ds.code);
				}
				else {
					swal({
						title: 'Error!',
						text: ds.message,
						type: 'error',
						html: true
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
		},
		error: function (rs) {
			showError();
			click = 0;
		}
	})
}


function save() {
	clearErrorByClass('e');

	let h = {
		'code': $('#code').val(),
		'save_type': $('#save-type').val(), //--- 0 = draft,  1 = บันทึกรับทันที , 3 = บันทึกรอรับ		
		'doc_date': $('#doc-date').val(),
		'due_date': $('#due-date').val(),
		'posting_date': $('#posting-date').val(),
		'vendor_code': $('#vendor').data('code'),
		'vendor_name': $('#vendor').data('name'),
		'po_code': $('#po-code').val().trim(),
		'invoice': $('#invoice').val().trim(),
		'warehouse_code': $('#warehouse').val(),
		'zone_code': $('#zone').data('code'),
		'approver': $('#approver').val(),
		'DocCur': $('#DocCur').val(),
		'DocRate': parseDefaultFloat($('#DocRate').val(), 1),
		'remark': $('#remark').val().trim(),
		'DiscPrcnt': parseDefaultFloat($('#disc-percent').val(), 0),
		'DiscAmount': parseDefaultFloat(removeCommas($('#disc-amount').val()), 0),
		'VatSum': parseDefaultFloat(removeCommas($('#vat-sum').val()), 0),
		'DocTotal': parseDefaultFloat(removeCommas($('#doc-total').val()), 0),
		'TotalQty': parseDefaultFloat(removeCommas($('#total-qty').val()), 0),
		'rows': []
	};

	let dataWhsCode = $('#zone').data('warehouse');

	if (!isDate(h.doc_date)) {
		$('#doc-date').hasError();
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}

	if (h.vendor_code == '' || h.vendor_name == '') {
		$('#vendor-code').hasError();
		$('#vendor-name').hasError();
		swal('กรุณาระบุผู้จำหน่าย');
		return false;
	}

	//--- ใบสั่งซื้อถูกต้องหรือไม่
	if (h.po_code == '') {
		$('#po-code').hasError();
		swal('กรุณาระบุใบสั่งซื้อ');
		return false;
	}

	//--- ตรวจสอบใบส่งของ (ต้องระบุ)
	if (h.invoice.length == 0) {
		$('#invoice').hasError();
		swal('กรุณาระบุใบส่งสินค้า');
		return false;
	}

	if (h.warehouse_code == "") {
		$('#warehouse').hasError();
		swal('กรุณาระบุคลัง');
		return false;
	}

	//--- ตรวจสอบโซนรับเข้า
	if (h.zone_code == '') {
		swal('กรุณาระบุโซนเพื่อรับเข้า');
		return false;
	}

	if (h.warehouse_code != dataWhsCode) {
		$('#zone-code').hasError();
		swal('โซนไม่ตรงกับคลัง');
		return false;
	}

	if (h.DocRate <= 0) {
		$('#DocRate').hasError();
		swal('กรุณาระบุอัตราแลกเปลี่ยน');
		return false;
	}

	//--- มีรายการในใบสั่งซื้อหรือไม่
	if ($(".receive-qty").length == 0) {
		showError('ไม่พบรายการรับเข้า');
		return false;
	}

	$('.receive-qty').each(function () {
		let el = $(this);
		let qty = parseDefaultFloat(removeCommas(el.val()), 0);

		if (qty > 0) {
			let DiscPrcnt = parseDefaultFloat(el.data('discprcnt'), 0);
			let PriceBefDi = parseDefaultFloat(el.data('bprice'), 0);
			let PriceAfDisc = parseDefaultFloat(el.data('price'), 0);
			let DiscAmount = PriceBefDi * (DiscPrcnt * 0.01);
			let PriceAfVAT = parseDefaultFloat(el.data('aprice'), 0);
			let vatRate = parseDefaultFloat(el.data('vatrate'), 0) * 0.01;
			let LineTotal = qty * PriceAfDisc; //-- Line total before vat
			let vatAmount = LineTotal * vatRate;
			let amount = LineTotal + vatAmount; //-- Line total after vat

			let row = {
				'baseEntry': el.data('baseentry'),
				'baseLine': el.data('baseline'),
				'product_code': el.data('code'),
				'product_name': el.data('name'),
				'PriceBefDi': PriceBefDi,
				'DiscPrcnt': DiscPrcnt,
				'DiscAmount': DiscAmount,
				'PriceAfDisc': PriceAfDisc,
				'LineTotal': LineTotal,
				'price': PriceAfVAT,
				'qty': qty,
				'backlogs': el.data('backlogs'),
				'currency': h.DocCur,
				'rate': h.DocRate,
				'vatGroup': el.data('vatcode'),
				'vatRate': el.data('vatrate'),
				'vatAmount': vatAmount,
				'amount': amount,
				'UomCode': el.data('uomcode'),
				'unitMsr': el.data('unitmsr')
			};

			h.rows.push(row);
		}
	});

	if (h.rows.length < 1) {
		swal('ไม่พบรายการรับเข้า');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'save',
		type: "POST",
		cache: "false",
		data: {
			"data": JSON.stringify(h)
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);
				if (ds.status == 'success') {
					swal({
						title: 'Success',
						text: 'บันทึกรายการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					setTimeout(function () {
						viewDetail(h.code);
					}, 1200);
				}
				else if (ds.status == 'warning') {
					swal({
						title: 'Warning',
						text: ds.message,
						type: 'warning',
						html: true
					}, () => {
						viewDetail(h.code);
					});
				}
				else {
					swal({
						title: 'Error!',
						text: ds.message,
						type: 'error',
						html: true
					});
				}
			}
			else {
				swal({
					title: 'Error!',
					text: ds.message,
					type: 'error',
					html: true
				});
			}
		},
		error: function (rs) {
			showError(rs);
		}
	});
}


function finish(h) {
	if (h !== null && h !== undefined) {
		load_in();

		setTimeout(() => {
			$.ajax({
				url: HOME + 'finish_receive',
				type: 'POST',
				cache: false,
				data: {
					'data': JSON.stringify(h)
				},
				success: function (rs) {
					load_out();
					if (isJson(rs)) {
						let ds = JSON.parse(rs);

						if (ds.status === 'success') {
							swal({
								title: 'Success',
								text: 'บันทึกรายการเรียบร้อยแล้ว',
								type: 'success',
								timer: 1000
							});

							setTimeout(function () {
								viewDetail(h.code);
							}, 1200);
						}
						else if (ds.status === 'warning') {
							swal({
								title: 'Warning',
								text: ds.message,
								type: 'warning',
								html: true
							}, () => {
								viewDetail(h.code);
							});
						}
						else {
							showError(ds.message);
						}
					}
					else {
						showError(rs);
					}
				},
				error: function (rs) {
					showError(rs);
				}
			})
		}, 100);

	}
	else {
		beep();
		showError('No data found');
	}
}


function validateReceive() {
	clearErrorByClass('receive-qty');
	let code = $('#code').val();
	let totalRequest = parseDefaultFloat($('#req-qty').val(), 0);
	let totalQty = parseDefault(removeCommas($('#total-qty').val()), 0);
	let discSum = parseDefaultFloat(removeCommas($('#disc-amount').val()), 0);
	let vatSum = parseDefaultFloat(removeCommas($('#vat-sum').val()), 0);
	let docTotal = parseDefaultFloat(removeCommas($('#doc-total').val()), 0);
	let totalReceive = 0;
	let err = 0;

	let h = {
		'code': code,
		'totalQty': totalQty,
		'discSum': discSum,
		'vatSum': vatSum,
		'docTotal': docTotal,
		'rows': []
	}

	$('.receive-qty').each(function () {
		let el = $(this);
		let uid = el.data('uid');
		let qty = parseDefaultFloat(el.val(), 0);
		let limit = parseDefault(parseFloat(el.data('limit')), 0);

		if (qty > 0) {
			if (qty > limit) {
				el.hasError();
				err++;
			}
			else {
				h.rows.push({
					'id': el.data('id'),
					'product_code': el.data('code'),
					'product_name': el.data('name'),
					'baseEntry': el.data('baseentry'),
					'baseLine': el.data('baseline'),
					'receive_qty': qty,
					'vatAmount': el.data('vatamount'),
					'lineTotal': parseDefaultFloat(removeCommas($(`#line-total-${uid}`).val()), 0)
				});

				totalReceive += qty;
			}
		}
	});

	if (err > 0) {
		beep();
		swal('จำนวนรับไม่ถูกต้อง');
		return false;
	}

	if (totalReceive < totalRequest) {
		swal({
			title: 'สินค้าไม่ครบ',
			text: 'จำนวนที่รับไม่ครบตามจำนวนที่ส่ง คุณต้องการบันทึกรับเพื่อปิดจบหรือไม่ ?',
			type: 'warning',
			html: true,
			showCancelButton: true,
			cancelButtonText: 'ยกเลิก',
			confirmButtonText: 'ยืนยัน',
			closeOnConfirm: true
		}, function () {
			return finish(h);
		})
	}
	else {
		return finish(h);
	}
}


function checkLimit(option) {
	clearErrorByClass('receive-qty');
	let allow = $('#allow-over-po').val() == '1' ? true : false;
	let over = 0;
	$('#save-type').val(option);

	$(".receive-qty").each(function () {
		let el = $(this);
		let limit = parseDefaultFloat(el.data('limit'), 0);
		let qty = parseDefaultFloat(removeCommas(el.val()), 0);

		if (limit > 0 && qty > 0) {
			if (qty > limit) {
				over++;

				if (!allow) {
					el.hasError();
				}
			}
		}
	});

	if (over > 0) {
		if (!allow) {
			swal({
				title: 'สินค้าเกิน',
				text: 'กรุณาระบุจำนวนรับไม่เกินยอดค้างร้บ',
				type: 'error'
			});

			return false;
		}
		else {
			getApprove();
		}
	}
	else {
		save();
	}
}


$("#sKey").keyup(function (e) {
	if (e.keyCode == 13) {
		doApprove();
	}
});


function getApprove() {
	$("#approveModal").modal("show");
}


$("#approveModal").on('shown.bs.modal', function () { $("#sKey").focus(); });


function validate_credentials() {
	var s_key = $("#s_key").val();
	var menu = $("#validateTab").val();
	var field = $("#validateField").val();
	if (s_key.length != 0) {
		$.ajax({
			url: BASE_URL + 'users/validate_credentials/get_permission',
			type: "GET",
			cache: "false",
			data: {
				"menu": menu,
				"s_key": s_key,
				"field": field
			},
			success: function (rs) {
				var rs = $.trim(rs);
				if (isJson(rs)) {
					var data = $.parseJSON(rs);
					$("#approverName").val(data.approver);
					closeValidateBox();
					callback();
					return true;
				} else {
					showValidateError(rs);
					return false;
				}
			}
		});
	} else {
		showValidateError('Please enter your secure code');
	}
}


function doApprove(option) {
	var s_key = $("#sKey").val();
	var menu = 'ICPURC'; //-- อนุมัติรับสินค้าเกินใบสั่งซื้อ
	var field = 'approve';

	if (s_key.length > 0) {
		$.ajax({
			url: BASE_URL + 'users/validate_credentials/get_permission',
			type: "GET",
			cache: "false",
			data: {
				"menu": menu,
				"s_key": s_key,
				"field": field
			},
			success: function (rs) {
				var rs = $.trim(rs);
				if (isJson(rs)) {
					var data = $.parseJSON(rs);
					$("#approver").val(data.approver);
					$("#approveModal").modal('hide');
					save();
				} else {
					$('#approvError').text(rs);
					return false;
				}
			}
		});
	}
}


function leave() {
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function () {
		goBack();
	});

}


function changeRate() {
	if ($('#DocCur').val() == 'THB') {
		$('#DocRate').val('1.00');
	}
	else {
		$('#DocRate').val("");
	}

}


function changePo() {
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function () {
		$("#receiveTable").html('');
		$('#btn-change-po').attr('disabled', 'disabled').addClass('hide');
		$('#btn-get-po').removeAttr('disabled', 'disabled').removeClass('hide');
		$('#po-code').val('');
		$('#po-code').removeAttr('disabled');
		$('#requestCode').val('');
		$('#requestCode').removeAttr('disabled');
		$('#btn-change-request').addClass('hide');
		$('#btn-get-request').removeClass('hide');
		$('#DocCur').val('THB');
		$('#DocRate').val('1.00');

		swal({
			title: 'Success',
			text: 'ยกเลิกข้อมูลเรียบร้อยแล้ว',
			type: 'success',
			timer: 1000
		});
		setTimeout(function () {
			$('#po-code').focus();
		}, 1200);
	});
}


function getPoCurrency(poCode) {
	$.ajax({
		url: HOME + 'get_po_currency',
		type: 'GET',
		cache: false,
		data: {
			'po_code': poCode
		},
		success: function (rs) {
			if (isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#DocCur').val(ds.DocCur);
				$('#DocRate').val(ds.DocRate);

				if (ds.DocCur == 'THB') {
					$('#DocRate').val(1.00);
				}
			}
		}
	})
}

//-------------------------- not use
function getData() {
	var po = $("#po-code").val();

	if (po.length < 5) {
		return false;
	}

	getPoCurrency(po);

	load_in();
	$.ajax({
		url: HOME + 'get_po_detail',
		type: "GET",
		cache: "false",
		data: {
			"po_code": po
		},
		success: function (rs) {
			load_out();
			var rs = $.trim(rs);
			if (isJson(rs)) {
				data = $.parseJSON(rs);
				var source = $("#template").html();
				var output = $("#receiveTable");
				render(source, data, output);
				$("#po-code").attr('disabled', 'disabled');
				$(".receive-box").keyup(function (e) {
					sumReceive();
				});

				update_vender(po);

				$('#btn-get-po').attr('disabled', 'disabled').addClass('hide');
				$('#btn-change-po').removeAttr('disabled').removeClass('hide');

				setTimeout(function () {
					$('#invoice').focus();
				}, 1000);

			} else {
				swal("ข้อผิดพลาด !", rs, "error");
				$("#receiveTable").html('');
			}
		}
	});
}


function zoneInit() {
	let whsCode = $('#warehouse').val();
	let dataWhsCode = $('#zone').data('warehouse');

	if (whsCode != dataWhsCode) {
		$('#zone-code').val('').data('warehouse', '');
		$('#zone-name').val('');
		$('#zone').data('code', '').data('name', '').data('warehouse', '');
	}

	$("#zone-name").autocomplete({
		source: `${BASE_URL}auto_complete/get_zone_code_and_name/${whsCode}`,
		autoFocus: true,
		close: function () {
			let arr = $(this).val().trim().split(' | ');

			if (arr.length == 2) {
				let code = arr[0];
				let name = arr[1];

				$('#zone-code').val(code).data('warehouse', whsCode);
				$('#zone-name').val(name);
				$('#zone').data('code', code).data('name', name).data('warehouse', whsCode);
			}
			else {
				$('#zone-code').val('').data('warehouse', '');
				$('#zone-name').val('');
				$('#zone').data('code', '').data('name', '').data('warehouse', '');
			}
		}
	});


	$("#zone-code").autocomplete({
		source: `${BASE_URL}auto_complete/get_zone_code_and_name/${whsCode}`,
		autoFocus: true,
		close: function () {
			let arr = $(this).val().trim().split(' | ');

			if (arr.length == 2) {
				let code = arr[0];
				let name = arr[1];

				$('#zone-code').val(code).data('warehouse', whsCode);
				$('#zone-name').val(name);
				$('#zone').data('code', code).data('name', name).data('warehouse', whsCode);
			}
			else {
				$('#zone-code').val('').data('warehouse', '');
				$('#zone-name').val('');
				$('#zone').data('code', '').data('name', '').data('warehouse', '');
			}
		}
	});
}


function checkBarcode() {
	let barcode = $('#barcode').val().trim();
	if (barcode.length) {
		let qty = parseDefaultFloat($('#qty').val(), 1);
		let valid = 0;

		if ($('.' + barcode).length) {

			$('#barcode').attr('disabled', 'disabled');

			$('.' + barcode).each(function () {
				if (valid == 0 && qty > 0) {
					let uid = $(this).val();
					let limit = parseDefaultFloat($(this).data('limit'), 0);
					let inputQty = parseDefaultFloat($(`#receive-qty-${uid}`).val(), 0);
					let diff = limit - inputQty;

					if (diff > 0) {
						let receiveQty = qty >= diff ? diff : qty;
						let newQty = inputQty + receiveQty;
						$(`#receive-qty-${uid}`).val(newQty);
						qty -= receiveQty;
					}

					if (qty == 0) {
						valid = 1;
					}

					recalAmount(uid);
				}
			});

			if (qty > 0) {
				beep();
				swal({
					title: "ข้อผิดพลาด !",
					text: "สินค้าเกิน " + qty + " Pcs.",
					type: "error"
				}, function () {
					setTimeout(function () {
						$("#barcode").focus();
					}, 1000);
				});
			}

			$('#qty').val(1);
			$('#barcode').removeAttr('disabled').val('').focus();
		}
		else {
			$('#barcode').val('');
			$('#barcode').removeAttr('disabled');
			beep();
			swal({
				title: "ข้อผิดพลาด !",
				text: "บาร์โค้ดไม่ถูกต้องหรือสินค้าไม่ตรงกับใบสั่งซื้อ",
				type: "error"
			}, function () {
				setTimeout(function () {
					$("#barcode").focus();
				}, 1000);
			});
		}
	}
}


$("#barcode").keyup(function (e) {
	if (e.keyCode == 13) {
		checkBarcode();
	}
});


function getUploadFile() {
	$('#upload-modal').modal('show');
}


function getFile() {
	$('#uploadFile').click();
}


$("#uploadFile").change(function () {
	if ($(this).val() != '') {
		let file = this.files[0];
		let name = file.name;
		let type = file.type;
		let size = file.size;

		if (size > 5000000) {
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		$('#show-file-name').val(name);
	}
});


function uploadfile() {
	$('#upload-modal').modal('hide');

	let file = $("#uploadFile")[0].files[0];
	let fd = new FormData();
	fd.append('uploadFile', $('input[type=file]')[0].files[0]);
	if (file !== '') {
		load_in();
		$.ajax({
			url: `${HOME}import_data`,
			type: "POST",
			cache: "false",
			data: fd,
			processData: false,
			contentType: false,
			success: function (rs) {
				load_out();
				if (isJson(rs)) {
					data = $.parseJSON(rs);

					$('#vendor-code').val(data.vendor_code);
					$('#vendor-name').val(data.vendor_name);
					$('#vendor').data('code', data.vendor_code).data('name', data.vendor_name);
					$('#po-code').val(data.po_code);
					$('#invoice').val(data.invoice_code);
					$('#po-code').attr('disabled', 'disabled');
					$('#DocCur').val(data.DocCur);
					$('#DocRate').val(data.DocRate);
					$('#disc-percent').val(data.DiscPrcnt);

					let ds = data.details;
					let source = $("#receive-template").html();
					let output = $("#receive-table");
					render(source, ds, output);

					recalTotal();
				}
				else {
					showError(rs);
					$("#receive-table").html('');
					recalTotal();
				}
			}
		});
	}
}


function accept() {
	$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
	$('#accept-modal').modal('show');
}


function acceptConfirm(save_type) {
	let code = $('#code').val();
	let note = $.trim($('#accept-note').val());

	if (note.length < 10) {
		$('#accept-error').text('กรุณาระบุหมายเหตุอย่างนี้อย 10 ตัวอักษร');
		return false;
	}
	else {
		$('#accept-error').text('');
	}

	$('#accept-modal').modal('hide');

	load_in();

	$.ajax({
		url: HOME + 'accept_confirm',
		type: 'POST',
		cache: false,
		data: {
			"code": code,
			"save_type": save_type,
			"accept_remark": note
		},
		success: function (rs) {
			load_out();
			if (isJson(rs)) {
				let ds = JSON.parse(rs);
				if (ds.status === 'success') {
					swal({
						title: 'Success',
						type: 'success',
						timer: 1000
					});

					setTimeout(() => {
						window.location.reload();
					}, 1200);
				}
				else if (ds.status === 'warning') {

					swal({
						title: 'Warning',
						text: ds.message,
						type: 'warning',
						html: true
					}, () => {
						window.location.reload();
					});
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
		}
	});

}
