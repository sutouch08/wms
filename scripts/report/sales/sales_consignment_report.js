$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker("option", "minDate", sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker("option", "maxDate", sd);
  }
});

function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


$('#pdFrom').autocomplete({
  source : `${BASE_URL}auto_complete/get_style_code`,
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    let pdFrom = arr[0];
    $(this).val(pdFrom);
    let pdTo = $('#pdTo').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
});


$('#pdTo').autocomplete({
  source: `${BASE_URL}auto_complete/get_style_code`,
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    let pdTo = arr[0];
    $(this).val(pdTo);
    let pdFrom = $('#pdFrom').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
})


function toggleAllCustomer(option){
  $('#allCustomer').val(option);
  if(option == 1){
    $('#btn-cus-all').addClass('btn-primary');
    $('#btn-cus-range').removeClass('btn-primary');
    $('#cusFrom').val('');
    $('#cusFrom').attr('disabled', 'disabled');
    $('#cusTo').val('');
    $('#cusTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-cus-all').removeClass('btn-primary');
    $('#btn-cus-range').addClass('btn-primary');
    $('#cusFrom').removeAttr('disabled');
    $('#cusTo').removeAttr('disabled');
    $('#cusFrom').focus();
  }
}


$('#cusFrom').autocomplete({
  source : `${BASE_URL}auto_complete/get_customer_code_and_name`,
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    let cusFrom = arr[0];
    $(this).val(cusFrom);
    let cusTo = $('#cusTo').val();
    if(cusTo.length > 0 && cusFrom.length > 0){
      if(cusFrom > cusTo){
        $('#cusTo').val(cusFrom);
        $('#cusFrom').val(cusTo);
      }
    }
  }
});


$('#cusTo').autocomplete({
  source: `${BASE_URL}auto_complete/get_customer_code_and_name`,
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    let cusTo = arr[0];
    $(this).val(cusTo);
    let cusFrom = $('#cusFrom').val();
    if(cusTo.length > 0 && cusFrom.length > 0){
      if(cusFrom > cusTo){
        $('#cusTo').val(cusFrom);
        $('#cusFrom').val(cusTo);
      }
    }
  }
})

function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    $('.chk').removeAttr('checked');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }

  zone_init();
}



function toggleAllZone(option){
  $('#allZone').val(option);
  if(option == 1){
    $('#btn-zone-all').addClass('btn-primary');
    $('#btn-zone-range').removeClass('btn-primary');
    $('#zoneCode').val('');
    $('#zoneName').val('');
    $('#zoneName').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-zone-all').removeClass('btn-primary');
    $('#btn-zone-range').addClass('btn-primary');
    $('#zoneName').removeAttr('disabled');
    $('#zoneName').focus();
  }
}

function zone_init() {
  let warehouse = "";
  let i = 0;

  $('.chk:checked').each(function (index, el) {
    warehouse += (i === 0 ? $(this).val() : "|" + $(this).val());
    i++;
  });

  if (warehouse.length > 0) {
    warehouse = "/" + warehouse;
  }

  $('#zoneName').autocomplete({
    source: `${BASE_URL}auto_complete/get_zone_code_and_name${warehouse}`,
    autoFocus: true,
    close: function () {
      let val = $(this).val();
      let arr = val.split(' | ');
      if (arr.length == 2) {
        $(this).val(arr[1]);
        $('#zoneCode').val(arr[0]);
      }
      else {
        $(this).val('');
        $('#zoneCode').val('');
      }
    }
  })
}

$('.chk').change(function(){
  zone_init();
})

