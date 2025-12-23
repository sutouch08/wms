var HOME = BASE_URL + 'report/audit/order_reference/';

function getOrder() {
  let code = $('#order-no').val().trim();

  if(code.length) {
    $.ajax({
      url:HOME + 'get_order',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success' && ds.data != null) {
            let source = $('#row-template').html();
            let output = $('#order-table');

            render_append(source, ds.data, output);

            reIndex();
            recalBox();
            $('#order-no').val('').focus();
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
}


function recalBox() {
  let total = 0;

  $('.carton-qty').each(function() {
    let qty = parseDefaultFloat($(this).val(), 0);

    total += qty;
  });

  $('#total-carton').val(total);
}


$('#order-no').keyup(function(e) {
  if(e.keyCode === 13) {
    getOrder();
  }
})


function doExport() {
  let h = [];

  $('.order-code').each(function() {
    let el = $(this);

    h.push({
      'order_code' : el.data('order'),
      'reference' : el.data('ref'),
      'tracking_no' : el.data('tracking'),
      'customer' : el.data('customer'),
      'channels' : el.data('channels'),
      'carton' : el.data('carton')
    });
  })

  if(h.length) {
    let token = generateUID();
    $('#token').val(token);
    $('#data').val(JSON.stringify(h));
    get_download(token);
    $('#exportForm').submit();
  }
}


function deleteRow(id) {
  $('#row-'+id).remove();
  reIndex();
  recalBox();
}
