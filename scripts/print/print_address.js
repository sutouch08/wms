
//--- properties for print
var center    = ($(document).width() - 800)/2;
var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";

//--- พิมพ์ใบนำส่งสำหรับแปะหน้ากล่อง
// function printAddress(id, order_code) {
// 	printOnlineAddress(id, order_code);
// }

function printAddress(id, order_code, id_sender)
{
	var customer_ref = $('#customer_ref').val();

	if( customer_ref != '' ){
		// printOnlineAddress(id, order_code);
		printPackingSheet(id, id_sender);
	}
	else{
		getAddressForm(id, id_sender);
	}
}


//--- เอา id address online
function getOnlineAddress()
{
	var code = $("#customer_ref").val();
	var order_code = $("#order_code").val();

	$.ajax({
		url: BASE_URL + 'masters/address/get_online_address/'+ code,
		type:"GET",
		cache: false,
		success: function(id){
			var id = $.trim(id);
			if( id == 'noaddress' || isNaN( parseInt(id) ) ){
				noAddress();
			}else{
				printOnlineAddress(id, order_code);
			}
		}
	});
}


//--- ตรวจสอบว่าลูกค้ามีที่อยู่มากกว่า 1 ที่อยู่หรือไม่
//--- ถ้ามีมากกว่า 1 ที่อยู่ จะให้เลือกก่อนว่าจะให้ส่งที่ไหน ใช้ขนส่งอะไร
function getAddressForm(id, id_sender)
{
	var order_code     = $("#order_code").val();
	var customer_code  = $("#customer_code").val();

	if(customer_code != null && customer_code != undefined && customer_code != "") {
		$.ajax({
			url: BASE_URL + 'masters/address/get_address_form',
			type:"POST",
			cache: "false",
			data:{
				"order_code" : order_code,
				"customer_code" : customer_code,
				"id" : id,
				"id_sender" : id_sender
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'no_address' ){
					noAddress();
				}else if( rs == 'no_sender' ){
					noSender();
				}else if( rs == 1 ){
					printPackingSheet(id, id_sender);
				}else{
					$("#info_body").html(rs);
					$("#infoModal").modal("show");
				}
			}
		});
	}
	else {
		printPackingSheet(id, id_sender);
	}
}


function printPackingSheet(id, id_sender)
{
  var order_code = $("#order_code").val();
	var customer_code = $('#customer_code').val();
	customer_code = customer_code == "" ? 0 : customer_code;

	id = (id === undefined || id === null || id === "") ? 0 : id;
	id_sender = (id_sender === undefined || id_sender === null || id_sender == "") ? 1 : id_sender;
	var target = BASE_URL + 'masters/address/print_address_sheet/'+order_code+'/'+customer_code+'/'+id+'/'+id_sender;
	window.open(target, "_blank", prop);
}


function printOnlineAddress(id, code)
{
	var center 	= ($(document).width() - 800)/2;
	var target 	= BASE_URL + 'masters/address/print_online_address/'+id+'/' + code;
	window.open(target, "_blank", prop );
}


function printSelectAddress()
{
	var order_code = $("#order_code").val();
	var customer_code   = $("#customer_code").val();
	var id_ad    = $('input[name=id_address]:radio:checked').val();
	var id_sen	 = $('input[name=id_sender]:radio:checked').val();
  var target   = BASE_URL + 'masters/address/print_address_sheet/'+order_code+'/'+customer_code+'/'+id_ad+'/'+id_sen;

	if( isNaN(parseInt(id_ad)) ){
    swal("กรุณาเลือกที่อยู่", "", "warning");
    return false;
  }

	if( isNaN(parseInt(id_sen)) ){
    swal("กรุณาเลือกขนส่ง", "", "warning");
    return false;
  }

	$("#infoModal").modal('hide');


	window.open(target, "_blank", prop);
}


function noAddress()
{
	swal("ข้อผิดพลาด", "ไม่พบที่อยู่ของลูกค้า กรุณาตรวจสอบว่าลูกค้ามีที่อยู่ในระบบแล้วหรือยัง", "warning");
}


function noSender()
{
	swal("ไม่พบผู้จัดส่ง", "ไม่พบรายชื่อผู้จัดส่ง กรุณาตรวจสอบว่าลูกค้ามีการกำหนดชื่อผู้จัดส่งในระบบแล้วหรือยัง", "warning");
}
