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

function setFocus() {
  if($('#item-bc').hasClass('hide')) {
    $('#barcode-box').focus();
  }
  else {
    $('#barcode-item').focus();
  }
}


function updateQc(id, qty) {
  qty = parseDefault(parseFloat(qty), 0);

  let badgeQty = parseDefault(parseFloat(removeCommas($('#badge-qty-'+id).text())), 0);
  let packQty = parseDefault(parseFloat(removeCommas($('#qc-'+id).text())), 0);
  let balance = badgeQty - qty;
  let packed = packQty + qty;

  if(qty > 0 && balance >= 0) {
    $('#badge-qty-'+id).text(addCommas(balance));
    $('#qc-'+id).text(addCommas(packed))

    if(balance == 0) {
      getCompleteItem(id);

      $('#incomplete-'+id).remove();
    }
  }
  else {
    showError("จำนวนไม่ถูกต้อง");
  }
}


function updateBox(id_box, qty) {
  let allQty = parseDefault(parseFloat(removeCommas($('#all-qty').text())), 0);
  let boxQty = parseDefault(parseFloat(removeCommas($('#box-qty-'+id_box).text())), 0);

  qty = parseDefault(parseFloat(qty), 1);
  allQty += qty;
  boxQty += qty;

  $('#all-qty').text(addCommas(allQty));
  $('#box-qty-'+id_box).text(addCommas(boxQty));
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
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.box_list !== null && ds.box_list !== undefined && ds.box_list != 'no box') {
          let data = ds.box_list;
          let source = $('#box-template').html();
          let output = $('#box-list');

          render(source, data, output);
        }
        else {
          let data = ds.box_list;
          let source = $('#no-box-template').html();
          let output = $('#box-list');

          render(source, data, output);
        }
      }
      else {
        showError(rs);
      }
    }
  });
}


$("#barcode-box").keyup(function(e){
  if(e.keyCode == 13){
    if( $(this).val() != ""){
      getBox();
    }
  }
});


function addBox() {
  let order_code = $('#order_code').val();
  let barcode = Math.random().toString(36).substring(2, 15);
  let allow_input_qty = $('#allow-input-qty').val();

  $.ajax({
    url:HOME + 'get_box',
    type:'GET',
    cache:false,
    data:{
      'order_code' : order_code,
      'barcode' : barcode
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
          $('#item-qty').val(1);

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
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


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
            $('#item-qty').val(1);

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
      },
      error:function(rs) {
        showError(rs);
      }
    });
  }
}

//-- change box to select box when press box icon
function getSelectBox(barcode) {
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
            $('#item-qty').val(1);

            if(allow_input_qty == 1) {
              $('#item-qty').removeClass('hide');
            }

            closeBoxList();

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
      qcProduct();
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


function qcProduct() {
  let order_code = $('#order_code').val();
  let id_box = $('#id_box').val();

  if(id_box == '') {
    beep();
    showError("กรุณาระบุกล่อง");
    return false;
  }

  if(order_code == '') {
    beep();
    showError('ไม่พบเลขที่ออเดอร์');
    return false;
  }

  let input_barcode = $("#barcode-item").val().trim();
  let iqty = parseDefault(parseInt($('#qc-qty').val()), 1);

  $('#barcode-item').attr('disabled', 'disabled');
  // $('#barcode-item').val('');

  if(input_barcode.length) {
    let barcode = md5(input_barcode); //--- id กับ barcode คือตัวเดียวกัน
    let id = barcode;

    if($('.'+barcode).length == 1) {
      let pdCode = $('.'+barcode).data('code');
      let pqty = parseDefault(parseInt($("."+barcode).val()), 1);
      let qty = iqty * pqty;

      if(qty <= 0) {
        beep();
        showError("จำนวนไม่ถูกต้อง");
        return false;
      }

      let d = {
        'order_code' : order_code,
        'id_box' : id_box,
        'product_code' : pdCode,
        'qty' : qty
      };

      load_in();

      $.ajax({
        url:HOME + 'do_qc',
        type:'POST',
        cache:false,
        data:{
          'data' : JSON.stringify(d)
        },
        success:function(rs) {
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status == 'success') {

              if(ds.result) {
                let result = ds.result;

                result.forEach((el) => {
                  updateBox(id_box, el.qty);
                  updateQc(el.detail_id, el.qty);
                });
              }
            }
            else {
              showError(ds.message);
            }

            $('#barcode-item').val('');
            $('#qc-qty').val(1);
          }
          else {
            showError(rs);
          }

          $('#barcode-item').removeAttr('disabled');
          $('#barcode-item').focus();
        },
        error:function(rs) {
          load_out();
          showError(rs);
          $('#barcode-item').removeAttr('disabled');
          $('#barcode-item').focus();
        }
      });
    }
    else
    {
      beep();
      swal("สินค้าไม่ถูกต้อง");
      $('#barcode-item').removeAttr('disabled');
    }
  }
}


