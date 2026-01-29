var HOME = BASE_URL + 'report/inventory/summary_stock_zone/';

function toggleOption(el) {
  if(el.is(':checked')) {
    $('.chk-row').prop('checked', true);
  }
  else {
    $('.chk-row').prop('checked', false);
  }
}


function getReport() {
  let stockOption = $('.chk-stock:checked').val();

  let h = {
    'option' : stockOption,
    'rows' : []
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
