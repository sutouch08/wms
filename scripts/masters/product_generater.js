function goBack(){
  var style = $('#style').val();
  window.location.href = BASE_URL + 'masters/products/edit/' + style + '/itemTab';
}


$('#items-wizard')
.ace_wizard({
  //step: 2 //optional argument. wizard will jump to step "2" at first
  //buttons: '.my-action-buttons' //which is possibly located somewhere else and is not a sibling of wizard
})
.on('actionclicked.fu.wizard' , function(e, info) {
   //info.step
   //info.direction

   //use e.preventDefault to cancel
})
.on('changed.fu.wizard', function() {
   //after step has changed
})
.on('finished.fu.wizard', function(e) {
   //do something when finish button is clicked
   genItems();
}).on('stepclick.fu.wizard', function(e) {
   //e.preventDefault();//this will prevent clicking and selecting steps
});




$('.colorBox').change(function(){
  var color = $(this).val();
  if($(this).is(':checked') === true){
    var text = $('#co-'+color).text();
    $('#colorBox').append('<input type="hidden" id="col-'+color+'" class="color" value="'+color+'" />');
    $('.imageBox').append('<option value="'+color+'">'+text+'</option>');
  }else{
    $('#col-'+color).remove();
    $(".imageBox option[value='"+color+"']").remove();
  }

  preGen();
});



$('.sizeBox').change(function(){
  var size = $(this).val();
  if($(this).is(':checked') === true){
    var text = $('#si-'+size).text();
    $('#sizeBox').append('<input type="hidden" id="size-'+ size +'" class="size" value="'+size+'" />');
    addCostPrice(size);
  }else{
    $('#size-'+ size).remove();
    removeCostPrice(size);
  }

  preGen();
});


function preGen(){
  var style = $('#style').val();
  var old_style = $('#old_style').val();
  var countColor = $('.color').length;
  var countSize = $('.size').length;

  $('#preGen').html('');

  if(countColor > 0 && countSize > 0){
    genColorAndSize(style, old_style);
  }

  if(countColor > 0 && countSize == 0){
    genColorOnly(style, old_style);
  }

  if(countColor == 0 && countSize > 0){
    genSizeOnly(style, old_style);
  }

}


function genColorAndSize(style, old_style){
  $('.color').each(function(){
    var color = $(this).val();
    $('.size').each(function(){
      var size = $(this).val();
      var itemCode = style + '-' + color + '-' + size;
      var old_code = "";

      if(old_style != ""){
        old_code = old_style + '-' + color + '-' + size;
      }

      addItemRow(itemCode, color, size, old_code);
    });
  });
}


function genColorOnly(style, old_style){
  $('.color').each(function(){
    var color = $(this).val();
    var itemCode = style + '-' + color;
    var old_code = "";
    if(old_style != ""){
      old_code = old_style + '-' + color;
    }

    addItemRow(itemCode, color, '', old_code);
  });
}



function genSizeOnly(style, old_style){
  $('.size').each(function(){
    var size = $(this).val();
    var itemCode = style + '-' + size;
    var old_code = "";
    if(old_style != ""){
      old_code = old_style + '-' + size;
    }
    addItemRow(itemCode, '', size, old_code);
  })
}

function addItemRow(itemCode, color, size, old_code)
{
  var row = '<tr id="'+itemCode+'">'+
            '<td class="middle text-center td-'+color+'">img</td>'+
            '<td class="middle">'+itemCode+'</td>'+
            '<td class="middle">'+
            '<input type="text" class="form-control input-sm" name="old_code['+itemCode+']" value="'+old_code+'"></td>'+
            '</tr>';
  $('#preGen').append(row);
}


$('.imageBox').change(function(){
  var id = $(this).attr('id');
  var color = $(this).val();
  var url = $('#img-'+ id).attr('src');
  var img = '<img src="'+url+'" id="se-'+id+'" class="se-'+id+'" style="width:50px;" />';
  if(color !== ''){
    $('.td-'+color).html(img);
  }else{
    $('.se-'+id).remove();
  }

});



function addCostPrice(size){
  var cost = removeCommas($('#cost').val());
  var price = removeCommas($('#price').val());
  var field = '<tr id="row-'+size+'">'+
              '<td class="middle text-center"><strong>'+ size +'</strong></td>'+
              '<td><input type="number" name="cost['+size+']" class="text-right" value="'+cost+'" /></td>'+
              '<td><input type="number" name="price['+size+']" class="text-right" value="'+price+'" /></td>'+
              '<td></td>'+
              '</tr>';
  $('#setCostPrice').append(field);
}


function removeCostPrice(size){
  $('#row-'+size).remove();
}



function genItems(){
  var style = $('#style').val();
  var countColor = $('.color').length;
  var countSize = $('.size').length;
  
  if(style.length == 0){
    swal('ไม่พบรุ่นสินค้า');
    return false;
  }

  if(countColor == 0 && countSize == 0){
    swal({
      title:'Error!',
      text:'ต้องกำหนดสีหรือไซส์อย่างน้อย 1 รายการ',
      type:'error'
    });

    return false;
  }

  load_in();

  $('#genItemFrom').submit();
}