function changeBox() {
  $("#id_box").val('');
  $('#item-bc').addClass('hide');
  $('#item-qty').addClass('hide');
  $('#item-qty').val(1);
  $('#barcode-box').val('');
  $("#barcode-box").removeAttr('disabled');
  $('#box-label').text("กรุณาระบุกล่อง");
  $('#box-bc').removeClass('hide');
  $("#barcode-box").focus();
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


function showEditOption(order_code, product_code) {
  load_in();

  $.ajax({
    url:HOME + 'get_checked_table',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code,
      'product_code' : product_code
    },
    success:function(rs){
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        let data = {
          'title' : product_code,
          'items' : ds
        };

        let source = $('#edit-template').html();
        let output = $('#edit-box');
        render(source, data, output);
        focus_init();

        openEditBox();

      }
      else {
        showError(rs);
      }
    }
  });
}


function updateQty(id_qc) {
  clearErrorByClass('e');

  let remove_qty = parseDefault(parseInt($('#input-'+id_qc).val()), 0);
  let limit = parseDefault(parseInt($('#input-'+id_qc).data('qty')), 0);

  if(remove_qty > 0) {

    if(remove_qty > limit) {
      $('#input-'+id_qc).hasError();
      showError("ยอดที่เอาออกต้องไม่เกินยอดตรวจนับ");
      return false;
    }

    if(limit >= remove_qty) {
      load_in();

      $.ajax({
        url:HOME + 'remove_check_qty',
        type:'POST',
        cache:'false',
        data:{
          'id' : id_qc,
          'qty' : remove_qty
        },
        success:function(rs){
          load_out();

          if(rs.trim() == 'success'){
            qty = limit - remove_qty;
            if(qty <= 0) {
              $('#edit-'+id_qc).remove();
            }
            else {
              $('#label-'+id_qc).text(qty);
              $('#input-'+id_qc).val('').focus();
            }
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          load_out();
          showError(rs);
        }
      });
    }
  }
}


function viewBoxItems(box_id) {
  let order_code = $('#order_code').val();

  $.ajax({
    url:HOME + 'get_box_details',
    type:'POST',
    cache:false,
    data:{
      'order_code' : order_code,
      'box_id' : box_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#box-item-template').html();
        let output = $('#box-details');

        render(source, ds, output);

        openBoxDetail();
      }
    }
  })
}

function closeHeader() {
  $('#header-pad').removeClass('move-in');
}

function openHeader() {
  closeBoxList();
  closeComplete();
  closeEditBox();
  closeBoxDetail();
  closeExtraMenu();
  $('#header-pad').addClass('move-in');
}

function closeExtraMenu() {
  $('#extra').val('hide');
  $('#extra-menu').removeClass('slide-in');
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
  $('#complete-box').removeClass('move-in');
}

function openComplete() {
  closeHeader();
  closeBoxDetail();
  closeBoxList();
  closeEditBox();
  $('#complete-box').addClass('move-in');
}

function toggleBoxList() {
  if($('#box-list').hasClass('move-in')) {
    closeBoxList();
  }
  else {
    openBoxList();
  }
}

function closeBoxList() {
  closeBoxDetail();
  $('#box-list').removeClass('move-in');
}


function openBoxList() {
  closeHeader();
  closeComplete();
  closeEditBox();
  closeBoxDetail();
  $('#box-list').addClass('move-in');
}

function closeEditBox() {
  $('#edit-box').removeClass('move-in');
}

function openEditBox() {
  $('#edit-box').addClass('move-in');
}


function closeBoxDetail() {
  $('#box-details').removeClass('move-in');
}

function openBoxDetail() {
  $('#box-details').addClass('move-in');
}


//--- ปิดออเดอร์ (ตรวจเสร็จแล้วจ้า) เปลี่ยนสถานะ
function closeOrder(){
  let order_code = $("#order_code").val();

  //--- รายการที่ต้องแก้ไข
  let must_edit = $('.must-edit').length;

  var notsave = 0;

  //-- ตรวจสอบว่ามีรายการที่ต้องแก้ไขให้ถูกต้องหรือเปล่า
  if(must_edit > 0){
    swal({
      title:'ข้อผิดพลาด',
      text:'พบรายการที่ต้องแก้ไข กรุณาแก้ไขให้ถูกต้อง',
      type:'error'
    });

    return false;
  }

  //--- ตรวจสอบก่อนว่ามีรายการที่ยังไม่บันทึกค้างอยู่หรือไม่
  $(".hidden-qc").each(function(index, element){
    if( $(this).val() > 0){
      notsave++;
    }
  });

  //--- ถ้ายังมีรายการที่ยังไม่บันทึก ให้บันทึกก่อน
  if(notsave > 0){
    saveQc(2);
  }else{
    //--- close order
    $.ajax({
      url: HOME +'close_order',
      type:'POST',
      cache:'false',
      data:{
        "order_code": order_code
      },
      success:function(rs){
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({title:'Success', type:'success', timer:1000});
          $('#btn-close').attr('disabled', 'disabled');
          $(".zone").attr('disabled', 'disabled');
          $(".item").attr('disabled', 'disabled');
          $(".close").attr('disabled', 'disabled');
          $('#btn-print-address').removeClass('hide');
        }else{
          swal("Error!", rs, "error");
        }
      }
    });
  }

}
