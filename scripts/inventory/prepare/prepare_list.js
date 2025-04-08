
function getSearch(){
  $("#searchForm").submit();
}


function clearFilter(){
  $.get(HOME + '/clear_filter', function(){ goBack(); });
}


function clearProcessFilter(){
  $.get(HOME + '/clear_filter', function(){ viewProcess(); });
}


$(".search").keyup(function(e){
  if( e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#toDate").datepicker("option", "minDate", sd);
  }
});


$("#toDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#fromDate").datepicker("option", "maxDate", sd);
  }
});


function toggleFilter() {
  let filter = $('#filter');
  let pad = $('#filter-pad');

  if(filter.val() == "hide") {
    filter.val("show");
    pad.addClass('move-in');
  }
  else {
    filter.val("hide");
    pad.removeClass('move-in');
  }
}

function toggleExtraMenu() {
  let hd = $('#extra');
  let pad = $('#extra-menu');

  if(hd.val() == "hide") {
    hd.val("show");
    pad.addClass('slide-in');
    setTimeout(() => {
      $('#barcode-order').focus();
    }, 500);
  }
  else {
    hd.val("hide");
    pad.removeClass('slide-in');
  }
}


function updateBackorder(option) {
  let count = $('.pc:checked').length;

  if(count) {
    let msg = "";
    if(option == '1') {
      msg = "ต้องการเปลี่ยนรายการที่เลือกให้เป็น Backorder หรือไม่ ?";
    }
    else {
      msg = "ต้องการเปลี่ยนรายการที่เลือกออกจากสถานะ Backorder หรือไม่ ?";
    }

    let h = {
      'option' : option,
      'orders' : []
    };

    $('.pc:checked').each(function() {
      h.orders.push($(this).val());
    });


    swal({
      title:'Backorder',
      text:msg,
      type:'warning',
      showCancelButton:true,
      cancelButtonText:'No',
      confirmButtonText:'Yes',
      closeOnConfirm:true
    }, function() {
      setTimeout(() => {
        load_in();

        $.ajax({
          url:HOME + '/update_back_order',
          type:'POST',
          cache:false,
          data:{
            'data' : JSON.stringify(h)
          },
          success:function(rs) {
            load_out();

            if(rs.trim() === 'success') {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(() => {
                refresh();
              }, 1200);
            }
            else {
              beep();
              showError(rs);
            }
          },
          error:function(rs) {
            beep();
            showError(rs);
          }
        })
      }, 100);
    });
  }
}
