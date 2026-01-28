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


function doExport() {
  $('#min-stock').clearError();

  let h = {
    'zone_code' : $('#zone-code').val().trim(),
    'product_code' : $('#pd-code').val().trim(),
    'min_stock' : $('#min-stock').val().trim(),
    'is_min' : $('#is-min').val()
  }

  if(h.min_stock <= 0) {
    $('#min-stock').hasError();
    return false;
  }

  let token = generateUID();

  $('#data').val(JSON.stringify(h));
  $('#token').val(token);

  $('#export-form').submit();
  get_download(token);
}

function printQr() {
  $('#min-stock').clearError();

  let h = {
    'zone_code' : $('#zone-code').val().trim(),
    'product_code' : $('#pd-code').val().trim(),
    'min_stock' : $('#min-stock').val().trim(),
    'is_min' : $('#is-min').val()
  }

  if(h.min_stock <= 0) {
    $('#min-stock').hasError();
    return false;
  }

  var mapForm = document.createElement('form');
  mapForm.target = "Map";
  mapForm.method = "POST";
  mapForm.action = HOME + "print_qr";

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
