window.addEventListener('load', () => {
  zone_init();
});

//--- จัดสินค้า ตัดยอดออกจากโซน เพิ่มเข้า buffer
function doPrepare(){
  var order_code = $("#order_code").val();
  var zone_code = $("#zone_code").val();
  var barcode = $("#barcode-item").val();
  var qty   = $("#qty").val();

  if( zone_code == ""){
    beep();
    swal("Error!", "ไม่พบรหัสโซน กรุณาเปลี่ยนโซนแล้วลองใหม่อีกครั้ง", "error");
    return false;
  }

  if( barcode.length == 0){
    beep();
    swal("Error!", "บาร์โค้ดสินค้าไม่ถูกต้อง", "error");
    return false;
  }

  if( isNaN(parseInt(qty))){
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
    success: function(rs){
      var rs = $.trim(rs);
      if( isJson(rs)){
        var rs = $.parseJSON(rs);
        var order_qty = parseInt( removeCommas( $("#order-qty-" + rs.id).text() ) );
        var prepared = parseInt( removeCommas( $("#prepared-qty-" + rs.id).text() ) );
        var balance = parseInt( removeCommas( $("#balance-qty-" + rs.id).text() ) );
        var prepare_qty = parseInt(rs.qty);

        prepared = prepared + prepare_qty;
        balance = order_qty - prepared;

        $("#prepared-qty-" + rs.id).text(addCommas(prepared));
        $("#balance-qty-" + rs.id).text(addCommas(balance));

        $("#qty").val(1);
        $("#barcode-item").val('');


        if( rs.valid == '1'){
          $("#complete-table").append($("#incomplete-" + rs.id));
          $("#incomplete-" + rs.id).removeClass('incomplete');
        }

        if( $(".incomplete").length == 0){
          $("#force-bar").addClass('hide');
          $("#close-bar").removeClass('hide');
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


//---- จัดเสร็จแล้ว
function finishPrepare(){
  var order_code = $("#order_code").val();
  $.ajax({
    url: BASE_URL + 'inventory/prepare/finish_prepare',
    type:"POST",
    cache:"false",
    data: {
      "order_code" : order_code
    },
    success: function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({title: "Success", type:"success", timer: 1000});
        setTimeout(function(){ goBack();}, 1200);
      }else{
        beep();
        swal("Error!", rs, "error");
      }
    }
  });
}


function forceClose(){
  swal({
    title: "Are you sure ?",
    text: "ต้องการบังคับจบออเดอร์นี้หรือไม่ ?",
    type: "warning",
    showCancelButton:true,
    confirmButtonColor:"#FA5858",
    confirmButtonText: "ใช่ ฉันต้องการ",
    cancelButtonText: "ยกเลิก",
    closeOnConfirm:false
  }, function(){
    finishPrepare();
  });
}

function zone_init() {
  let warehouse_code = $('#warehouse_code').val();

  $('#barcode-zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/' + warehouse_code,
    autoFocus:true,
    close:function() {
      let zone = $(this).val().trim().split(' | ');
      if(zone.length == 2) {
        $(this).val(zone[0]);
        $('#zone_code').val(zone[0]);
        $('#zone-name').val(zone[1]);
      }
      else {
        $(this).val('');
        $('#zone_code').val('');
        $('#zone-name').val('');
      }
    }
  })
}


//---- เมื่อมีการยิงบาร์โค้ดโซน เพื่อระบุว่าจะจัดสินค้าออกจากโซนนี้
$("#barcode-zone").keyup(function(e) {
  if(e.keyCode == 13) {
    setTimeout(() => {
      let barcode = $(this).val().trim();
      let whsCode = $('#warehouse_code').val();
      let whsName = $('#whs-name').val();

      if(barcode.length) {
        load_in();
        $.ajax({
          url: BASE_URL + 'masters/zone/get_zone',
          type:"GET",
          cache:"false",
          data:{
            "code" : barcode,
            "warehouse_code" : whsCode,
            "warehouse_name" : whsName
          },
          success: function(rs) {
            load_out();

            if(isJson(rs)) {
              let ds = JSON.parse(rs);

              if(ds.status == 'success') {
                $('#zone_code').val(ds.code);
                $('#barcode-zone').val(ds.code).attr('disabled', 'disabled');
                $('#zone-name').val(ds.name);
                $('#qty').val(1).removeAttr('disabled');
                $('#barcode-item').removeAttr('disabled');
                $('#btn-submit').removeAttr('disabled');
                $('#barcode-item').focus().select();
              }
              else {
                beep();
                showError(ds.message);
                $('#zone_code').val('');
              }
            }
            else {
              beep();
              showError(rs);
            }
          },
          error:function(rs) {
            load_out();
            beep();
            showError(rs);
          }
        });
      }
    }, 100);
  }
});


$('.b-click').click(function(){
  if(!$('#barcode-item').prop('disabled'))
  {
    var barcode = $.trim($(this).text());
    $('#barcode-item').val(barcode);
    $('#barcode-item').focus();
  }
});


function changeZone() {
  $("#zone_code").val('');
  $("#barcode-item").val('');
  $("#barcode-item").attr('disabled','disabled');
  $("#qty").val(1);
  $("#qty").attr('disabled', 'disabled');
  $("#btn-submit").attr('disabled', 'disabled');
  $("#barcode-zone").val('');
  $('#zone-name').val('');
  $("#barcode-zone").removeAttr('disabled');
  $("#barcode-zone").focus();
}


//---- ถ้าใส่จำนวนไม่ถูกต้อง
$("#qty").keyup(function(e){
  if( e.keyCode == 13){
    if(! isNaN($(this).val())){
      $("#barcode-item").focus();
    }else{
      swal("จำนวนไม่ถูกต้อง");
      $(this).val(1);
    }
  }
});


//--- เมื่อยิงบาร์โค้ดสินค้าหรือกดปุ่ม Enter
$("#barcode-item").keyup(function(e){
  if(e.keyCode == 13){
    if( $(this).val() != ""){
      doPrepare();
    }
  }
});


//--- กด Q เพื่อ focus ที่ Qty
$('#barcode-item').keydown(function(e) {
  if(e.keyCode == 81) {
    e.preventDefault();
    $('#qty').focus().select();
  }
})


//--- เปิด/ปิด การแสดงที่เก็บ
function toggleForceClose(){
  if( $("#force-close").prop('checked') == true){
    $("#btn-force-close").removeClass('not-show');
  }else{
    $("#btn-force-close").addClass('not-show');
  }
}


//---- กำหนดค่าการแสดงผลที่เก็บสินค้า เมื่อมีการคลิกปุ่มที่เก็บ
$(function () {
  $('.btn-pop').popover({html:true});
});


$("#showZone").change(function(){
  if( $(this).prop('checked')){
    $(".btn-pop").addClass('hide');
    $(".zoneLabel").removeClass('hide');
    setZoneLabel(1);
  }else{
    $(".zoneLabel").addClass('hide');
    $(".btn-pop").removeClass('hide');
    setZoneLabel(0);
  }
});


function setZoneLabel(showZone){
  //---- 1 = show , 0 == not show;
  $.get(BASE_URL + 'inventory/prepare/set_zone_label/'+showZone);
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
      var rs = $.trim(rs);
      if(rs != 4){
        window.location.reload();
      }
    }
  })
}, 10000);


function removeBuffer(orderCode, pdCode, order_detail_id) {
  $.ajax({
    url:BASE_URL + 'inventory/prepare/remove_buffer/',
    type:'POST',
    cache:false,
    data:{
      'order_code' : orderCode,
      'product_code' : pdCode,
      'order_detail_id' : order_detail_id
    },
    success:function(rs){
      if(rs === 'success'){
        window.location.reload();
      }else{
        swal(rs);
      }
    }
  })
}
