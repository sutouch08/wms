
function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
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



// JavaScript Document
function printReceived(){
	var code = $("#receive_code").val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_detail/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}



function pullBack(code) {
	swal({
		title:'ย้อนสถานะ',
		text:'ต้องการย้อนสถานะเอกสารกลับมาแก้ไขหรือไม่',
		type:'warning',
		html:true,
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		closeOnConfirm:true
	}, function() {
		load_in();

		setTimeout(() => {
			$.ajax({
			url:HOME + 'pull_back',
			type:'POST',
			cache:false,
			data:{
				"code" : code
			},
			success:function(rs) {
				load_out();

				if(rs == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						window.location.reload();
					}, 1200);
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
		}, 100);
	})
}


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
			$('#force-cancel').prop('checked', false);
			$('#cancle-reason').val('').removeClass('has-error');

			cancle_received(code);
	});
}


function cancle_received(code){
	let reason = $.trim($('#cancle-reason').val());
	let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'cancle_received',
		type:"POST",
		cache:"false",
		data:{
			"receive_code" : code,
			"reason" : reason,
			"force_cancel" : force_cancel
		},
		success: function(rs){
			load_out();

			var rs = $.trim(rs);
			if( rs == 'success' ){
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


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return cancle_received(code);
}


$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});
