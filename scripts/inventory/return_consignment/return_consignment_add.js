function toggleCheckAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} else {
		$('.chk').prop("checked", false);
	}
}


function deleteChecked(){
	var count = $('.chk:checked').length;
	if(count > 0){
		$('.chk:checked').each(function(){
			var id = $(this).data('id');
			var no = $(this).val();
			removeRow(no, id);
		})
	}
}


function unsave(){
	var code = $('#return_code').val();
	$.ajax({
		url:HOME + 'unsave/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			if(rs === 'success'){
				swal({
					title:'Success',
					text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
					type:'success',
					time:1000
				});

				setTimeout(function(){
					goEdit(code);
				}, 1500);
			}
		}
	})
}


function save()
{
	var error = 0;
	$('.input-price').each(function(){
		let price = parseFloat($(this).val());
		if(isNaN(price)){
			error++;
			swal('กรุณาใสราคาให้ครบถ้วน');
			$(this).addClass('has-error');
			return false;
		}else{
			$(this).removeClass('has-error');
		}
	});

	$('.input-qty').each(function(){
		let qty = parseFloat($(this).val());
		if(isNaN(qty) || qty == 0){
			error++;
			swal('กรุณาใส่จำนวนให้ครบถ้วน');
			$(this).addClass('has-error');
			return false;
		}
	});

	if(error == 0){
		$('#detailsForm').submit();
	}
}



function approve(){
	var code = $('#return_code').val();
	$.get(HOME+'approve/'+code, function(rs){
		if(rs === 'success'){
			swal({
				title:'Success',
				type:'success',
				timer: 1000
			});

			$('#btn-approve').remove();
		}else{
			swal({
				title:'Error',
				text:rs,
				type:'error'
			})
		}
	});
}



function doExport(){
	var code = $('#return_code').val();
	$.get(HOME + 'export_return/'+code, function(rs){
		if(rs === 'success'){
			swal({
				title:'Success',
				text:'ส่งข้อมูลไป SAP สำเร็จ',
				type:'success',
				timer:1000
			});
			setTimeout(function(){
				viewDetail(code);
			}, 1500);
		}
	});
}



function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
	var code = $('#return_code').val();
	var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var customer_code = $('#customer_code').val();
	var warehouse_code = $('#warehouse_code').val();
	var zone_code = $('#zone_code').val();
	var from_warehouse_code = $('#from_warehouse_code').val();
	var from_zone_code = $('#from_zone_code').val();
  var remark = $('#remark').val();
	var gp = $('#gp').val();

	if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	if(from_zone_code.length == 0){
		swal('กรุณาระบุโซนฝากขาย');
		return false;
	}

  load_in();
	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'return_code' : code,
			'date_add' : date_add,
			'invoice' : invoice,
			'customer_code' : customer_code,
			'warehouse_code' : warehouse_code,
			'zone_code' : zone_code,
			'from_zone_code' : from_zone_code,
			'remark' : remark,
			'gp' : gp
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				$('.edit').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					text:'ต้องการโหลดข้อมูลรายการสินค้าใหม่หรือไม่ ?',
					type: 'success',
					showCancelButton: true,
					cancelButtonText: 'No',
					confirmButtonText: 'Yes',
					closeOnConfirm: false
				}, function(){
					window.location.reload();
				});
			}
			else
			{
				swal({
					title:'Error!!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}



$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});


function invoice_init()
{
	var customer_code = $('#customer_code').val();
	$('#invoice').autocomplete({
		source:HOME + 'search_invoice_code',
		autoFocus:true,
		close:function(){
			var rs = $.trim($(this).val());
			var arr = rs.split(' | ');
			if(arr.length == 2)
			{
				$(this).val(arr[0]);
				$('#bill_amount').val(removeCommas(arr[1]));
			}
		}
	})
}




function addNew()
{
  var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var gp = $('#gp').val();
	var customer_code = $('#customer_code').val();
	var from_zone = $('#from_zone_code').val();
	var zone_code = $('#zone_code').val();
	var remark = $('#remark').val();

  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(from_zone.length == 0){
		swal('กรุณาระบุโซนฝากขาย');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	load_in();
	$.ajax({
		url: HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'date_add' : date_add,
			'invoice' : invoice,
			'gp' : gp,
			'customer_code' : customer_code,
			'from_zone' : from_zone,
			'zone_code' : zone_code,
			'remark' : remark
		},
		success:function(rs){
			load_out();
			var rs = $.parseJSON(rs);
			if(rs.status === 'success'){
				goEdit(rs.code);
			}else{
				swal({
					title:'Error',
					text:rs.message,
					type:'error'
				})
			}
		}
	});
}


$('#customer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customerCode').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customerCode').val('');
			$('#customer').val('');
		}

		fromZoneInit();
	}
});


