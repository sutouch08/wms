var autoFocus = 1;

window.addEventListener('load', () => {
  focus_init();
  bclick_init();
  $('#barcode-box').focus();
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


function updateBox(){
  var id_box = $("#id_box").val();
  var qty = parseInt( removeCommas( $("#"+id_box).text() ) ) +1 ;
  $("#"+id_box).text(addCommas(qty));
}



function updateBoxList(){
  let id_box = $("#id_box").val();
  let order_code = $("#order_code").val();

  $.ajax({
    url: HOME + 'get_box_list',
    type:"GET",
    cache: "false",
    data:{
      "order_code" : order_code,
      "id_box" : id_box
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $("#box-template").html();
        var data = $.parseJSON(rs);
        var output = $("#box-row");
        render(source, data, output);
      }
      else if(rs == "no box") {
        $("#box-row").html('<span id="no-box-label">ยังไม่มีการตรวจสินค้า</span>');
      }
      else {
        swal("Error!", rs, "error");
      }
    }
  });
}



//---
$("#barcode-box").keyup(function(e){
  if(e.keyCode == 13){
    if( $(this).val() != ""){
      getBox();
    }
  }
});

function getBox() {
  let barcode = $('#barcode-box').val().trim();
  let order_code = $('#order_code').val();
  let allow_input_qty = $('#allow-input-qty').val();

  if(barcode.length > 0) {
    $.ajax({
      url: HOME + 'get_box',
      type:"GET",
      cache:"false",
      data:{
        "barcode":barcode,
        "order_code" : order_code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            $("#id_box").val(ds.box_id);
            $("#barcode-box").attr('disabled', 'disabled');
            $('#box-label').text("กล่องที่ "+ds.box_no);
            $('#box-bc').addClass('hide');
            $('#item-bc').removeClass('hide');

            if(allow_input_qty == 1) {
              $('#item-qty').removeClass('hide');
            }

            $("#barcode-item").focus();

            updateBoxList();
          }
          else {
            showError(ds.message);
          }
        }
        else {
          showError(rs);
        }

        if( ! isNaN( parseInt(rs) ) ) {

        }else{
          swal("Error!", rs, "error");
        }
      },
      error:function(rs) {
        showError(rs);
      }
    });
  }
}


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
    $('#barcode-box').focus();
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



function closeHeader() {
  $('#header').val("hide");
  $('#header-pad').removeClass('move-in');
}

function toggleHeader() {
  let hd = $('#header');
  let pad = $('#header-pad');

  if(hd.val() == "hide") {
    hd.val("show");
    pad.addClass('move-in');
  }
  else {
    hd.val("hide");
    pad.removeClass('move-in');
  }

  closeBoxList();
  closeComplete();
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

function closeComplete() {
  $('#complete').val("hide");
  $('#complete-box').removeClass('move-in');
}

function toggleComplete() {
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

  closeHeader();
  closeBoxList();
}

function closeBoxList() {
  $('#box-pad').val("hide");
  $('#box-list').removeClass('move-in');
}

function toggleBoxList() {
  let hd = $('#box-pad');
  let pad = $('#box-list');

  if(hd.val() == "hide") {
    hd.val("show");
    pad.addClass('move-in');
  }
  else {
    hd.val("hide");
    pad.removeClass('move-in');
  }

  closeHeader();
  closeComplete();
}
