
$('#date').datepicker({
  dateFormat:'dd-mm-yy'
})


function add() {
  clearErrorByClass('e');

  let h = {
    'date_add' : $('#date').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'zone_code' : $('#zone').val(),
    'channels_code' : $('#channels').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date').hasError();
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    swal("กรุณาเลือกคลัง");
    return false;
  }

  if(h.zone_code == "") {
    $('#zone').hasError();
    swal("กรุณาระบุโซน");
    return false;
  }

  load_in()

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out()

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          goEdit(ds.code);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError();
    }
  })
}


function getEdit() {
  $('.e').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function update() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val(),
    'date_add' : $('#date').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'zone_code' : $('#zone').val(),
    'channels_code' : $('#channels').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date').hasError();
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    swal("กรุณาเลือกคลัง");
    return false;
  }

  if(h.zone_code == "") {
    $('#zone').hasError();
    swal("กรุณาระบุโซน");
    return false;
  }

  load_in()

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out()

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          goEdit(ds.code);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError();
    }
  })
}


function releasePickList() {
  let code = $('#code').val();
  let count = $('.pick-detail').length;

  if(count == 0) {
    swal("ไม่พบรายการใน Pick List");
    return false;
  }

  swal({
    title:'Release Pick List',
    text:'ต้องการปล่อยจัด Pick List นี้หรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'release_pick_list',
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
              viewDetail(code);
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
    }, 100)
  })
}
