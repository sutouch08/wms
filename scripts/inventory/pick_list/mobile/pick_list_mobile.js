
function toggleFilter() {
  let filter = $('#filter-pad');

  if(filter.hasClass('move-in')) {
    filter.removeClass('move-in');
  }
  else {
    filter.addClass('move-in');
  }
}


function closeFilter() {
  $('#filter-pad').removeClass('move-in');
}
