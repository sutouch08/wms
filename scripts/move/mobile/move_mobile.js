var HOME = BASE_URL + 'inventory/move/';

function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = HOME + 'add_new';
}


function edit(code) {
  window.location.href = HOME + 'edit/'+code;
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


//---  บันทึกเอกสาร
function save(){
  let code = $('#move-code').val();

  //--- check temp
  $.ajax({
    url:HOME + 'check_temp_exists/'+code,
    type:'POST',
    cache:'false',
    success:function(rs) {
      //--- ถ้าไม่มียอดค้างใน temp
      if( rs.trim() == 'not_exists'){
        //--- ส่งข้อมูลไป formula
        saveMove(code);
      }
      else {
        beep();
        showError('พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ');
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  });
}



function saveMove(code)
{
  load_in();

  $.ajax({
    url:HOME + 'save_move/'+code,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
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
        else if(ds.status == 'warning') {
          swal({
            title:'Warning',
            text:ds.message,
            type:'warning'
          }, () => {
            goDetail(code);
          });
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}
