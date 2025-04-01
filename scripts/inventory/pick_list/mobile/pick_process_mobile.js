var warehouse_code = $('#warehouse').val();
var autoFocus = 1;

window.addEventListener('load', () => {
  focus_init();
  bclick_init();
  $('#barcode-zone').focus();

  let showKeyboard = getCookie('showKeyboard');

  if(showKeyboard == 1) {
    zone_init();
  }
});


function zone_init() {
  $('#barcode-zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/' + warehouse_code,
    disabled:false,
    autoFocus:true,
    close:function() {
      let zone = $(this).val().trim().split(' | ');

      if(zone.length == 2) {
        $(this).val(zone[0]);
        $('#zone-name').val(zone[1]);

        setTimeout(() => {
          get_zone_code();
        }, 200);
      }
      else {
        $(this).val('');
        $('#zone-name').val('');
      }
    }
  })
}


function disable_zone_autocomplete() {
  $('#barcode-zone').autocomplete({
    disabled:true
  });
}


$('#barcode-zone').keyup(function(e) {
  if(e.keyCode === 13) {
    let code = $(this).val().trim();

    if(code.length) {
      setTimeout(() => {
        get_zone_code();
      }, 100);
    }
  }
})


$('#barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    doPicking();
  }
})


function bclick_init() {
  $('.b-click').click(function(){
    let barcode = $(this).text().trim();
    $('#barcode-item').val(barcode);
    $('#barcode-item').focus();
  });
}


