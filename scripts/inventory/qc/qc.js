var HOME = BASE_URL + 'inventory/qc/';

function goBack(){
  window.location.href = HOME;
}


//--- ต้องการจัดสินค้า
function goQc(code, view){
  if(view === undefined) {
    window.location.href = HOME + 'process/'+code;
  }
  else {
    window.location.href = HOME + 'process/'+code+'/mobile';
  }
}


function viewProcess(){
  window.location.href = HOME + 'view_process';
}


function refresh() {
  window.location.reload();
}

//---- กำหนดค่าการแสดงผลที่เก็บสินค้า เมื่อมีการคลิกปุ่มที่เก็บ
$(function () {
  $('.btn-pop').popover({html:true});
});


function confirmCanceledOrder(code) {

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/orders/order_state_change',
    type:'POST',
    cache:false,
    data:{
      'order_code' : code,
      'state' : 9,
      'force_cancel' : 1,
      'reason_id' : 4,
      'cancle_reason' : "ออเดอร์ถูกยกเลิกบน platform แล้ว"
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Canceled',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          goBack();
        }, 1200);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}
