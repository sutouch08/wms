window.addEventListener('load', () => {
	poInit();
	zone_init();
});

var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;

$("#doc-date").datepicker({ dateFormat: 'dd-mm-yy'});
$("#due-date").datepicker({ dateFormat: 'dd-mm-yy'});
$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy'});

function getSample(){
	window.location.href = HOME + 'get_sample_file';
}


function editHeader(){
	$('.h').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader() {
	$('.h').removeClass('has-error');

	let code = $('#receive_code').val();
	let date_add = $('#doc-date').val();
	let due_date = $('#due-date').val();
	let posting_date = $('#posting-date').val();
	let is_wms = $('#is_wms').val();
	let remark = $('#remark').val();

	if( ! isDate(date_add)) {
		$('#doc-date').addClass('has-error');
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}

	if( ! isDate(due_date)) {
		$('#due-date').addClass('has-error');
		swal("กรุณาระบุวันที่สินค้าเข้า");
		return false;
	}

	if( is_wms === "") {
		$('#is_wms').addClass('has-error');
		swal("กรุณาเลือกช่องทางการรับ");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update_header',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'date_add' : date_add,
			'due_date' : due_date,
			'posting_date' : posting_date,
			'remark' : remark,
			'is_wms' : is_wms
		},
		success:function(rs) {
			load_out();
			if(rs === 'success'){
				swal({
					title:'Updated',
					text:'Update successfully',
					type:'success',
					timer:1000
				});

				$('.h').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');
			}else{
				swal({
					title:'Error!',
					text: rs,
					type:'error',
					html:true
				});
			}
		}
	})
}


function save() {

	clearErrorByClass('h');

	let  h = {
		'code' : $('#receive_code').val(),
		'save_type' : $('#save-type').val(), //--- 0 = draft,  1 = บันทึกรับทันที , 3 = บันทึกรอรับ
		'is_wms' : $('#is_wms').val(),
		'doc_date' : $('#doc-date').val(),
		'due_date' : $('#due-date').val(),
		'posting_date' : $('#posting-date').val(),
		'vendor_code' : $('#vendor_code').val(),
		'vendor_name' : $('#vendorName').val(),
		'po_code' : $('#poCode').val().trim(),
		'invoice' : $('#invoice').val().trim(),
		'warehouse_code' : $('#warehouse').val(),
		'zone_code' : $('#zone_code').val(),
		'approver' : $('#approver').val(),
		'DocCur' : $('#DocCur').val(),
		'DocRate' : parseDefault(parseFloat($('#DocRate').val()), 1),
		'remark' : $('#remark').val().trim(),
		'rows' : []
	};

	if(h.is_wms == "") {
		$('#is_wms').hasError();
		swal('กรุณาเลือกช่องทางการรับ');
		return false;
	}

	if( ! isDate(h.doc_date)) {
		$('#doc-date').hasError();
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}

	if( ! isDate(h.due_date)) {
		$('#due-date').hasError();
		swal("กรุณาระบุวันที่สินค้าเข้า");
		return false;
	}

	if(h.vendor_code == '' || h.vendor_name == '') {
		$('#vendor_code').hasError();
		$('#vendorName').hasError();
		swal('กรุณาระบุผู้จำหน่าย');
		return false;
	}

	//--- ใบสั่งซื้อถูกต้องหรือไม่
	if(h.po_code == '') {
		$('#poCode').hasError();
		swal('กรุณาระบุใบสั่งซื้อ');
		return false;
	}

	//--- ตรวจสอบใบส่งของ (ต้องระบุ)
	if(h.invoice.length == 0) {
		$('#invoice').hasError();
		swal('กรุณาระบุใบส่งสินค้า');
		return false;
	}

	if(h.warehose_code == "") {
		$('#warehouse').hasError();
		swal('กรุณาระบุคลัง');
		return false;
	}

	//--- ตรวจสอบโซนรับเข้า
	if(h.zone_code == '' || h.zoneName == '') {
		swal('กรุณาระบุโซนเพื่อรับเข้า');
		return false;
	}

	if(h.DocRate <= 0) {
		$('#DocRate').hasError();
		swal('กรุณาระบุอัตราแลกเปลี่ยน');
		return false;
	}

	//--- มีรายการในใบสั่งซื้อหรือไม่
	if($(".receive-qty").length = 0) {
		showError('ไม่พบรายการรับเข้า');
		return false;
	}

	$('.receive-qty').each(function() {
		let el = $(this);
		let qty = parseDefault(parseFloat(el.val()), 0);

		if(qty > 0) {
			uid = el.data('uid');

			let row = {
				'baseEntry' : el.data('baseentry'),
				'baseLine' : el.data('baseline'),
				'product_code' : el.data('code'),
				'product_name' : el.data('name'),
				'qty' : qty,
				'price' : el.data('price'),
				'backlogs' : el.data('backlogs'),
				'currency' : el.data('currency'),
				'rate' : h.DocRate,
				'vatGroup' : el.data('vatcode'),
				'vatRate' : el.data('vatrate')
			}

			h.rows.push(row);
		}
	});

	if(h.rows.length < 1) {
		swal('ไม่พบรายการรับเข้า');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'save',
		type:"POST",
		cache:"false",
		data: {
			"data" : JSON.stringify(h)
		},
		success: function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);
				if(ds.status == 'success') {
					swal({
						title:'Success',
						text:'บันทึกรายการเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						viewDetail(h.code);
					}, 1200);
				}
				else if(ds.status == 'warning') {
					swal({
						title:'Warning',
						text: ds.message,
						type:'warning',
						html:true
					}, () => {
						viewDetail(h.code);
					});
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
					title:'Error!',
					text:ds.message,
					type:'error',
					html:true
				});
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
}


