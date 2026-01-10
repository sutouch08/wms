window.addEventListener('load', () => {
	zoneBarcodeInit();
	from_zone_init();
	to_zone_init();
});


$('#item-code').keyup((e) => {
	if(e.keyCode == 13) {
		getProductInZone();
	}
});


//-------  ดึงรายการสินค้าในโซน
function getProductInZone() {
	let h = {
		'code' : $('#code').val(),
		'warehouse_code' : $('#from-warehouse').val(),
		'zone_code' : $('#from-zone-code').val().trim(),
		'item_code' : $('#item-code').val().trim()
	}

	load_in();

	$.ajax({
		url:HOME + 'get_product_in_zone',
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
					let source = $('#zoneTemplate').html();
					let output = $('#zone-list');

					render(source, ds.data, output);

					//inputQtyInit();
					$('#myTab a[href="#zone-table"]').tab('show');
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
}


function fillQty(el) {
	let qty = parseDefaultFloat(el.data('limit'), 0);
	el.val(qty);
}


function validQty(el) {
	let limit = parseDefaultFloat(el.data('limit'), 0);
	let qty = parseDefaultFloat(el.val(), 0);

	if(qty > limit) {
		el.hasError();
	}
	else {
		el.clearError();
	}
}


$("#from-zone").keyup(function(e) {
	if( e.keyCode == 13 ) {
		$('#item-code').focus();;
	}
});


function from_zone_init() {
	let code = $('#from-warehouse').val();

	$("#from-zone").autocomplete({
		source: HOME + 'get_transfer_zone/'+ code,
		autoFocus: true,
		close: function() {
			let rs = $(this).val().split(' | ');

			if( rs.length == 2 ) {
				$(this).val(rs[0]);
				$('#from-zone-code').val(rs[0]);
			}
			else {
				$(this).val('');
				$('#from-zone-code').val('');
			}
		}
	});
}


function to_zone_init(){
	let code = $('#to-warehouse').val();

	$("#to-zone").autocomplete({
		source: HOME + 'get_transfer_zone/' + code,
		autoFocus: true,
		close: function() {
			let rs = $(this).val().split(' | ');

			if( rs.length == 2 ) {
				$(this).val(rs[0]);
				$('#to-zone-code').val(rs[0]);
			}
			else {
				$(this).val('');
				$('#to-zone-code').val('');
			}
		}
	});
}


//------- สลับไปแสดงหน้า transfer_detail
async function showTransferTable(){
	await getTransferTable();
	$('#myTab a[href="#transfer-table"]').tab('show');
}


async function showTempTable(){
	await getTempTable();
	$('#myTab a[href="temp-table"]').tab('show');
}


function inputQtyInit() {
	$('.input-qty').keyup(function(){
		var qty = parseInt($(this).val());
		var limit = parseInt($(this).attr('max'));
		qty = isNaN(qty) ? 0 : qty;
		limit = isNaN(limit) ? 0 : limit;

		if(qty > limit)
		{
			swal('โอนได้ไม่เกิน ' + limit);
			$(this).val(limit);
		}
	})
}



//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneTo(){
	var barcode = $("#toZone-barcode").val();

	if( barcode.length > 0 ) {
		//---	คลังปลายทาง
		var warehouse_code = $("#to_warehouse_code").val();

		$.ajax({
			url: BASE_URL + 'masters/zone/get_warehouse_zone',
			type:"GET",
			cache:"false",
			data:{
				"barcode" : barcode,
				"warehouse_code" : warehouse_code
			},
			success: function(rs){

				var rs = $.trim(rs);

				if( isJson(rs) ){

					//---	รับข้อมูลแล้วแปลงจาก json
					var ds = $.parseJSON(rs);

					//---	update id โซนปลายทาง
					$("#to_zone_code").val(ds.code);
					$('#from_zone_code').val('');

					//---	update ชื่อโซน
					$("#zoneName-label").val(ds.name);

					//---	disabled ช่องยิงบาร์โค้ดโซน
					$("#toZone-barcode").attr('disabled', 'disabled');
					$('#fromZone-barcode').val('');
					$('#fromZone-barcode').attr('disabled', 'disabled');

					//--- active new zone button
					$('#btn-new-zone').removeAttr('disabled');

					$('#qty-temp').removeAttr('disabled');

					$('#barcode-item-temp').removeAttr('disabled');

					$('#barcode-item-temp').focus();

				}
				else {

					swal("ข้อผิดพลาด", rs, "error");

					//---	ลบไอดีโซนปลายทาง
					$("#to_zone_code").val("");
					$('#from_zone_code').val('');

					//---	ไม่แสดงชื่อโซน
					$('#zoneName-label').val('');

					//--- disabled new zone buton
					$('#btn-new-zone').attr('disabled');

					beep();
				}
			}
		});
	}
}


$("#toZone-barcode").keyup(function(e) {
	if( e.keyCode == 13 ){
		setTimeout(() => {
			getZoneTo();
		}, 200);
	}
});


$('#barcode-item-temp').keyup(function(e){
	if(e.keyCode === 13) {
		//---	บาร์โค้ดสินค้าที่ยิงมา
		let barcode = $(this).val();

		if(barcode.length) {

			let from_zone = $('#from_zone_code').val();
			let to_zone = $('#to_zone_code').val();
			let transfer_code = $("#transfer_code").val();

			if(from_zone.length > 0 && to_zone.length == 0) {
				addToTemp(transfer_code, barcode, from_zone);
			}
			else if(to_zone.length > 0 && from_zone.length == 0) {
				moveToZone(transfer_code, barcode, to_zone);
			}
			else {
				swal("กรุณาระบุโซน");
				return false;
			}
		}
	}
});


function newZone() {
	$('#from_zone_code').val('');
	$('#fromZone-barcode').val('');
	$('#to_zone_code').val('');
	$('#toZone-barcode').val('');
	$('#zoneName-label').val('');

	$('#fromZone-barcode').removeAttr('disabled');
	$('#toZone-barcode').removeAttr('disabled');
	$('#qty-temp').attr('disabled', 'disabled');
	$('#barcode-item-temp').attr('disabled', 'disabled');
	$('#fromZone-barcode').focus();
}


//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneFrom(){

	var barcode = $("#fromZone-barcode").val();

	if( barcode.length > 0 ) {
		//---	คลังต้นทาง
		var warehouse_code = $("#from_warehouse_code").val();

		$.ajax({
			url:BASE_URL + 'masters/zone/get_warehouse_zone',
			type:"GET",
			cache:"false",
			data:{
				"barcode" : barcode,
				"warehouse_code" : warehouse_code
			},
			success: function(rs){

				var rs = $.trim(rs);

				if( isJson(rs) ){

					//---	รับข้อมูลแล้วแปลงจาก json
					var ds = $.parseJSON(rs);

					//---	update id โซนต้นทาง
					$("#from_zone_code").val(ds.code);
					$('#to_zone_code').val('');

					//---	update ชื่อโซน
					$("#zoneName-label").val(ds.name);


					$("#fromZone-barcode").attr('disabled', 'disabled');
					$('#toZone-barcode').val('');
					$('#toZone-barcode').attr('disabled', 'disabled');
					$('#btn-new-zone').removeAttr('disabled');
					$('#qty-temp').val(1).removeAttr('disabled');
					$('#barcode-item-temp').removeAttr('disabled');
					$('#barcode-item-temp').focus();

				}
				else {
					swal("ข้อผิดพลาด", rs, "error");

					//---	ลบไอดีโซนต้นทาง
					$("#from_zone_code").val("");
					$('#to_zone_code').val('');

					//---	ไม่แสดงชื่อโซน
					$('#zoneName').val('');

					beep();
				}
			}
		});
	}
}


