function saveProduct(){
  id_rule = $('#id_rule').val();
  all_product = $('#all_product').val();
  product_style = $('#product_style').val();
  product_group = $('#product_group').val();
  product_sub = $('#product_sub').val();
  product_category = $('#product_category').val();
  product_type = $('#product_type').val();
  product_kind = $('#product_kind').val();
  product_brand = $('#product_brand').val();
  product_year = $('#product_year').val();

  countId = $('.styleId').length;

  //--- ถ้าเลือกสินค้าทั้งหมดจะไม่สนใจเงื่อนไขอื่นๆ
  if(all_product == 'N'){

    //--- ถ้าเป็นการระบุชื่อสินค้ารายคนแล้วยังไม่ได้ระบุ
    if(product_style == 'Y' && countId == 0){
      swal('กรุณาระบุสินค้าอย่างน้อย 1 รายการ');
      return false;
    }

    if(product_style == 'N'){
      count_group = parseInt($('.chk-pd-group:checked').size());
      count_sub   = parseInt($('.chk-pd-sub:checked').size());
      count_type  = parseInt($('.chk-pd-type:checked').size());
      count_kind  = parseInt($('.chk-pd-kind:checked').size());
      count_cate  = parseInt($('.chk-pd-cat:checked').size());
      count_brand = parseInt($('.chk-pd-brand:checked').size());
      count_year  = parseInt($('.chk-pd-year:checked').size());
      sum_count = count_group + count_sub + count_type + count_kind + count_cate + count_brand + count_year;


      //---- กรณีลือกสินค้าแบบเป็นกลุ่มแล้วไม่ได้เลือก
      if(product_group == 'Y' && count_group == 0 ){
        swal('กรุณาเลือกกลุ่มสินค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกสินค้าแบบเป็นกลุ่มย่อยแล้วไม่ได้เลือก
      if(product_sub == 'Y' && count_sub == 0 ){
        swal('กรุณาเลือกกลุ่มย่อย อย่างน้อย 1 รายการ');
        return false;
      }


      //---- กรณีลือกสินค้าแบบเป็นหมวดหมู่แล้วไม่ได้เลือก
      if(product_category == 'Y' && count_cate == 0 ){
        swal('กรุณาเลือกหมวดหมู่สินค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกสินค้าแบบเป็นชนิดแล้วไม่ได้เลือก
      if(product_type == 'Y' && count_type == 0 ){
        swal('กรุณาเลือกชนิดสินค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกสินค้าแบบเป็นประเภทแล้วไม่ได้เลือก
      if(product_kind == 'Y' && count_kind == 0 ){
        swal('กรุณาเลือกประเภทสินค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกสินค้าแบบเป็นยี่ห้อแล้วไม่ได้เลือก
      if(product_brand == 'Y' && count_brand == 0 ){
        swal('กรุณาเลือกยี่ห้อ อย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกสินค้าแบบเป็นเกรดแล้วไม่ได้เลือก
      if(product_year == 'Y' && count_year == 0 ){
        swal('กรุณาเลือกปีสินค้าอย่างน้อย 1 รายการ');
        return false;
      }

      if(sum_count == 0){
        swal('กรุณาระบุอย่างน้อย 1 เงื่อนไข');
        return false;
      }

    } //-- end if product_style == 'N'

  } //--- end if all_product

  ds = [
    {'name':'id_rule', 'value':id_rule},
    {'name':'all_product', 'value':all_product},
    {'name':'product_style', 'value':product_style},
    {'name':'product_group', 'value':product_group},
    {'name':'product_sub_group', 'value':product_sub},
    {'name':'product_category', 'value':product_category},
    {'name':'product_type', 'value':product_type},
    {'name':'product_kind', 'value':product_kind},
    {'name':'product_brand', 'value':product_brand},
    {'name':'product_year', 'value':product_year}
  ];

  //--- เก็บข้อมูลชื่อสินค้า
  if(product_style == 'Y'){
    $('.styleId').each(function(index, el) {
      ds.push({'name':$(this).attr('name'), 'value':$(this).val()});
    });
  }

  //--- เก็บข้อมูลกลุ่มสินค้า
  if(product_style == 'N' && product_group == 'Y'){
    i = 0;
    $('.chk-pd-group').each(function(index, el) {
      if($(this).is(':checked')){
        name = 'productGroup['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }


  //--- เก็บข้อมูลกลุ่มย่อยสินค้า
  if(product_style == 'N' && product_sub == 'Y'){
    i = 0;
    $('.chk-pd-sub').each(function(index, el) {
      if($(this).is(':checked')){
        name = 'productSubGroup['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }


  //--- เก็บข้อมูลหมวดหมู่สินค้า
  if(product_style == 'N' && product_category == 'Y'){
    i = 0;
    $('.chk-pd-cat').each(function(index, el) {
      if($(this).is(':checked')){
        name = 'productCategory['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }


  //--- เก็บข้อมูลชนิดสินค้า
  if(product_style == 'N' && product_type == 'Y'){
    i = 0;
    $('.chk-pd-type').each(function(index, el) {
      if($(this).is(':checked')){
        name = 'productType['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  //--- เก็บข้อมูเลือกประเภทสินค้า
  if(product_style == 'N' && product_kind == 'Y'){
    i = 0;
    $('.chk-pd-kind').each(function(index, el){
      if($(this).is(':checked')){
        name = 'productKind['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  //--- เก็บข้อมูลยี่ห้อสินค้า
  if(product_style == 'N' && product_brand == 'Y'){
    i = 0;
    $('.chk-pd-brand').each(function(index, el){
      if($(this).is(':checked')){
        name = 'productBrand['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }


  //--- เก็บข้อมูลเกรดสินค้า
  if(product_style == 'N' && product_year == 'Y'){
    i = 0;
    $('.chk-pd-year').each(function(index, el){
      if($(this).is(':checked')){
        name = 'productYear['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  load_in();
  $.ajax({
    url: BASE_URL + 'discount/discount_rule/set_product_rule',
    type:'POST',
    cache:'false',
    data:ds,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Saved',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });


} //--- end function




function showStyleList(){
  $('#style-list-modal').modal('show');
}



function showProductGroup(){
  $('#pd-group-modal').modal('show');
}



function showProductSubGroup(){
  $('#pd-sub-modal').modal('show');
}



function showProductType(){
  $('#pd-type-modal').modal('show');
}



function showProductKind(){
  $('#pd-kind-modal').modal('show');
}



function showProductCategory(){
  $('#pd-cat-modal').modal('show');
}




function showProductBrand(){
  $('#pd-brand-modal').modal('show');
}



function showProductYear(){
  $('#pd-year-modal').modal('show');
}





$('#txt-style-id-box').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      addStyleId();
    }
  }
});





$('.chk-pd-group').change(function(e){
  count = 0;
  $('.chk-pd-group').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-group').text(count);
});





$('.chk-pd-type').change(function(e){
  count = 0;
  $('.chk-pd-type').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-type').text(count);
});





$('.chk-pd-kind').change(function(e){
  count = 0;
  $('.chk-pd-kind').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-kind').text(count);
});




$('.chk-pd-cat').change(function(e){
  count = 0;
  $('.chk-pd-cat').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-cat').text(count);
});




$('.chk-pd-sub').change(function(e){
  count = 0;
  $('.chk-pd-sub').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-sub').text(count);
});



$('.chk-pd-brand').change(function(e){
  count = 0;
  $('.chk-pd-brand').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-brand').text(count);
});




$('.chk-pd-year').change(function(e){
  count = 0;
  $('.chk-pd-year').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-year').text(count);
});





$('#txt-style-id-box').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    arr = $(this).val().split(' | ');
    if(arr.length == 2){
      code = arr[0];
      $('#id_style').val(code);
      $(this).val(code);
    }else{
      $(this).val('');
      $('#id_style').val('');
    }
  }
});



function addStyleId(){
  id = $('#id_style').val();
  psCode = $('#txt-style-id-box').val();
  if(psCode.length > 0){
    count = parseInt($('#psCount').text());
    count++;
    list  = '<li style="min-height:15px; padding:5px;" id="style-id-'+id+'">';
    list += '<a href="#" class="paddint-5" onclick="removeStyleId(\''+id+'\')"><i class="fa fa-times red"></i></a>';
    list += '<span style="margin-left:10px;">'+psCode+'</span>';
    list += '</li>';

    input = '<input type="hidden" name="styleId['+id+']" id="styleId-'+id+'" class="styleId" value="'+id+'" />';
    $('#style-list').append(list);
    $('#style-list').append(input);
    $('#psCount').text(count);

    $('#txt-style-id-box').val('');
    $('#id_style').val('');
    $('#txt-style-id-box').focus();
  }

}



function removeStyleId(id){
  count = parseInt($('#psCount').text());
  $('#style-id-'+id).remove();
  $('#styleId-'+id).remove();
  count--;
  $('#psCount').text(count);
}


//--- เลือกสินค้าทั้งหมด
function toggleAllProduct(option){
  $('#all_product').val(option);
  if(option == 'Y'){
    $('#btn-pd-all-yes').addClass('btn-primary');
    $('#btn-pd-all-no').removeClass('btn-primary');
    disActiveProductControl();
  }else if(option == 'N'){
    $('#btn-pd-all-no').addClass('btn-primary');
    $('#btn-pd-all-yes').removeClass('btn-primary');
    $('.not-pd-all').removeAttr('disabled');
    activeProductControl();
  }
}



function disActiveProductControl(){
  toggleProductGroup();
  toggleProductSubGroup();
  toggleProductCategory();
  toggleProductType();
  toggleProductKind();
  toggleProductBrand();
  toggleProductYear();
  $('.not-pd-all').attr('disabled', 'disabled');
}




function activeProductControl(){
  product_style = $('#product_style').val();
  if(product_style == 'Y'){
    toggleProductGroup();
    toggleProductSubGroup();
    toggleProductCategory();
    toggleProductType();
    toggleProductKind();
    toggleProductBrand();
    toggleProductYear();
    return;
  }

  toggleProductGroup($('#product_group').val());
  toggleProductSubGroup($('#product_sub').val());
  toggleProductCategory($('#product_category').val());
  toggleProductType($('#product_type').val());
  toggleProductKind($('#product_kind').val());
  toggleProductBrand($('#product_brand').val());
  toggleProductYear($('#product_year').val());
}






function toggleStyleId(option){
  if(option == '' || option == undefined){
    option = $('#product_style').val();
  }

  $('#product_style').val(option);
  if(option == 'Y'){
    $('#btn-style-id-yes').addClass('btn-primary');
    $('#btn-style-id-no').removeClass('btn-primary');
    $('#txt-style-id-box').removeAttr('disabled');
    $('#btn-style-id-add').removeAttr('disabled');

  }else if(option == 'N'){
    $('#btn-style-id-no').addClass('btn-primary');
    $('#btn-style-id-yes').removeClass('btn-primary');
    $('#txt-style-id-box').attr('disabled', 'disabled');
    $('#btn-style-id-add').attr('disabled', 'disabled');
  }

  activeProductControl();
}


function toggleProductGroup(option){
  if(option == '' || option == undefined){
    option = $('#product_group').val();
  }

  $('#product_group').val(option);
  all = $('#all_product').val();
  sc = $('#product_style').val();

  if(option == 'Y' && sc == 'N' && all == 'N'){
    $('#btn-pd-group-no').removeClass('btn-primary');
    $('#btn-pd-group-yes').addClass('btn-primary');
    $('#btn-pd-group-no').removeAttr('disabled');
    $('#btn-pd-group-yes').removeAttr('disabled');
    $('#btn-select-pd-group').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-group-yes').removeClass('btn-primary');
    $('#btn-pd-group-no').addClass('btn-primary');
    $('#btn-pd-group-no').removeAttr('disabled');
    $('#btn-pd-group-yes').removeAttr('disabled');
    $('#btn-select-pd-group').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-group-yes').attr('disabled', 'disabled');
    $('#btn-pd-group-no').attr('disabled', 'disabled');
    $('#btn-select-pd-group').attr('disabled', 'disabled');
    return;
  }
}


function toggleProductSubGroup(option){
  if(option == '' || option == undefined){
    option = $('#product_sub').val();
  }

  $('#product_sub').val(option);
  all = $('#all_product').val();
  sc = $('#product_style').val();
  if(option == 'Y' && sc == 'N' && all == 'N'){
    $('#btn-pd-sub-no').removeClass('btn-primary');
    $('#btn-pd-sub-yes').addClass('btn-primary');
    $('#btn-pd-sub-no').removeAttr('disabled');
    $('#btn-pd-sub-yes').removeAttr('disabled');
    $('#btn-select-pd-sub').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-sub-yes').removeClass('btn-primary');
    $('#btn-pd-sub-no').addClass('btn-primary');
    $('#btn-pd-sub-no').removeAttr('disabled');
    $('#btn-pd-sub-yes').removeAttr('disabled');
    $('#btn-select-pd-sub').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-sub-yes').attr('disabled', 'disabled');
    $('#btn-pd-sub-no').attr('disabled', 'disabled');
    $('#btn-select-pd-sub').attr('disabled', 'disabled');
    return;
  }
}





function toggleProductCategory(option){
  if(option == '' || option == undefined){
    option = $('#product_category').val();
  }


  $('#product_category').val(option);
  sc = $('#product_style').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-pd-cat-no').removeClass('btn-primary');
    $('#btn-pd-cat-yes').addClass('btn-primary');
    $('#btn-pd-cat-no').removeAttr('disabled');
    $('#btn-pd-cat-yes').removeAttr('disabled');
    $('#btn-select-pd-cat').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-cat-yes').removeClass('btn-primary');
    $('#btn-pd-cat-no').addClass('btn-primary');
    $('#btn-pd-cat-no').removeAttr('disabled');
    $('#btn-pd-cat-yes').removeAttr('disabled');
    $('#btn-select-pd-cat').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-cat-yes').attr('disabled', 'disabled');
    $('#btn-pd-cat-no').attr('disabled', 'disabled');
    $('#btn-select-pd-cat').attr('disabled', 'disabled');
  }
}



function toggleProductType(option){
  if(option == '' || option == undefined){
    option = $('#product_type').val();
  }

  $('#product_type').val(option);
  sc = $('#product_style').val();
  all = $('#all_product').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-pd-type-no').removeClass('btn-primary');
    $('#btn-pd-type-yes').addClass('btn-primary');
    $('#btn-pd-type-no').removeAttr('disabled');
    $('#btn-pd-type-yes').removeAttr('disabled');
    $('#btn-select-pd-type').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-type-yes').removeClass('btn-primary');
    $('#btn-pd-type-no').addClass('btn-primary');
    $('#btn-pd-type-yes').removeAttr('disabled');
    $('#btn-pd-type-no').removeAttr('disabled');
    $('#btn-select-pd-type').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-type-yes').attr('disabled', 'disabled');
    $('#btn-pd-type-no').attr('disabled', 'disabled');
    $('#btn-select-pd-type').attr('disabled', 'disabled');
  }
}



function toggleProductKind(option){
  if(option == '' || option == undefined){
    option = $('#product_kind').val();
  }


  $('#product_kind').val(option);
  sc = $('#product_style').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-pd-kind-no').removeClass('btn-primary');
    $('#btn-pd-kind-yes').addClass('btn-primary');
    $('#btn-pd-kind-no').removeAttr('disabled');
    $('#btn-pd-kind-yes').removeAttr('disabled');
    $('#btn-select-pd-kind').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-kind-yes').removeClass('btn-primary');
    $('#btn-pd-kind-no').addClass('btn-primary');
    $('#btn-pd-kind-no').removeAttr('disabled');
    $('#btn-pd-kind-yes').removeAttr('disabled');
    $('#btn-select-pd-kind').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-kind-yes').attr('disabled', 'disabled');
    $('#btn-pd-kind-no').attr('disabled', 'disabled');
    $('#btn-select-pd-kind').attr('disabled', 'disabled');
  }
}


function toggleProductBrand(option){
  if(option == '' || option == undefined){
    option = $('#product_brand').val();
  }


  $('#product_brand').val(option);
  sc = $('#product_style').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-pd-brand-no').removeClass('btn-primary');
    $('#btn-pd-brand-yes').addClass('btn-primary');
    $('#btn-pd-brand-no').removeAttr('disabled');
    $('#btn-pd-brand-yes').removeAttr('disabled');
    $('#btn-select-pd-brand').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-brand-yes').removeClass('btn-primary');
    $('#btn-pd-brand-no').addClass('btn-primary');
    $('#btn-pd-brand-no').removeAttr('disabled');
    $('#btn-pd-brand-yes').removeAttr('disabled');
    $('#btn-select-pd-brand').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-brand-yes').attr('disabled', 'disabled');
    $('#btn-pd-brand-no').attr('disabled', 'disabled');
    $('#btn-select-pd-brand').attr('disabled', 'disabled');
  }
}


function toggleProductYear(option){
  if(option == '' || option == undefined){
    option = $('#product_year').val();
  }


  $('#product_year').val(option);
  sc = $('#product_style').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-pd-year-no').removeClass('btn-primary');
    $('#btn-pd-year-yes').addClass('btn-primary');
    $('#btn-pd-year-no').removeAttr('disabled');
    $('#btn-pd-year-yes').removeAttr('disabled');
    $('#btn-select-pd-year').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-pd-year-yes').removeClass('btn-primary');
    $('#btn-pd-year-no').addClass('btn-primary');
    $('#btn-pd-year-no').removeAttr('disabled');
    $('#btn-pd-year-yes').removeAttr('disabled');
    $('#btn-select-pd-year').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-pd-year-yes').attr('disabled', 'disabled');
    $('#btn-pd-year-no').attr('disabled', 'disabled');
    $('#btn-select-pd-year').attr('disabled', 'disabled');
  }
}


$(document).ready(function() {
  var all = $('#all_product').val();
  var styleId = $('#product_style').val();
  toggleAllProduct(all);
  toggleStyleId(styleId);
});
