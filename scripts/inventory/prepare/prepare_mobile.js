var autoFocus = 1;

window.addEventListener('load', () => {
  focus_init();
  bclick_init();
  $('#barcode-zone').focus();
});


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


$('#barcode-zone').keyup(function(e) {
  if(e.keyCode === 13) {
    let bZone = $(this).val().trim();

    if(bZone.length > 0) {
      let whsCode = $('#warehouse_code').val();
      getZone(bZone, whsCode);
    }
  }
});

$('#barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val() != "") {
      doPrepare();
    }
  }
});

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

//--- จัดสินค้า ตัดยอดออกจากโซน เพิ่มเข้า buffer
function doPrepare() {
  let order_code = $("#order_code").val();
  let zone_code = $("#zone_code").val();
  let barcode = $("#barcode-item").val();
  let qty   = parseDefault(parseFloat($("#qty").val()), 0);

  if( zone_code == "") {
    beep();
    swal("Error!", "ไม่พบรหัสโซน กรุณาเปลี่ยนโซนแล้วลองใหม่อีกครั้ง", "error");
    return false;
  }

  if( barcode.length == 0){
    beep();
    swal("Error!", "บาร์โค้ดสินค้าไม่ถูกต้อง", "error");
    return false;
  }

  if(qty <= 0){
    beep();
    swal("Error!", "จำนวนไม่ถูกต้อง", "error");
    return false;
  }

  $.ajax({
    url: BASE_URL + 'inventory/prepare/do_prepare',
    type:"POST",
    cache:"false",
    data:{
      "order_code" : order_code,
      "zone_code" : zone_code,
      "barcode" : barcode,
      "qty" : qty
    },
    success: function(rs) {
      if( isJson(rs)){
        let ds = JSON.parse(rs);
        let order_qty = parseDefault(parseInt(removeCommas($("#order-qty-" + ds.id).text())), 0);
        let prepared = parseDefault(parseInt(removeCommas($("#prepared-qty-" + ds.id).text())), 0);
        let balance = parseDefault(parseInt(removeCommas($("#balance-qty-" + ds.id).text())), 0);
        let prepare_qty = parseInt(ds.qty);
        let picked = parseDefault(parseInt(removeCommas($('#pick-qty').text())), 0);

        prepared = prepared + prepare_qty;
        balance = order_qty - prepared;

        $("#prepared-qty-" + ds.id).text(addCommas(prepared));
        $("#balance-qty-" + ds.id).text(addCommas(balance));
        $('#badge-qty-'+ ds.id).text(addCommas(balance));

        $('#pick-qty').text(addCommas(picked + qty));

        $("#qty").val(1);
        $("#barcode-item").val('');

        if( ds.valid == '1') {
          getCompleteItem(ds.id);
        }
        else {
          $('.incomplete-item').removeClass('heighlight');
          $('#incomplete-'+ds.id).addClass('heighlight');
          $('#incomplete-'+ds.id).prependTo('#incomplete-box');
          $('#btn-scroll-up').click();
        }
      }
      else {
        beep();
        swal("Error!", rs, "error");
        $("#qty").val(1);
        $("#barcode-item").val('');
      }
    }
  });
}


function finishPrepare() {
  var order_code = $("#order_code").val();
  $.ajax({
    url: BASE_URL + 'inventory/prepare/finish_prepare',
    type:"POST",
    cache:"false",
    data: {
      "order_code" : order_code
    },
    success: function(rs) {
      var rs = $.trim(rs);

      if(rs == 'success') {
        swal({
          title: "Success",
          type:"success",
          timer: 1000
        });

        setTimeout(function() {
          goBack();
        }, 1200);
      }
      else{
        beep();
        showError(rs);
      }
    }
  });
}


