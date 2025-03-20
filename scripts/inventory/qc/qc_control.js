//--- ปิดออเดอร์ (ตรวจเสร็จแล้วจ้า) เปลี่ยนสถานะ
function closeOrder(){
  var order_code = $("#order_code").val();

  //--- รายการที่ต้องแก้ไข
  var must_edit = $('.must-edit').length;

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


function forceClose(){
  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการบังคับจบออเดอร์นี้หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#FA5858",
    confirmButtonText: 'บังคับจบ',
    cancelButtonText: 'ยกเลิก',
    closeOnConfirm: false
    }, function(){
      closeOrder();
  });
}


//--- บันทึกยอดตรวจนับที่ยังไม่ได้บันทึก
function saveQc(option){
  //--- Option 0 = just save, 1 = change box after saved, 2 = close order after Saved
  let order_code = $("#order_code").val();
  let id_box = $("#id_box").val();

  if(id_box == '' || order_code == ''){
    return false;
  }

  let ds = {
    'order_code' : order_code,
    'id_box' : id_box,
    'rows' : []
  };

  let rows = [];

  $(".hidden-qc").each(function(index, element){
    let qty = parseDefault(parseFloat($(this).val()), 0);

    if(qty > 0) {
      ds.rows.push({
        'product_code' : $(this).data('code'),
        'qty' : qty
      });
    }
  });

  // if(Object.keys(ds).length > 2) {
  if(ds.rows.length) {
    load_in();
    $.ajax({
      url: HOME + 'save_qc',
      type:"POST",
      cache:"false",
      data: {
        "data" : JSON.stringify(ds)
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if( rs == 'success'){

          //--- เอาสีน้ำเงินออกเพื่อให้รู้ว่าบันทึกแล้ว
          $(".blue").removeClass('blue');

          //---
          if(option == 0){
            swal({
              title:'Saved',
              type:'success',
              timer:1000
            });

            setTimeout(function(){ $("#barcode-item").focus();}, 2000);
          }

          //--- รีเซ็ตจำนวนที่ยังไม่ได้บันทึก
          $('.hidden-qc').each(function(index, element){
            $(this).val(0);
          });


          //--- ถ้ามาจากการเปลี่ยนกล่อง
          if( option == 1) {

            swal({
              title:'Saved',
              type:'success',
              timer:1000
            } );
          }

          //--- ถ้ามาจากการกดปุ่ม ตรวจเสร็จแล้ว หรือ ปุ่มบังคับจบ
          if( option == 2){
            closeOrder();
          }
        }
        else {
          //--- ถ้าผิดพลาด
          showError(rs);
        }

      }
    });
  }
}


//--- เมื่อยิงบาร์โค้ด
$("#barcode-item").keyup(function(e){
  if( e.keyCode == 13 && $(this).val() != "" ){
    qcProduct();
  }
});


function qcProduct() {
  let id_box = $('#id_box').val();

  if(id_box == "") {
    beep();
    swal("กรุณาระบุกล่อง");
    return false;
  }

  let input_barcode = $("#barcode-item").val();
  let iqty = parseDefault(parseInt($('#qc-qty').val()), 1);

  $('#barcode-item').val('');

  if(input_barcode.length) {
    let barcode = md5(input_barcode); //--- id กับ barcode คือตัวเดียวกัน
    let id = barcode;

    if($('#bc-'+barcode).length == 1) {
      let pdCode = $('#bc-'+barcode).data('code');
      let pqty = parseDefault(parseInt($("#bc-"+barcode).val()), 1);
      let qty = iqty * pqty;

      //--- จำนวนที่จัดมา
      let prepared = parseInt( removeCommas( $("#prepared-"+id).text() ) );

      //--- จำนวนที่ตรวจไปแล้วยังไม่บันทึก
      let notsave = parseInt( removeCommas( $("#"+id).val() ) ) + qty;

      //--- จำนวนที่ตรวจแล้วทั้งหมด (รวมที่ยังไม่บันทึก) ของสินค้านี้
      let qc_qty = parseInt( removeCommas( $("#qc-"+id).text() ) ) + qty;

      //--- จำนวนสินค้าที่ตรวจแล้วทั้งออเดอร์ (รวมที่ยังไม่บันทึกด้วย)
      let all_qty = parseInt( removeCommas( $("#all_qty").text() ) ) + qty;

      //--- ถ้าจำนวนที่ตรวจแล้ว
      if(qc_qty <= prepared) {

        $("#"+id).val(notsave);

        $("#qc-"+id).text(addCommas(qc_qty));

        //--- อัพเดตจำนวนในกล่อง
        updateBox(qc_qty);

        //--- อัพเดตยอดตรวจรวมทั้งออเดอร์
        $("#all_qty").text( addCommas(all_qty));

        //--- เปลียนสีแถวที่ถูกตรวจแล้ว
        $("#row-"+id).addClass('blue');


        //--- ย้ายรายการที่กำลังตรวจขึ้นมาบรรทัดบนสุด
        $("#incomplete-table").prepend($("#row-"+id));


        //--- ถ้ายอดตรวจครบตามยอดจัดมา
        if( qc_qty == prepared )
        {
          //--- ย้ายบรรทัดนี้ลงข้างล่าง(รายการที่ครบแล้ว)
          $("#complete-table").append($("#row-"+id));
          $("#row-"+id).removeClass('incomplete');
        }


        if($(".incomplete").length == 0 )
        {
          showCloseButton();
        }

        $('#qc-qty').val(1);
      }
      else
      {
        beep();
        swal("สินค้าเกิน!");
      }
    }
    else
    {
      beep();
      swal("สินค้าไม่ถูกต้อง");
    }
  }
}


