var HOME = BASE_URL + 'inventory/transfer/';

function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = HOME + 'add_new';
}


function process(code) {
  window.location.href = HOME + 'process/'+code;
}


function goDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
		goBack();
	});
}


function getSearch(){
  $('#searchForm').submit();
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function toggleFilter() {
  let filter = $('#filter-pad');

  if(filter.hasClass('move-in')) {
    filter.removeClass('move-in');
  }
  else {
    filter.addClass('move-in');
  }
}


function closeFilter() {
  $('#filter-pad').removeClass('move-in');
}


function save() {
  let code = $('#transfer-code').val();

  swal({
    title:'บันทึกเอกสาร',
    text:'เมื่อบันทึกเอกสารแล้วจะไม่สามารถแก้ไขได้อีก ต้องการดำเนินการหรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    comfirmButtonText:'บันทึก',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      checkTempExists(code);
    }, 100);
  });
}


function checkTempExists(code) {
  load_in();

  $.ajax({
    url:HOME + 'check_temp_exists/'+code,
    typ:'POST',
    cache:false,
    success:function(rs) {
      if(rs.trim() == 'not_exists') {
        saveTransfer(code);
      }
      else {
        load_out();
        beep();
        swal({
          title:'Temp exists !',
          text:'พบรายการค้างใน Temp หากทำการบันทึกรายการใน temp จะถูกเคลียร์ <br/>ต้องการบันทึกหรือไม่ ?',
          type:'warning',
          html:true,
          showCancelButton:true,
          cancelButtonText:'ยกเลิก',
          confirmButtonText:'บันทึก',
          confirmButtonColor:'red',
          closeOnConfirm:true
        }, function() {
          setTimeout(() => {
            saveTransfer(code);
          }, 100);
        });
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  });
}


function saveTransfer(code)
{
  load_in();

  $.ajax({
    url:HOME + 'save_mobile_transfer/'+code,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();
      if(rs.trim() === 'success') {
        swal({
          title:'Saved',
          text: 'บันทึกเอกสารเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          goDetail(code);
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
