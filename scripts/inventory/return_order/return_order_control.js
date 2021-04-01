$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    var qty = $('#qty').val();
    doReceive();
  }
});


$('#invoice-box').keyup(function(e){
  if(e.keyCode === 13){
    load_invoice();
  }
})



function load_invoice(){
  var code = $('#return_code').val();
  var invoice = $('#invoice-box').val();
  if(invoice.length == 0){
    return false;
  }


  load_in();
  if($('.'+invoice).length > 0){
    load_out();
    return false;
  }

  $.ajax({
    url:HOME + 'get_invoice/' + invoice,
    type:'GET',
    cache:false,
    success:function(rs){
      load_out();
      if(isJson(rs))
      {
        var source = $('#row-template').html();
        var data = $.parseJSON(rs);
        var output = $('#detail-table');
        render_append(source, data, output);
        reIndex();
        inputQtyInit();
        //inputPriceInit();
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
