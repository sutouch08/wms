
function addNew(){
  window.location.href = HOME + 'add_new';
}


function edit(code){
	window.location.href = HOME + 'edit/'+ code;
}


function goProcess(code) {
	window.location.href = HOME + 'process/'+code;
}


function processMobile(code) {
	window.location.href = HOME + 'process_mobile/'+code;
}


function viewDetail(code){
	window.location.href = HOME + 'view_detail/'+ code;
}


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


function printReceived(code) {
  let width = 800;
  let height = 900;
	let center = ($(document).width() - width) / 2;
  let target = HOME + 'print/'+code;
  window.open(target, "_blank", `width=${width}, height=${height}, left=${center}, scrollbars=yes`);
}


function cancel(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
		}, function() {
			$('#cancle-code').val(code);
			$('#force-cancel').prop('checked', false);
			$('#cancle-reason').val('').removeClass('has-error');

			$('#cancle-modal').modal('show');
	});
}


function doCancle() {
  $('#cancle-reason').clearError();
  let code = $('#cancle-code').val().trim();
	let reason = $('#cancle-reason').val().trim();
  let force = $('#force-cancel').is(':checked') ? 1 : 0;

	if(reason.length < 10 && ! force)
	{
		$('#cancle-reason').hasError().focus();
		return false;
	}

  $('#cancle-modal').modal('hide');

	load_in();

	$.ajax({
		url: HOME + 'cancel',
		type:"POST",
		cache:"false",
		data:{
			"code" : code,
			"reason" : reason
		},
		success: function(rs) {
			load_out();

			if( rs.trim() === 'success' ){
				swal({
					title: 'Cancled',
					type: 'success',
					timer: 1000
				});

				setTimeout(function(){
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


$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});
