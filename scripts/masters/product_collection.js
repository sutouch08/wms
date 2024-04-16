var HOME = BASE_URL + 'masters/product_collection/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function clearFilter(){
  $.get(HOME + 'clear_filter', function() {
    goBack();
  })
}

function add() {
  $('.r').removeClass('has-error');
  $('.e').text('');

  let code = $.trim($('#code').val());
  let name = $.trim($('#name').val());
  let active = $('#active').val() == '0' ? 0 : 1;

  if(code.length == 0) {
    $('#code').addClass('has-error');
    $('#code-error').text('Required');
    return false;
  }

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Required');
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'active' : active
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            addNew();
          }, 1200);
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
          title:'Error',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function update() {
  $('.r').removeClass('has-error');
  $('.e').text('');

  let id = $('#id').val();
  let code = $.trim($('#code').val());
  let name = $.trim($('#name').val());
  let active = $('#active').val() == '0' ? 0 : 1;

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Required');
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'name' : name,
      'active' : active
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
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
          title:'Error',
          text:rs,
          type:'error'
        })
      }
    }
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
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            $('#row-'+id).remove();
            reIndex();
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            })
          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          })
        }
      }
    })
  })
}



function getSearch(){
  $('#searchForm').submit();
}
