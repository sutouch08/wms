var autoFocus = 1;

window.addEventListener('load', () => {
  focus_init();
  setFocus();
});


function focus_init() {
	$('.focus').focusout(function() {
    autoFocus = 1;
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


function editBoxQty(el) {
  focus_init();
  el.focus();
}


function setFocus(table) {
  table = table === undefined || table === null ? $('#active-focus').val() : table;

  $('#active-focus').val(table);

  if(table == 'B') {
    if($('#box-item-bc').hasClass('hide')) {
      $('#box-barcode-zone').focus();
    }
    else {
      $('#box-barcode-item').focus();
    }
  }

  if(table == 'F') {
    if($('#from-item-bc').hasClass('hide')) {
      $('#from-barcode-zone').focus();
    }
    else {
      $('#from-barcode-item').focus();
    }
  }

  if(table == 'T') {
    if($('#to-item-bc').hasClass('hide')) {
      $('#to-barcode-zone').focus();
    }
    else {
      $('#to-barcode-item').focus();
    }
  }
}


function showMoveTable(table) {
  $('.move-table').addClass('hide');
  closeExtraMenu();

  if(table === 'L') {
    getMoveTable();
    $('#move-table').removeClass('hide');
    return;
  }

  if(table === 'Z') {
    $('#zone-table').removeClass('hide');
    setFocus('F');
    return;
  }

  if(table === 'T') {
    getTempTable();

    $('#temp-table').removeClass('hide');
    setFocus('T');
  }

  if(table === 'B') {

    $('#box-table').removeClass('hide');
    setFocus('B');
  }
}


function toggleHeader() {
  closeExtraMenu();
  let pad = $('#header-pad');
  if(pad.hasClass('move-in')) {
    pad.removeClass('move-in');
  }
  else {
    pad.addClass('move-in');
  }
}


function closeHeader() {
  $('#header').val('hide');
  $('#header-pad').removeClass('move-in');
}


function toggleExtraMenu() {
  let hd = $('#extra');
  let pad = $('#extra-menu');

  if(hd.val() == "hide") {
    hd.val("show");
    pad.addClass('slide-in');
  }
  else {
    hd.val("hide");
    pad.removeClass('slide-in');
  }
}


function closeExtraMenu() {
  $('#extra').val('hide');
  $('#extra-menu').removeClass('slide-in');
}


$('#from-barcode-zone').keyup(function(e) {
  if(e.keyCode === 13) {
    console.log('ok');
    let bZone = $(this).val().trim();
    let whsCode = $('#from-warehouse-code').val();

    if(bZone.length > 0) {
      getFromZone(bZone, whsCode);
    }
  }
});


$('#from-barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val() != "") {
      addToTemp();
    }
  }
});


$('#btn-from-increse').click(function() {
  let qty = parseDefault(parseFloat($('#from-qty').val()), 0);
  qty++;
  $('#from-qty').val(qty);
  $('#from-barcode-item').focus();
})


$('#btn-from-decrese').click(function() {
  let qty = parseDefault(parseFloat($('#from-qty').val()), 0);

  if(qty > 0) {
    qty--;
  }
  else {
    qty = 0;
  }

  $('#from-qty').val(qty);
  $('#from-barcode-item').focus();
})


function getFromZone(bZone, whsCode) {
  let code = $('#transfer-code').val().trim();

  if(bZone != "" && bZone !== undefined && bZone !== null) {
    load_in();

    $.ajax({
      url:HOME + '/get_from_zone',
      type:'GET',
      cache:false,
      data:{
        'transfer_code' : code,
        'warehouse_code' : whsCode,
        'zone_code' : bZone
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status === 'success') {
            $('#from-zone-code').val(ds.zone.code);
            $('#from-zone-name').text(ds.zone.name);
            $('#from-zone-bc').addClass('hide');
            $('#from-item-qty').removeClass('hide');
            $('#from-item-bc').removeClass('hide');
            $('#from-qty').val(1);
            $('#from-barcode-item').val('').focus();
          }
          else {
            beep();

            $('#from-barcode-zone').val('');

            showError(ds.message);
          }
        }
        else {
          beep();
          $('#from-barcode-zone').val('');
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        $('#from-barcode-zone').val('');
        showError(rs);
      }
    })
  }
}


function getMoveTable() {
	let code	= $("#transfer-code").val();
  load_in();
	$.ajax({
		url: HOME + 'get_transfer_table/'+ code,
		type:"GET",
    cache:"false",
		success: function(rs) {
      load_out();

			if( isJson(rs) ) {
				var source 	= $("#moveTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#move-list");
				render(source, data, output);
        reCal();
			}
		},
    error:function(rs) {
      showError(rs);
    }
	});
}


