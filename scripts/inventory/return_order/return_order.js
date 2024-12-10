// JavaScript Document
var HOME = BASE_URL + 'inventory/return_order/';

function goDelete(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function(){
			$('#cancle-code').val(code);
			$('#cancle-reason').val('').removeClass('has-error');
			cancle_return(code);
	});
}


function cancle_return(code)
{
	let reason = $.trim($('#cancle-reason').val());
	let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'cancle_return/'+code,
		type:"POST",
		cache:"false",
		data:{
			"reason" : reason,
			"force_cancel" : force_cancel
		},
		success: function(rs) {
			var rs = $.trim(rs);
			if( rs == 'success' ) {
				setTimeout(function() {
					swal({
						title: 'Cancled',
						type: 'success',
						timer: 1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}, 200);
			}
			else {
				setTimeout(function() {
					swal({
						title:"Error!",
						text:rs,
						type:'error'
					});
				}, 200);
			}
		}
	});
}


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return cancle_return(code);
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
	window.location.href = HOME + 'edit/'+ code;
}


function viewDetail(code){
	window.location.href = HOME + 'view_detail/'+ code;
}


function goBack(){
	window.location.href = HOME;
}

function getSearch(){
	$("#searchForm").submit();
}


$(".search").keyup(function(e){
	if( e.keyCode == 13 ){
		getSearch();
	}
});



$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});



// JavaScript Document
function printReturn(){
	var code = $("#return_code").val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_detail/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}


function printWmsReturn(){
	var code = $("#return_code").val();
	var center = ($(document).width() - 800) /2;
  	var target = HOME + 'print_wms_return/'+code;
  	window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}


function sendToFulfillment(code) {
	load_in();

	$.ajax({
		url:HOME + 'send_to_fulfillment',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(rs.trim() === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				$('#row-'+code).remove();
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
		error:function(rs) {
			load_out();
			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
	})
}