function updateBox(){
  var id_box = $("#id_box").val();
  var qty = parseInt( removeCommas( $("#"+id_box).text() ) ) +1 ;
  $("#"+id_box).text(addCommas(qty));
}


function updateBoxList(box_id){
  let id_box = box_id != undefined ? box_id : $("#id_box").val();
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

        if(ds.status == 'success') {
          if(ds.box_list != 'no box') {
            var source = $("#box-template").html();
            var data = ds.box_list;
            var output = $("#box-row");
            render(source, data, output);

            $('#id_box').val(id_box);
            $('#barcode-item').focus();
          }
          else {
            $("#box-row").html('<div class="col-lg-12 col-md-12 col-sm-12 col-sm-12 padding-5"><span id="no-box-label">ยังไม่มีการตรวจสินค้า</span></div>');
          }
        }
        else {
          showError(rs);
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


//---
$("#barcode-box").keyup(function(e){
  if(e.keyCode == 13){
    if( $(this).val() != ""){
      getBox();
    }
  }
});


function confirmSaveBeforeAddBox() {
  let current_box_id = $('#id_box').val();
  var count = 0;
  $(".hidden-qc").each(function(index, element){
    if( $(this).val() > 0){
      count++;
    }
  });

  if( count > 0 ){
    swal({
  		title: "บันทึกรายการก่อน ?",
  		text: "คุณจำเป็นต้องบันทึกรายการก่อนที่จะเปลี่ยนกล่องใหม่",
  		type: "warning",
  		showCancelButton: true,
  		confirmButtonColor: "#5FB404",
  		confirmButtonText: 'บันทึก',
  		cancelButtonText: 'ยกเลิก',
  		closeOnConfirm: true
    },
    function(){
        saveQc(1);
  	});
  }
  else {
    addBox();
  }
}


function addBox() {
  let order_code = $('#order_code').val();

  $.ajax({
    url:HOME + 'add_new_box',
    type:'POST',
    cache:false,
    data:{
      'order_code' : order_code
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          $("#id_box").val(ds.box_id);
          $(".item").removeAttr('disabled');
          $("#barcode-item").focus();
          updateBoxList(ds.box_id);
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


function selectBox(box_id) {
  $('#id_box').val(box_id)
  $('#barcode-item').removeAttr('disabled').focus();
}


//--- ดึงไอดีกล่อง
function getBox(){
  var barcode = $("#barcode-box").val();
  var order_code = $("#order_code").val();
  if( barcode.length > 0){
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
            $(".item").removeAttr('disabled');
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


function confirmSaveBeforeChangeBox(box_id){
  let current_box_id = $('#id_box').val();
  var count = 0;
  $(".hidden-qc").each(function(index, element){
    if( $(this).val() > 0){
      count++;
    }
  });

  if( count > 0 ){
    swal({
  		title: "บันทึกรายการก่อน ?",
  		text: "คุณจำเป็นต้องบันทึกรายการก่อนที่จะเปลี่ยนกล่องใหม่",
  		type: "warning",
  		showCancelButton: true,
  		confirmButtonColor: "#5FB404",
  		confirmButtonText: 'บันทึก',
  		cancelButtonText: 'ยกเลิก',
  		closeOnConfirm: false
    }, function(isConfirm){
  			if(isConfirm) {
          saveQc(1);
          $('#box-'+box_id).prop('checked', false);
        }
        else {
          $('#box-'+current_box_id).prop('checked', true);
        }
  	});
  }
  else {
    selectBox(box_id);
  }
}

/*
function changeBox(){

  $("#id_box").val('');
  $("#barcode-item").val('');
  $(".item").attr('disabled', 'disabled');
  $("#barcode-box").removeAttr('disabled');
  $("#barcode-box").val('');
  $("#barcode-box").focus();
}
*/


function showCloseButton(){
  $("#force-bar").addClass('hide');
  $("#close-bar").removeClass('hide');
}


function showForceCloseBar(){
  $("#close-bar").addClass('hide');
  $("#force-bar").removeClass('hide');
}


function updateQty(id_qc){
  remove_qty = Math.ceil($('#input-'+id_qc).val());
  limit = parseInt($('#label-'+id_qc).text());
  limit = isNaN(limit) ? 0 : limit;

  if(remove_qty > limit){
    swal('ยอดที่เอาออกต้องไม่มากกว่ายอดตรวจนับ');
    return false;
  }

  if(limit >= remove_qty){
    load_in();
    $.ajax({
      url:HOME + 'remove_check_qty',
      //url:'controller/qcController.php?decreaseCheckedQty',
      type:'POST',
      cache:'false',
      data:{
        'id' : id_qc,
        'qty' : remove_qty
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          qty = limit - remove_qty;
          $('#label-'+id_qc).text(qty);
          $('#input-'+id_qc).val('');
        }
      }
    });
  }
}


function editBox(id_box, box_label) {
  let order_code = $('#order_code').val();
  load_in();

  $.ajax({
    url:HOME + 'get_checked_box_details',
    type:'GET',
    cache:false,
    data:{
      'order_code' : order_code,
      'id_box' : id_box
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#edit-box-template').html();
          let output = $('#edit-box-table');
          render(source, ds.data, output);

          $('#edit-box-title').text(box_label);
          $('#edit-box-modal').modal('show');
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
      load_out();
      showError(rs);
    }
  })
}


function checkEditQty(id_qc) {
  let input = $('#edit-input-'+id_qc);
  input.clearError();
  let preQty = parseDefault(parseFloat(input.val()), 0);
  let qty = parseDefault(parseInt(input.val()), 0);
  let limit = parseDefault(parseInt(input.data('qty')), 0);

  if(qty > limit || qty < 0 || preQty != qty) {
    input.hasError();
  }
}


function updateEditQty() {
  clearErrorByClass('e');
  let err = 0;
  let h = [];

  $('.edit-input-qty').each(function() {
    let input = $(this);
    let id = input.data('id');
    let preQty = parseDefault(parseFloat(input.val()), 0);
    let qty = parseDefault(parseInt(input.val()), 0);
    let limit = parseDefault(parseInt(input.data('qty')), 0);
    let product_code = input.data('item');

    if(qty > limit || qty < 0 || preQty != qty) {
      input.hasError();
      err++;
    }
    else {
      if(qty > 0) {
        h.push({'id' : id, 'remove_qty' : qty, 'product_code' : product_code});
      }
    }
  });

  if(err > 0) {
    return false;
  }

  if(h.length == 0) {
    swal("กรุณาระบุจำนวน");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update_check_qty',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.reload();
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
}


function removeBox(id_box, box_label) {
  let order_code = $('#order_code').val();

  swal({
    title:'Are you sure ?',
    text:'คุณแน่ใจว่าต้องการลบ '+box_label+' ? <br/>รายการตรวจนับสำหรับกล่องนี้จะถูกลบไปด้วย และไม่สามารถกู้คืนได้<br/>ต้องการดำเนินการหรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonText:'ดำเนินการ',
    confirmButtonColor:'red',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  }, function() {
    load_in();
    setTimeout(() => {
      $.ajax({
        url:HOME + 'remove_checked_box',
        type:'POST',
        cache:false,
        data:{
          "order_code" : order_code,
          "box_id" : id_box
        },
        success:function(rs) {
          load_out();
          if(isJson(rs)) {
            let ds = JSON.parse(rs);
            if(ds.status == 'success') {
              window.location.reload();
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
          load_out();
          showError(rs);
        }
      })
    }, 100);
  });
}

function showEditOption(order_code, product_code){
  $('#edit-title').text(product_code);
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
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#edit-template').html();
        var data = $.parseJSON(rs);
        var output = $('#edit-body');
        render(source, data, output);
        $('#edit-modal').modal('show');
      }else{
        swal('Error!',rs, 'error');
      }
    }
  });
}


$('.bc').click(function(){
  if(!$('#barcode-item').prop('disabled'))
  {
    var bc = $.trim($(this).text());
    $('#barcode-item').val(bc);
    $('#barcode-item').focus();
  }
});
