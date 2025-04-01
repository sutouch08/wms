$('#order-from-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#order-to-date').datepicker('option', 'minDate', sd)
  }
})

$('#order-to-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#order-from-date').datepicker('option', 'maxDate', sd)
  }
})


function chkOrderTabAll(el) {
  if(el.is(':checked')) {
    $('.chk-od').prop('checked', true)
  }
  else {
    $('.chk-od').prop('checked', false)
  }
}


function checkOrderAll(el) {
  if(el.is(':checked')) {
    $('.chk-list').prop('checked', true);
  }
  else {
    $('.chk-list').prop('checked', false);
  }
}


function clearOrderList() {
  let channels = $('#channels').val();
  $('#order-from-date').val('');
  $('#order-to-date').val('');
  $('#channels-code').val(channels).change();
  $('#customer').val('');
  $('#order-code').val('');
  $('#is-pick-list').val('0');
}


function getOrderList() {
  let h = {
    'from_date' : $('#order-from-date').val(),
    'to_date' : $('#order-to-date').val(),
    'channels' : $('#channels-code').val(),
    'customer' : $('#customer').val().trim(),
    'order_code' : $('#order-code').val().trim(),
    'is_pick_list' : $('#is-pick-list').val(),
    'warehouse_code' : $('#warehouse').val()
  }

  load_in();

  $.ajax({
    url:HOME + 'get_order_list',
    type:'POST',
    cache:false,
    data:{
      'filter' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs)

        if(ds.status === 'success') {
          let source = $('#order-template').html();
          let output = $('#order-list');

          render(source, ds.data, output);

          $('#orderListModal').modal('show');
        }
        else {
          showError(ds.message)
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


function addToPickList() {
  if($('.chk-list:checked').length) {
    $('#orderListModal').modal('hide');

    let h = {
      'code' : $('#code').val(),
      'orders' : []
    }

    $('.chk-list:checked').each(function() {
      if($(this).is(':checked')) {
        h.orders.push($(this).val());
      }
    });

    if(h.orders.length == 0) {
      swal({
        title:'Error!',
        text:'ไม่พบรายการที่เลือก',
        type:'error'
      }, function() {
        $('#orderListModal').modal('show');
      })

      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add_to_pick_list',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            })

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }
          else {
            showError(ds.message)
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
}


function deleteOrders() {
  let code = $('#code').val();

  if($('.chk-od:checked').length) {
    let h = {
      'code' : code,
      'orders' : []
    }

    $('.chk-od:checked').each(function() {
      h.orders.push($(this).val());
    });

    if(h.orders.length) {
      swal({
        title:'ลบออเดอร์',
        text:'ต้องการลบออเดอร์ที่เลือกออกหรือไม่ ?',
        type:'warning',
        html:true,
        showCancelButton:true,
        cancelButtonText:'No',
        confirmButtonText:'Yes',
        closeOnConfirm:true
      }, function() {
        load_in();

        setTimeout(() => {
          $.ajax({
            url:HOME + 'delete_orders',
            type:'POST',
            cache:false,
            data:{
              'data' : JSON.stringify(h)
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
                  window.location.reload();
                }, 1200);
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
        }, 100)
      })
    }
  }
}