function addToTemp() {
  let transfer_code = $('#transfer-code').val().trim();
  let zone_code = $('#from-zone-code').val().trim();
  let barcode = $('#from-barcode-item').val().trim();

  $('#from-barcode-item').val('').blur();

  if(zone_code.length == 0) {
    beep();
    showError("กรุณาระบุโซน");
    $('#from-barcode-item').focus();
    return false;
  }

  if(barcode.length == 0) {
    beep();
    showError("กรุณาแสกนสินค้า");
    $('#from-barcode-item').focus();
    return false;
  }

  let qty = parseDefault(parseFloat($('#from-qty').val().trim()), 1);

  load_in();

  $.ajax({
    url:HOME + 'add_to_temp',
    type:'POST',
    cache:false,
    data:{
      "transfer_code" : transfer_code,
      "from_zone" : zone_code,
      "qty" : qty,
      "barcode" : barcode,
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status === 'success') {
          let source = $('#zoneTemplate').html();
          let data = ds.data;
          let output = $('#zone-list');

          if($('#row-'+barcode).length) {
            $('#row-'+barcode).remove();
          }

          render_prepend(source, data, output);
          reIndex();

          $('.zone-table-item').removeClass('highlight');
          $('#row-'+barcode).addClass('highlight');
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

      //---	reset จำนวนเป็น 1
      $("#from-qty").val(1);

      //---	focus ที่ช่องยิงบาร์โค้ด รอการยิงต่อไป
      $("#from-barcode-item").focus();
    },
    error:function(rs) {
      beep();
      showError(rs);

      //---	reset จำนวนเป็น 1
      $("#from-qty").val(1);

      //---	focus ที่ช่องยิงบาร์โค้ด รอการยิงต่อไป
      $("#from-barcode-item").focus();
    }
  })
}


function getTempTable(){
	let code = $("#transfer-code").val();
  load_in();
	$.ajax({
		url: HOME + 'get_temp_table/'+code,
		type:"GET",
    cache:"false",
		success: function(rs) {
      load_out();
			if( isJson(rs) ){
				var source 	= $("#tempTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#temp-list");
				render(source, data, output);
        recalTemp();
			}
		},
    error:function(rs) {
      showError(rs);
    }
	});
}


$('#to-barcode-zone').keyup(function(e) {
  if(e.keyCode === 13) {
    let bZone = $(this).val().trim();
    let whsCode = $('#to-warehouse-code').val().trim();

    if(bZone.length > 0) {
      getToZone(bZone, whsCode);
    }
  }
});


$('#to-barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val().trim() != "") {
      moveToZone();
    }
  }
});


$('#box-barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val().trim() != "") {
      addToBox();
    }
  }
});


$('#btn-to-increse').click(function() {
  let qty = parseDefault(parseFloat($('#to-qty').val()), 0);
  qty++;
  $('#to-qty').val(qty);
  $('#to-barcode-item').focus();
})


$('#btn-to-decrese').click(function() {
  let qty = parseDefault(parseFloat($('#to-qty').val()), 0);

  if(qty > 0) {
    qty--;
  }
  else {
    qty = 0;
  }

  $('#to-qty').val(qty);
  $('#to-barcode-item').focus();
})


$('#btn-box-increse').click(function() {
  let qty = parseDefault(parseFloat($('#box-qty').val()), 0);
  qty++;
  $('#box-qty').val(qty);
  $('#box-barcode-item').focus();
})


$('#btn-box-decrese').click(function() {
  let qty = parseDefault(parseFloat($('#box-qty').val()), 0);

  if(qty > 0) {
    qty--;
  }
  else {
    qty = 0;
  }

  $('#box-qty').val(qty);
  $('#box-barcode-item').focus();
})


function getToZone() {
  let code = $('#transfer-code').val().trim();
  let bZone = $('#to-barcode-zone').val().trim();
  let whsCode = $('#to-warehouse-code').val();

  if(bZone != "" && bZone !== undefined && bZone !== null) {
    load_in();

    $.ajax({
      url:HOME + 'get_to_zone',
      type:'GET',
      cache:false,
      data:{
        'transfer_code' : code,
        'warehouse_code' : whsCode,
        'zone_code' : bZone
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status === 'success') {
            $('#to-zone-code').val(ds.zone.code);
            $('#to-zone-name').text(ds.zone.name);
            $('#to-zone-bc').addClass('hide');
            $('#to-item-qty').removeClass('hide');
            $('#to-item-bc').removeClass('hide');
            $('#to-qty').val(1);
            $('#to-barcode-item').val('').focus();
          }
          else {
            beep();

            $('#to-barcode-zone').val('');

            showError(ds.message);
          }
        }
        else {
          beep();
          $('#to-barcode-zone').val('');
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        $('#to-barcode-zone').val('');
        showError(rs);
      }
    })
  }
}


