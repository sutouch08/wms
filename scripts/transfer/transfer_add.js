var click = 0;
//--- เพิ่มเอกสารโอนคลังใหม่
// function addTransfer() {
//   if(click === 0) {
//     click = 1;
//
//     $('.h').removeClass('has-error');
//
//     var code = $('#code').val();
//
//     //--- วันที่เอกสาร
//     var date_add = $('#date').val();
//
//     //--- คลังต้นทาง
//     var from_warehouse = $('#from_warehouse').val();
//     var from_warehouse_code = $('#from_warehouse_code').val();
//
//     //--- คลังปลายทาง
//     var to_warehouse = $('#to_warehouse').val();
//     var to_warehouse_code = $('#to_warehouse_code').val();
//     var is_wms = $('#is_wms').val();
//     var api = $('#api').val();
//     var wx_code = $('#wx_code').val();
//
//     //--- หมายเหตุ
//     var remark = $.trim($('#remark').val());
//     var reqRemark = $('#require_remark').val() == 1 ? true : false;
//
//     //--- ตรวจสอบวันที่
//     if( ! isDate(date_add))
//     {
//       swal('วันที่ไม่ถูกต้อง');
//       $('#date').addClass('has-error');
//       click = 0;
//       return false;
//     }
//
//     //--- ตรวจสอบคลังต้นทาง
//     if(from_warehouse.length == 0 || from_warehouse_code == ''){
//       swal('คลังต้นทางไม่ถูกต้อง');
//       $('.f').addClass('has-error');
//       click = 0;
//       return false;
//     }
//
//     //--- ตรวจสอบคลังปลายทาง
//     if(to_warehouse_code == '' || to_warehouse.length == 0){
//       swal('คลังปลายทางไม่ถูกต้อง');
//       $('.t').addClass('has-error');
//       click = 0;
//       return false;
//     }
//
//     //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
//     if( from_warehouse_code == to_warehouse_code) {
//       swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
//       $('.f').addClass('has-error');
//       $('.t').addClass('has-error');
//       click = 0;
//       return false;
//     }
//
//     if( is_wms == "") {
//       swal('กรุณาเลือกการดำเนินการ');
//       $('#is_wms').addClass('has-error');
//       click = 0;
//       return false;
//     }
//
//     if(reqRemark && remark.length < 10) {
//       swal({
//         title: 'Required',
//         text: "กรุณาระบุหมายเหตุอย่างน้อย 10 ตัวอักษร",
//         type:'warning'
//       });
//
//       $('#remark').addClass('has-error');
//       click = 0;
//       return false;
//     }
//
//     load_in();
//
//     $.ajax({
//       url:HOME + 'add',
//       type:'POST',
//       cache:false,
//       data:{
//         'code' : code,
//         'date' : date_add,
//         'from_warehouse_code' : from_warehouse_code,
//         'to_warehouse_code' : to_warehouse_code,
//         'is_wms' : is_wms,
//         'api' : api,
//         'wx_code' : wx_code,
//         'remark' : remark
//       },
//       success:function(rs) {
//         load_out();
//         if(isJson(rs)) {
//           let ds = JSON.parse(rs);
//           if(ds.status == 'success') {
//             let uuid = get_uuid();
//             window.location.href = HOME + 'edit/'+ds.code+'/'+uuid;
//           }
//           else {
//             swal({
//               title:'Error!',
//               text:ds.message,
//               type:'error',
//               html:true
//             });
//           }
//
//           click = 0;
//         }
//         else {
//           swal({
//             title:'Error!',
//             text:rs,
//             type:'error',
//             html:true
//           });
//
//           click = 0;
//         }
//       }
//     });
//   }
//
// }


