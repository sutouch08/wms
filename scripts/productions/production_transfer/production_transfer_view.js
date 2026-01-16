function viewProductionOrder(code) {
  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = BASE_URL + 'productions/production_order/sap_production_order/'+code+'?nomenu';
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


function viewApiLogs(code) {
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


function rollBack(code) {
  swal({
    title:'ย้อนสถานะ',
    text:'ต้องการย้อนเอกสารหรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    load_in();

    $.ajax({
      url:HOME + 'rollback',
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
            refresh();
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
  })
}


function close(code) {
  swal({
    title:'Close Document',
    text:'ต้องการบันทึกและปิดเอกสารนี้หรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    load_in();

    $.ajax({
      url:HOME + 'close',
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
            if(ds.status === 'success') {
              if(ds.ex == 0) {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                });

                setTimeout(() => {
                  refresh();
                }, 1200);
              }
              else {
                swal({
                  title:'Oops !',
                  text:ds.message,
                  type:'info'
                }, function() {
                  refresh();
                })
              }
            }
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
  })
}