function getBoxZone() {
  let code = $('#transfer-code').val().trim();
  let bZone = $('#box-barcode-zone').val().trim();
  let whsCode = $('#to-warehouse-code').val();

  if(bZone != "" && bZone !== undefined && bZone !== null) {
    load_in();

    $.ajax({
      url:HOME + 'get_to_zone',
      type:'GET',
      cache:false,
      data:{
        'transfer_code' : code,
        'warehouse_code' : whsCode,
        'zone_code' : bZone
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status === 'success') {
            $('#box-zone-code').val(ds.zone.code);
            $('#box-zone-name').text(ds.zone.name);
            $('#box-zone-bc').addClass('hide');
            $('#box-item-qty').removeClass('hide');
            $('#box-item-bc').removeClass('hide');
            $('#box-qty').val(1);
            $('#box-barcode-item').val('').focus();
            $('#box-total').text(0);
          }
          else {
            beep();

            $('#box-barcode-zone').val('');

            showError(ds.message);
          }
        }
        else {
          beep();
          $('#box-barcode-zone').val('');
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        $('#box-barcode-zone').val('');
        showError(rs);
      }
    })
  }
}


function moveToZone() {
  let bc = $('#to-barcode-item');
  let transfer_code = $('#transfer-code').val();
  let zone_code = $('#to-barcode-zone').val().trim();
  let barcode = bc.val().trim();
  let product_code = $('#bc-'+barcode).val();
  let qty = parseDefault(parseFloat($('#to-qty').val()), 1);

  if(product_code == "" | product_code === null || product_code === undefined) {
    beep();
    swal({
      title:'สินค้าไม่ถูกต้อง',
      type:'error'
    }, function() {
      $('#to-barcode-item').val('').focus();
    });

    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'move_to_zone',
    type:"POST",
    cache:"false",
    data:{
      "transfer_code" : transfer_code,
      "zone_code" : zone_code,
      "qty" : qty,
      "product_code" : product_code,
      "barcode" : barcode
    },
    success: function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          if(ds.data !== null && ds.data !== undefined && ds.data !== "") {
            let temp = ds.data;
            let temp_qty = parseDefault(parseFloat(temp.qty), 0);

            $('.row-temp').removeClass('highlight');

            if(temp_qty <= 0) {
              $('#row-temp-'+temp.id).remove();
              reIndex('tmp');
            }
            else {
              $("#qty-temp-"+temp.id).text(addCommas(temp_qty));
              $('#temp-list').prepend($('#row-temp-'+temp.id));
              $('#row-temp-'+temp.id).addClass('highlight');
            }


            recalTemp();

            $("#to-qty").val(1);
            $('#to-barcode-item').val('').focus();
          }
          else {
            showError("No temp data response");
            $("#to-qty").val(1);
            bc.focus();
          }
        }
        else {
          beep();
          showError(ds.message);
          $("#to-qty").val(1);
          bc.focus();
        }
      }
      else {
        beep();
        showError(rs);
        $("#to-qty").val(1);
        bc.focus();
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
      $("#to-qty").val(1);
      bc.focus();
    }
  });
}


function addToBox() {
  let barcode = $('#box-barcode-item').val().trim();

  if(barcode.length) {
    let product_code = $('#box-item-'+barcode).val();
    let qty = parseDefault(parseFloat($('#box-qty').val()), 1);

    if(product_code == "" || product_code == null || product_code == undefined) {
      beep();
      swal({
        title:'Error!',
        text:'ไม่พบรายการสินค้า',
        type:'error'
      }, function() {
        $('#box-barcode-item').val('').focus();
      });
    }
    else {
      $('.box-table-item').removeClass('highlight');

      if($('#box-'+barcode).length) {
        let cQty = parseDefault(parseFloat($('#box-qty-'+barcode).val()), 0);
        let nQty = cQty + qty;
        $('#box-qty-'+barcode).val(nQty);
        $('#box-list').prepend($('#box-'+barcode));
        $('#box-'+barcode).addClass('highlight');
        $('#box-qty').val(1);
        $('#box-barcode-item').val('').focus();
      }
      else {
        let source = $('#boxTemplate').html();
        let data = {
          'barcode' : barcode,
          'product_code' : product_code,
          'qty' : qty
        };
        let output = $('#box-list');
        render_prepend(source, data, output);
        $('#box-'+barcode).addClass('highlight');
        $('#box-barcode-item').val('').focus();
      }

      reIndex('box-no');

      recalBox();
    }
  } //--- endif barcode.length
}


