var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;

function getSample(){
	window.location.href = HOME + 'get_sample_file';
}

function editHeader(){
	$('.header-box').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader() {
	var code = $('#receive_code').val();
	var date_add = $('#dateAdd').val();
	var remark = $('#remark').val();
	var is_wms = $('#is_wms').val();

	if(!isDate(date_add)){
		swal('วันที่ไม่ถูกต้อง');
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

				$('.header-box').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');
			}else{
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	})
}



function save() {
	is_wms = $('#is_wms').val();

	code = $('#receive_code').val();

	over_po = $('#allow_over_po').val()

	request_code = $('#requestCode').val();
	//--- Vendor code
	vendor_code = $('#vendor_code').val();

	vendor_name = $('#vendorName').val();

	//--- อ้างอิง PO Code
	po = $.trim($('#poCode').val());

	//--- เลขที่ใบส่งสินค้า
	invoice = $.trim($('#invoice').val());

	//--- zone id
	zone_code = $('#zone_code').val();

	zoneName = $('#zoneName').val();

	//--- approve key
	approver = $('#approver').val();

	//--- นับจำนวนรายการในใบสั่งซื้อ
	count = $(".receive-box").length;

	//--- Currency
	docCur = $('#DocCur').val();

	//--- Doc rate
	docRate = parseDefault(parseFloat($('#DocRate').val()), 0);

	//--- ตรวจสอบความถูกต้องของข้อมูล
	if(code == '' || code == undefined){
		swal('ไม่พบเลขที่เอกสาร', 'หากคุณเห็นข้อผิดพลาดนี้มากกว่า 1 ครับ ให้ลองออกจากหน้านี้แล้วกลับเข้ามาทำรายการใหม่', 'error');
		return false;
	}

	if(vendor_code == '' || vendor_name == ''){
		swal('กรุณาระบุผู้จำหน่าย');
		return false;
	}


	//--- ใบสั่งซื้อถูกต้องหรือไม่
	if(po == ''){
		swal('กรุณาระบุใบสั่งซื้อ');
		return false;
	}


	//--- มีรายการในใบสั่งซื้อหรือไม่
	if(count = 0){
		swal('Error!', 'ไม่พบรายการรับเข้า','error');
		return false;
	}

	//--- ตรวจสอบใบส่งของ (ต้องระบุ)
	if(invoice.length == 0) {
		swal('กรุณาระบุใบส่งสินค้า');
		return false;
	}

	//--- ตรวจสอบโซนรับเข้า
	if(zone_code == '' || zoneName == ''){
		swal('กรุณาระบุโซนเพื่อรับเข้า');
		return false;
	}

	if(docRate <= 0) {
		swal('กรุณาระบุอัตราแลกเปลี่ยน');
		$('#DocRate').addClass('has-error');
		return false;
	}
	else {
		$('#DocRate').removeClass('has-error');
	}

	header = {
		'receive_code' : code,
		'vendor_code' : vendor_code,
		'vendorName' : vendor_name,
		'poCode' : po,
		'invoice' : invoice,
		'zone_code' : zone_code,
		'approver' : approver,
		'requestCode' : request_code,
		'DocCur' : docCur,
		'DocRate' : docRate
	}


	var rows = [];



	$('.receive-box').each(function(index, el) {
		qty = parseDefault(parseFloat($(this).val()), 0);

		if(qty > 0) {
			uid = $(this).data('uid');
			let row = {
				'baseEntry' : $('#docEntry_'+uid).val(),
				'baseLine' : $('#lineNum_'+uid).val(),
				'product_code' : $('#item_'+uid).val(),
				'qty' : qty,
				'price' : $('#price_'+uid).val(),
				'backlogs' : $('#backlog_'+uid).val(),
				'currency' : $('#currency_'+uid).val(),
				'rate' : $('#rate_'+uid).val(),
				'vatGroup' : $('#vatGroup_'+uid).val(),
				'vatRate' : $('#vatRate_'+uid).val()
			}

			rows.push(row);
		}
	});


	if(rows.length < 1){
		swal('ไม่พบรายการรับเข้า');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'save',
		type:"POST",
		cache:"false",
		data: {
			"receive_code" : code,
			"header" : JSON.stringify(header),
			"items" : JSON.stringify(rows)
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
						viewDetail(code);
					}, 1200);
				}
				else if(ds.status == 'warning') {
					swal({
						title:'Warning',
						text: ds.message,
						type:'warning',
						html:true
					}, () => {
						viewDetail(code);
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
		}
	});

}	//--- end save



