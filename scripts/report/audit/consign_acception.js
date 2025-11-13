var HOME = BASE_URL + 'report/audit/consign_acception/';

window.addEventListener('load', () => {
  customerInit();
  zoneInit();
});

//--- Date picker
$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});


function customerInit() {
  $('#customer-code').autocomplete({
    source:HOME + 'customer_code_and_name',
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2) {
        $('#customer-code').val(arr[0]);
        $('#customer-name').val(arr[1]);

        zoneInit();

        $('#zone-code').focus();
      }
      else {
        $('#customer-code').val('');
        $('#customer-name').val('');
      }
    }
  });
}


function zoneInit() {
  let customer_code = $('#customer-code').val().trim();
  $('#zone-code').autocomplete({
    source:HOME + 'zone_customer/' + customer_code,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2) {
        $('#zone-code').val(arr[0]);
        $('#zone-name').val(arr[1]);
      }
      else {
        $('#zone-code').val('');
        $('#zone-name').val('');
      }
    }
  })
}


function getReport() {
  clearErrorByClass('r');

  let h = {
    'customer_code' : $('#customer-code').val().trim(),
    'zone_code' : $('#zone-code').val().trim(),
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val(),
    'date_type' : $('#date-type').val(),
    'is_accept' : $('#is-accept').val(),
    'is_complete' : $('#is-complete').val()
  }

  if( ! isDate(h.from_date) || ! isDate(h.to_date)) {
    showError('กรุณาระบุวันที่');
    $('#fromDate').hasError();
    $('#toDate').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data: {
      "json" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
          let output = $('#result-table');

          render(source, ds.data, output);
        }
        else {
          showError(ds.message);
        }
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



function doExport() {
  clearErrorByClass('r');

  let h = {
    'customer_code' : $('#customer-code').val().trim(),
    'zone_code' : $('#zone-code').val().trim(),
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val(),
    'date_type' : $('#date-type').val(),
    'is_accept' : $('#is-accept').val(),
    'is_complete' : $('#is-complete').val()
  }

  if( ! isDate(h.from_date) || ! isDate(h.to_date)) {
    showError('กรุณาระบุวันที่');
    $('#fromDate').hasError();
    $('#toDate').hasError();
    return false;
  }

  let token = generateUID();
  $('#token').val(token);
  $('#filter').val(JSON.stringify(h));
  get_download(token);
  $('#exportForm').submit();
}
