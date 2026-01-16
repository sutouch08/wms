function viewProductionOrder(code) {
  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = HOME + 'sap_production_order/'+code+'?nomenu';
    let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
    window.open(target, '_blank', prop);
  }
}

function viewIXProductionOrder(code) {
  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = BASE_URL + 'productions/production_order/view_detail/'+code+'?nomenu';
    let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
    window.open(target, '_blank', prop);
  }
}


function toggleBatchRow(uid) {
  let el = $('#toggle-batch-row-'+uid);
  let option = el.data('option');
  option = option == 'show' ? 'hide' : 'show';

  if(option == 'show') {
    $('.child-of-'+uid).removeClass('hide');
    el.html('<i class="fa fa-minus fa-lg"></i>');
  }

  if(option == 'hide') {
    $('.child-of-'+uid).addClass('hide');
    el.html('<i class="fa fa-plus fa-lg"></i>');
  }

  el.data('option', option);
}
