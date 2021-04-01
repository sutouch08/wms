$('.cost').keyup(function(){
  if(isNaN(parseFloat($(this).val())))
  {
    $(this).val(0);
  }
})


$('.price').keyup(function(){
  if(isNaN(parseFloat($(this).val())))
  {
    $(this).val(0);
  }
})


function update_size_cost_price(no)
{
  var code = $('#style_code').val();
  var size = $('#size_'+no).val();
  var cost = $('#cost_'+no).val();
  var price = $('#price_'+no).val();

  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/update_cost_price_by_size',
    type:'POST',
    cache:false,
    data:{
      'style_code' : code,
      'size' : size,
      'cost' : cost,
      'price' : price
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    }
  })
}

function update_all_size_cost_price()
{
  $('#sizeForm').submit();
}
