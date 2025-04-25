
var chk = setInterval(function () { checkState(); }, 90000);



function checkState(){
  var order_code = $("#order_code").val();
  $.ajax({
    url: HOME + 'get_state',
    type: 'GET',
    data: {
      'order_code' : order_code
    },
    success: function(rs){
      var rs = $.trim(rs);
      if( rs == '8'){
        $("#btn-confirm-order").remove();
        clearInterval(chk);
      }
    }
  });
}

var click = 0;

function confirmOrder() {
  if(click == 0) {
    click = 1;

    let order_code = $("#order_code").val();

    load_in();

    $.ajax({
      url: HOME + 'confirm_order',
      type:'POST',
      cache:'false',
      data:{
        'order_code' : order_code
      },
      success:function(rs){
        load_out();
        if( rs.trim() == 'success'){
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            window.location.reload();
          },1200);
        }
        else {
          click = 0;
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        click = 0;
        beep();
        showError(rs);
      }
    });
  }
}
