var HOME = BASE_URL + 'inventory/pick_list/';

function goBack() {
  window.location.href = HOME;
}


function getSearch() {
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', function() {
    goBack();
  })
}


function addNew() {
  window.location.href = HOME + 'add_new';
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function goProcess(code) {
  window.location.href = HOME + 'process/'+code;
}


function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
}


function goCancel(code) {
  swal({
    title:'ยกเลิก',
    text:'ต้องการยกเลิก '+code+' หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    confirmButtonColor:'#d15b47',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'cancel',
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
              window.location.reload();
            }, 1200)
          }
          else {
            beep();
            showError(rs);
          }
        },
        error:function(rs) {
          beep();
          showError(rs);
        }
      })
    }, 100)
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