function saveBox() {
  closeExtraMenu();

  let h = {
    'transfer_code' : $('#transfer-code').val(),
    'zone_code' : $('#box-barcode-zone').val().trim(),
    'items' : []
  };

  if(h.zone_code.length == 0) {
    beep();
    showError('กรุณาระบุโซน');
    return false;
  }

  $('.box-item').each(function() {
    let qty = parseDefault(parseFloat($(this).val()), 0);

    if(qty > 0) {
      let pdCode = $(this).data('code');
      h.items.push({'product_code' : pdCode, 'qty' : qty});
    }
  });

  if(h.items.length === 0) {
    beep();
    showError('ไม่พบรายการ');
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'save_to_zone',
    type:"POST",
    cache:"false",
    data:{
      "data" : JSON.stringify(h)
    },
    success: function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#box-list').html('');
          $('#box-qty').val(1);
          $('#box-total').text(0);
          $('#box-barcode-item').val('').focus();
        }
        else {
          beep();
          showError(ds.message);
          $("#box-qty").val(1);
          $('#box-barcode-item').val('').focus();
        }
      }
      else {
        beep();
        showError(rs);
        $("#box-qty").val(1);
        $('#box-barcode-item').val('').focus();
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
      $("#box-qty").val(1);
      $('#box-barcode-item').val('').focus();
    }
  });
}


function rollBackToTemp(id, product_code) {
	let code = $('#transfer-code').val();

  swal({
		title: 'คุณแน่ใจ ?',
		text: 'เมื่อลบแล้วรายการจะถูกดึงกลับเข้า temp ต้องการลบ '+ product_code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'roll_back_to_temp',
			type:"POST",
      cache:false,
			data:{
        'transfer_code' : code,
				'product_code' : product_code,
				'id' : id
			},
			success: function(rs) {
				if( rs.trim() == 'success' ) {
				   swal({
             title:'Success',
             type:'success',
             timer:1000
           });

					$('#wms-'+id).text('0');

					reIndex('mo');

					reCal();
				}
        else {
					showError(rs);
				}
			},
      error:function(rs) {
        showError(rs);
      }
		});
	});
}


function deleteMoveTemp(id, code) {
  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'delete_temp/'+ id,
			type:"POST",
      cache:false,
			data:{
				'code' : code,
				'id' : id
			},
			success: function(rs) {

				if( rs.trim() == 'success' ) {
					swal({
						title:'Success',
						text: 'ดำเนินการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					$('#row-temp-'+id).remove();

          recalTemp();
				}
				else{
					showError(rs);
				}
			},
      error:function(rs) {
        showError(rs);
      }
		});
	});
}


function reCal() {
	let total = 0;
  let wms = 0;

	$('.qty').each(function() {
		let qty = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    total += qty;
	});

  $('.wms').each(function() {
    let wms_qty = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    wms += wms_qty;
  })

	$('#move-total').text(addCommas(total));
  $('#wms-total').text(addCommas(wms));
  $('#move-table-total-qty').text(addCommas(total));
  $('#move-table-total-wms').text(addCommas(wms));
}


function recalTemp() {
  let total = 0;
  $('.temp-qty').each(function() {
    let qty = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    total += qty;
  })

  $('#temp-total').text(addCommas(total));
}


function recalBox() {
  let total = 0;
  $('.box-item').each(function() {
    let qty = parseDefault(parseFloat($(this).val()), 0);
    total += qty;
  })

  $('#box-total').text(addCommas(total));
}


function changeZone() {
  let table = $('#active-focus').val();

  if(table == 'F') {
    $('#from-zone-code').val('');
    $('#from-zone-name').text('กรุณาระบุโซน');
    $('#from-item-qty').addClass('hide');
    $('#from-item-bc').addClass('hide');
    $('#zone-list').html('');
    $('#from-zone-bc').removeClass('hide');
    $('#from-barcode-zone').val('').focus();
  }

  if(table == 'T') {
    $('#to-zone-code').val('');
    $('#to-zone-name').text('กรุณาระบุโซน');
    $('#to-item-qty').addClass('hide');
    $('#to-item-bc').addClass('hide');
    $('#to-zone-bc').removeClass('hide');
    $('#to-barcode-zone').val('').focus();
  }

  if(table == 'B') {
    if($('.box-item').length) {
      beep();

      swal({
        title:'Warning',
        text:'กรุณาบันทึกปิดกล่องก่อนเปลี่ยนโซน',
        type:'warning'
      }, function() {
        setFocus(table);
      });

      return false;
    }

    $('#box-zone-code').val('');
    $('#box-zone-name').text('กรุณาระบุโซน');
    $('#box-item-qty').addClass('hide');
    $('#box-item-bc').addClass('hide');
    $('#box-zone-bc').removeClass('hide');
    $('#box-barcode-zone').val('').focus();
  }
}


function removeBoxItem(barcode, pdCode) {
  swal({
    title:"คุณแน่ใจ ?",
    text:'ต้องการลบรายการ '+pdCode+' หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    $('#box-'+barcode).remove();
    reIndex('box-no');
    recalBox();
    setTimeout(() => {
      $('#box-barcode-item').val('').focus();
    }, 100);
  })
}
