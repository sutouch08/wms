var click = 0;

window.addEventListener('load', () => {
  itemInit();
})


$('#posting-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#due-date').datepicker('option', 'minDate', sd);
  }
});


$('#due-date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#product-code').autocomplete({
  source:HOME + 'get_bom_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 3) {
      $(this).val(arr[0]);
      $('#product-name').val(arr[1]);
      $('#planned-uom').val(arr[2]);

      setTimeout(() => {
        let row = 0;
        $('.item-code').each(function() {
          if($(this).val().length) {
            row++;
          }
        });

        if(row > 0) {
          itemChangeConfirm();
        }
        else {
          getBomData();
        }
      }, 100);
    }
  }
});


$('#origin-num').autocomplete({
  source:HOME + 'get_sales_order',
  autoFocus:true,
  position:{
    my:"right top",
    at:"right bottom"
  },
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 4) {
      $(this).val(arr[0]);
      $('#origin-type').val('Sales Order');
      $('#origin-type').data('type', 'S');
      $('#origin-abs').val(arr[3]);
      $('#customer').val(arr[1]);
      $('#customer-code').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#origin-type').val('Manual');
      $('#origin-type').data('type', 'M');
      $('#origin-abs').val('');
      $('#customer').val('');
      $('#customer-code').val('');
    }
  }
})


function validOrigin() {
  let code = $('#origin-num').val().trim();

  console.log(code.length);

  if(code.length < 4) {
    $('#origin-num').val('');
    $('#origin-type').val('Manual');
    $('#origin-type').data('type', 'M');
    $('#origin-abs').val('');
  }
}


$('#customer').autocomplete({
  source:HOME + 'get_customer_code_and_name',
  autoFocus:true,
  position:{
    my :"right top",
    at : "right bottom"
  },
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
})


function whInit(uid) {
  $('#warehouse-'+uid).autocomplete({
    source:HOME + 'get_warehouse_code_and_name',
    autoFocus:true,
    position:{
      my : "right top",
      at : "right bottom"
    },
    close:function() {
      let arr = $('#warehouse-'+uid).val().split(' | ');

      if(arr.length == 2) {
        $('#warehouse-'+uid).val(arr[0]);
      }

      setTimeout(function() {
        updateAvailableStock(uid);
      }, 200);
    }
  });
}


function itemInit(uid) {
  if(uid != null && uid != undefined) {
    $('#item-code-'+uid).autocomplete({
      source:HOME + 'get_item_code_and_name',
      autoFocus:true,
      close:function() {
        let arr = $(this).val().split(' | ');

        if(arr.length === 2) {
          let code = arr[0];
          $('#item-code-'+uid).val(arr[0]);
          $('#item-name-'+uid).val(arr[1]);

          getItemData(code, uid);
        }
        else {
          $('#item-code-'+uid).val('');
          $('#item-name-'+uid).val('');
        }
      }
    })
  }
  else {
    $('.item-code').autocomplete({
      source:HOME + 'get_item_code_and_name',
      autoFocus:true,
      close:function() {
        let uid = $(this).data('uid');
        let arr = $(this).val().split(' | ');

        if(arr.length === 2) {
          let code = arr[0];
          $('#item-code-'+uid).val(arr[0]);
          $('#item-name-'+uid).val(arr[1]);
          getItemData(code, uid);
        }
        else {
          $('#item-code-'+uid).val('');
          $('#item-name-'+uid).val('');
        }
      }
    })
  }
}