function getCompleteItem(id) {
  $.ajax({
    url:HOME + '/get_complete_item/' + id,
    type:'GET',
    cache:false,
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#complete-template').html();
          let output = $('#complete-box');

          render_append(source, ds.data, output);

          $("#incomplete-" + ds.data.id).remove();

          if( $(".incomplete-item").length == 0){
            $('#close-bar').removeClass('hide');
            $('#finished').val(1);
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
}


function getIncompleteItem(id) {
  let whsCode = $('#warehouse_code').val();

  $.ajax({
    url:HOME + '/get_incomplete_item',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'warehouse_code' : whsCode
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#incomplete-template').html();
          let output = $('#incomplete-box');

          render_append(source, ds.data, output);

          $("#complete-" + ds.data.id).remove();

          $('#finished').val(0);
          $('#close-bar').addClass('hide');
          bclick_init();

          let picked = parseDefault(parseInt(removeCommas($('#pick-qty').text())), 0);
          let pQty = parseDefault(parseInt(ds.data.qty), 0);

          picked = picked - pQty;

          $('#pick-qty').text(addCommas(picked));

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


function getZone(bZone, whsCode) {
  if(bZone != "" && bZone !== undefined && bZone !== null) {
    $.ajax({
      url:HOME + '/get_zone_code',
      type:'GET',
      cache:false,
      data:{
        'zone_code' : bZone,
        'warehouse_code' : whsCode
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            $('#zone_code').val(ds.zone.code);
            $('#zone-name').text(ds.zone.name);
            $('#zone-bc').addClass('hide');
            $('#item-qty').removeClass('hide');
            $('#item-bc').removeClass('hide');
            $('#qty').val(1);
            $('#barcode-item').val('').focus();
            $('#force-row').addClass('item-bc');
          }
          else {
            beep();
            $('#barcode-zone').val('');

            swal({
              title:'Error!',
              text:ds.message,
              type:'error',
              html:true
            });
          }
        }
        else {
          beep();
          $('#barcode-zone').val('');
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        $('#barcode-zone').val('');
        showError(rs);
      }
    })
  }
}


function showItem() {
  $('#zone-bc').addClass('hide');
  $('#item-qty').removeClass('hide');
  $('#item-bc').removeClass('hide');
  $('#qty').val(1);
  $('#barcode-item').val('').focus();
  $('#force-row').addClass('item-bc');
}


function confirmClose(){
  closeExtraMenu();

  setTimeout(() => {
    swal({
      title: "Force Close",
      text: "สินค้าไม่ครบ <br/>ต้องการบังคับจบออเดอร์นี้หรือไม่ ?",
      type: "warning",
      showCancelButton:true,
      confirmButtonColor:"#FA5858",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      html:true,
      closeOnConfirm:true
    }, function(){
      finishPrepare();
    });
  }, 250);
}


function changeZone(){
  closeExtraMenu();

  $('#force-row').removeClass('item-bc');
  $("#zone_code").val('');
  $('#zone-name').text('กรุณาระบุโซน');
  $("#barcode-item").val('');
  $("#qty").val(1);
  $("#barcode-zone").val('');
  $('.e-item').addClass('hide');
  $('.e-zone').removeClass('hide');
  $("#barcode-zone").focus();
}


function toggleHeader() {
  let hd = $('#header');
  let pad = $('#header-pad');

  if(hd.val() == "hide") {
    closeExtraMenu();
    hd.val("show");
    pad.addClass('move-in');
  }
  else {
    hd.val("hide");
    pad.removeClass('move-in');
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


function toggleComplete() {
  closeExtraMenu();

  let hd = $('#complete');
  let pad = $('#complete-box');

  if(hd.val() == "hide") {
    hd.val("show");
    pad.addClass('move-in');
  }
  else {
    hd.val("hide");
    pad.removeClass('move-in');
  }
}


function closeCompleteBox() {
  $('#complete').val('hide');
  $('#complete-box').removeClass('move-in');
}


function removeBuffer(orderCode, pdCode, order_detail_id) {
  swal({
    title:'คุณแน่ใจ ?',
    text:'ต้องการลบ '+pdCode+' ออกจาก Buffer หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    setTimeout(()=>{
      $.ajax({
        url: HOME + '/remove_buffer',
        type: 'POST',
        cache: false,
        data:{
          'order_code' : orderCode,
          'product_code' : pdCode,
          'order_detail_id' : order_detail_id
        },
        success:function(rs) {
          if(rs === 'success') {
            getIncompleteItem(order_detail_id);
          }
          else {
            showError(rs)
          }
        },
        error:function(rs) {
          showError(rs);
        }
      })
    },100);
  })
}


function showKeyboard(input) {
  if(input == 'zone') {
    $('#barcode-zone').attr('inputmode', 'text');
    $('#zone-qr').addClass('hide');
    $('#zone-keyboard').removeClass('hide');
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
  }

  if(input == 'item') {
    $('#barcode-item').attr('inputmode', 'none');
    $('#item-keyboard').addClass('hide');
    $('#item-qr').removeClass('hide');
  }

  setCookie('showKeyboard', 0, 60);
}


function reloadStockInZone(id, pdCode, whsCode) {
  load_in();

  $.ajax({
    url:HOME + '/reload_stock_in_zone',
    type:'GET',
    cache:false,
    data:{
      'product_code' : pdCode,
      'warehouse_code' : whsCode
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#stock-'+id).html('Location : '+ds.result);
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


var intv = setInterval(function() {
  var order_code = $('#order_code').val();
  $.ajax({
    url: BASE_URL + 'inventory/prepare/check_state',
    type:'GET',
    cache:'false',
    data:{
      'order_code':order_code
    },
    success:function(rs){
      if(rs.trim() != 4){
        window.location.reload();
      }
    }
  })
}, 10000);
