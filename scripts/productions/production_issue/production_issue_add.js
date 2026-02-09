window.addEventListener('load', () => {
  baseRefInit();
  detailsInit();
  reCalTotal();
});

var click = 0;

$('#date-add').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#posting-date').datepicker({
  dateFormat:'dd-mm-yy'
})


function baseRefInit() {
  let status = $('#p-status').val();

  $('#base-ref').autocomplete({
    source:HOME + 'get_production_order_code/' + status,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 3) {
        $(this).val(arr[0]);
        $('#base-item').val(arr[1]);
      }
      else {
        $(this).val('');
        $('#base-item').val('');
      }
    }
  })
}


$('#base-ref').keyup(function(e) {
  if(e.keyCode === 13) {
    setTimeout(() => {
      getOrderData();
    }, 150);
  }
})


function detailsInit() {
  $('.issue-qty').each(function() {
    let uid = $(this).data('uid');
    whsInit(uid);
    binInit(uid);

    reCalBatchRows(uid);
  })
}


function add(type) {
  if(click == 0) {
    click = 1;

    clearErrorByClass('h');
    clearErrorByClass('r');

    let h = {
      'type' : type,
      'date_add' : $('#date-add').val().trim(),
      'shipped_date' : $('#posting-date').val().trim(),
      'baseRef' : $('#base-ref').val().trim(),
      'orderRef' : $('#order-ref').val().trim(),
      'externalRef' : $('#external-ref').val().trim(),
      'ItemCode' : $('#base-item').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

    let prevBaseRef = $('#base-ref').data('prev');

    if( ! isDate(h.date_add)) {
      $('#date-add').hasError();
      click = 0;
      return false;
    }


    if(h.baseRef == "") {
      $('#base-ref').hasError();
      click = 0;
      return false;
    }

    if(h.baseRef != prevBaseRef) {
      $('#base-ref').hasError();
      click = 0;
      swal({
        title:'Error',
        text:'Production order มีการเปลี่ยนแปลง',
        type:'error'
      });

      return false;
    }

    let error = 0;
    let errMsg = "";
    let line = 0;
    let sc = true;

    $('.issue-qty').each(function() {
      if(sc === true) {
        let el = $(this);
        let uid = el.data('uid');
        let whsCode = $('#whs-'+uid).val().trim();
        let binCode = $('#bin-'+uid).val().trim();
        let issueQty = parseDefaultFloat(removeCommas(el.val()), 0);
        let inStock = parseDefaultFloat(removeCommas($('#instock-'+uid).val()), 0);
        let hasBatch = el.data('hasbatch') == 'Y' ? 1 : 0;

        if(issueQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

        if(sc === true && whsCode.length == 0) {
          sc = false;
          $('#whs-'+uid).hasError();
          errMsg = "กรุณาระบุคลัง";
          return false;
        }

        if(sc === true && hasBatch == 0) {
          if(binCode.length == 0) {
            sc = false;
            $('#bin-'+uid).hasError();
            errMsg = "กรุณาระบุโซน";
            return false;
          }

          if(inStock < issueQty) {
            sc = false;
            el.hasError();
            errMsg = 'จำนวนคงเหลือต้นทางไม่เพียงพอ กรุณาแก้ไข';
            return false;
          }
        }

        if(sc === true && hasBatch == 1) {
          if($('.child-of-'+uid).length == 0) {
            sc = false;
            $('#item-code-'+uid).hasError();
            errMsg = "กรุณาระบุ Batch";
            return false;
          }
        }

        if(sc === true) {
          let itemCode = el.data('code');
          let itemName = el.data('name');

          let row = {
            'uid' : uid,
            'LineNum' : line,
            'BaseType' : el.data('basetype'),
            'BaseEntry' : el.data('baseentry'),
            'BaseRef' : el.data('baseref'),
            'BaseLine' : el.data('baseline'),
            'hasBatch' : hasBatch,
            'ItemCode' : itemCode,
            'ItemName' : itemName,
            'WhsCode' : whsCode,
            'BinCode' : binCode,
            'Qty' : issueQty,
            'Uom' : el.data('uom'),
            'UomCode' : el.data('uomcode'),
            'UomEntry' : el.data('uomentry'),
            'batchRows' : []
          };

          if(hasBatch == 1) {
            let sumBatchQty = 0;

            $('.child-of-'+uid).each(function() {
              if(sc === false) { return false; }

              let uuid = $(this).data('uid');
              let ro = $('#batch-qty-'+uuid);
              let bQty = parseDefaultFloat(ro.val(), 0);
              let bStock = parseDefaultFloat(removeCommas($('#batch-in-stock-'+uuid).val()), 0);
              sumBatchQty += bQty;

              if(bQty > bStock) {
                sc = false;
                ro.hasError();
                errMsg = "จำนวนคงเหลือไม่พอ กรุณาแก้ไข";
                return false;
              }

              row.batchRows.push({
                'ItemCode' : itemCode,
                'ItemName' : itemName,
                'BatchNum' : ro.data('batchnum'),
                'BatchAttr1' : ro.data('attr1'),
                'BatchAttr2' : ro.data('attr2'),
                'Qty' : bQty,
                'WhsCode' : ro.data('fromwhs'),
                'BinCode' : ro.data('frombin'),
                'uid' : uuid
              });
            });

            if(roundNumber(issueQty, 4) != roundNumber(sumBatchQty, 4)) {
              el.hasError();
              sc = false;
              errMsg = "จำนวนไม่ถูกต้อง กรุณาแก้ไข" ;
              return false;
            }
          }

          h.rows.push(row);
          line++;
        }
      } //--- end sc

    }); //--- each

    if(sc === false) {
      click = 0;
      swal({
        title:'Error!',
        text:errMsg,
        type:'error'
      });

      return false;
    }

    if(type != 'P' && h.rows.length == 0) {
      click = 0;
      swal({
        title:'Error!',
        text:'ไม่พบรายการโอนย้าย',
        type:'error'
      });

      return false;
    }

    // console.log(h); click = 0; return false;
    if(sc === true) {
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

              if(ds.ex == 0) {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                });

                setTimeout(() => {
                  viewDetail(ds.code);
                }, 1200);
              }
              else {
                swal({
                  title:'Oops !',
                  text:ds.message,
                  type:'info'
                }, function() {
                  viewDetail(ds.code);
                })
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
}


function save(type) {
  if(click == 0) {
    click = 1;

    clearErrorByClass('h');
    clearErrorByClass('r');

    let h = {
      'type' : type,
      'code' : $('#code').val().trim(),
      'date_add' : $('#date-add').val().trim(),
      'shipped_date' : $('#posting-date').val().trim(),
      'baseRef' : $('#base-ref').val().trim(),
      'orderRef' : $('#order-ref').val().trim(),
      'externalRef' : $('#external-ref').val().trim(),
      'ItemCode' : $('#base-item').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

    let prevBaseRef = $('#base-ref').data('prev');

    if( ! isDate(h.date_add)) {
      $('#date-add').hasError();
      click = 0;
      return false;
    }


    if(h.baseRef == "") {
      $('#base-ref').hasError();
      click = 0;
      return false;
    }

    if(h.baseRef != prevBaseRef) {
      $('#base-ref').hasError();
      click = 0;
      swal({
        title:'Error',
        text:'Production order มีการเปลี่ยนแปลง',
        type:'error'
      });

      return false;
    }

    let error = 0;
    let errMsg = "";
    let line = 0;
    let sc = true;

    $('.issue-qty').each(function() {
      if(sc === true) {
        let el = $(this);
        let uid = el.data('uid');
        let whsCode = $('#whs-'+uid).val().trim();
        let binCode = $('#bin-'+uid).val().trim();
        let issueQty = parseDefaultFloat(removeCommas(el.val()), 0);
        let inStock = parseDefaultFloat(removeCommas($('#instock-'+uid).val()), 0);
        let hasBatch = el.data('hasbatch') == 'Y' ? 1 : 0;

        if(issueQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

        if(sc === true && whsCode.length == 0) {
          sc = false;
          $('#whs-'+uid).hasError();
          errMsg = "กรุณาระบุคลัง";
          return false;
        }

        if(sc === true && hasBatch == 0) {
          if(binCode.length == 0) {
            sc = false;
            $('#bin-'+uid).hasError();
            errMsg = "กรุณาระบุโซน";
            return false;
          }

          if(inStock < issueQty) {
            sc = false;
            el.hasError();
            errMsg = 'จำนวนคงเหลือต้นทางไม่เพียงพอ กรุณาแก้ไข';
            return false;
          }
        }

        if(sc === true && hasBatch == 1) {
          if($('.child-of-'+uid).length == 0) {
            sc = false;
            $('#item-code-'+uid).hasError();
            errMsg = "กรุณาระบุ Batch";
            return false;
          }
        }

        if(sc === true) {
          let itemCode = el.data('code');
          let itemName = el.data('name');

          let row = {
            'uid' : uid,
            'LineNum' : line,
            'BaseType' : el.data('basetype'),
            'BaseEntry' : el.data('baseentry'),
            'BaseRef' : el.data('baseref'),
            'BaseLine' : el.data('baseline'),
            'hasBatch' : hasBatch,
            'ItemCode' : itemCode,
            'ItemName' : itemName,
            'WhsCode' : whsCode,
            'BinCode' : binCode,
            'Qty' : issueQty,
            'Uom' : el.data('uom'),
            'UomCode' : el.data('uomcode'),
            'UomEntry' : el.data('uomentry'),
            'batchRows' : []
          };

          if(hasBatch == 1) {
            let sumBatchQty = 0;

            $('.child-of-'+uid).each(function() {
              if(sc === false) { return false; }

              let uuid = $(this).data('uid');
              let ro = $('#batch-qty-'+uuid);
              let bQty = parseDefaultFloat(ro.val(), 0);
              let bStock = parseDefaultFloat(removeCommas($('#batch-in-stock-'+uuid).val()), 0);

              if(bQty > bStock) {
                sc = false;
                ro.hasError();
                errMsg = "จำนวนคงเหลือไม่พอ กรุณาแก้ไข";
                return false;
              }

              sumBatchQty += bQty;

              row.batchRows.push({
                'ItemCode' : itemCode,
                'ItemName' : itemName,
                'BatchNum' : ro.data('batchnum'),
                'BatchAttr1' : ro.data('attr1'),
                'BatchAttr2' : ro.data('attr2'),
                'Qty' : bQty,
                'WhsCode' : ro.data('fromwhs'),
                'BinCode' : ro.data('frombin'),
                'uid' : uuid
              });
            });

            if(roundNumber(issueQty, 4) != roundNumber(sumBatchQty, 4)) {
              el.hasError();
              sc = false;
              errMsg = "จำนวนไม่ถูกต้อง กรุณาแก้ไข" ;
              return false;
            }
          }

          h.rows.push(row);
          line++;
        }
      } //--- end sc

    }); //--- each

    if(sc === false) {
      click = 0;
      swal({
        title:'Error!',
        text:errMsg,
        type:'error'
      });

      return false;
    }

    if(type != 'P' && h.rows.length == 0) {
      click = 0;
      swal({
        title:'Error!',
        text:'ไม่พบรายการโอนย้าย',
        type:'error'
      });

      return false;
    }

    // console.log(h); click = 0; return false;
    if(sc === true) {
      load_in();

      $.ajax({
        url:HOME + 'save',
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
                  viewDetail(ds.code);
                }, 1200);
              }
              else {
                swal({
                  title:'Oops !',
                  text:ds.message,
                  type:'info'
                }, function() {
                  viewDetail(ds.code);
                })
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
}


function getOrderData() {
  let baseCode = $('#base-ref').val().trim();
  let baseItem = $('#base-item').val().trim();

  if(baseCode.length) {
    load_in();

    $.ajax({
      url:HOME + 'get_production_order_details',
      type:'POST',
      cache:false,
      data:{
        'baseCode' : baseCode
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

            $('#production-modal-title').text(baseCode + '  |  ' + baseItem);

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


//---- add production line to issue
function addToOrder() {
  $('#production-modal').modal('hide');
  let baseCode = $('#base-ref').val().trim();
  $('#base-ref').data('prev', baseCode);
  let ds = [];
  let uids = [];

  $('.pi-qty').each(function() {
    let el = $(this);
    let qty = parseDefaultFloat(removeCommas(el.val()), 0);
    let uid = el.data('uid');

    if(qty > 0) {
      uids.push(uid);

      ds.push({
        'uid' : uid,
        'Qty' : qty,
        'whsCode' : el.data('whscode'),
        'ItemCode' : el.data('code'),
        'ItemName' : el.data('name'),
        'BaseEntry' : el.data('baseentry'),
        'BaseRef' : el.data('baseref'),
        'BaseLine' : el.data('baseline'),
        'BaseType' : el.data('basetype'),
        'ManBtchNum' : el.data('hasbatch'),
        'hasBatch' : el.data('hasbatch') == 'Y' ? true : false,
        'UomEntry' : el.data('uomentry'),
        'UomCode' : el.data('uomcode'),
        'UomName' : el.data('uom'),
        'InStock' : el.data('instock')
      });
    }
  });

  if(ds.length) {
    let source = $('#details-template').html();
    let output = $('#details-table');
    render(source, ds, output);
    reIndex('no');

    uids.forEach((uuid, i) => {
      whsInit(uuid);
      binInit(uuid);
    });
  }

  reCalTotal();
}


function chooseAll() {
  $('.pi-qty').each(function() {
    let el = $(this);
    el.val(el.data('balance'));
  });
}


function clearAll() {
  $('.pi-qty').val('');
}


function whsInit(uid) {
  $('#whs-'+uid).autocomplete({
    source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
    autoFocus:true,
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
}


function binInit(uid) {
  let whsCode = $('#whs-'+uid).val();

  $('#bin-'+uid).autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+whsCode,
    autoFocus:true,
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

  getAvailableStock(uid);
}


function clearBatchFilter() {
  $('#whs-filter').val('all').change();
  $('#batch-num-filter').val('');
  $('#attr1-filter').val('');
  $('#attr2-filter').val('');
}


function getPreBatch(uid) {
  $('#item-code-'+uid).clearError();
  let itemCode = $('#item-code-'+uid).val().trim();
  let whsCode = $('#whs-'+uid).val();


  if(itemCode.length == 0) {
    $('#item-code-'+uid).hasError();
    swal("กรุณาระบุรหัสสินค้า");
    return false;
  }

  if(whsCode != '') {
    $('#whs-filter').val(whsCode).change();
  }

  $('#pre-target-uid').val(uid);
  $('#pre-batch-modal').modal('show');

  dragElement('pre-batch-modal', 'pre-batch-modal-header');
}


function getBatch() {
  let uid = $('#pre-target-uid').val();
  let issueQty = parseDefaultFloat(removeCommas($('#issue-qty-'+uid).val().trim()), 0);
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
          $('#target-uid').val(uid);
          $('#target-qty').val(issueQty);
          $('#issue-qty').text(addCommas(issueQty));

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
    error:function(rs) {
      showError(rs);
    }
  })
}


function addBatchRows() {
  clearErrorByClass('bi-qty');
  let error = 0;
  let ds = []; //--- for new rows
  let us = []; //--- for update rows
  let uid = $('#target-uid').val();

  if($('.bi-qty').length) {
    $('.bi-qty').each(function() {
      let uom = $('#uom-'+uid);
      let el = $(this);
      let uuid = el.data('uid');
      let onHand = parseDefaultFloat(removeCommas($('#bi-onhand-'+uuid).val()), 0);
      let qty = parseDefaultFloat(el.val(), 0);

      if(onHand < qty || qty < 0) {
        $('#bi-qty-'+uuid).hasError();
        error++;
      }

      if(qty > 0 && onHand >= qty) {
        if($('#batch-qty-'+uuid).length) {
          let bQty = parseDefaultFloat($('#batch-qty-'+uuid).val(), 0);
          let nQty = bQty + qty;

          us.push({
            'uid' : uuid,
            'qty' : nQty
          });
        }
        else {
          ds.push({
            'uid' : uuid,
            'parentUid' : uid,
            'batchNum' : el.data('batch'),
            'batchAttr1' : el.data('attr1'),
            'batchAttr2' : el.data('attr2'),
            'binCode' : el.data('bin'),
            'whsCode' : el.data('whs'),
            'InStock' : onHand,
            'qty' : qty,
            'UomName' : uom.val()
          });
        }
      }
    });

    if(error == 0) {
      if(us.length > 0) {
        us.forEach((ro) => {
          $('#batch-qty-' + ro.uid).val(ro.qty);
        })
      }

      if(ds.length > 0) {
        let source = $('#batch-rows-template').html();
        let output = $('#row-'+uid);
        render_after(source, ds, output);
      }

      $('#batch-modal').modal('hide');

      reCalBatchRows(uid);
    }
  }

  reCalTotal();
}


function removeBatchRow(uid) {
  let parentUid = $('#batch-qty-'+uid).data('parent');
  $('#batch-rows-'+uid).remove();

  reCalBatchRows(parentUid);
}


function removeRow(uid) {
  $('#row-'+uid).remove();
  $('.child-of-'+uid).remove();

  reIndex('no');
  reCalTotal();
}


function getAvailableStock(uid) {
  let itemCode = $('#item-code-'+uid).val().trim();
  let whsCode = $('#whs-'+uid).val().trim();

  if(whsCode.length && itemCode.length) {
    $.ajax({
      url:HOME + 'get_available_stock',
      type:'POST',
      cache:false,
      data:{
        'ItemCode' : itemCode,
        'WhsCode' : whsCode
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            $('#instock-'+uid).val(ds.available);
          }
          else {
            $('#instock-'+uid).val('0.00');
          }
        }
        else {
          $('#instock-'+uid).val('0.00');
        }
      }
    })
  }
}


function reCalBatchRows(parentUid) {
  if($('.child-of-'+parentUid).length) {
    let pQty = 0;
    $('.child-of-'+parentUid).each(function() {
      let uid = $(this).data('uid');
      let qty = parseDefaultFloat(removeCommas($('#batch-qty-'+uid).val()), 0);
      pQty += qty;
    });

    $('#issue-qty-'+parentUid).val(addCommas(pQty.toFixed(2)));
  }

  reIndex('b-'+parentUid);

  reCalTotal();
}


function reCalTotal() {
  let totalQty = 0;
  let totalItems = $('.issue-qty').length;
  let totalBatch = $('.batch-qty').length;

  $('.issue-qty').each(function() {
    totalQty += parseDefaultFloat(removeCommas($(this).val()), 0);
  });

  $('#total-item-row').val(addCommas(totalItems.toFixed(2)));
  $('#total-batch-row').val(addCommas(totalBatch.toFixed(2)));
  $('#total-item-qty').val(addCommas(totalQty.toFixed(2)));
}
