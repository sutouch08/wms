function doCancle() {
  $('#reason-id').removeClass('has-error');
  $('#cancle-reason').removeClass('has-error');

  let reason_id = $('#reason-id').val();
  let reason = $.trim($('#cancle-reason').val());

  if(reason_id == '') {
    $('#reason-id').addClass('has-error');
    return false;
  }

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return changeState();
}

$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


function showReason() {
	$('#cancle-reason-modal').modal('show');
}

function showCancleModal() {
  $('#reason-id').val('').removeClass('has-error');
  $('#cancle-reason').val('').removeClass('has-error');
  $('#cancle-modal').modal('show');
}