function checkLimit() {
	//--- Allow receive over po
	var allow = $('#allow_over_po').val() == '1' ? true : false;
	var over = 0;

	$(".receive-box").each(function() {
    let uid = $(this).data('uid');
		var limit = parseDefault(parseFloat($("#limit_"+uid).val()), 0);
		var qty = parseDefault(parseFloat($(this).val()), 0);

		if(limit > 0 && qty > 0) {
			if(qty > limit) {
				over++;

				if( ! allow) {
					$(this).addClass('has-error');
				}
			}
			else {
				$(this).removeClass('has-error');
			}
		}
		else {
			$(this).removeClass('has-error');
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


function doApprove(){
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


function changeRequestPo(){
	const is_strict = $('#is_strict_request').val();

	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		$("#receiveTable").html('');
		$('#btn-change-request').addClass('hide');
		$('#btn-get-request').removeClass('hide');
		$('#requestCode').val('');
		$('#requestCode').removeAttr('disabled');
		$('#vendor_code').val('');
		$('#vendorName').val('');
		$('#poCode').val('');
		$('#invoice').val('');
		$('#DocCur').val('THB');
		$('#DocRate').val('1.00');

		if(is_strict == '0') {
			$('#poCode').removeAttr('disabled');
			$('#btn-get-po').removeAttr('disabled');
		}

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



function getPoCurrency(poCode)
{
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



function getData(){
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


function getRequestData(){
	var code = $("#requestCode").val();
	if(code.length == 0) {
		return false;
	}

	load_in();
	$.ajax({
		url: HOME + 'get_receive_request_po_detail',
		type:"GET",
		cache:"false",
		data:{
			"request_code" : code
		},
		success: function(rs) {
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) ){
				data = $.parseJSON(rs);
				var source = $("#request-template").html();
				var output = $("#receiveTable");
				render(source, data.data, output);

				$("#requestCode").attr('disabled', 'disabled');

				$(".receive-box").keyup(function(e){
    				sumReceive();
				});

				$('#vendor_code').val(data.vendor_code);
				$('#vendorName').val(data.vendor_name);
				$('#poCode').val(data.po_code);
				$('#invoice').val(data.invoice_code);
				$('#DocCur').val(data.currency);
				$('#DocRate').val(data.rate);

				$('#btn-change-po').attr('disabled', 'disabled').addClass('hide');
				$('#btn-get-po').attr('disabled', 'disabled').removeClass('hide');
				$('#poCode').attr('disabled', 'disabled');
				$('#requestCode').attr('disabled', 'disabled');
				$('#btn-get-request').addClass('hide');
				$('#btn-change-request').removeClass('hide');

				sumReceive();

				$('#barcode').focus();

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
	requestInit();
});


$('#vendor_code').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendorName').val('');
	}
	poInit();
	requestInit();
});




$(document).ready(function() {
	poInit();
	requestInit();
});


function poInit(){
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



function requestInit(){
	var vendor_code = $('#vendor_code').val();
	if(vendor_code == ''){
		$("#requestCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_request_receive_po_code',
			autoFocus: true,
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[0]);
				}else{
					$(this).val('');
				}
			}
		});
	}else{
		$("#requestCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_request_receive_po_code/'+vendor_code,
			autoFocus: true,
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[0]);
				}else{
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


function update_request_vender(code){
	$.ajax({
		url: BASE_URL + 'inventory/receive_po_request/get_vender_by_request_code',
		type:'GET',
		cache:false,
		data:{
			'code' : code
		},
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
			getData();
		}
	}
});


$('#requestCode').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			getRequestData();
		}
	}
});






