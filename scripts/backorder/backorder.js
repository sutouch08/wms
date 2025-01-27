var HOME = BASE_URL + 'orders/backorder/';

function goBack(){
  window.location.href = HOME;
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){ goBack(); });
}


function getSearch(){
  $('#searchForm').submit();
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#from-date").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#to-date").datepicker("option", "minDate", ds);
	}
});

$("#to-date").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#from-date").datepicker("option", "maxDate", ds);
	}
});