function getItemData(code, uid) {
  if(code != "" && code != null && code != undefined) {

    let whsCode = $('#warehouse-'+uid).val().trim();

    $.ajax({
      url:HOME + 'get_item_data',
      type:'POST',
      cache:false,
      data:{
        'ItemCode' : code,
        'WhsCode' : whsCode
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let item = ds.item;
            let available = ds.available;
            let baseQty = 1;
            let plannedQty = parseDefaultFloat($('#planned-qty').val(), 0);

            $('#item-name-'+uid).val(item.ItemName);
            $('#base-qty-'+uid).val(baseQty);
            $('#base-ratio-'+uid).val(1);
            $('#planned-qty-'+uid).val(addCommas(plannedQty.toFixed(2)));
            $('#issued-'+uid).val(0);
            $('#available-'+uid).val(available);
            $('#uom-'+uid).val(item.Uom);
            $('#uom-'+uid).data('uomentry', item.UomEntry);
            $('#uom-'+uid).data('uomcode', item.UomCode);

            $('#base-qty-'+uid).focus();
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


function updateAvailableStock(uid) {
  let itemCode = $('#item-code-'+uid).val().trim();
  let whsCode = $('#warehouse-'+uid).val().trim();

  $.ajax({
    url:HOME + 'get_available_stock',
    type:'POST',
    cahce:false,
    data:{
      'ItemCode' : itemCode,
      'WhsCode' : whsCode
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#available-'+uid).val(ds.available);
        }
        else {
          $('#available-'+uid).val('0.00');
        }
      }
      else {
        $('#available-'+uid).val('0.00');
      }
    }
  })
}


function getBomData() {
  let code = $('#product-code').val().trim();
  let planned_qty = parseDefaultFloat($('#planned-qty').val(), 1);

  load_in();

  $.ajax({
    url:HOME + 'get_bom_data',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'planned_qty' : planned_qty
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#details-template').html();
          let output = $('#details-table');

          $('#deatil-table').html('');

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
  })
}


function recalQty(uid) {
  let pQty = parseDefaultFloat($('#planned-qty').val(),1);

  if(uid === null || uid === undefined) {
    if($('.base-qty').length) {
      $('.base-qty').each(function() {
        uid = $(this).data('uid');
        let bQty = parseDefaultFloat(removeCommas($(this).val()), 1);
        let plannedQty = pQty * bQty;

        $('#planned-qty-'+uid).val(addCommas(roundNumber(plannedQty, 4)));
      });
    }
  }
  else {
    let el = $('#base-qty-'+uid);
    let bQty = parseDefaultFloat(removeCommas(el.val()), 1);
    let plannedQty = pQty * bQty;

    $('#planned-qty-'+uid).val(addCommas(roundNumber(plannedQty, 4)));
  }
}


function addRow() {
  let no = parseDefaultInt($('#row-no').val(), 0);
  let ne = no + 1;
  let uid = generateUID();
  let whs = $('#warehouse').val();
  let source = $('#new-row-template').html();
  let output = $('#details-table');
  let data = [{
    'no' : ne,
    'uid' : uid,
    'Warehouse' : whs,
    'type_item' : 'selected',
    'type_resource' : '',
    'type_text' : ''
  }];

  render_append(source, data, output);

  $('#row-no').val(ne);

  reIndex('no');

  setTimeout(() => {
    itemInit(uid);
    whInit(uid);
  }, 100);
}


function add() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('r');

    let h = {
      'Type' : $('#type').val(),
      'Status' : $('#status').val(),
      'ItemCode' : $('#product-code').val().trim(),
      'ItemName' : $('#product-name').val().trim(),
      'PlannedQty' : parseDefaultFloat($('#planned-qty').val(), 0),
      'Uom' : $('#planned-uom').val().trim(),
      'Warehouse' : $('#warehouse').val(),
      'PostDate' : $('#posting-date').val(),
      'DueDate' : $('#due-date').val(),
      'OriginType' : $('#origin-type').data('type'),
      'OriginNum' : $('#origin-num').val().trim(),
      'OriginAbs' : $('#origin-abs').val(),
      'CardCode' : $('#customer').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

    if(h.ItemCode.length === 0) {
      $('#product-code').hasError();
      click = 0;
      return false;
    }

    if(h.PlannedQty <= 0) {
      $('#planned-qty').hasError();
      click = 0;
      return false;
    }

    if( ! isDate(h.PostDate)) {
      click = 0;
      $('#posting-date').hasError();
      return false;
    }

    if( ! isDate(h.DueDate)) {
      $('#due-date').hasError();
      click = 0;
      return false;
    }

    let lineNum = 0;
    let err = 0;

    $('.item-code').each(function() {
      let item_code = $(this).val().trim();

      if(item_code.length) {
        let uid = $(this).data('uid');
        let bQty = parseDefaultFloat($('#base-qty-'+uid).val(), 0);
        let pQty = parseDefaultFloat($('#planned-qty-'+uid).val(), 0);
        let whs = $('#warehouse-'+uid).val();

        if(bQty <= 0) {
          $('#base-qty-'+uid).hasError();
          err++;
        }

        if(pQty <= 0) {
          $('#planned-qty-'+uid).hasError();
          err++;
        }

        if(whs.length == 0) {
          $('#warehouse-'+uid).hasError();
          err++;
        }

        if(err == 0) {
          h.rows.push({
            'uid' : uid,
            'LineNum' : lineNum,
            'ItemType' : $('#type-'+uid).val(),
            'ItemCode' : item_code,
            'ItemName' : $('#item-name-'+uid).val().trim(),
            'BaseQty' : bQty,
            'PlannedQty' : pQty,
            'WhsCode' : whs,
            'IssueType' : $('#issue-type-'+uid).val(),
            'Uom' : $('#uom-'+uid).val(),
            'UomEntry' : $('#uom-'+uid).data('uomentry'),
            'UomCode' : $('#uom-'+uid).data('uomcode')
          });

          lineNum++;
        }
      }
    });

    if(err > 0) {
      click = 0;
      swal({
        title:'Error !',
        text:'พบรายการที่ไม่ถูกต้อง กรุณาแก้ไข',
        type:'error'
      });

      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              edit(ds.code);
            }, 1200);
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
        click = 0;
        showError(rs);
      }
    })
  }
}


