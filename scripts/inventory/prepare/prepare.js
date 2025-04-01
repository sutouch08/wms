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


function toggleAllPc(el) {
  if(el.is(':checked')) {
    $('.pc').prop('checked', true);
  }
  else {
    $('.pc').prop('checked', false);
  }
}


function goToProcess() {
  let code = $('#order-code').val().trim();

  if(code.length) {
    goPrepare(code);
  }
}


$('#order-code').keyup(function(e) {
  if(e.keyCode === 13) {
    goToProcess();
  }
})


$('#barcode-order').keyup(function(e) {
  if(e.keyCode === 13) {
    let code = $(this).val().trim();
    if(code.length) {
      goPrepare(code, 'mobile');
    }
  }
})



function genPickList() {
  let limit = 100;
  let count = $('.pc:checked').length;

  if(count > limit) {
    beep();
    swal("เลือกได้ไม่เกิน "+limit+" ออเดอร์");
    return false;
  }

  if(count > 0 && count <= limit) {
    let h = [];

    $('.pc:checked').each(function(){
      let code = $(this).val();
      h.push({'code' : code});
    });

    if(h.length) {
      var mapForm = document.createElement('form');
       mapForm.target = "Map";
       mapForm.method = "POST";
       mapForm.action = HOME + "/gen_pick_list";

       var mapInput = document.createElement("input");
       mapInput.type = "hidden";
       mapInput.name = "data";
       mapInput.value = JSON.stringify(h);

       mapForm.appendChild(mapInput);

       document.body.appendChild(mapForm);

       map = window.open("", "Map", "status=0,title=0,height=900,width=800,scrollbars=1");

       if(map) {
         mapForm.submit();
       }
       else {
         swal('You must allow popups for this map to work.');
       }
    }
  }
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