function getReport() {
  clearErrorByClass('r');

  const h = {
    "allProduct": $('#allProduct').val(),
    "pdFrom": $('#pdFrom').val(),
    "pdTo": $('#pdTo').val(),
    "allCustomer": $('#allCustomer').val(),
    "cusFrom": $('#cusFrom').val(),
    "cusTo": $('#cusTo').val(),
    "allWhouse": $('#allWarehouse').val(),
    "whsList": [],
    "allZone": $('#allZone').val(),
    "zoneCode": $('#zoneCode').val(),
    "fromDate": $('#fromDate').val(),
    "toDate": $('#toDate').val()
  };

  if (!isDate(h.fromDate) || !isDate(h.toDate)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if (h.allProduct == 0) {
    if (h.pdFrom.length == 0) {
      $('#pdFrom').hasError();
      return false;
    }

    if (h.pdTo.length == 0) {
      $('#pdTo').hasError();
      return false;
    }
  }

  if (h.allCustomer == 0) {
    if (h.cusFrom.length == 0) {
      $('#cusFrom').hasError();
      return false;
    }

    if (h.cusTo.length == 0) {
      $('#cusTo').hasError();
      return false;
    }
  }

  if (h.allWhouse == 0) {
    let count = $('.chk:checked').length;
    if (count == 0) {
      $('#wh-modal').modal('show');
      return false;
    }

    $('.chk:checked').each(function () {
      h.whsList.push($(this).val());
    });
  }

  if (h.allZone == 0) {
    if (h.zoneCode == '' || $('#zoneName').val().trim() == '') {
      $('#zoneName').hasError();
      return false;
    }
  }

  load_in();

  $.ajax({
    url: `${HOME}get_report`,
    type: 'GET',
    cache: 'false',
    data: {
      'data': JSON.stringify(h)
    },
    success: function (rs) {
      load_out();
      if (isJson(rs)) {
        let ds = JSON.parse(rs);
        let data = ds.data;
        let total = ds.total;
        let source = $('#template').html();
        let output = $('#data-row');
        render(source, data, output);

        $('#total-qty').text(total.totalQty);
        $('#total-discount').text(total.totalDiscount);
        $('#total-cost').text(total.totalCost);
        $('#total-amount').text(total.totalAmount);
      }
      else {
        showError(rs);
      }
    },
    error: function (rs) {
      showError(rs);
    }
  });
}

async function doExport() {
  clearErrorByClass('r');

  const h = {
    "allProduct": $('#allProduct').val(),
    "pdFrom": $('#pdFrom').val(),
    "pdTo": $('#pdTo').val(),
    "allCustomer": $('#allCustomer').val(),
    "cusFrom": $('#cusFrom').val(),
    "cusTo": $('#cusTo').val(),
    "allWhouse": $('#allWarehouse').val(),
    "whsList": [],
    "allZone": $('#allZone').val(),
    "zoneCode": $('#zoneCode').val(),
    "fromDate": $('#fromDate').val(),
    "toDate": $('#toDate').val()
  };

  if (!isDate(h.fromDate) || !isDate(h.toDate)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if (h.allProduct == 0) {
    if (h.pdFrom.length == 0) {
      $('#pdFrom').hasError();
      return false;
    }

    if (h.pdTo.length == 0) {
      $('#pdTo').hasError();
      return false;
    }
  }

  if (h.allCustomer == 0) {
    if (h.cusFrom.length == 0) {
      $('#cusFrom').hasError();
      return false;
    }

    if (h.cusTo.length == 0) {
      $('#cusTo').hasError();
      return false;
    }
  }

  if (h.allWhouse == 0) {
    let count = $('.chk:checked').length;
    if (count == 0) {
      $('#wh-modal').modal('show');
      return false;
    }

    $('.chk:checked').each(function () {
      h.whsList.push($(this).val());
    });
  }

  if (h.allZone == 0) {
    if (h.zoneCode == '' || $('#zoneName').val().trim() == '') {
      $('#zoneName').hasError();
      return false;
    }
  }

  let url = `${HOME}/count_export`;
  let filter = JSON.stringify(h);
  load_in('0%');
  const total = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: filter
  }).then(r => r.json());

  const limit = 5000;
  let offset = 0;
  while (offset < total) {
    const percent = Math.floor((offset / total) * 100);
    updateProgress(percent);
    const res = await fetch(`${HOME}/export_chunk/${total}/${limit}/${offset}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: filter
    }).then(r => r.json());    

    offset += res.offset;
  }

  load_out();
  window.location = `${HOME}/export_finished`;
}

function updateProgress(percent) {
  document.getElementsByClassName('loader-text')[0].innerText = percent + "%";
}


$(document).ready(function(){
  zone_init();
})
