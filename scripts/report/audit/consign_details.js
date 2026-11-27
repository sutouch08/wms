function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#to-whs-modal').modal('show');
  }
}


function toggleAllState(option){
  $('#allState').val(option);

  if(option == 1) {
    $('#btn-st-all').addClass('btn-primary');
    $('#btn-st-range').removeClass('btn-primary');
    return
  }

  if(option == 0) {
    $('#btn-st-all').removeClass('btn-primary');
    $('#btn-st-range').addClass('btn-primary');
    $('#state-modal').modal('show');
  }
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});


function getReport() {
  let h = {
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val(),   
    'fromWhsCode' : $('#from-whs').val(),
    'allWarehouse' : $('#allWarehouse').val(),
    'warehouseList' : []
  };

  if(! isDate(h.from_date) || ! isDate(h.to_date)) {
    swal("กรุณาระบุวันที่");
    return false;
  }
  
  if(h.fromWhsCode == '') {
    swal("กรุณาเลือกคลังต้นทาง");
    return false;
  }

  if(h.allWarehouse == '0' && $('.wh-chk:checked').length === 0) {    
    $('#to-whs-modal').modal('show');
    return false;
  }
    
  if(h.allWarehouse == '0') {
    $('.wh-chk:checked').each(function() {
      h.warehouseList.push($(this).val());
    });
  }

  load_in();

  $.ajax({
    url:`${HOME}get_report`,
    type:'POST',
    cache: false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status === 'success') {
          let template = $('#template').html();
          let output = $('#report-table');

          render(template, ds.data, output);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function doExport(){
  let h = {
    'from_date': $('#fromDate').val(),
    'to_date': $('#toDate').val(),
    'fromWhsCode': $('#from-whs').val(),
    'allWarehouse': $('#allWarehouse').val(),
    'warehouseList': []
  };

  if (!isDate(h.from_date) || !isDate(h.to_date)) {
    swal("กรุณาระบุวันที่");
    return false;
  }

  if (h.fromWhsCode == '') {
    swal("กรุณาเลือกคลังต้นทาง");
    return false;
  }

  if (h.allWarehouse == '0' && $('.wh-chk:checked').length === 0) {
    $('#to-whs-modal').modal('show');
    return false;
  }

  if (h.allWarehouse == '0') {
    $('.wh-chk:checked').each(function () {
      h.warehouseList.push($(this).val());
    });
  }

  let token = generateUID();
  $('#token').val(token);
  $('#data').val(JSON.stringify(h));  
  get_download(token);
  $('#export-form').submit();
}