function finish(h) {
	if(h !== null && h !== undefined) {
		load_in();
		setTimeout(() => {
			$.ajax({
				url:HOME + 'finish_receive',
				type:'POST',
				cache:false,
				data:{
					'data' : JSON.stringify(h)
				},
				success:function(rs) {
					load_out();
					if(isJson(rs)) {
						let ds = JSON.parse(rs);

						if(ds.status === 'success') {
							swal({
								title:'Success',
								text:'บันทึกรายการเรียบร้อยแล้ว',
								type:'success',
								timer:1000
							});

							setTimeout(function() {
								viewDetail(h.code);
							}, 1200);
						}
						else if(ds.status === 'warning') {
							swal({
								title:'Warning',
								text: ds.message,
								type:'warning',
								html:true
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
				error:function(rs) {
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

	let code = $('#receive_code').val();
	let totalQty = parseDefault(parseFloat(removeCommas($('#total-qty').val())), 0);
	let totalReceive = 0;
	let err = 0;

	let h = {
		'code' : $('#receive_code').val(),
		'rows' : []
	}

	$('.receive-qty').each(function() {
		let el = $(this);
		let qty = parseDefault(parseFloat(el.val()), 0);
		let limit = parseDefault(parseFloat(el.data('limit')), 0);

		if(qty > 0) {
			if(qty > limit) {
				el.hasError();
				err++;
			}
			else {
				h.rows.push({
					'id' : el.data('id'),
					'product_code' : el.data('code'),
					'product_name' : el.data('name'),
					'baseEntry' : el.data('baseentry'),
					'baseLine' : el.data('baseline'),
					'backlogs' : limit,
					'receive_qty' : qty
				});

				totalReceive += qty;
			}
		}
	});

	if(err > 0) {
		beep();
		swal('จำนวนรับไม่ถูกต้อง');
		return false;
	}

	if(totalReceive < totalQty) {
		swal({
			title:'สินค้าไม่ครบ',
			text:'จำนวนที่รับไม่ครบตามจำนวนที่ส่ง คุณต้องการบันทึกรับเพื่อปิดจบหรือไม่ ?',
			type:'warning',
			html:true,
			showCancelButton:true,
			cancelButtonText:'ยกเลิก',
			confirmButtonText:'ยืนยัน',
			closeOnConfirm:true
		}, function() {
			return finish(h);
		})
	}
	else {
		return finish(h);
	}
}


function checkLimit(option) {
	clearErrorByClass('receive-qty');
	var allow = $('#allow_over_po').val() == '1' ? true : false;
	var over = 0;

	$('#save-type').val(option);

	$(".receive-qty").each(function() {
		let el = $(this);
		let uid = el.data('uid');
		let limit = parseDefault(parseFloat(el.data('limit')), 0);
		let qty = parseDefault(parseFloat(el.val()), 0);

		if(limit > 0 && qty > 0) {
			if(qty > limit) {
				over++;

				if( ! allow) {
					$(this).hasError();
				}
			}
		}
	});

	if( over > 0)
	{
		if( ! allow) {
			swal({
				title:'สินค้าเกิน',
				text: 'กรุณาระบุจำนวนรับไม่เกินยอดค้างร้บ',
				type:'error'
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


$("#sKey").keyup(function(e) {
    if( e.keyCode == 13 ){
		doApprove();
	}
});


function getApprove(){
	$("#approveModal").modal("show");
}


$("#approveModal").on('shown.bs.modal', function(){ $("#sKey").focus(); });


function validate_credentials(){
	var s_key = $("#s_key").val();
	var menu 	= $("#validateTab").val();
	var field = $("#validateField").val();
	if( s_key.length != 0 ){
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approverName").val(data.approver);
					closeValidateBox();
					callback();
					return true;
				}else{
					showValidateError(rs);
					return false;
				}
			}
		});
	}else{
		showValidateError('Please enter your secure code');
	}
}


function doApprove(option){
	var s_key = $("#sKey").val();
	var menu = 'ICPURC'; //-- อนุมัติรับสินค้าเกินใบสั่งซื้อ
	var field = 'approve';

	if( s_key.length > 0 )
	{
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approver").val(data.approver);
					$("#approveModal").modal('hide');
					save();
				}else{
					$('#approvError').text(rs);
					return false;
				}
			}
		});
	}
}


function leave(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});

}


function changeRate() {
	if($('#DocCur').val() == 'THB') {
		$('#DocRate').val('1.00');
	}
	else {
		$('#DocRate').val("");
	}

}


function changePo(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		$("#receiveTable").html('');
		$('#btn-change-po').attr('disabled', 'disabled').addClass('hide');
		$('#btn-get-po').removeAttr('disabled', 'disabled').removeClass('hide');
		$('#poCode').val('');
		$('#poCode').removeAttr('disabled');
		$('#requestCode').val('');
		$('#requestCode').removeAttr('disabled');
		$('#btn-change-request').addClass('hide');
		$('#btn-get-request').removeClass('hide');
		$('#DocCur').val('THB');
		$('#DocRate').val('1.00');

		swal({
			title:'Success',
			text:'ยกเลิกข้อมูลเรียบร้อยแล้ว',
			type:'success',
			timer:1000
		});
		setTimeout(function(){
			$('#poCode').focus();
		}, 1200);
	});
}


function getPoCurrency(poCode) {
	$.ajax({
		url:HOME + 'get_po_currency',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#DocCur').val(ds.DocCur);
				$('#DocRate').val(ds.DocRate);

				if(ds.DocCur == 'THB') {
					$('#DocRate').val(1.00);
				}
			}
		}
	})
}


function getData() {
	var po = $("#poCode").val();

	if(po.length < 5) {
		return false;
	}

	getPoCurrency(po);

	load_in();
	$.ajax({
		url: HOME + 'get_po_detail',
		type:"GET",
		cache:"false",
		data:{
			"po_code" : po
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) ){
				data = $.parseJSON(rs);
				var source = $("#template").html();
				var output = $("#receiveTable");
				render(source, data, output);
				$("#poCode").attr('disabled', 'disabled');
				$(".receive-box").keyup(function(e){
    				sumReceive();
				});

				update_vender(po);

				$('#btn-get-po').attr('disabled', 'disabled').addClass('hide');
				$('#btn-change-po').removeAttr('disabled').removeClass('hide');

				setTimeout(function(){
					$('#invoice').focus();
				},1000);

			}else{
				swal("ข้อผิดพลาด !", rs, "error");
				$("#receiveTable").html('');
			}
		}
	});
}


