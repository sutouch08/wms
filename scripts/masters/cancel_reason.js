var HOME = BASE_URL + 'masters/cancel_reason/';

function addNew(){
  window.location.href = HOME + 'add_new';
}

function goBack(){
  window.location.href = HOME;
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function clearFilter() {
  var url = HOME + 'clear_filter';
  $.get(url, function(rs) {
    window.location.href = HOME;
  });
}


function add() {
  $('#name').removeClass('has-error');
  $('#name-error').text('');
  let name = $.trim($('#name').val());
  let active = $('#active').val();

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Requied');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'name' : name,
      'active' : active
    },
    success:function(rs) {
      load_out();

      if(rs == 'success') {
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
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function update(id) {
  $('#name').removeClass('has-error');
  $('#name-error').text('');
  let name = $.trim($('#name').val());
  let active = $('#active').val();

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Requied');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'name' : name,
      'active' : active
    },
    success:function(rs) {
      load_out();

      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function viewDetail(id) {
  $.ajax({
    url:HOME + 'get/'+id,
    type:'GET',
    cache:false,
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          $('#v-name').val(ds.data.name);
          $('#v-status').val(ds.data.status);
          $('#create-by').val(ds.data.user);
          $('#create-at').val(ds.data.date_add);
          $('#update-by').val(ds.data.update_user);
          $('#update-at').val(ds.data.date_upd);
          $('#prev-name').val(ds.data.prev_name);

          $('#detailModal').modal('show');
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
		closeOnConfirm: true
  },function(){
    setTimeout(() => {
      doDelete(id);
    }, 200);
  })
}


function doDelete(id) {
  load_in();

  $.ajax({
    url:HOME + 'delete',
    type:'POST',
    cache:false,
    data:{
      'id' : id
    },
    success:function(rs) {
      load_out();

      if(rs === 'success') {
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
          text:rs,
          type:'error'
        })
      }
    }
  })
}
