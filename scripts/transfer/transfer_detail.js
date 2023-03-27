function doExport()
{
	var code = $('#transfer_code').val();
	load_in();
	$.ajax({
		url:HOME + 'export_transfer/' + code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	});
}


function sendToWms() {
	var code = $('#transfer_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_wms/'+code,
		type:'POST',
		cache:false,
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				});
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}

function deleteMoveItem(id, code)
{
	var code = $('#transfer_code').val();
  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'delete_detail/'+ id,
			type:"POST",
      cache:"false",
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Success',
						text: 'ดำเนินการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					$('#row-'+id).remove();
					reIndex();
					reCal();
				}else{
					swal("ข้อผิดพลาด", rs, "error");
				}
			}
		});
	});
}


function reCal(){
	var total = 0;
	$('.qty').each(function(){
		var qty = parseInt(removeCommas($(this).text()));
		if(!isNaN(qty))
		{
			total += qty;
		}
	});

	$('#total').text(addCommas(total));
}


//------------  ตาราง transfer_detail
function getTransferTable(){
	var code	= $("#transfer_code").val();
	$.ajax({
		url: HOME + 'get_transfer_table/'+ code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#transferTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#transfer-list");
				render(source, data, output);
			}
		}
	});
}




function getTempTable(){
	var code = $("#transfer_code").val();
	$.ajax({
		url: HOME + 'get_temp_table/'+code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#tempTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#temp-list");
				render(source, data, output);
			}
		}
	});
}




//--- เพิ่มรายการลงใน transfer detail
//---	เพิ่มลงใน transfer_temp
//---	update stock ตามรายการที่ใส่ตัวเลข
function addToTransfer(){
	var code	= $('#transfer_code').val();

	//---	โซนต้นทาง
	var from_zone = $("#from_zone_code").val();

	if(from_zone.length == 0)
	{
		swal('โซนต้นทางไม่ถูกต้อง');
		return false;
	}

	//--- โซนปลายทาง
	var to_zone = $('#to_zone_code').val();
	if(to_zone.length == 0)
	{
		swal('โซนปลายทางไม่ถูกต้อง');
		return false;
	}

	//---	จำนวนช่องที่มีการป้อนตัวเลขเพื่อย้ายสินค้าออก
	var count  = countInput();
	if(count == 0)
	{
		swal('ข้อผิดพลาด !', 'กรุณาระบุจำนวนในรายการที่ต้องการย้าย อย่างน้อย 1 รายการ', 'warning');
		return false;
	}

	//---	ตัวแปรสำหรับเก็บ ojbect ข้อมูล
	var ds  = {};
	var items = [];

	ds.transfer_code = code;
	ds.from_zone = from_zone;
	ds.to_zone = to_zone;



	$('.input-qty').each(function(index, element) {
	    let qty = parseDefault(parseInt($(this).val()),0);

			if(qty > 0) {
				let pd_code  = $(this).attr('id')
				items.push({"item_code" : pd_code, "qty" : qty});
			}
    });

	ds.items = items;

	if( count > 0 ) {
		load_in();
		setTimeout(function(){
			$.ajax({
				url: HOME + 'add_to_transfer',
				type:"POST",
				cache:"false",
				data: {
					"data" : JSON.stringify(ds)
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title: 'success',
							text: 'เพิ่มรายการเรียบร้อยแล้ว',
							type: 'success',
							timer: 1000
						});

						setTimeout( function(){
							showTransferTable();
						}, 1200);

					}else{

						swal("ข้อผิดพลาด", rs, "error");
					}
				}
			});
		}, 500);
	}
	else
	{

		swal('ข้อผิดพลาด !', 'กรุณาระบุจำนวนในรายการที่ต้องการย้าย อย่างน้อย 1 รายการ', 'warning');

	}
}



function selectAll(){
	$('.input-qty').each(function(index, el){
		var qty = $(this).attr('max');
		$(this).val(qty);
	});
}


function clearAll(){
	$('.input-qty').each(function(index, el){
		$(this).val('');
	});
}




//----- นับจำนวน ช่องที่มีการใส่ตัวเลข
function countInput(){
	var count = 0;
	$(".input-qty").each(function(index, element) {
        count += ($(this).val() == "" ? 0 : 1 );
    });
	return count;
}
