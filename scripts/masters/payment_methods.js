var HOME = BASE_URL + 'masters/payment_methods/';
function addNew(){
  window.location.href = BASE_URL + 'masters/payment_methods/add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/payment_methods/edit/'+code;
}


function add() {
  let code = $('#code').val();
  let name = $('#name').val();
  let role = $('#role').val();

  if(code.length == 0) {
    $('#code-error').text('Required');
    $('#code').addClass('has-error');
    return false;
  }
  else {
    $('#code-error').text('');
    $('#code').removeClass('has-error');
  }

  if(name.lenght == 0) {
    $('#name-error').text('Required');
    $('#name').addClass('has-error');
    return false;
  }
  else {
    $('#name-error').text('');
    $('#name').removeClass('has-error');
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'role' : role
    },
    success:function(rs) {
      load_out();

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer: 1000
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
  });
}


function update() {
  let id = $('#id').val();
  let code = $('#code').val();
  let name = $('#name').val();
  let role = $('#role').val();

  if(code.length == 0) {
    $('#code-error').text('Required');
    $('#code').addClass('has-error');
    return false;
  }
  else {
    $('#code-error').text('');
    $('#code').removeClass('has-error');
  }

  if(name.lenght == 0) {
    $('#name-error').text('Required');
    $('#name').addClass('has-error');
    return false;
  }
  else {
    $('#name-error').text('');
    $('#name').removeClass('has-error');
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'name' : name,
      'role' : role
    },
    success:function(rs) {
      load_out();

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer: 1000
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
  });
}

function clearFilter(){
  var url = BASE_URL + 'masters/payment_methods/clear_filter';
  var page = BASE_URL + 'masters/payment_methods';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function getDelete(code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    window.location.href = BASE_URL + 'masters/payment_methods/delete/' + code;
  })
}



function getSearch(){
  $('#searchForm').submit();
}



function check(){
  if($('#term-check').is(":checked")){
    $('#term').val(1);
  }else{
    $('#term').val(0);
  }

  //console.log($('#term').val());
  getSearch();
}
