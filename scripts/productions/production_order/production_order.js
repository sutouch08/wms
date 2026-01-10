function addNew() {
  window.location.href = HOME + 'add_new';
}

function goBack() {
  window.location.href = HOME;
}


function leave() {
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


function edit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
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
