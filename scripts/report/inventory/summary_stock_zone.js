var HOME = BASE_URL + 'report/inventory/summary_stock_zone/';

function toggleOption(el) {
  if(el.is(':checked')) {
    $('.chk-row').prop('checked', true);
  }
  else {
    $('.chk-row').prop('checked', false);
  }
}


$('#item-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_product_code_and_name',
  minLength:2,
  autoFocus:true,
  close:function() {
    let arr = $(this).val().trim().split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
})

function getReport() {
  $('#item-code').clearError();
  let stockOption = $('.chk-stock:checked').val();

  let h = {
    'option' : stockOption,
    'itemOption' : $('#item-option').is(':checked') ? 1 : 0,
    'itemCode' : $('#item-code').val().trim(),
    'rows' : []
  }

  if(h.itemOption == 1 && h.itemCode == "") {
    swal({
      title:'Oop!',
      text:'ในกรณีที่ต้องการกรองด้วยรหัสสินค้า <br/>กรุณาระบุรหัสสินค้าด้วย',
      type:'warning',
      html:true
    });

    $('#item-code').hasError();
    return false;
  }

  if(h.itemOption == 1 && stockOption == 'E') {
    swal({
      title:'Oop!',
      text:'ในกรณีที่ต้องการกรองด้วยรหัสสินค้า <br/>ไม่สามารถเลือกเฉพาะโซนที่ว่างได้',
      type:'warning',
      html:true
    });

    return false;
  }

  $('.chk-row:checked').each(function() {
    h.rows.push($(this).val());
  });


  if(h.rows.length == 0) {
    swal({
      title:'Error',
      text:"กรุณาเลือก ROW อย่างน้อย 1 ROW",
      type:'warning'
    });

    return false;
  }



  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
          let output = $('#result');

          render(source, ds.data, output);
        }
        else {
          beep();
          showError(ds.message);
        }
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
}


function getItemStockZone(binAbs) {
  let width = 400;
  let height = 600;
  let left = (window.innerWidth - width) / 2;
  let target = HOME + 'getItemStockZone/'+binAbs + '?nomenu';
  let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
  window.open(target, '_blank', prop);
}
