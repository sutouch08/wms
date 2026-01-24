function addNew() {
  window.location.href = HOME + 'add_new';
}

function goBack() {
  window.location.href = HOME;
}


function leave() {
  if($('.planned-qty').length) {
    swal({
      title:'Warning',
      text:'รายการที่แก้ไขจะไม่ถูกบันทึก ต้องการออกจากหน้านี้หรือไม่ ?',
      type:'warning',
      showCancelButton:true,
      cancelButtonText:'No',
      comfirmButtonText:'Yes',
      closeOnConfirm:true
    }, function(isConfirm) {
      if(isConfirm) {
        goBack();
      }
    })
  }
  else {
    goBack();
  }
}


function edit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
}


function goCancel(code) {
  swal({
    title:'Are you sure ?',
    text: 'ต้องการยกเลิกเอกสารนี้หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonText:'Yes',
    confirmButtonColor:'#fa5858',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    $('#cancle-code').val(code);
    $('#cancle-reason').val('').removeClass('has-error');
    $('#force-cancel').prop('checked', false);
    $('#cancle-modal').modal('show');

    $('#cancle-modal').on('shown.bs.modal', function() {
      $('#cancle-reason').focus();
    })
  });
}


function doCancle() {
  $('#cancle-reason').clearError();

  let code = $('#cancle-code').val();
  let reason = $('#cancle-reason').val().trim();
  let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

  if(reason.length < 10) {
    $('#cancel-reason').hasError();
    return false;
  }

  $('#cancle-modal').modal('hide');

  load_in();

  $.ajax({
    url:HOME + 'cancel',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'reason' : reason,
      'force_cancel' : force_cancel
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        setTimeout(() => {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            refresh();
          }, 1200);
        }, 100)
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


function getSearch() {
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME+'clear_filter', function() {
    goBack();
  })
}

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function sendToSap(code) {
  load_in();

  $.ajax({
    url:HOME + 'do_export',
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

        setTimeout(() => {
          refresh();
        }, 1200);
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