$("#zoneName").autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		if(rs.length == ''){
			$('#zone_code').val('');
			$('#zoneName').val('');
		}else{
			arr = rs.split(' | ');
			$('#zone_code').val(arr[0]);
			$('#zoneName').val(arr[1]);
		}
	}
});


$("#zone_code").autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		if(rs.length == '') {
			$('#zone_code').val('');
			$('#zoneName').val('');
		}else{
			arr = rs.split(' | ');
			$('#zone_code').val(arr[0]);
			$('#zoneName').val(arr[1]);
		}
	}
});


$("#dateAdd").datepicker({ dateFormat: 'dd-mm-yy'});


function checkBarcode() {
	var barcode = $.trim($('#barcode').val());
	if(barcode.length) {
		var qty = parseDefault(parseFloat($('#qty').val()), 1);
		var valid = 0;

		if($('.'+barcode).length) {

			$('#barcode').attr('disabled', 'disabled');

			$('.'+barcode).each(function() {
				if(valid == 0 && qty > 0) {
					let uid = $(this).val();
					let limit = parseDefault(parseFloat($(this).data('limit')), 0);
					let inputQty = parseDefault(parseFloat($('#receive_'+uid).val()), 0);
					let diff = limit - inputQty;

					if(diff > 0) {
						let receiveQty = qty >= diff ? diff : qty;
						let newQty = inputQty + receiveQty;
						$('#receive_'+uid).val(newQty);
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
					text: "สินค้าเกินใบสั่งซื้อ "+qty+" Pcs.",
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


function sumReceive(){
	let totalQty = 0;
	let totalAmount = 0;

	$(".receive-box").each(function() {
		let no = $(this).data('uid');
    let qty = parseDefault(parseFloat($(this).val()), 0);
		let price = parseDefault(parseFloat($('#price_'+no).val()), 0);
		let amount = qty * price;
		totalQty += qty;
		totalAmount += amount;

		$('#line_total_'+no).text(addCommas(amount.toFixed(2)));
  });

	$("#total-receive").text( addCommas(totalQty) );
	$('#total-amount').text(addCommas(totalAmount.toFixed(2)));
}



function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();

  if(code.length == 0){
    $('#addForm').submit();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: HOME + 'is_exists/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            $('#addForm').submit();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}

$('#code').keyup(function(e){
	if(e.keyCode == 13){
		validateOrder();
	}
});


function receiveAll() {
	$('.receive-box').each(function() {
		let id = $(this).data('uid');

		let qty = $('#backlog_'+id).val();
		$(this).val(qty);
	});

	sumReceive();
}


function clearAll() {
	$('.receive-box').each(function() {
		$(this).val("");
	});

	sumReceive();
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


	function uploadfile()
	{
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
						var source = $("#template").html();
						var output = $("#receiveTable");
						render(source, ds, output);

						$(".receive-box").keyup(function(e){
		    				sumReceive();
						});

						$('#btn-get-po').addClass('hide');
						$('#btn-change-po').removeClass('hide');

					}else{
						swal("ข้อผิดพลาด !", rs, "error");
						$("#receiveTable").html('');
					}
				}
			});
		}
	}


	function accept() {
		$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
		$('#accept-modal').modal('show');
	}

	function acceptConfirm() {
		let code = $('#receive_code').val();
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
							type:'warning'
						}, () => {
							window.location.reload();							
						});
					}
					else {
						swal({
							title:'Error!',
							text: rs,
							type:'error'
						});
					}
				}
			}
		});

	}
