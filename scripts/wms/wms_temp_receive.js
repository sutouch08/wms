var HOME = BASE_URL + 'rest/V1/wms_temp_receive/';

function goBack() {
	window.location.href = HOME;
}


function getSearch() {
	$('#searchForm').submit();
}

function clearFilter() {
	$.get(HOME + 'clear_filter', function() {
		goBack();
	})
}


function getDetails(id) {
	//--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= HOME + 'get_detail/'+id+'?nomenu';
	window.open(target, "_blank", prop );
}
