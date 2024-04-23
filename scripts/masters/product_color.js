function addNew(){
  window.location.href = BASE_URL + 'masters/product_color/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/product_color';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/product_color/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/product_color/clear_filter';
  var page = BASE_URL + 'masters/product_color';
  $.get(url, function(rs){
    window.location.href = page;
  });
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
      load_in();

      $.ajax({
        url:BASE_URL + 'masters/product_color/delete',
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
    }, 200);
  })
}


function toggleActive(option, code)
{
  $.ajax({
    url:BASE_URL + 'masters/product_color/set_active',
    type:'POST',
    cache:'false',
    data:{
      'code' : code,
      'active' : option
    },
    success:function(rs){
      if(rs != ''){
        $('#'+code).html(rs);
      }
    }
  });
}


function getSearch(){
  $('#searchForm').submit();
}


function add() {
  $('.r').removeClass('has-error');
  $('.e').text('');

  let code = $('#code').val();
  let name = $('#name').val();
  let group = $('#color_group').val();

  if(code.length == 0) {
    $('#code-error').text('required');
    $('#code').addClass('has-error');
    return false;
  }

  if(name.length == 0) {
    $('#name-error').text('required');
    $('#name').addClass('has-error');
    return false;
  }

  if(group == "") {
    $('#group-error').text('required');
    $('#color_group').addClass('has-error');
    return false;
  }

  load_in();

  $.ajax({
    url:BASE_URL + 'masters/product_color/add',
    type:'POST',
    cache:false,
    data: {
      'code' : code,
      'name' : name,
      'group' : group
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
        })
      }
    }
  })
}


function update(id) {
  $('.r').removeClass('has-error');
  $('.e').text('');

  let code = $('#code').val();
  let name = $('#name').val();
  let group = $('#color_group').val();

  if(name.length == 0) {
    $('#name-error').text('required');
    $('#name').addClass('has-error');
    return false;
  }

  if(group == "") {
    $('#group-error').text('required');
    $('#color_group').addClass('has-error');
    return false;
  }

  load_in();

  $.ajax({
    url:BASE_URL + 'masters/product_color/update',
    type:'POST',
    cache:false,
    data: {
      'id' : id,
      'code' : code,
      'name' : name,
      'group' : group
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
        })
      }
    }
  })
}
