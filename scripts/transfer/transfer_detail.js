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
					type:'error',
					html:true
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


function sendToPlc() {
	var code = $('#transfer_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_plc/'+code,
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


function sendToSoko() {
	var code = $('#transfer_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_soko/'+code,
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


function sendToPos() {
	var code = $('#transfer_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_pos/'+code,
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

function removeTemp(id, item_code) {
	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการลบ '+item_code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#d15b47',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'delete_temp',
				type:'POST',
				cache:false,
				data:{
					'id' : id
				},
				success:function(rs) {
					load_out();

					if(rs === 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						$('#row-temp-'+id).remove();
						reIndex('tmp-no');
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
		}, 200);
	})
}


function checkAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	let code = $('#transfer_code').val();
	let ids = [];

	if($('.chk:checked').length) {
		$('.chk:checked').each(function() {
			ids.push($(this).val());
		});

		if(ids.length > 0) {
			swal({
				title: 'คุณแน่ใจ ?',
				text: 'ต้องการลบ '+ ids.length +' รายการที่เลือก หรือไม่ ?',
				type: 'warning',
				showCancelButton: true,
				comfirmButtonColor: '#DD6855',
				confirmButtonText: 'ใช่ ฉันต้องการ',
				cancelButtonText: 'ไม่ใช่',
				closeOnConfirm: true
			},
			function() {
				load_in();

				setTimeout(() => {
					$.ajax({
						url:HOME + 'delete_detail',
						type:'POST',
						cache:false,
						data:{
							'transfer_code' : code,
							'ids' : JSON.stringify(ids)
						},
						success:function(rs) {
							load_out();

							if(rs == 'success') {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								$('.chk:checked').each(function() {
									let id = $(this).val();
									$('#row-'+id).remove();
								});

								reIndex();
								reCal();
							}
							else {
								swal({
									title:'Error!',
									text:rs,
									type:'error'
								});
							}
						}
					})
				}, 200);
			});
		}
	}
}


function updateTransferQty(id) {
	let el = $('#trans-qty-'+id);
	let prevQty = parseDefault(parseFloat(el.data('qty')), 0);
	let qty = parseDefault(parseFloat(el.val()), 0);
	let wmsQty = parseDefault(parseFloat(el.data('wms')), 0);

	if(qty < wmsQty) {
		beep();
		showError('ยอดตั้งต้องไม่น้อยกว่ายอดที่รับแล้ว');
		el.val(prevQty);
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update_transfer_qty',
		type:'POST',
		cache:false,
		data:{
			'id' : id,
			'qty' : qty
		},
		success:function(rs) {
			load_out();

			if(rs.trim() === 'success') {
				el.data('qty', qty);
				reCal(-1);
			}
			else {
				beep();
				el.val(prevQty);
				showError(rs);
			}
		},
		error:function(rs) {
			beep();
			showError(rs);
		}
	});
}


function reCal(is_wms) {
	var total = 0;

	if(is_wms == -1) {
		$('.trans-qty').each(function(){
			total += parseDefault(parseFloat($(this).val()), 0);			
		});
	}
	else {
		$('.qty').each(function(){
			total += parseDefault(parseInt(removeCommas($(this).text())), 0);
		});
	}

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
				let pd_code  = $(this).data('sku'); //$(this).attr('id')
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
							recalZoneQty();
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

function recalZoneQty() {
	$('.input-qty').each(function() {
		if($(this).val() != 0) {
			let no = $(this).data('no');
			let limit = parseDefault(parseFloat($(this).data('limit')), 0);
			let qty = parseDefault(parseFloat($(this).val()), 0);

			if(qty > 0 && limit > 0) {
				let nQty = limit - qty;

				$('#qty-label-'+no).text(addCommas(nQty));
				$(this).data('limit', nQty);
				$(this).val('');

				if(nQty <= 0) {
					$('#zone-row-'+no).remove();
					reIndex('zone-no');
				}
			}
		}
	})
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

function accept() {
	let canAccept = $('#can-accept').val() == 1 ? true : false;
	let code = $('#transfer_code').val();

	if(canAccept) {
		$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
		$('#accept-modal').modal('show');
	}
	else {

		swal({
			title:'Acception',
			text:'ยินยอมให้โอนสินค้าเข้าโซนของคุณใช่หรือไม่ ?',
			type:'info',
			showCancelButton:true,
			confirmButtonColor:'#87B87F',
			confirmButtonText:'ยืนยัน',
			cancelButtonText:'ยกเลิก',
			closeOnConfirm:true
		}, function() {
			load_in();

			$.ajax({
				url:HOME + 'accept_zone',
				type:'POST',
				cache:false,
				data: {
					'code' : code
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
								setTimeout(() => {
									window.location.reload();
								}, 500);
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
			})
		})
	}
}


function acceptConfirm() {
	let code = $('#transfer_code').val();
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
						type:'warning',
						html:true
					}, () => {
						setTimeout(() => {
							window.location.reload();
						}, 500);
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


function pullBack(code) {
	load_in();

  $.ajax({
    url:HOME + '/pull_back',
    type:'POST',
    cache:'false',
    data:{
      'transfer_code' : code
    },
    success:function(rs) {
			load_out();
      if(rs.trim() == 'success') {
        swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(() => {
					window.location.reload();
				}, 1200);
      }
			else {
      showError(rs);
      }
    },
		error:function(rs) {
			showError(rs);
		}
  });
}