$("#vendorName").autocomplete({
	source: BASE_URL + 'auto_complete/get_vendor_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$("#vendor_code").val(arr[0]);
			$('#poCode').focus();
		}else{
			$(this).val('');
			$("#vendor_code").val('');
		}
	}
});


$("#vendor_code").autocomplete({
	source: BASE_URL + 'auto_complete/get_vendor_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ) {
			$('#vendor_code').val(arr[0]);
			$("#vendorName").val(arr[1]);
			$('#poCode').focus();
		}else{
			$('#vendorName').val('');
			$("#vendor_code").val('');
		}
	}
});


$('#vendorName').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendor_code').val('');
	}
	poInit();
});


$('#vendor_code').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendorName').val('');
	}
	poInit();
});


function poInit() {
	var vendor_code = $('#vendor_code').val();
	if(vendor_code == ''){
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code',
			autoFocus: true,
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[1]);
				}
				else {
					$(this).val('');
				}
			}
		});
	}else{
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code/'+vendor_code,
			autoFocus: true,
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[1]);
				}
				else {
					$(this).val('');
				}
			}
		});
	}
}


function update_vender(po_code){
	$.ajax({
		url: BASE_URL + 'inventory/receive_po/get_vender_by_po/'+po_code,
		type:'GET',
		cache:false,
		success:function(rs){
			if(isJson(rs)){
				var ds = $.parseJSON(rs);
				$('#vendor_code').val(ds.code);
				$('#vendorName').val(ds.name);
			}
		}
	});
}