$("#fromZone-barcode").keyup(function(e) {
    if( e.keyCode == 13 ){
			setTimeout(() => {
				getZoneFrom();
			}, 200);
	}
});


function zoneBarcodeInit() {
	let fromWhsCode = $('#from_warehouse_code').val();
	let toWhsCode = $('#to_warehouse_code').val();
	let fromTarget = HOME + 'get_transfer_zone/'+fromWhsCode;
	let toTarget = HOME + 'get_transfer_zone/'+toWhsCode;

	$('#fromZone-barcode').autocomplete({
		source:fromTarget,
		autoFocus:true,
		close:function() {
			let zone = $(this).val().split(' | ');

			if(zone.length == 2) {
				$(this).val(zone[0]);
			}
			else {
				$(this).val('');
			}
		}
	});


	$('#toZone-barcode').autocomplete({
		source:toTarget,
		autoFocus:true,
		close:function() {
			let zone = $(this).val().split(' | ');

			if(zone.length == 2) {
				$(this).val(zone[0]);
			}
			else {
				$(this).val('');
			}
		}
	})
}


function addToTemp(transfer_code, barcode, zone_code) {
	//---	จำนวนที่ป้อนมา
	var qty = parseInt($("#qty-temp").val());

	//---	เมื่อมีการใส่จำนวนมาตามปกติ
	if( qty != '' && qty != 0 ){
		$.ajax({
			url: HOME + 'add_to_temp',
			type:"POST",
			cache:"false",
			data:{
				"transfer_code" : transfer_code,
				"from_zone" : zone_code,
				"qty" : qty,
				"barcode" : barcode,
			},
			success: function(rs) {

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {

						if(ds.row.id) {
							let id = ds.row.id;

							if($('#row-temp-'+id).length) {
								let source = $('#tempUpdateTemplate').html();
								let output = $('#row-temp-'+id);

								render(source, ds.row, output);
							}
							else {
								let source = $('#tempRowTemplate').html();
								let output = $('#temp-list');

								render_prepend(source, ds.row, output);
							}
						}

						$('#qty-temp').val(1);
						$('#barcode-item-temp').val('');
						$('#barcode-item-temp').focus();
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
						text:rs,
						type:'error',
						html:true
					});
				}
			}
		});
	}
}


