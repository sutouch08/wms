var HOME = BASE_URL + 'report/audit/backorder_item/';

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


function getReport() {
  clearErrorByClass('r');

  let h = {
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val()
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
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val()
  }

  let token = generateUID();
  $('#token').val(token);
  $('#filter').val(JSON.stringify(h));
  get_download(token);
  $('#exportForm').submit();
}