$('#poCode').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			confirmPo();
		}
	}
});


function warehouse_init() {
	$('#zone_code').val('');
	$('#zoneName').val('');

	zone_init();
}


function zone_init() {
	var whsCode = $('#warehouse').val();

	$("#zoneName").autocomplete({
		source: BASE_URL + 'auto_complete/get_zone_code_and_name/'+whsCode,
		autoFocus: true,
		close: function(){
			var rs = $(this).val();
			if(rs.length == '') {
				$('#zone_code').val('');
				$('#zoneName').val('');
			}
			else {
				arr = rs.split(' | ');
				$('#zone_code').val(arr[0]);
				$('#zoneName').val(arr[1]);
			}
		}
	});


	$("#zone_code").autocomplete({
		source: BASE_URL + 'auto_complete/get_zone_code_and_name/'+whsCode,
		autoFocus: true,
		close: function(){
			var rs = $(this).val();
			if(rs.length == '') {
				$('#zone_code').val('');
				$('#zoneName').val('');
			}
			else {
				arr = rs.split(' | ');
				$('#zone_code').val(arr[0]);
				$('#zoneName').val(arr[1]);
			}
		}
	});
}


function checkBarcode() {
	let barcode = $('#barcode').val().trim();
	if(barcode.length) {
		let qty = parseDefault(parseFloat($('#qty').val()), 1);
		let valid = 0;

		if($('.'+barcode).length) {

			$('#barcode').attr('disabled', 'disabled');

			$('.'+barcode).each(function() {
				if(valid == 0 && qty > 0) {
					let uid = $(this).val();
					let limit = parseDefault(parseFloat($(this).data('limit')), 0);
					let inputQty = parseDefault(parseFloat($('#receive-qty-'+uid).val()), 0);
					let diff = limit - inputQty;

					if(diff > 0) {
						let receiveQty = qty >= diff ? diff : qty;
						let newQty = inputQty + receiveQty;
						$('#receive-qty-'+uid).val(newQty);
						qty -= receiveQty;
					}

					if(qty == 0) {
						valid = 1;
					}
				}
			});

			if(qty > 0) {
				beep();
				swal({
					title: "ข้อผิดพลาด !",
					text: "สินค้าเกิน "+qty+" Pcs.",
					type: "error"
				},
				function(){
					setTimeout( function() {
						$("#barcode")	.focus();
					}, 1000 );
				});
			}

			sumReceive();
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
			},
			function(){
				setTimeout( function() {
					$("#barcode")	.focus();
				}, 1000 );
			});
		}
	}
}


