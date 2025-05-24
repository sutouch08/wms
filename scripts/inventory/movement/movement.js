var HOME = BASE_URL + 'inventory/movement/';

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose: (sd) => {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose: (sd) => {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function getSearch() {
  load_in();
  $('#searchForm').submit();
}


$('.search-box').keyup((e) => {
  if(e.keyCode == 13) {
    getSearch();
  }
});


function clearFilter() {
  load_in();

  $.ajax({
    url : HOME + 'clear_filter',
    type:'POST',
    cache:false,
    success:() => {
      window.location.href = HOME;
    }
  });
}


function exportFilter() {
  let token = generateUID()
  let ref = $('#ref').val().trim()
  let pd = $('#pd-code').val().trim()
  let wh = $('#warehouse').val()
  let zone = $('#zone-code').val().trim()
  let from = $('#fromDate').val().trim()
  let to = $('#toDate').val().trim()

  if( ! isDate(from) || ! isDate(to))
  {
    swal("กรุณาระบุวันที่");
    return false;
  }

  $('#ex-ref').val(ref);
  $('#ex-pd-code').val(pd)
  $('#ex-whs-code').val(wh)
  $('#ex-zone-code').val(zone)
  $('#ex-from-date').val(from)
  $('#ex-to-date').val(to)
  $('#token').val(token)

  get_download(token);

  $('#export-form').submit();
}
