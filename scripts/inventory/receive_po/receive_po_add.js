// JavaScript Document

var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;


function editHeader(){
	$('.header-box').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
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
		success:function(rs){
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




function receiveProduct(pdCode){
	var qty = isNaN( parseInt( $("#qty").val() ) ) ? 1 : parseInt( $("#qty").val() );
	var bc = $("#barcode");
	var input = $("#receive_"+ pdCode);
	if(input.length == 1 ){
		bc.val('');
		bc.attr('disabled', 'disabled');
		var cqty = input.val() == "" ? 0 : parseInt(input.val());
		qty += cqty;
		input.val(qty);
		$("#qty").val(1);
		sumReceive();
		bc.removeAttr('disabled');
		bc.focus();
	}else{
		swal({
			title: "ข้อผิดพลาด !",
			text: "บาร์โค้ดไม่ถูกต้องหรือสินค้าไม่ตรงกับใบสั่งซื้อ",
			type: "error"},
			function(){
				setTimeout( function(){ $("#barcode")	.focus(); }, 1000 );
		});
	}
}




function save(){
	is_wms = $('#is_wms').val();

	code = $('#receive_code').val();

	over_po = $('#over_po').val()
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

	//--- ราคาสินค้าแต่ละตัว
	price = $('#prices').val()

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
	if(invoice.length == 0){
		swal('กรุณาระบุใบส่งสินค้า');
		return false;
	}

	if(is_wms == 0) {
		//--- ตรวจสอบโซนรับเข้า
		if(zone_code == '' || zoneName == ''){
			swal('กรุณาระบุโซนเพื่อรับเข้า');
			return false;
		}
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
				'product_code' : $('#item_'+uid).val(),
				'qty' : qty,
				'price' : $('#price_'+uid).val(),
				'backlogs' : $('#limit_'+uid).val(),
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

	var ds = [{

	}];


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
		success: function(rs){
			load_out();

			rs = $.trim(rs);
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'บันทึกรายการเรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					viewDetail(code);
				}, 1200);
			}
			else
			{
				swal("ข้อผิดพลาด !", rs, "error");
			}
		}
	});


}	//--- end save



function checkLimit(){
	//--- Allow receive over po
	var allow_over_po = $('#over_po').val();
	var limit = $("#overLimit").val();
	var over = 0;
	$(".barcode").each(function(index, element) {
    var arr = $(this).attr("id").split('_');
		var barcode = arr[1];
		var limit = parseInt($("#limit_"+barcode).val() );
		var qty = parseInt($("#receive_"+barcode).val() );
		if( ! isNaN(limit) && ! isNaN( qty ) ){
			if( qty > limit ){
				over++;
				}
			}
    });

	if( over > 0 && allow_over_po == 1)
	{
		getApprove();
	}
	else if(over > 0 && allow_over_po == 0)
	{
		swal({
			title:'สินค้าเกิน',
			text:'สินค้าเกินใบสั่งซื้อหรือสินค้าเกินใบขออนัมัติ',
			type:'warning'
		});
		return false;
	} else{
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
		$('#btn-change-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#poCode').val('');
		$('#poCode').removeAttr('disabled');
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

				$('#btn-get-po').addClass('hide');
				$('#btn-change-po').removeClass('hide');
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
	load_in();
	$.ajax({
		url: HOME + 'get_receive_request_po_detail',
		type:"GET",
		cache:"false",
		data:{
			"request_code" : code
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) ){
				data = $.parseJSON(rs);
				var source = $("#template").html();
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

				$('#btn-get-po').attr('disabled', 'disabled');
				$('#poCode').attr('disabled', 'disabled');
				$('#requestCode').attr('disabled', 'disabled');
				$('#btn-get-request').addClass('hide');
				$('#btn-change-request').removeClass('hide');

				$('#zone').focus();

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



$('#vendorName').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendor_code').val('');
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
	source: BASE_URL + 'auto_complete/get_zone_code', //"controller/receiveProductController.php?search_zone",
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





$("#dateAdd").datepicker({ dateFormat: 'dd-mm-yy'});






function checkBarcode() {
	var barcode = $.trim($('#barcode').val());

	if($('#'+barcode).length == 1){
		uid = $('#'+barcode).val();
		receiveProduct(uid);
	}
	else {
		$('#barcode').val('');
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



$("#barcode").keyup(function(e) {
  if( e.keyCode == 13 ){
		checkBarcode();
	}
});




function sumReceive(){

	var qty = 0;
	$(".receive-box").each(function(index, element) {
    	var cqty = isNaN( parseInt( $(this).val() ) ) ? 0 : parseInt( $(this).val() );
			qty += cqty;
    });
	$("#total-receive").text( addCommas(qty) );
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
