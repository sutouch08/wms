
var validPwd = true;
function changePassword(){
  var id = $('#user_id').val();
  var pwd = $('#pwd').val();
  var cmp = $('#pwd').val();
  if(pwd.length == 0 || cmp.length == 0){
    validPWD();
  }

  if(! validPwd){
    return false;
  }

  $('#resetForm').submit();
}


function validPWD(){
  var pwd = $('#pwd').val();
  var cmp = $('#cm-pwd').val();
  if(pwd.length > 0 && cmp.length > 0){
    if(pwd != cmp){
      $('#cm-pwd-error').text('Password missmatch!');
      $('#pwd').addClass('has-error');
      $('#cm-pwd').addClass('has-error');
      validPwd = false;
    }else{
      $('#cm-pwd-error').text('');
      $('#pwd').removeClass('has-error');
      $('#cm-pwd').removeClass('has-error');
      validPwd = true;
    }
  }else{
    $('#cm-pwd-error').text('Password is required!');
    $('#pwd').addClass('has-error');
    $('#cm-pwd').addClass('has-error');
    validPwd = false;
  }
}


$('#pwd').focusout(function(){
  validPWD();
})



$('#pwd').keyup(function(e){
  validPWD();
});



$('#cm-pwd').keyup(function(e){
  validPWD(e);
})


var validkey = true;

function change_skey(){
  let skey = $('#skey').val();
  let cmskey = $('#cm-skey').val();
  let uid = $('#uid').val();

  if(skey.length === 0 || cmskey.length === 0){
    validSkey();
  }

  if(! validSkey){
    return false;
  }

  $.ajax({
    url:BASE_URL + 'user_pwd/change_skey',
    type:'POST',
    cache:false,
    data:{
      'uid' : uid,
      'skey' : skey
    },
    success:function(rs){
      if(rs === 'success'){
        swal({
          title:'Updated',
          text:'Secret key changed',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function validSkey(){
  var pwd = $('#skey').val();
  var cmp = $('#cm-skey').val();
  if(pwd.length > 0 && cmp.length > 0){
    if(pwd != cmp){
      $('#cm-skey-error').text('Secret key missmatch!');
      $('#skey').addClass('has-error');
      $('#cm-skey').addClass('has-error');
      validkey = false;
    }else{
      $('#cm-skey-error').text('');
      $('#skey').removeClass('has-error');
      $('#cm-skey').removeClass('has-error');
      validkey = true;
    }
  }else{
    $('#cm-skey-error').text('Secret key is required!');
    $('#skey').addClass('has-error');
    $('#cm-skey').addClass('has-error');
    validkey = false;
  }
}


$('#skey').focusout(function(){
  validSkey();
})



$('#skey').keyup(function(e){
  validSkey();
});



$('#cm-skey').keyup(function(e){
  validSkey();
})
