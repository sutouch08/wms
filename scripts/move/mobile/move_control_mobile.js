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
		}, 2000)
	})

	$('.focus').focusin(function() {
		autoFocus = 0;
	});
}


function setFocus(table) {
  table = table === undefined || table === null ? $('#active-focus').val() : table;

  $('#active-focus').val(table);

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
}


function toggleHeader() {
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


$('#from-barcode-zone').keyup(function(e) {
  if(e.keyCode === 13) {
    console.log('ok');
    let bZone = $(this).val().trim();
    let whsCode = $('#warehouse-code').val();

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
  let code = $('#move-code').val().trim();

  if(bZone != "" && bZone !== undefined && bZone !== null) {
    load_in();

    $.ajax({
      url:HOME + '/get_from_zone',
      type:'GET',
      cache:false,
      data:{
        'move_code' : code,
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

            let source = $("#zoneTemplate").html();
            let output	= $("#zone-list");

            render(source, ds.data, output);
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
	let code	= $("#move-code").val();
  load_in();
	$.ajax({
		url: HOME + 'get_move_table/'+ code,
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
  let move_code = $('#move-code').val().trim();
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
  let cQty = parseDefault(parseFloat($('#from-qty-'+barcode).data('qty')), 0);

  if(qty > cQty) {
    beep();
    showError("จำนวนในโซนไม่เพียงพอ");
    $('#from-barcode-item').focus();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add_to_temp',
    type:'POST',
    cache:false,
    data:{
      "move_code" : move_code,
      "from_zone" : zone_code,
      "qty" : qty,
      "barcode" : barcode,
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        //--- ลดยอดสินค้าคงเหลือในโซนบนหน้าเว็บ (ในฐานข้อมูลถูกลดแล้ว)
        curQty = cQty - qty;

        //---	แสดงผลยอดสินค้าคงเหลือในโซน
        $("#from-qty-"+barcode).text(addCommas(curQty));
        $('#from-qty-'+barcode).data('qty', curQty);

        if(curQty <= 0) {
          $('#row-'+barcode).remove();
          reIndex();
        }

        //---	reset จำนวนเป็น 1
        $("#from-qty").val(1);

        //---	focus ที่ช่องยิงบาร์โค้ด รอการยิงต่อไป
        $("#from-barcode-item").focus();
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


function getTempTable(){
	let code = $("#move-code").val();
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
    let whsCode = $('#warehouse-code').val().trim();

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


function getToZone() {
  let code = $('#move-code').val().trim();
  let bZone = $('#to-barcode-zone').val().trim();
  let whsCode = $('#warehouse-code').val();

  if(bZone != "" && bZone !== undefined && bZone !== null) {
    load_in();

    $.ajax({
      url:HOME + '/get_to_zone',
      type:'GET',
      cache:false,
      data:{
        'move_code' : code,
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


function moveToZone() {
  let bc = $('#to-barcode-item');
  let move_code = $('#move-code').val();
  let zone_code = $('#to-barcode-zone').val().trim();
  let barcode = bc.val().trim();
  let qty = parseDefault(parseFloat($('#to-qty').val()), 1);
  let cQty = parseDefault(parseFloat($('#qty-temp-'+barcode).data('qty')), 0);
  let id = $('#qty-temp-'+barcode).data('id');

  bc.val('');

  if(cQty <= 0) {
    beep();
    showError('สินค้าไม่ถูกต้อง');
    return false;
  }

  if(qty > cQty) {
    beep();
    showError('จำนวนในโซนไม่เพียงพอ');
    return false;
  }

  $.ajax({
    url: HOME + 'move_to_zone',
    type:"POST",
    cache:"false",
    data:{
      "move_code" : move_code,
      "zone_code" : zone_code,
      "qty" : qty,
      "barcode" : barcode
    },
    success: function(rs) {
      if( rs.trim() == 'success') {
        curQty = cQty - qty;

        if(curQty == 0 ) {
          $('#row-temp-'+id).remove();
          reIndex('tmp');
        }
        else {
          $("#qty-temp-"+barcode).text(addCommas(curQty));
          $("#qty-temp-"+barcode).data('qty', curQty);
        }

        recalTemp();

        $("#to-qty").val(1);
        bc.focus();
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
  });

}


function deleteMoveItem(id, code) {
	let move_code = $('#move-code').val();

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
			url:HOME + 'delete_detail/'+ id,
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
             type:'success',
             timer:1000
           });

					$('#row-'+id).remove();
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

	$('.qty').each(function() {
		let qty = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    total += qty;
	});

	$('#move-total').text(addCommas(total));
  $('#move-total-table').text(addCommas(total));
}


function recalTemp() {
  let total = 0;
  $('.temp-qty').each(function() {
    let qty = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    total += qty;
  })

  $('#temp-total').text(addCommas(total));
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
}