function addTransfer() {
  if(click === 0) {
    click = 1;

    $('.h').removeClass('has-error');

    let h = {
      'date_add' : $('#date').val().trim(),
      'from_warehouse' : $('#from-warehouse').val().trim(),
      'to_warehouse' : $('#to-warehouse').val().trim(),
      'is_wms' : $('#is-wms').val(),
      'api' : 0,
      'wx_code' : $('#wx-code').val().trim(),
      'remark' : $('#remark').val().trim()
    }

    let reqRemark = $('#require-remark').val() == 1 ? true : false;

    //--- ตรวจสอบวันที่
    if( ! isDate(h.date_add))
    {
      swal('วันที่ไม่ถูกต้อง');
      $('#date').hasError();
      click = 0;
      return false;
    }

    //--- ตรวจสอบคลังต้นทาง
    if(h.from_warehouse == '') {
      swal('กรุณาเลือกคลังต้นทาง');
      $('#from-warehouse').hasError();
      click = 0;
      return false;
    }

    //--- ตรวจสอบคลังปลายทาง
    if(h.to_warehouse == '') {
      swal('กรุณาเลือกคลังปลายทาง');
      $('#to-warehouse').hasError();
      click = 0;
      return false;
    }

    //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
    if( h.from_warehouse == h.to_warehouse) {
      swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
      $('#from-warehouse').hasError();
      $('#to-warehouse').hasError();
      click = 0;
      return false;
    }

    if(reqRemark && h.remark.length < 10) {
      swal({
        title: 'Required',
        text: "กรุณาระบุหมายเหตุอย่างน้อย 10 ตัวอักษร",
        type:'warning'
      });

      $('#remark').addClass('has-error');
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            let uuid = get_uuid();
            window.location.href = HOME + 'edit/'+ds.code+'/'+uuid;
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
        click = 0;
        showError(rs);
      }
    });
  }
}

//--- update เอกสาร
function update() {
  $('.h').removeClass('has-error');

  //--- ไอดีเอกสาร สำหรับส่งไปอ้างอิงการแก้ไข
  let code = $('#transfer_code').val();

  let h = {
    'code' : $('#code').val(),
    'date_add' : $('#date').val().trim(),
    'from_warehouse' : $('#from-warehouse').val(),
    'to_warehouse' : $('#to-warehouse').val(),
    'is_wms' : $('#is-wms').val(),
    'wx_code' : $('#wx-code').val().trim(),
    'remark' : $('#remark').val().trim()
  };

  let prevFromWhs = $('#prev-from-warehouse').val();
  let prevToWhs = $('#prev-to-warehouse').val();

  //--- ตรวจสอบวันที่
  if( ! isDate(h.date_add)) {
    swal('วันที่ไม่ถูกต้อง');
    $('#date').hasError();
    return false;
  }

  //--- ตรวจสอบคลังต้นทาง
  if(h.from_warehouse == '') {
    swal('กรุณาเลือกคลังต้นทาง');
    $('#from-warehouse').hasError();
    return false;
  }

  //--- ตรวจสอบคลังปลายทาง
  if(h.to_warehouse == '') {
    swal('กรุณาเลือกคลังปลายทาง');
    $('#to-warehouse').hasError();
    return false;
  }

  //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
  if( h.from_warehouse == h.to_warehouse) {
    swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
    $('#from-warehouse').hasError();
    $('#to-warehouse').hasError();
    return false;
  }

  //--- ตรวจสอบหากมีการเปลี่ยนคลัง ต้องเช็คก่อนว่ามีการทำรายการไปแล้วหรือยัง
  if(h.from_warehouse != prevFromWhs || h.to_warehouse != prevToWhs)
  {
    $.ajax({
      url:HOME + 'is_exists_detail/'+code,
      type:'POST',
      cache:false,
      success:function(rs)
      {
        if(rs === 'exists')
        {
          swal({
            title:'Warning !',
            text:'มีการทำรายการแล้วไม่สามารถเปลี่ยนคลังได้',
            type:'warning'
          });

          return false;
        }
        else
        {
          do_update(h);
        }
      }
    })
  }
  else
  {
    do_update(h);
  }
}


