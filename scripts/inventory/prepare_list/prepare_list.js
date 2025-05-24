function goBack(){
  window.location.href = BASE_URL + 'inventory/prepare_list';
}

function getSearch(){
  $('#searchForm').submit();
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
})


function clearFilter(){
  $.get(BASE_URL + 'inventory/prepare_list/clear_filter', function(){
    goBack();
  })
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});


function exportFilter() {
  let code = $('#order-code').val().trim();
  let pd_code = $('#pd-code').val().trim();
  let zone_code = $('#zone-code').val().trim();
  let warehouse_code = $('#warehouse').val();
  let user = $('#user').val();
  let from_date = $('#fromDate').val().trim();
  let to_date = $('#toDate').val().trim();
  let token = generateUID();

  if( ! isDate(from_date) || ! isDate(to_date)) {
    swal("กรุณาระบุวันที่");
    return false;
  }

  $('#ex-order-code').val(code)
  $('#ex-pd-code').val(pd_code)
  $('#ex-whs-code').val(warehouse_code)
  $('#ex-zone-code').val(zone_code)
  $('#ex-user').val(user)
  $('#ex-form-date').val(from_date)
  $('#ex-to-date').val(to_date)
  $('#token').val(token)

  get_download(token);
  $('#export-form').submit();
}
