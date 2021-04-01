$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    var qty = $('#qty').val();
    doReceive();
  }
});


$('#invoice-box').keyup(function(e){
  if(e.keyCode === 13){
    add_invoice();
  }
})


//---- ยิงบาร์โค้ดเพื่อรับสินค้า
//---- 1. เช็คก่อนว่ามีรายการอยู่ในตารางหน้านี้หรือไม่ ถ้ามีเพิ่มจำนวน แล้วคำนวนยอดใหม่
//---- 2. ถ้าไม่มีรายการอยู่ เช็คสินค้าก่อนว่ามีในระบบหรือไม่
//---- 3. ถ้ามีในระบบ เพิ่มรายการเข้าตาราง
function doReceive()
{
  var barcode = $('#barcode').val();
  var qty = parseInt($('#qty').val());

  if(!isNaN(qty) && barcode.length > 0)
  {
    $('#barcode').attr('disabled', 'disabled');

    //---- ถ้ามีรายการนี้อยู่ในตารางแล้ว
    if($('#barcode_'+barcode).length)
    {
      var no = $('#barcode_'+barcode).val();
      var c_qty = parseDefault(parseInt($('#qty_'+no).val()), 0);
      var new_qty = c_qty + qty;
      $('#qty_'+no).val(new_qty);
      recalRow($('#qty_'+qty), no);
      $('#barcode').val('');
      $('#qty').val(1);
      $('#barcode').removeAttr('disabled');
      $('#barcode').focus();
    }
    else
    {
      //---- ถ้าไม่มีรายการอยู่
      //---- เช็คสินค้า แล้วเพิ่มเข้ารายการ
      load_in();
      $.ajax({
        url:HOME + 'get_item',
        type:'POST',
        cache:false,
        data:{
          'barcode' : barcode
        },
        success:function(rs){
          load_out();
          if(isJson(rs)){
            var pd = $.parseJSON(rs);
            var code = pd.code;
            var gp = $('#gp').val();
            var price = parseFloat(pd.price).toFixed(2);
            var discount = (parseFloat(gp) * 0.01).toFixed(2);
            var amount = price * qty;
            var disAmount = (price * discount) * qty;

            if(code.length)
            {
              var invoice = $('#invoice_code').val();
              var no = $('#no').val();
              no++;
              $('#no').val(no);
              var data = {
                'no' : no,
                'barcode' : barcode,
                'code' : pd.code,
                'name' : pd.name,
                'qty' : qty,
                'price' : price,
                'invoice' : invoice,
                'discount' : gp,
                'amount' : addCommas((amount - disAmount ).toFixed(2))
              };

              var source = $('#row-template').html();
              var output = $('#detail-table');
              render_append(source, data, output);
              reIndex();
              recalTotal();

              $('#barcode').val('');
              $('#qty').val(1);
              $('#barcode').removeAttr('disabled');
              $('#barcode').focus();
            }
          }
          else
          {
            swal('ไม่พบสินค้า');
            $('#barcode').removeAttr('disabled');
          }
        }//-- success
      }); //--- ajax
    }
  }
}


function getActiveCheckList(){
  var zone_code = $('#from_zone_code').val();
  load_in();
  $.ajax({
    url:HOME + 'get_active_check_list/'+zone_code,
    type:'GET',
    cache:'false',
    success:function(rs){
      load_out();
      if(isJson(rs)){
        var source = $('#check-list-template').html();
        var data = $.parseJSON(rs);
        var output = $('#check-list-body');
        render(source, data, output);
        $('#check-list-modal').modal('show');
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

function add_invoice()
{
  var code = $('#return_code').val();
  var invoice = $('#invoice-box').val();
  var customer_code = $('#customer_code').val();

  if(invoice.length == 0){
    return false;
  }

  if(customer_code.length == 0){
    return false;
  }


  load_in();

  $.ajax({
    url:HOME + 'add_invoice',
    type:'POST',
    cache:false,
    data:{
      'invoice' : invoice,
      'customer_code' : customer_code,
      'return_code' : code
    },
    success:function(rs){
      load_out();
      if(isJson(rs))
      {
        var data = $.parseJSON(rs);
        $('#invoice_list').html(data.invoice);
        $('#bill_amount').val(data.amount);
        $('#invoice-box').val('');
      }
      else
      {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}



function removeInvoice(return_code, invoice_code)
{
  load_in();
  $.ajax({
    url:HOME + 'remove_invoice',
    type:'GET',
    cache:false,
    data:{
      'return_code' : return_code,
      'invoice_code' : invoice_code
    },
    success:function(rs){
      load_out();
      if(isJson(rs)){
        var ds = $.parseJSON(rs);
        $('#invoice_list').html(ds.invoice);
        $('#bill_amount').val(ds.amount);
      }
      else
      {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}




function load_invoice(){
  var code = $('#return_code').val();
  var invoice = $('#invoice-box').val();
  var customer_code = $('#customer_code').val();
  var no = $('#no').val();

  if(invoice.length == 0){
    return false;
  }

  if(customer_code.length == 0){
    return false;
  }


  load_in();
  if($('.'+invoice).length > 0){
    load_out();
    return false;
  }

  $.ajax({
    url:HOME + 'get_invoice',
    type:'GET',
    cache:false,
    data:{
      'invoice' : invoice,
      'customer_code' : customer_code,
      'no' : no
    },
    success:function(rs){
      load_out();
      if(isJson(rs))
      {
        var source = $('#row-template').html();
        var data = $.parseJSON(rs);
        var output = $('#detail-table');
        $('#no').val(data.top);
        render_append(source, data, output);
        reIndex();
        recalTotal();
        $('#invoice-box').val('');
      }
      else
      {
        swal(rs);
      }
    }
  })
}