function do_update(h)
{
  load_in();

  //--- ถ้าไม่มีอะไรผิดพลาด ส่งข้อมูไป update
  $.ajax({
    url: HOME + 'update',
    type:'POST',
    cache:'false',
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          refresh();
        }, 1200);
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



//--- แก้ไขหัวเอกสาร
function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



//---  บันทึกเอกสาร
function save() {
  var code = $('#code').val();

  //--- check temp
  $.ajax({
    url:HOME + 'check_temp_exists/'+code,
    type:'POST',
    cache:'false',
    success:function(rs) {
      //--- ถ้าไม่มียอดค้างใน temp
      if( rs.trim() == 'not_exists') {
        saveTransfer(code);
      }
      else{
        swal({
          title:'ข้อผิดพลาด !',
          text:'พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ',
          type:'error'
        });
      }
    }
  });
}


function saveAsRequest()
{
  let code = $('#transfer_code').val().trim();
  load_in();

  $.ajax({
    url:HOME + 'save_as_request',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
          swal({
            title:'Saved',
            text: 'บันทึกเอกสารเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            goDetail(code);
          }, 1200);
        }
        else if(ds.status == 'warning') {
          swal({
            title:'Warning',
            text:ds.message,
            type:'warning',
            html:true
          }, () => {
            goDetail(code);
          });
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        });
      }
    }
  });
}


function saveTransfer(code)
{
  load_in();

  $.ajax({
    url:HOME + 'save_transfer/'+code,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
          swal({
            title:'Saved',
            text: 'บันทึกเอกสารเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            goDetail(code);
          }, 1200);
        }
        else if(ds.status == 'warning') {
          swal({
            title:'Warning',
            text:ds.message,
            type:'warning',
            html:true
          }, () => {
            goDetail(code);
          });
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        });
      }
    }
  });
}



function unSave(){
  var code = $('#transfer_code').val();
  swal({
		title: 'คำเตือน !!',
		text: 'หากต้องการยกเลิกการบันทึก คุณต้องยกเลิกเอกสารนี้ใน SAP ก่อน ต้องการยกเลิกการบันทึก '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'unsave_transfer/'+ code,
			type:"POST",
      cache:"false",
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Success',
						text: 'ดำเนินการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);

				}else{
					swal("ข้อผิดพลาด", rs, "error");
				}
			}
		});
	});
}

$('#fromWhsCode').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $(this).val(code);
      $('#from_warehouse_code').val(code);
      $('#from_warehouse').val(name);
      $('#to_warehouse_code').focus();
    }
    else
    {
      $(this).val('');
      $('#from_warehouse_code').val('');
      $('#from_warehouse').val('');
    }
  }
});

$('#toWhsCode').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $(this).val(code);
      $('#to_warehouse_code').val(code);
      $('#to_warehouse').val(name);
      $('#remark').focus();
    }
    else
    {
      $(this).val('');
      $('#to_warehouse_code').val('');
      $('#to_warehouse').val('');
    }
  }
});

$('#from_warehouse_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#from_warehouse_code').val(code);
      $('#from_warehouse').val(name);
      $('#to_warehouse_code').focus();
    }
    else
    {
      $('#from_warehouse_code').val('');
      $('#from_warehouse').val('');
    }
  }
});


$('#to_warehouse_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#to_warehouse_code').val(code);
      $('#to_warehouse').val(name);
      $('#remark').focus();
    }
    else
    {
      $('#to_warehouse_code').val('');
      $('#to_warehouse').val('');
    }
  }
});




$('#from_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#from_warehouse_code').val(code);
      $(this).val(name);
      $('#to_warehouse_code').focus();
    }
    else
    {
      $('#from_warehouse_code').val('');
      $(this).val('');
    }
  }
});


$('#to_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#to_warehouse_code').val(code);
      $(this).val(name);
      $('#remark').focus();
    }
    else
    {
      $('#to_warehouse_code').val('');
      $(this).val('');
    }
  }
});


$('#wx_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_wx_code',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		if(rs == 'not found') {
			$(this).val('');
		}
	}
})
