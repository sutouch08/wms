var HOME = `${BASE_URL}masters/weighing_machine/`;

function goBack() {
  window.location.href = HOME;
}

function clearFilter() {
  const url = `${HOME}clear_filter`;
  $.get(url, function() {
    goBack();
  });
}

function getSearch() {
  $('#searchForm').submit();
}

function addNew() {
  clearErrorByClass('r');
  $('#action').val('add');
  $('#device-uid').val('');
  $('#device-id').val('');
  $('#device-code').val('');
  $('#device-unit').val('kg');
  $('#device-port').val('RS-232');
  $('#device-name').val('');
  $('input[name="active"][value="1"]').prop('checked', true);
  $('#device-baud-rate').val('9600');
  $('#device-data-bits').val('8');
  $('#device-stop-bits').val('1');
  $('#device-parity').val('none');
  $('#result-message').val('');
  $('#weight').val('');
  $('#save-btn').text('Add');
  $('#add-modal').modal('show');
}
  

$('#add-modal').on('shown.bs.modal', function () {
  $('#device-code').focus();
})


function edit(id) {
  $('#action').val('edit');
  clearErrorByClass('r');
  $.ajax({
    url:`${HOME}get_data/${id}`,
    type:'GET',
    cache:false,
    success:function(rs) {
      let ds = JSON.parse(rs);
      if(ds.status === 'success') {
        let data = ds.data;
        $('#device-id').val(data.id);
        $('#device-uid').val(data.deviceId);
        $('#device-code').val(data.deviceCode);
        $('#device-name').val(data.deviceName);
        $('#device-port').val(data.devicePort);
        $('#device-unit').val(data.deviceUnit);
        $('#device-baud-rate').val(data.deviceBaudRate);
        $('#device-data-bits').val(data.deviceDataBits);
        $('#device-stop-bits').val(data.deviceStopBits);
        $('#device-parity').val(data.deviceParity);
        $('input[name="active"][value="'+data.active+'"]').prop('checked', true);
        $('#result-message').val('');
        $('#weight').val('');
        $('#save-btn').text('Update');
        $('#add-modal').modal('show');        
      }
      else {
        showError(ds.message);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}

function save() {
  disconnect(); // disconnect if port is open before saving
  const action = $('#action').val();

  if(action === 'add') {
    add();
  }

  if(action === 'edit') {
    update();
  }
}

function add() {
  clearErrorByClass('r');

  let ds = {
    'deviceId': generateUUID(),
    'deviceCode': $('#device-code').val().trim(),
    'deviceName': $('#device-name').val().trim(),
    'active': $('input[name="active"]:checked').val(),
    'devicePort': $('#device-port').val(),
    'deviceUnit': $('#device-unit').val(),
    'deviceBaudRate': parseInt($('#device-baud-rate').val()),
    'deviceDataBits': parseInt($('#device-data-bits').val()),
    'deviceStopBits': parseInt($('#device-stop-bits').val()),
    'deviceParity': $('#device-parity').val()
  }

  if (ds.deviceCode.length == 0) {
    $('#device-code').hasError();
    $('#result-message').val("กรุณาระบุรหัสเครื่องชั่ง");
    return false;
  }

  if (ds.deviceName.length == 0) {
    $('#device-name').hasError();
    $('#result-message').val("กรุณาระบุชื่อเครื่องชั่ง");
    return false;
  }

  $('#add-modal').modal('hide');

  load_in();

  $.ajax({
    url:`${HOME}add`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(ds)
    },
    success:function(rs) {
      load_out();
      let ds = JSON.parse(rs);
      if(ds.status === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        let source = $('#rows-template').html();
        let output = $('#device-table');

        render_append(source, ds.data, output);
        reIndex();
      }
      else {
        swal({
          title:'Error!',
          text:ds.message,
          type:'error'
        });
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}

function update() {
  clearErrorByClass('r');

  let ds = {
    'id' : $('#device-id').val(),
    'deviceId': $('#device-uid').val(),
    'deviceCode': $('#device-code').val().trim(),
    'deviceName': $('#device-name').val().trim(),
    'active': $('input[name="active"]:checked').val(),
    'devicePort': $('#device-port').val(),
    'deviceUnit': $('#device-unit').val(),
    'deviceBaudRate': parseInt($('#device-baud-rate').val()),
    'deviceDataBits': parseInt($('#device-data-bits').val()),
    'deviceStopBits': parseInt($('#device-stop-bits').val()),
    'deviceParity': $('#device-parity').val()
  }

  if (ds.deviceCode.length == 0) {
    $('#device-code').hasError();
    $('#result-message').val("กรุณาระบุรหัสเครื่องชั่ง");
    return false;
  }

  if (ds.deviceName.length == 0) {
    $('#device-name').hasError();
    $('#result-message').val("กรุณาระบุชื่อเครื่องชั่ง");
    return false;
  }

  $('#add-modal').modal('hide');

  load_in();

  $.ajax({
    url:`${HOME}update`,
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(ds)
    },
    success:function(rs) {
      load_out();
      let ds = JSON.parse(rs);
      if(ds.status === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        let source = $('#edit-rows-template').html();
        let output = $('#row-'+ds.id);

        render(source, ds.data, output);
        reIndex();
      }
      else {
        swal({
          title:'Error!',
          text:ds.message,
          type:'error'
        });
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}

function remove(id, code) {
  swal({
    title: 'Are you sure ?',
    text: 'ต้องการลบ ' + code + ' หรือไม่ ?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function(isConfirm) {
    if (isConfirm) {
      $.ajax({
        url: `${HOME}remove`,
        type: 'POST',
        cache: false,
        data: {
          'id': id
        },
        success: function(rs) {
          let ds = JSON.parse(rs);
          if (ds.status === 'success') {
            $('#row-' + id).remove();
            reIndex();
          }
          else {
            swal({
              title: 'Error!',
              text: ds.message,
              type: 'error'
            });
          }
        },
        error: function(rs) {
          showError(rs);
        }
      });
    }
  });
}