var HOME = BASE_URL + 'masters/package/';
var click = 0;

function addNew(){
  clearErrorByClass('add');

  $('.add').val('');

  $('#package-type').val('box');

  $('#add-modal').on('shown.bs.modal', function() {
    $('#package-name').focus();
  });

  $('#add-modal').modal('show');
}


function add() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('add');

    let h = {
      'name' : $('#package-name').val().trim(),
      'type' : $('#package-type').val(),
      'w' : parseDefault(parseFloat($('#package-width').val()), 0),
      'l' : parseDefault(parseFloat($('#package-length').val()), 0),
      'h' : parseDefault(parseFloat($('#package-height').val()), 0)
    };

    if(h.name.length == 0) {
      $('#package-name').hasError();
      click = 0;
      return false;
    }

    if(h.w <= 0) {
      $('#package-width').hasError();
      click = 0;
      return false;
    }

    if(h.l <= 0) {
      $('#package-length').hasError();
      click = 0;
      return false;
    }

    if(h.h <= 0) {
      $('#package-height').hasError();
      click = 0;
      return false;
    }

    $('#add-modal').modal('hide');

    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'add',
        type:'POST',
        cache:false,
        data:{
          'data' : JSON.stringify(h)
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
            click = 0;
            beep();
            showError(rs);
          }
        },
        error:function(rs) {
          click = 0
          beep();
          showError(rs);
        }
      })

    }, 200);
  } //- click
}


function update() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('edit');

    let h = {
      'id' : $('#edit-id').val(),
      'name' : $('#edit-package-name').val().trim(),
      'type' : $('#edit-package-type').val(),
      'w' : parseDefault(parseFloat($('#edit-package-width').val()), 0),
      'l' : parseDefault(parseFloat($('#edit-package-length').val()), 0),
      'h' : parseDefault(parseFloat($('#edit-package-height').val()), 0),
      'active' : $('#edit-active').is(':checked') ? 1 : 0
    };

    if(h.name.length == 0) {
      $('#edit-package-name').hasError();
      click = 0;
      return false;
    }

    if(h.w <= 0) {
      $('#edit-package-width').hasError();
      click = 0;
      return false;
    }

    if(h.l <= 0) {
      $('#edit-package-length').hasError();
      click = 0;
      return false;
    }

    if(h.h <= 0) {
      $('#edit-package-height').hasError();
      click = 0;
      return false;
    }

    $('#edit-modal').modal('hide');

    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'update',
        type:'POST',
        cache:false,
        data:{
          'data' : JSON.stringify(h)
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
            click = 0;
            beep();
            showError(rs);
          }
        },
        error:function(rs) {
          click = 0
          beep();
          showError(rs);
        }
      })

    }, 200);
  } //- click
}


function goBack(){
  window.location.href = HOME;
}


function getEdit(id){
  $.ajax({
    url:HOME + 'get_edit/'+id,
    type:'GET',
    cache:false,
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let row = ds.data;

          $('.edit').clearError();

          $('#edit-id').val(row.id);
          $('#edit-package-name').val(row.name);
          $('#edit-package-type').val(row.type);
          $('#edit-package-width').val(row.width);
          $('#edit-package-length').val(row.length);
          $('#edit-package-height').val(row.height);
          if(row.active == 1) {
            $('#edit-active').prop('checked', true);
          }
          else {
            $('#edit-active').prop('checked', false);
          }

          $('#edit-modal').modal('show');
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
      showError(rs);
    }
  })
}


function clearFilter(){
  $.get(HOME + 'clear_filter', function() {
    goBack();
  })
}



function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function() {
    $.ajax({
      url:HOME + 'delete/'+id,
      type:'POST',
      cache:false,
      success:function(rs) {
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
  })
}



function getSearch(){
  $('#searchForm').submit();
}
