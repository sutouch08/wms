var HOME = BASE_URL + 'report/inventory/stock_balance_warehouse/';

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


var label = $('#txt-label');
var totalLabel = $('#total-label');
var click = 0;
var offset = 0;
var limit = 1000;
var totalWhs = 0; //--- จำนวนคลังที่เลือกทั้งหมด
var currentWhs = 0; // ลำดับคลังปัจจุบันทึ่กำลังทำงานอยู่
var totalStock = 0; // จำนวนรายการทั้งหมด (ทุกคลังที่เลือก)
var currentStock = 0; //  จำนวนที่ดึงได้ในปัจจุบัน
var totalWhsItems = 0; // จำนวนรายการทั้งหมดของคลังที่กำลังทำงานอยู่
var cuurrentWhsItems = 0; //  จำนวนรายการที่ดึงได้ในปัจจุบันของคลังที่กำลังทำงานอยู่

var stockData = [];
var allowGetStock = true;
var isFinished = false;
var isCancle = false;
var percent = 0;
var whsList = [];

function doExport() {
  if(click == 0) {
    click = 1;
    let allWhouse = $('#allWarehouse').val();

    if(allWhouse == 0) {
      var count = $('.chk:checked').length;
      if(count == 0){
        $('#wh-modal').modal('show');
        click = 0;
        return false;
      }
    }

    if(allWhouse == 0) {
      whsList = [];
      $('.chk:checked').each(function() {
        whsList.push({'code' : $(this).data('code'), 'name' : $(this).data('name')});
      });
    }

    if(allWhouse == 1) {
      whs = [];
      $('.chk').each(function() {
        whsList.push({'code' : $(this).data('code'), 'name' : $(this).data('name')});
      })
    }

    reset_data();

    $('#progressModal').modal('show');

    getReport();
  } // click = 0
}


function getReport() {
  label.html('Getting data ...');

  if(whsList.length) {
    totalWhs = whsList.length;
    currentWhs = 0;
    countStockItems();
  }
}


function countStockItems() {
  totalLabel.html('กำลังคำนวนจำนวนรายการทั้งหมด...');

  $.ajax({
    url:HOME + 'countStockItems',
    type:'POST',
    cache:false,
    data:{
      "whsList" : JSON.stringify(whsList)
    },
    success:function(rs) {
      let count = parseDefault(parseInt(rs), 0);

      if(count > 0) {
        totalStock = count;
        totalLabel.html("จำนวนรายการทั้งหมด " + addCommas(totalStock) + " รายการ");
        update_progress();
        $('#txt-percent').removeClass('hide');

        let whsCode = whsList[currentWhs].code;
        countWhsItems(whsCode);
      }
      else {
        totalLabel.html("ไม่พบรายการตามเงื่อนไขที่กำหนด");
        load_out();
      }
    }
  })
}


function countWhsItems() {
  let whsCode = whsList[currentWhs].code;
  let whsName = whsList[currentWhs].name;
  totalWhsItems = 0;
  cuurrentWhsItems = 0;
  offset = 0;
  label.html("กำลังคำนวนรายการจากคลัง " + whsName);

  $.ajax({
    url:HOME + 'countWhsItems',
    type:'POST',
    cache:false,
    data:{
      "whsCode" : whsCode
    },
    success:function(rs) {
      let count = parseDefault(parseInt(rs), 0);

      if(count > 0) {
        totalWhsItems = count;
        update_progress();
        $('#txt-percent').removeClass('hide');
      }

      getData();
    }
  })
}


function getData() {
  let whsCode = whsList[currentWhs].code;
  let whsName = whsList[currentWhs].name;

  $.ajax({
    url:HOME + 'getStock',
    type:'POST',
    cache:false,
    data:{
      "whsCode" : whsCode,
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
            cuurrentWhsItems += ds.rows;

            ds.data.forEach((row) => {
              stockData.push(row);
            });

            update_progress();
            totalLabel.html("จำนวนสินค้าทั้งหมด  " + addCommas(currentStock) + " / " + addCommas(totalStock));
            label.html("กำลังดึงข้อมูลจากคลัง " + whsName +" : " + addCommas(cuurrentWhsItems) + " / " + addCommas(totalWhsItems));

            if(isCancel == false) {
              if(cuurrentWhsItems == totalWhsItems) {
                currentWhs++;

                if(currentWhs < totalWhs) {
                  countWhsItems();
                }
                else {
                  createFile();
                }
              }
              else {
                if(currentStock < totalStock) {
                  getData();
                }
              }
            }
            else {
              finish_and_close();
            }
          }
          else {
            if(cuurrentWhsItems == totalWhsItems) {
              currentWhs++;

              if(currentWhs < totalWhs) {
                whsCode = whsList[currentWhs].code;
                countWhsItems(whsCode);
              }
            }
            else {
              getData();
            }
          }

          if(currentWhs == totalWhs && currentStock == totalStock) {
            createFile();
          }
        }
        else {
          click = 0;
          showError(ds.message);
        }
      }
      else {
        click = 0;
        showError(rs);
      }
    }
  })
}


function createFile() {
  finish_progress();
  /* generate worksheet and workbook */
  const worksheet = XLSX.utils.json_to_sheet(stockData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Data");

  /* fix headers */
  XLSX.utils.sheet_add_aoa(worksheet, [["รหัสคลัง", "ชื่อคลัง", "รหัสสินค้า", "สินค้า", "ทุน (มาตรฐาน)", "คงเหลือ"]], { origin:"A1"});

  XLSX.writeFile(workbook, "Stock_By_Warehouse_Report.xlsx", {compression:true});

  finish_and_close();
}

function reset_data() {
  offset = 0;
  totalStock = 0;
  currentStock = 0;
  totalWhsItems = 0;
  cuurrentWhsItems = 0;
  totalWhs = 0;
  currentWhs = 0;
  percent = 0;
  stockData = [];
  isFinished = false,
  isCancel = false;
  $('#txt-percent').addClass('hide');
  $('#txt-percent').attr("data-percent", 0 + "%");
  $('#progress-bar').css("width", 0+"%");
  click = 0;
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
