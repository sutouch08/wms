var HOME = BASE_URL + 'report/audit/backorder_item/';

//--- Date picker
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


function toggleAllChannels(option){
  $('#allChannels').val(option);
  if(option == 1){
    $('#btn-channels-all').addClass('btn-primary');
    $('#btn-channels-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-channels-all').removeClass('btn-primary');
    $('#btn-channels-range').addClass('btn-primary');
    $('#channels-modal').modal('show');
  }
}

function toggleAllRole(option){
  $('#allRole').val(option);

  if(option == 1) {
    $('#btn-role-all').addClass('btn-primary');
    $('#btn-role-range').removeClass('btn-primary');
    return
  }

  if(option == 0) {
    $('#btn-role-all').removeClass('btn-primary');
    $('#btn-role-range').addClass('btn-primary');
    $('#role-modal').modal('show');
  }
}


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
    $('#warehouse-modal').modal('show');
  }
}


function getReport() {
  clearErrorByClass('r');

  let h = {
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val(),
    'allChannels' : $('#allChannels').val(),
    'allRole' : $('#allRole').val(),
    'allWarehouse' : $('#allWarehouse').val(),
    'channels' : [],
    'role' : [],
    'warehouse' : []
  }

  let countChannels = $('.ch-chk:checked').length;
  let countWarehouse = $('.wh-chk:checked').length;
  let countRole = $('.role-chk:checked').length;

  if(h.allRole == '0' && countRole === 0) {
    $('#role-modal').modal('show');
    return false;
  }

  if(h.allChannels === '0' && countChannels === 0){
    $('#channels-modal').modal('show');
    return false;
  }

  if(h.allWarehouse === '0' && countWarehouse === 0){
    $('#warehouse-modal').modal('show');
    return false;
  }

  if(countRole > 0) {
    $('.role-chk:checked').each(function() {
      h.role.push($(this).val());
    });
  }

  if(countChannels > 0) {
    $('.ch-chk:checked').each(function() {
      h.channels.push($(this).val());
    });
  }

  if(countWarehouse > 0) {
    $('.wh-chk:checked').each(function() {
      h.warehouse.push($(this).val());
    });
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data: {
      "json" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
          let output = $('#result-table');

          render(source, ds.data, output);
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
  });
}



function doExport() {
  clearErrorByClass('r');

  let h = {
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val(),
    'allChannels' : $('#allChannels').val(),
    'allRole' : $('#allRole').val(),
    'allWarehouse' : $('#allWarehouse').val(),
    'channels' : [],
    'role' : [],
    'warehouse' : []
  }

  let countChannels = $('.ch-chk:checked').length;
  let countWarehouse = $('.wh-chk:checked').length;
  let countRole = $('.role-chk:checked').length;

  if(h.allRole == '0' && countRole === 0) {
    $('#role-modal').modal('show');
    return false;
  }

  if(h.allChannels === '0' && countChannels === 0){
    $('#channels-modal').modal('show');
    return false;
  }

  if(h.allWarehouse === '0' && countWarehouse === 0){
    $('#warehouse-modal').modal('show');
    return false;
  }

  if(countRole > 0) {
    $('.role-chk:checked').each(function() {
      h.role.push($(this).val());
    });
  }

  if(countChannels > 0) {
    $('.ch-chk:checked').each(function() {
      h.channels.push($(this).val());
    });
  }

  if(countWarehouse > 0) {
    $('.wh-chk:checked').each(function() {
      h.warehouse.push($(this).val());
    });
  }

  let token = generateUID();
  $('#token').val(token);
  $('#filter').val(JSON.stringify(h));
  get_download(token);
  $('#exportForm').submit();
}