function focus_init() {
	$('.focus').focusout(function() {
		autoFocus = 1
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


$('#btn-increse').click(function() {
  let qty = parseDefault(parseFloat($('#qty').val()), 0);
  qty++;
  $('#qty').val(qty);
  $('#barcode-item').focus();
})


$('#btn-decrese').click(function() {
  let qty = parseDefault(parseFloat($('#qty').val()), 0);

  if(qty > 0) {
    qty--;
  }
  else {
    qty = 0;
  }

  $('#qty').val(qty);
  $('#barcode-item').focus();
})


function setFocus() {
  if($('#item-bc').hasClass('hide')) {
    $('#barcode-zone').focus();
  }
  else {
    $('#barcode-item').focus();
  }
}


function get_zone_code() {
  let warehouse_code = $('#warehouse').val();
  let zone_code = $('#barcode-zone').val();

  if(zone_code.length) {
    load_in();
    $.ajax({
      url:HOME + 'get_zone_code',
      type:'GET',
      cache:false,
      data:{
        'zone_code' : zone_code,
        'warehouse_code' : warehouse_code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            $('#barcode-zone').val(ds.zone.code)
            $('#zone-name').text(ds.zone.name);
            $('#zone-bc').addClass('hide');
            $('#item-qty').removeClass('hide');
            $('#item-bc').removeClass('hide');
            $('#qty').val(1);
            $('#barcode-item').val('').focus();
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
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }
}


function changeZone() {
  $('#barcode-zone').val('')
  $('#zone-name').val('')
  $('#qty').val(1);
  $('#barcode-item').val('');
  $('#item-qty').addClass('hide');
  $('#item-bc').addClass('hide');
  $('#zone-bc').removeClass('hide');
  $('#barcode-zone').focus();
}


function toggleHeader() {
  closeExtraMenu();
  closeCompleteBox();
  closeTransBox();

  if($('#header-pad').hasClass('move-in')) {
    $('#header-pad').removeClass('move-in');
  }
  else {
    $('#header-pad').addClass('move-in');
  }
}


function closeHeader() {
  $('#header-pad').removeClass('move-in');
}


function toggleCompleteBox() {
  closeExtraMenu();
  closeHeader();
  closeTransBox();

  if($('#complete-box').hasClass('move-in')) {
    $('#complete-box').removeClass('move-in');
    setFocus();
  }
  else {
    $('#complete-box').addClass('move-in');
    autoFocus = 0;
  }
}


function closeCompleteBox() {
  $('#complete-box').removeClass('move-in');
}


function toggleTransBox() {
  closeExtraMenu();
  closeHeader();
  closeCompleteBox();

  if($('#trans-box').hasClass('move-in')) {

    $('#trans-box').removeClass('move-in');
  }
  else {
    $('#trans-box').addClass('move-in');
    autoFocus = 0;

    setTimeout(() => {
      reloadTransection();
    }, 100)
  }
}


function closeTransBox() {
  $('#trans-box').removeClass('move-in');
}


function toggleExtraMenu() {
  if($('#extra-menu').hasClass('slide-in')) {
    $('#extra-menu').removeClass('slide-in');
  }
  else {
    $('#extra-menu').addClass('slide-in');
  }
}


function closeExtraMenu() {
  $('#extra-menu').removeClass('slide-in');
}


function showKeyboard(input) {
  if(input == 'zone') {
    $('#barcode-zone').attr('inputmode', 'text');
    $('#zone-qr').addClass('hide');
    $('#zone-keyboard').removeClass('hide');
    zone_init();
  }

  if(input == 'item') {
    $('#barcode-item').attr('inputmode', 'text');
    $('#item-qr').addClass('hide');
    $('#item-keyboard').removeClass('hide');
  }

  setCookie('showKeyboard', 1, 60);
}


function hideKeyboard(input) {
  if(input == 'zone') {
    $('#barcode-zone').attr('inputmode', 'none');
    $('#zone-keyboard').addClass('hide');
    $('#zone-qr').removeClass('hide');
    disable_zone_autocomplete();
  }

  if(input == 'item') {
    $('#barcode-item').attr('inputmode', 'none');
    $('#item-keyboard').addClass('hide');
    $('#item-qr').removeClass('hide');
  }

  setCookie('showKeyboard', 0, 60);
}

function doPicking() {
  let warehouse_code = $('#warehouse').val();
  let zone_code = $('#barcode-zone').val();
  let qty = parseDefault(parseInt($('#qty').val()), 1);
  let barcode = $('#barcode-item').val().trim();
  let el = $('#'+barcode);

  if(warehouse_code.length == 0) {
    beep();
    swal("ไม่พบคลังสินค้า");
    return false;
  }

  if(zone_code.length == 0) {
    beep();
    swal("กรุณาระบุโซน");
    return false;
  }

  if(el === undefined) {
    beep();
    swal("ไม่พบรายการสินค้า");
    return false;
  }

  let id = el.data('id');
  let product_code = el.data('code');
  let product_name = el.data('name');
  let releaseQty = parseDefault(parseInt(removeCommas($('#release-qty-'+id).text())), 0);
  let pickQty = parseDefault(parseInt(removeCommas($('#pick-qty-'+id).text())), 0);
  let sumQty = pickQty + qty;

  // console.log('id:'+id+', Sum:'+sumQty+', qty:'+qty+', pick:'+pickQty+', release:'+releaseQty);

  if(sumQty > releaseQty) {
    beep();

    swal({
      title:"จำนวนเกินที่กำหนด",
      type:'warning'
    }, function() {
      setTimeout(() => {
        $('#qty').val(1);
        $('#barcode-item').val('').focus();
      }, 200);
    });

    return false;
  }

  let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val(),
    'zone_code' : zone_code,
    'qty' : qty,
    'row_id' : id
  }

  $.ajax({
    url:HOME + 'do_picking',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let pickQtty = ds.pick_qty;
          let balance = releaseQty - pickQtty;

          $('.incomplete-item').removeClass('highlight');

          $('#pick-qty-'+id).text(pickQtty);
          $('#balance-qty-'+id).text(balance);
          $('#badge-qty-'+id).text(balance);

          if(balance == 0) {

            $('#incomplete-'+id).remove();

            let data = {
              'id' : id,
              'product_code' : product_code,
              'product_name' : product_name,
              'releaseQty' : releaseQty,
              'pickQtty' : pickQtty
            };

            let source = $('#complete-row-template').html();
            let output = $('#complete-box');
            render_append(source, data, output);
          }
          else {
            $('#incomplete-'+id).addClass('highlight').prependTo($('#incomplete-box'));
          }

          $('#qty').val(1);
          $('#barcode-item').val('').focus();

          recalTotalPicked();
        }
        else {
          beep();
          showError(ds.message);
          $('#qty').val(1);
          $('#barcode-item').val('').focus();
        }
      }
      else {
        beep();
        showError();
        $('#qty').val(1);
        $('#barcode-item').val('').focus();
      }
    },
    error:function(rs) {
      beep()
      showError(rs)
      $('#qty').val(1);
      $('#barcode-item').val('').focus();
    }
  })
}


function reloadPickRow(id) {
  let whsCode = $('#warehouse').val();
  load_in();
  $.ajax({
    url:HOME + 'get_pick_row',
    type:'POST',
    cache:false,
    data:{
      'row_id' : id,
      'warehouse_code' : whsCode
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#pick-qty-'+id).text(ds.data.pick_qty);
          $('#balance-qty-'+id).text(ds.data.balance_qty);
          $('#badge-qty-'+id).text(ds.data.balance_qty);
          $('#stock-in-zone-'+id).html(ds.data.stock_in_zone);

          recalTotalPicked();
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
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function recalTotalPicked() {
  let picked = 0;

  $('.picked-qty').each(function() {
    let qty = parseDefault(parseInt(removeCommas($(this).text())), 0);

    picked += qty;
  });

  $('#total-picked').text(addCommas(picked));
}


function reloadInComplete() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_incomplete_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#incomplete-template').html();
          let output = $('#incomplete-box');

          render(source, ds.data, output);

          recalTotalPicked();
          bclick_init();
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
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function reloadComplete() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_complete_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#complete-template').html();
          let output = $('#complete-box');

          render(source, ds.data, output);

          recalTotalPicked();
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
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function reloadTransection() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_transection_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#trans-template').html();
          let output = $('#trans-box');

          render(source, ds.data, output);

          recalTotalPicked();
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
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function removeTransection(id, qty, product_code, zone_code) {
  swal({
    title:'Delete Transection',
    text:'คุณแน่ใจว่าต้องการลบ Transection <br/>'+product_code+' : '+ zone_code + '<br/>สต็อกจะถูกดีดกลับเข้าโซน '+zone_code+' จำนวน '+qty+' pcs.<br/>ต้องการดำเนินการต่อหรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    confirmButtonColor:'red',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'delete_transection',
        type:'GET',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            $('#trans-'+id).remove();

            reloadInComplete();
            reloadComplete();
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


function confirmClose(){
  closeExtraMenu();

  setTimeout(() => {
    swal({
      title: "Force Close",
      text: "สินค้าไม่ครบ <br/>ต้องการบังคับจบเอกสารนี้หรือไม่ ?",
      type: "warning",
      showCancelButton:true,
      confirmButtonColor:"#FA5858",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      html:true,
      closeOnConfirm:true
    }, function(){
      finishPick();
    });
  }, 100);
}


function finishPick() {
  var code = $("#code").val();

  load_in();

  $.ajax({
    url: HOME + 'finish_pick',
    type:"POST",
    cache:"false",
    data: {
      "code" : code
    },
    success: function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          if(ds.ex == 1) {
            swal({
              title:'Oops!',
              text:"บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ <br/>คุณจำเป็นต้องกดส่งข้อมูลไป SAP ด้วยตัวเองอีกครั้ง",
              type:'success',
              html:true
            }, function() {
              viewDetail(code);
            });
          }
          else {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              goBack();
            }, 1200)
          }
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
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  });
}
