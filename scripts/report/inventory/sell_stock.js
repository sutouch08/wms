var HOME = BASE_URL + 'report/inventory/sell_stock/';

function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var pdFrom = arr[0];
    $(this).val(pdFrom);
    var pdTo = $('#pdTo').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var pdTo = arr[0];
    $(this).val(pdTo);
    var pdFrom = $('#pdFrom').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
})

function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }
}


function getReport(){
  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();

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
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }

  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'allWhouse' , 'value' : allWhouse},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo}
  ];

  if(allWhouse == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse['+$(this).val()+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
      else {
        swal({
          title:'Oops !',
          text:rs,
          type:'warning'
        });
      }
    }
  });

}

var label = $('#txt-label');
var click = 0;
var offset = 0;
var limit = 100;
var totalStock = 0;
var currentStock = 0;
var stockData = [];
var allowGetStock = true;
var isFinished = false;
var isCancle = false;
var percent = 0;

function doExport() {
  if(click > 0) {
    return false;
  }

  $('.e').removeClass('has-error');

  click = 1;
  $('.btn-report').attr('disabled', 'disabled');

  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();

  if(allProduct == 0) {
    if(pdFrom.length == 0) {
      $('#pdFrom').addClass('has-error');
      click = 0;
      $('.btn-report').removeAttr('disabled');
      return false;
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }
  }


  if(allWhouse == 0){
    var count = $('.chk:checked').length;
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }

  var data = {
    'allProduct' : allProduct,
    'allWhouse' : allWhouse,
    'pdFrom' : pdFrom,
    'pdTo' : pdTo,
    'whsList' : []
  };

  if(allWhouse == 0) {
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse['+$(this).val()+']';
        data.whsList.push($(this).val());
      }
    });
  }

  reset_data();

  $('#progressModal').modal('show');

  countStockItems(data);

}

function activeButton() {
  click = 0;
  $('.btn-report').removeAttr('disabled');
}

function countStockItems(data) {
  label.text('Getting data...');

  $.ajax({
    url:HOME + 'countStockItems',
    type:'POST',
    cache:false,
    data:{
      "filter" : JSON.stringify(data)
    },
    success:function(rs) {
      let count = parseDefault(parseInt(rs), 0);

      if(count > 0) {
        totalStock = count;
        label.text("Getting data .. " + addCommas(currentStock) + " Of " + addCommas(totalStock));
        update_progress();
        $('#txt-percent').removeClass('hide');

        getData(data);
      }
    }
  })
}


function getData(option) {
  label.text("Getting data .. " + addCommas(currentStock) + " Of " + addCommas(totalStock));

  $.ajax({
    url:HOME + 'getStock',
    type:'POST',
    cache:false,
    data:{
      "filter" : JSON.stringify(option),
      "limit" : limit,
      "offset" : offset
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          if(ds.rows > 0) {
            offset += ds.rows;

            currentStock += ds.rows;

            ds.data.forEach((row) => {
              stockData.push(row);
            });

            update_progress();

            if(isCancel == false) {
              getData(option);
            }
            else {
              finish_and_close();
            }
          }
          else {
            finish_progress();
            /* generate worksheet and workbook */
            const worksheet = XLSX.utils.json_to_sheet(stockData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Data");

            /* fix headers */
            XLSX.utils.sheet_add_aoa(worksheet, [["ลำดับ", "รหัส", "รหัสเก่า", "สินค้า", "ทุน", "ในสต็อก", "ยอดจอง", "คงเหลือ", "มูลค่า"]], { origin:"A1"});

            XLSX.writeFile(workbook, "Stock_Report.xlsx", {compression:true});

            finish_and_close();
          }
        }
        else {
          load_out();
          activeButton();
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          })
        }
      }
      else {
        load_out();
        activeButton();
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function reset_data() {
  offset = 0;
  totalStock = 0;
  currentStock = 0;
  percent = 0;
  stockData = [];
  isFinished = false,
  isCancel = false;
  $('#txt-percent').addClass('hide');
  $('#txt-percent').attr("data-percent", 0 + "%");
  $('#progress-bar').css("width", 0+"%");
  click = 0;
  $('.btn-report').removeAttr('disabled');
}


function update_progress() {
  percent = (currentStock/totalStock) * 100;

  var percentage;
  if(percent > 100){
    percentage = 100;
  }else{
    percentage = parseInt(percent);
  }

  $('#txt-percent').attr("data-percent", percentage + "%");
  $('#progress-bar').css("width", percentage+"%");
}


function finish_progress(){
  percent = 100;
  $('#txt-percent').attr("data-percent", percent + "%");
  $('#progress-bar').css("width", percent+"%");
}

function finish_and_close() {
  $('#progressModal').modal('hide');
  reset_data();
}

function cancel_and_close() {
  isCancel = true;
  $('#progressModal').modal('hide');
}
