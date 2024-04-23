function addNew(){
  window.location.href = BASE_URL + 'masters/product_size/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/product_size';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/product_size/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/product_size/clear_filter';
  var page = BASE_URL + 'masters/product_size';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function add() {
  $('.r').removeClass('has-error');
  $('.e').text('');
  let code = $.trim($('#code').val());
  let name = $.trim($('#name').val());
  let position = $('#position').val();

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

  if(position == '' || position == undefined) {
    $('#pos-error').text('required');
    $('#position').addClass('has-error');
    return false;
  }

  $.ajax({
    url:BASE_URL + 'masters/product_size/add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'position' : position
    },
    success:function(rs) {
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
  $('.r').removeClass('has-error');
  $('.e').text('');

  let code = $('#code').val();
  let name = $('#name').val();
  let position = parseDefault(parseInt($('#position').val()), 0);

  if(name.length == 0) {
    $('#name-error').text('required');
    $('#name').addClass('has-error');
    return false;
  }

  $.ajax({
    url:BASE_URL + 'masters/product_size/update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'name' : name,
      'position' : position
    },
    success:function(rs) {
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
  },function() {
    setTimeout(() => {
      load_in();
      $.ajax({
        url:BASE_URL + 'masters/product_size/delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();
          if(rs == 'success') {
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
            })
          }
        }
      })
    }, 200)
  })
}



function getSearch(){
  $('#searchForm').submit();
}