$("#barcode").keyup(function(e) {
  if( e.keyCode == 13 ) {
		checkBarcode();
	}
});


function sumReceive() {
	let totalQty = 0;
	let totalAmount = 0;

	$(".receive-qty").each(function() {
		let el = $(this);
		el.clearError();
		let no = el.data('uid');
    let qty = parseDefault(parseFloat(el.val()), 0);
		let price = parseDefault(parseFloat(el.data('price')), 0);
		let limit = parseDefault(parseFloat(el.data('limit')), 0);
		let amount = qty * price;
		totalQty += qty;
		totalAmount += amount;

		if(qty > limit) {
			el.hasError();
		}

		$('#line-total-'+no).val(addCommas(amount.toFixed(4)));
  });

	$("#total-receive").val( addCommas(totalQty) );
	$('#total-amount').val(addCommas(totalAmount.toFixed(4)));
}


function getUploadFile(){
  $('#upload-modal').modal('show');
}


function getFile(){
  $('#uploadFile').click();
}


$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		//readURL(this);
    $('#show-file-name').text(name);
	}
});


function uploadfile() {
	$('#upload-modal').modal('hide');

	var file	= $("#uploadFile")[0].files[0];
	var fd = new FormData();
	fd.append('uploadFile', $('input[type=file]')[0].files[0]);
	if( file !== '')
	{
		load_in();
		$.ajax({
			url:HOME + 'import_data',
			type:"POST",
			cache:"false",
			data: fd,
			processData:false,
			contentType: false,
			success: function(rs){
				load_out();
				if( isJson(rs) ){
					data = $.parseJSON(rs);

					$('#vendor_code').val(data.vendor_code);
					$('#vendorName').val(data.vendor_name);
					$('#poCode').val(data.po_code);
					$('#invoice').val(data.invoice_code);
					$('#poCode').attr('disabled', 'disabled');
					$('#DocCur').val(data.DocCur);
					$('#DocRate').val(data.DocRate);

					var ds = data.details;
					var source = $("#receive-template").html();
					var output = $("#receive-table");
					render(source, ds, output);

					$('#btn-confirm-po').addClass('hide');
					$('#btn-get-po').removeClass('hide');

					sumReceive();
				}
				else{
					showError(rs);
					$("#receive-table").html('');
					sumReceive();
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
	let code = $('#receive_code').val();
	let note = $.trim($('#accept-note').val());

	if(note.length < 10) {
		$('#accept-error').text('กรุณาระบุหมายเหตุอย่างนี้อย 10 ตัวอักษร');
		return false;
	}
	else {
		$('#accept-error').text('');
	}

	$('#accept-modal').modal('hide');

	load_in();

	$.ajax({
		url:HOME + 'accept_confirm',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"save_type" : save_type,
			"accept_remark" : note
		},
		success:function(rs) {
			load_out();
			if(isJson(rs))
			{
				let ds = JSON.parse(rs);
				if(ds.status === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						window.location.reload();
					}, 1200);
				}
				else if(ds.status === 'warning') {

					swal({
						title:'Warning',
						text:ds.message,
						type:'warning',
						html:true
					}, () => {
						window.location.reload();
					});
				}
				else {
					swal({
						title:'Error!',
						text: rs,
						type:'error',
						html:true
					});
				}
			}
		}
	});

}
