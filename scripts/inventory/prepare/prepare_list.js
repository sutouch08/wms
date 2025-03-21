
function getSearch(){
  $("#searchForm").submit();
}


function clearFilter(){
  $.get(HOME + '/clear_filter', function(){ goBack(); });
}


function clearProcessFilter(){
  $.get(HOME + '/clear_filter', function(){ viewProcess(); });
}


$(".search").keyup(function(e){
  if( e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#toDate").datepicker("option", "minDate", sd);
  }
});


$("#toDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose: function(sd){
    $("#fromDate").datepicker("option", "maxDate", sd);
  }
});


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

function toggleExtraMenu() {
  let hd = $('#extra');
  let pad = $('#extra-menu');

  if(hd.val() == "hide") {
    hd.val("show");
    pad.addClass('slide-in');
    setTimeout(() => {
      $('#barcode-order').focus();
    }, 500);
  }
  else {
    hd.val("hide");
    pad.removeClass('slide-in');
  }
}

//
// //---- Reload page every 5 minute
// $(document).ready(function(){
//   setInterval(function(){ goBack();}, 300000);
// });
