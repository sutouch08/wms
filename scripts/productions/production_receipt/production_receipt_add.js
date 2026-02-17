window.addEventListener('load', () => {
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


$('#base-ref').autocomplete({
  source:HOME + 'get_production_order_code',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
      $('#base-item').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#base-item').val('');
    }
  }
})


$('#base-ref').keyup(function(e) {
  if(e.keyCode === 13) {
    setTimeout(() => {
      getOrderData();
    }, 150);
  }
})


function detailsInit() {
  $('.receipt-qty').each(function() {
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
      'ItemCode' : $('#base-item').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

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

    let error = 0;
    let errMsg = "";
    let line = 0;
    let sc = true;

    $('.receipt-qty').each(function() {
      if(sc === true) {
        let el = $(this);
        let uid = el.data('uid');
        let whsCode = $('#whs-'+uid).val().trim();
        let binCode = $('#bin-'+uid).val().trim();
        let receiptQty = parseDefaultFloat(removeCommas(el.val()), 0);
        let hasBatch = el.data('hasbatch') == 'Y' ? 1 : 0;

        if(receiptQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

        if(whsCode.length == 0) {
          sc = false;
          $('#whs-'+uid).hasError();
          errMsg = "กรุณาระบุคลัง";
          return false;
        }

        if(binCode.length == 0) {
          sc = false;
          $('#bin-'+uid).hasError();
          errMsg = "กรุณาระบุโซน";
          return false;
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
            'hasBatch' : hasBatch,
            'ItemCode' : itemCode,
            'ItemName' : itemName,
            'WhsCode' : whsCode,
            'BinCode' : binCode,
            'TranType' : $('#tran-type-'+uid).val(),
            'Qty' : receiptQty,
            'Uom' : el.data('uom'),
            'UomCode' : el.data('uomcode'),
            'UomEntry' : el.data('uomentry'),
            'batchRows' : []
          };

          if(hasBatch == 1) {
            let sumBatchQty = 0;
            let batches = [];

            $('.child-of-'+uid).each(function() {
              if(sc === false) { return false; }

              let uuid = $(this).data('uid');
              let ro = $('#batch-qty-'+uuid);
              let batchNum = $('#batch-'+uuid).val().trim();
              let batchAttr1 = $('#batch-attr1-'+uuid).val().trim();
              let batchAttr2 = $('#batch-attr2-'+uuid).val().trim();

              if(batchNum == "" || batchNum == null || batchNum == undefined) {
                sc = false;
                $('#batch-'+uuid).hasError();
                errMsg = "กรุณาระบุ Batch No.";
                return false;
              }

              if(sc === true) {
                if(batches.includes(batchNum)) {
                  sc = false;
                  $('#batch-'+uuid).hasError();
                  errMsg = "Batch No. ซ้ำ";
                  return false;
                }
                else {
                  batches.push(batchNum);
                }
              }

              let bQty = parseDefaultFloat(ro.val(), 0);
              sumBatchQty += bQty;

              row.batchRows.push({
                'ItemCode' : itemCode,
                'ItemName' : itemName,
                'BatchNum' : batchNum,
                'BatchAttr1' : batchAttr1,
                'BatchAttr2' : batchAttr2,
                'Qty' : bQty,
                'WhsCode' : whsCode,
                'BinCode' : binCode,
                'uid' : uuid
              });
            });

            if(sc === true && roundNumber(receiptQty, 4) != roundNumber(sumBatchQty, 4)) {
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
  let code = $('#code').val().trim();

  if(click == 0) {
    click = 1;

    clearErrorByClass('h');
    clearErrorByClass('r');

    let h = {
      'type' : type,
      'code' : code,
      'date_add' : $('#date-add').val().trim(),
      'shipped_date' : $('#posting-date').val().trim(),
      'baseRef' : $('#base-ref').val().trim(),
      'orderRef' : $('#order-ref').val().trim(),
      'ItemCode' : $('#base-item').val().trim(),
      'remark' : $('#remark').val().trim(),
      'rows' : []
    };

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

    let error = 0;
    let errMsg = "";
    let line = 0;
    let sc = true;

    $('.receipt-qty').each(function() {
      if(sc === true) {
        let el = $(this);
        let uid = el.data('uid');
        let whsCode = $('#whs-'+uid).val().trim();
        let binCode = $('#bin-'+uid).val().trim();
        let receiptQty = parseDefaultFloat(removeCommas(el.val()), 0);
        let hasBatch = el.data('hasbatch') == 'Y' ? 1 : 0;

        if(receiptQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

        if(whsCode.length == 0) {
          sc = false;
          $('#whs-'+uid).hasError();
          errMsg = "กรุณาระบุคลัง";
          return false;
        }

        if(binCode.length == 0) {
          sc = false;
          $('#bin-'+uid).hasError();
          errMsg = "กรุณาระบุโซน";
          return false;
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
            'hasBatch' : hasBatch,
            'ItemCode' : itemCode,
            'ItemName' : itemName,
            'WhsCode' : whsCode,
            'BinCode' : binCode,
            'TranType' : $('#tran-type-'+uid).val(),
            'Qty' : receiptQty,
            'Uom' : el.data('uom'),
            'UomCode' : el.data('uomcode'),
            'UomEntry' : el.data('uomentry'),
            'batchRows' : []
          };

          if(hasBatch == 1) {
            let sumBatchQty = 0;
            let batches = [];

            $('.child-of-'+uid).each(function() {
              if(sc === false) { return false; }

              let uuid = $(this).data('uid');
              let ro = $('#batch-qty-'+uuid);
              let batchNum = $('#batch-'+uuid).val().trim();
              let batchAttr1 = $('#batch-attr1-'+uuid).val().trim();
              let batchAttr2 = $('#batch-attr2-'+uuid).val().trim();

              if(batchNum == "" || batchNum == null || batchNum == undefined) {
                sc = false;
                $('#batch-'+uuid).hasError();
                errMsg = "กรุณาระบุ Batch No.";
                return false;
              }

              if(sc === true) {
                if(batches.includes(batchNum)) {
                  sc = false;
                  $('#batch-'+uuid).hasError();
                  errMsg = "Batch No. ซ้ำ";
                  return false;
                }
                else {
                  batches.push(batchNum);
                }
              }

              let bQty = parseDefaultFloat(ro.val(), 0);
              sumBatchQty += bQty;

              row.batchRows.push({
                'ItemCode' : itemCode,
                'ItemName' : itemName,
                'BatchNum' : batchNum,
                'BatchAttr1' : batchAttr1,
                'BatchAttr2' : batchAttr2,
                'Qty' : bQty,
                'WhsCode' : whsCode,
                'BinCode' : binCode,
                'uid' : uuid
              });
            });

            if(sc === true && roundNumber(receiptQty, 4) != roundNumber(sumBatchQty, 4)) {
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
                  viewDetail(code);
                }, 1200);
              }
              else {
                swal({
                  title:'Oops !',
                  text:ds.message,
                  type:'info'
                }, function() {
                  viewDetail(code);
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
      url:HOME + 'get_production_order_data',
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


//---- add production line to receipt
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
      let tranType = $('#pi-type-'+uid).val();

      ds.push({
        'uid' : uid,
        'Qty' : qty,
        'whsCode' : el.data('whscode'),
        'ItemCode' : el.data('code'),
        'ItemName' : el.data('name'),
        'BaseEntry' : el.data('baseentry'),
        'BaseRef' : el.data('baseref'),
        'BaseType' : el.data('basetype'),
        'ManBtchNum' : el.data('hasbatch'),
        'hasBatch' : el.data('hasbatch') == 'Y' ? true : false,
        'UomEntry' : el.data('uomentry'),
        'UomCode' : el.data('uomcode'),
        'UomName' : el.data('uom'),
        'tranComplete' : tranType == 'C' ? 'selected' : '',
        'tranReject' : tranType == 'R' ? 'selected' : ''
      });
    }
  });

  if(ds.length) {
    let source = $('#details-template').html();
    let output = $('#details-table');
    render_append(source, ds, output);
    reIndex('no');

    uids.forEach((uuid, i) => {
      whsInit(uuid);
      binInit(uuid);
    });
  }

  reCalTotal();
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
  let zone = $('#bin-'+uid);

  if(zone.data('whs') != whsCode) {
    zone.val('');
    zone.data('whs', '');
  }

  $('#bin-'+uid).autocomplete({
    source:HOME + 'get_zone_code_and_name/'+whsCode,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');
      if(arr.length == 3) {
        $(this).val(arr[0]);
        $(this).data('whs', arr[2]);
      }
      else {
        $(this).val('');
        $(this).data('whs', '');
      }
    }
  })
}


function addBatchRow(parentUid) {
	let el = $('#receipt-qty-'+parentUid);
	let no = parseDefaultInt(el.data('no'), 0);
	let ne = no + 1;
	el.data('no', ne);

	let cuid = el.data('uid'); // pre uid for last child row
	let uid = generateUID(); //--- new child row uid

	$('.child-of-'+parentUid).each(function() {
		cuid = $(this).data('uid'); //-- find leatest batch row in parent
	});

	let ds = {
		'parentUid' : parentUid,
		'uid' : uid,
		'UomName' : el.data('uom')
	};

	let source = $('#batch-row-template').html();
	let output = $('#batch-row-'+cuid).length ? $('#batch-row-'+cuid) : $('#row-'+parentUid);

	render_after(source, ds, output);
	batchInit(uid);
	setTimeout(() => {
		$('#batch-'+uid).focus();
	}, 100)

  reCalBatchRows(parentUid);
}

//--- for copy paste batch number
function newBatchRow(parentUid) {
  let el = $('#receipt-qty-'+parentUid);
	let no = parseDefaultInt(el.data('no'), 0);
	let ne = no + 1;
	el.data('no', ne);

	let cuid = el.data('uid'); // pre uid for last child row
	let uid = generateUID(); //--- new child row uid

	$('.child-of-'+parentUid).each(function() {
		cuid = $(this).data('uid'); //-- find leatest batch row in parent
	});

	let ds = {
		'parentUid' : parentUid,
		'uid' : uid,
		'UomName' : el.data('uom')
	};

	let source = $('#batch-row-template').html();
	let output = $('#batch-row-'+cuid).length ? $('#batch-row-'+cuid) : $('#row-'+parentUid);

	render_after(source, ds, output);
	batchInit(uid);
  return uid;
}


function batchInit(cid) {
	if(cid == "" || cid == undefined) {
		$('.batch-row').keyup(function(e) {
			if(e.keyCode === 13) {
				if($(this).val().trim() != "") {
					let uid = $(this).data('uid');
					let pid = $(this).data('parent');
					let val = $(this).val().trim();
					let arr = val.split(' | ');

					if(arr.length == 3) {

						if(arr[0].length) {
							$('#batch-'+uid).val(arr[0]);
						}

						if(arr[1].length) {
							$('#batch-attr1-'+uid).val(arr[1]);
						}

						if(arr[2].length) {
							let qty = parseDefaultFloat(arr[2], 0);

							if(qty > 0) {
								$('#batch-qty-'+uid).val(qty);
								addBatchRow(pid);
							}
							else {
								$('#batch-qty-'+uid).val('').focus();
							}
						}
						else {
							$('#batch-qty-'+uid).val('').focus();
						}
					}
					else {
						$('#batch-attr1-'+uid).focus();
					}
				}
			}
		});

		$('.batch-attr1').keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-attr2-'+uid).focus();
			}
		});

		$('.batch-attr2').keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-qty-'+uid).focus();
			}
		});

		$('.batch-qty').keyup(function(e) {
			if(e.keyCode === 13) {
				let qty = parseDefaultFloat($(this).val(), 0);

				if(qty > 0) {
					let uid = $(this).data('parent');
					addBatchRow(uid);
				}
			}
		})

    $('.batch-qty').change(function() {
      let uid = $(this).data('parent');
      reCalBatchRows(uid);
		})
	}
	else {
		$('#batch-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				if($(this).val().trim() != "") {
					let uid = $(this).data('uid');
					let pid = $(this).data('parent');
					let val = $(this).val().trim();
					let arr = val.split(' | ');

					if(arr.length == 3) {

						if(arr[0].length) {
							$('#batch-'+uid).val(arr[0]);
						}

						if(arr[1].length) {
							$('#batch-attr1-'+uid).val(arr[1]);
						}

						if(arr[2].length) {
							let qty = parseDefaultFloat(arr[2], 0);

							if(qty > 0) {
								$('#batch-qty-'+uid).val(qty);
								addBatchRow(pid);
							}
							else {
								$('#batch-qty-'+uid).val('').focus();
							}
						}
						else {
							$('#batch-qty-'+uid).val('').focus();
						}
					}
					else {
						$('#batch-attr1-'+uid).focus();
					}
				}
			}
		});

		$('#batch-attr1-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-attr2-'+uid).focus();
			}
		});

		$('#batch-attr2-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				let uid = $(this).data('uid');
				$('#batch-qty-'+uid).focus();
			}
		});

		$('#batch-qty-'+cid).keyup(function(e) {
			if(e.keyCode === 13) {
				let qty = parseDefaultFloat($(this).val(), 0);

				if(qty > 0) {
					let uid = $(this).data('parent');
					addBatchRow(uid);
				}
			}
		})

    $('#batch-qty-'+cid).change(function() {
      let uid = $(this).data('parent');
      reCalBatchRows(uid);
		})
	}
}


