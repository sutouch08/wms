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
	var is_wms = $('#is_wms').val();
	var remark = $('#remark').val();
	if(!isDate(date_add)){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	$.ajax({
		url:HOME + 'update_header',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'is_wms' : is_wms,
			'date_add' : date_add,
			'remark' : remark
		},
		success:function(rs){
			var rs = $.trim(rs);
			if(rs === 'success'){
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				$('.header-box').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');
			}else{
				swal({
					title:'Error',
					text:rs,
					type:'error'
				})
			}
		}
	})
}



function receiveProduct(no){
	var qty = isNaN( parseInt( $("#qty").val() ) ) ? 1 : parseInt( $("#qty").val() );
	var bc = $("#barcode");
	var input = $("#receive_"+ no);
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

function save_wms() {
	var is_wms = $('#is_wms').val();
	if(is_wms) {
		var code = $('#receive_code').val();
		var order_code = $.trim($('#order_code').val());
		var invoice = $.trim($('#invoice').val());

		//--- ตรวจสอบความถูกต้องของข้อมูล
		if(code == '' || code == undefined){
			swal('ไม่พบเลขที่เอกสาร', 'หากคุณเห็นข้อผิดพลาดนี้มากกว่า 1 ครับ ให้ลองออกจากหน้านี้แล้วกลับเข้ามาทำรายการใหม่', 'error');
			return false;
		}

		//--- ใบสั่งซื้อถูกต้องหรือไม่
		if(order_code == ''){
			swal('กรุณาระบุใบเบิกแปรสภาพ');
			return false;
		}

		//--- ตรวจสอบใบส่งของ (ต้องระบุ)
		if(invoice.length == 0){
			swal('กรุณาระบุใบส่งสินค้า');
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'save_wms',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'order_code' : order_code,
				'invoice' : invoice
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(rs == 'success') {
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
					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					})
				}
			},
			error:function(xhr, status, error) {
				load_out();
				swal({
					title:'Error',
					text:xhr.responseText,
					type:'error',
					html:true
				});
			}
		})

	}
}


