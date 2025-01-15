var HOME = BASE_URL + 'inventory/prepare';


function goBack(){
    window.location.href = HOME;
}


function refresh() {
  load_in();
  setTimeout(() => {
    window.location.reload();
  }, 100);
}

//---- ไปหน้าจัดสินค้า
function goPrepare(code, view){
  if(view === undefined) {
    window.location.href = HOME + '/process/'+code;
  }
  else {
    window.location.href = HOME + '/process/'+code+'/mobile';
  }
}


function goProcess(view){
  window.location.href = HOME + '/view_process';
}


function goToBuffer() {
  window.location.href = BASE_URL + 'inventory/buffer';
}


function pullBack(code){
  $.ajax({
    url:HOME + '/pull_order_back',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        $('#row-'+code).remove();
        reIndex();
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

//--- ไปหน้ารายการที่กำลังจัดสินค้าอยู่
function viewProcess(){
  window.location.href = HOME + '/view_process';
}
