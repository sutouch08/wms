function viewProductionOrder(code) {
  if(code.length) {
    let baseItem = $('#base-item').val().trim();

    load_in();

    $.ajax({
      url:HOME + 'get_production_order_details',
      type:'POST',
      cache:false,
      data:{
        'baseCode' : code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let data = ds.data;
            let source = $('#production-modal-template').html();
            let output = $('#production-modal-table');

            render(source, data, output);

            reIndex('p-no');
            $('#production-modal-title').text(code + '  |  ' + baseItem);

            $('#production-modal').modal('show');
            dragElement('production-modal', 'production-modal-header');
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


function clearBatchFilter() {
  $('#whs-filter').val('all').change();
  $('#batch-num-filter').val('');
  $('#attr1-filter').val('');
  $('#attr2-filter').val('');
}


function showFilterBatch(uid) {
  $('#batch-modal').modal('hide');

  if(uid != undefined && uid != null && uid != '') {
    $('#pre-target-uid').val(uid);
  }

  setTimeout(() => {
    $('#pre-batch-modal').modal('show');
    dragElement('pre-batch-modal', 'pre-batch-modal-header');
  }, 200);
}


function viewItemBatch() {
  let uid = $('#pre-target-uid').val();
  $('#pre-batch-modal').modal('hide');

  let filter = {
    'ItemCode' : $('#item-code-'+uid).val().trim(),
    'WhsCode' : $('#whs-filter').val(),
    'BatchNum' : $('#batch-num-filter').val().trim(),
    'BatchAttr1' : $('#attr1-filter').val().trim(),
    'BatchAttr2' : $('#attr2-filter').val().trim()
  }

  load_in();

  $.ajax({
    url:HOME + 'get_item_batch_rows',
    type:'POST',
    cache:false,
    data:{
      'filter' : JSON.stringify(filter)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {

        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let title = filter.ItemCode;
          $('#batch-modal-title').text(title);
          let source = $('#batch-modal-template').html();
          let output = $('#batch-modal-table');

          render(source, ds.data, output);

          reIndex('b-no');
          $('#batch-modal').modal('show');
          dragElement('batch-modal', 'batch-modal-header');
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },

  })
}


function viewIXProductionOrder() {
  let code = $('#order-ref').val().trim();

  if(code.length) {
    let target = BASE_URL + 'productions/production_order/view_detail/'+code;

    window.open(target, '_blank');
  }
}


function viewApiLogs(code)
{
  let url = BASE_URL + "rest/V1/sap_api_logs";
  let mapForm = document.createElement("form");
  mapForm.target = "Map";
  mapForm.method = "POST";
  mapForm.action = url;

  let mapInput = document.createElement("input");
  mapInput.type = "text";
  mapInput.name = "code";
  mapInput.value = code;
  mapForm.appendChild(mapInput);

  document.body.appendChild(mapForm);
  map = window.open(url, "Map", "width=1350, height=900, scrollbars=yes");
  mapForm.submit();
  document.body.removeChild(mapForm);
}