function moveToZone(transfer_code, barcode, zone_code) {
	//---	จำนวนที่ป้อนมา
	var qty = parseInt($("#qty-temp").val());

	//---	เมื่อมีการใส่จำนวนมาตามปกติ
	if( qty != '' && qty != 0 ){
		$.ajax({
			url: HOME + 'move_to_zone',
			type:"POST",
			cache:"false",
			data:{
				"transfer_code" : transfer_code,
				"zone_code" : zone_code,
				"qty" : qty,
				"barcode" : barcode,
			},
			success: function(rs) {
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {

						let temp = ds.temp_result;
						let trans = ds.trans_result;

						if(temp.length) {
							temp.forEach(function(row) {
								let id = row.id;

								if(row.qty == '0') {
									$('#row-temp-'+id).remove();
									reIndex('tmp-no');
								}
								else {
									let source = $('#tempUpdateTemplate').html();
									let output = $('#row-temp-'+id);

									render(source, row, output);
								}
							});
						}

						$('#qty-temp').val(1);
						$('#barcode-item-temp').val('');
						$('#barcode-item-temp').focus();
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
						text:rs,
						type:'error',
						html:true
					});
				}
			}
		});
	}
}


function getStockSap(code) {
	load_in();

	$.ajax({
		url:HOME + 'get_stock_from_sap',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(rs == 'success') {
				window.location.reload();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function updateStock(code) {
	load_in();

	$.ajax({
		url:HOME + 'update_stock_from_sap',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(rs == 'success') {
				window.location.reload();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
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
		let code = $('#transfer_code').val();

    $('#upload-modal').modal('hide');

		var file	= $("#uploadFile")[0].files[0];
		var fd = new FormData();
		fd.append('transfer_code', code);
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
				success: function(rs) {
					load_out();

					if(rs == 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						})
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
				error:function(xhr) {
					load_out();

					swal({
						title:'Error!',
						text:xhr.responseText,
						type:'error',
						html:true
					})
				}
			});
		}
	}

	function getTemplate(){
		window.location.href = HOME + 'get_template_file';
	}
