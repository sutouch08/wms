
$(document).ready(function(){
  var interv = setInterval(function(){ goBack(); }, 300000);
});


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose: function(sd){
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function getSearch(){
  $('#searchForm').submit();
}


$('.search').keyup(function(e) {
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}


function clearProcessFilter(){
  $.get(HOME + 'clear_filter', function(){
    viewProcess();
  });
}


function toggleFilter() {
  let filter = $('#filter');
  let pad = $('#filter-pad');

  if(filter.val() == "hide") {
    filter.val("show");
    pad.addClass('move-in');
  }
  else {
    filter.val("hide");
    pad.removeClass('move-in');
  }
}
