var autoFocus = 1;

window.addEventListener('load',  () => {
  focus_init();
});

function focus_init() {
	$('.focus').focusout(function() {
		autoFocus = 1
		setTimeout(() => {
			if(autoFocus == 1) {
				setFocus();
			}
		}, 1000)
	})

	$('.focus').focusin(function() {
		autoFocus = 0;
	});
}

function setFocus() {
  if($('#order-add').hasClass('hide')) {
    $('#del-order-no').focus();
  }
  else {
    $('#order-no').focus();
  }
}

function showHeader() {
  autoFocus = 0;
  $('#header-pad').addClass('move-in');
}


function closeHeader() {
  autoFocus = 1;
  $('#header-pad').removeClass('move-in');
  setFocus();
}

function showRemoveOrder() {
  $('#order-add').addClass('hide')
  $('#order-del').removeClass('hide');

  $('#del-order-no').val('').focus();
}

function showAddOrder() {
  $('#order-del').addClass('hide');
  $('#order-add').removeClass('hide');
  $('#order-no').val('').focus();
}


function add() {
  clearErrorByClass('e');

  let h = {
    'date_add' : $('#date-add').val(),
    'channels_code' : $('#channels').val(),
    'channels_name' : $('#channels option:selected').data('name'),
    'sender_code' : $('#sender').val(),
    'sender_name' : $('#sender option:selected').text(),
    'plate_no' : $('#plate-no').val().trim(),
    'province' : $('#province').val().trim(),
    'driver_name' : $('#driver-name').val().trim(),
    'remark' : $('#remark').val().trim()
  };

  if(h.sender_code == "") {
    $('#sender').hasError();
    return false;
  }

  if(h.plate_no == "") {
    $('#plate-no').hasError();
    return false;
  }

  if(h.province == "") {
    $('#province').hasError();
    return false;
  }

  if(h.driver_name == "") {
    $('#driver-name').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          goEdit(ds.code);
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


function update() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val(),
    'date_add' : $('#date-add').val(),
    'channels_code' : $('#channels').val(),
    'channels_name' : $('#channels option:selected').data('name'),
    'sender_code' : $('#sender').val(),
    'sender_name' : $('#sender option:selected').text(),
    'plate_no' : $('#plate-no').val().trim(),
    'province' : $('#province').val().trim(),
    'driver_name' : $('#driver-name').val().trim(),
    'remark' : $('#remark').val().trim()
  };

  if(h.sender_code == "") {
    $('#sender').hasError();
    return false;
  }

  if(h.plate_no == "") {
    $('#plate-no').hasError();
    return false;
  }

  if(h.province == "") {
    $('#province').hasError();
    return false;
  }

  if(h.driver_name == "") {
    $('#driver-name').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          goEdit(ds.code);
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


function save() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'save',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          viewDetail(code);
        },1200);
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


function closeDispatch(code) {
  swal({
    title:'ปิดเอกสาร',
    text:'เมือปิดเอกสารแล้วจะไม่สามารแก้ไขได้อีก <br/>ต้องการปิดเอกสารหรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {

    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'close_dispatch',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              viewDetail(code);
            }, 1200);
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      })
    }, 100);
  })
}