$('#customerCode').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customerCode').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customerCode').val('');
			$('#customer').val('');
		}

		fromZoneInit();
	}
});


function fromZoneInit(){
	var customer = $('#customer_code').val();
	$('#fromZone').autocomplete({
		source : BASE_URL + 'auto_complete/get_consignment_zone/'+customer,
		autoFocus:true,
		close:function(){
			var arr = $(this).val().split(' | ');
			if(arr.length == 2){
				$(this).val(arr[1]);
				$('#from_zone_code').val(arr[0]);
			}else{
				$(this).val('');
				$('#from_zone_code').val('');
			}
		}
	});
}




$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$(this).val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$(this).val('');
			$('#zone_code').val('');
		}
	}
})



function recalRow(el, no) {
	var price = parseFloat($('#price_' + no).val());
	var qty = parseFloat(el.val());
	var discount = parseFloat($('#discount_' + no).val()) * 0.01;
	price = isNaN(price) ? 0 : price;
	qty = isNaN(qty) ? 0 : qty;
	discount = qty * (price * discount);
	var amount = (qty * price) - discount;
	amount = amount.toFixed(2);
	$('#amount_' + no).text(addCommas(amount));
	recalTotal();
}


function recalTotal(){
	var totalAmount = 0;
	var totalQty = 0;
	$('.amount-label').each(function(){
		let amount = removeCommas($(this).text());
		amount = parseFloat(amount);
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		let qty = $(this).val();
		qty = parseFloat(qty);
		totalQty += qty;
	});

	$('#total-qty').text(addCommas(totalQty));
	$('#total-amount').text(addCommas(totalAmount.toFixed(2)));
}



function removeRow(no, id){
	if(id != '' && id != '0' && id != 0){
		$.ajax({
			url:HOME + 'delete_detail/'+id,
			type:'GET',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					$('#row_' + no).remove();
					reIndex();
					recalTotal();
				}
				else
				{
					swal(rs);
					return false;
				}
			}
		});
	}
	else
	{
		$('#row_'+no).remove();
		reIndex();
		recalTotal();
	}
}


function loadCheckList(check_code){
  $('#check-list-modal').modal('hide');
  swal({
    title: "นำเข้ายอดต่าง",
		text: "ต้องการนำเข้ายอดต่างจากเอกสารกระทบยอด "+check_code+" หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    var code = $('#code').val();
    load_in();
    $.ajax({
      url: HOME + 'load_check_list/'+code,
      type:'POST',
      cache:'false',
      data:{
        'check_code' : check_code
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title: 'Success',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            window.location.reload();
          },1500);
        }else{
          swal('Error!', rs, 'error');
        }
      }
    });

  });//--- swal
}


function getSample(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'get_sample_file/'+token;
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



function uploadfile(){
  var code = $('#return_code').val();
  var excel = $('#uploadFile')[0].files[0];

	$("#upload-modal").modal('hide');

	var fd = new FormData();

	fd.append('excel', $('input[type=file]')[0].files[0]);
	load_in();

	$.ajax({
		url:HOME + 'import_excel_file/'+code,
		type:"POST",
    cache: "false",
    data: fd,
    processData:false,
    contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);
			}
			else
			{
				swal("Error!", rs, "error");
			}
		}
	});
}


$(document).ready(function(){
	fromZoneInit();
})
