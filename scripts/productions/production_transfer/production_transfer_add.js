window.addEventListener('load', () => {
  binCodeInit();
  baseRefInit();
  detailsInit();
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


function binCodeInit(change) {
  let whsCode = $('#toWhsCode').val();

  if(change == 'Y') {
    $('#bin-code').val('');
    $('#bin-name').val('');
  }

  $('#bin-code').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+whsCode,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2) {
        let code = arr[0];
        let name = arr[1] == '' ? code : arr[1];
        $(this).val(code);
        $('#bin-name').val(name);

        if($('.to-bin').length) {
          $('.to-bin').val(arr[0]);
        }
      }
      else {
        $(this).val('');
        $('#bin-name').val('');
      }
    }
  })

  if($('.to-whs').length) {
    $('.to-whs').each(function() {
      let el = $(this);
      el.val(whsCode);
      toBinInit(el.data('uid'));
    })
  }
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
      'fromWhsCode' : $('#fromWhsCode').val(),
      'toWhsCode' : $('#toWhsCode').val(),
      'toBinCode' : $('#bin-code').val().trim(),
      'baseRef' : $('#base-ref').val().trim(),
      'orderRef' : $('#order-ref').val().trim(),
      'itemCode' : $('#base-item').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

    let prevBaseRef = $('#base-ref').data('prev');

    if( ! isDate(h.date_add)) {
      $('#date-add').hasError();
      click = 0;
      return false;
    }

    if(h.fromWhsCode == "") {
      $('#fromWhsCode').hasError();
      click = 0;
      return false;
    }

    if(h.toWhsCode == "") {
      $('#toWhsCode').hasError();
      click = 0;
      return false;
    }

    if(h.toWhsCode == h.fromWhsCode) {
      $('#fromWhsCode').hasError();
      $('#toWhsCode').hasError();
      click = 0;
      return false;
    }

    let error = 0;
    let errMsg = "";
    let line = 0;
    let sc = true;

    $('.tran-qty').each(function() {
      if(sc === true) {
        let el = $(this);
        let uid = el.data('uid');
        let fromWhsCode = $('#from-whs-'+uid).val().trim();
        let toWhsCode = $('#to-whs-'+uid).val().trim();
        let fromBinCode = $('#from-bin-'+uid).val().trim();
        let toBinCode = $('#to-bin-'+uid).val().trim();
        let trQty = parseDefaultFloat(removeCommas(el.val()), 0);
        let trStock = parseDefaultFloat(removeCommas($('#instock-'+uid).val()), 0);
        let hasBatch = el.data('hasbatch') == 'Y' ? 1 : 0;

        if(trQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

        if(sc === true) {
          if(toWhsCode.length == 0) {
            sc = false;
            $('#to-whs-'+uid).hasError();
            errMsg = "กรุณาระบุคลังปลายทาง";
            return false;
          }
        }

        if(sc === true) {
          if(toBinCode.length == 0) {
            sc = false;
            $('#to-bin-'+uid).hasError();
            errMsg = "กรุณาระบุโซนปลายทาง";
            return false;
          }
        }

        if(sc === true) {
          if(hasBatch == 0) {
            if(fromWhsCode.length == 0) {
              sc = false;
              $('#from-whs-'+uid).hasError();
              errMsg = "กรุณาระบุคลังต้นทาง";
              return false;
            }

            if(fromBinCode.length == 0) {
              sc = false;
              $('#from-bin-'+uid).hasError();
              errMsg = "กรุณาระบุโซนต้นทาง";
              return false;
            }
          }
        }

        if(sc === true) {
          if(hasBatch == 0 && trStock < trQty) {
            sc = false;
            el.hasError();
            errMsg = 'จำนวนคงเหลือต้นทางไม่พอโอน กรุณาแก้ไข';
            return false;
          }
        }

        if(sc === true) {
          if(hasBatch == 1) {
            if($('.child-of-'+uid).length == 0) {
              sc = false;
              $('#item-code-'+uid).hasError();
              errMsg = "กรุณาระบุ Batch";
              return false;
            }
          }
        }

        if(sc === true) {
          let itemCode = el.data('code');
          let itemName = el.data('name');

          let row = {
            'uid' : uid,
            'LineNum' : line,
            'hasBatch' : hasBatch,
            'ItemCode' : itemCode,
            'ItemName' : itemName,
            'fromWhsCode' : fromWhsCode,
            'toWhsCode' : toWhsCode,
            'fromBinCode' : fromBinCode,
            'toBinCode' : toBinCode,
            'Qty' : trQty,
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
              let bToWhs = $('#batch-toWhs-'+uuid).val().trim();
              let bToBin = $('#batch-toBin-'+uuid).val().trim();

              sumBatchQty += bQty;

              if(bQty > bStock) {
                sc = false;
                ro.hasError();
                errMsg = "จำนวนคงเหลือต้นทางไม่พอโอน กรุณาแก้ไข";
                return false;
              }

              if(bToWhs.length == 0) {
                sc = false;
                $('#batch-toWhs-'+uuid).hasError();
                errMsg = "กรุณาระบุคลังปลายทาง";
                return false;
              }

              if(bToBin.length == 0) {
                sc = false;
                $('#batch-toBin-'+uuid).hasError();
                errMsg = "กรุณาระบุโซนปลายทาง";
                return false;
              }

              row.batchRows.push({
                'ItemCode' : itemCode,
                'ItemName' : itemName,
                'BatchNum' : ro.data('batchnum'),
                'BatchAttr1' : ro.data('attr1'),
                'BatchAttr2' : ro.data('attr2'),
                'Qty' : bQty,
                'fromWhsCode' : ro.data('fromwhs'),
                'fromBinCode' : ro.data('frombin'),
                'toWhsCode' : bToWhs,
                'toBinCode' : bToBin,
                'uid' : uuid
              });

            });

            if(trQty != sumBatchQty) {
              el.hasError();
              sc = false;
              errMsg = "จำนวนไม่ถูกต้อง กรุณาแก้ไข";
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

//--- update
function save(type) {
  if(click == 0) {
    click = 1;

    clearErrorByClass('h');
    clearErrorByClass('r');

    let h = {
      'code' : $('#code').val().trim(),
      'type' : type,
      'date_add' : $('#date-add').val().trim(),
      'shipped_date' : $('#posting-date').val().trim(),
      'fromWhsCode' : $('#fromWhsCode').val(),
      'toWhsCode' : $('#toWhsCode').val(),
      'toBinCode' : $('#bin-code').val().trim(),
      'baseRef' : $('#base-ref').val().trim(),
      'orderRef' : $('#order-ref').val().trim(),
      'itemCode' : $('#base-item').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

    let prevBaseRef = $('#base-ref').data('prev');

    if( ! isDate(h.date_add)) {
      $('#date-add').hasError();
      click = 0;
      return false;
    }

    if(h.fromWhsCode == "") {
      $('#fromWhsCode').hasError();
      click = 0;
      return false;
    }

    if(h.toWhsCode == "") {
      $('#toWhsCode').hasError();
      click = 0;
      return false;
    }

    if(h.toWhsCode == h.fromWhsCode) {
      $('#fromWhsCode').hasError();
      $('#toWhsCode').hasError();
      click = 0;
      return false;
    }

    let error = 0;
    let errMsg = "";
    let line = 0;
    let sc = true;

    $('.tran-qty').each(function() {
      if(sc === true) {
        let el = $(this);
        let uid = el.data('uid');
        let fromWhsCode = $('#from-whs-'+uid).val().trim();
        let toWhsCode = $('#to-whs-'+uid).val().trim();
        let fromBinCode = $('#from-bin-'+uid).val().trim();
        let toBinCode = $('#to-bin-'+uid).val().trim();
        let trQty = parseDefaultFloat(removeCommas(el.val()), 0);
        let trStock = parseDefaultFloat(removeCommas($('#instock-'+uid).val()), 0);
        let hasBatch = el.data('hasbatch') == 'Y' ? 1 : 0;

        if(trQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

        if(sc === true) {
          if(toWhsCode.length == 0) {
            sc = false;
            $('#to-whs-'+uid).hasError();
            errMsg = "กรุณาระบุคลังปลายทาง";
            return false;
          }
        }

        if(sc === true) {
          if(toBinCode.length == 0) {
            sc = false;
            $('#to-bin-'+uid).hasError();
            errMsg = "กรุณาระบุโซนปลายทาง";
            return false;
          }
        }

        if(sc === true) {
          if(hasBatch == 0) {
            if(fromWhsCode.length == 0) {
              sc = false;
              $('#from-whs-'+uid).hasError();
              errMsg = "กรุณาระบุคลังต้นทาง";
              return false;
            }

            if(fromBinCode.length == 0) {
              sc = false;
              $('#from-bin-'+uid).hasError();
              errMsg = "กรุณาระบุโซนต้นทาง";
              return false;
            }
          }
        }

        if(sc === true) {
          if(hasBatch == 0 && trStock < trQty) {
            sc = false;
            el.hasError();
            errMsg = 'จำนวนคงเหลือต้นทางไม่พอโอน กรุณาแก้ไข';
            return false;
          }
        }

        if(sc === true) {
          if(hasBatch == 1) {
            if($('.child-of-'+uid).length == 0) {
              sc = false;
              $('#item-code-'+uid).hasError();
              errMsg = "กรุณาระบุ Batch";
              return false;
            }
          }
        }

        if(sc === true) {
          let itemCode = el.data('code');
          let itemName = el.data('name');

          let row = {
            'uid' : uid,
            'LineNum' : line,
            'hasBatch' : hasBatch,
            'ItemCode' : itemCode,
            'ItemName' : itemName,
            'fromWhsCode' : fromWhsCode,
            'toWhsCode' : toWhsCode,
            'fromBinCode' : fromBinCode,
            'toBinCode' : toBinCode,
            'Qty' : trQty,
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
              let bToWhs = $('#batch-toWhs-'+uuid).val().trim();
              let bToBin = $('#batch-toBin-'+uuid).val().trim();

              sumBatchQty += bQty;

              if(bQty > bStock) {
                sc = false;
                ro.hasError();
                errMsg = "จำนวนคงเหลือต้นทางไม่พอโอน กรุณาแก้ไข";
                return false;
              }

              if(bToWhs.length == 0) {
                sc = false;
                $('#batch-toWhs-'+uuid).hasError();
                errMsg = "กรุณาระบุคลังปลายทาง";
                return false;
              }

              if(bToBin.length == 0) {
                sc = false;
                $('#batch-toBin-'+uuid).hasError();
                errMsg = "กรุณาระบุโซนปลายทาง";
                return false;
              }

              row.batchRows.push({
                'ItemCode' : itemCode,
                'ItemName' : itemName,
                'BatchNum' : ro.data('batchnum'),
                'BatchAttr1' : ro.data('attr1'),
                'BatchAttr2' : ro.data('attr2'),
                'Qty' : bQty,
                'fromWhsCode' : ro.data('fromwhs'),
                'fromBinCode' : ro.data('frombin'),
                'toWhsCode' : bToWhs,
                'toBinCode' : bToBin,
                'uid' : uuid
              });

            });

            if(trQty != sumBatchQty) {
              el.hasError();
              sc = false;
              errMsg = "จำนวนไม่ถูกต้อง กรุณาแก้ไข";
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
  let fromWhsCode = $('#fromWhsCode').val();
  let toWhsCode = $('#toWhsCode').val();
  let toBinCode = $('#bin-code').val().trim();

  if(baseCode.length) {
    $('#production-modal-title').text(baseCode + '  |  ' + baseItem);

    load_in();

    $.ajax({
      url:HOME + 'get_production_order_details',
      type:'POST',
      cache:false,
      data:{
        'baseCode' : baseCode,
        'toBinCode' : toBinCode,
        'toWhsCode' : toWhsCode
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

            $('#production-modal').modal('show');
            dragElement('production-modal', 'production-modal-header'); // make modal moveable send modal id and modal-heade id
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


//---- add production line to transfer
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
        'fromWhsCode' : el.data('fromwhs'),
        'toWhsCode' : el.data('towhs'),
        'toBinCode' : el.data('tobin'),
        'ItemCode' : el.data('code'),
        'ItemName' : el.data('name'),
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
      fromWhsInit(uuid);
      toWhsInit(uuid);
      fromBinInit(uuid);
      toBinInit(uuid);
    });
  }
}


function detailsInit() {
  $('.tran-qty').each(function() {
    let uid = $(this).data('uid');
    fromWhsInit(uid);
    toWhsInit(uid);
    fromBinInit(uid);
    toBinInit(uid);

    $('.child-of-' + uid).each(function() {
      let uuid = $(this).data('uid');

      batchToWhsInit(uuid);
      batchToBinInit(uuid);
    })
  })
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


function fromWhsInit(uid) {
  $('#from-whs-'+uid).autocomplete({
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


function toWhsInit(uid) {
  $('#to-whs-'+uid).autocomplete({
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


function fromBinInit(uid, clear) {
  let fromWhsCode = $('#from-whs-'+uid).val();

  if(clear) {
    $('#from-bin-'+uid).val('');
  }

  $('#from-bin-'+uid).autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+fromWhsCode,
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


function toBinInit(uid, clear) {
  let toWhsCode = $('#to-whs-'+uid).val();

  if(clear) {
    $('#to-bin-'+uid).val('');
  }

  $('#to-bin-'+uid).autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+toWhsCode,
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


function itemInit(uid) {
  let item = null;
  $('#item-code-'+uid).autocomplete({
    source:HOME + 'get_item_code_and_nam',
    autoFocus:true,
    select:function(event, ui) {
      item = ui.item;
    },
    close:function(event, ui) {
      if(item != null && item != undefined) {
        $('#item-code-'+uid).val(item.ItemCode);
        $('#item-code-'+uid).data('hasbatch', item.hasBatch);
        $('#item-name-'+uid).val(item.ItemName);
        $('#tran-qty-'+uid).data('code', item.ItemCode);
        $('#tran-qty-'+uid).data('name', item.ItemName);
        $('#tran-qty-'+uid).data('hasbatch', item.hasBatch);
        $('#tran-qty-'+uid).data('uomentry', item.UomEntry);
        $('#tran-qty-'+uid).data('uomcode', item.UomCode);
        $('#tran-qty-'+uid).data('uom', item.Uom);
        $('#uom-'+uid).val(item.Uom);
        $('#uom-'+uid).data('uomentry', item.UomEntry);
        $('#uom-'+uid).data('uomcode', item.UomCode);

        if(item.hasBatch == 'Y') {
          $('#batch-td-'+uid).html(`<a class="pointer add-batch" href="javascript:getPreBatch('${uid}')" title="Add Batch Number">
            <i class="fa fa-plus fa-lg blue"></i>
          </a>`);
        }
        else {
          $('#batch-btn-'+uid).html('');
        }

        getAvailableStock(uid);
      }
    }
  })
}


function batchToWhsInit(uid) {
  $('#batch-toWhs-'+uid).autocomplete({
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


function batchToBinInit(uid, clear) {
  let whsCode = $('#batch-toWhs-'+uid).val();
  if(clear) {
    $('#batch-toBin-'+uid).val('');
  }

  $('#batch-toBin-'+uid).autocomplete({
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


function getPreBatch(uid) {
  $('#item-code-'+uid).clearError();
  let itemCode = $('#item-code-'+uid).val().trim();
  let whsCode = $('#from-whs-'+uid).val();


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
  let rQty = parseDefaultFloat(removeCommas($('#tran-qty-'+uid).val()), 0);

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
          $('#target-qty').val(rQty);
          $('#tr-qty').text(addCommas(rQty));

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


function addBatchRows() {
  clearErrorByClass('bi-qty');
  let error = 0;
  let ds = []; //--- for new rows
  let us = []; //--- for update rows
  let uid = $('#target-uid').val();
  let toWhsCode = $('#to-whs-'+uid).val().trim();
  let toBinCode = $('#to-bin-'+uid).val().trim();

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
            'toWhsCode' : toWhsCode,
            'toBinCode' : toBinCode,
            'InStock' : addCommas(onHand.toFixed(2)),
            'qty' : addCommas(qty.toFixed(2)),
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

      if(ds.length > 0) {
        ds.forEach((ro) => {
          batchToWhsInit(ro.uid);
          batchToBinInit(ro.uid);
          setTimeout(() => {
            $('#batch-toBin-'+ro.uid).val(toBinCode);
          },50);
        })
      }

      $('#batch-modal').modal('hide');

      reCalBatchRows(uid);
    }
  }
}


function removeBatchRow(uid) {
  let parentUid = $('#batch-qty-'+uid).data('parent');
  $('#batch-rows-'+uid).remove();

  reCalBatchRows(parentUid);
}


function addRow() {
  let uid = generateUID();
  let fromWhsCode = $('#fromWhsCode').val();
  let toWhsCode = $('#toWhsCode').val();
  let toBinCode = $('#bin-code').val();
  let source = $('#row-template').html();
  let output = $('#details-table');
  let data = {
    'uid' : uid,
    'fromWhsCode' : fromWhsCode,
    'toWhsCode' : toWhsCode,
    'toBinCode' : toBinCode
  }

  render_append(source, data, output);

  reIndex();

  setTimeout(() => {
    itemInit(uid);
    fromWhsInit(uid);
    toWhsInit(uid);
    fromBinInit(uid);
    toBinInit(uid);

    $('#to-bin-'+uid).val(toBinCode);
    $('#item-code-'+uid).focus();
  }, 200);

}


function removeRow(uid) {
  $('#row-'+uid).remove();
  $('.child-of-'+uid).remove();

  reIndex('no');
}


function getAvailableStock(uid) {
  let itemCode = $('#item-code-'+uid).val().trim();
  let whsCode = $('#from-whs-'+uid).val().trim();
  let binCode = $('#from-bin-'+uid).val().trim();

  if(whsCode.length && itemCode.length) {
    $.ajax({
      url:HOME + 'get_available_stock',
      type:'POST',
      cache:false,
      data:{
        'ItemCode' : itemCode,
        'WhsCode' : whsCode,
        'BinCode' : binCode
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


function getBinStock(uid) {
  let itemCode = $('#item-code-'+uid).val().trim();
  let itemName = $('#item-name-'+uid).val().trim();
  let whsCode = $('#from-whs-'+uid).val().trim();

  if(itemCode.length && whsCode.length) {
    $('#bin-modal-title').text(itemCode + ' | '+whsCode);
    $('#from-bin-stock-uid').val(uid);

    load_in();

    $.ajax({
      url:HOME + 'get_bin_item_stock',
      type:'POST',
      cache:false,
      data:{
        'ItemCode' : itemCode,
        'WhsCode' : whsCode
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let source = $('#bin-modal-template').html();
            let output = $('#bin-modal-table');

            render(source, ds.data, output);

            $('#bin-modal').modal('show');

            dragElement('bin-modal', 'bin-modal-header');
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


//-- add Bin location - qty to item row
function addToRow() {
  clearErrorByClass('bin-qty');

  let uid = $('#from-bin-stock-uid').val();
  let qty = 0;
  let binCode = '';
  let error = 0;

  $('.bin-qty').each(function() {
    let q = parseDefaultFloat(removeCommas($(this).val()), 0);
    if(q > 0) {
      let instock = parseDefaultFloat(removeCommas($(this).data('instock')), 0);

      if(q > instock) {
        $(this).hasError();
        error++;
        return false;
      }

      qty = q;
      binCode = $(this).data('bin');
    }
  });

  if(error == 0) {
    $('#from-bin-'+uid).val(binCode);
    $('#tran-qty-'+uid).val(addCommas(qty));
    $('#bin-modal').modal('hide');

    getAvailableStock(uid);
  }
}


function reCalBatchRows(parentUid) {
  let pQty = 0;

  $('.child-of-'+parentUid).each(function() {
    let uid = $(this).data('uid');
    let qty = parseDefaultFloat(removeCommas($('#batch-qty-'+uid).val()), 0);
    pQty += qty;
  });

  $('#tran-qty-'+parentUid).val(addCommas(pQty.toFixed(2)));
}