function update() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('r');

    let h = {
      'code' : $('#code').val(),
      'Type' : $('#type').val(),
      'Status' : $('#status').val(),
      'ItemCode' : $('#product-code').val().trim(),
      'ItemName' : $('#product-name').val().trim(),
      'PlannedQty' : parseDefaultFloat($('#planned-qty').val(), 0),
      'Uom' : $('#planned-uom').val().trim(),
      'Warehouse' : $('#warehouse').val(),
      'PostDate' : $('#posting-date').val(),
      'DueDate' : $('#due-date').val(),
      'OriginType' : $('#origin-type').data('type'),
      'OriginNum' : $('#origin-num').val().trim(),
      'OriginAbs' : $('#origin-abs').val(),
      'CardCode' : $('#customer').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

    if(h.ItemCode.length === 0) {
      $('#product-code').hasError();
      click = 0;
      return false;
    }

    if(h.PlannedQty <= 0) {
      $('#planned-qty').hasError();
      click = 0;
      return false;
    }

    if( ! isDate(h.PostDate)) {
      click = 0;
      $('#posting-date').hasError();
      return false;
    }

    if( ! isDate(h.DueDate)) {
      $('#due-date').hasError();
      click = 0;
      return false;
    }

    let lineNum = 0;
    let err = 0;

    $('.item-code').each(function() {
      let item_code = $(this).val().trim();

      if(item_code.length) {
        let uid = $(this).data('uid');
        let bQty = parseDefaultFloat($('#base-qty-'+uid).val(), 0);
        let pQty = parseDefaultFloat($('#planned-qty-'+uid).val(), 0);
        let whs = $('#warehouse-'+uid).val();

        if(bQty <= 0) {
          $('#base-qty-'+uid).hasError();
          err++;
        }

        if(pQty <= 0) {
          $('#planned-qty-'+uid).hasError();
          err++;
        }

        if(whs.length == 0) {
          $('#warehouse-'+uid).hasError();
          err++;
        }

        if(err == 0) {
          h.rows.push({
            'uid' : uid,
            'LineNum' : lineNum,
            'ItemType' : $('#type-'+uid).val(),
            'ItemCode' : item_code,
            'ItemName' : $('#item-name-'+uid).val().trim(),
            'BaseQty' : bQty,
            'PlannedQty' : pQty,
            'WhsCode' : whs,
            'IssueType' : $('#issue-type-'+uid).val(),
            'Uom' : $('#uom-'+uid).val(),
            'UomEntry' : $('#uom-'+uid).data('uomentry'),
            'UomCode' : $('#uom-'+uid).data('uomcode')
          });

          lineNum++;
        }
      }
    });

    if(err > 0) {
      click = 0;
      swal({
        title:'Error !',
        text:'พบรายการที่ไม่ถูกต้อง กรุณาแก้ไข',
        type:'error'
      });

      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

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
                title:'Oops!',
                type:'info',
                text:ds.message
              }, function() {
                refresh();
              });
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
        click = 0;
        showError(rs);
      }
    })
  }
}


function closeOrder(code) {
  swal({
    title:'Close Order',
    text:'เมื่อ close order แล้วจะไม่สามารถกลับมาแก้ไขได้อีก<br>ต้องการดำเนินการต่อหรือไม่ ?',
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
        url:HOME + 'close_order',
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
    }, 100);
  })
}

function itemChangeConfirm() {
  let code = $('#product-code').val().trim();
  let cPrev = $('#product-code').data('prev');
  let nPrev = $('#product-name').data('prev');

  if(code.length) {
    if(code != cPrev) {
      swal({
        title:'Warning !',
        text:'รายการปัจจุบันจะถูกเคลียร์ <br>ต้องการดำเนินการต่อหรือไม่ ?',
        type:'warning',
        html:true,
        showCancelButton:true,
        confirmButtonText:'Yes',
        cancelButtonText:'No',
        closeOnConfirm:true
      }, function(isConfirm) {
        if(isConfirm) {
          getBomData();
        }
        else {
          $('#product-code').val(cPrev);
          $('#product-name').val(nPrev);
        }
      })
    }
  }
}


function createProductionTransfer(code) {
  let docNum = $('#doc-num').val().trim();

  if(docNum.length) {
    let target = BASE_URL + 'productions/production_transfer/add_new/'+code;
    window.open(target, '_blank');
  }
  else {
    showError('เอกสารยังไม่เข้า SAP กรุณาตรวจสอบ');
  }
}


function createGoodsIssue(code) {
  let docNum = $('#doc-num').val().trim();

  if(docNum.length) {
    let target = BASE_URL + 'productions/production_issue/add_new/'+code;
    window.open(target, '_blank');
  }
  else {
    showError('เอกสารยังไม่เข้า SAP กรุณาตรวจสอบ');
  }
}


function createGoodsReceipt(code) {
  let docNum = $('#doc-num').val().trim();

  if(docNum.length) {
    let target = BASE_URL + 'productions/production_receipt/add_new/'+code;
    window.open(target, '_blank');
  }
  else {
    showError('เอกสารยังไม่เข้า SAP กรุณาตรวจสอบ');
  }
}


function viewTQ() {
  let code = $('#tq-list').val();

  if(code.length) {
    let target = BASE_URL + 'productions/production_transfer/view_detail/'+code;

    window.open(target, '_blank');
  }
}
