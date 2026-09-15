const addNew = () => {
	window.location.href = `${HOME}add_new`;
}

const edit = (code) => {
	window.location.href = `${HOME}edit/${code}`;
}

const viewDetail = (code) => {
	window.location.href = `${HOME}view_detail/${code}`;
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
		url: `${HOME}cancle_return/${code}`,
		type:"POST",
		cache:"false",
		data:{
			"reason" : reason,
			"force_cancel" : force_cancel
		},
		success: function(rs) {			
			if( rs.trim() === 'success' ) {
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
						type:'error',
						html:true
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


function pullBack(code) {
	load_in();

	$.ajax({
		url:`${HOME}pull_back`,
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

function printReturn() {
	const code = $('#return_code').val();
	const width = 800;
	const height = 900;
	const center = ($(document).width() - width) /2;
	const url = `${HOME}print_detail/${code}`;
  window.open(url, "_blank", `width=${width}, height=${height}, left=${center}, scrollbars=yes`);
}