function removeBatchRow(uid) {
  let parentUid = $('#batch-qty-'+uid).data('parent');
  $('#batch-row-'+uid).remove();

  reCalBatchRows(parentUid);
}


function removeRow(uid) {
  $('#row-'+uid).remove();
  $('.child-of-'+uid).remove();

  reIndex('no');

  reCalTotal();
}


function reCalBatchRows(parentUid) {
  if($('.child-of-'+parentUid).length) {
    let pQty = 0;
    $('.child-of-'+parentUid).each(function() {
      let uid = $(this).data('uid');
      let qty = parseDefaultFloat(removeCommas($('#batch-qty-'+uid).val()), 0);
      pQty += qty;
    });

    $('#receipt-qty-'+parentUid).val(addCommas(pQty.toFixed(2)));
  }

  reIndex('b-'+parentUid);

  reCalTotal();
}


function reCalTotal() {
  let totalQty = 0;
  let totalItems = $('.receipt-qty').length;
  let totalBatch = $('.batch-qty').length;

  $('.receipt-qty').each(function() {
    totalQty += parseDefaultFloat(removeCommas($(this).val()), 0);
  });

  $('#total-item-row').val(addCommas(totalItems.toFixed(2)));
  $('#total-batch-row').val(addCommas(totalBatch.toFixed(2)));
  $('#total-item-qty').val(addCommas(totalQty.toFixed(2)));
}