function getEdit() {
  $('.e').removeAttr('disabled');

  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function addToDispatch() {
  let id = $('#id').val();
  let code = $('#code').val();
  let channels = $('#channels').val();
  let channels_name = $('#channels option:selected').data('name');
  let order_code = $('#order-no').val();

  $('#order-no').val('').attr('disabled', 'disabled');

  if(order_code.length) {
    load_in();

    $.ajax({
      url:HOME + 'add_to_dispatch',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'code' : code,
        'channels' : channels,
        'channels_name' : channels_name,
        'order_code' : order_code
      },
      success:function(rs) {
        load_out();
        $('#order-no').removeAttr('disabled');
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status === 'success') {
            let data = ds.data;

            if($('#dispatch-'+data.id).length) {
              let shipped = parseDefault(parseInt(data.carton_shipped), 1);
              let cartons = parseDefault(parseInt($('#carton-qty-'+data.id).val()), 1);

              $('#carton-shipped-'+data.id).val(shipped);
              $('#dispatch-'+data.id).prependTo($('#incomplete-box'));
            }
            else {
              let source = $('#row-template').html();
              let output = $('#incomplete-box');

              render_prepend(source, ds.data, output);

              let orderQty = parseDefault(parseInt(removeCommas($('#order-qty').val())), 0);
              let totalQty = parseDefault(parseInt(removeCommas($('#total-qty').val())), 0);

              if(orderQty > 0) {
                orderQty--;
              }

              totalQty++;

              $('#order-qty').val(addCommas(orderQty));
              $('#total-qty').val(addCommas(totalQty));
            }

            $('.dispatch-row').removeClass('heighlight');
            $('#dispatch-'+data.id).addClass('heighlight');

            recalBox();
            $('#order-no').focus();
          }
          else {
            beep();
            showError(ds.message);
          }
        }
      },
      error:function(rs) {
        $('#order-no').removeAttr('disabled').focus();
        beep();
        showError(rs);
      }
    })
  }
}


function reloadDispatch() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_dispatch_table',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#dispatch-template').html();
          let output = $('#dispatch-table');

          render(source, ds.data, output);
          recalBox();
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


function recalBox() {
  let totalQty = 0;
  let totalShipped = 0;

  $('.dispatch-row').each(function() {
    let id = $(this).data('id');
    let qty = parseDefault(parseInt($('#carton-qty-'+id).val()), 1);
    let shipped = parseDefault(parseInt($('#carton-shipped-'+id).val()), 1);

    totalQty += qty;
    totalShipped += shipped;
  });

  $('#total-carton').val(totalQty);
  $('#total-shipped').val(totalShipped);
}


function viewPending() {
  let code = $('#code').val();
  window.location.href = HOME + 'view_pending_order/'+code;
}


function removeOrder() {
  let code = $('#code').val();
  let order_code = $('#del-order-no').val().trim();

  if(order_code.length) {
    $('#del-order-no').val('');

    load_in();

    $.ajax({
      url:HOME + 'remove_detail',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'order_code' : order_code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let id = ds.id;

            if(ds.action == 'update') {
              let qty = parseDefault(parseInt($('#carton-shipped-'+id).val()), 1);
              qty--;
              $('#carton-shipped-'+id).val(qty);
            }
            else {
              $('#dispatch-'+id).remove();
            }

            let orderQty = parseDefault(parseInt(removeCommas($('#order-qty').val())), 0);
            let totalQty = parseDefault(parseInt(removeCommas($('#total-qty').val())), 0);

            orderQty++;
            totalQty = totalQty == 0 ? 0 : totalQty - 1;

            $('#order-qty').val(addCommas(orderQty));
            $('#total-qty').val(addCommas(totalQty));

            recalBox();

            $('#del-order-no').focus();
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }
}



$('#order-no').keyup(function(e) {
  if(e.keyCode === 13) {
    addToDispatch();
  }
})


$('#del-order-no').keyup(function(e) {
  if(e.keyCode === 13) {
    removeOrder();
  }
})


$('#plate-no').autocomplete({
  source:BASE_URL + 'auto_complete/get_carplate/',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
      $('#province').val(arr[1]);
      $('#driver-name').focus();
    }
    else {
      if(arr.length == 1) {
        if(arr[0] === 'not found' || arr[0] === '*') {
          $(this).val('');
        }
        else {
          $('#province').focus();
        }
      }
    }
  }
})


$('#driver-name').autocomplete({
  source:BASE_URL + 'auto_complete/get_driver_name/',
  autoFocus:true,
  close:function() {
    let name = $(this).val().trim();

    if(name === 'not found' || name === '*') {
      $(this).val('');
    }
    else {
      if(name.length) {
        $('#remark').focus();
      }
    }
  }
});


$('#province').autocomplete({
  source:BASE_URL + 'auto_complete/plate_province',
  autoFocus:true,
  close:function() {
    let province = $(this).val().trim();

    if(province === 'not found' || province === '*') {
      $(this).val('');
    }

    $('#driver-name').focus();
  }
})
