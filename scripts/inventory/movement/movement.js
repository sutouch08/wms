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
  let range = $('#data-range').val()

  if(! isDate(from) && ! isDate(to) && ref.length == 0 && pd.length == 0 && wh == 'all' && zone.length == 0) {
    swal("กรุณาระบุตัวกรองอย่างน้อย 1 ตัวกรอง");
    return false;
  }

  $('#ex-ref').val(ref);
  $('#ex-pd-code').val(pd)
  $('#ex-whs-code').val(wh)
  $('#ex-zone-code').val(zone)
  $('#ex-from-date').val(from)
  $('#ex-to-date').val(to)
  $('#ex-data-range').val(range);
  $('#token').val(token)

  get_download(token);

  $('#export-form').submit();
}
