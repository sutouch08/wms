var HOME = BASE_URL + 'report/inventory/fast_move_stock/';

function getReport() {
  $('#is-min').clearError();

  let h = {
    'zone_code' : $('#zone-code').val().trim(),
    'product_code' : $('#pd-code').val().trim(),
    'min_stock' : $('#min-stock').val().trim(),
    'is_min' : $('#is-min').val()
  }

  if(h.is_min <= 0) {
    $('#is-min').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
          let output = $('result');

          render(source, ds.data, output);
        }
        else {
          beep();
          showError(ds.message);
        }
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}



function doExport(){
  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var currentDate = $('#currentDate').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var date = $('#date').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allWhouse == 0){
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(currentDate == 0){
    if(date == ''){
      $('#date').addClass('has-error');
      return false;
    }else{
      $('#date').removeClass('has-error');
    }
  }
  else
  {
    $('#date').removeClass('has-error');
  }

  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'allWhouse' , 'value' : allWhouse},
    {'name' : 'currentDate' , 'value' : currentDate},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
    {'name' : 'date', 'value' : date}
  ];

  if(allWhouse == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse['+$(this).val()+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  $('#reportForm').submit();
  //
  // data = $.param(data);
  //
  // var token = new Date().getTime();
  // var target = HOME + 'do_export';
  // target += '&'+data;
  // target += '&token='+token;
  // get_download(token);
  // window.location.href = target;

}