function save() {

	is_wms = $('#is_wms').val();

	code = $('#receive_code').val();

	//--- อ้างอิง PO Code
	order_code = $.trim($('#order_code').val());

	//--- เลขที่ใบส่งสินค้า
	invoice = $.trim($('#invoice').val());

	//--- zone id
	zone_code = $('#zone_code').val();
	zoneName = $('#zoneName').val();

	//--- approve key
	approver = $('#approver').val();

	//--- นับจำนวนรายการในใบสั่งซื้อ
	count = $(".receive-box").length;


	//--- ตรวจสอบความถูกต้องของข้อมูล
	if(code == '' || code == undefined){
		swal('ไม่พบเลขที่เอกสาร', 'หากคุณเห็นข้อผิดพลาดนี้มากกว่า 1 ครับ ให้ลองออกจากหน้านี้แล้วกลับเข้ามาทำรายการใหม่', 'error');
		return false;
	}


	//--- ใบสั่งซื้อถูกต้องหรือไม่
	if(order_code == ''){
		swal('กรุณาระบุใบเบิกแปรสภาพ');
		return false;
	}

	//--- ตรวจสอบใบส่งของ (ต้องระบุ)
	if(invoice.length == 0){
		swal('กรุณาระบุใบส่งสินค้า');
		return false;
	}

	if(is_wms == 0) {
		//--- มีรายการในใบสั่งซื้อหรือไม่
		if(count = 0){
			swal('Error!', 'ไม่พบรายการรับเข้า','error');
			return false;
		}

		//--- ตรวจสอบโซนรับเข้า
		if(zone_code == '' || zoneName == ''){
			swal('กรุณาระบุโซนเพื่อรับเข้า');
			return false;
		}
	}



	ds = [
		{'name' : 'receive_code', 'value' : code},
		{'name' : 'order_code', 'value' : order_code},
		{'name' : 'invoice', 'value' : invoice},
		{'name' : 'zone_code', 'value' : zone_code},
		{'name' : 'approver', 'value' : approver}
	];


	$('.receive-box').each(function(index, el) {
		var no = $(this).data('no');
		qty = parseInt($(this).val());
		pdCode = $('#product_'+no).val();
		pdName = "products["+no+"]";
		qtyName = "receive["+no+"]";

		if($(this).val() > 0 && !isNaN(qty)){
			ds.push({
				'name' : pdName, 'value' : pdCode
			});

			ds.push({
				'name' : qtyName, 'value' : qty
			});
		}
	});

	if(ds.length < 7){
		swal('ไม่พบรายการรับเข้า');
		return false;
	}


	load_in();

	$.ajax({
		url: HOME + 'save',
		type:"POST",
		cache:"false",
		data: ds,
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

		var limit = $("#overLimit").val();
		var over = 0;
		$(".receive-box").each(function(index, element) {
			var no = $(this).data('no');
			var limit = parseInt($("#limit_"+no).val());
			var qty = parseInt($("#receive_"+no).val() );
			if( ! isNaN(limit) && ! isNaN( qty ) ){
				if( qty > limit ){
					over++;
					}
				}
	    });

		if( over > 0 ){
			swal({
				title:'Error!',
				text:'ยอดรับเกินยอดค้างรับ กรุณาตรวจสอบ',
				type:'error'
			});
			//getApprove();
		}else{
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


function click_init(){
	$('.barcode').click(function(){
		var barcode = $.trim($(this).text());
		$('#barcode').val(barcode);
		$('#barcode').focus();
	});
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
	var menu = 'APOVPO'; //-- อนุมัติรับสินค้าเกินใบสั่งซื้อ
	var field = '';

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
		$('#order_code').val('');
		$('#order_code').removeAttr('disabled');
		swal({
			title:'Success',
			text:'ยกเลิกข้อมูลเรียบร้อยแล้ว',
			type:'success',
			timer:1000
		});
		setTimeout(function(){
			$('#order_code').focus();
		}, 1200);
	});
}


function getData(){
	var order_code = $("#order_code").val();
	load_in();
	$.ajax({
		url: HOME + 'get_transform_detail',
		type:"GET",
		cache:"false",
		data:{
			"order_code" : order_code
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) ){
				data = $.parseJSON(rs);
				var source = $("#template").html();
				var output = $("#receiveTable");
				render(source, data, output);
				$("#order_code").attr('disabled', 'disabled');
				$(".receive-box").keyup(function(e){
    				sumReceive();
				});

				$('#btn-get-po').addClass('hide');
				$('#btn-change-po').removeClass('hide');
				click_init();
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


$("#order_code").autocomplete({
	source: BASE_URL + 'auto_complete/get_transform_code',
	autoFocus: true,
	close:function(){
		var code = $(this).val();
		var arr = code.split(' | ');
		if(arr.length == 2){
			$(this).val(arr[1]);
		}
	}
});




$('#order_code').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			getData();
		}
	}
});






$("#zoneName").autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2){
			$('#zone_code').val(arr[0]);
			$('#zoneName').val(arr[1]);
		}else{
			$('#zone_code').val('');
			$('#zoneName').val('');
		}
	}
});





$("#dateAdd").datepicker({ dateFormat: 'dd-mm-yy'});






function checkBarcode(){
	barcode = $('#barcode').val();

	if($('#'+barcode).length == 1){
		no = $('#'+barcode).val();
		receiveProduct(no);
	}else{
		$('#barcode').val('');
		swal({
			title: "ข้อผิดพลาด !",
			text: "บาร์โค้ดไม่ถูกต้องหรือสินค้าไม่ตรงกับใบสั่งซื้อ",
			type: "error"
		},
			function(){
				setTimeout( function(){ $("#barcode")	.focus(); }, 1000 );
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
		var no = $(this).data('no');
		var limit = parseInt($('#backlog_'+no).val());
		var cqty = isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
		qty += cqty;
		if(cqty > limit) {
			$(this).addClass('has-error');
		}
		else {
			$(this).removeClass('has-error');
		}
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
